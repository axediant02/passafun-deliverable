<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\QuizStatuses;
use App\Http\Services\ImageService;

class DashboardController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getStatistics()
    {
        try {
            $dailyParticipants = Participant::whereDate('created_at', Carbon::today())->count();

            $totalParticipants = Participant::count();

            $totalQuizzes = Quiz::count();

            $topQuizzes = Quiz::withCount('participantQuizSummaries')
            ->whereIn('quiz_status_id', [QuizStatuses::Published])
                ->orderBy('participant_quiz_summaries_count', 'desc')
                ->take(5)
                ->get(['id', 'name', 'description', 'thumbnail'])
                ->map(function ($quiz) {
                    $quiz->thumbnail_url = $this->imageService->getTemporaryImageUrl($quiz->thumbnail);
                    return $quiz;
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'daily_participants' => $dailyParticipants,
                    'total_participants' => $totalParticipants,
                    'total_quizzes' => $totalQuizzes,
                    'top_quizzes' => $topQuizzes
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
