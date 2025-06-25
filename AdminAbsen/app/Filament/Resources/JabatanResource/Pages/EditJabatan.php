<?php

namespace App\Filament\Resources\JabatanResource\Pages;

use App\Filament\Resources\JabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditJabatan extends EditRecord
{
    protected static string $resource = JabatanResource::class;

    public function getTitle(): string
    {
        return 'Edit Data Jabatan';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\RestoreAction::make()
                ->label('Pulihkan')
                ->icon('heroicon-o-arrow-path')
                ->color('success'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->icon('heroicon-o-check')
                ->color('success'),
            $this->getCancelFormAction()
                ->label('Batal')
                ->icon('heroicon-o-x-mark')
                ->color('gray'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Data Jabatan Berhasil Diperbarui')
            ->body('Perubahan data jabatan telah berhasil disimpan.');
    }
}
