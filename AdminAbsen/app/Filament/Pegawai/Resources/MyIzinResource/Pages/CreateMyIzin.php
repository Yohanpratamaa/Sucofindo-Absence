<?php

namespace App\Filament\Pegawai\Resources\MyIzinResource\Pages;

use App\Filament\Pegawai\Resources\MyIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateMyIzin extends CreateRecord
{
    protected static string $resource = MyIzinResource::class;

    public function getTitle(): string
    {
        return 'Ajukan Izin Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengajuan Izin Berhasil')
            ->body('Pengajuan izin telah dikirim dan menunggu persetujuan dari atasan.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan user_id diisi dengan ID user yang sedang login
        $data['user_id'] = Auth::id();
        
        return $data;
    }
}
