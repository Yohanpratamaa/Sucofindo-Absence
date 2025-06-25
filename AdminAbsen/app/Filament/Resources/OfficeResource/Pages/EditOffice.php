<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOffice extends EditRecord
{
    protected static string $resource = OfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['latitude'] = (float) ($data['location']['lat'] ?? $data['latitude']);
        $data['longitude'] = (float) ($data['location']['lng'] ?? $data['longitude']);
        $data['radius'] = (float) $data['radius'];
        return $data;
    }
}
