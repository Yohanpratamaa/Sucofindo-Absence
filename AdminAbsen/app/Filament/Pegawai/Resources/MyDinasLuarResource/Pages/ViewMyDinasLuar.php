<?php

namespace App\Filament\Pegawai\Resources\MyDinasLuarResource\Pages;

use App\Filament\Pegawai\Resources\MyDinasLuarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Models\Attendance;

class ViewMyDinasLuar extends ViewRecord
{
    protected static string $resource = MyDinasLuarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada edit action karena data tidak bisa diubah
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dinas Luar')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Dinas')
                                    ->date('d M Y')
                                    ->icon('heroicon-m-calendar-days')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Waktu Absensi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('check_in')
                                    ->label('Check In')
                                    ->time('H:i')
                                    ->placeholder('Belum check in')
                                    ->icon('heroicon-m-arrow-right-on-rectangle')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('absen_siang')
                                    ->label('Absen Siang')
                                    ->time('H:i')
                                    ->placeholder('Belum absen siang')
                                    ->icon('heroicon-m-sun')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('check_out')
                                    ->label('Check Out')
                                    ->time('H:i')
                                    ->placeholder('Belum check out')
                                    ->icon('heroicon-m-arrow-left-on-rectangle')
                                    ->color('danger'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('durasi_kerja')
                                    ->label('Durasi Kerja')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('status_kehadiran')
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

                Infolists\Components\Section::make('Kelengkapan Absensi')
                    ->schema([
                        Infolists\Components\TextEntry::make('kelengkapan_info')
                            ->label('Status Kelengkapan')
                            ->getStateUsing(function (?Attendance $record): string {
                                if (!$record) return '-';
                                $kelengkapan = $record->kelengkapan_absensi;
                                return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
                            })
                            ->badge()
                            ->color(function (?Attendance $record): string {
                                if (!$record) return 'gray';
                                $kelengkapan = $record->kelengkapan_absensi;
                                return $kelengkapan['status'] === 'Lengkap' ? 'success' : 'warning';
                            }),

                        Infolists\Components\TextEntry::make('absensi_requirement')
                            ->label('Requirement Dinas Luar')
                            ->placeholder('Tidak ada requirement khusus')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Foto Absensi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('picture_absen_masuk_url')
                                    ->label('Foto Check In')
                                    ->height(200)
                                    ->width(200),

                                Infolists\Components\ImageEntry::make('picture_absen_siang_url')
                                    ->label('Foto Absen Siang')
                                    ->height(200)
                                    ->width(200),

                                Infolists\Components\ImageEntry::make('picture_absen_pulang_url')
                                    ->label('Foto Check Out')
                                    ->height(200)
                                    ->width(200),
                            ]),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('lokasi_check_in')
                                    ->label('Lokasi Check In')
                                    ->getStateUsing(function (?Attendance $record): string {
                                        if (!$record || !$record->latitude_absen_masuk || !$record->longitude_absen_masuk) {
                                            return 'Tidak ada data lokasi';
                                        }
                                        return sprintf('Lat: %s, Lng: %s', (string)$record->latitude_absen_masuk, (string)$record->longitude_absen_masuk);
                                    }),

                                Infolists\Components\TextEntry::make('lokasi_check_out')
                                    ->label('Lokasi Check Out')
                                    ->getStateUsing(function (?Attendance $record): string {
                                        if (!$record || !$record->latitude_absen_pulang || !$record->longitude_absen_pulang) {
                                            return 'Tidak ada data lokasi';
                                        }
                                        return sprintf('Lat: %s, Lng: %s', (string)$record->latitude_absen_pulang, (string)$record->longitude_absen_pulang);
                                    }),

                                Infolists\Components\TextEntry::make('lokasi_absen_siang')
                                    ->label('Lokasi Absen Siang')
                                    ->getStateUsing(function (?Attendance $record): string {
                                        if (!$record || !$record->latitude_absen_siang || !$record->longitude_absen_siang) {
                                            return 'Tidak ada data lokasi';
                                        }
                                        return sprintf('Lat: %s, Lng: %s', (string)$record->latitude_absen_siang, (string)$record->longitude_absen_siang);
                                    })
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('overtime_formatted')
                                    ->label('Waktu Lembur')
                                    ->placeholder('Tidak ada lembur'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
