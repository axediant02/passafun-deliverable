<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChoiceController;

Route::get('/choices', [ChoiceController::class, 'index']);
Route::post('/choices', [ChoiceController::class, 'store']);
Route::get('/choices/{id}', [ChoiceController::class, 'getChoicesByQuizId']);
Route::put('/choices/{id}', [ChoiceController::class, 'update']);
Route::delete('/choices/{id}', [ChoiceController::class, 'destroy']);

