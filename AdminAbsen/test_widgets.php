<?php

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing Dashboard Widgets...\n\n";

    // Test AttendanceStatsOverview data
    echo "=== AttendanceStatsOverview Data ===\n";

    $totalPegawai = \App\Models\Pegawai::count();
    $absensiHariIni = \App\Models\Attendance::today()->count();
    $absensiBulanIni = \App\Models\Attendance::thisMonth()->count();

    echo "Total Pegawai: {$totalPegawai}\n";
    echo "Absensi Hari Ini: {$absensiHariIni}\n";
    echo "Absensi Bulan Ini: {$absensiBulanIni}\n";

    $persentaseHariIni = $totalPegawai > 0 ? round(($absensiHariIni / $totalPegawai) * 100, 1) : 0;
    echo "Persentase Kehadiran Hari Ini: {$persentaseHariIni}%\n";

    // Test AttendanceChart data (7 hari terakhir)
    echo "\n=== AttendanceChart Data (7 Hari Terakhir) ===\n";
    for ($i = 6; $i >= 0; $i--) {
        $date = \Carbon\Carbon::now()->subDays($i);
        $count = \App\Models\Attendance::whereDate('created_at', $date)->count();
        echo "{$date->format('d M')}: {$count} absensi\n";
    }

    // Test AttendanceStatusChart data
    echo "\n=== AttendanceStatusChart Data (Bulan Ini) ===\n";

    $tepatWaktu = \App\Models\Attendance::thisMonth()
        ->whereTime('check_in', '<=', '08:00:00')
        ->whereNotNull('check_in')
        ->count();

    $terlambat = \App\Models\Attendance::thisMonth()
        ->whereTime('check_in', '>', '08:00:00')
        ->whereNotNull('check_in')
        ->count();

    $tidakHadir = \App\Models\Attendance::thisMonth()
        ->whereNull('check_in')
        ->count();

    echo "Tepat Waktu: {$tepatWaktu}\n";
    echo "Terlambat: {$terlambat}\n";
    echo "Tidak Hadir: {$tidakHadir}\n";

    // Test AttendanceTypeChart data
    echo "\n=== AttendanceTypeChart Data (Bulan Ini) ===\n";

    $wfo = \App\Models\Attendance::thisMonth()
        ->where('attendance_type', 'WFO')
        ->count();

    $dinasLuar = \App\Models\Attendance::thisMonth()
        ->where('attendance_type', 'Dinas Luar')
        ->count();

    echo "WFO: {$wfo}\n";
    echo "Dinas Luar: {$dinasLuar}\n";

    // Test MonthlyAttendanceChart data
    echo "\n=== MonthlyAttendanceChart Data (6 Bulan Terakhir) ===\n";
    for ($i = 5; $i >= 0; $i--) {
        $month = \Carbon\Carbon::now()->subMonths($i);
        $count = \App\Models\Attendance::whereYear('created_at', $month->year)
                          ->whereMonth('created_at', $month->month)
                          ->count();
        echo "{$month->format('M Y')}: {$count} absensi\n";
    }

    // Test RecentAttendanceTable data
    echo "\n=== RecentAttendanceTable Data (10 Terbaru) ===\n";
    $recentAttendances = \App\Models\Attendance::with('user')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    foreach ($recentAttendances as $attendance) {
        $nama = $attendance->user->nama ?? 'N/A';
        $npp = $attendance->user->npp ?? 'N/A';
        $tanggal = $attendance->created_at->format('d M Y H:i');
        $status = $attendance->status_kehadiran;
        echo "- {$nama} ({$npp}) | {$tanggal} | {$status}\n";
    }

    // Test TopAttendanceTable data
    echo "\n=== TopAttendanceTable Data (Top 5) ===\n";
    $topAttendance = \App\Models\Pegawai::withCount([
        'attendances as total_absensi' => function ($query) {
            $query->thisMonth();
        },
        'attendances as tepat_waktu' => function ($query) {
            $query->thisMonth()
                  ->whereTime('check_in', '<=', '08:00:00')
                  ->whereNotNull('check_in');
        }
    ])
    ->having('total_absensi', '>', 0)
    ->orderBy('total_absensi', 'desc')
    ->take(5)
    ->get();

    foreach ($topAttendance as $index => $pegawai) {
        $ranking = $index + 1;
        $persentase = $pegawai->total_absensi > 0 ?
            round(($pegawai->tepat_waktu / $pegawai->total_absensi) * 100, 1) : 0;
        echo "{$ranking}. {$pegawai->nama} | Total: {$pegawai->total_absensi} | Tepat Waktu: {$persentase}%\n";
    }

    echo "\n=== All Widget Tests Completed Successfully! ===\n";
    echo "✅ Dashboard widgets are ready to display data\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
