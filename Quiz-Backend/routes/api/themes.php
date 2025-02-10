<?php

use App\Http\Middleware\AdminRoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThemeController;

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function(){
        Route::post('/themes', [ThemeController::class, 'store']);
        Route::put('/themes/{id}', [ThemeController::class, 'update']);
        Route::delete('/themes/{id}', [ThemeController::class, 'destroy']);
        Route::get('/themes/searching', [ThemeController::class, 'search']);
});

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':viewer'])->group(function(){
        Route::get('/themes', [ThemeController::class, 'index']);
        Route::get('/themes/{id}', [ThemeController::class, 'show']);
});
