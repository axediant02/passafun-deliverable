<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;

Route::get('/participants', [ParticipantController::class, 'index']);
Route::get('/participants/search', [ParticipantController::class, 'search']);
Route::get('/participants/quiz/{quizId}', [ParticipantController::class, 'showParticipantsByQuizId']);
Route::post('/participants', [ParticipantController::class, 'store']);
Route::get('/participants/filter', [ParticipantController::class,'filterParticipantData']);
Route::get('/participants/{id}', [ParticipantController::class, 'show']);
Route::put('/participants/{id}', [ParticipantController::class, 'update']);
Route::delete('/participants/{id}', [ParticipantController::class, 'destroy']);
Route::get('/participant/{uniqueResultId}', [ParticipantController::class, 'fetchParticipantByUniqueId']);
