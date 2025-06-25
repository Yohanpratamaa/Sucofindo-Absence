<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['latitude'] = -6.9431000;
        $data['longitude'] = 107.5851494;
        $data['radius'] = (float) ($data['radius'] ?? 100);
        unset($data['location']);
        return $data;
    }
}
