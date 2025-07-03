<?php

namespace App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewMyOvertimeRequest extends ViewRecord
{
    protected static string $resource = MyOvertimeRequestResource::class;

    public function getTitle(): string
    {
        return 'Detail Pengajuan Lembur';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => $record->status === 'Assigned'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pengajuan Lembur')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('overtime_id')
                                    ->label('ID Lembur')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Assigned' => 'warning',
                                        'Accepted' => 'success',
                                        'Rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'Assigned' => 'Menunggu Persetujuan',
                                        'Accepted' => 'Disetujui',
                                        'Rejected' => 'Ditolak',
                                        default => ucfirst($state),
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Jadwal Lembur')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hari_lembur')
                                    ->label('Hari')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('tanggal_lembur')
                                    ->label('Tanggal')
                                    ->date('d M Y')
                                    ->icon('heroicon-m-calendar-days'),

                                Infolists\Components\TextEntry::make('total_jam_formatted')
                                    ->label('Total Jam')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-clock'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->time('H:i')
                                    ->icon('heroicon-m-play')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->time('H:i')
                                    ->icon('heroicon-m-stop')
                                    ->color('danger'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Detail Lembur')
                    ->schema([
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Lembur')
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('assigned_at')
                                    ->label('Waktu Pengajuan')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-m-paper-airplane'),

                                Infolists\Components\TextEntry::make('user.nama')
                                    ->label('Diajukan Oleh')
                                    ->icon('heroicon-m-user'),

                                Infolists\Components\TextEntry::make('total_jam')
                                    ->label('Total Jam Lembur')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-clock')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state || $state == 0) return '0 menit';
                                        
                                        $hours = floor($state / 60);
                                        $minutes = $state % 60;
                                        
                                        if ($hours > 0 && $minutes > 0) {
                                            return "{$hours} jam {$minutes} menit";
                                        } elseif ($hours > 0) {
                                            return "{$hours} jam";
                                        } else {
                                            return "{$minutes} menit";
                                        }
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('approvedBy.nama')
                                    ->label('Diproses Oleh')
                                    ->placeholder('Belum diproses')
                                    ->icon('heroicon-m-user-circle'),

                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Waktu Diproses')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('Belum diproses')
                                    ->icon('heroicon-m-check-circle'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->approved_by || $record->approved_at),
            ]);
    }
}
