<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreenshotController;

foreach (glob(__DIR__ . '/api/*.php') as $file) {
    require $file;
}

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post(uri: '/take-screenshot', action: [ScreenshotController::class, 'takeScreenshot']);