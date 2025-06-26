<?php

namespace App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeApprovals extends ListRecords
{
    protected static string $resource = OvertimeApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Assign Lembur Baru')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['assigned_by'] = auth()->id();
                    $data['status'] = 'Assigned';
                    return $data;
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Pengajuan Lembur';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget statistik lembur di sini
        ];
    }
}
