<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizQuestionController;

Route::get('/quiz-questions', [QuizQuestionController::class, 'index']);
Route::post('/quiz-questions', [QuizQuestionController::class, 'store']);
Route::get('/quiz-questions/{id}', [QuizQuestionController::class, 'show']);
Route::put('/quiz-questions/{id}', [QuizQuestionController::class, 'update']);
Route::delete('/quiz-questions/{id}', [QuizQuestionController::class, 'destroy']);
Route::post('/quizzes/{quiz}/questions', [QuizQuestionController::class, 'storeQuestionWithChoices']);
Route::get('/quizzes/{quiz}/questions', [QuizQuestionController::class, 'getQuestionsWithChoices']);