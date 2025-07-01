<?php

namespace App\Filament\KepalaBidang\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;

class AttendanceStatsWidget extends BaseStatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get team members (employees only)
        $teamMemberIds = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        // Today's stats
        $todayAttendances = Attendance::whereIn('user_id', $teamMemberIds)
            ->whereDate('created_at', $today)
            ->get();

        $todayPresent = $todayAttendances->whereNotNull('check_in')->count();
        $todayOnTime = $todayAttendances->where('status_kehadiran', 'Tepat Waktu')->count();
        $todayLate = $todayAttendances->where('status_kehadiran', 'Terlambat')->count();

        // This month stats
        $monthAttendances = Attendance::whereIn('user_id', $teamMemberIds)
            ->whereDate('created_at', '>=', $thisMonth)
            ->get();

        $monthPresent = $monthAttendances->whereNotNull('check_in')->count();
        $monthTotal = $teamMemberIds->count() * $today->diffInDaysFiltered(function (Carbon $date) use ($thisMonth) {
            return $date->isWeekday() && $date >= $thisMonth && $date <= Carbon::today();
        });

        $attendanceRate = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 1) : 0;

        return [
            Stat::make('👥 Hadir Hari Ini', $todayPresent)
                ->description('dari ' . $teamMemberIds->count() . ' karyawan')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, $todayPresent]),

            Stat::make('⏰ Tepat Waktu', $todayOnTime)
                ->description('Karyawan tepat waktu hari ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success')
                ->chart([3, 2, 5, 3, 8, 2, $todayOnTime]),

            Stat::make('⚠️ Terlambat', $todayLate)
                ->description('Karyawan terlambat hari ini')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($todayLate > 0 ? 'danger' : 'success')
                ->chart([1, 0, 2, 1, 0, 3, $todayLate]),

            Stat::make('📊 Tingkat Kehadiran', $attendanceRate . '%')
                ->description('Rata-rata bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($attendanceRate >= 90 ? 'success' : ($attendanceRate >= 80 ? 'warning' : 'danger'))
                ->chart([85, 88, 92, 87, 90, 89, $attendanceRate]),
        ];
    }
}
