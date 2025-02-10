<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

Route::post('/export-participant-csv', [ExportController::class, 'exportParticipantCsv']);
Route::post('/export-participant-csv/{id}', [ExportController::class, 'exportParticipantCsvById']);
Route::get('/export-participant-quiz-summary-csv/{participantId}/{summaryId}', [ExportController::class, 'exportParticipantQuizSummaryCsvById']);
Route::post('/export-quiz-participants/{quizId}', [ExportController::class, 'exportQuizParticipantsCsv']);