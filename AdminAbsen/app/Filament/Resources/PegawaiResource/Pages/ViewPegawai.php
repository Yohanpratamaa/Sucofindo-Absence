<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    public function getTitle(): string
    {
        return 'Detail Pegawai: ' . $this->record->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Detail')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Data Pribadi')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('nama'),
                                        Infolists\Components\TextEntry::make('npp')->label('NPP'),
                                        Infolists\Components\TextEntry::make('email'),
                                        Infolists\Components\TextEntry::make('nik')->label('NIK'),
                                        Infolists\Components\TextEntry::make('status_pegawai')->badge(),
                                        Infolists\Components\TextEntry::make('status')->badge(),
                                        Infolists\Components\TextEntry::make('role_user')->badge(),
                                        Infolists\Components\TextEntry::make('nomor_handphone')->label('No. HP'),
                                        Infolists\Components\TextEntry::make('alamat')->columnSpanFull(),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Jabatan & Posisi')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('jabatan_nama')
                                            ->label('Jabatan'),
                                        Infolists\Components\TextEntry::make('jabatan_tunjangan')
                                            ->label('Tunjangan Jabatan')
                                            ->money('IDR'),
                                        Infolists\Components\TextEntry::make('posisi_nama')
                                            ->label('Posisi'),
                                        Infolists\Components\TextEntry::make('posisi_tunjangan')
                                            ->label('Tunjangan Posisi')
                                            ->money('IDR'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Pendidikan')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('pendidikan_list')
                                    ->label('Riwayat Pendidikan')
                                    ->schema([
                                        Infolists\Components\Grid::make(3)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('jenjang')
                                                    ->badge()
                                                    ->color('primary'),
                                                Infolists\Components\TextEntry::make('sekolah_univ')
                                                    ->label('Sekolah/Universitas'),
                                                Infolists\Components\TextEntry::make('fakultas_program_studi')
                                                    ->label('Fakultas'),
                                                Infolists\Components\TextEntry::make('jurusan'),
                                                Infolists\Components\TextEntry::make('thn_masuk')
                                                    ->label('Tahun Masuk')
                                                    ->date('Y'),
                                                Infolists\Components\TextEntry::make('thn_lulus')
                                                    ->label('Tahun Lulus')
                                                    ->date('Y'),
                                                Infolists\Components\TextEntry::make('ipk_nilai')
                                                    ->label('IPK/Nilai'),
                                                Infolists\Components\TextEntry::make('ijazah')
                                                    ->label('Ijazah')
                                                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null)
                                                    ->openUrlInNewTab(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->state(function ($record) {
                                        $pendidikan = $record->pendidikan_list;

                                        if (empty($pendidikan)) {
                                            return [];
                                        }

                                        if (is_string($pendidikan)) {
                                            $decoded = json_decode($pendidikan, true);
                                            return is_array($decoded) ? $decoded : [];
                                        }

                                        if (is_array($pendidikan)) {
                                            return $pendidikan;
                                        }

                                        return [];
                                    }),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Kontak Darurat')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('emergency_contacts')
                                    ->label('Daftar Kontak Darurat')
                                    ->schema([
                                        Infolists\Components\Grid::make(3)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('relationship')
                                                    ->label('Hubungan')
                                                    ->badge()
                                                    ->color('warning'),
                                                Infolists\Components\TextEntry::make('nama_kontak')
                                                    ->label('Nama'),
                                                Infolists\Components\TextEntry::make('no_emergency')
                                                    ->label('Nomor Telepon'),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->state(function ($record) {
                                        $contacts = $record->emergency_contacts;

                                        if (empty($contacts)) {
                                            return [];
                                        }

                                        if (is_string($contacts)) {
                                            $decoded = json_decode($contacts, true);
                                            return is_array($decoded) ? $decoded : [];
                                        }

                                        if (is_array($contacts)) {
                                            return $contacts;
                                        }

                                        return [];
                                    }),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Fasilitas')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('fasilitas_list')
                                    ->label('Daftar Fasilitas')
                                    ->schema([
                                        Infolists\Components\Grid::make(3)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('jenis_fasilitas')
                                                    ->label('Jenis')
                                                    ->badge()
                                                    ->color('primary'),
                                                Infolists\Components\TextEntry::make('nama_jaminan')
                                                    ->label('Nama Jaminan'),
                                                Infolists\Components\TextEntry::make('no_jaminan')
                                                    ->label('Nomor'),
                                                Infolists\Components\TextEntry::make('provider')
                                                    ->label('Provider'),
                                                Infolists\Components\TextEntry::make('nilai_fasilitas')
                                                    ->label('Nilai')
                                                    ->money('IDR'),
                                                Infolists\Components\TextEntry::make('status_fasilitas')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'aktif' => 'success',
                                                        'nonaktif' => 'danger',
                                                        'pending' => 'warning',
                                                        'expired' => 'gray',
                                                        default => 'gray',
                                                    }),
                                                Infolists\Components\TextEntry::make('tanggal_mulai')
                                                    ->label('Mulai')
                                                    ->date(),
                                                Infolists\Components\TextEntry::make('tanggal_berakhir')
                                                    ->label('Berakhir')
                                                    ->date()
                                                    ->placeholder('Tidak terbatas'),
                                                Infolists\Components\TextEntry::make('keterangan')
                                                    ->label('Keterangan')
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->state(function ($record) {
                                        // Pastikan data fasilitas_list adalah array
                                        $fasilitas = $record->fasilitas_list;

                                        // Jika null atau kosong, return array kosong
                                        if (empty($fasilitas)) {
                                            return [];
                                        }

                                        // Jika masih string JSON, decode dulu
                                        if (is_string($fasilitas)) {
                                            $decoded = json_decode($fasilitas, true);
                                            return is_array($decoded) ? $decoded : [];
                                        }

                                        // Jika sudah array, return as-is
                                        if (is_array($fasilitas)) {
                                            return $fasilitas;
                                        }

                                        // Fallback: return array kosong
                                        return [];
                                    }),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
