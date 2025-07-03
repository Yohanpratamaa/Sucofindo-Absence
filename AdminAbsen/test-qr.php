<?php

require_once 'vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

try {
    echo "Testing QR code generation...\n";

    // Test SVG format
    $qrCodeSvg = QrCode::format('svg')->size(100)->generate('test data');
    echo "✓ SVG QR code generated successfully\n";

    // Test PNG format
    try {
        $qrCodePng = QrCode::format('png')->size(100)->generate('test data');
        echo "✓ PNG QR code generated successfully\n";
    } catch (Exception $e) {
        echo "✗ PNG QR code failed: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "✗ QR code generation failed: " . $e->getMessage() . "\n";
}
