<?php

/**
 * RAILWAY ATTENDANCE DEBUG SCRIPT
 * 
 * Script khusus untuk debug masalah gambar attendance di Railway
 * Akses via: https://your-app.railway.app/debug-railway-attendance
 */

require_once __DIR__ . '/vendor/autoload.php';

// Set headers untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Bootstrap Laravel minimal
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    $debug = [];
    
    // 1. Environment check
    $debug['environment'] = [
        'RAILWAY_ENVIRONMENT' => $_ENV['RAILWAY_ENVIRONMENT'] ?? 'not set',
        'APP_ENV' => config('app.env'),
        'APP_URL' => config('app.url'),
        'ASSET_URL' => config('app.asset_url'),
        'FILESYSTEM_DISK' => config('filesystems.default'),
        'FILAMENT_FILESYSTEM_DISK' => config('filament.default_filesystem_disk', 'not set'),
    ];

    // 2. Storage paths
    $debug['paths'] = [
        'storage_app_public' => storage_path('app/public'),
        'public_storage' => public_path('storage'),
        'attendance_dir' => storage_path('app/public/attendance'),
        'symlink_exists' => is_link(public_path('storage')),
        'symlink_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : null,
        'symlink_working' => is_dir(public_path('storage')),
    ];

    // 3. Directory structure
    $debug['directories'] = [
        'storage_app_public_exists' => is_dir(storage_path('app/public')),
        'attendance_dir_exists' => is_dir(storage_path('app/public/attendance')),
        'public_storage_exists' => is_dir(public_path('storage')),
        'attendance_via_symlink' => is_dir(public_path('storage/attendance')),
    ];

    // 4. File permissions
    if (is_dir(storage_path('app/public/attendance'))) {
        $attendanceDir = storage_path('app/public/attendance');
        $debug['permissions'] = [
            'attendance_dir_perms' => substr(sprintf('%o', fileperms($attendanceDir)), -4),
            'attendance_readable' => is_readable($attendanceDir),
            'attendance_writable' => is_writable($attendanceDir),
        ];

        // List files
        $files = glob($attendanceDir . '/*');
        $debug['files'] = [
            'count' => count($files),
            'samples' => array_slice(array_map('basename', $files), 0, 5),
        ];

        // Check first file if exists
        if (!empty($files)) {
            $firstFile = $files[0];
            $debug['first_file'] = [
                'name' => basename($firstFile),
                'size' => filesize($firstFile),
                'perms' => substr(sprintf('%o', fileperms($firstFile)), -4),
                'readable' => is_readable($firstFile),
            ];
        }
    }

    // 5. Test Storage facade
    try {
        $debug['storage_facade'] = [
            'disk_exists' => \Illuminate\Support\Facades\Storage::disk('public') ? true : false,
            'attendance_exists' => \Illuminate\Support\Facades\Storage::disk('public')->exists('attendance'),
            'disk_root' => \Illuminate\Support\Facades\Storage::disk('public')->path(''),
        ];

        $storageFiles = \Illuminate\Support\Facades\Storage::disk('public')->allFiles('attendance');
        $debug['storage_facade']['files_via_storage'] = count($storageFiles);
        $debug['storage_facade']['sample_files'] = array_slice($storageFiles, 0, 5);

    } catch (Exception $e) {
        $debug['storage_facade_error'] = $e->getMessage();
    }

    // 6. Test attendance model
    try {
        $attendance = \App\Models\Attendance::whereNotNull('picture_absen_masuk')->first();
        if ($attendance) {
            $debug['attendance_test'] = [
                'id' => $attendance->id,
                'picture_path' => $attendance->picture_absen_masuk,
                'generated_url' => $attendance->picture_absen_masuk_url,
                'file_exists_storage' => \Illuminate\Support\Facades\Storage::disk('public')->exists($attendance->picture_absen_masuk),
                'file_exists_direct' => file_exists(storage_path('app/public/' . $attendance->picture_absen_masuk)),
            ];

            // Test URL accessibility
            $testUrl = config('app.url') . '/storage/' . $attendance->picture_absen_masuk;
            $debug['attendance_test']['test_url'] = $testUrl;

        } else {
            $debug['attendance_test'] = 'No attendance records with images found';
        }

    } catch (Exception $e) {
        $debug['attendance_error'] = $e->getMessage();
    }

    // 7. URL generation tests
    $testPath = 'attendance/test.jpg';
    $debug['url_generation'] = [
        'asset_url' => asset('storage/' . $testPath),
        'url_helper' => url('storage/' . $testPath),
        'manual_url' => config('app.url') . '/storage/' . $testPath,
        'with_asset_url' => (config('app.asset_url') ?: config('app.url')) . '/storage/' . $testPath,
    ];

    // 8. Railway specific checks
    $debug['railway_specific'] = [
        'is_railway' => !empty($_ENV['RAILWAY_ENVIRONMENT']),
        'railway_static_url' => $_ENV['RAILWAY_STATIC_URL'] ?? 'not set',
        'force_https' => $_ENV['FORCE_HTTPS'] ?? 'not set',
        'trust_proxies' => $_ENV['TRUST_PROXIES'] ?? 'not set',
    ];

    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => $debug,
        'recommendations' => []
    ];

    // Generate recommendations based on findings
    if (!$debug['paths']['symlink_exists']) {
        $response['recommendations'][] = 'Storage symlink missing - run php artisan storage:link';
    }

    if (!$debug['paths']['symlink_working']) {
        $response['recommendations'][] = 'Storage symlink broken - recreate with ln -sf ../storage/app/public public/storage';
    }

    if (!$debug['directories']['attendance_dir_exists']) {
        $response['recommendations'][] = 'Attendance directory missing - create storage/app/public/attendance';
    }

    if (isset($debug['files']) && $debug['files']['count'] === 0) {
        $response['recommendations'][] = 'No files in attendance directory - check if files were uploaded properly';
    }

    if (!$debug['environment']['ASSET_URL']) {
        $response['recommendations'][] = 'ASSET_URL not set - should be your Railway app URL';
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
