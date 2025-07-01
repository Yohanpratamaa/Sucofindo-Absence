<?php
// Laravel Artisan Command untuk test storage
// Run dengan: php artisan tinker --execute="include 'test_storage.php'"

use Illuminate\Support\Facades\Storage;

echo "Testing storage permissions for dinas luar attendance...\n\n";

// Test storage directory creation
$attendanceDir = 'attendance';
if (!Storage::disk('public')->exists($attendanceDir)) {
    echo "Creating attendance directory...\n";
    if (Storage::disk('public')->makeDirectory($attendanceDir)) {
        echo "✓ Directory created successfully\n";
    } else {
        echo "✗ Failed to create directory\n";
        return;
    }
} else {
    echo "✓ Directory exists\n";
}

// Test write permissions
$testFilename = $attendanceDir . '/test_write_' . time() . '.txt';
if (Storage::disk('public')->put($testFilename, 'test content')) {
    echo "✓ Write permission OK\n";

    // Test file size
    $size = Storage::disk('public')->size($testFilename);
    echo "✓ File size: $size bytes\n";

    // Clean up
    Storage::disk('public')->delete($testFilename);
    echo "✓ Cleanup successful\n";
} else {
    echo "✗ Write permission FAILED\n";
    return;
}

// Test base64 image decode dan save
$testBase64 = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8A8A';

$imageData = base64_decode($testBase64);
if ($imageData !== false) {
    echo "✓ Base64 decode successful\n";

    $testImageFilename = $attendanceDir . '/test_image_' . time() . '.jpg';
    if (Storage::disk('public')->put($testImageFilename, $imageData)) {
        echo "✓ Image save successful\n";

        // Verify file
        if (Storage::disk('public')->exists($testImageFilename)) {
            $imageSize = Storage::disk('public')->size($testImageFilename);
            echo "✓ Image file verified, size: $imageSize bytes\n";
        }

        // Clean up
        Storage::disk('public')->delete($testImageFilename);
    } else {
        echo "✗ Image save FAILED\n";
    }
} else {
    echo "✗ Base64 decode FAILED\n";
}

echo "\n✓ All storage tests completed!\n";
echo "Storage is ready for dinas luar attendance photos.\n";
