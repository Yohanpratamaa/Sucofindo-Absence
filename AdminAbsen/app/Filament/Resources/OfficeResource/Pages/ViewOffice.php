<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Actions; // Tambah ini
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOffice extends ViewRecord
{
    protected static string $resource = OfficeResource::class;
    
    protected static string $view = 'filament.resources.office-resource.pages.view-office';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Kantor: ' . $this->getRecord()->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Office Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nama Kantor')
                                    ->weight('semibold')
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('radius')
                                    ->label('Radius Absensi')
                                    ->suffix(' meter')
                                    ->badge()
                                    ->color('success'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->copyable()
                                    ->copyMessage('Latitude disalin!')
                                    ->icon('heroicon-m-map-pin'),
                                Infolists\Components\TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->copyable()
                                    ->copyMessage('Longitude disalin!')
                                    ->icon('heroicon-m-map-pin'),
                            ]),
                        MapEntry::make('location')
                            ->label('Peta Lokasi')
                            ->columnSpanFull()
                            ->defaultLocation(latitude: -6.9431000, longitude: 107.5851494)
                            ->draggable(false)
                            ->zoom(15)
                            ->minZoom(5)
                            ->maxZoom(18)
                            ->tilesUrl('https://tile.openstreetmap.org/{z}/{x}/{y}.png')
                            ->showMarker(true)
                            ->markerColor('#3b82f6')
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)
                            ->state(fn ($record) => [
                                'lat' => $record->latitude,
                                'lng' => $record->longitude,
                            ])
                            ->extraStyles([
                                'min-height: 400px',
                                'height: 400px',
                                'width: 100%',
                                'position: relative',
                                'z-index: 10',
                                'border-radius: 12px',
                                'overflow: hidden',
                                'box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                            ])
                            ->extraAttributes([
                                'class' => 'responsive-map-container',
                            ]),
                    ]),
                Infolists\Components\Section::make('Jadwal Operasional')
                    ->description('Jadwal kerja kantor per hari dalam seminggu')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('schedules')
                            ->label('Jadwal per Hari')
                            ->schema([
                                Infolists\Components\TextEntry::make('day_of_week')
                                    ->label('Hari')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'monday' => 'primary',
                                        'tuesday' => 'success', 
                                        'wednesday' => 'warning',
                                        'thursday' => 'info',
                                        'friday' => 'danger',
                                        'saturday' => 'gray',
                                        'sunday' => 'gray',
                                        default => 'gray'
                                    })
                                    ->formatStateUsing(fn ($state) => match($state) {
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu', 
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu',
                                        default => ucfirst($state)
                                    }),
                                Infolists\Components\TextEntry::make('start_time')
                                    ->label('Jam Masuk')
                                    ->icon('heroicon-m-clock')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : 'Libur'),
                                Infolists\Components\TextEntry::make('end_time')
                                    ->label('Jam Pulang')
                                    ->icon('heroicon-m-clock')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : 'Libur'),
                            ])
                            ->columns([
                                'default' => 3,
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 3,
                            ])
                            ->grid([
                                'default' => 1,
                                'sm' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),
                    ]),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'responsive-office-view',
        ];
    }
}
