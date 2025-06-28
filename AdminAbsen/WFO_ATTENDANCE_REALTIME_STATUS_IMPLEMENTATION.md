# WFO Attendance Real-Time Status Implementation

## Overview
This document describes the implementation of real-time attendance status logic for WFO (Work From Office) attendance in the Employee panel.

## Key Features Implemented

### 1. Real-Time Status Calculation
- **Attendance time is recorded in real time** when employee checks in
- **Status is automatically determined** based on office schedule at the moment of check-in
- **Status options**: "Tepat Waktu" (On Time) or "Terlambat" (Late)

### 2. Office Schedule Integration
- System uses office schedule data to determine the correct check-in time for each day
- Each office can have different schedules for different days of the week
- Falls back to default 08:00 AM if no specific schedule is found

### 3. Status Display
- Status is shown immediately after check-in in the notification
- Status is displayed in the dashboard widget
- Status is shown in the WFO attendance page header
- Status includes color coding (Green for "Tepat Waktu", Orange for "Terlambat")

## Technical Implementation

### 1. WfoAttendance.php - processCheckIn() Method
```php
// Get current time and day
$currentTime = Carbon::now();
$currentDay = strtolower($currentTime->format('l')); // monday, tuesday, etc.

// Find office schedule for today
$officeSchedule = OfficeSchedule::getScheduleForDay($this->nearestOffice->id, $currentDay);

// Determine attendance status
$attendanceStatus = 'Tepat Waktu';
$isLate = false;

if ($officeSchedule && $officeSchedule->start_time) {
    $scheduledStartTime = Carbon::parse($officeSchedule->start_time)
        ->setDate($currentTime->year, $currentTime->month, $currentTime->day);

    if ($currentTime->greaterThan($scheduledStartTime)) {
        $attendanceStatus = 'Terlambat';
        $isLate = true;
    }
} else {
    // If no schedule, use default 08:00
    $defaultStartTime = Carbon::parse('08:00')
        ->setDate($currentTime->year, $currentTime->month, $currentTime->day);

    if ($currentTime->greaterThan($defaultStartTime)) {
        $attendanceStatus = 'Terlambat';
        $isLate = true;
    }
}

// Create attendance record with office schedule reference
$attendance = Attendance::create([
    'user_id' => Auth::id(),
    'office_working_hours_id' => $officeSchedule ? $officeSchedule->id : null,
    'check_in' => $currentTime,
    'latitude_absen_masuk' => $latitude,
    'longitude_absen_masuk' => $longitude,
    'picture_absen_masuk' => $photoPath,
    'attendance_type' => 'WFO',
]);
```

### 2. Attendance Model - Dynamic Status Calculation
The Attendance model has a `getStatusKehadiranAttribute()` accessor that dynamically calculates the status:

```php
public function getStatusKehadiranAttribute()
{
    if (!$this->check_in) {
        return 'Tidak Hadir';
    }

    // Get office schedule for the check-in day
    $checkInDate = Carbon::parse($this->check_in);
    $dayOfWeek = strtolower($checkInDate->format('l'));

    $schedule = null;
    if ($this->officeSchedule && $this->officeSchedule->office_id) {
        $schedule = OfficeSchedule::getScheduleForDay($this->officeSchedule->office_id, $dayOfWeek);
    }

    // Default to 08:00 if no schedule found
    $jamMasukStandar = $schedule && $schedule->start_time
        ? Carbon::parse($schedule->start_time)
        : Carbon::parse('08:00');

    $jamMasukStandar->setDate(
        $checkInDate->year,
        $checkInDate->month,
        $checkInDate->day
    );

    // Check if late
    if ($checkInDate->greaterThan($jamMasukStandar)) {
        return 'Terlambat';
    }

    return 'Tepat Waktu';
}
```

### 3. Widget Integration
The WfoAttendanceStatusWidget displays the real-time status:

