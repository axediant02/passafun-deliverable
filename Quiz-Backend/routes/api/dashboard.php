<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminRoleMiddleware;

Route::middleware(['auth:sanctum', AdminRoleMiddleware::class . ':admin'])->group(function(){
    Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics']);
});

Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics']);

