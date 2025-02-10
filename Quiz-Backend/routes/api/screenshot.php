<?php

use App\Http\Controllers\ScreenshotController;
use Illuminate\Support\Facades\Route;


Route::post('/screenshot/{quizId}/{uniqueResultId}', [ScreenshotController::class, 'takeScreenshot']);
