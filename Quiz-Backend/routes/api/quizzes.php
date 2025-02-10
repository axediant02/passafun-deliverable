<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Middleware\AdminRoleMiddleware;

Route::get('/quizzes/names', [QuizController::class, 'getQuizNames']);
Route::get('/quizzes/published', [QuizController::class, 'getPublishedQuizzesNoRelations']);
Route::get('/quizzes/uid/{uid}', [QuizController::class, 'getPublishedQuizzesByUid']);
Route::get('/quizzes/status', [QuizController::class, 'getQuizByStatus']);
Route::get('/quizzes/search-quiz', [QuizController::class, 'searchQuizzes']);
Route::get('/quizzes/check-quiz-status', [QuizController::class, 'checkQuizIfPublished']);
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::get('/quizzes/{id}/participants', [QuizController::class,'showQuizParticipants']);

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function () {
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::post('/quizzes/{quiz}/update', [QuizController::class, 'updateQuiz']);
    Route::patch('/quizzes/{quiz}', [QuizController::class, 'updateQuizStatus']);
    Route::patch('/quizzes/{quiz}/isFeatured', [QuizController::class, 'updateIsFeaturedQuiz']);
    Route::put('quizzes/{quiz}/updateTheme', [QuizController::class, 'updateQuizTheme']);
    Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy']);
    Route::delete('/quizzes/{quiz}/destroyImages', [QuizController::class, 'destroyQuizImages']);
    Route::get('/quizzes/search/admin', [QuizController::class, 'searchQuizzesAsAdmin']);
});

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':viewer'])->group(function () {
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/search/admin', [QuizController::class, 'searchQuizzesAsAdmin']);
});
