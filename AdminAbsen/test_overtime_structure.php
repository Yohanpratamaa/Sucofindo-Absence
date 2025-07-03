<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Overtime Assignment Structure ===\n\n";

// Test 1: Check table columns
echo "1. Checking overtime_assignments table structure:\n";
try {
    $columns = Schema::getColumnListing('overtime_assignments');
    echo "Columns: " . implode(', ', $columns) . "\n\n";
} catch (Exception $e) {
    echo "Error getting columns: " . $e->getMessage() . "\n\n";
}

// Test 2: Check if we can create an OvertimeAssignment
echo "2. Testing OvertimeAssignment model creation:\n";
try {
    // Get a test pegawai
    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "No pegawai found in database\n";
        exit;
    }

    echo "Using pegawai: {$pegawai->nama} (ID: {$pegawai->id})\n";

    // Create test overtime assignment
    $overtime = new OvertimeAssignment();
    $overtime->pegawai_id = $pegawai->id;
    $overtime->overtime_id = 'OT-' . date('Ymd') . '-' . str_pad(1, 3, '0', STR_PAD_LEFT);
    $overtime->tanggal_lembur = '2025-01-15';
    $overtime->jam_mulai = '18:00:00';
    $overtime->jam_selesai = '20:00:00';
    $overtime->total_jam = 2;
    $overtime->keterangan = 'Test overtime assignment';
    $overtime->status = 'pending';
    $overtime->assigned_by = 1; // Assuming admin user ID 1

    echo "Attempting to save overtime assignment...\n";
    $overtime->save();

    echo "✓ Overtime assignment created successfully!\n";
    echo "ID: {$overtime->id}\n";
    echo "Overtime ID: {$overtime->overtime_id}\n";
    echo "Tanggal: {$overtime->tanggal_lembur}\n";
    echo "Jam: {$overtime->jam_mulai} - {$overtime->jam_selesai}\n";
    echo "Total Jam: {$overtime->total_jam}\n";

    // Test accessor
    if (method_exists($overtime, 'getTotalJamFormattedAttribute')) {
        echo "Total Jam Formatted: {$overtime->total_jam_formatted}\n";
    }

    echo "Keterangan: {$overtime->keterangan}\n";
    echo "Status: {$overtime->status}\n\n";

    // Clean up test data
    $overtime->delete();
    echo "✓ Test data cleaned up\n\n";

} catch (Exception $e) {
    echo "✗ Error creating overtime assignment: " . $e->getMessage() . "\n\n";
}

// Test 3: Check fillable fields
echo "3. Checking OvertimeAssignment fillable fields:\n";
$model = new OvertimeAssignment();
$fillable = $model->getFillable();
echo "Fillable fields: " . implode(', ', $fillable) . "\n\n";

echo "=== Test Complete ===\n";
