<?php

namespace App\Filament\Resources\PosisiResource\Pages;

use App\Filament\Resources\PosisiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPosisi extends EditRecord
{
    protected static string $resource = PosisiResource::class;

    public function getTitle(): string
    {
        return 'Edit Data Posisi';
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
            ->title('Data Posisi Berhasil Diperbarui')
            ->body('Perubahan data posisi telah berhasil disimpan.');
    }
}
