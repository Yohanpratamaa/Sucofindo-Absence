<?php

namespace App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource;
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
        $data['status'] = 'Assigned';

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Lembur Berhasil Di-assign')
            ->body('Penugasan lembur telah berhasil dibuat dan ditugaskan kepada pegawai.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
