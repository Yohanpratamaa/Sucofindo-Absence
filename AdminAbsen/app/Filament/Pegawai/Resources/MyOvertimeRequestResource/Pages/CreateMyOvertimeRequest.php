<?php

namespace App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateMyOvertimeRequest extends CreateRecord
{
    protected static string $resource = MyOvertimeRequestResource::class;

    public function getTitle(): string
    {
        return 'Ajukan Lembur Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengajuan Lembur Berhasil')
            ->body('Pengajuan lembur telah dikirim dan menunggu persetujuan dari atasan.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan user_id dan assigned_by diisi dengan ID user yang sedang login
        $data['user_id'] = Auth::id();
        $data['assigned_by'] = Auth::id();
        $data['status'] = 'Assigned';

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Ajukan Lembur'), // Ubah label tombol submit
            $this->getCancelFormAction()
                ->label('Cancel'), // Tambah tombol cancel
        ];
    }
}
