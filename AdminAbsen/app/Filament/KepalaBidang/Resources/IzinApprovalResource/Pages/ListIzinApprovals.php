<?php

namespace App\Filament\KepalaBidang\Resources\IzinApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\IzinApprovalResource;
use Filament\Resources\Pages\ListRecords;

class ListIzinApprovals extends ListRecords
{
    protected static string $resource = IzinApprovalResource::class;

    public function getTitle(): string
    {
        return 'Persetujuan Izin Tim';
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
