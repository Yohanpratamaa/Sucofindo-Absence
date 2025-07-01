<?php

namespace App\Filament\Pegawai\Resources\MyDinasLuarResource\Pages;

use App\Filament\Pegawai\Resources\MyDinasLuarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMyDinasLuars extends ListRecords
{
    protected static string $resource = MyDinasLuarResource::class;

    public function getTitle(): string
    {
        return 'Riwayat Dinas Luar Saya';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada action create karena dinas luar dibuat melalui mobile app
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge($this->getTabBadgeCount()),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('created_at', now()->month)
                                                                ->whereYear('created_at', now()->year))
                ->badge($this->getTabBadgeCount('bulan_ini')),

            'minggu_ini' => Tab::make('Minggu Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge($this->getTabBadgeCount('minggu_ini')),

            'belum_lengkap' => Tab::make('Belum Lengkap')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function ($q) {
                    $q->whereNull('check_in')
                      ->orWhereNull('absen_siang')
                      ->orWhereNull('check_out');
                }))
                ->badge($this->getTabBadgeCount('belum_lengkap'))
                ->badgeColor('warning'),
        ];
    }

    protected function getTabBadgeCount(string $tab = 'all'): int
    {
        $query = $this->getResource()::getEloquentQuery();

        return match ($tab) {
            'bulan_ini' => $query->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
            'minggu_ini' => $query->whereBetween('created_at', [
                                     now()->startOfWeek(),
                                     now()->endOfWeek()
                                 ])
                                 ->count(),
            'belum_lengkap' => $query->where(function ($q) {
                                         $q->whereNull('check_in')
                                           ->orWhereNull('absen_siang')
                                           ->orWhereNull('check_out');
                                     })
                                     ->count(),
            default => $query->count(),
        };
    }
}
