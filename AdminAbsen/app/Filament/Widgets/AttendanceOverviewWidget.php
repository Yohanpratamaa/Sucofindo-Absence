<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AttendanceOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Total karyawan aktif
        $totalEmployees = Pegawai::active()->count();
        
        // Absensi hari ini
        $todayAttendance = Attendance::today()->count();
        $todayPresenceRate = $totalEmployees > 0 ? round(($todayAttendance / $totalEmployees) * 100, 1) : 0;
        
        // Absensi bulan ini
        $monthlyAttendance = Attendance::thisMonth()->count();
        $workDaysThisMonth = $this->getWorkDaysInMonth();
        $expectedMonthlyAttendance = $totalEmployees * $workDaysThisMonth;
        $monthlyPresenceRate = $expectedMonthlyAttendance > 0 ? round(($monthlyAttendance / $expectedMonthlyAttendance) * 100, 1) : 0;
        
        // Keterlambatan bulan ini
        $lateAttendances = Attendance::thisMonth()
            ->whereRaw("TIME(check_in) > '08:00:00'")
            ->count();
        $lateRate = $monthlyAttendance > 0 ? round(($lateAttendances / $monthlyAttendance) * 100, 1) : 0;
        
        // Total lembur bulan ini (dalam jam)
        $totalOvertimeMinutes = Attendance::thisMonth()
            ->where('overtime', '>', 0)
            ->sum('overtime');
        $totalOvertimeHours = round($totalOvertimeMinutes / 60, 1);

        return [
            Stat::make('Kehadiran Hari Ini', $todayAttendance . ' / ' . $totalEmployees)
                ->description($todayPresenceRate . '% dari total karyawan')
                ->descriptionIcon('heroicon-m-users')
                ->color($todayPresenceRate >= 90 ? 'success' : ($todayPresenceRate >= 75 ? 'warning' : 'danger'))
                ->chart($this->getDailyAttendanceChart()),
                
            Stat::make('Tingkat Kehadiran Bulan Ini', $monthlyPresenceRate . '%')
                ->description($monthlyAttendance . ' dari ' . $expectedMonthlyAttendance . ' kehadiran')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($monthlyPresenceRate >= 90 ? 'success' : ($monthlyPresenceRate >= 75 ? 'warning' : 'danger')),
                
            Stat::make('Tingkat Keterlambatan', $lateRate . '%')
                ->description($lateAttendances . ' dari ' . $monthlyAttendance . ' kehadiran')
                ->descriptionIcon('heroicon-m-clock')
                ->color($lateRate <= 5 ? 'success' : ($lateRate <= 15 ? 'warning' : 'danger')),
                
            Stat::make('Total Lembur Bulan Ini', $totalOvertimeHours . ' jam')
                ->description(round($totalOvertimeMinutes / 60, 1) . ' jam dari ' . $monthlyAttendance . ' kehadiran')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
        ];
    }

    private function getWorkDaysInMonth(): int
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $workDays = 0;
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }
        
        return $workDays;
    }

    private function getDailyAttendanceChart(): array
    {
        $data = [];
        
        // Get attendance data for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Attendance::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        return $data;
    }
}
