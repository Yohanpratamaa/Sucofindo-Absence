<?php

namespace App\Filament\KepalaBidang\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    // Remove custom view - use default Filament dashboard view
    // protected static string $view = 'filament.kepala-bidang.pages.dashboard';

    public function getTitle(): string
    {
        return 'Dashboard Kepala Bidang';
    }

    public function getSubheading(): string
    {
        return 'Selamat datang di dashboard kepala bidang. Akses fitur export melalui menu "Export" di sidebar.';
    }

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            \App\Filament\KepalaBidang\Widgets\TeamAttendanceWidget::class,
            \App\Filament\KepalaBidang\Widgets\ApprovalStatsWidget::class,
            \App\Filament\KepalaBidang\Widgets\TeamPerformanceWidget::class,
        ];
    }
}
