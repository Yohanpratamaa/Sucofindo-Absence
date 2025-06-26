<?php

namespace App\Filament\KepalaBidang\Pages;

use Filament\Pages\Page;

class AttendanceAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.kepala-bidang.pages.attendance-analytics';

    protected static ?string $navigationLabel = 'Analisis Absensi';

    protected static ?string $navigationGroup = 'Laporan & Export';

    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Analisis Absensi Tim';
    }

    public function getSubheading(): string
    {
        return 'Dashboard analisis dan insights absensi tim';
    }
}
