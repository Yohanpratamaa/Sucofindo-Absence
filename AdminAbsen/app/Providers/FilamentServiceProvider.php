<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Override Filament logout response to use our unified logout
        $this->app->bind(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request): RedirectResponse
                {
                    // Use our unified logout
                    return redirect('/logout');
                }
            };
        });
    }

    public function boot(): void
    {
        // Simple branding customization using CSS
        Filament::registerRenderHook(
            'panels::head.end',
            fn (): string => '<style>
                /* Hide default Filament branding and info widgets */
                .fi-widget[data-widget="filament-widgets-filament-info-widget"],
                .filament-widgets-filament-info-widget,
                .fi-wi-info,
                .fi-footer,
                .fi-simple-footer {
                    display: none !important;
                }
                
                /* Hide branding links */
                a[href*="filamentphp.com"], 
                a[href*="github.com/filamentphp"] {
                    display: none !important;
                }
            </style>'
        );
    }
}
