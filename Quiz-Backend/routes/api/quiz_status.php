<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizStatusController;

Route::get('/quiz-status', [QuizStatusController::class, 'index']);
Route::post('/quiz-status', [QuizStatusController::class, 'store']);
Route::get('/quiz-status/{id}', [QuizStatusController::class, 'show']);
Route::put('/quiz-status/{id}', [QuizStatusController::class, 'update']);
Route::delete('/quiz-status/{id}', [QuizStatusController::class, 'destroy']);
