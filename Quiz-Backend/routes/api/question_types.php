<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionTypeController;

Route::get('/question-types', [QuestionTypeController::class, 'index']);
Route::post('/question-types', [QuestionTypeController::class, 'store']);
Route::get('/question-types/{id}', [QuestionTypeController::class, 'show']);
Route::put('/question-types/{id}', [QuestionTypeController::class, 'update']);
Route::delete('/question-types/{id}', [QuestionTypeController::class, 'destroy']);