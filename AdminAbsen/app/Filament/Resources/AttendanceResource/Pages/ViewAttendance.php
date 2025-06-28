<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
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
            // Hanya menampilkan action Back untuk admin
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
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('user.npp')
                            ->label('NPP'),

                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),

                        Infolists\Components\TextEntry::make('user.jabatan')
                            ->label('Jabatan'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Absensi')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d F Y')),

                        Infolists\Components\TextEntry::make('attendance_type')
                            ->label('Tipe Absensi')
                            ->badge()
                            ->color(fn (string $state): string => match($state) {
                                'WFO' => 'primary',
                                'Dinas Luar' => 'warning',
                                default => 'gray'
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Status & Kelengkapan Absensi')
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

                        Infolists\Components\TextEntry::make('keterlambatan_detail')
                            ->label('Detail Keterlambatan'),

                        Infolists\Components\TextEntry::make('kelengkapan_absensi')
                            ->label('Kelengkapan Absensi')
                            ->formatStateUsing(function ($record): string {
                                $kelengkapan = $record->kelengkapan_absensi;
                                return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
                            })
                            ->badge()
                            ->color(function ($record): string {
                                $kelengkapan = $record->kelengkapan_absensi;
                                return $kelengkapan['status'] === 'Lengkap' ? 'success' : 'warning';
                            }),

                        Infolists\Components\TextEntry::make('durasi_kerja')
                            ->label('Durasi Kerja'),

                        Infolists\Components\TextEntry::make('overtime_formatted')
                            ->label('Lembur')
                            ->placeholder('Tidak ada lembur'),

                        Infolists\Components\TextEntry::make('absensi_requirement')
                            ->label('Requirement Absensi')
                            ->columnSpan(3),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Detail Waktu Absensi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_in')
                                        ->label('Check In')
                                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Check In')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray'),

                                    Infolists\Components\TextEntry::make('jam_masuk_standar')
                                        ->label('Jam Masuk Standar')
                                        ->suffix(' (Standar)')
                                        ->color('info'),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('absen_siang')
                                        ->label('Absen Siang')
                                        ->formatStateUsing(function ($state, $record) {
                                            if (!$record || $record->attendance_type === 'WFO') {
                                                return 'Tidak Diperlukan (WFO)';
                                            }
                                            return $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Absen Siang';
                                        })
                                        ->badge()
                                        ->color(function ($state, $record) {
                                            if (!$record || $record->attendance_type === 'WFO') return 'gray';
                                            return $state ? 'warning' : 'gray';
                                        }),

                                    Infolists\Components\TextEntry::make('attendance_type')
                                        ->label('Catatan Siang')
                                        ->formatStateUsing(fn ($state) => $state === 'WFO' ? 'WFO tidak perlu absen siang' : 'Dinas Luar wajib absen siang')
                                        ->color(fn ($state) => $state === 'WFO' ? 'info' : 'warning'),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_out')
                                        ->label('Check Out')
                                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Check Out')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'info' : 'gray'),

                                    Infolists\Components\TextEntry::make('jam_keluar_standar')
                                        ->label('Jam Keluar Standar')
                                        ->suffix(' (Standar)')
                                        ->color('info'),
                                ]),
                            ]),
                    ]),

                Infolists\Components\Section::make('📸 Foto Selfie Absensi')
                    ->description('Preview foto selfie yang diambil saat melakukan absensi dengan detail lokasi dan waktu')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // ===== FOTO CHECK IN =====
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_in_header')
                                        ->label('🌅 ABSEN MASUK / PAGI')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->check_in) {
                                                $time = Carbon::parse($record->check_in)->format('H:i:s');
                                                $deadline = Carbon::parse($record->jam_masuk_standar ?? '08:00:00');
                                                $checkInTime = Carbon::parse($record->check_in);
                                                $status = $checkInTime->gt($deadline) ? '🔴 TERLAMBAT' : '🟢 TEPAT WAKTU';
                                                return "{$time} - {$status}";
                                            }
                                            return '❌ Belum Check In';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            if (!$record->check_in) return 'gray';
                                            $deadline = Carbon::parse($record->jam_masuk_standar ?? '08:00:00');
                                            $checkInTime = Carbon::parse($record->check_in);
                                            return $checkInTime->gt($deadline) ? 'danger' : 'success';
                                        }),

                                    Infolists\Components\TextEntry::make('check_in_location')
                                        ->label('📍 Lokasi Check In')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->latitude_absen_masuk && $record->longitude_absen_masuk) {
                                                $lat = number_format($record->latitude_absen_masuk, 6);
                                                $lng = number_format($record->longitude_absen_masuk, 6);

                                                if ($record->attendance_type === 'WFO' && $record->officeSchedule?->office) {
                                                    $office = $record->officeSchedule->office;
                                                    $distance = $this->calculateDistance(
                                                        $office->latitude,
                                                        $office->longitude,
                                                        $record->latitude_absen_masuk,
                                                        $record->longitude_absen_masuk
                                                    );
                                                    return "Lat: {$lat}\nLng: {$lng}\n🏢 Jarak dari kantor: {$distance}m";
                                                }

                                                return "Lat: {$lat}\nLng: {$lng}\n🌍 Lokasi Dinas Luar";
                                            }
                                            return 'Tidak ada data lokasi';
                                        })
                                        ->copyable()
                                        ->copyMessage('Koordinat berhasil disalin!')
                                        ->color(fn ($record) => $record->attendance_type === 'WFO' ? 'primary' : 'warning'),

                                    Infolists\Components\ImageEntry::make('picture_absen_masuk_url')
                                        ->label('📷 Foto Selfie Check In')
                                        ->height(300)
                                        ->width('100%')
                                        ->extraAttributes([
                                            'class' => 'rounded-lg shadow-lg border-2 border-gray-200',
                                            'style' => 'object-fit: cover; cursor: pointer;'
                                        ])
                                        ->defaultImageUrl('/images/placeholder-selfie.jpg')
                                        ->visible(fn ($record) => $record->picture_absen_masuk)
                                        ->tooltip('Klik untuk memperbesar foto'),

                                    Infolists\Components\TextEntry::make('check_in_status')
                                        ->label('ℹ️ Status Check In')
                                        ->formatStateUsing(function ($record) {
                                            if (!$record->check_in) {
                                                return '❌ Belum melakukan check in';
                                            }
                                            $status = $record->picture_absen_masuk ? '✅ Foto tersedia' : '⚠️ Tidak ada foto';
                                            $location = ($record->latitude_absen_masuk && $record->longitude_absen_masuk) ? '📍 Lokasi terekam' : '📍 Lokasi tidak tersedia';
                                            return "{$status}\n{$location}";
                                        })
                                        ->color(function ($record) {
                                            if (!$record->check_in) return 'gray';
                                            return ($record->picture_absen_masuk && $record->latitude_absen_masuk) ? 'success' : 'warning';
                                        }),
                                ])
                                ->columnSpan(1),

                                // ===== FOTO ABSEN SIANG =====
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('absen_siang_header')
                                        ->label('☀️ ABSEN SIANG')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->attendance_type === 'WFO') {
                                                return '🏢 Tidak Diperlukan untuk WFO';
                                            }
                                            if ($record->absen_siang) {
                                                $time = Carbon::parse($record->absen_siang)->format('H:i:s');
                                                return "🟡 {$time} - SELESAI";
                                            }
                                            return '❌ Belum Absen Siang';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            if ($record->attendance_type === 'WFO') return 'gray';
                                            return $record->absen_siang ? 'warning' : 'danger';
                                        }),

                                    Infolists\Components\TextEntry::make('absen_siang_location')
                                        ->label('📍 Lokasi Absen Siang')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->attendance_type === 'WFO') {
                                                return 'WFO tidak memerlukan absen siang';
                                            }
                                            if ($record->latitude_absen_siang && $record->longitude_absen_siang) {
                                                $lat = number_format($record->latitude_absen_siang, 6);
                                                $lng = number_format($record->longitude_absen_siang, 6);
                                                return "Lat: {$lat}\nLng: {$lng}\n🌍 Lokasi Dinas Luar";
                                            }
                                            return 'Tidak ada data lokasi';
                                        })
                                        ->copyable()
                                        ->copyMessage('Koordinat berhasil disalin!')
                                        ->color(fn ($record) => $record->attendance_type === 'WFO' ? 'gray' : 'warning')
                                        ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar'),

                                    Infolists\Components\ImageEntry::make('picture_absen_siang_url')
                                        ->label('📷 Foto Selfie Absen Siang')
                                        ->height(300)
                                        ->width('100%')
                                        ->extraAttributes([
                                            'class' => 'rounded-lg shadow-lg border-2 border-orange-200',
                                            'style' => 'object-fit: cover; cursor: pointer;'
                                        ])
                                        ->defaultImageUrl('/images/placeholder-selfie.jpg')
                                        ->visible(fn ($record) => $record->attendance_type === 'Dinas Luar' && $record->picture_absen_siang)
                                        ->tooltip('Klik untuk memperbesar foto'),

                                    Infolists\Components\TextEntry::make('absen_siang_note')
                                        ->label('ℹ️ Catatan Absen Siang')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->attendance_type === 'WFO') {
                                                return '🏢 Mode WFO tidak memerlukan absen siang\n✅ Sistem otomatis melewati requirement ini';
                                            }
                                            if (!$record->absen_siang) {
                                                return '⚠️ WAJIB melakukan absen siang untuk Dinas Luar\n❌ Belum melakukan absen siang';
                                            }
                                            $status = $record->picture_absen_siang ? '✅ Foto tersedia' : '⚠️ Tidak ada foto';
                                            $location = ($record->latitude_absen_siang && $record->longitude_absen_siang) ? '📍 Lokasi terekam' : '📍 Lokasi tidak tersedia';
                                            return "✅ Absen siang selesai\n{$status}\n{$location}";
                                        })
                                        ->color(function ($record) {
                                            if ($record->attendance_type === 'WFO') return 'info';
                                            if (!$record->absen_siang) return 'danger';
                                            return ($record->picture_absen_siang && $record->latitude_absen_siang) ? 'success' : 'warning';
                                        }),
                                ])
                                ->columnSpan(1),

                                // ===== FOTO CHECK OUT =====
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_out_header')
                                        ->label('🌆 ABSEN PULANG / SORE')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->check_out) {
                                                $time = Carbon::parse($record->check_out)->format('H:i:s');
                                                $standardOut = Carbon::parse($record->jam_keluar_standar ?? '17:00:00');
                                                $checkOutTime = Carbon::parse($record->check_out);

                                                if ($checkOutTime->lt($standardOut)) {
                                                    $diff = $standardOut->diff($checkOutTime);
                                                    return "{$time} - 🔴 PULANG CEPAT ({$diff->format('%H:%I')} lebih awal)";
                                                } else {
                                                    return "{$time} - 🟢 NORMAL";
                                                }
                                            }
                                            return '❌ Belum Check Out';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            if (!$record->check_out) return 'gray';
                                            $standardOut = Carbon::parse($record->jam_keluar_standar ?? '17:00:00');
                                            $checkOutTime = Carbon::parse($record->check_out);
                                            return $checkOutTime->lt($standardOut) ? 'warning' : 'info';
                                        }),

                                    Infolists\Components\TextEntry::make('check_out_location')
                                        ->label('📍 Lokasi Check Out')
                                        ->formatStateUsing(function ($record) {
                                            if ($record->latitude_absen_pulang && $record->longitude_absen_pulang) {
                                                $lat = number_format($record->latitude_absen_pulang, 6);
                                                $lng = number_format($record->longitude_absen_pulang, 6);

                                                if ($record->attendance_type === 'WFO' && $record->officeSchedule?->office) {
                                                    $office = $record->officeSchedule->office;
                                                    $distance = $this->calculateDistance(
                                                        $office->latitude,
                                                        $office->longitude,
                                                        $record->latitude_absen_pulang,
                                                        $record->longitude_absen_pulang
                                                    );
                                                    return "Lat: {$lat}\nLng: {$lng}\n🏢 Jarak dari kantor: {$distance}m";
                                                }

                                                return "Lat: {$lat}\nLng: {$lng}\n🌍 Lokasi Dinas Luar";
                                            }
                                            return 'Tidak ada data lokasi';
                                        })
                                        ->copyable()
                                        ->copyMessage('Koordinat berhasil disalin!')
                                        ->color(fn ($record) => $record->attendance_type === 'WFO' ? 'primary' : 'warning'),

                                    Infolists\Components\ImageEntry::make('picture_absen_pulang_url')
                                        ->label('📷 Foto Selfie Check Out')
                                        ->height(300)
                                        ->width('100%')
                                        ->extraAttributes([
                                            'class' => 'rounded-lg shadow-lg border-2 border-blue-200',
                                            'style' => 'object-fit: cover; cursor: pointer;'
                                        ])
                                        ->defaultImageUrl('/images/placeholder-selfie.jpg')
                                        ->visible(fn ($record) => $record->picture_absen_pulang)
                                        ->tooltip('Klik untuk memperbesar foto'),

                                    Infolists\Components\TextEntry::make('check_out_status')
                                        ->label('ℹ️ Status Check Out')
                                        ->formatStateUsing(function ($record) {
                                            if (!$record->check_out) {
                                                return '❌ Belum melakukan check out';
                                            }
                                            $status = $record->picture_absen_pulang ? '✅ Foto tersedia' : '⚠️ Tidak ada foto';
                                            $location = ($record->latitude_absen_pulang && $record->longitude_absen_pulang) ? '📍 Lokasi terekam' : '📍 Lokasi tidak tersedia';
                                            return "{$status}\n{$location}";
                                        })
                                        ->color(function ($record) {
                                            if (!$record->check_out) return 'gray';
                                            return ($record->picture_absen_pulang && $record->latitude_absen_pulang) ? 'success' : 'warning';
                                        }),
                                ])
                                ->columnSpan(1),
                            ]),
                    ]),

                Infolists\Components\Section::make('Informasi Lokasi & Validasi')
                    ->description('Detail lokasi dan validasi berdasarkan tipe absensi')
                    ->schema([
                        Infolists\Components\TextEntry::make('attendance_type')
                            ->label('Tipe Absensi')
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record) return '-';
                                if ($state === 'WFO') {
                                    return 'Work From Office - Lokasi harus di area kantor';
                                } else {
                                    return 'Dinas Luar - Lokasi fleksibel sesuai penugasan';
                                }
                            })
                            ->color(fn ($state) => $state === 'WFO' ? 'primary' : 'warning'),

                        Infolists\Components\TextEntry::make('office_info')
                            ->label('Info Kantor')
                            ->formatStateUsing(function ($record) {
                                if ($record->attendance_type === 'WFO' && $record->officeSchedule?->office) {
                                    $office = $record->officeSchedule->office;
                                    return "Kantor: {$office->nama} | Lat: {$office->latitude}, Long: {$office->longitude}";
                                }
                                return 'Tidak ada info kantor (Dinas Luar)';
                            })
                            ->visible(fn ($record) => $record->attendance_type === 'WFO'),

                        Infolists\Components\TextEntry::make('schedule_info')
                            ->label('Info Jadwal')
                            ->formatStateUsing(function ($record) {
                                return "Jadwal ID: {$record->office_working_hours_id} | Masuk: {$record->jam_masuk_standar} | Keluar: {$record->jam_keluar_standar}";
                            }),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('ID Absensi'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d F Y H:i:s')),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d F Y H:i:s')),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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
