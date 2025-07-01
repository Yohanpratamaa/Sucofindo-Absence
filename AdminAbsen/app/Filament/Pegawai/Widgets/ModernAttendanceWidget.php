<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ModernAttendanceWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.modern-attendance-widget';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentMonth = Carbon::now()->format('Y-m');

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        // This month stats
        $monthlyStats = Attendance::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN TIME(check_in) <= "08:00:00" THEN 1 ELSE 0 END) as on_time,
                SUM(CASE WHEN TIME(check_in) > "08:00:00" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN attendance_type = "WFO" THEN 1 ELSE 0 END) as wfo_count,
                SUM(CASE WHEN attendance_type = "Dinas Luar" THEN 1 ELSE 0 END) as dinas_luar_count
            ')
            ->first();

        // This week attendance
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weeklyAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get();

        // Recent attendance (last 5 days)
        $recentAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'today_attendance' => $todayAttendance,
            'monthly_stats' => $monthlyStats,
            'weekly_attendance' => $weeklyAttendance,
            'recent_attendance' => $recentAttendance,
            'current_time' => Carbon::now(),
            'current_month_name' => Carbon::now()->isoFormat('MMMM Y'),
        ];
    }
}
