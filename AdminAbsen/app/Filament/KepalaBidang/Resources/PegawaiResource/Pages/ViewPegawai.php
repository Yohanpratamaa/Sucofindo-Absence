<?php

namespace App\Filament\KepalaBidang\Resources\PegawaiResource\Pages;

use App\Filament\KepalaBidang\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Data'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Data Personal')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Lengkap')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),

                        TextEntry::make('npp')
                            ->label('NPP')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('no_hp')
                            ->label('Nomor HP')
                            ->copyable()
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d F Y'),

                        TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'L' => 'blue',
                                'P' => 'pink',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => $state,
                            }),

                        TextEntry::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data Kepegawaian')
                    ->schema([
                        TextEntry::make('jabatan')
                            ->label('Jabatan')
                            ->weight(FontWeight::SemiBold),

                        TextEntry::make('divisi')
                            ->label('Divisi/Departemen')
                            ->weight(FontWeight::SemiBold),

                        TextEntry::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->date('d F Y')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('status')
                            ->label('Status Pegawai')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'resigned' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Aktif',
                                'inactive' => 'Non-Aktif',
                                'resigned' => 'Mengundurkan Diri',
                                default => $state,
                            }),

                        TextEntry::make('role_user')
                            ->label('Role')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'employee' => 'Karyawan',
                                'kepala_bidang' => 'Kepala Bidang',
                                'super_admin' => 'Super Admin',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d F Y, H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d F Y, H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Detail Pegawai: ' . $this->record->nama;
    }
}
