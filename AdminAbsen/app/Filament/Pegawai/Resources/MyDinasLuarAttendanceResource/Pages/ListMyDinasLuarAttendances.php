<?php

namespace App\Filament\Pegawai\Resources\MyDinasLuarAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyDinasLuarAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyDinasLuarAttendances extends ListRecords
{
    protected static string $resource = MyDinasLuarAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('absen_dinas_luar')
                ->label('Absensi Dinas Luar')
                ->icon('heroicon-o-map-pin')
                ->url(\App\Filament\Pegawai\Pages\DinaslLuarAttendance::getUrl())
                ->color('success'),
        ];
    }

    public function getTitle(): string
    {
        return 'Riwayat Absensi Dinas Luar';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Pegawai\Widgets\DinaslLuarAttendanceStatusWidget::class,
        ];
    }
}
