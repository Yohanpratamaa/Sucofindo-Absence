<?php

namespace App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMyOvertimeRequest extends EditRecord
{
    protected static string $resource = MyOvertimeRequestResource::class;

    public function getTitle(): string
    {
        return 'Ubah Pengajuan Lembur';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->label('Batalkan Pengajuan')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Pengajuan Lembur')
                ->modalDescription('Apakah Anda yakin ingin membatalkan pengajuan lembur ini?')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Pengajuan Dibatalkan')
                        ->body('Pengajuan lembur berhasil dibatalkan.')
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
            ->title('Pengajuan Lembur Diperbaharui')
            ->body('Perubahan pengajuan lembur berhasil disimpan.')
            ->duration(5000);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }
}
