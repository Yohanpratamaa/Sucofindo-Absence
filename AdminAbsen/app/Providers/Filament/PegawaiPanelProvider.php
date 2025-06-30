<?php

namespace App\Providers\Filament;

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

class PegawaiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pegawai')
            ->path('pegawai')
            // ->login() // Disable built-in login, use unified login
            ->loginRouteSlug('disabled-login')
            ->brandName('Smart Absens - Pegawai')
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
                'primary' => Color::Green,
                'secondary' => Color::Gray,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Pegawai/Resources'), for: 'App\\Filament\\Pegawai\\Resources')
            ->discoverPages(in: app_path('Filament/Pegawai/Pages'), for: 'App\\Filament\\Pegawai\\Pages')
            ->pages([
                \App\Filament\Pegawai\Pages\Dashboard::class,
                \App\Filament\Pegawai\Pages\WfoAttendance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Pegawai/Widgets'), for: 'App\\Filament\\Pegawai\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Pegawai\Widgets\WfoAttendanceStatusWidget::class,
                \App\Filament\Pegawai\Widgets\MyAttendanceWidget::class,
                \App\Filament\Pegawai\Widgets\MyOvertimeStatsWidget::class,
                \App\Filament\Pegawai\Widgets\MyIzinStatsWidget::class,
                \App\Filament\Pegawai\Widgets\RecentRequestsWidget::class,
                \App\Filament\Pegawai\Widgets\AttendanceStatsWidget::class,
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
                \App\Http\Middleware\FilamentUnifiedAuthenticate::class,
                \App\Http\Middleware\ClearFilamentSessionData::class,
                \App\Http\Middleware\EnsureFilamentUserIntegrity::class,
                \App\Http\Middleware\EnsurePegawaiRole::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout')
                    ->url('/logout')
                    ->icon('heroicon-m-arrow-left-on-rectangle'),
            ])
            ->navigationGroups([
                'Absensi',
                'Izin',
                'Lembur',
                'Profil',
            ])
            ->maxContentWidth('full');
    }
}
