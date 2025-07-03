<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyAllAttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class MyAllAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Riwayat Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Riwayat Absensi';

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
                Forms\Components\Placeholder::make('info')
                    ->content('Absensi hanya dapat dilakukan melalui halaman absensi atau aplikasi mobile.')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tanggal Column
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable(),

                // === CHECK-IN GROUP ===
                Tables\Columns\ColumnGroup::make('Check-In', [
                    // Check-In - Jam Masuk
                    Tables\Columns\TextColumn::make('check_in')
                        ->label('Jam Masuk')
                        ->time('H:i')
                        ->weight(FontWeight::Medium)
                        ->placeholder('-')
                        ->color('success'),

                    // Check-In - Foto & Jam
                    Tables\Columns\ImageColumn::make('picture_absen_masuk_url')
                        ->label('Foto & Jam')
                        ->height(40)
                        ->width(40)
                        ->circular()
                        ->defaultImageUrl('/images/no-photo.png'),

                    // Check-In - Lokasi
                    Tables\Columns\IconColumn::make('location_checkin')
                        ->label('Lokasi')
                        ->getStateUsing(fn ($record) => !is_null($record->latitude_absen_masuk))
                        ->boolean()
                        ->icon(fn ($state) => $state ? 'heroicon-s-map-pin' : 'heroicon-s-x-mark')
                        ->color(fn ($state) => $state ? 'success' : 'gray'),
                ]),

                // === CHECK-IN KE-2 GROUP (Hanya untuk Dinas Luar) ===
                Tables\Columns\ColumnGroup::make('Check-In Ke-2', [
                    // Check-In Ke-2 - Jam
                    Tables\Columns\TextColumn::make('absen_siang')
                        ->label('Jam')
                        ->time('H:i')
                        ->weight(FontWeight::Medium)
                        ->placeholder('-')
                        ->color('warning'),

                    // Check-In Ke-2 - Foto & Jam
                    Tables\Columns\ImageColumn::make('picture_absen_siang_url')
                        ->label('Foto & Jam')
                        ->height(40)
                        ->width(40)
                        ->circular()
                        ->defaultImageUrl('/images/no-photo.png'),

                    // Check-In Ke-2 - Lokasi & Jarak
                    Tables\Columns\IconColumn::make('location_siang')
                        ->label('Lokasi & Jarak')
                        ->getStateUsing(fn ($record) => !is_null($record->latitude_absen_siang))
                        ->boolean()
                        ->icon(fn ($state) => $state ? 'heroicon-s-map-pin' : 'heroicon-s-x-mark')
                        ->color(fn ($state) => $state ? 'warning' : 'gray'),
                ])->visibleFrom('md'), // Hide on mobile for better responsive

                // === CHECK-OUT GROUP ===
                Tables\Columns\ColumnGroup::make('Check-Out', [
                    // Check-Out - Jam Pulang
                    Tables\Columns\TextColumn::make('check_out')
                        ->label('Jam Pulang')
                        ->time('H:i')
                        ->weight(FontWeight::Medium)
                        ->placeholder('-')
                        ->color('danger'),

                    // Check-Out - Foto & Jam
                    Tables\Columns\ImageColumn::make('picture_absen_pulang_url')
                        ->label('Foto & Jam')
                        ->height(40)
                        ->width(40)
                        ->circular()
                        ->defaultImageUrl('/images/no-photo.png'),

                    // Check-Out - Lokasi & Jarak
                    Tables\Columns\IconColumn::make('location_checkout')
                        ->label('Lokasi & Jarak')
                        ->getStateUsing(fn ($record) => !is_null($record->latitude_absen_pulang))
                        ->boolean()
                        ->icon(fn ($state) => $state ? 'heroicon-s-map-pin' : 'heroicon-s-x-mark')
                        ->color(fn ($state) => $state ? 'danger' : 'gray'),
                ]),

                // === STATUS GROUP ===
                // Tipe Absensi
                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'WFO' => 'success',
                        'Dinas Luar' => 'info',
                        default => 'gray',
                    }),

                // Durasi Kerja
                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi')
                    ->badge()
                    ->color('primary'),

                // Status Kehadiran
                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        'Tidak Absensi' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('daftar_presensi')
                    ->label('Daftar Presensi')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn () => route('filament.pegawai.pages.attendance-page'))
                    ->tooltip('Halaman Absensi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('attendance_type')
                    ->label('Tipe Absensi')
                    ->options([
                        'WFO' => 'Work From Office',
                        'Dinas Luar' => 'Dinas Luar',
                    ])
                    ->placeholder('Semua Tipe'),

                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Status Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Tidak Hadir' => 'Tidak Hadir',
                        'Tidak Absensi' => 'Tidak Absensi',
                    ])
                    ->placeholder('Semua Status'),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)
                                                  ->whereYear('created_at', now()->year))
                    ->indicator('Bulan Ini'),

                Tables\Filters\Filter::make('minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn ($query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]))
                    ->indicator('Minggu Ini'),

                Tables\Filters\Filter::make('belum_lengkap')
                    ->label('Belum Lengkap')
                    ->query(function ($query) {
                        return $query->where(function ($q) {
                            $q->whereNull('check_out')
                              ->orWhere(function ($subQ) {
                                  $subQ->where('attendance_type', 'Dinas Luar')
                                       ->whereNull('absen_siang');
                              });
                        });
                    })
                    ->indicator('Belum Lengkap'),

                Tables\Filters\Filter::make('tidak_absensi')
                    ->label('Tidak Absensi')
                    ->query(function ($query) {
                        return $query->where(function ($q) {
                            $q->whereNull('check_in')
                              ->orWhereTime('check_in', '>=', '17:00:00');
                        });
                    })
                    ->indicator('Tidak Absensi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Absensi')
                    ->modalWidth('5xl'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s') // Auto refresh setiap 30 detik
            ->recordClasses(fn ($record) =>
                $record->status_kehadiran === 'Tidak Absensi'
                    ? 'bg-red-50 border-l-4 border-red-500'
                    : null
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAllAttendances::route('/'),
            'view' => Pages\ViewMyAllAttendance::route('/{record}'),
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

    public static function getNavigationBadge(): ?string
    {
        $totalAttendance = static::getEloquentQuery()->count();
        $todayAttendance = static::getEloquentQuery()
            ->whereDate('created_at', today())
            ->count();

        return $todayAttendance > 0 ? "Hari ini: {$todayAttendance}" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $todayAttendance = static::getEloquentQuery()
            ->whereDate('created_at', today())
            ->count();

        return $todayAttendance > 0 ? 'success' : 'gray';
    }
}
