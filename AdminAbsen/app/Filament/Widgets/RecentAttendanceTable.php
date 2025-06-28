<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAttendanceTable extends BaseWidget
{
    protected static ?string $heading = 'Absensi Terbaru';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    // Enable real-time polling every 15 seconds
    protected static ?string $pollingInterval = '15s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('user.photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user->nama ?? 'N/A') . '&color=7F9CF5&background=EBF4FF')
                    ->size(40),

                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Check In')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('attendance_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'WFO' => 'primary',
                        'Dinas Luar' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi')
                    ->placeholder('-'),
            ])
            ->actions([
                // Hanya menampilkan action Detail untuk admin
                Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn (?Attendance $record): string => $record ? \App\Filament\Resources\AttendanceResource::getUrl('view', ['record' => $record]) : '#')
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
