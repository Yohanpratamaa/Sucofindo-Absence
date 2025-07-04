<?php
/**
 * Pre-deployment validation script untuk Railway
 * Jalankan script ini untuk memastikan semua konfigurasi siap untuk deployment
 */

echo "🔍 RAILWAY DEPLOYMENT VALIDATION\n";
echo "================================\n\n";

$errors = [];
$warnings = [];

// Check 1: Environment file
echo "📄 Checking environment configuration...\n";
if (!file_exists('.env.production')) {
    $errors[] = "File .env.production tidak ditemukan";
} else {
    $env = file_get_contents('.env.production');

    // Check critical variables
    $required = [
        'APP_NAME' => 'Application name',
        'APP_ENV' => 'Environment (should be production)',
        'APP_KEY' => 'Application key',
        'APP_URL' => 'Application URL',
        'DB_CONNECTION' => 'Database connection',
        'DB_HOST' => 'Database host',
        'DB_DATABASE' => 'Database name',
        'DB_USERNAME' => 'Database username',
        'DB_PASSWORD' => 'Database password',
    ];

    foreach ($required as $key => $desc) {
        if (!str_contains($env, $key . '=')) {
            $errors[] = "Missing required environment variable: $key ($desc)";
        }
    }

    // Check Filament specific
    if (!str_contains($env, 'FILAMENT_ENABLED=true')) {
        $warnings[] = "FILAMENT_ENABLED tidak diset ke true";
    }

    if (!str_contains($env, 'FILAMENT_HTTPS=true')) {
        $warnings[] = "FILAMENT_HTTPS tidak diset ke true (diperlukan untuk production)";
    }
}

// Check 2: Railway configuration
echo "🚂 Checking Railway configuration...\n";
if (!file_exists('railway.toml')) {
    $warnings[] = "File railway.toml tidak ditemukan (opsional, nixpacks akan digunakan)";
} else {
    $railway = file_get_contents('railway.toml');
    if (!str_contains($railway, 'healthcheckPath')) {
        $warnings[] = "Health check path tidak dikonfigurasi di railway.toml";
    }
}

// Check 3: Package.json dan dependencies
echo "📦 Checking package.json...\n";
if (!file_exists('package.json')) {
    $warnings[] = "File package.json tidak ditemukan";
} else {
    $package = json_decode(file_get_contents('package.json'), true);
    if (!isset($package['scripts']['build'])) {
        $errors[] = "Build script tidak ditemukan di package.json";
    }

    if (!isset($package['devDependencies']['vite'])) {
        $warnings[] = "Vite tidak ditemukan di devDependencies";
    }
}

// Check 4: Composer.json
echo "🎼 Checking composer.json...\n";
if (!file_exists('composer.json')) {
    $errors[] = "File composer.json tidak ditemukan";
} else {
    $composer = json_decode(file_get_contents('composer.json'), true);
    if (!isset($composer['require']['filament/filament'])) {
        $errors[] = "Filament tidak ditemukan di composer.json dependencies";
    }
}

// Check 5: Critical directories
echo "📁 Checking directory structure...\n";
$required_dirs = [
    'app/Filament',
    'resources/views/filament',
    'storage',
    'bootstrap/cache',
    'public',
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        $errors[] = "Required directory tidak ditemukan: $dir";
    }
}

// Check 6: File permissions (if on Unix)
if (PHP_OS_FAMILY !== 'Windows') {
    echo "🔐 Checking file permissions...\n";
    $writable_dirs = ['storage', 'bootstrap/cache'];

    foreach ($writable_dirs as $dir) {
        if (is_dir($dir) && !is_writable($dir)) {
            $warnings[] = "Directory tidak writable: $dir (akan diset otomatis oleh Railway)";
        }
    }
}

// Check 7: Filament pages
echo "🎨 Checking Filament implementation...\n";
$filament_files = [
    'app/Filament/KepalaBidang/Pages/AttendanceAnalytics.php',
    'resources/views/filament/kepala-bidang/pages/attendance-analytics.blade.php',
];

foreach ($filament_files as $file) {
    if (!file_exists($file)) {
        $errors[] = "Filament file tidak ditemukan: $file";
    }
}

// Check 8: Build scripts
echo "🔨 Checking build scripts...\n";
if (file_exists('build.sh')) {
    if (!is_executable('build.sh') && PHP_OS_FAMILY !== 'Windows') {
        $warnings[] = "build.sh tidak executable (akan diset oleh Railway)";
    }
}

// Results
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 VALIDATION RESULTS\n";
echo str_repeat("=", 50) . "\n\n";

if (empty($errors)) {
    echo "✅ SUCCESS: Tidak ada error kritis ditemukan!\n\n";
} else {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "   • $error\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   • $warning\n";
    }
    echo "\n";
}

// Deployment readiness
if (empty($errors)) {
    echo "🚀 STATUS: READY FOR RAILWAY DEPLOYMENT\n\n";

    echo "📋 Next Steps:\n";
    echo "1. Push code ke Railway repository\n";
    echo "2. Set environment variables di Railway dashboard\n";
    echo "3. Deploy dan monitor health check\n";
    echo "4. Test Filament admin panel: /admin\n";
    echo "5. Test analytics dashboard: /admin/kepala-bidang/attendance-analytics\n\n";

    echo "🌐 Expected URLs:\n";
    echo "• Main: https://sucofindo-absen-production.up.railway.app\n";
    echo "• Admin: https://sucofindo-absen-production.up.railway.app/admin\n";
    echo "• Analytics: https://sucofindo-absen-production.up.railway.app/admin/kepala-bidang/attendance-analytics\n\n";
} else {
    echo "🛑 STATUS: NOT READY - FIX ERRORS FIRST\n\n";
    exit(1);
}

echo "✨ Validation completed!\n";
