<?php

use App\Http\Middleware\AdminRoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::post('/password/email', [AuthController::class, 'sendOtp']);
Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});