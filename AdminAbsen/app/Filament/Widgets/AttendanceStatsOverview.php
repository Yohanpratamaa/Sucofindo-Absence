<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AttendanceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Hitung statistik dasar
        $totalPegawai = Pegawai::count();
        $absensiHariIni = Attendance::today()->count();
        $absensiMingguIni = Attendance::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $absensiBulanIni = Attendance::thisMonth()->count();

        // Hitung persentase kehadiran hari ini
        $persentaseHariIni = $totalPegawai > 0 ? round(($absensiHariIni / $totalPegawai) * 100, 1) : 0;

        // Hitung rata-rata absensi per hari bulan ini
        $hariKerjaBulanIni = $this->getWorkDaysThisMonth();
        $rataRataPerHari = $hariKerjaBulanIni > 0 ? round($absensiBulanIni / $hariKerjaBulanIni, 1) : 0;

        // Statistik terlambat hari ini
        $terlambatHariIni = Attendance::today()
            ->whereTime('check_in', '>', '08:00:00')
            ->count();

        return [
            Stat::make('Total Karyawan', $totalPegawai)
                ->description('Terdaftar dalam sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Absensi Hari Ini', $absensiHariIni)
                ->description("{$persentaseHariIni}% dari total karyawan")
                ->descriptionIcon($persentaseHariIni >= 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($persentaseHariIni >= 80 ? 'success' : 'warning')
                ->chart([3, 7, 5, 8, 6, 9, $absensiHariIni]),

            Stat::make('Terlambat Hari Ini', $terlambatHariIni)
                ->description($absensiHariIni > 0 ? round(($terlambatHariIni / $absensiHariIni) * 100, 1) . '% dari yang hadir' : '0%')
                ->descriptionIcon('heroicon-m-clock')
                ->color($terlambatHariIni == 0 ? 'success' : 'danger')
                ->chart([2, 1, 3, 0, 2, 1, $terlambatHariIni]),

            Stat::make('Absensi Minggu Ini', $absensiMingguIni)
                ->description('Total kehadiran minggu ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([5, 8, 6, 9, 7, 10, $absensiMingguIni]),

            Stat::make('Absensi Bulan Ini', $absensiBulanIni)
                ->description("Rata-rata {$rataRataPerHari} per hari")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success')
                ->chart([20, 25, 30, 28, 35, 32, $absensiBulanIni]),

            Stat::make('Rata-rata Kehadiran', $rataRataPerHari)
                ->description('Per hari kerja bulan ini')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary')
                ->chart([15, 18, 16, 20, 17, 19, $rataRataPerHari]),
        ];
    }

    private function getWorkDaysThisMonth(): int
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();

        $workDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }

        return $workDays;
    }
}
