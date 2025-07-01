<?php

namespace App\Filament\KepalaBidang\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;

class AttendanceTypeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Tipe Absensi Bulan Ini';
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $teamMemberIds = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        $thisMonth = Carbon::now()->startOfMonth();

        $attendanceTypes = Attendance::whereIn('user_id', $teamMemberIds)
            ->whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('attendance_type, COUNT(*) as count')
            ->groupBy('attendance_type')
            ->pluck('count', 'attendance_type')
            ->toArray();

        $wfo = $attendanceTypes['WFO'] ?? 0;
        $dinasLuar = $attendanceTypes['Dinas Luar'] ?? 0;

        return [
            'datasets' => [
                [
                    'data' => [$wfo, $dinasLuar],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Blue for WFO
                        'rgba(245, 158, 11, 0.8)',   // Orange for Dinas Luar
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                    ],
                    'hoverBackgroundColor' => [
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(245, 158, 11, 0.9)',
                    ],
                ],
            ],
            'labels' => ['Work From Office (' . $wfo . ')', 'Dinas Luar (' . $dinasLuar . ')'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'aspectRatio' => 1.5,
            'layout' => [
                'padding' => [
                    'top' => 15,
                    'bottom' => 10,
                    'left' => 10,
                    'right' => 10,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'align' => 'center',
                    'labels' => [
                        'boxWidth' => 14,
                        'boxHeight' => 14,
                        'padding' => 20,
                        'usePointStyle' => false,
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
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'elements' => [
                'arc' => [
                    'borderWidth' => 2,
                    'borderColor' => '#FFFFFF',
                    'hoverBorderWidth' => 3,
                ],
            ],
            'cutout' => '60%',
            'radius' => '85%',
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }
}
