<?php

namespace App\Filament\Resources\PosisiResource\Pages;

use App\Filament\Resources\PosisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosisis extends ListRecords
{
    protected static string $resource = PosisiResource::class;

    public function getTitle(): string
    {
        return 'Data Posisi';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Posisi')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
