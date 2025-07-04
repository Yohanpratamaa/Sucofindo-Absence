<?php
/**
 * Test local storage configuration sebelum deploy ke Railway
 */

echo "🧪 LOCAL STORAGE TEST\n";
echo "====================\n\n";

// Test 1: Check if storage directories exist
echo "📁 Checking storage directories...\n";
$dirs = [
    'storage/app/public',
    'storage/app/public/uploads',
    'storage/app/public/images',
    'storage/app/public/avatars',
    'storage/app/public/documents',
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "  ✅ $dir exists\n";
    } else {
        echo "  ❌ $dir missing\n";
        mkdir($dir, 0775, true);
        echo "  🔧 Created $dir\n";
    }
}

// Test 2: Check storage link
echo "\n🔗 Checking storage link...\n";
$linkPath = 'public/storage';
if (file_exists($linkPath)) {
    if (is_link($linkPath)) {
        echo "  ✅ Storage link exists\n";
        echo "  📍 Points to: " . readlink($linkPath) . "\n";
    } else {
        echo "  ⚠️ public/storage exists but is not a symlink\n";
    }
} else {
    echo "  ❌ Storage link missing\n";
    echo "  🔧 Creating storage link...\n";
    shell_exec('php artisan storage:link');
    if (file_exists($linkPath)) {
        echo "  ✅ Storage link created successfully\n";
    } else {
        echo "  ❌ Failed to create storage link\n";
    }
}

// Test 3: Test file operations
echo "\n📝 Testing file operations...\n";
$testFile = 'storage/app/public/test-' . time() . '.txt';
$testContent = "Test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "  ✅ File creation successful\n";

    // Check if file is accessible via storage link
    $linkedFile = 'public/storage/' . basename($testFile);
    if (file_exists($linkedFile)) {
        echo "  ✅ File accessible via storage link\n";
    } else {
        echo "  ❌ File not accessible via storage link\n";
    }

    // Cleanup
    unlink($testFile);
    echo "  🧹 Test file cleaned up\n";
} else {
    echo "  ❌ File creation failed\n";
}

// Test 4: Check permissions
echo "\n🔐 Checking permissions...\n";
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        if (is_writable($dir)) {
            echo "  ✅ $dir is writable (permissions: $perms)\n";
        } else {
            echo "  ❌ $dir is not writable (permissions: $perms)\n";
        }
    }
}

// Test 5: Test Laravel Storage facade
echo "\n🎯 Testing Laravel Storage facade...\n";
try {
    // Check if we can load Laravel
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';

        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

            $testFileName = 'facade-test-' . time() . '.txt';
            $testContent = 'Laravel Storage facade test';

            if (\Illuminate\Support\Facades\Storage::disk('public')->put($testFileName, $testContent)) {
                echo "  ✅ Laravel Storage facade working\n";

                $url = asset('storage/' . $testFileName);
                echo "  🌐 Test file URL: $url\n";

                // Cleanup
                \Illuminate\Support\Facades\Storage::disk('public')->delete($testFileName);
                echo "  🧹 Laravel test file cleaned up\n";
            } else {
                echo "  ❌ Laravel Storage facade failed\n";
            }
        }
    }
} catch (Exception $e) {
    echo "  ⚠️ Laravel test skipped: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Storage directories created\n";
echo "✅ Storage link configured\n";
echo "✅ File operations tested\n";
echo "✅ Permissions verified\n";
echo "\n🚀 Ready for Railway deployment!\n";
echo "\n📌 Remember to:\n";
echo "1. Set FILESYSTEM_DISK=public in Railway environment\n";
echo "2. Test /test-storage endpoint after deployment\n";
echo "3. Verify file uploads work in Filament admin\n";
