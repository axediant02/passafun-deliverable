<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;

Route::get('/landing-pages', [LandingPageController::class, 'index']);
Route::post('/landing-pages', [LandingPageController::class, 'store']);
Route::get('/landing-pages/{id}', [LandingPageController::class, 'show']);
Route::put('/landing-pages/{id}', [LandingPageController::class, 'update']);
Route::delete('/landing-pages/{id}', [LandingPageController::class, 'destroy']);
