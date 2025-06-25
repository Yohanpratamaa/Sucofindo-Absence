<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttendanceChart;
use App\Filament\Widgets\AttendanceStatsOverview;
use App\Filament\Widgets\AttendanceStatusChart;
use App\Filament\Widgets\AttendanceTypeChart;
use App\Filament\Widgets\MonthlyAttendanceChart;
use App\Filament\Widgets\RecentAttendanceTable;
// use App\Filament\Widgets\TopAttendanceTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            AttendanceStatsOverview::class,
            AttendanceChart::class,
            AttendanceStatusChart::class,
            AttendanceTypeChart::class,
            MonthlyAttendanceChart::class,
            RecentAttendanceTable::class,
            // TopAttendanceTable::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
