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
                                        Infolists\Components\TextEntry::make('alamat')->columnSpanFull(),
                                    ]),
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
                                    ->columns(1),
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
