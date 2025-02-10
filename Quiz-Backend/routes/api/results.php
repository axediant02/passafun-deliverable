<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;
use App\Http\Middleware\AdminRoleMiddleware;


Route::prefix('quizzes/{quiz}')->group(function () {
    Route::get('/results', [ResultController::class, 'getResultsByQuizId']);
    Route::post('/results', [ResultController::class, 'storeResult']);
});

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function(){
    Route::post('/results/{id}', [ResultController::class, 'updateResult']);
    Route::delete('/results/{id}', [ResultController::class, 'destroy']);
    Route::delete('/results/{id}/image/delete', [ResultController::class, 'deleteImageInResultCard']);
});

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':viewer'])->group(function(){
    Route::get('/results', [ResultController::class, 'index']);
});


