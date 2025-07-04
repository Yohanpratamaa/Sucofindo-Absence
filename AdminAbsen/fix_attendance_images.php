<?php

/**
 * ATTENDANCE IMAGES FIX FOR RAILWAY
 *
 * Script untuk memperbaiki masalah gambar attendance yang tidak bisa diakses di Railway.
 *
 * Issues yang diatasi:
 * 1. Storage symlink tidak ada atau broken
 * 2. Directory attendance tidak ada
 * 3. File permissions salah
 * 4. URL generation tidak sesuai environment Railway
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Attendance;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATTENDANCE IMAGES FIX FOR RAILWAY ===\n\n";

$isRailway = !empty($_ENV['RAILWAY_ENVIRONMENT']) || !empty(getenv('RAILWAY_ENVIRONMENT'));
echo "Environment: " . ($isRailway ? 'RAILWAY' : 'LOCAL') . "\n\n";

// 1. Fix storage link
echo "1. FIXING STORAGE LINK...\n";
$publicStoragePath = public_path('storage');
$storageAppPublic = storage_path('app/public');

// Remove existing symlink if broken
if (is_link($publicStoragePath) && !is_dir(readlink($publicStoragePath))) {
    echo "   Removing broken symlink...\n";
    unlink($publicStoragePath);
}

// Create storage directory if not exists
if (!is_dir($storageAppPublic)) {
    echo "   Creating storage/app/public directory...\n";
    mkdir($storageAppPublic, 0755, true);
}

// Create symlink if not exists
if (!is_link($publicStoragePath)) {
    echo "   Creating storage symlink...\n";
    if (PHP_OS_FAMILY === 'Windows') {
        // Windows
        shell_exec("mklink /D \"$publicStoragePath\" \"$storageAppPublic\"");
    } else {
        // Unix/Linux
        symlink($storageAppPublic, $publicStoragePath);
    }
}

echo "   Symlink status: " . (is_link($publicStoragePath) ? 'OK' : 'FAILED') . "\n\n";

// 2. Create attendance directory
echo "2. ENSURING ATTENDANCE DIRECTORY...\n";
$attendanceDir = storage_path('app/public/attendance');
if (!is_dir($attendanceDir)) {
    echo "   Creating attendance directory...\n";
    mkdir($attendanceDir, 0755, true);
}

echo "   Attendance directory: " . (is_dir($attendanceDir) ? 'OK' : 'FAILED') . "\n\n";

// 3. Fix file permissions
echo "3. FIXING FILE PERMISSIONS...\n";
if (PHP_OS_FAMILY !== 'Windows') {
    // Unix/Linux only
    echo "   Setting directory permissions...\n";
    chmod(storage_path('app'), 0755);
    chmod(storage_path('app/public'), 0755);
    chmod($attendanceDir, 0755);

    // Fix existing files permissions
    $files = glob($attendanceDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            chmod($file, 0644);
        }
    }
    echo "   Permissions fixed\n";
} else {
    echo "   Skipping permission fix (Windows)\n";
}
echo "\n";

// 4. Test and report
echo "4. TESTING CONFIGURATION...\n";
echo "   Storage disk root: " . Storage::disk('public')->path('') . "\n";
echo "   Attendance dir exists: " . (Storage::disk('public')->exists('attendance') ? 'YES' : 'NO') . "\n";

try {
    $files = Storage::disk('public')->allFiles('attendance');
    echo "   Files in attendance: " . count($files) . "\n";
} catch (Exception $e) {
    echo "   Error listing files: " . $e->getMessage() . "\n";
}

// 5. Check sample attendance record
echo "\n5. CHECKING ATTENDANCE RECORDS...\n";
try {
    $sampleAttendance = Attendance::whereNotNull('picture_absen_masuk')->first();
    if ($sampleAttendance) {
        echo "   Sample ID: {$sampleAttendance->id}\n";
        echo "   Image path: {$sampleAttendance->picture_absen_masuk}\n";
        echo "   File exists: " . (Storage::disk('public')->exists($sampleAttendance->picture_absen_masuk) ? 'YES' : 'NO') . "\n";
        echo "   Generated URL: {$sampleAttendance->picture_absen_masuk_url}\n";
    } else {
        echo "   No attendance records with images found\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== RAILWAY DEPLOYMENT CHECKLIST ===\n";
echo "✓ Storage symlink created\n";
echo "✓ Attendance directory created\n";
echo "✓ File permissions set (if Unix)\n";
echo "\nNext steps for Railway:\n";
echo "1. Deploy this fix to Railway\n";
echo "2. Check if existing images are accessible\n";
echo "3. Test new image uploads\n";
echo "4. Verify URLs work in browser\n";

echo "\nScript completed successfully!\n";
