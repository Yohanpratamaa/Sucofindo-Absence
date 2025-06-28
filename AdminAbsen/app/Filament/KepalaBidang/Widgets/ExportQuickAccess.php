<?php

namespace App\Filament\KepalaBidang\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExportQuickAccess extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Export Laporan', 'Data & Export')
                ->description('Akses semua fitur export dan data laporan')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->url('/kepala-bidang/attendance-reports'),

            Stat::make('Data Laporan', 'View & Export')
                ->description('Lihat data rekap tim dan export')
                ->descriptionIcon('heroicon-m-table-cells')
                ->color('success')
                ->url('/kepala-bidang/attendance-reports'),

            Stat::make('Analytics', 'Analisis Absensi')
                ->description('Insights dan rekomendasi export')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->url('/kepala-bidang/attendance-analytics'),
        ];
    }
}
