<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Carbon\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Data Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Data Absensi';

    protected static ?string $navigationGroup = 'Manajemen Data';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Kepala Bidang hanya bisa melihat absensi pegawai (role employee)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        return parent::getEloquentQuery()->whereIn('user_id', $teamMembers);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Absensi Karyawan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Karyawan')
                                    ->relationship('user', 'nama', fn (Builder $query) => $query->where('role_user', 'employee'))
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\Select::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->options([
                                        'WFO' => 'Work From Office',
                                        'Dinas Luar' => 'Dinas Luar',
                                    ])
                                    ->disabled(),

                                Forms\Components\TextInput::make('office_working_hours_id')
                                    ->label('ID Jam Kerja')
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Waktu Absensi')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('check_in')
                                    ->label('Check In')
                                    ->disabled(),

                                Forms\Components\TimePicker::make('absen_siang')
                                    ->label('Absen Siang')
                                    ->disabled(),

                                Forms\Components\TimePicker::make('check_out')
                                    ->label('Check Out')
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Lokasi Check In')
                    ->description('Untuk WFO: Lokasi harus di area kantor. Untuk Dinas Luar: Lokasi fleksibel sesuai penugasan.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_masuk')
                                    ->label('Latitude Check In')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '🏢 Harus di area kantor'
                                            : '📍 Lokasi fleksibel'
                                    ),

                                Forms\Components\TextInput::make('longitude_absen_masuk')
                                    ->label('Longitude Check In')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '🏢 Harus di area kantor'
                                            : '📍 Lokasi fleksibel'
                                    ),

                                Forms\Components\FileUpload::make('picture_absen_masuk')
                                    ->label('Foto Check In')
                                    ->image()
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Lokasi Absen Siang')
                    ->description('Untuk WFO: Tidak diperlukan absen siang. Untuk Dinas Luar: WAJIB absen siang dengan lokasi dan foto.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_siang')
                                    ->label('Latitude Absen Siang')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '❌ Tidak diperlukan untuk WFO'
                                            : '✅ Wajib untuk Dinas Luar'
                                    ),

                                Forms\Components\TextInput::make('longitude_absen_siang')
                                    ->label('Longitude Absen Siang')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '❌ Tidak diperlukan untuk WFO'
                                            : '✅ Wajib untuk Dinas Luar'
                                    ),

                                Forms\Components\FileUpload::make('picture_absen_siang')
                                    ->label('Foto Absen Siang')
                                    ->image()
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '❌ Tidak diperlukan untuk WFO'
                                            : '✅ Wajib untuk Dinas Luar'
                                    ),
                            ]),
                    ])
                    ->collapsed(fn (?Attendance $record): bool =>
                        $record && $record->attendance_type === 'WFO'
                    )
                    ->collapsible(),

                Forms\Components\Section::make('Lokasi Check Out')
                    ->description('Untuk WFO: Lokasi harus di area kantor. Untuk Dinas Luar: Lokasi fleksibel sesuai penugasan.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_pulang')
                                    ->label('Latitude Check Out')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '🏢 Harus di area kantor'
                                            : '📍 Lokasi fleksibel'
                                    ),

                                Forms\Components\TextInput::make('longitude_absen_pulang')
                                    ->label('Longitude Check Out')
                                    ->disabled()
                                    ->helperText(fn (?Attendance $record): string =>
                                        $record && $record->attendance_type === 'WFO'
                                            ? '🏢 Harus di area kantor'
                                            : '📍 Lokasi fleksibel'
                                    ),

                                Forms\Components\FileUpload::make('picture_absen_pulang')
                                    ->label('Foto Check Out')
                                    ->image()
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Informasi Jadwal & Status')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->content(fn (?Attendance $record): string => $record?->attendance_type ?? '-'),

                                Forms\Components\Placeholder::make('absensi_requirement')
                                    ->label('Requirement Absensi')
                                    ->content(fn (?Attendance $record): string => $record?->absensi_requirement ?? '-')
                                    ->columnSpan(2),

                                Forms\Components\Placeholder::make('jam_masuk_standar')
                                    ->label('Jam Masuk Standar')
                                    ->content(fn (?Attendance $record): string => $record?->jam_masuk_standar ?? '-'),

                                Forms\Components\Placeholder::make('jam_keluar_standar')
                                    ->label('Jam Keluar Standar')
                                    ->content(fn (?Attendance $record): string => $record?->jam_keluar_standar ?? '-'),

                                Forms\Components\Placeholder::make('status_kehadiran')
                                    ->label('Status Kehadiran')
                                    ->content(fn (?Attendance $record): string => $record?->status_kehadiran ?? '-'),

                                Forms\Components\Placeholder::make('keterlambatan_detail')
                                    ->label('Detail Keterlambatan')
                                    ->content(fn (?Attendance $record): string => $record?->keterlambatan_detail ?? '-')
                                    ->columnSpan(2),

                                Forms\Components\Placeholder::make('kelengkapan_absensi')
                                    ->label('Kelengkapan Absensi')
                                    ->content(function (?Attendance $record): string {
                                        if (!$record) return '-';
                                        $kelengkapan = $record->kelengkapan_absensi;
                                        return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
                                    }),

                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Tanggal Absensi')
                                    ->content(fn (?Attendance $record): string => $record?->created_at?->format('d M Y') ?? '-'),
                            ]),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('overtime')
                                    ->label('Lembur (menit)')
                                    ->numeric()
                                    ->suffix(' menit')
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Nama Pegawai Column (tambahan untuk kepala bidang)
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable()
                    ->size('sm')
                    ->color('gray'),

                // Tanggal Column
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable()
                    ->weight(FontWeight::Medium)
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
                        ->label('Foto')
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
                        ->label('Foto')
                        ->height(40)
                        ->width(40)
                        ->circular()
                        ->defaultImageUrl('/images/no-photo.png'),

                    // Check-In Ke-2 - Lokasi & Jarak
                    Tables\Columns\IconColumn::make('location_siang')
                        ->label('Lokasi')
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
                        ->label('Foto')
                        ->height(40)
                        ->width(40)
                        ->circular()
                        ->defaultImageUrl('/images/no-photo.png'),

                    // Check-Out - Lokasi & Jarak
                    Tables\Columns\IconColumn::make('location_checkout')
                        ->label('Lokasi')
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

                // Toggleable columns untuk detail tambahan
                Tables\Columns\TextColumn::make('keterlambatan_detail')
                    ->label('Detail Keterlambatan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('overtime_formatted')
                    ->label('Lembur')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('kelengkapan_status')
                    ->label('Kelengkapan')
                    ->getStateUsing(function (?Attendance $record): string {
                        if (!$record) return '-';
                        $kelengkapan = $record->kelengkapan_absensi;
                        return "{$kelengkapan['completed']}/{$kelengkapan['total']}";
                    })
                    ->colors([
                        'success' => fn (?Attendance $record): bool =>
                            $record && $record->kelengkapan_absensi['status'] === 'Lengkap',
                        'warning' => fn (?Attendance $record): bool =>
                            $record && $record->kelengkapan_absensi['status'] === 'Belum Lengkap',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'nama', fn (Builder $query) => $query->where('role_user', 'employee'))
                    ->searchable()
                    ->preload(),

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
                    ])
                    ->attribute('status_kehadiran'),

                Tables\Filters\Filter::make('kelengkapan_absensi')
                    ->label('Kelengkapan Absensi')
                    ->form([
                        Forms\Components\Select::make('kelengkapan')
                            ->label('Status Kelengkapan')
                            ->options([
                                'lengkap' => 'Lengkap',
                                'tidak_lengkap' => 'Tidak Lengkap',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['kelengkapan'])) {
                            return $query;
                        }

                        if ($data['kelengkapan'] === 'lengkap') {
                            return $query->where(function ($q) {
                                $q->where(function ($wfo) {
                                    // WFO lengkap: check_in dan check_out
                                    $wfo->where('attendance_type', 'WFO')
                                        ->whereNotNull('check_in')
                                        ->whereNotNull('check_out');
                                })->orWhere(function ($dinas) {
                                    // Dinas Luar lengkap: check_in, absen_siang, dan check_out
                                    $dinas->where('attendance_type', 'Dinas Luar')
                                          ->whereNotNull('check_in')
                                          ->whereNotNull('absen_siang')
                                          ->whereNotNull('check_out');
                                });
                            });
                        } else {
                            // Tidak lengkap
                            return $query->where(function ($q) {
                                $q->where(function ($wfo) {
                                    // WFO tidak lengkap: tidak ada check_in atau check_out
                                    $wfo->where('attendance_type', 'WFO')
                                        ->where(function ($incomplete) {
                                            $incomplete->whereNull('check_in')
                                                      ->orWhereNull('check_out');
                                        });
                                })->orWhere(function ($dinas) {
                                    // Dinas Luar tidak lengkap: tidak ada salah satu dari check_in, absen_siang, atau check_out
                                    $dinas->where('attendance_type', 'Dinas Luar')
                                          ->where(function ($incomplete) {
                                              $incomplete->whereNull('check_in')
                                                        ->orWhereNull('absen_siang')
                                                        ->orWhereNull('check_out');
                                          });
                                });
                            });
                        }
                    }),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder => $query->thisMonth())
                    ->toggle(),

                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->today())
                    ->toggle(),
            ])
            ->actions([
                // Hanya menampilkan action Detail untuk Kepala Bidang
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_checkout')
                    ->label('Absen Keluar Terpilih')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Absen Keluar Pegawai Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin mengabsen keluar semua pegawai yang dipilih?')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->check_in && !$record->check_out) {
                                $record->update([
                                    'check_out' => now()->format('H:i:s')
                                ]);
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Absen Keluar Berhasil')
                            ->body("{$count} pegawai telah diabsen keluar.")
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = now()->format('Y-m-d');
        return static::getModel()::whereDate('created_at', $today)
            ->whereHas('user', function (Builder $query) {
                $query->where('role_user', 'employee');
            })
            ->whereNull('check_out')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return !$record->check_out; // Hanya bisa edit jika belum checkout
    }

    public static function canDelete($record): bool
    {
        return false; // Kepala bidang tidak bisa menghapus data absensi
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c);
    }
}
