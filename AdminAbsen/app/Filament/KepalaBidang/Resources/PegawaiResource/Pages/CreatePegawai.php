<?php

namespace App\Filament\KepalaBidang\Resources\PegawaiResource\Pages;

use App\Filament\KepalaBidang\Resources\PegawaiResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pegawai Berhasil Ditambahkan')
            ->body('Data pegawai baru telah berhasil disimpan.');
    }

    public function getTitle(): string
    {
        return 'Tambah Pegawai Baru';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan role_user adalah employee
        $data['role_user'] = 'employee';

        return $data;
    }
}
