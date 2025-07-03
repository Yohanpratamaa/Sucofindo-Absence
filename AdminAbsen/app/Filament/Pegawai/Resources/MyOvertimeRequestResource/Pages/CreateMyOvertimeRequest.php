<?php

namespace App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource;
use App\Models\OvertimeAssignment;
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

        // Auto-generate overtime ID dengan format: OT-YYYYMMDD-XXXX
        $date = now()->format('Ymd');
        $lastRecord = OvertimeAssignment::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRecord ? (int)substr($lastRecord->overtime_id, -4) + 1 : 1;
        $data['overtime_id'] = 'OT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

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
