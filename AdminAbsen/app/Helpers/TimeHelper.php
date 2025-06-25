<?php

if (!function_exists('jakartaNow')) {
    /**
     * Get current time in Jakarta timezone
     */
    function jakartaNow()
    {
        return \Carbon\Carbon::now('Asia/Jakarta');
    }
}

if (!function_exists('jakartaToday')) {
    /**
     * Get today's date in Jakarta timezone
     */
    function jakartaToday()
    {
        return \Carbon\Carbon::today('Asia/Jakarta');
    }
}

if (!function_exists('formatJakartaTime')) {
    /**
     * Format time with Jakarta timezone
     */
    function formatJakartaTime($format = 'Y-m-d H:i:s')
    {
        return \Carbon\Carbon::now('Asia/Jakarta')->format($format);
    }
}

if (!function_exists('parseJakartaTime')) {
    /**
     * Parse time with Jakarta timezone
     */
    function parseJakartaTime($time)
    {
        return \Carbon\Carbon::parse($time, 'Asia/Jakarta');
    }
}

if (!function_exists('isWorkingDay')) {
    /**
     * Check if current day is working day (Monday to Friday)
     */
    function isWorkingDay($date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date, 'Asia/Jakarta') : jakartaNow();
        return $date->isWeekday();
    }
}

if (!function_exists('getWorkingHoursStatus')) {
    /**
     * Get working hours status
     */
    function getWorkingHoursStatus()
    {
        $now = jakartaNow();
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

if (!function_exists('getAttendanceStatus')) {
    /**
     * Get attendance status based on check-in time
     */
    function getAttendanceStatus($checkInTime)
    {
        if (!$checkInTime) {
            return 'Tidak Hadir';
        }
        
        $checkIn = parseJakartaTime($checkInTime);
        $standardTime = parseJakartaTime('08:00:00');
        
        return $checkIn->greaterThan($standardTime) ? 'Terlambat' : 'Tepat Waktu';
    }
}

if (!function_exists('getRealTimeStats')) {
    /**
     * Get real-time statistics
     */
    function getRealTimeStats()
    {
        return [
            'current_time' => formatJakartaTime('H:i:s'),
            'current_date' => formatJakartaTime('d M Y'),
            'is_working_day' => isWorkingDay(),
            'working_hours_status' => getWorkingHoursStatus(),
            'total_employees' => \App\Models\Pegawai::count(),
            'today_attendance' => \App\Models\Attendance::today()->count(),
            'this_month_attendance' => \App\Models\Attendance::thisMonth()->count(),
        ];
    }
}
