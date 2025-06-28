<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisWeek = Carbon::now()->startOfWeek();

        // Total karyawan aktif
        $totalEmployees = Pegawai::where('status', 'active')
            ->where('role_user', 'employee')
            ->count();

        // Absensi hari ini
        $todayAttendance = Attendance::whereDate('created_at', $today)->count();

        // Yang sudah check in hari ini
        $todayCheckedIn = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in')
            ->count();

        // Yang sudah check out hari ini
        $todayCheckedOut = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_out')
            ->count();

        // Terlambat hari ini
        $todayLate = Attendance::whereDate('created_at', $today)
            ->where('status_kehadiran', 'Terlambat')
            ->count();

        // Absensi minggu ini
        $weeklyAttendance = Attendance::where('created_at', '>=', $thisWeek)->count();

        // Absensi bulan ini
        $monthlyAttendance = Attendance::where('created_at', '>=', $thisMonth)->count();

        // WFO vs Dinas Luar hari ini
        $todayWFO = Attendance::whereDate('created_at', $today)
            ->where('attendance_type', 'WFO')
            ->count();

        $todayDinasLuar = Attendance::whereDate('created_at', $today)
            ->where('attendance_type', 'Dinas Luar')
            ->count();

        // Tingkat kehadiran bulan ini
        $expectedAttendance = $totalEmployees * Carbon::now()->day;
        $attendanceRate = $expectedAttendance > 0 ? round(($monthlyAttendance / $expectedAttendance) * 100, 1) : 0;

        return [
            Stat::make('Total Karyawan Aktif', $totalEmployees)
                ->description('Karyawan dalam sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Absensi Hari Ini', $todayAttendance)
                ->description($todayCheckedIn . ' check in, ' . $todayCheckedOut . ' check out')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success')
                ->chart($this->getWeeklyChart()),

            Stat::make('Terlambat Hari Ini', $todayLate)
                ->description('Dari ' . $todayAttendance . ' absensi')
                ->descriptionIcon('heroicon-m-clock')
                ->color($todayLate > 0 ? 'danger' : 'success'),

            Stat::make('Tingkat Kehadiran', $attendanceRate . '%')
                ->description('Bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($attendanceRate >= 90 ? 'success' : ($attendanceRate >= 75 ? 'warning' : 'danger')),

            Stat::make('WFO vs Dinas Luar', $todayWFO . ' : ' . $todayDinasLuar)
                ->description('Perbandingan hari ini')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            Stat::make('Absensi Bulan Ini', $monthlyAttendance)
                ->description('Total ' . Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-document-chart-bar')
                ->color('warning'),
        ];
    }

    private function getWeeklyChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Attendance::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
