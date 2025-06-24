<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    public function getTitle(): string
    {
        return 'Tambah Data Pegawai';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data pegawai berhasil ditambahkan';
    }

    // Custom form actions (buttons)
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Selanjutnya')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }

    // Override create action behavior
    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Penyimpanan')
            ->modalDescription('Apakah Anda yakin ingin menyimpan data pegawai ini?')
            ->modalSubmitActionLabel('Ya, Simpan')
            ->action(function () {
                $this->create();
            });
    }

    // Add custom header actions
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\Action::make('import')
    //             ->label('Import Excel')
    //             ->icon('heroicon-o-document-arrow-up')
    //             ->color('info')
    //             ->action(function () {
    //                 // Logic untuk import
    //                 $this->notify('success', 'Fitur import akan segera tersedia');
    //             }),

    //         Actions\Action::make('template')
    //             ->label('Download Template')
    //             ->icon('heroicon-o-document-arrow-down')
    //             ->color('warning')
    //             ->url(route('download.template'))
    //             ->openUrlInNewTab(),
    //     ];
    // }
}
