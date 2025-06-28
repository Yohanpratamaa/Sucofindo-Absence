<?php

namespace App\Filament\Pegawai\Resources\MyAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyAttendanceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewMyAttendance extends ViewRecord
{
    protected static string $resource = MyAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Detail Absensi';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Absensi')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Tanggal')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),

                                Components\TextEntry::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'WFO' => 'primary',
                                        'Dinas Luar' => 'warning',
                                        default => 'gray',
                                    }),

                                Components\TextEntry::make('check_in')
                                    ->label('Check In')
                                    ->time('H:i')
                                    ->icon('heroicon-o-arrow-right-on-rectangle'),

                                Components\TextEntry::make('check_out')
                                    ->label('Check Out')
                                    ->time('H:i')
                                    ->placeholder('Belum check out')
                                    ->icon('heroicon-o-arrow-left-on-rectangle'),

                                Components\TextEntry::make('durasi_kerja')
                                    ->label('Durasi Kerja')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-clock'),

                                Components\TextEntry::make('status_kehadiran')
                                    ->label('Status Kehadiran')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Tepat Waktu' => 'success',
                                        'Terlambat' => 'warning',
                                        'Tidak Hadir' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Components\Section::make('Foto Absensi')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\ImageEntry::make('picture_absen_masuk')
                                    ->label('Foto Check In')
                                    ->disk('public')
                                    ->height(200)
                                    ->placeholder('Tidak ada foto'),

                                Components\ImageEntry::make('picture_absen_pulang')
                                    ->label('Foto Check Out')
                                    ->disk('public')
                                    ->height(200)
                                    ->placeholder('Belum check out'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->picture_absen_masuk || $record->picture_absen_pulang),

                Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('latitude_absen_masuk')
                                    ->label('Latitude Check In')
                                    ->placeholder('Tidak tersedia'),

                                Components\TextEntry::make('longitude_absen_masuk')
                                    ->label('Longitude Check In')
                                    ->placeholder('Tidak tersedia'),

                                Components\TextEntry::make('latitude_absen_pulang')
                                    ->label('Latitude Check Out')
                                    ->placeholder('Belum check out'),

                                Components\TextEntry::make('longitude_absen_pulang')
                                    ->label('Longitude Check Out')
                                    ->placeholder('Belum check out'),
                            ]),
                    ]),

                Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Components\TextEntry::make('absen_siang')
                            ->label('Absen Siang')
                            ->time('H:i')
                            ->placeholder('Tidak ada')
                            ->visible(fn ($record) => $record->absen_siang),

                        Components\TextEntry::make('overtime_formatted')
                            ->label('Lembur')
                            ->placeholder('Tidak ada')
                            ->visible(fn ($record) => $record->overtime > 0),

                        Components\TextEntry::make('officeSchedule.office.name')
                            ->label('Kantor')
                            ->placeholder('Tidak tersedia'),
                    ])
                    ->columns(3),
            ]);
    }
}
