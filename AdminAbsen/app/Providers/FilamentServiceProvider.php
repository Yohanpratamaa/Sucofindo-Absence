<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
