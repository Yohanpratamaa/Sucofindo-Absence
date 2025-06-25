<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AttendanceTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Tipe Absensi Bulan Ini';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        // Hitung tipe absensi bulan ini
        $wfo = Attendance::thisMonth()
            ->where('attendance_type', 'WFO')
            ->count();

        $dinasLuar = Attendance::thisMonth()
            ->where('attendance_type', 'Dinas Luar')
            ->count();

        $other = Attendance::thisMonth()
            ->where('attendance_type', '!=', 'WFO')
            ->where('attendance_type', '!=', 'Dinas Luar')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tipe Absensi',
                    'data' => [$wfo, $dinasLuar, $other],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // Blue - WFO
                        'rgb(245, 158, 11)',   // Amber - Dinas Luar
                        'rgb(107, 114, 128)',  // Gray - Other
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['WFO', 'Dinas Luar', 'Lainnya'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
