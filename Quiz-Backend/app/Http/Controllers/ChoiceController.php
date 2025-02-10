<?php

namespace App\Http\Controllers;

use App\Http\Requests\Choices\StoreChoiceRequest;
use App\Http\Requests\Choices\UpdateChoiceRequest;
use App\Models\Choice;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class ChoiceController extends Controller
{
    public function index()
    {
        $choices = Choice::with('quizQuestion.quiz')->get();
        return response()->json($choices);
    }

    public function show($id)
    {
        $choice = Choice::with('quizQuestion.quiz')->findOrFail($id);
        return response()->json($choice);
    }

    public function getChoicesByQuizId($id)
    {
        $quizQuestions = QuizQuestion::with(['choices', 'quiz'])->where('quiz_id', $id)->get();

        $choices = $quizQuestions->flatMap(function ($quizQuestion) {
            return $quizQuestion->choices->map(function ($choice) use ($quizQuestion) {
                return [
                    'id' => $choice->id,
                    'question_id' => $quizQuestion->id,
                    'choice_text' => $choice->choice_text,
                    'choice_image' => $choice->choice_image,
                    'points' => $choice->points,
                    'is_correct' => $choice->is_correct,
                    'quiz_question' => [
                        'id' => $quizQuestion->id,
                        'question_type_id' => $quizQuestion->question_type_id,
                        'quiz_id' => $quizQuestion->quiz_id,
                        'question_text' => $quizQuestion->question_text,
                        'question_image' => $quizQuestion->question_image,
                        'quiz_name' => $quizQuestion->quiz->name
                    ]
                ];
            });
        });

        if ($choices->isEmpty()) {
            return response()->json(['message' => 'No choices found for the specified quiz.'], 404);
        }

        return response()->json($choices);
    }

    public function store(StoreChoiceRequest $request)
    {
        $validatedData = $request->validated();

        $choice = Choice::create($validatedData);
        return response()->json($choice, 201);
    }

    public function update(UpdateChoiceRequest $request, $id)
    {
        $validatedData = $request->validated();

        $choice = Choice::findOrFail($id);
        $choice->update($validatedData);
        return response()->json($choice);
    }

    public function destroy($id)
    {
        Choice::destroy($id);
        return response()->json(null, 204);
    }
}
