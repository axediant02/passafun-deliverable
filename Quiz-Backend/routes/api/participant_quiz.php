<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantQuizController;

Route::get('/participant-quiz', [ParticipantQuizController::class, 'index']);
Route::post('/participant-quiz', [ParticipantQuizController::class, 'store']);
Route::get('/participant-quiz/{id}', [ParticipantQuizController::class, 'show']);
Route::put('/participant-quiz/{id}', [ParticipantQuizController::class, 'update']);
Route::delete('/participant-quiz/{id}', [ParticipantQuizController::class, 'destroy']);
