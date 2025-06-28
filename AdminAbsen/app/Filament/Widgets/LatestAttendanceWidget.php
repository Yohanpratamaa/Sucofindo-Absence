<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAttendanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Absensi Terbaru';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with(['user'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Check In')
                    ->time('H:i')
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->check_in) return 'gray';
                        $checkIn = Carbon::parse($record->check_in);
                        $deadline = Carbon::parse('08:00:00');
                        return $checkIn->gt($deadline) ? 'danger' : 'success';
                    }),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('Belum checkout')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'danger',
                        'Tidak Hadir' => 'gray',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'WFO' => 'primary',
                        'Dinas Luar' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\ImageColumn::make('picture_absen_masuk_url')
                    ->label('Foto')
                    ->height(32)
                    ->width(32)
                    ->circular()
                    ->tooltip('Foto Check In'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (?Attendance $record): string => $record ? "/admin/attendances/{$record->id}" : '#')
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Belum ada absensi')
            ->emptyStateDescription('Data absensi akan muncul setelah pegawai melakukan absensi.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
