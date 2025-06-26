<?php

namespace App\Filament\KepalaBidang\Widgets;

use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PegawaiStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPegawai = Pegawai::where('role_user', 'employee')->count();
        $pegawaiAktif = Pegawai::where('role_user', 'employee')->where('status', 'active')->count();
        $pegawaiNonAktif = Pegawai::where('role_user', 'employee')->where('status', 'inactive')->count();
        $pegawaiMengundurkan = Pegawai::where('role_user', 'employee')->where('status', 'resigned')->count();

        // Pegawai baru bulan ini
        $pegawaiBaru = Pegawai::where('role_user', 'employee')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Pegawai', $totalPegawai)
                ->description('Semua pegawai di sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pegawai Aktif', $pegawaiAktif)
                ->description('Pegawai dengan status aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pegawai Non-Aktif', $pegawaiNonAktif)
                ->description('Pegawai dengan status non-aktif')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('warning'),

            Stat::make('Pegawai Baru', $pegawaiBaru)
                ->description('Pegawai baru bulan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }
}
