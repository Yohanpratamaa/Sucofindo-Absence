<?php

namespace App\Filament\Pegawai\Resources\MyDinasLuarAttendanceResource\Pages;

use App\Filament\Pegawai\Resources\MyDinasLuarAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Carbon\Carbon;

class ViewMyDinasLuarAttendance extends ViewRecord
{
    protected static string $resource = MyDinasLuarAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                Infolists\Components\Section::make('Informasi Absensi')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d F Y')),

                        Infolists\Components\TextEntry::make('attendance_type')
                            ->label('Tipe Absensi')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('status_kehadiran')
                            ->label('Status Kehadiran')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Tepat Waktu' => 'success',
                                'Terlambat' => 'warning',
                                'Tidak Hadir' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Detail Absensi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_in')
                                        ->label('Absen Pagi')
                                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Absen')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray'),

                                    Infolists\Components\TextEntry::make('latitude_absen_masuk')
                                        ->label('Latitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\TextEntry::make('longitude_absen_masuk')
                                        ->label('Longitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\ImageEntry::make('picture_absen_masuk_url')
                                        ->label('Foto Absen Pagi')
                                        ->height(200)
                                        ->extraAttributes(['class' => 'rounded-lg'])
                                        ->defaultImageUrl(asset('images/no-image.png'))
                                        ->visible(fn ($record) => $record->picture_absen_masuk),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('absen_siang')
                                        ->label('Absen Siang')
                                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Absen')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'warning' : 'gray'),

                                    Infolists\Components\TextEntry::make('latitude_absen_siang')
                                        ->label('Latitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\TextEntry::make('longitude_absen_siang')
                                        ->label('Longitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\ImageEntry::make('picture_absen_siang_url')
                                        ->label('Foto Absen Siang')
                                        ->height(200)
                                        ->extraAttributes(['class' => 'rounded-lg'])
                                        ->defaultImageUrl(asset('images/no-image.png'))
                                        ->visible(fn ($record) => $record->picture_absen_siang),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('check_out')
                                        ->label('Absen Sore')
                                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i:s') : 'Belum Absen')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'info' : 'gray'),

                                    Infolists\Components\TextEntry::make('latitude_absen_pulang')
                                        ->label('Latitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\TextEntry::make('longitude_absen_pulang')
                                        ->label('Longitude')
                                        ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),

                                    Infolists\Components\ImageEntry::make('picture_absen_pulang_url')
                                        ->label('Foto Absen Sore')
                                        ->height(200)
                                        ->extraAttributes(['class' => 'rounded-lg'])
                                        ->defaultImageUrl(asset('images/no-image.png'))
                                        ->visible(fn ($record) => $record->picture_absen_pulang),
                                ]),
                            ])
                    ]),

                Infolists\Components\Section::make('Progress Absensi')
                    ->schema([
                        Infolists\Components\TextEntry::make('progress_info')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $pagi = !is_null($record->check_in);
                                $siang = !is_null($record->absen_siang);
                                $sore = !is_null($record->check_out);

                                $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
                                $percentage = round(($completed / 3) * 100);

                                $status = [];
                                $status[] = $pagi ? '✅ Absen Pagi' : '❌ Belum Absen Pagi';
                                $status[] = $siang ? '✅ Absen Siang' : '❌ Belum Absen Siang';
                                $status[] = $sore ? '✅ Absen Sore' : '❌ Belum Absen Sore';

                                return "Progress: {$percentage}%\n\n" . implode("\n", $status);
                            })
                            ->prose()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function getTitle(): string
    {
        return 'Detail Absensi Dinas Luar - ' . $this->record->created_at->format('d F Y');
    }
}
