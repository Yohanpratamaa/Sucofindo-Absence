<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PegawaiResource;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 0; // Show at the top

    public static function canView(): bool
    {
        // Only show for super admin
        return Auth::user()?->role_user === 'super admin';
    }

    protected function getViewData(): array
    {
        return [
            'createUrl' => PegawaiResource::getUrl('create'),
            'indexUrl' => PegawaiResource::getUrl('index'),
        ];
    }
}
