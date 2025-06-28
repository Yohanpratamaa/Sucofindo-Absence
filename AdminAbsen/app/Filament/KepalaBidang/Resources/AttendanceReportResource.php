<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\AttendanceReportResource\Pages;
use App\Models\Attendance;
use App\Models\Pegawai;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceReportExport;
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

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationLabel = 'Export Laporan';

    protected static ?string $modelLabel = 'Export Laporan Absensi';

    protected static ?string $pluralModelLabel = 'Export Laporan Absensi';

    protected static ?string $navigationGroup = 'Laporan & Export';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Show attendance from team members (for demo, show all employee attendance)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        return parent::getEloquentQuery()
            ->with(['user'])
            ->whereIn('user_id', $teamMembers);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form tidak diperlukan untuk resource report
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // Query untuk rekap per karyawan dalam tim
                Pegawai::query()
                    ->select([
                        'pegawais.*',
                        DB::raw('COUNT(attendances.id) as total_hadir'),
                        DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                        DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                        DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                        DB::raw('AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL
                                       THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60
                                       ELSE NULL END) as avg_work_minutes'),
                    ])
                    ->leftJoin('attendances', 'pegawais.id', '=', 'attendances.user_id')
                    ->where('pegawais.role_user', 'employee')
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
                        $percentage = round(($record->total_hadir / $workDays) * 100, 1);

                        if ($percentage >= 90) return 'success';
                        if ($percentage >= 75) return 'warning';
                        return 'danger';
                    })
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('periode_custom')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] || $data['sampai_tanggal'],
                                function (Builder $query) use ($data) {
                                    $query->whereHas('attendances', function (Builder $q) use ($data) {
                                        if ($data['dari_tanggal']) {
                                            $q->whereDate('created_at', '>=', $data['dari_tanggal']);
                                        }
                                        if ($data['sampai_tanggal']) {
                                            $q->whereDate('created_at', '<=', $data['sampai_tanggal']);
                                        }
                                    });
                                }
                            );
                    }),

                Tables\Filters\SelectFilter::make('jabatan_nama')
                    ->label('Jabatan')
                    ->options(function () {
                        return Pegawai::where('role_user', 'employee')
                            ->where('status', 'active')
                            ->distinct()
                            ->pluck('jabatan_nama', 'jabatan_nama')
                            ->toArray();
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
                    ->color('info')
                    ->url(function ($record) {
                        // Navigate to a detailed attendance view for this employee
                        return '#'; // Akan diimplementasikan jika diperlukan
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // Bulk actions akan diimplementasikan jika diperlukan
            ])
            ->defaultSort('total_hadir', 'desc')
            ->emptyStateHeading('Tidak ada data rekap absensi')
            ->emptyStateDescription('Silakan tambahkan data absensi terlebih dahulu atau ubah filter periode.');
    }

    public static function getRelations(): array
    {
        return [
            // Relations tidak diperlukan untuk report
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceReports::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    private static function getWorkDaysInCurrentPeriod(): int
    {
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
