<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThumbnailCustomizationController;
use App\Http\Middleware\AdminRoleMiddleware;

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function () {
    Route::post('/thumbnail-customization', [ThumbnailCustomizationController::class, 'store']);
    Route::post('/update/thumbnail-customization/{quizId}', [ThumbnailCustomizationController::class, 'update']);
});

Route::get('/thumbnail-customization/{id}', [ThumbnailCustomizationController::class, 'show']);