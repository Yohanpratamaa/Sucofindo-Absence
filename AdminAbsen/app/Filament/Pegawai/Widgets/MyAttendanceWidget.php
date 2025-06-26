<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyAttendanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Get current month attendance
        $currentMonth = Carbon::now()->format('Y-m');
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->count();

        // Get this week attendance
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weeklyAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Get today attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        // Get late attendance this month
        $lateAttendance = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->whereRaw("TIME(check_in) > '08:00:00'")
            ->count();

        return [
            Stat::make('Kehadiran Bulan Ini', $monthlyAttendance)
                ->description('Total hari hadir')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Kehadiran Minggu Ini', $weeklyAttendance)
                ->description('Total hari hadir minggu ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Status Hari Ini', $todayAttendance ? 'Sudah Absen' : 'Belum Absen')
                ->description($todayAttendance ? 'Anda sudah melakukan absensi' : 'Jangan lupa absen')
                ->descriptionIcon($todayAttendance ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($todayAttendance ? 'success' : 'warning'),

            Stat::make('Keterlambatan', $lateAttendance)
                ->description('Jumlah terlambat bulan ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
