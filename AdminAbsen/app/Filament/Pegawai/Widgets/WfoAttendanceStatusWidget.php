<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WfoAttendanceStatusWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.wfo-attendance-status-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getTodayAttendance()
    {
        return Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->where('attendance_type', 'WFO')
            ->first();
    }

    public function getCanCheckIn()
    {
        $todayAttendance = $this->getTodayAttendance();
        return !$todayAttendance;
    }

    public function getCanCheckOut()
    {
        $todayAttendance = $this->getTodayAttendance();
        return $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out;
    }

    public function getAttendanceStats()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return [
            'total_hadir' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->whereNotNull('check_in')
                ->count(),
            
            'total_terlambat' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->whereTime('check_in', '>', '08:00:00')
                ->count(),
            
            'total_wfo' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('attendance_type', 'WFO')
                ->count(),
        ];
    }
}
