<?php

namespace App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource;
use App\Models\OvertimeAssignment;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateOvertimeApproval extends CreateRecord
{
    protected static string $resource = OvertimeApprovalResource::class;

    public function getTitle(): string
    {
        return 'Assign Lembur Baru';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Karena kepala bidang yang assign lembur langsung,
        // maka statusnya langsung disetujui otomatis
        $data['assigned_by'] = Auth::id();
        $data['status'] = 'Accepted';
        $data['approved_by'] = Auth::id(); // Kepala bidang yang assign sekaligus approve
        $data['approved_at'] = now(); // Set waktu approval saat ini

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Lembur Berhasil Di-assign dan Disetujui')
            ->body('Penugasan lembur telah berhasil dibuat dan langsung disetujui karena Anda sebagai kepala bidang yang menugaskan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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
