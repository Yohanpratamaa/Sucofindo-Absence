<?php

namespace App\Filament\Pegawai\Resources\MyIzinResource\Pages;

use App\Filament\Pegawai\Resources\MyIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewMyIzin extends ViewRecord
{
    protected static string $resource = MyIzinResource::class;

    public function getTitle(): string
    {
        return 'Detail Pengajuan Izin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => is_null($record->approved_by)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pengajuan Izin')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis_izin')
                                    ->label('Jenis Izin')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'sakit' => 'danger',
                                        'cuti' => 'success',
                                        'izin' => 'warning',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('approval_status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($record): string => match (true) {
                                        is_null($record->approved_by) => 'warning',
                                        !is_null($record->approved_at) => 'success',
                                        default => 'danger',
                                    })
                                    ->formatStateUsing(fn ($record): string => match (true) {
                                        is_null($record->approved_by) => 'Menunggu Persetujuan',
                                        !is_null($record->approved_at) => 'Disetujui',
                                        default => 'Ditolak',
                                    }),

                                Infolists\Components\TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('tanggal_akhir')
                                    ->label('Tanggal Akhir')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('durasi_izin')
                                    ->label('Durasi Izin')
                                    ->getStateUsing(function ($record) {
                                        $start = $record->tanggal_mulai;
                                        $end = $record->tanggal_akhir;
                                        $diff = $start->diffInDays($end) + 1;
                                        return $diff . ' hari';
                                    }),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Waktu Pengajuan')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('keterangan')
                                    ->label('Keterangan/Alasan')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('dokumen_pendukung')
                                    ->label('Dokumen Pendukung')
                                    ->formatStateUsing(fn ($state) => $state ? 'Ada dokumen' : 'Tidak ada dokumen')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Informasi Persetujuan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('approvedBy.nama')
                                    ->label('Diproses Oleh')
                                    ->placeholder('Belum diproses'),

                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Tanggal Diproses')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('Belum diproses'),

                                Infolists\Components\TextEntry::make('status_keterangan')
                                    ->label('Keterangan Persetujuan')
                                    ->columnSpanFull()
                                    ->getStateUsing(fn ($record) => match (true) {
                                        is_null($record->approved_by) => 'Menunggu persetujuan dari atasan',
                                        !is_null($record->approved_at) => 'Izin telah disetujui',
                                        default => 'Izin ditolak',
                                    }),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->approved_by !== null),
            ]);
    }
}
