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
        $data['assigned_by'] = Auth::id();

        // Auto-generate overtime ID dengan format: OT-YYYYMMDD-XXXX
        $date = now()->format('Ymd');
        $lastRecord = OvertimeAssignment::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRecord ? (int)substr($lastRecord->overtime_id, -4) + 1 : 1;
        $data['overtime_id'] = 'OT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Karena kepala bidang yang assign lembur langsung,
        // maka statusnya langsung disetujui otomatis
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
}
