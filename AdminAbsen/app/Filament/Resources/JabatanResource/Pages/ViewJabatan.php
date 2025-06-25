<?php

namespace App\Filament\Resources\JabatanResource\Pages;

use App\Filament\Resources\JabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJabatan extends ViewRecord
{
    protected static string $resource = JabatanResource::class;

    public function getTitle(): string
    {
        return 'Detail Data Jabatan';
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
