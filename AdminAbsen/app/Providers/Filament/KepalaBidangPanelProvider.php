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

class KepalaBidangPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kepala-bidang')
            ->path('kepala-bidang')
            // ->login() // Disable built-in login, use unified login
            ->loginRouteSlug('disabled-login')
            ->brandName('Smart Absens - Kepala Bidang')
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
                'primary' => Color::Orange,
                'secondary' => Color::Gray,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/KepalaBidang/Resources'), for: 'App\\Filament\\KepalaBidang\\Resources')
            ->discoverPages(in: app_path('Filament/KepalaBidang/Pages'), for: 'App\\Filament\\KepalaBidang\\Pages')
            ->pages([
                \App\Filament\KepalaBidang\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/KepalaBidang/Widgets'), for: 'App\\Filament\\KepalaBidang\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\KepalaBidang\Widgets\TeamAttendanceWidget::class,
                \App\Filament\KepalaBidang\Widgets\ApprovalStatsWidget::class,
                \App\Filament\KepalaBidang\Widgets\TeamPerformanceWidget::class,
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
                \App\Http\Middleware\EnsureKepalaBidangRole::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout')
                    ->url('/logout')
                    ->icon('heroicon-m-arrow-left-on-rectangle'),
            ])
            ->navigationGroups([
                'Manajemen Tim',
                'Persetujuan',
                'Laporan',
                'Profil',
            ])
            ->maxContentWidth('full');
    }
}
