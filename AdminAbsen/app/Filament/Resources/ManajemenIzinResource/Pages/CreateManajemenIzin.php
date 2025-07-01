<?php

namespace App\Filament\Resources\ManajemenIzinResource\Pages;

use App\Filament\Resources\ManajemenIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateManajemenIzin extends CreateRecord
{
    protected static string $resource = ManajemenIzinResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Jenis Izin Berhasil Dibuat')
            ->body('Jenis izin baru telah berhasil ditambahkan ke sistem.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Transform syarat_pengajuan array to proper format
        if (isset($data['syarat_pengajuan']) && is_array($data['syarat_pengajuan'])) {
            $data['syarat_pengajuan'] = array_values(array_filter(
                array_map(function ($item) {
                    return $item['syarat'] ?? null;
                }, $data['syarat_pengajuan'])
            ));
        }

        return $data;
    }
}
