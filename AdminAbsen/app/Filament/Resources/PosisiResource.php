<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PosisiResource\Pages;
use App\Filament\Resources\PosisiResource\RelationManagers;
use App\Models\Posisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PosisiResource extends Resource
{
    protected static ?string $model = Posisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Data Posisi';

    protected static ?string $modelLabel = 'Posisi';

    protected static ?string $pluralModelLabel = 'Data Posisi';

    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Posisi')
                    ->description('Isi informasi posisi/jabatan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Posisi')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Front End Developer, UI/UX Designer'),

                                Forms\Components\TextInput::make('tunjangan')
                                    ->label('Tunjangan')
                                    ->prefix('Rp')
                                    ->placeholder('1.500.000')
                                    ->default(0)
                                    ->formatStateUsing(function ($state) {
                                        return $state ? number_format($state, 0, ',', '.') : '';
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        // Remove dots and convert to integer
                                        return $state ? (int) str_replace('.', '', $state) : 0;
                                    })
                                    ->live(debounce: 300)
                                    ->extraInputAttributes([
                                        'oninput' => 'this.value = this.value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")',
                                        'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57'
                                    ]),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Deskripsi tugas dan tanggung jawab posisi'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Posisi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tunjangan')
                    ->label('Tunjangan')
                    ->formatStateUsing(function ($state) {
                        return 'Rp' . number_format($state, 0, ',', '.');
                    })
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),

                Tables\Columns\TextColumn::make('pegawai_count')
                    ->label('Digunakan')
                    ->getStateUsing(function ($record) {
                        $count = $record->pegawai_count ?? 0;
                        return $count . ' pegawai';
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama');
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
            'index' => Pages\ListPosisis::route('/'),
            'create' => Pages\CreatePosisi::route('/create'),
            'view' => Pages\ViewPosisi::route('/{record}'),
            'edit' => Pages\EditPosisi::route('/{record}/edit'),
        ];
    }
}
