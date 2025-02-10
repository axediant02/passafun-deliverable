<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantQuizSummaryController;

Route::post('/participant-quiz-summary', [ParticipantQuizSummaryController::class, 'storeParticipantSummary']);
Route::get('/participant-quiz-summaries', [ParticipantQuizSummaryController::class, 'index']);
Route::get('/participant-quiz-summary/{id}', [ParticipantQuizSummaryController::class, 'show']);
Route::get('/participant/{participantId}/summaries-and-answers', [ParticipantQuizSummaryController::class, 'showByParticipant']);
Route::get('/quiz/{quizId}/answers', [ParticipantQuizSummaryController::class, 'showAnswersByQuiz']);
Route::get('quiz-summary/{summaryId}', [ParticipantQuizSummaryController::class, 'showQuizSummary']);
Route::get('/result/{uniqueResultId}', [ParticipantQuizSummaryController::class, 'showResultByUniqueResultId']);
Route::get('/participant-quiz-summaries/paginated', [ParticipantQuizSummaryController::class, 'indexPaginated']);
