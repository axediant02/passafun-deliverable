<?php

namespace App\Http\Controllers;

use App\Models\ParticipantQuizSummary;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

class ScreenshotController extends Controller
{


        public function takeScreenshot($quizId ,$uniqueResultId)
    {
        $url = getEnvironmentUrl() . "/{$quizId}/r/{$uniqueResultId}/thumbnail";
        $localPath = public_path("screenshots/{$uniqueResultId}.jpg");
        $s3Path = "screenshots/{$uniqueResultId}.jpg";

        try {
            File::ensureDirectoryExists(dirname($localPath));
            File::delete($localPath);

            Browsershot::url($url)
                ->windowSize(1200, 628)
                ->deviceScaleFactor(2)
                ->waitUntilNetworkIdle()
                ->waitForFunction('document.readyState === "complete"')
                ->save($localPath);

            $uploaded = Storage::disk('s3')->putFileAs(
                'screenshots',
                $localPath,
                "{$uniqueResultId}.jpg",
                'public'
            );

        $publicUrl = Storage::disk('s3')->url($s3Path);

            $summary = ParticipantQuizSummary::where('unique_result_id', $uniqueResultId)->first();

            if ($summary) {
                $summary->update(['image_thumbnail' => $publicUrl]);
            }

            File::delete($localPath);

            return response()->json(['success' => true, 'url' => $publicUrl]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }





    public function showResultByResultUID($uniqueResultId)
    {
        $participantQuizSummary = ParticipantQuizSummary::where('unique_result_id', $uniqueResultId)
            ->with(['result', 'quiz.landingPage'])
            ->first();

        if (!$participantQuizSummary) {
            return response()->json(['error' => 'Participant Quiz Summary not found'], 404);
        }

        $quiz = $participantQuizSummary->quiz;

        $result = [
            'quizId' => $quiz->uid,
            'quizName' => $quiz->name ?? 'Unknown Quiz',
            'header' => $participantQuizSummary->result->header ?? 'No Header Found',
            'description' => $participantQuizSummary->result->description ?? 'No Description Found',
            'financial_tips' => $participantQuizSummary->result->financial_tips ?? '',
            'image' => $participantQuizSummary->image_thumbnail ?? 'No Image found',
            'sub_header' => $quiz->landingPage->sub_header ?? 'No Subheader Found', // Get sub_header
        ];

        return view('screenshot', compact('result'));
    }


}






