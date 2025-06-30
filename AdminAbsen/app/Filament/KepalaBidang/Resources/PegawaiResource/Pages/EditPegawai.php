<?php

namespace App\Filament\KepalaBidang\Resources\PegawaiResource\Pages;

use App\Filament\KepalaBidang\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat Detail'),
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->visible(false), // Kepala Bidang tidak bisa menghapus pegawai
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data Pegawai Berhasil Diperbarui')
            ->body('Perubahan data pegawai telah berhasil disimpan.');
    }

    public function getTitle(): string
    {
        return 'Edit Data Pegawai';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Pastikan role_user tetap employee
        $data['role_user'] = 'employee';

        return $data;
    }
}
