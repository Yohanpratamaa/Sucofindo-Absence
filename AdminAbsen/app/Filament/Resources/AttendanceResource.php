<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Pegawai;
use App\Exports\AttendanceExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Fieldset;
use Filament\Support\Enums\FontWeight;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Manajemen Absensi';

    protected static ?string $modelLabel = 'Data Absensi';

    protected static ?string $pluralModelLabel = 'Manajemen Absensi';

    protected static ?int $navigationSort = 5;

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
                                    ->relationship('user', 'nama')
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
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_masuk')
                                    ->label('Latitude Check In')
                                    ->disabled(),

                                Forms\Components\TextInput::make('longitude_absen_masuk')
                                    ->label('Longitude Check In')
                                    ->disabled(),

                                Forms\Components\FileUpload::make('picture_absen_masuk')
                                    ->label('Foto Check In')
                                    ->image()
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Lokasi Absen Siang')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_siang')
                                    ->label('Latitude Absen Siang')
                                    ->disabled(),

                                Forms\Components\TextInput::make('longitude_absen_siang')
                                    ->label('Longitude Absen Siang')
                                    ->disabled(),

                                Forms\Components\FileUpload::make('picture_absen_siang')
                                    ->label('Foto Absen Siang')
                                    ->image()
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Lokasi Check Out')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude_absen_pulang')
                                    ->label('Latitude Check Out')
                                    ->disabled(),

                                Forms\Components\TextInput::make('longitude_absen_pulang')
                                    ->label('Longitude Check Out')
                                    ->disabled(),

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
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_absen')
                    ->label('Tanggal')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in_formatted')
                    ->label('Check In')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('check_in', $direction);
                    }),

                Tables\Columns\TextColumn::make('absen_siang_formatted')
                    ->label('Absen Siang')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('check_out_formatted')
                    ->label('Check Out')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('check_out', $direction);
                    }),

                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi Kerja'),

                Tables\Columns\TextColumn::make('overtime_formatted')
                    ->label('Lembur')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('keterlambatan_detail')
                    ->label('Detail')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('jam_masuk_standar')
                    ->label('Jam Masuk Std')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'WFO' => 'primary',
                        'Dinas Luar' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'nama')
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
                Tables\Actions\ViewAction::make(),
                // Export actions sudah dipindahkan ke header actions di ListAttendances
            ])
            ->bulkActions([
                // Tidak ada bulk actions untuk absensi
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto refresh every 30 seconds for real-time updates
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
            'index' => Pages\ListAttendances::route('/'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            // Admin hanya bisa view, tidak bisa create/edit absensi
        ];
    }
}
