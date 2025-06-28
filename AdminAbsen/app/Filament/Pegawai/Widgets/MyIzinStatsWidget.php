<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Izin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyIzinStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $userId = Auth::id();
        
        return [
            Stat::make('Total Pengajuan Izin', Izin::where('user_id', $userId)->count())
                ->description('Total pengajuan izin')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Menunggu Persetujuan', Izin::where('user_id', $userId)->whereNull('approved_by')->count())
                ->description('Pengajuan pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Disetujui', Izin::where('user_id', $userId)->whereNotNull('approved_by')->whereNotNull('approved_at')->count())
                ->description('Izin disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak', Izin::where('user_id', $userId)->whereNotNull('approved_by')->whereNull('approved_at')->count())
                ->description('Izin ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
