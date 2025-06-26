<?php

namespace App\Filament\Pegawai\Resources\MyIzinResource\Pages;

use App\Filament\Pegawai\Resources\MyIzinResource;
use Filament\Resources\Pages\ListRecords;

class ListMyIzins extends ListRecords
{
    protected static string $resource = MyIzinResource::class;

    public function getTitle(): string
    {
        return 'Riwayat Izin Saya';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
