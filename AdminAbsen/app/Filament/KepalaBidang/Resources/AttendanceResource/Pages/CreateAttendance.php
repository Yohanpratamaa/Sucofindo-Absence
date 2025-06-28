<?php

namespace App\Filament\KepalaBidang\Resources\AttendanceResource\Pages;

use App\Filament\KepalaBidang\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    public function getTitle(): string
    {
        return 'Tambah Data Absensi';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
