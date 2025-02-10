
<?php

use App\Http\Controllers\ParticipantQuizSummaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScreenshotController;
use App\Models\Quiz;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'dummy'])->get('/test', function () {
    return response()->json(['message' => 'Dummy middleware working!']);
});


Route::get('/meta/{uid}', [QuizController::class, 'showQuizByUid']);
Route::get('/result/{uniqueResultId}', [ScreenshotController::class, 'showResultByResultUID']);
