<?php

namespace App\Filament\Pegawai\Resources\MyAllAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyAllAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewMyAllAttendance extends ViewRecord
{
    protected static string $resource = MyAllAttendanceResource::class;

    public function getTitle(): string
    {
        return 'Detail Absensi';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Absensi')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal')
                                    ->date('l, d F Y')
                                    ->icon('heroicon-m-calendar-days'),

                                Infolists\Components\TextEntry::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'WFO' => 'primary',
                                        'Dinas Luar' => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Waktu Absensi')
                    ->icon('heroicon-m-clock')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('check_in')
                                    ->label('Check In')
                                    ->time('H:i:s')
                                    ->placeholder('Belum check in')
                                    ->icon('heroicon-m-arrow-right-on-rectangle')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('absen_siang')
                                    ->label('Absen Siang')
                                    ->time('H:i:s')
                                    ->placeholder('Tidak ada/belum absen siang')
                                    ->icon('heroicon-m-sun')
                                    ->color('warning')
                                    ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\TextEntry::make('check_out')
                                    ->label('Check Out')
                                    ->time('H:i:s')
                                    ->placeholder('Belum check out')
                                    ->icon('heroicon-m-arrow-left-on-rectangle')
                                    ->color('danger'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Status dan Durasi')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('status_kehadiran')
                                    ->label('Status Kehadiran')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Tepat Waktu' => 'success',
                                        'Terlambat' => 'warning',
                                        'Tidak Hadir' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('durasi_kerja')
                                    ->label('Durasi Kerja')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Belum selesai'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Foto Absensi')
                    ->icon('heroicon-m-camera')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('picture_absen_masuk_url')
                                    ->label('Foto Check In')
                                    ->height(200)
                                    ->width(200)
                                    ->placeholder('Tidak ada foto'),

                                Infolists\Components\ImageEntry::make('picture_absen_siang_url')
                                    ->label('Foto Absen Siang')
                                    ->height(200)
                                    ->width(200)
                                    ->placeholder('Tidak ada foto')
                                    ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\ImageEntry::make('picture_absen_pulang_url')
                                    ->label('Foto Check Out')
                                    ->height(200)
                                    ->width(200)
                                    ->placeholder('Tidak ada foto'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Lokasi Absensi')
                    ->icon('heroicon-m-map-pin')
                    ->schema([
                        Infolists\Components\Grid::make(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('location_check_in')
                                    ->label('Lokasi Check In')
                                    ->getStateUsing(function ($record) {
                                        if ($record->latitude_absen_masuk && $record->longitude_absen_masuk) {
                                            return "Lat: {$record->latitude_absen_masuk}, Lng: {$record->longitude_absen_masuk}";
                                        }
                                        return 'Lokasi tidak tersedia';
                                    })
                                    ->icon('heroicon-m-map-pin')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('location_absen_siang')
                                    ->label('Lokasi Absen Siang')
                                    ->getStateUsing(function ($record) {
                                        if ($record->latitude_absen_siang && $record->longitude_absen_siang) {
                                            return "Lat: {$record->latitude_absen_siang}, Lng: {$record->longitude_absen_siang}";
                                        }
                                        return 'Lokasi tidak tersedia';
                                    })
                                    ->icon('heroicon-m-map-pin')
                                    ->color('warning')
                                    ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\TextEntry::make('location_check_out')
                                    ->label('Lokasi Check Out')
                                    ->getStateUsing(function ($record) {
                                        if ($record->latitude_absen_pulang && $record->longitude_absen_pulang) {
                                            return "Lat: {$record->latitude_absen_pulang}, Lng: {$record->longitude_absen_pulang}";
                                        }
                                        return 'Lokasi tidak tersedia';
                                    })
                                    ->icon('heroicon-m-map-pin')
                                    ->color('danger'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Informasi Kantor')
                    ->icon('heroicon-m-building-office')
                    ->schema([
                        Infolists\Components\TextEntry::make('office.name')
                            ->label('Kantor')
                            ->placeholder('Tidak terdaftar'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dicatat pada')
                            ->dateTime('d F Y, H:i:s'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir diupdate')
                            ->dateTime('d F Y, H:i:s'),
                    ])
                    ->visible(fn ($record) => $record->attendance_type === 'WFO'),
            ]);
    }
}
