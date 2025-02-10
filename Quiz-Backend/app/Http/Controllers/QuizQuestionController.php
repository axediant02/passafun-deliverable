<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizQuestion\StoreQuestionRequest;
use App\Http\Requests\QuizQuestion\UpdateQuestionRequest;
use App\Http\Services\QuestionDuplicatorService;
use App\Models\QuizQuestion;
use App\Models\Quiz;
use App\Models\Choice;
use App\Enums\QuestionTypeEnums;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{

    protected $duplicator;

    public function __construct(QuestionDuplicatorService $duplicator)
    {
        $this->duplicator = $duplicator;
    }

    public function index(Request $request)
    {
        $quizId = $request->query('quiz_id');

        if ($quizId) {
            $quiz = Quiz::findOrFail($quizId);
            $questions = QuizQuestion::whereHas('quizLinks', function ($query) use ($quizId) {
                $query->where('quiz_id', $quizId);
            })->get()->map(function ($question) {
                $question->questionImageUrl = $this->getTemporaryImageUrl($question->questionImage);

                $question->choices->transform(function ($choice) {
                    $choice->choiceImageUrl = $this->getTemporaryImageUrl($choice->choiceImage);
                    return $choice;
                });
                return $question->choice;
            });

            return response()->json([
                'quiz' => $quiz,
                'questions' => $questions
            ]);
        } else {
            $questions = QuizQuestion::all();
            return response()->json($questions);
        }
    }

    public function getQuestionsWithChoices($quizId)
    {
        $quiz = Quiz::with([
            'questions' => function ($query) {
                $query->orderBy('question_order', 'asc')
                    ->with(['choices', 'questionType']);
            }
        ])->findOrFail($quizId);
        foreach ($quiz->questions as $question) {
            $question->questionImageUrl = $this->getTemporaryImageUrl($question->question_image);
            $question->choices->transform(function ($choice) {
                $choice->choiceImageUrl = $this->getTemporaryImageUrl($choice->choice_image);
                return $choice;
            });
        }

        return response()->json($quiz);
    }


    public function show($id)
    {
        $question = QuizQuestion::with('questionType', 'quiz')->findOrFail($id);
        return response()->json($question);
    }

    public function store(Request $request)
    {
        $question = QuizQuestion::create($request->all());
        return response()->json($question, 201);
    }

    public function storeQuestionWithChoices(StoreQuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();

        $this->validateChoicePoints($request->input('choices', []), $validatedData['question_type_id']);

        $questionImagePath = $this->handleQuestionImageUpload($request);
        $question = $this->createQuestion($quizId, $validatedData, $questionImagePath);
        $this->settingQuestionOrder($question, $validatedData, $quizId);
        $this->storeChoices($question, $request->input('choices', []));
        $question->questionImageUrl = $this->getTemporaryImageUrl($question->question_image);
        $question->choices->transform(function ($choice) {
            $choice->choiceImageUrl = $this->getTemporaryImageUrl($choice->choice_image);
            return $choice;
        });
    
        return response()->json($question->load('choices'));
    }


    private function handleQuestionImageUpload($request)
    {
        if ($request->hasFile('question_image')) {
            return $this->storeImage($request->file('question_image'), 'question-images');
        }

        return null;
    }

    private function createQuestion($quizId, $validatedData, $questionImagePath)
    {
        return QuizQuestion::create([
            'quiz_id' => $quizId,
            'question_type_id' => $validatedData['question_type_id'],
            'question_text' => $validatedData['question_text'],
            'question_image' => $questionImagePath ?? null,
        ]);
    }

    private function settingQuestionOrder($question, $validatedData, $quizId)
    {
        $question->update([
            'question_order' => $question->id,
        ]);
    }

    private function storeChoices($question, array $choices)
    {
        foreach ($choices as $index => $choiceData) {
            $choiceImagePath = $this->handleChoiceImageUpload($choiceData, $index);

            $question->choices()->create([
                'choice_text' => $choiceData['choice_text'],
                'choice_image' => $choiceImagePath ?? null,
                'points' => $choiceData['points'],
                'is_correct' => $choiceData['is_correct'],
            ]);
        }
    }

    private function handleChoiceImageUpload($choiceData, $index)
    {
        if (request()->hasFile("choices.$index.choice_image")) {
            return $this->storeImage(request()->file("choices.$index.choice_image"), 'choice-images');
        }

        return null;
    }

    public function updateQuestionData(UpdateQuestionRequest $request, $questionId)
    {
        try {
       
        if ($request->boolean('question_image_removed')) {
            $request->request->remove('question_image');
        }
    
        $validatedFields = $request->validated();
        $question = QuizQuestion::with(['choices', 'questionType'])->findOrFail($questionId);
   
        if ($validatedFields['question_type_id'] === QuestionTypeEnums::OpenEnded) {
      
            $question->choices()->delete();
        }

        $this->updateQuestion($question, $validatedFields, $request);
        
        if ($validatedFields['question_type_id'] !== QuestionTypeEnums::OpenEnded && $request->has('choices')) {
           
            $this->updateChoices($question, $request->input('choices', []));
        }

        $question->load(['choices', 'questionType']);    
        $question->questionImageUrl = $this->getTemporaryImageUrl($question->question_image);
        $question->choices->transform(function ($choice) {
            $choice->choiceImageUrl = $this->getTemporaryImageUrl($choice->choice_image);
            return $choice;
        });
   
        return response()->json([
            'question' => $question,
        ]);

    } catch (\Exception $error) {
        Log::error('Error updating question', [
            'question_id' => $questionId,
            'error' => $error->getMessage(),
            'trace' => $error->getTraceAsString()
        ]);
        throw $error;
        }
    }

    private function updateQuestion(QuizQuestion $question, array $validatedFields, Request $request)
    {
        $updated = false;

        if (isset($validatedFields['question_type_id'])) {
            $question->question_type_id = $validatedFields['question_type_id'];
            $updated = true;
        }

        if (array_key_exists('question_text', $validatedFields)) {
            $question->question_text = $validatedFields['question_text'];
            $updated = true;
        }

        if ($request->boolean('question_image_removed')) {
            if ($question->question_image) {
                Storage::disk('s3')->delete($question->question_image);
            }
            $question->question_image = null;
            $updated = true;
        } elseif ($request->hasFile('question_image')) {
            if ($question->question_image) {
                Storage::disk('s3')->delete($question->question_image);
            }
            $question->question_image = $this->storeImage($request->file('question_image'), 'question-images');
            $updated = true;
       
        }

        if ($updated) {
            $question->save();
        }
    }

    private function updateChoices(QuizQuestion $question, array $choicesData)
    {
        
        $existingChoices = $question->choices->keyBy('id');
        $processedChoiceIds = [];

        foreach ($choicesData as $choiceData) {
            if (isset($choiceData['id']) && $existingChoices->has($choiceData['id'])) {
                
                $choice = $existingChoices[$choiceData['id']];
                $choice->update($choiceData);
                $processedChoiceIds[] = $choiceData['id'];
            } else {
                
                $question->choices()->create($choiceData);
            }
        }

        $existingChoices->each(function ($choice) use ($processedChoiceIds) {
            if (!in_array($choice->id, $processedChoiceIds)) {
                $choice->delete();
            }
        });
    }

    private function isExistingChoice(array $choiceData, $existingChoices): bool
    {
        return isset($choiceData['id']) && $existingChoices->has($choiceData['id']);
    }

    private function updateChoice(Choice $choice, array $choiceData, int $index)
    {
        $fields = ['choice_text', 'is_correct', 'points'];
        $updated = false;
        foreach ($fields as $field) {
            if (isset($choiceData[$field])) {
                $choice->$field = $choiceData[$field];
                if (isset($choiceData['choice_image_removed']) && $choiceData['choice_image_removed']) {
                    $choice->choice_image = null;
                }
                $updated = true;
            }
        }
        if (request()->hasFile("choices.$index.choice_image")) {
            $choice->choice_image = $this->storeImage(request()->file("choices.$index.choice_image"), 'choice-images');
            $updated = true;
        }
        if ($updated) {
            $choice->save();
        }
    }

    private function createSingleChoice(array $choiceData, int $index, QuizQuestion $question)
    {
        $newChoice = [
            'choice_text' => $choiceData['choice_text'], 
            'is_correct' => $choiceData['is_correct'] ?? false,
            'points' => $choiceData['points'] ?? 0,
            'question_id' => $question->id
        ];

        if (request()->hasFile("choices.$index.choice_image")) {
            $newChoice['choice_image'] = $this->storeImage(request()->file("choices.$index.choice_image"), 'choice-images');
        }

        Choice::create($newChoice);
    }

    public function duplicateQuestionWithChoices($quizId, $questionId)
    {
        $originalQuestion = $this->duplicator->getQuestionWithChoicesAndQuestionType($questionId);
        $this->duplicator->incrementQuestionOrder($originalQuestion);
        $duplicateQuestion = $this->duplicator->duplicateQuestion($originalQuestion);
        $this->duplicator->duplicateChoices($originalQuestion, $duplicateQuestion);
        return $this->duplicator->getQuizQuestions($quizId);
    }


    public function destroyQuestion($id)
    {
        $question = QuizQuestion::find($id);
        if (!$question) {
            return $this->errorResponse('Question not found', 404);
        }
        $question->choices()->delete();
        $question->delete();
        return $this->respond('Question deleted successfully', $question->id, null, 204);
    }

    public function destroyQuestionImage($id) {
        
        $question = QuizQuestion::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        if (empty($question->question_text)) {
            throw new \Exception('Cannot delete image: Question text is required');
        }

        if ($question->question_image) {
            Storage::disk('s3')->delete($question->question_image);
            $question->question_image = null;
            $question->save();
        }

        return response()->json(['message' => 'Image deleted successfully!'], 200);
    }

    private function errorResponse(string $message, int $statusCode, $resourceId = null)
    {
        return response()->json(['error' => $message, 'resource_id' => $resourceId], $statusCode);
    }

    public function destroyChoice($id, $choiceId)
    {
        try {
            $question = $this->findQuestion($id);
            $choice = $this->getChoice($question, $choiceId);
            $this->deleteChoice($choice);
            return $this->respond('Choice deleted successfully', $question->id, $choice->id, 200);
        } catch (ModelNotFoundException $e) {
            return $this->respond('Question or choice not found', null, null, 404);
        } catch (\Exception $e) {
            \Log::error('Error deleting choice: ' . $e->getMessage());
            return $this->respond('Failed to delete choice', null, null, 500);
        }
    }

    private function findQuestion($id): QuizQuestion
    {
        return QuizQuestion::findOrFail($id);
    }

    private function getChoice($question, $choiceId): Choice
    {
        $choice = $question->choices()->find($choiceId);

        if (!$choice) {
            throw new ModelNotFoundException('Choice not found');
        }

        return $choice;
    }

    private function deleteChoice(Choice $choice)
    {
        $choice->delete();
    }

    private function respond(string $message, $questionId = null, $choiceId = null, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'question_id' => $questionId,
            'choice_id' => $choiceId
        ], $statusCode);
    }

    protected function storeImage($image, $folder)
    {
        $originalName = $image->getClientOriginalName();
        return $image->storeAs($folder, $originalName, [
            'disk' => 's3',
            'visibility' => 'private',
        ]);
    }

    private function getTemporaryImageUrl($path)
    {
        return $path ? Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60)) : null;
    }

    private function validateChoicePoints(array $choices, int $questionTypeId)
    {
        if ($questionTypeId === QuestionTypeEnums::OpenEnded) {
            return;
        }

        if (empty($choices)) {
            return;
        }

        $hasPoints = false;

        foreach ($choices as $choice) {
            if (isset($choice['points']) && $choice['points'] > 5) {
                abort(422, 'Choice points must not exceed 5.');
            }

            if (isset($choice['points']) && $choice['points'] > 0) {
                $hasPoints = true;
            }
        }

        if (!$hasPoints) {
            abort(422, 'At least one choice must have points greater than 0.');
        }
    }
}
