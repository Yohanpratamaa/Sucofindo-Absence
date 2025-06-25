<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RealTimeController;

Route::get('/', function () {
    return view('welcome');
});

// Real-time API routes
Route::prefix('api/realtime')->group(function () {
    Route::get('/stats', [RealTimeController::class, 'getStats']);
    Route::get('/recent-attendance', [RealTimeController::class, 'getRecentAttendance']);
    Route::get('/dashboard-data', [RealTimeController::class, 'getDashboardData']);
});
