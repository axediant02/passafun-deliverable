<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Quiz;
use App\Enums\QuizStatuses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;

class QuizLibraryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getPopularQuizzes()
    {
        try {

            $totalQuizzes = Quiz::count();

            $topQuizzes = Quiz::where('quiz_status_id', QuizStatuses::Published)
                ->withCount('participantQuizSummaries')
                ->orderByDesc('participant_quiz_summaries_count')
                ->take(5)
                ->get(['id', 'name', 'description', 'thumbnail'])
                ->map(fn($quiz) => $this->addQuizImagesUrl($quiz));

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_quizzes' => $totalQuizzes,
                    'top_quizzes' => $topQuizzes,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
//
    public function getPublishedQuizzes(Request $request)
    {
        $numberOfQuizzes = $request->query('numberOfQuizzes', 10);
        $sortBy = $request->query('sortBy', 'created_at');
        $sortOrder = $request->query('sortOrder', 'desc');

        $quizzes = Quiz::where('quiz_status_id', QuizStatuses::Published)
            ->with('landingPage')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($numberOfQuizzes);

        $quizzes->getCollection()->transform(function ($quiz) {
            $quiz = $this->addQuizImagesUrl($quiz);

            if ($quiz->landingPage) {
                $quiz->sub_header = $quiz->landingPage->sub_header;
            }
            return $quiz;
        });
        return response()->json($quizzes);
    }



    public function getFeaturedQuizzes()
    {
        $quizzes = Quiz::where('quiz_status_id', QuizStatuses::Published)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        if ($quizzes->isEmpty()) {
            $quizzes = Quiz::where('quiz_status_id', QuizStatuses::Published)
                ->withCount('participantQuizSummaries')
                ->orderByDesc('participant_quiz_summaries_count')
                ->take(10)
                ->get();
        }

        return response()->json($quizzes->map(fn($quiz) => $this->addQuizImagesUrl($quiz)));
    }

    private function addQuizImagesUrl($quiz)
    {
        $quiz->thumbnail_url = $this->imageService->getTemporaryImageUrl($quiz->thumbnail);
        $quiz->cover_image_url = $this->imageService->getTemporaryImageUrl($quiz->cover_image);
        return $quiz;
    }
}
