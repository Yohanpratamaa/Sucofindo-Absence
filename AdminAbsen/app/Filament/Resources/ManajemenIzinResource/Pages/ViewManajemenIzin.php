<?php

namespace App\Filament\Resources\ManajemenIzinResource\Pages;

use App\Filament\Resources\ManajemenIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewManajemenIzin extends ViewRecord
{
    protected static string $resource = ManajemenIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Jenis Izin')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_izin')
                                    ->label('Nama Jenis Izin')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('kode_izin')
                                    ->label('Kode Izin')
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Aturan dan Pengaturan')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'cuti' => 'Cuti',
                                        'izin_khusus' => 'Izin Khusus',
                                        'sakit' => 'Sakit',
                                        'dinas' => 'Dinas',
                                        default => $state,
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'cuti' => 'success',
                                        'izin_khusus' => 'warning',
                                        'sakit' => 'danger',
                                        'dinas' => 'info',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('max_hari')
                                    ->label('Maksimal Hari')
                                    ->formatStateUsing(fn (?int $state): string => $state ? $state . ' hari' : 'Tanpa batas')
                                    ->badge()
                                    ->color(fn (?int $state): string => $state ? 'warning' : 'success'),

                                Infolists\Components\TextEntry::make('urutan_tampil')
                                    ->label('Urutan Tampil')
                                    ->badge(),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\IconEntry::make('perlu_dokumen')
                                    ->label('Memerlukan Dokumen')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                Infolists\Components\IconEntry::make('auto_approve')
                                    ->label('Otomatis Disetujui')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('warning'),

                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status Aktif')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Syarat Pengajuan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('syarat_pengajuan')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('syarat')
                                    ->label('')
                                    ->formatStateUsing(fn (string $state): string => "• " . $state)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Medium),
                            ])
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->syarat_pengajuan)),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('createdBy.nama')
                                    ->label('Dibuat Oleh')
                                    ->placeholder('Tidak diketahui'),

                                Infolists\Components\TextEntry::make('updatedBy.nama')
                                    ->label('Diperbarui Oleh')
                                    ->placeholder('Tidak diketahui'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
