<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Absensi 7 Hari Terakhir';

    protected static ?int $sort = 2;

    // Enable real-time polling every 30 seconds
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Ambil data 7 hari terakhir dengan timezone Jakarta
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i);
            $count = Attendance::whereDate('created_at', $date)->count();

            $data[] = $count;
            $labels[] = $date->format('d M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Absensi',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
