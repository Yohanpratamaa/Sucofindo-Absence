<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceReportResource\Pages;
use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Support\Enums\FontWeight;

class AttendanceReportResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Rekap Absensi';

    protected static ?string $modelLabel = 'Rekap Absensi';

    protected static ?string $pluralModelLabel = 'Rekap Absensi';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // Query untuk rekap per karyawan
                Pegawai::query()
                    ->select([
                        'pegawais.*',                        DB::raw('COUNT(attendances.id) as total_hadir'),
                        DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                        DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                        DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                        DB::raw('AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL
                                       THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60
                                       ELSE NULL END) as avg_work_minutes'),
                    ])
                    ->leftJoin('attendances', 'pegawais.id', '=', 'attendances.user_id')
                    ->where('pegawais.status', 'active')
                    ->groupBy('pegawais.id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('npp')
                    ->label('NPP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan_nama')
                    ->label('Jabatan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_hadir')
                    ->label('Total Hadir')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_terlambat')
                    ->label('Terlambat')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('total_tidak_checkout')
                    ->label('Tidak Checkout')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('total_overtime_minutes')
                    ->label('Total Lembur')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state <= 0) return '-';
                        $hours = intval($state / 60);
                        $minutes = $state % 60;
                        return $hours . 'j ' . $minutes . 'm';
                    })
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('avg_work_minutes')
                    ->label('Rata-rata Kerja/Hari')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state <= 0) return '-';
                        $hours = intval($state / 60);
                        $minutes = intval($state % 60);
                        return $hours . 'j ' . $minutes . 'm';
                    })
                    ->sortable()
                    ->alignCenter(),
                      Tables\Columns\TextColumn::make('tingkat_kehadiran')
                    ->label('Tingkat Kehadiran')
                    ->state(function ($record) {
                        $workDays = self::getWorkDaysInCurrentPeriod();
                        if ($workDays <= 0) return '0%';
                        $percentage = round(($record->total_hadir / $workDays) * 100, 1);
                        return $percentage . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $workDays = self::getWorkDaysInCurrentPeriod();
                        if ($workDays <= 0) return 'gray';
                        $percentage = ($record->total_hadir / $workDays) * 100;
                        return $percentage >= 90 ? 'success' : ($percentage >= 75 ? 'warning' : 'danger');
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('periode')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] && $data['sampai_tanggal'],
                                function (Builder $query) use ($data) {
                                    $query->whereHas('attendances', function (Builder $q) use ($data) {
                                        $q->whereBetween('created_at', [$data['dari_tanggal'], $data['sampai_tanggal']]);
                                    });
                                }
                            );
                    }),

                Tables\Filters\SelectFilter::make('jabatan_nama')
                    ->label('Jabatan')
                    ->options(function () {
                        return Pegawai::distinct()->pluck('jabatan_nama', 'jabatan_nama')->toArray();
                    }),
                      Tables\Filters\Filter::make('tingkat_kehadiran_rendah')
                    ->label('Kehadiran < 75%')
                    ->query(function (Builder $query): Builder {
                        $workDays = self::getWorkDaysInCurrentPeriod();
                        return $query->havingRaw('COUNT(attendances.id) < ?', [$workDays * 0.75]);
                    })
                    ->toggle(),

                Tables\Filters\Filter::make('sering_terlambat')
                    ->label('Sering Terlambat (>5x)')
                    ->query(function (Builder $query): Builder {
                        return $query->havingRaw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) > 5');
                    })
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('detail_attendance')
                    ->label('Detail Absensi')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => AttendanceResource::getUrl('index', ['tableFilters[user_id][value]' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // Export functionality bisa ditambahkan di sini
            ])
            ->defaultSort('total_hadir', 'desc')
            ->emptyStateHeading('Tidak ada data rekap absensi')
            ->emptyStateDescription('Silakan tambahkan data absensi terlebih dahulu atau ubah filter periode.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceReports::route('/'),
        ];
    }

    private static function getWorkDaysInCurrentPeriod(): int
    {
        // Default to current month if no filter is applied
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $workDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }

        return $workDays;
    }
}
