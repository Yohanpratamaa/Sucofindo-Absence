<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use App\Models\Izin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DataAbsensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Statistik Absensi Bulan Ini
        $absensiThisMonth = Attendance::where('user_id', $userId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Statistik Dinas Luar Bulan Ini
        $dinasLuarThisMonth = Attendance::where('user_id', $userId)
            ->where('attendance_type', 'Dinas Luar')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Statistik Izin Bulan Ini
        $izinThisMonth = Izin::where('user_id', $userId)
            ->whereMonth('tanggal_mulai', $currentMonth)
            ->whereYear('tanggal_mulai', $currentYear)
            ->count();

        // Absensi Belum Lengkap
        $absensiTidakLengkap = Attendance::where('user_id', $userId)
            ->where(function ($query) {
                $query->where(function ($wfo) {
                    // WFO tidak lengkap
                    $wfo->where('attendance_type', 'WFO')
                        ->where(function ($incomplete) {
                            $incomplete->whereNull('check_in')
                                      ->orWhereNull('check_out');
                        });
                })->orWhere(function ($dinas) {
                    // Dinas Luar tidak lengkap
                    $dinas->where('attendance_type', 'Dinas Luar')
                          ->where(function ($incomplete) {
                              $incomplete->whereNull('check_in')
                                        ->orWhereNull('absen_siang')
                                        ->orWhereNull('check_out');
                          });
                });
            })
            ->count();

        // Keterlambatan Bulan Ini
        $terlambatThisMonth = Attendance::where('user_id', $userId)
            ->where('status_kehadiran', 'Terlambat')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        return [
            Stat::make('Total Absensi Bulan Ini', $absensiThisMonth)
                ->description('Absensi di bulan ' . Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([
                    $this->getWeeklyAttendanceChart()
                ]),

            Stat::make('Dinas Luar Bulan Ini', $dinasLuarThisMonth)
                ->description('Total dinas luar bulan ini')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),

            Stat::make('Pengajuan Izin Bulan Ini', $izinThisMonth)
                ->description('Total izin yang diajukan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Absensi Belum Lengkap', $absensiTidakLengkap)
                ->description($absensiTidakLengkap > 0 ? 'Segera lengkapi absensi' : 'Semua absensi lengkap')
                ->descriptionIcon($absensiTidakLengkap > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($absensiTidakLengkap > 0 ? 'danger' : 'success'),

            Stat::make('Keterlambatan Bulan Ini', $terlambatThisMonth)
                ->description($terlambatThisMonth > 0 ? 'Usahakan datang tepat waktu' : 'Tidak ada keterlambatan')
                ->descriptionIcon($terlambatThisMonth > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-badge')
                ->color($terlambatThisMonth > 0 ? 'warning' : 'success'),
        ];
    }

    protected function getWeeklyAttendanceChart(): array
    {
        $userId = Auth::id();
        $weeks = [];

        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();

            $count = Attendance::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            $weeks[] = $count;
        }

        return $weeks;
    }

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';
}
