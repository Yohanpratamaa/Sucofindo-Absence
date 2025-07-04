<?php

namespace App\Filament\KepalaBidang\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Attendance;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceAnalytics extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.kepala-bidang.pages.attendance-analytics';

    protected static ?string $navigationLabel = 'Analisis Absensi';

    protected static ?string $navigationGroup = 'Laporan & Export';

    protected static ?int $navigationSort = 2;

    public function mount(): void
    {
        $this->filters = [
            'date_range' => 'month',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('date_range')
                            ->label('Periode')
                            ->options([
                                'week' => 'Minggu Ini',
                                'month' => 'Bulan Ini',
                                'quarter' => 'Kuartal Ini',
                                'custom' => 'Custom',
                            ])
                            ->default('month')
                            ->live()
                            ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                match ($state) {
                                    'week' => [
                                        $set('start_date', now()->startOfWeek()->toDateString()),
                                        $set('end_date', now()->endOfWeek()->toDateString()),
                                    ],
                                    'month' => [
                                        $set('start_date', now()->startOfMonth()->toDateString()),
                                        $set('end_date', now()->endOfMonth()->toDateString()),
                                    ],
                                    'quarter' => [
                                        $set('start_date', now()->startOfQuarter()->toDateString()),
                                        $set('end_date', now()->endOfQuarter()->toDateString()),
                                    ],
                                    default => null,
                                };
                            }),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('date_range') === 'custom'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('date_range') === 'custom'),
                    ]),
            ])
            ->statePath('filters');
    }

    public function getTitle(): string
    {
        return 'Analisis Absensi Tim';
    }

    public function getSubheading(): string
    {
        return 'Dashboard analisis dan insights absensi tim';
    }

    public function getAttendanceStats(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->toDateString();

        $totalEmployees = Pegawai::where('role_user', 'employee')->where('status', 'active')->count();

        $totalAttendance = Attendance::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function($query) {
                $query->where('role_user', 'employee');
            })
            ->count();

        $onTimeAttendance = Attendance::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_kehadiran', 'Tepat Waktu')
            ->whereHas('user', function($query) {
                $query->where('role_user', 'employee');
            })
            ->count();

        $lateAttendance = Attendance::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_kehadiran', 'Terlambat')
            ->whereHas('user', function($query) {
                $query->where('role_user', 'employee');
            })
            ->count();

        $absentCount = Attendance::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status_kehadiran', ['Tidak Hadir', 'Tidak Absensi'])
            ->whereHas('user', function($query) {
                $query->where('role_user', 'employee');
            })
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'total_attendance' => $totalAttendance,
            'on_time' => $onTimeAttendance,
            'late' => $lateAttendance,
            'absent' => $absentCount,
            'on_time_percentage' => $totalAttendance > 0 ? round(($onTimeAttendance / $totalAttendance) * 100, 1) : 0,
            'late_percentage' => $totalAttendance > 0 ? round(($lateAttendance / $totalAttendance) * 100, 1) : 0,
        ];
    }

    public function getTopPerformers(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->toDateString();

        return Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->withCount([
                'attendances as total_attendance' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'attendances as on_time_attendance' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->where('status_kehadiran', 'Tepat Waktu');
                }
            ])
            ->having('total_attendance', '>', 0)
            ->get()
            ->map(function ($employee) {
                $attendanceRate = $employee->total_attendance > 0
                    ? round(($employee->on_time_attendance / $employee->total_attendance) * 100, 1)
                    : 0;
                return [
                    'name' => $employee->nama,
                    'npp' => $employee->npp,
                    'total_attendance' => $employee->total_attendance,
                    'on_time_attendance' => $employee->on_time_attendance,
                    'attendance_rate' => $attendanceRate,
                ];
            })
            ->sortByDesc('attendance_rate')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function getDailyTrends(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->toDateString();

        $dailyStats = Attendance::selectRaw('DATE(created_at) as date,
                                           COUNT(*) as total,
                                           SUM(CASE WHEN status_kehadiran = "Tepat Waktu" THEN 1 ELSE 0 END) as on_time,
                                           SUM(CASE WHEN status_kehadiran = "Terlambat" THEN 1 ELSE 0 END) as late')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function($query) {
                $query->where('role_user', 'employee');
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->toArray();

        return $dailyStats;
    }
}
