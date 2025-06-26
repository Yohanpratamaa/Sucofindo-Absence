<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceStatsWidget extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kehadiran Bulanan';

    protected function getData(): array
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        
        // Get attendance data for last 12 months
        $months = [];
        $attendanceData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $months[] = $date->format('M Y');
            
            $attendance = Attendance::where('user_id', $user->id)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthYear])
                ->count();
            
            $attendanceData[] = $attendance;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kehadiran',
                    'data' => $attendanceData,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
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
