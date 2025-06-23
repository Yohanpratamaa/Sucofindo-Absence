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

                NavigationGroup::make('Master Data')
                    ->items([
                        NavigationItem::make('Pegawai')
                            ->icon('heroicon-o-users')
                            ->activeIcon('heroicon-s-users')
                            ->url(route('filament.admin.resources.pegawais.index'))
                            ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.pegawais.*')),
                    ]),

                NavigationGroup::make('Absensi')
                    ->items([
                        NavigationItem::make('Data Absensi')
                            ->icon('heroicon-o-clock')
                            ->activeIcon('heroicon-s-clock')
                            ->url('#'),

                        NavigationItem::make('Laporan Absensi')
                            ->icon('heroicon-o-document-text')
                            ->activeIcon('heroicon-s-document-text')
                            ->url('#'),
                    ]),
            ]);
        });
    }
}
