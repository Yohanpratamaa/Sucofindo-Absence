<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\EncryptCookies;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Smart Absens')
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/favicon.ico'))
            ->renderHook(
                'panels::head.end',
                fn (): string => '<style>
                    .fi-widget[data-widget="filament-widgets-filament-info-widget"],
                    .filament-widgets-filament-info-widget,
                    .fi-wi-info { display: none !important; }
                    a[href*="filamentphp.com"], a[href*="github.com/filamentphp"] { display: none !important; }
                    .fi-footer, .fi-simple-footer { display: none !important; }
                </style>'
            )
            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Gray,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\AttendanceStatsOverview::class,
                \App\Filament\Widgets\AttendanceChart::class,
                \App\Filament\Widgets\AttendanceStatusChart::class,
                \App\Filament\Widgets\AttendanceTypeChart::class,
                \App\Filament\Widgets\MonthlyAttendanceChart::class,
                \App\Filament\Widgets\RecentAttendanceTable::class,
                \App\Filament\Widgets\TopAttendanceTable::class,
                // Remove FilamentInfoWidget to hide default branding
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Master Data',
                'Absensi',
                'Laporan',
                'Pengaturan',
            ])
            ->maxContentWidth('full');
    }
}
