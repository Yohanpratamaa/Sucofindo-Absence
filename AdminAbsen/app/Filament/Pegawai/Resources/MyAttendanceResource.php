<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyAttendanceResource\Pages;
use App\Filament\Components\AttendanceImageColumn;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Absensi Saya';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Data Absensi';

    public static function getNavigationGroup(): ?string
    {
        return 'Data Absensi';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // View only - no create/edit form for attendance
                Forms\Components\Placeholder::make('info')
                    ->content('Absensi hanya dapat dilakukan melalui aplikasi mobile.')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'WFO' => 'primary',
                        'Dinas Luar' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('Belum check out')
                    ->sortable(),

                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi Kerja')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\ImageColumn::make('picture_absen_masuk_url')
                    ->label('Foto Masuk')
                    ->height(60)
                    ->width(60)
                    ->circular()
                    ->toggleable()
                    ->tooltip('Klik untuk memperbesar')
                    ->extraAttributes(['style' => 'object-fit: cover;']),

                Tables\Columns\ImageColumn::make('picture_absen_pulang_url')
                    ->label('Foto Pulang')
                    ->height(60)
                    ->width(60)
                    ->circular()
                    ->placeholder('Belum check out')
                    ->toggleable()
                    ->tooltip('Klik untuk memperbesar')
                    ->extraAttributes(['style' => 'object-fit: cover;']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('attendance_type')
                    ->label('Tipe Absensi')
                    ->options([
                        'WFO' => 'Work From Office',
                        'Dinas Luar' => 'Dinas Luar',
                    ]),

                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Status Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Tidak Hadir' => 'Tidak Hadir',
                    ]),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)
                                                  ->whereYear('created_at', now()->year)),

                Tables\Filters\Filter::make('belum_checkout')
                    ->label('Belum Check Out')
                    ->query(fn ($query) => $query->whereNull('check_out')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAttendances::route('/'),
            'view' => Pages\ViewMyAttendance::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
