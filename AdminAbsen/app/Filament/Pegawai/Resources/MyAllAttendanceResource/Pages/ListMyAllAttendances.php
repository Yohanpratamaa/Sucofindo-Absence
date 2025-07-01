<?php

namespace App\Filament\Pegawai\Resources\MyAllAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyAllAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMyAllAttendances extends ListRecords
{
    protected static string $resource = MyAllAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Daftar Presensi';
    }

    public function getHeading(): string
    {
        return 'Daftar Presensi';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('buat_absensi')
                ->label('Absensi Sekarang')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->url(fn () => route('filament.pegawai.pages.attendance-page'))
                ->tooltip('Halaman Absensi'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge($this->getTabBadgeCount())
                ->icon('heroicon-m-calendar-days'),

            'hari_ini' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge($this->getTabBadgeCount('hari_ini'))
                ->badgeColor('success')
                ->icon('heroicon-m-calendar'),

            'bulan_ini' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('created_at', now()->month)
                                                                ->whereYear('created_at', now()->year))
                ->badge($this->getTabBadgeCount('bulan_ini'))
                ->badgeColor('primary')
                ->icon('heroicon-m-calendar-days'),

            'minggu_ini' => Tab::make('Minggu Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge($this->getTabBadgeCount('minggu_ini'))
                ->badgeColor('info')
                ->icon('heroicon-m-calendar'),

            'wfo' => Tab::make('WFO')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('attendance_type', 'WFO'))
                ->badge($this->getTabBadgeCount('wfo'))
                ->badgeColor('primary')
                ->icon('heroicon-m-building-office'),

            'dinas_luar' => Tab::make('Dinas Luar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('attendance_type', 'Dinas Luar'))
                ->badge($this->getTabBadgeCount('dinas_luar'))
                ->badgeColor('warning')
                ->icon('heroicon-m-map-pin'),

            'belum_lengkap' => Tab::make('Belum Lengkap')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(function ($q) {
                    $q->whereNull('check_out')
                      ->orWhere(function ($subQ) {
                          $subQ->where('attendance_type', 'Dinas Luar')
                               ->whereNull('absen_siang');
                      });
                }))
                ->badge($this->getTabBadgeCount('belum_lengkap'))
                ->badgeColor('warning')
                ->icon('heroicon-m-exclamation-triangle'),
        ];
    }

    protected function getTabBadgeCount(string $tab = 'all'): int
    {
        $query = $this->getResource()::getEloquentQuery();

        return match ($tab) {
            'hari_ini' => $query->whereDate('created_at', today())->count(),
            'bulan_ini' => $query->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
            'minggu_ini' => $query->whereBetween('created_at', [
                                     now()->startOfWeek(),
                                     now()->endOfWeek()
                                 ])
                                 ->count(),
            'wfo' => $query->where('attendance_type', 'WFO')->count(),
            'dinas_luar' => $query->where('attendance_type', 'Dinas Luar')->count(),
            'belum_lengkap' => $query->where(function ($q) {
                                         $q->whereNull('check_out')
                                           ->orWhere(function ($subQ) {
                                               $subQ->where('attendance_type', 'Dinas Luar')
                                                    ->whereNull('absen_siang');
                                           });
                                     })
                                     ->count(),
            default => $query->count(),
        };
    }
}
