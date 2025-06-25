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

    // Enable real-time polling every 10 seconds
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // Hitung statistik dasar dengan timestamp terkini
        $now = Carbon::now('Asia/Jakarta');
        $totalPegawai = Pegawai::count();
        $absensiHariIni = Attendance::today()->count();
        $absensiMingguIni = Attendance::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
        ])->count();
        $absensiBulanIni = Attendance::thisMonth()->count();

        // Hitung persentase kehadiran hari ini
        $persentaseHariIni = $totalPegawai > 0 ? round(($absensiHariIni / $totalPegawai) * 100, 1) : 0;

        // Hitung rata-rata absensi per hari bulan ini
        $hariKerjaBulanIni = $this->getWorkDaysThisMonth();
        $rataRataPerHari = $hariKerjaBulanIni > 0 ? round($absensiBulanIni / $hariKerjaBulanIni, 1) : 0;

        // Statistik terlambat hari ini (real-time)
        $terlambatHariIni = Attendance::today()
            ->whereTime('check_in', '>', '08:00:00')
            ->count();

        // Generate dynamic chart data based on current data
        $chartDataDaily = $this->getDailyChartData(7);
        $chartDataWeekly = $this->getWeeklyChartData(7);
        $chartDataMonthly = $this->getMonthlyChartData(6);

        return [
            Stat::make('Total Karyawan', $totalPegawai)
                ->description('Terdaftar dalam sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, $totalPegawai]),

            Stat::make('Absensi Hari Ini', $absensiHariIni)
                ->description("{$persentaseHariIni}% dari total karyawan")
                ->descriptionIcon($persentaseHariIni >= 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($persentaseHariIni >= 80 ? 'success' : 'warning')
                ->chart($chartDataDaily),

            Stat::make('Terlambat Hari Ini', $terlambatHariIni)
                ->description($absensiHariIni > 0 ? round(($terlambatHariIni / $absensiHariIni) * 100, 1) . '% dari yang hadir' : '0%')
                ->descriptionIcon('heroicon-m-clock')
                ->color($terlambatHariIni == 0 ? 'success' : 'danger')
                ->chart($this->getLateChartData(7)),

            Stat::make('Absensi Minggu Ini', $absensiMingguIni)
                ->description('Total kehadiran minggu ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart($chartDataWeekly),

            Stat::make('Absensi Bulan Ini', $absensiBulanIni)
                ->description("Rata-rata {$rataRataPerHari} per hari")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success')
                ->chart($chartDataMonthly),

            Stat::make('Update Terakhir', $now->format('H:i:s'))
                ->description($now->format('d M Y'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray')
                ->chart([1, 2, 1, 3, 2, 4, 3]),
        ];
    }

    private function getWorkDaysThisMonth(): int
    {
        $start = Carbon::now('Asia/Jakarta')->startOfMonth();
        $end = Carbon::now('Asia/Jakarta');
        
        $workDays = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workDays++;
            }
            $start->addDay();
        }
        
        return $workDays;
    }

    private function getDailyChartData(int $days): array
    {
        $data = [];
        $now = Carbon::now('Asia/Jakarta');
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $count = Attendance::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        return $data;
    }

    private function getWeeklyChartData(int $weeks): array
    {
        $data = [];
        $now = Carbon::now('Asia/Jakarta');
        
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startOfWeek = $now->copy()->subWeeks($i)->startOfWeek();
            $endOfWeek = $now->copy()->subWeeks($i)->endOfWeek();
            
            $count = Attendance::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $data[] = $count;
        }
        
        return $data;
    }

    private function getMonthlyChartData(int $months): array
    {
        $data = [];
        $now = Carbon::now('Asia/Jakarta');
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $startOfMonth = $now->copy()->subMonths($i)->startOfMonth();
            $endOfMonth = $now->copy()->subMonths($i)->endOfMonth();
            
            $count = Attendance::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $data[] = $count;
        }
        
        return $data;
    }

    private function getLateChartData(int $days): array
    {
        $data = [];
        $now = Carbon::now('Asia/Jakarta');
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $count = Attendance::whereDate('created_at', $date)
                ->whereTime('check_in', '>', '08:00:00')
                ->count();
            $data[] = $count;
        }
        
        return $data;
    }
}
