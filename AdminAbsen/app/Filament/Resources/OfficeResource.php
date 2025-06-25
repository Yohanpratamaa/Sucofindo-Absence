<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Office Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama Kantor'),
                        Map::make('location')
                            ->label('Peta Lokasi')
                            ->columnSpanFull()
                            ->defaultLocation(latitude: -6.9431000, longitude: 107.5851494)
                            ->draggable(false)
                            ->clickable(false)
                            ->zoom(15)
                            ->minZoom(5)
                            ->maxZoom(20)
                            ->tilesUrl('https://tile.openstreetmap.org/{z}/{x}/{y}.png')
                            ->showMarker(true)
                            ->markerColor('#3b82f6')
                            ->rangeSelectField('radius')
                            ->disabled(true)
                            ->showFullscreenControl(true)
                            ->showZoomControl(true),
                        Forms\Components\TextInput::make('radius')
                            ->required()
                            ->numeric()
                            ->label('Radius Absensi (meter)')
                            ->minValue(10)
                            ->maxValue(1000)
                            ->default(100),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Jadwal Operasional')
                    ->schema([
                        Forms\Components\Repeater::make('schedules')
                            ->relationship('schedules')
                            ->schema([
                                Forms\Components\Select::make('day_of_week')
                                    ->required()
                                    ->options([
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu',
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu',
                                    ])
                                    ->label('Hari'),
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Jam Masuk')
                                    ->nullable(),
                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Jam Pulang')
                                    ->nullable(),
                            ])
                            ->columns(3)
                            ->label('Jadwal per Hari')
                            ->addActionLabel('Tambah Jadwal')
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action->label('Hapus Jadwal')
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kantor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude'),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude'),
                Tables\Columns\TextColumn::make('radius')
                    ->label('Radius (m)'),
                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('Jadwal')
                    ->counts('schedules')
                    ->formatStateUsing(fn ($state) => "$state hari"),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
            'view' => Pages\ViewOffice::route('/{record}'),
        ];
    }
}
