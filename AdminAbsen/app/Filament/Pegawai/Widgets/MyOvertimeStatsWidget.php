<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\OvertimeAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyOvertimeStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Total Pengajuan Lembur', OvertimeAssignment::where('user_id', $userId)->count())
                ->description('Total pengajuan lembur')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Menunggu Persetujuan', OvertimeAssignment::where('user_id', $userId)->where('status', 'Assigned')->count())
                ->description('Pengajuan pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Disetujui', OvertimeAssignment::where('user_id', $userId)->where('status', 'Accepted')->count())
                ->description('Lembur disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak', OvertimeAssignment::where('user_id', $userId)->where('status', 'Rejected')->count())
                ->description('Lembur ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
