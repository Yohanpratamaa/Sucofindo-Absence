<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DinaslLuarAttendanceStatusWidget extends Widget
{
    protected static string $view = 'filament.pegawai.widgets.dinas-luar-attendance-status-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getTodayAttendance()
    {
        return Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->where('attendance_type', 'Dinas Luar')
            ->first();
    }

    public function getAttendanceStatus()
    {
        $todayAttendance = $this->getTodayAttendance();
        
        if (!$todayAttendance) {
            return [
                'status' => 'Belum Absen',
                'color' => 'gray',
                'check_in' => null,
                'absen_siang' => null,
                'check_out' => null
            ];
        }

        return [
            'status' => $todayAttendance->status_kehadiran,
            'color' => $todayAttendance->status_color,
            'check_in' => $todayAttendance->check_in_formatted,
            'absen_siang' => $todayAttendance->absen_siang_formatted,
            'check_out' => $todayAttendance->check_out_formatted,
            'type' => $todayAttendance->attendance_type
        ];
    }

    public function getAttendanceProgress()
    {
        $todayAttendance = $this->getTodayAttendance();
        
        if (!$todayAttendance) {
            return [
                'pagi' => false,
                'siang' => false,
                'sore' => false,
                'percentage' => 0
            ];
        }

        $pagi = !is_null($todayAttendance->check_in);
        $siang = !is_null($todayAttendance->absen_siang);
        $sore = !is_null($todayAttendance->check_out);

        $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
        $percentage = round(($completed / 3) * 100);

        return [
            'pagi' => $pagi,
            'siang' => $siang,
            'sore' => $sore,
            'percentage' => $percentage
        ];
    }

    public function getCanCheckInPagi()
    {
        $todayAttendance = $this->getTodayAttendance();
        return !$todayAttendance;
    }

    public function getCanCheckInSiang()
    {
        $todayAttendance = $this->getTodayAttendance();
        return $todayAttendance && $todayAttendance->check_in && !$todayAttendance->absen_siang;
    }

    public function getCanCheckOut()
    {
        $todayAttendance = $this->getTodayAttendance();
        return $todayAttendance && $todayAttendance->absen_siang && !$todayAttendance->check_out;
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
                ->where('attendance_type', 'Dinas Luar')
                ->whereNotNull('check_in')
                ->count(),
            
            'total_terlambat' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('attendance_type', 'Dinas Luar')
                ->whereNotNull('check_in')
                ->get()
                ->filter(function ($attendance) {
                    return $attendance->status_kehadiran === 'Terlambat';
                })
                ->count(),
            
            'total_lengkap' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('attendance_type', 'Dinas Luar')
                ->whereNotNull('check_in')
                ->whereNotNull('absen_siang')
                ->whereNotNull('check_out')
                ->count(),
        ];
    }
}