```php
public function getAttendanceStatus()
{
    $todayAttendance = $this->getTodayAttendance();

    if (!$todayAttendance) {
        return [
            'status' => 'Belum Absen',
            'color' => 'gray',
            'check_in' => null,
            'check_out' => null
        ];
    }

    return [
        'status' => $todayAttendance->status_kehadiran,
        'color' => $todayAttendance->status_color,
        'check_in' => $todayAttendance->check_in_formatted,
        'check_out' => $todayAttendance->check_out_formatted,
        'type' => $todayAttendance->attendance_type
    ];
}
```

## Database Schema Changes

### 1. office_working_hours_id Nullable
Made `office_working_hours_id` nullable to handle cases where no office schedule is found:

```php
// Migration: 2025_06_28_103405_make_office_working_hours_id_nullable_in_attendances_table.php
Schema::table('attendances', function (Blueprint $table) {
    $table->unsignedBigInteger('office_working_hours_id')->nullable()->change();
});
```

## User Experience

### 1. Check-In Process
1. Employee accesses WFO Attendance page
2. Camera and location validation occur
3. Employee takes selfie photo
4. Upon check-in submission:
   - Current time is recorded in real-time
   - System checks office schedule for current day
   - Status is determined immediately (Tepat Waktu or Terlambat)
   - Notification shows the result with status

### 2. Status Display
- **Dashboard Widget**: Shows current attendance status with color coding
- **Attendance Page**: Shows status in header section
- **Notification**: Immediate feedback after check-in with status

### 3. Real-Time Behavior
- **No delays**: Status is calculated at the exact moment of check-in
- **Schedule-aware**: Uses actual office schedule, not hardcoded times
- **Fallback handling**: Uses 08:00 AM default if no schedule is configured
- **Accurate timestamps**: All times are stored with full datetime precision

## Benefits

1. **Real-Time Accuracy**: Status reflects the exact moment of attendance
2. **Schedule Flexibility**: Supports different schedules for different offices and days
3. **Immediate Feedback**: Employee knows their status immediately
4. **Consistent Reporting**: Status calculation is consistent across all views
5. **Admin Visibility**: Administrators can see accurate late attendance data

## Testing Scenarios

### Scenario 1: On-Time Check-In
- Office schedule: 08:00 AM
- Employee check-in: 07:55 AM
- Expected result: Status = "Tepat Waktu", Color = Green

### Scenario 2: Late Check-In
- Office schedule: 08:00 AM
- Employee check-in: 08:15 AM
- Expected result: Status = "Terlambat", Color = Orange

### Scenario 3: No Schedule Configured
- No office schedule found
- Default time: 08:00 AM
- Employee check-in: 08:30 AM
- Expected result: Status = "Terlambat", Color = Orange

### Scenario 4: Different Day Schedule
- Monday schedule: 08:00 AM
- Tuesday schedule: 09:00 AM
- Employee check-in Tuesday: 08:30 AM
- Expected result: Status = "Tepat Waktu", Color = Green

## Files Modified

### PHP Files
- `app/Filament/Pegawai/Pages/WfoAttendance.php` - Main check-in logic
- `app/Filament/Pegawai/Widgets/WfoAttendanceStatusWidget.php` - Status display
- `app/Models/Attendance.php` - Status calculation (already existed)

### Blade Templates
- `resources/views/filament/pegawai/pages/wfo-attendance.blade.php` - Status display in header
- `resources/views/filament/pegawai/widgets/wfo-attendance-status-widget.blade.php` - Widget updates

### Database Migrations
- `2025_06_28_103405_make_office_working_hours_id_nullable_in_attendances_table.php` - Schema update

## Conclusion

The WFO attendance system now accurately records attendance status in real-time based on office schedules. The status "Terlambat" (Late) is automatically assigned when an employee checks in after the scheduled start time, and "Tepat Waktu" (On Time) is assigned for on-time attendance. The system provides immediate feedback to employees and maintains consistency across all administrative views.
