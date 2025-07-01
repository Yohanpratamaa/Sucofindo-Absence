<?php

namespace App\Filament\Resources\ManajemenIzinResource\Pages;

use App\Filament\Resources\ManajemenIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditManajemenIzin extends EditRecord
{
    protected static string $resource = ManajemenIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
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
            ->title('Jenis Izin Berhasil Diperbarui')
            ->body('Data jenis izin telah berhasil diperbarui.');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Transform syarat_pengajuan from array to repeater format
        if (isset($data['syarat_pengajuan']) && is_array($data['syarat_pengajuan'])) {
            $data['syarat_pengajuan'] = array_map(function ($syarat) {
                return ['syarat' => $syarat];
            }, $data['syarat_pengajuan']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
