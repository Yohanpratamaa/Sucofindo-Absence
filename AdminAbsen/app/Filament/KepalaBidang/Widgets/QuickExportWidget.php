<?php

namespace App\Filament\KepalaBidang\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickExportWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('📗 Export Excel Tim', 'Rekap Absensi')
                ->description('Export semua anggota tim dalam format Excel untuk analisis')
                ->descriptionIcon('heroicon-m-document-arrow-down')
                ->color('success')
                ->url('/kepala-bidang/attendance-reports'),

            Stat::make('📄 Export PDF Tim', 'Laporan Formal')
                ->description('Export semua anggota tim dalam format PDF siap print')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('danger')
                ->url('/kepala-bidang/attendance-reports'),

            Stat::make('📊 Export Detail Excel', 'Per Karyawan')
                ->description('Export detail absensi individual dalam format Excel')
                ->descriptionIcon('heroicon-m-user')
                ->color('info')
                ->url('/kepala-bidang/attendance-reports'),

            Stat::make('📋 Export Detail PDF', 'Evaluasi Individual')
                ->description('Export detail absensi individual dalam format PDF')
                ->descriptionIcon('heroicon-m-clipboard-document')
                ->color('warning')
                ->url('/kepala-bidang/attendance-reports'),
        ];
    }
}
