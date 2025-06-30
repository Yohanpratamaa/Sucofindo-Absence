<?php

namespace App\Filament\Pegawai\Resources\MyIzinResource\Pages;

use App\Filament\Pegawai\Resources\MyIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMyIzin extends EditRecord
{
    protected static string $resource = MyIzinResource::class;

    public function getTitle(): string
    {
        return 'Ubah Pengajuan Izin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->label('Batalkan Pengajuan')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Pengajuan Izin')
                ->modalDescription('Apakah Anda yakin ingin membatalkan pengajuan izin ini?')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Pengajuan Dibatalkan')
                        ->body('Pengajuan izin berhasil dibatalkan.')
                ),
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
            ->title('Pengajuan Izin Diperbaharui')
            ->body('Perubahan pengajuan izin berhasil disimpan.')
            ->duration(5000);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }
}
