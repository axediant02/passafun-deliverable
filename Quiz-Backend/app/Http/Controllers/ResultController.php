<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizResults\StoreResultRequest;
use App\Http\Requests\QuizResults\UpdateResultRequest;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Quiz;
use App\Models\ParticipantQuizSummary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ResultController extends Controller
{
    protected $deletedDefaultResults = [];

    public function index()
    {
        $results = Result::with('quiz')->get();
        $this->attachImageUrls($results);

        return response()->json(['results' => $results]);
    }

    public function getResultsByQuizId($quizId)
    {
        $quiz = Quiz::with('results')->findOrFail($quizId);
        $this->attachImageUrls($quiz->results);

        $defaultResult = Result::where('quiz_id', $quizId)->where('min_points', 0)->first();

        if (!$defaultResult) {
            $defaultResult = $this->createDefaultResult($quizId);
        }

        foreach ($quiz->results as $result) {
            $result->isDefault = ($result->min_points === 0);
        }

        if ($defaultResult && !$quiz->results->contains('id', $defaultResult->id)) {
            $quiz->results->push($defaultResult);
        }

        $quiz->results = $quiz->results->filter(function ($result) {
            return $result->min_points !== 0 || $result->isDefault;
        });

        return response()->json($quiz);
    }

    public function showResultByUniqueResultId($uniqueResultId)
    {
        $participantQuizSummary = ParticipantQuizSummary::where('unique_result_id', $uniqueResultId)
            ->with('result')
            ->firstOrFail();

        if (!$participantQuizSummary->result) {
            return response()->json(['error' => 'Result not found for the given unique_result_id'], 404);
        }

        $result = $participantQuizSummary->result;
        $resultData = [
            'header' => $result->header,
            'description' => $result->description,
            'financial_tips' => $result->financial_tips,
            'image' => $result->image,
        ];

        return response()->json($resultData);
    }

    public function storeResult(StoreResultRequest $request, $quizId)
    {
        $validatedData = $request->validated();

        $existingDefaultResult = Result::where('quiz_id', $quizId)->where('min_points', 0)->first();

        $minPoints = (int)$validatedData['min_points'];

        if ($minPoints === 0 && $existingDefaultResult) {
            return response()->json(['error' => 'A default result with 0 min point already exists.'], 422);
        }

        $this->checkPointsValidity($minPoints, $validatedData['max_points']);

        $quiz = Quiz::findOrFail($quizId);
        $result = new Result($this->prepareResultData($validatedData, $request, $quizId));
        $result->save();
        $this->attachImageUrls(collect([$result]));

        return response()->json([
            'message' => 'Result stored successfully!',
            'result' => $result
        ], 201);
    }

    public function updateResult(UpdateResultRequest $request, $id)
    {
        $validatedData = $request->validated();

        $result = Result::findOrFail($id);

        if ($result->min_points == 0) {

            $validatedData['min_points'] = 0;
        } else {

            if (!array_key_exists('min_points', $validatedData)) {
            return response()->json([
                'message' => 'min_points is required.'
            ], 422);
            }

            if ($validatedData['min_points'] === null || $validatedData['min_points'] === '') {
            return response()->json([
                'message' => 'min_points is required.'
            ], 422);
            }
        }

        if ($validatedData['min_points'] != 0) {
            $this->checkPointsValidity($validatedData['min_points'], $validatedData['max_points']);
        }

        if ($validatedData['min_points'] == 0 && $result->min_points != 0) {
            $existingResult = Result::where('min_points', 0)
                ->where('id', '!=', $id)
                ->first();

        if ($existingResult) {
            return response()->json([
                'message' => 'Only one result can have minimum points set to 0.'
            ], 422);
            }
        }

        $this->updateResultData($result, $validatedData, $request);
        $this->attachImageUrls(collect([$result]));

        return response()->json([
            'message' => 'Result updated successfully!',
            'result' => $result
        ], 200);
    }

    public function destroy($id)
    {
        $result = Result::findOrFail($id);

        $this->deleteImage($result->image);
        $result->delete();

        if ($result->min_points === 0) {
            $this->deletedDefaultResults[] = $result->quiz_id;
        }

        return response()->json(null, 204);
    }

    public function deleteImageInResultCard($id)
    {

        try {
            $result = Result::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Result not found with id: ' . $id);
            return response()->json(['error' => 'Result not found'], 404);
        }

        if ($result->image) {
            Log::info('Image found, deleting from S3: ' . $result->image);
            Storage::disk('s3')->delete($result->image);
            $result->image = null;
            $result->save();
        }

        return response()->json(['message' => 'Image deleted successfully!'], 200);
    }



    protected function checkPointsValidity($minPoints, $maxPoints)
    {
        if ($minPoints !== null && $maxPoints !== null && $minPoints > $maxPoints) {
            abort(422, 'Minimum points cannot be greater than Maximum points.');
        }
    }

    protected function prepareResultData($validatedData, $request, $quizId)
{
    $data = [
        'quiz_id' => $quizId,
        'header' => $validatedData['header'] ?? '',
        'description' => $validatedData['description'] ?? '',
        'financial_tips' => $validatedData['financial_tips'] ?? '',
        'min_points' => isset($validatedData['min_points']) ? (int)$validatedData['min_points'] : null,
        'max_points' => isset($validatedData['max_points']) ? (int)$validatedData['max_points'] : null,
    ];

    if ($request->hasFile('image')) {
        $data['image'] = $this->storeImage($request->file('image'));
    }

    return $data;
}

    protected function storeImage($image)
    {
        $originalName = $image->getClientOriginalName();
        return $image->storeAs('results_page_images', $originalName, [
            'disk' => 's3',
            'visibility' => 'private',
        ]);
    }

    protected function updateResultData(Result $result, $validatedData, Request $request)
    {
        $result->fill($this->prepareResultData($validatedData, $request, $result->quiz_id));
        $result->save();
    }

    protected function deleteImage($imagePath)
    {
        if ($imagePath) {
            Storage::disk('s3')->delete($imagePath);
        }
    }

    protected function attachImageUrls($results)
    {
        foreach ($results as $result) {
            if ($result->image) {
                $result->image_url = Storage::disk('s3')->temporaryUrl($result->image, now()->addMinutes(60));
            }
        }
    }

    private function createDefaultResult($quizId)
    {

        return Result::create([
            'quiz_id' => $quizId,
            'header' => 'Result Name (Click to customize)',
            'description' => 'Click here to add your result description. Make it engaging and informative for your quiz takers!',
            'min_points' => 0,
            'max_points' => 0,
        ]);
    }
}
