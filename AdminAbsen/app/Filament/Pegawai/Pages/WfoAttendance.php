<?php

namespace App\Filament\Pegawai\Pages;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\OfficeSchedule;
use App\Models\Pegawai;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WfoAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Absensi WFO';
    protected static ?string $title = 'Absensi Work From Office';
    protected static string $view = 'filament.pegawai.pages.wfo-attendance';

    public static function getNavigationGroup(): ?string
    {
        return 'Absensi Pegawai';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    // Hide from navigation (replaced by combined AttendancePage)
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $currentLocation = null;
    public $nearestOffice = null;
    public $isWithinRadius = false;
    public $todayAttendance = null;
    public $canCheckIn = false;
    public $canCheckOut = false;

    public function mount()
    {
        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();
    }

    public function updatedCurrentLocation()
    {
        // Auto-refresh location status when location changes
        $this->dispatch('location-updated');
    }

    protected function loadTodayAttendance()
    {
        $this->todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->first(); // Load any attendance type for today
    }

    protected function calculateAttendanceStatus()
    {
        if (!$this->todayAttendance) {
            $this->canCheckIn = true;
            $this->canCheckOut = false;
        } else {
            // Jika sudah ada attendance hari ini (any type), tidak bisa check in lagi
            $this->canCheckIn = false;
            // Bisa check out jika ada check_in tapi belum check_out
            $this->canCheckOut = $this->todayAttendance->check_in && !$this->todayAttendance->check_out;
        }
    }

    public function checkLocation($latitude, $longitude)
    {
        $this->currentLocation = [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];

        // Cari kantor terdekat dan cek apakah dalam radius
        $offices = Office::all();
        $nearestDistance = PHP_FLOAT_MAX;
        $nearestOffice = null;

        foreach ($offices as $office) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $office->latitude,
                $office->longitude
            );

            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestOffice = $office;
            }
        }

        $this->nearestOffice = $nearestOffice;
        $this->isWithinRadius = $nearestOffice && $nearestDistance <= $nearestOffice->radius;

        // Return status for frontend
        return [
            'success' => true,
            'isWithinRadius' => $this->isWithinRadius,
            'nearestOffice' => $nearestOffice ? $nearestOffice->name : null,
            'distance' => round($nearestDistance, 2),
            'allowedRadius' => $nearestOffice ? $nearestOffice->radius : 0
        ];
    }

    public function processCheckIn($photoData, $latitude, $longitude)
    {
        Log::info('processCheckIn called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'can_check_in' => $this->canCheckIn
        ]);

        if (!$this->canCheckIn) {
            Log::warning('Check in not allowed', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda sudah melakukan absensi hari ini.')
                ->send();
            return;
        }

        // Validasi lokasi
        $locationCheck = $this->checkLocation($latitude, $longitude);
        Log::info('Location check result', $locationCheck);

        if (!$locationCheck['isWithinRadius']) {
            Notification::make()
                ->danger()
                ->title('Lokasi Tidak Valid')
                ->body('Anda berada di luar radius kantor yang diizinkan.')
                ->send();
            return;
        }

        // Simpan foto selfie
        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in');
        Log::info('Photo save result', ['path' => $photoPath]);

        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        // Buat record attendance dengan office schedule
        $currentTime = Carbon::now();
        $currentDay = strtolower($currentTime->format('l')); // monday, tuesday, etc.

        // Cari jadwal kantor untuk hari ini
        $officeSchedule = OfficeSchedule::getScheduleForDay($this->nearestOffice->id, $currentDay);

        // Tentukan status kehadiran
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
            // Jika tidak ada jadwal, gunakan default 08:00
            $defaultStartTime = Carbon::parse('08:00')
                ->setDate($currentTime->year, $currentTime->month, $currentTime->day);

            if ($currentTime->greaterThan($defaultStartTime)) {
                $attendanceStatus = 'Terlambat';
                $isLate = true;
            }
        }

        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'office_working_hours_id' => $officeSchedule ? $officeSchedule->id : null,
            'check_in' => $currentTime,
            'latitude_absen_masuk' => $latitude,
            'longitude_absen_masuk' => $longitude,
            'picture_absen_masuk' => $photoPath,
            'attendance_type' => 'WFO',
        ]);

        Log::info('Attendance created', [
            'id' => $attendance->id,
            'status' => $attendanceStatus,
            'is_late' => $isLate,
            'office_schedule_id' => $officeSchedule ? $officeSchedule->id : null
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        // Notifikasi dengan status kehadiran
        $notificationBody = $isLate
            ? 'Anda telah berhasil melakukan check in WFO. Status: Terlambat'
            : 'Anda telah berhasil melakukan check in WFO. Status: Tepat Waktu';

        Notification::make()
            ->success()
            ->title('Check In Berhasil')
            ->body($notificationBody)
            ->send();
    }

    public function processCheckOut($photoData, $latitude, $longitude)
    {
        Log::info('processCheckOut called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'can_check_out' => $this->canCheckOut
        ]);

        if (!$this->canCheckOut) {
            Log::warning('Check out not allowed', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum check in atau sudah check out hari ini.')
                ->send();
            return;
        }

        // Validasi lokasi
        $locationCheck = $this->checkLocation($latitude, $longitude);
        Log::info('Location check result for check out', $locationCheck);

        if (!$locationCheck['isWithinRadius']) {
            Notification::make()
                ->danger()
                ->title('Lokasi Tidak Valid')
                ->body('Anda berada di luar radius kantor yang diizinkan.')
                ->send();
            return;
        }

        // Simpan foto selfie
        $photoPath = $this->savePhotoFromBase64($photoData, 'check_out');
        Log::info('Photo save result for check out', ['path' => $photoPath]);

        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        // Update record attendance
        $this->todayAttendance->update([
            'check_out' => Carbon::now(),
            'latitude_absen_pulang' => $latitude,
            'longitude_absen_pulang' => $longitude,
            'picture_absen_pulang' => $photoPath,
        ]);

        Log::info('Attendance updated for check out', ['id' => $this->todayAttendance->id]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Check Out Berhasil')
            ->body('Anda telah berhasil melakukan check out WFO.')
            ->send();
    }

    protected function savePhotoFromBase64($base64Data, $type)
    {
        try {
            Log::info('Starting to save photo for type: ' . $type);
            Log::info('Base64 data length: ' . strlen($base64Data));

            // Remove data:image/jpeg;base64, prefix if exists
            $base64Data = preg_replace('#^data:image/[^;]+;base64,#', '', $base64Data);
            Log::info('Base64 data after cleaning prefix: ' . strlen($base64Data));

            // Decode base64
            $imageData = base64_decode($base64Data);

            if ($imageData === false) {
                Log::error('Failed to decode base64 data');
                return false;
            }

            Log::info('Image data decoded successfully, size: ' . strlen($imageData) . ' bytes');

            // Ensure attendance directory exists
            $attendanceDir = 'attendance';
            if (!Storage::disk('public')->exists($attendanceDir)) {
                Storage::disk('public')->makeDirectory($attendanceDir);
                Log::info('Created attendance directory');
            }

            // Generate filename
            $filename = $attendanceDir . '/' . Auth::id() . '_' . $type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.jpg';
            Log::info('Generated filename: ' . $filename);

            // Save to storage
            $saved = Storage::disk('public')->put($filename, $imageData);
            Log::info('Save result: ' . ($saved ? 'success' : 'failed'));

            if ($saved) {
                // Verify file exists
                $exists = Storage::disk('public')->exists($filename);
                Log::info('File verification: ' . ($exists ? 'exists' : 'not found'));

                // Check file size
                if ($exists) {
                    $size = Storage::disk('public')->size($filename);
                    Log::info('Saved file size: ' . $size . ' bytes');
                }
            }

            return $saved ? $filename : false;
        } catch (\Exception $e) {
            Log::error('Error saving attendance photo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    public function getOffices()
    {
        return Office::all(['id', 'name', 'latitude', 'longitude', 'radius'])->map(function ($office) {
            return [
                'id' => $office->id,
                'name' => $office->name,
                'latitude' => (float) $office->latitude,
                'longitude' => (float) $office->longitude,
                'radius' => (float) $office->radius,
            ];
        });
    }

    public function getCurrentAttendanceStatus()
    {
        if (!$this->todayAttendance) {
            return [
                'status' => 'Belum Absen',
                'color' => 'gray',
                'check_in' => null,
                'check_out' => null
            ];
        }

        return [
            'status' => $this->todayAttendance->status_kehadiran,
            'color' => $this->todayAttendance->status_color,
            'check_in' => $this->todayAttendance->check_in_formatted,
            'check_out' => $this->todayAttendance->check_out_formatted
        ];
    }

    public function getScheduledStartTime()
    {
        if (!$this->nearestOffice) {
            return '08:00'; // Default time
        }

        $currentDay = strtolower(Carbon::now()->format('l'));
        $schedule = OfficeSchedule::getScheduleForDay($this->nearestOffice->id, $currentDay);

        if ($schedule && $schedule->start_time) {
            return Carbon::parse($schedule->start_time)->format('H:i');
        }

        return '08:00'; // Default time
    }

    // Test method untuk debugging foto
    public function testPhotoSave($photoData)
    {
        Log::info('testPhotoSave called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'photo_starts_with' => substr($photoData, 0, 50)
        ]);

        $photoPath = $this->savePhotoFromBase64($photoData, 'test');

        if ($photoPath) {
            Notification::make()
                ->success()
                ->title('Test Foto Berhasil')
                ->body('Foto berhasil disimpan di: ' . $photoPath)
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Test Foto Gagal')
                ->body('Gagal menyimpan foto test.')
                ->send();
        }

        return $photoPath;
    }
}
