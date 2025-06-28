<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\OvertimeAssignment;
use App\Models\Izin;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecentRequestsWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.recent-requests-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        $userId = Auth::id();

        return [
            'recentOvertimes' => OvertimeAssignment::where('user_id', $userId)
                ->with(['approvedBy'])
                ->latest()
                ->limit(5)
                ->get(),
            'recentIzins' => Izin::where('user_id', $userId)
                ->with(['approvedBy'])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
