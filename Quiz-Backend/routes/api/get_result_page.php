<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GetResultPageController;


Route::get('/get-result-page', [GetResultPageController::class, 'index']);
Route::get('/get-result-page/{quizId}', [GetResultPageController::class, 'getResultFormByQuizId']);
Route::post('/get-result-page/{quizId}', [GetResultPageController::class, 'update']);
Route::delete('/get-result-page/{resultId}', [GetResultPageController::class, 'destroy']);

