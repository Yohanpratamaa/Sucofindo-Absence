<?php

namespace App\Filament\Resources\IzinResource\Pages;

use App\Filament\Resources\IzinResource;
use App\Models\Izin;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class ViewIzin extends ViewRecord
{
    protected static string $resource = IzinResource::class;

    public function getTitle(): string
    {
        return 'Detail Izin - ' . $this->record->user->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Pegawai')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('user.nama')
                                    ->label('Nama Pegawai'),
                                Components\TextEntry::make('user.npp')
                                    ->label('NPP'),
                                Components\TextEntry::make('user.jabatan.nama')
                                    ->label('Jabatan'),
                                Components\TextEntry::make('user.posisi_nama')
                                    ->label('Posisi'),
                            ]),
                    ]),

                Components\Section::make('Detail Izin')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('jenis_izin')
                                    ->label('Jenis Izin')
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match($state) {
                                            'cuti' => 'primary',
                                            'sakit' => 'warning',
                                            'izin' => 'info',
                                            default => 'gray'
                                        };
                                    }),
                                Components\TextEntry::make('periode_izin')
                                    ->label('Periode Izin'),
                                Components\TextEntry::make('durasi_hari')
                                    ->label('Durasi')
                                    ->formatStateUsing(fn (int $state): string => $state . ' hari'),
                                Components\TextEntry::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->dateTime(),
                            ]),
                        Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Status Persetujuan')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->getStateUsing(function (Izin $record): string {
                                        return $record->status_badge['label'];
                                    })
                                    ->color(function (Izin $record): string {
                                        return $record->status_badge['color'];
                                    }),
                                Components\TextEntry::make('approval_info')
                                    ->label('Informasi Persetujuan')
                                    ->getStateUsing(function (Izin $record): string {
                                        return $record->approval_info;
                                    }),
                            ]),
                    ]),

                Components\Section::make('Dokumen Pendukung')
                    ->schema([
                        Components\TextEntry::make('dokumen_pendukung')
                            ->label('File Dokumen')
                            ->formatStateUsing(function (?string $state): string {
                                if (!$state) return '-';
                                return '<a href="' . asset('storage/' . $state) . '" target="_blank" class="text-primary-600 hover:text-primary-500 underline">Lihat Dokumen</a>';
                            })
                            ->html(),
                    ])
                    ->visible(fn (Izin $record): bool => !empty($record->dokumen_pendukung)),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui Izin')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Izin')
                ->modalDescription(function (): string {
                    $currentUser = Filament::auth()->user();
                    return "Apakah Anda yakin ingin menyetujui izin ini?\n\nIzin akan tercatat disetujui oleh: {$currentUser->nama}";
                })
                ->action(function (): void {
                    $currentUser = Filament::auth()->user();
                    $this->record->approve(Filament::auth()->id());

                    Notification::make()
                        ->success()
                        ->title('Izin Disetujui')
                        ->body("Izin telah berhasil disetujui oleh {$currentUser->nama}")
                        ->send();
                })
                ->visible(fn (): bool => $this->record->status === 'pending'),

            Actions\Action::make('reject')
                ->label('Tolak Izin')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Izin')
                ->modalDescription(function (): string {
                    $currentUser = Filament::auth()->user();
                    return "Apakah Anda yakin ingin menolak izin ini?\n\nIzin akan tercatat ditolak oleh: {$currentUser->nama}";
                })
                ->action(function (): void {
                    $currentUser = Filament::auth()->user();
                    $this->record->reject(Filament::auth()->id());

                    Notification::make()
                        ->success()
                        ->title('Izin Ditolak')
                        ->body("Izin telah berhasil ditolak oleh {$currentUser->nama}")
                        ->send();
                })
                ->visible(fn (): bool => $this->record->status === 'pending'),
        ];
    }
}
