<?php

namespace App\Filament\Pegawai\Resources\MyAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListMyAttendances extends ListRecords
{
    protected static string $resource = MyAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Riwayat Absensi Saya';
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
