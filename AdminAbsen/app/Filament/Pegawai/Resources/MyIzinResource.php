<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyIzinResource\Pages;
use App\Models\Izin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyIzinResource extends Resource
{
    protected static ?string $model = Izin::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Izin Saya';

    protected static ?string $modelLabel = 'Izin';

    protected static ?string $pluralModelLabel = 'Data Izin';

    protected static ?string $navigationGroup = 'Izin';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'izin' => 'Izin',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan/Alasan')
                    ->required()
                    ->rows(3),

                Forms\Components\FileUpload::make('dokumen_pendukung')
                    ->label('Dokumen Pendukung')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(2048),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sakit' => 'danger',
                        'cuti' => 'success',
                        'izin' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50),

                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        is_null($record->approved_by) => 'warning',
                        !is_null($record->approved_at) => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($record): string => match (true) {
                        is_null($record->approved_by) => 'Menunggu',
                        !is_null($record->approved_at) => 'Disetujui',
                        default => 'Ditolak',
                    }),

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Diproses Oleh')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'izin' => 'Izin',
                    ]),

                Tables\Filters\Filter::make('status_pending')
                    ->label('Menunggu Persetujuan')
                    ->query(fn (Builder $query): Builder => $query->whereNull('approved_by')),

                Tables\Filters\Filter::make('status_approved')
                    ->label('Disetujui')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('approved_by')->whereNotNull('approved_at')),

                Tables\Filters\Filter::make('status_rejected')
                    ->label('Ditolak')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('approved_by')->whereNull('approved_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => is_null($record->approved_by)), // Only editable if not processed
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyIzins::route('/'),
            // 'create' => Pages\CreateMyIzin::route('/create'),
            // 'view' => Pages\ViewMyIzin::route('/{record}'),
            // 'edit' => Pages\EditMyIzin::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return is_null($record->approved_by);
    }

    public static function canDelete($record): bool
    {
        return is_null($record->approved_by);
    }
}
