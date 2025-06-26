<?php

namespace App\Filament\Pegawai\Resources\MyAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyAttendanceResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyAttendance extends ViewRecord
{
    protected static string $resource = MyAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Detail Absensi';
    }
}
