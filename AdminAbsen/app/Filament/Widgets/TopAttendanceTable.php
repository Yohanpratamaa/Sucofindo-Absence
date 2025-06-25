<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class TopAttendanceTable extends BaseWidget
{
    protected static ?string $heading = 'Karyawan dengan Absensi Terbanyak (Bulan Ini)';

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pegawai::query()
                    ->withCount([
                        'attendances as total_absensi' => function ($query) {
                            $query->thisMonth();
                        },
                        'attendances as tepat_waktu' => function ($query) {
                            $query->thisMonth()
                                  ->whereTime('check_in', '<=', '08:00:00')
                                  ->whereNotNull('check_in');
                        },
                        'attendances as terlambat' => function ($query) {
                            $query->thisMonth()
                                  ->whereTime('check_in', '>', '08:00:00')
                                  ->whereNotNull('check_in');
                        }
                    ])
                    ->having('total_absensi', '>', 0)
                    ->orderBy('total_absensi', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('ranking')
                    ->label('#')
                    ->state(function ($record, $rowLoop) {
                        return $rowLoop->iteration;
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'orange',
                        default => 'primary'
                    }),

                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama) . '&color=7F9CF5&background=EBF4FF')
                    ->size(40),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('npp')
                    ->label('NPP')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('jabatan_nama')
                    ->label('Jabatan')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('total_absensi')
                    ->label('Total Absensi')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tepat_waktu')
                    ->label('Tepat Waktu')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('terlambat')
                    ->label('Terlambat')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('persentase_tepat_waktu')
                    ->label('% Tepat Waktu')
                    ->state(function ($record) {
                        if ($record->total_absensi > 0) {
                            return round(($record->tepat_waktu / $record->total_absensi) * 100, 1) . '%';
                        }
                        return '0%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->total_absensi > 0) {
                            $percentage = ($record->tepat_waktu / $record->total_absensi) * 100;
                            return $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                        }
                        return 'gray';
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Pegawai $record): string => \App\Filament\Resources\PegawaiResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
