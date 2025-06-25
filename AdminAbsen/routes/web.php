<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\RealTimeController;

// Redirect root URL to Filament admin login/dashboard
Route::get('/', function () {
    // Check if user is authenticated
    if (Auth::check()) {
        // If authenticated, redirect to admin dashboard
        return redirect()->to('/admin');
    } else {
        // If not authenticated, redirect to admin login
        return redirect()->to('/admin/login');
    }
})->name('home');

// Alternative simple redirect (uncomment if preferred)
// Route::redirect('/', '/admin', 302);

// Real-time API routes
Route::prefix('api/realtime')->group(function () {
    Route::get('/stats', [RealTimeController::class, 'getStats']);
    Route::get('/recent-attendance', [RealTimeController::class, 'getRecentAttendance']);
    Route::get('/dashboard-data', [RealTimeController::class, 'getDashboardData']);
});
