<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil latitude dari location[lat] atau latitude, default ke -6.200000
        $data['latitude'] = (float) ($data['location']['lat'] ?? ($data['latitude'] ?? -6.9431000));
        // Ambil longitude dari location[lng] atau longitude, default ke 106.816666
        $data['longitude'] = (float) ($data['location']['lng'] ?? ($data['longitude'] ?? 107.5851494));
        // Pastiin radius ada, default ke 100 kalau kosong
        $data['radius'] = (float) ($data['radius'] ?? 100);
        // Hapus field location biar nggak disimpen
        unset($data['location']);

        return $data;
    }
}
