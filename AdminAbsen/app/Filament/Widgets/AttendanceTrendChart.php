<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AttendanceTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Absensi 30 Hari Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $wfoData = [];
        $dinasLuarData = [];

        // Get data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            $totalAttendance = Attendance::whereDate('created_at', $date)->count();
            $wfoCount = Attendance::whereDate('created_at', $date)
                ->where('attendance_type', 'WFO')
                ->count();
            $dinasLuarCount = Attendance::whereDate('created_at', $date)
                ->where('attendance_type', 'Dinas Luar')
                ->count();

            $data[] = $totalAttendance;
            $wfoData[] = $wfoCount;
            $dinasLuarData[] = $dinasLuarCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Absensi',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'WFO',
                    'data' => $wfoData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Dinas Luar',
                    'data' => $dinasLuarData,
                    'borderColor' => 'rgb(251, 191, 36)',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
