<?php

namespace App\Filament\Resources\PosisiResource\Pages;

use App\Filament\Resources\PosisiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPosisi extends EditRecord
{
    protected static string $resource = PosisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
