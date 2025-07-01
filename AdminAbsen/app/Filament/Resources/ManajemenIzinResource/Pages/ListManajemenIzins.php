<?php

namespace App\Filament\Resources\ManajemenIzinResource\Pages;

use App\Filament\Resources\ManajemenIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListManajemenIzins extends ListRecords
{
    protected static string $resource = ManajemenIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jenis Izin')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Manajemen Jenis Izin';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ManajemenIzinResource\Widgets\ManajemenIzinStatsWidget::class,
        ];
    }
}
