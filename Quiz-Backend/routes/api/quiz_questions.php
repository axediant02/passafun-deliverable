<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Middleware\AdminRoleMiddleware;

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function () {
        Route::post('/quiz-questions', [QuizQuestionController::class, 'store']);
        Route::post('/quiz-questions/{id}', [QuizQuestionController::class, 'updateQuestionData']);
        Route::post('/quizzes/{quiz}/questions', [QuizQuestionController::class, 'storeQuestionWithChoices']);
        Route::post('/quizzes/{quizId}/questions/update', [QuizQuestionController::class, 'updateQuestionData']);
        Route::delete('/quiz-questions/{id}', [QuizQuestionController::class, 'destroyQuestion']);
        Route::delete('/quiz-questions/{id}/image/delete', [QuizQuestionController::class, 'destroyQuestionImage']);
        Route::delete('/quiz-questions/{id}/{choiceId}', [QuizQuestionController::class, 'destroyChoice']);
        Route::post('/quizzes/{quizId}/questions/{id}', [QuizQuestionController::class, 'DuplicateQuestionWithChoices']);
});

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':viewer'])->group(function () {
        Route::get('/quiz-questions', [QuizQuestionController::class, 'index']);
        Route::get('/quiz-questions/{id}', [QuizQuestionController::class, 'show']);
        Route::get('/quizzes/{quiz}/questions', [QuizQuestionController::class, 'getQuestionsWithChoices']);
});
