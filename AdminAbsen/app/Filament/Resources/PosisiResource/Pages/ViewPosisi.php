<?php

namespace App\Filament\Resources\PosisiResource\Pages;

use App\Filament\Resources\PosisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPosisi extends ViewRecord
{
    protected static string $resource = PosisiResource::class;

    public function getTitle(): string
    {
        return 'Detail Data Posisi';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\RestoreAction::make()
                ->label('Pulihkan')
                ->icon('heroicon-o-arrow-path')
                ->color('success'),
        ];
    }
}
