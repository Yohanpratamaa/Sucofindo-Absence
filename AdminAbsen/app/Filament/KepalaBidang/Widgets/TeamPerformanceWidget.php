<?php

namespace App\Filament\KepalaBidang\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TeamPerformanceWidget extends ChartWidget
{
    protected static ?string $heading = 'Performa Tim Bulanan';

    protected function getData(): array
    {
        // Get team members
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        // Get last 6 months data
        $months = [];
        $attendanceData = [];
        $lateData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $months[] = $date->format('M Y');
            
            // Total attendance
            $attendance = Attendance::whereIn('user_id', $teamMembers)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthYear])
                ->count();
            
            // Late attendance
            $late = Attendance::whereIn('user_id', $teamMembers)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthYear])
                ->whereRaw("TIME(check_in) > '08:00:00'")
                ->count();
            
            $attendanceData[] = $attendance;
            $lateData[] = $late;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kehadiran',
                    'data' => $attendanceData,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
                [
                    'label' => 'Keterlambatan',
                    'data' => $lateData,
                    'backgroundColor' => '#F59E0B',
                    'borderColor' => '#D97706',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
