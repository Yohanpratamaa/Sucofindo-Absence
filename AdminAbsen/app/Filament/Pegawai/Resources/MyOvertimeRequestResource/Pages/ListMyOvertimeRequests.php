<?php

namespace App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyOvertimeRequests extends ListRecords
{
    protected static string $resource = MyOvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Lembur')
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }

    public function getTitle(): string
    {
        return 'Pengajuan Lembur Saya';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget statistik di masa depan
        ];
    }
}
