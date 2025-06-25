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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Office Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Kantor'),
                        Infolists\Components\TextEntry::make('latitude')
                            ->label('Latitude'),
                        Infolists\Components\TextEntry::make('longitude')
                            ->label('Longitude'),
                        Infolists\Components\TextEntry::make('radius')
                            ->label('Radius Absensi (meter)'),
                        MapEntry::make('location')
                            ->label('Peta Lokasi')
                            ->defaultLocation(latitude: -6.9431000, longitude: 107.5851494)
                            ->draggable(false)
                            ->zoom(15)
                            ->minZoom(5)
                            ->maxZoom(20)
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->showMarker(true)
                            ->markerColor('#3b82f6')
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)
                            ->state(fn ($record) => [
                                'lat' => $record->latitude,
                                'lng' => $record->longitude,
                            ])
                            ->extraStyles(['min-height: 50vh']),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Jadwal Operasional')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('schedules')
                            ->label('Jadwal per Hari')
                            ->schema([
                                Infolists\Components\TextEntry::make('day_of_week')
                                    ->label('Hari')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                Infolists\Components\TextEntry::make('start_time')
                                    ->label('Jam Masuk')
                                    ->formatStateUsing(fn ($state) => $state ?? 'Libur'),
                                Infolists\Components\TextEntry::make('end_time')
                                    ->label('Jam Pulang')
                                    ->formatStateUsing(fn ($state) => $state ?? 'Libur'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
