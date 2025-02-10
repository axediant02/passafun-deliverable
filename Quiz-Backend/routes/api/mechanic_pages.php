<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MechanicPageController;

Route::get('/mechanic-pages', [MechanicPageController::class, 'index']);
Route::post('/mechanic-pages', [MechanicPageController::class, 'store']);
Route::get('/mechanic-pages/{id}', [MechanicPageController::class, 'show']);
Route::post('/mechanic-pages/{id}', [MechanicPageController::class, 'update']);
Route::delete('/mechanic-pages/{id}', [MechanicPageController::class, 'destroy']);
Route::delete('/mechanic-pages/{id}/{instructionId}', [MechanicPageController::class, 'destroy']);
