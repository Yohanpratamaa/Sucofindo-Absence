<?php

namespace App\Filament\KepalaBidang\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;

class AttendanceChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Kehadiran 7 Hari Terakhir';
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $teamMemberIds = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return Carbon::today()->subDays($daysAgo);
        });

        $attendanceData = $last7Days->map(function ($date) use ($teamMemberIds) {
            $attendances = Attendance::whereIn('user_id', $teamMemberIds)
                ->whereDate('created_at', $date)
                ->get();

            return [
                'date' => $date->format('M d'),
                'hadir' => $attendances->whereNotNull('check_in')->count(),
                'tepat_waktu' => $attendances->where('status_kehadiran', 'Tepat Waktu')->count(),
                'terlambat' => $attendances->where('status_kehadiran', 'Terlambat')->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $attendanceData->pluck('hadir')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#FFFFFF',
                    'pointHoverBackgroundColor' => '#FFFFFF',
                    'pointHoverBorderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                ],
                [
                    'label' => 'Tepat Waktu',
                    'data' => $attendanceData->pluck('tepat_waktu')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => '#FFFFFF',
                    'pointHoverBackgroundColor' => '#FFFFFF',
                    'pointHoverBorderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $attendanceData->pluck('terlambat')->toArray(),
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'pointBackgroundColor' => 'rgb(245, 158, 11)',
                    'pointBorderColor' => '#FFFFFF',
                    'pointHoverBackgroundColor' => '#FFFFFF',
                    'pointHoverBorderColor' => 'rgb(245, 158, 11)',
                    'fill' => true,
                ],
            ],
            'labels' => $attendanceData->pluck('date')->toArray(),
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
            'maintainAspectRatio' => true,
            'aspectRatio' => 1.8,
            'layout' => [
                'padding' => [
                    'top' => 10,
                    'bottom' => 10,
                    'left' => 5,
                    'right' => 5,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 11,
                            'family' => 'Inter, sans-serif',
                        ],
                        'color' => '#6B7280',
                        'maxTicksLimit' => 6,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                            'family' => 'Inter, sans-serif',
                        ],
                        'color' => '#6B7280',
                        'maxRotation' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'align' => 'center',
                    'labels' => [
                        'boxWidth' => 12,
                        'boxHeight' => 12,
                        'padding' => 20,
                        'usePointStyle' => true,
                        'font' => [
                            'size' => 12,
                            'family' => 'Inter, sans-serif',
                            'weight' => '500',
                        ],
                        'color' => '#374151',
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#F9FAFB',
                    'bodyColor' => '#F9FAFB',
                    'borderColor' => '#374151',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                ],
            ],
            'elements' => [
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 6,
                    'borderWidth' => 2,
                ],
                'line' => [
                    'borderWidth' => 3,
                    'tension' => 0.3,
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
