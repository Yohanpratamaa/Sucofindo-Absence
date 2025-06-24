<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JabatanResource\Pages;
use App\Filament\Resources\JabatanResource\RelationManagers;
use App\Models\Jabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Data Jabatan';

    protected static ?string $modelLabel = 'Jabatan';

    protected static ?string $pluralModelLabel = 'Data Jabatan';

    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Jabatan')
                    ->description('Isi informasi jabatan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Jabatan')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Manager IT, Senior Developer'),

                                Forms\Components\TextInput::make('tunjangan')
                                    ->label('Tunjangan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->default(0)
                                    ->step(1000),

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
                                    ->placeholder('Deskripsi tugas dan tanggung jawab jabatan'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Jabatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tunjangan')
                    ->label('Tunjangan')
                    ->money('IDR')
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
            'index' => Pages\ListJabatans::route('/'),
            'create' => Pages\CreateJabatan::route('/create'),
            'edit' => Pages\EditJabatan::route('/{record}/edit'),
        ];
    }
}
