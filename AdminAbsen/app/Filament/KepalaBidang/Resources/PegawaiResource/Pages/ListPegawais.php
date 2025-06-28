<?php

namespace App\Filament\KepalaBidang\Resources\PegawaiResource\Pages;

use App\Filament\KepalaBidang\Resources\PegawaiResource;
use App\Filament\KepalaBidang\Widgets\PegawaiStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPegawais extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pegawai Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PegawaiStatsOverview::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Manajemen Pegawai';
    }
}
