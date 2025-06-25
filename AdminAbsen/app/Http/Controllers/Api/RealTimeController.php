<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RealTimeController extends Controller
{
    public function getStats()
    {
        $now = Carbon::now('Asia/Jakarta');
        
        $stats = [
            'timestamp' => $now->timestamp,
            'current_time' => $now->format('H:i:s'),
            'current_date' => $now->format('d M Y'),
            'current_datetime' => $now->format('d M Y, H:i:s'),
            'is_working_day' => $now->isWeekday(),
            'working_hours_status' => $this->getWorkingHoursStatus($now),
            'total_employees' => Pegawai::count(),
            'today_attendance' => Attendance::today()->count(),
            'this_month_attendance' => Attendance::thisMonth()->count(),
            'late_today' => Attendance::today()->whereTime('check_in', '>', '08:00:00')->count(),
        ];
        
        return response()->json($stats);
    }
    
    public function getRecentAttendance()
    {
        $recent = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_name' => $attendance->user->nama ?? 'N/A',
                    'user_npp' => $attendance->user->npp ?? 'N/A',
                    'check_in' => $attendance->check_in_formatted,
                    'status' => $attendance->status_kehadiran,
                    'attendance_type' => $attendance->attendance_type,                    'created_at' => $attendance->created_at->format('d M Y, H:i'),
                    'time_ago' => Carbon::parse($attendance->created_at)->diffForHumans(),
                ];
            });
            
        return response()->json($recent);
    }
    
    public function getDashboardData()
    {
        $now = Carbon::now('Asia/Jakarta');
        $totalPegawai = Pegawai::count();
        $absensiHariIni = Attendance::today()->count();
        $absensiBulanIni = Attendance::thisMonth()->count();
        
        $data = [
            'timestamp' => $now->timestamp,
            'current_time' => $now->format('H:i:s'),
            'current_date' => $now->format('d M Y'),
            'stats' => [
                'total_employees' => $totalPegawai,
                'today_attendance' => $absensiHariIni,
                'this_month_attendance' => $absensiBulanIni,
                'attendance_percentage' => $totalPegawai > 0 ? round(($absensiHariIni / $totalPegawai) * 100, 1) : 0,
                'late_today' => Attendance::today()->whereTime('check_in', '>', '08:00:00')->count(),
            ],
            'recent_attendance' => Attendance::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($attendance) {
                    return [
                        'user_name' => $attendance->user->nama ?? 'N/A',
                        'status' => $attendance->status_kehadiran,
                        'time_ago' => Carbon::parse($attendance->created_at)->diffForHumans(),
                    ];
                }),
        ];
        
        return response()->json($data);
    }
    
    private function getWorkingHoursStatus(Carbon $now): string
    {
        $hour = $now->hour;
        
        if ($hour >= 8 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 13 && $hour < 17) {
            return 'afternoon';
        } else {
            return 'non-working';
        }
    }
}
