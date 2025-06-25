<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AttendanceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Kehadiran Bulan Ini';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Hitung status kehadiran bulan ini
        $tepatWaktu = Attendance::thisMonth()
            ->whereTime('check_in', '<=', '08:00:00')
            ->whereNotNull('check_in')
            ->count();

        $terlambat = Attendance::thisMonth()
            ->whereTime('check_in', '>', '08:00:00')
            ->whereNotNull('check_in')
            ->count();

        $tidakHadir = Attendance::thisMonth()
            ->whereNull('check_in')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Kehadiran',
                    'data' => [$tepatWaktu, $terlambat, $tidakHadir],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',  // Green - Tepat Waktu
                        'rgb(245, 158, 11)', // Amber - Terlambat  
                        'rgb(239, 68, 68)',  // Red - Tidak Hadir
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
