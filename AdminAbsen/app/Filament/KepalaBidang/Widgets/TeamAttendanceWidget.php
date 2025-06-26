<?php

namespace App\Filament\KepalaBidang\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeamAttendanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get team members (all employees under this kepala bidang - for demo we'll use all employees)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        // Get today's attendance
        $todayAttendance = Attendance::whereIn('user_id', $teamMembers)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Get team size
        $teamSize = $teamMembers->count();

        // Get this week attendance
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekAttendance = Attendance::whereIn('user_id', $teamMembers)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Get late attendance today
        $lateToday = Attendance::whereIn('user_id', $teamMembers)
            ->whereDate('created_at', Carbon::today())
            ->whereRaw("TIME(check_in) > '08:00:00'")
            ->count();

        return [
            Stat::make('Tim Aktif', $teamSize)
                ->description('Total anggota tim')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Hadir Hari Ini', $todayAttendance)
                ->description("Dari {$teamSize} anggota tim")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Hadir Minggu Ini', $weekAttendance)
                ->description('Total kehadiran minggu ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Terlambat Hari Ini', $lateToday)
                ->description('Anggota tim yang terlambat')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
