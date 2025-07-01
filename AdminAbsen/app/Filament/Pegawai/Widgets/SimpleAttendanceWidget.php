<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SimpleAttendanceWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.simple-attendance-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getData(): array
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentMonth = Carbon::now()->format('Y-m');

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        // This month stats - simplified without status_kehadiran column
        $monthlyAttendances = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->get();

        // Calculate stats manually since status_kehadiran is an accessor
        $monthlyStats = (object) [
            'total_hari_hadir' => $monthlyAttendances->count(),
            'tepat_waktu' => $monthlyAttendances->filter(function($attendance) {
                return $attendance->status_kehadiran === 'Tepat Waktu';
            })->count(),
            'terlambat' => $monthlyAttendances->filter(function($attendance) {
                return $attendance->status_kehadiran === 'Terlambat';
            })->count(),
            'wfo' => $monthlyAttendances->filter(function($attendance) {
                return $attendance->attendance_type === 'WFO';
            })->count(),
            'dinas_luar' => $monthlyAttendances->filter(function($attendance) {
                return $attendance->attendance_type === 'Dinas Luar';
            })->count(),
        ];

        // Recent attendance (last 5)
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'today_attendance' => $todayAttendance,
            'monthly_stats' => $monthlyStats,
            'recent_attendance' => $recentAttendance,
            'current_time' => Carbon::now(),
            'current_month_name' => Carbon::now()->isoFormat('MMMM Y'),
        ];
    }
}
