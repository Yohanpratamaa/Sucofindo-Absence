<?php

namespace App\Filament\Pegawai\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    // Remove custom view - use default Filament dashboard view
    // protected static string $view = 'filament.pegawai.pages.dashboard';

    public function getTitle(): string
    {
        return 'Dashboard Pegawai';
    }

    public function getSubheading(): string
    {
        return 'Selamat datang di dashboard pegawai';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Pegawai\Widgets\MyAttendanceWidget::class,
            // \App\Filament\Pegawai\Widgets\MyIzinWidget::class,
            \App\Filament\Pegawai\Widgets\AttendanceStatsWidget::class,
        ];
    }
}
