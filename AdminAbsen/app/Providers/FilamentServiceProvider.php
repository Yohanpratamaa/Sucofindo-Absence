<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            // Custom navigation items
            Filament::navigation([
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->activeIcon('heroicon-s-home')
                    ->url(route('filament.admin.pages.dashboard'))
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
            ]);
        });
    }
}
