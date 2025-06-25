<?php

namespace App\Filament\Resources\PosisiResource\Pages;

use App\Filament\Resources\PosisiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePosisi extends CreateRecord
{
    protected static string $resource = PosisiResource::class;

    public function getTitle(): string
    {
        return 'Tambah Data Posisi';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan Data')
                ->icon('heroicon-o-check')
                ->color('success'),
            $this->getCancelFormAction()
                ->label('Batal')
                ->icon('heroicon-o-x-mark')
                ->color('gray'),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data Posisi Berhasil Ditambahkan')
            ->body('Data posisi baru telah berhasil disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
