<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminRoleMiddleware;

// Registration route does not require authorization
Route::post('/admins', [AdminController::class, 'store']);

// Middleware for admin role
Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function(){
        Route::put('/admins/{id}', [AdminController::class, 'update']);
        Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
});

// Middleware for viewer role
Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':viewer'])->group(function(){
        Route::get('/admins', [AdminController::class, 'index']);
        Route::get('/admins/{id}', [AdminController::class, 'show']);
});
