<?php

/**
 * DEBUGGING ATTENDANCE IMAGE ISSUE
 *
 * Script untuk mendiagnosa masalah gambar attendancecho "\n6. URL GENERATION TEST:\n";
try {
    $testPath = 'attendance/test.jpg';
    echo "   Test path: {$testPath}\n";
    echo "   Manual URL: " . url('storage/' . $testPath) . "\n";
    echo "   Asset URL: " . asset('storage/' . $testPath) . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}ak terdeteksi
 * di Railway deployment.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATTENDANCE IMAGES DIAGNOSTIC ===\n\n";

echo "1. STORAGE CONFIGURATION:\n";
echo "   Default filesystem: " . config('filesystems.default') . "\n";
echo "   Public disk root: " . config('filesystems.disks.public.root') . "\n";
echo "   Public disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   ASSET_URL: " . config('app.asset_url', 'not set') . "\n\n";

echo "2. STORAGE PATHS:\n";
$publicDiskRoot = Storage::disk('public')->path('');
echo "   Public disk root path: {$publicDiskRoot}\n";
echo "   Attendance directory: {$publicDiskRoot}attendance\n";
echo "   Directory exists: " . (is_dir($publicDiskRoot . 'attendance') ? 'YES' : 'NO') . "\n\n";

echo "3. SYMLINK STATUS:\n";
$symlinkPath = public_path('storage');
$symlinkTarget = storage_path('app/public');
echo "   Symlink path: {$symlinkPath}\n";
echo "   Symlink target: {$symlinkTarget}\n";
echo "   Symlink exists: " . (is_link($symlinkPath) ? 'YES' : 'NO') . "\n";
if (is_link($symlinkPath)) {
    echo "   Symlink points to: " . readlink($symlinkPath) . "\n";
    echo "   Target exists: " . (is_dir(readlink($symlinkPath)) ? 'YES' : 'NO') . "\n";
}
echo "\n";

echo "4. ATTENDANCE RECORDS WITH IMAGES:\n";
try {
    $attendancesWithImages = Attendance::whereNotNull('picture_absen_masuk')
        ->orWhereNotNull('picture_absen_siang')
        ->orWhereNotNull('picture_absen_pulang')
        ->limit(10)
        ->get();

    if ($attendancesWithImages->isEmpty()) {
        echo "   No attendance records with images found\n\n";
    } else {
        foreach ($attendancesWithImages as $attendance) {
            echo "   ID: {$attendance->id}\n";

            if ($attendance->picture_absen_masuk) {
                echo "     Check In: {$attendance->picture_absen_masuk}\n";
                $exists = Storage::disk('public')->exists($attendance->picture_absen_masuk);
                echo "     File exists: " . ($exists ? 'YES' : 'NO') . "\n";
                if ($exists) {
                    $fullPath = Storage::disk('public')->path($attendance->picture_absen_masuk);
                    echo "     Full path: {$fullPath}\n";
                    echo "     File size: " . filesize($fullPath) . " bytes\n";
                    echo "     URL: " . url('storage/' . $attendance->picture_absen_masuk) . "\n";
                }
            }

            if ($attendance->picture_absen_siang) {
                echo "     Check Noon: {$attendance->picture_absen_siang}\n";
                $exists = Storage::disk('public')->exists($attendance->picture_absen_siang);
                echo "     File exists: " . ($exists ? 'YES' : 'NO') . "\n";
            }

            if ($attendance->picture_absen_pulang) {
                echo "     Check Out: {$attendance->picture_absen_pulang}\n";
                $exists = Storage::disk('public')->exists($attendance->picture_absen_pulang);
                echo "     File exists: " . ($exists ? 'YES' : 'NO') . "\n";
            }

            echo "   ---\n";
        }
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

echo "5. DIRECTORY STRUCTURE:\n";
try {
    $files = Storage::disk('public')->allFiles('attendance');
    echo "   Files in attendance directory: " . count($files) . "\n";

    if (!empty($files)) {
        echo "   Sample files:\n";
        foreach (array_slice($files, 0, 5) as $file) {
            $fullPath = Storage::disk('public')->path($file);
            echo "     - {$file} (" . filesize($fullPath) . " bytes)\n";
        }
    }
} catch (Exception $e) {
    echo "   Error listing files: " . $e->getMessage() . "\n";
}

echo "\n6. URL GENERATION TEST:\n";
try {
    $testPath = 'attendance/test.jpg';
    echo "   Test path: {$testPath}\n";
    echo "   Storage URL: " . Storage::disk('public')->url($testPath) . "\n";
    echo "   Manual URL: " . url('storage/' . $testPath) . "\n";
    echo "   Asset URL: " . asset('storage/' . $testPath) . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";

// Check common issues
$issues = [];
$solutions = [];

if (!is_dir($publicDiskRoot . 'attendance')) {
    $issues[] = "Attendance directory doesn't exist";
    $solutions[] = "Create attendance directory in storage/app/public/";
}

if (!is_link($symlinkPath)) {
    $issues[] = "Storage symlink doesn't exist";
    $solutions[] = "Run 'php artisan storage:link' or create manually";
} elseif (!is_dir(readlink($symlinkPath))) {
    $issues[] = "Storage symlink target doesn't exist";
    $solutions[] = "Ensure storage/app/public directory exists";
}

try {
    $files = Storage::disk('public')->allFiles('attendance');
    if (empty($files)) {
        $issues[] = "No files found in attendance directory";
        $solutions[] = "Upload some test files or check if images were uploaded correctly";
    }
} catch (Exception $e) {
    $issues[] = "Cannot access attendance directory";
    $solutions[] = "Check directory permissions and symlink";
}

if (!empty($issues)) {
    echo "FOUND ISSUES:\n";
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". {$issue}\n";
        echo "   Solution: {$solutions[$i]}\n\n";
    }
} else {
    echo "No obvious issues found. Check Railway logs for more details.\n\n";
}

echo "=== QUICK FIXES FOR RAILWAY ===\n";
echo "1. Ensure storage:link is recreated after each deployment\n";
echo "2. Check file permissions (755 for directories, 644 for files)\n";
echo "3. Verify ASSET_URL is set correctly in Railway environment\n";
echo "4. Check if attendance directory exists in storage/app/public/\n";
echo "5. Test with a fresh image upload to see if new images work\n\n";

echo "Script completed.\n";
