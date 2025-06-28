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

                                Infolists\Components\TextEntry::make('assigned_at')
                                    ->label('Waktu Mulai Lembur')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Waktu Pengajuan')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('keterangan')
                                    ->label('Keterangan')
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

                                Infolists\Components\TextEntry::make('approval_info')
                                    ->label('Keterangan Persetujuan')
                                    ->columnSpanFull()
                                    ->placeholder('Tidak ada keterangan'),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->approved_by !== null),
            ]);
    }
}
