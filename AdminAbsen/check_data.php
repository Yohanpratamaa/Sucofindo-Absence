<?php

// Bootstrap Laravel
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Attendance;

try {
    echo "=== ATTENDANCE DATA SUMMARY ===\n";
    
    $total = Attendance::count();
    $wfo = Attendance::where('attendance_type', 'WFO')->count();
    $dinasLuar = Attendance::where('attendance_type', 'Dinas Luar')->count();
    
    echo "Total attendance records: {$total}\n";
    echo "WFO records: {$wfo}\n";
    echo "Dinas Luar records: {$dinasLuar}\n\n";
    
    echo "=== DINAS LUAR WITH IMAGES ===\n";
    $dinasLuarWithImages = Attendance::where('attendance_type', 'Dinas Luar')
        ->where(function($query) {
            $query->whereNotNull('picture_absen_masuk')
                  ->orWhereNotNull('picture_absen_siang')
                  ->orWhereNotNull('picture_absen_pulang');
        })
        ->count();
    
    echo "Dinas Luar with images: {$dinasLuarWithImages}\n\n";
    
    echo "=== RECENT DINAS LUAR RECORDS ===\n";
    $recentDinasLuar = Attendance::where('attendance_type', 'Dinas Luar')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    foreach ($recentDinasLuar as $att) {
        echo "ID: {$att->id}\n";
        echo "Date: {$att->created_at->format('Y-m-d H:i')}\n";
        echo "User: {$att->user_id}\n";
        echo "Check In: " . ($att->check_in ? $att->check_in->format('H:i') : 'NULL') . "\n";
        echo "Absen Siang: " . ($att->absen_siang ? $att->absen_siang->format('H:i') : 'NULL') . "\n";
        echo "Check Out: " . ($att->check_out ? $att->check_out->format('H:i') : 'NULL') . "\n";
        echo "Picture Masuk: " . ($att->picture_absen_masuk ?? 'NULL') . "\n";
        echo "Picture Siang: " . ($att->picture_absen_siang ?? 'NULL') . "\n";
        echo "Picture Pulang: " . ($att->picture_absen_pulang ?? 'NULL') . "\n";
        echo str_repeat("-", 40) . "\n";
    }
    
    echo "\n=== WFO RECORDS WITH IMAGES FOR COMPARISON ===\n";
    $wfoWithImages = Attendance::where('attendance_type', 'WFO')
        ->where(function($query) {
            $query->whereNotNull('picture_absen_masuk')
                  ->orWhereNotNull('picture_absen_pulang');
        })
        ->take(2)
        ->get();
    
    foreach ($wfoWithImages as $att) {
        echo "WFO ID: {$att->id}, Date: {$att->created_at->format('Y-m-d')}\n";
        echo "Picture Masuk: " . ($att->picture_absen_masuk ?? 'NULL') . "\n";
        echo "Picture Pulang: " . ($att->picture_absen_pulang ?? 'NULL') . "\n";
        echo str_repeat("-", 30) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
