<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use App\Models\Izin;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiCalendarWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.absensi-calendar';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    public function getViewData(): array
    {
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get all attendance for current month
        $attendances = Attendance::where('user_id', $userId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get()
            ->keyBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        // Get all izin for current month
        $izins = Izin::where('user_id', $userId)
            ->where(function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('tanggal_mulai', $currentMonth)
                      ->whereYear('tanggal_mulai', $currentYear)
                      ->orWhereMonth('tanggal_akhir', $currentMonth)
                      ->whereYear('tanggal_akhir', $currentYear);
            })
            ->get();

        // Build calendar data
        $calendarData = [];
        $startDate = Carbon::create($currentYear, $currentMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $dayData = [
                'date' => $date->copy(),
                'day' => $date->day,
                'is_weekend' => $date->isWeekend(),
                'is_today' => $date->isToday(),
                'attendance' => $attendances->get($dateKey),
                'izin' => $this->getIzinForDate($izins, $date),
                'status' => 'no_data'
            ];

            // Determine status
            if ($dayData['izin']) {
                $dayData['status'] = 'izin';
            } elseif ($dayData['attendance']) {
                $attendance = $dayData['attendance'];
                if ($attendance->attendance_type === 'Dinas Luar') {
                    $dayData['status'] = 'dinas_luar';
                } elseif ($attendance->status_kehadiran === 'Tepat Waktu') {
                    $dayData['status'] = 'hadir_tepat_waktu';
                } elseif ($attendance->status_kehadiran === 'Terlambat') {
                    $dayData['status'] = 'terlambat';
                }
            } elseif ($date->isPast() && !$date->isWeekend()) {
                $dayData['status'] = 'tidak_hadir';
            }

            $calendarData[] = $dayData;
        }

        return [
            'calendarData' => $calendarData,
            'currentMonth' => $startDate->format('F Y'),
            'stats' => [
                'total_hadir' => collect($calendarData)->where('status', 'hadir_tepat_waktu')->count(),
                'total_terlambat' => collect($calendarData)->where('status', 'terlambat')->count(),
                'total_dinas_luar' => collect($calendarData)->where('status', 'dinas_luar')->count(),
                'total_izin' => collect($calendarData)->where('status', 'izin')->count(),
                'total_tidak_hadir' => collect($calendarData)->where('status', 'tidak_hadir')->count(),
            ]
        ];
    }

    protected function getIzinForDate($izins, Carbon $date): ?object
    {
        return $izins->first(function ($izin) use ($date) {
            $start = Carbon::parse($izin->tanggal_mulai);
            $end = Carbon::parse($izin->tanggal_akhir);
            return $date->between($start, $end);
        });
    }
}
