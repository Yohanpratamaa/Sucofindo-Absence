<?php

namespace App\Filament\KepalaBidang\Resources\AttendanceResource\Pages;

use App\Filament\KepalaBidang\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Carbon\Carbon;

class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Hanya menampilkan action Back untuk Kepala Bidang
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pegawai')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.nama')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('user.npp')
                            ->label('NPP (Nomor Pokok Pegawai)')
                            ->badge()
                            ->color('gray'),

                        Infolists\Components\TextEntry::make('user.jabatan_nama')
                            ->label('Jabatan')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Absensi')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('l, d F Y'))
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Waktu Absensi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('check_in_formatted')
                                    ->label('Check In')
                                    ->placeholder('Belum absen masuk')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('absen_siang_formatted')
                                    ->label('Absen Siang')
                                    ->placeholder('Tidak perlu / Belum absen')
                                    ->badge()
                                    ->color('warning')
                                    ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\TextEntry::make('check_out_formatted')
                                    ->label('Check Out')
                                    ->placeholder('Belum absen keluar')
                                    ->badge()
                                    ->color('danger'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('durasi_kerja')
                                    ->label('Durasi Kerja')
                                    ->placeholder('Belum selesai')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('overtime_formatted')
                                    ->label('Lembur')
                                    ->placeholder('Tidak ada lembur')
                                    ->badge()
                                    ->color('purple'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Status & Informasi')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('status_kehadiran')
                                    ->label('Status Kehadiran')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'Tepat Waktu' => 'success',
                                        'Terlambat' => 'warning',
                                        'Tidak Hadir' => 'danger',
                                        default => 'gray'
                                    }),

                                Infolists\Components\TextEntry::make('attendance_type')
                                    ->label('Tipe Absensi')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'WFO' => 'primary',
                                        'Dinas Luar' => 'warning',
                                        default => 'gray'
                                    }),
                            ]),

                        Infolists\Components\TextEntry::make('keterlambatan_detail')
                            ->label('Detail Keterlambatan')
                            ->placeholder('Tidak ada keterlambatan')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('kelengkapan_status')
                            ->label('Status Kelengkapan Absensi')
                            ->formatStateUsing(function ($record): string {
                                $kelengkapan = $record->kelengkapan_absensi;
                                return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
                            })
                            ->badge()
                            ->color(function ($record): string {
                                $kelengkapan = $record->kelengkapan_absensi;
                                return $kelengkapan['status'] === 'Lengkap' ? 'success' : 'warning';
                            })
                            ->columnSpanFull(),
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

                Infolists\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude_absen_masuk')
                                    ->label('Latitude Check In')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->latitude_absen_masuk)),

                                Infolists\Components\TextEntry::make('longitude_absen_masuk')
                                    ->label('Longitude Check In')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->longitude_absen_masuk)),

                                Infolists\Components\TextEntry::make('latitude_absen_siang')
                                    ->label('Latitude Absen Siang')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->latitude_absen_siang) && $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\TextEntry::make('longitude_absen_siang')
                                    ->label('Longitude Absen Siang')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->longitude_absen_siang) && $record->attendance_type === 'Dinas Luar'),

                                Infolists\Components\TextEntry::make('latitude_absen_pulang')
                                    ->label('Latitude Check Out')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->latitude_absen_pulang)),

                                Infolists\Components\TextEntry::make('longitude_absen_pulang')
                                    ->label('Longitude Check Out')
                                    ->placeholder('Lokasi tidak tersedia')
                                    ->visible(fn ($record) => !empty($record->longitude_absen_pulang)),
                            ]),
                    ])
                    ->visible(fn ($record) =>
                        !empty($record->latitude_absen_masuk) ||
                        !empty($record->latitude_absen_siang) ||
                        !empty($record->latitude_absen_pulang)
                    ),

                Infolists\Components\Section::make('Jadwal & Requirement')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('jam_masuk_standar')
                                    ->label('Jam Masuk Standar')
                                    ->placeholder('Tidak ada jadwal')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('jam_keluar_standar')
                                    ->label('Jam Keluar Standar')
                                    ->placeholder('Tidak ada jadwal')
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Infolists\Components\TextEntry::make('absensi_requirement')
                            ->label('Requirement Absensi')
                            ->placeholder('Tidak ada requirement khusus')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i:s'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diupdate Pada')
                                    ->dateTime('d M Y, H:i:s'),
                            ]),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Detail Data Absensi - ' . $this->record->user->nama;
    }
}
