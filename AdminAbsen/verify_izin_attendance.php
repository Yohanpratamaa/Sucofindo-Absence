<?php

/**
 * Simple verification script to check the Izin-Attendance integration
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Izin;
use App\Models\Attendance;
use Carbon\Carbon;

echo "=== IZIN-ATTENDANCE INTEGRATION VERIFICATION ===\n\n";

// Check approved izin
$approvedIzin = Izin::where('approved_by', '!=', null)
    ->where('approved_at', '!=', null)
    ->with('user')
    ->get();

echo "📋 APPROVED IZIN:\n";
foreach ($approvedIzin as $izin) {
    $attendanceCount = Attendance::where('izin_id', $izin->id)->count();
    echo "- ID: {$izin->id} | {$izin->jenis_izin} | {$izin->user->nama} | ";
    echo "Period: {$izin->tanggal_mulai->format('Y-m-d')} to {$izin->tanggal_akhir->format('Y-m-d')} | ";
    echo "Attendance Records: {$attendanceCount}\n";
}

echo "\n📅 ATTENDANCE RECORDS WITH IZIN:\n";
$attendanceWithIzin = Attendance::whereNotNull('izin_id')
    ->with(['user', 'izin'])
    ->orderBy('created_at')
    ->get();

foreach ($attendanceWithIzin as $attendance) {
    echo "- Date: {$attendance->created_at->format('Y-m-d')} | ";
    echo "User: {$attendance->user->nama} | ";
    echo "Status: {$attendance->status_kehadiran} | ";
    echo "Keterangan: " . substr($attendance->keterangan_izin, 0, 50) . "...\n";
}

echo "\n⏳ PENDING IZIN:\n";
$pendingIzin = Izin::whereNull('approved_by')->with('user')->get();
foreach ($pendingIzin as $izin) {
    echo "- ID: {$izin->id} | {$izin->jenis_izin} | {$izin->user->nama} | ";
    echo "Period: {$izin->tanggal_mulai->format('Y-m-d')} to {$izin->tanggal_akhir->format('Y-m-d')}\n";
}

echo "\n✅ Verification complete!\n";
