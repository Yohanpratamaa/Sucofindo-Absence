<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\RealTimeController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\Auth\UnifiedLoginController;
use App\Models\Pegawai;
use App\Http\Controllers\StorageTestController;

// Setup routes for initial admin creation
Route::middleware('guest')->group(function () {
    Route::get('/setup', [SetupController::class, 'showSetupForm'])->name('setup.form');
    Route::post('/setup', [SetupController::class, 'processSetup'])->name('setup.process');
});

// Unified Login Routes - Single entry point for all roles
Route::middleware('guest')->group(function () {
    Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UnifiedLoginController::class, 'login'])->name('unified.login');
});

// Unified Logout Route - Works for all panels
Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('unified.logout')->middleware('auth');
Route::get('/logout', [UnifiedLoginController::class, 'logout'])->name('unified.logout.get')->middleware('auth');

// Test route untuk checking photo size
Route::post('/test-photo-size', [App\Http\Controllers\PhotoSizeTestController::class, 'checkPhotoSize'])->name('test.photo.size');

// Test route untuk debugging photo storage
Route::get('/test-photo', [App\Http\Controllers\PhotoTestController::class, 'testPhoto'])->name('test.photo');

// Test route untuk checking dinas luar data
Route::get('/test-dinas-luar', function () {
    $dinasLuarAttendances = App\Models\Attendance::where('attendance_type', 'Dinas Luar')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    $data = [
        'total' => App\Models\Attendance::count(),
        'dinas_luar_count' => App\Models\Attendance::where('attendance_type', 'Dinas Luar')->count(),
        'wfo_count' => App\Models\Attendance::where('attendance_type', 'WFO')->count(),
        'attendances' => $dinasLuarAttendances
    ];

    return response()->json($data, 200, [], JSON_PRETTY_PRINT);
})->name('test.dinas.luar');

// Storage testing routes untuk Railway debugging
Route::get('/test-storage', [StorageTestController::class, 'testStorage']);
Route::post('/test-upload', [StorageTestController::class, 'testUpload']);
Route::get('/test-attendance-images', [StorageTestController::class, 'testAttendanceImages']);

// Railway debugging routes
Route::get('/debug-railway-attendance', function () {
    include base_path('debug-railway-attendance.php');
});

// Redirect root URL to appropriate panel based on authentication and setup status
Route::get('/', function () {
    try {
        // Check if super admin exists
        $adminExists = Pegawai::where('role_user', 'super admin')->exists();

        if (!$adminExists) {
            // If no super admin, redirect to setup
            return redirect()->route('setup.form');
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Make sure user object is fully loaded and has role_user attribute
            if ($user && isset($user->role_user)) {
                // Redirect based on user role
                $redirectUrl = \App\Services\UserRoleService::getRedirectUrlByRole($user->role_user);
                return redirect()->to($redirectUrl);
            } else {
                // If user object is incomplete, logout and redirect to login
                Auth::logout();
                return redirect()->route('login');
            }
        } else {
            // If not authenticated, redirect to unified login
            return redirect()->route('login');
        }
    } catch (\Exception $e) {
        // If any error occurs, redirect to login
        return redirect()->route('login');
    }
})->name('home');

// Izin Document Routes
Route::middleware('auth')->group(function () {
    Route::get('/izin/{izin}/print', [App\Http\Controllers\IzinController::class, 'print'])->name('izin.print');
    Route::get('/izin/{izin}/document/download', [App\Http\Controllers\IzinController::class, 'downloadDocument'])->name('izin.document.download');
    Route::get('/izin/{izin}/document/preview', [App\Http\Controllers\IzinController::class, 'previewDocument'])->name('izin.document.preview');
});

// Real-time API routes
Route::prefix('api/realtime')->group(function () {
    Route::get('/stats', [RealTimeController::class, 'getStats']);
    Route::get('/recent-attendance', [RealTimeController::class, 'getRecentAttendance']);
    Route::get('/dashboard-data', [RealTimeController::class, 'getDashboardData']);
});
