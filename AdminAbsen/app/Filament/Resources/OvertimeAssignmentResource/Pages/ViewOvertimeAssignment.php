<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOvertimeAssignment extends ViewRecord
{
    protected static string $resource = OvertimeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
