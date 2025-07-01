<?php

namespace App\Filament\Pegawai\Pages;

use App\Models\Attendance;
use App\Models\Office;
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

class AttendancePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $title = 'Absensi Pegawai';
    protected static string $view = 'filament.pegawai.pages.attendance-simple';

    public static function getNavigationGroup(): ?string
    {
        return 'Absensi Pegawai';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    // Public properties for Livewire
    public $attendanceType = 'WFO'; // Default to WFO
    public $currentLocation = null;
    public $todayAttendance = null;
    public $canCheckInPagi = false;
    public $canCheckInSiang = false;
    public $canCheckOut = false;
    public $canCheckIn = false; // For WFO

    public function mount()
    {
        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();
    }

    protected function loadTodayAttendance()
    {
        // First, check if there's any attendance today regardless of type
        $anyAttendanceToday = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($anyAttendanceToday) {
            // If there's attendance today, lock to that attendance type
            $this->attendanceType = $anyAttendanceToday->attendance_type;
            $this->todayAttendance = $anyAttendanceToday;
        } else {
            // If no attendance today, load based on selected type
            $this->todayAttendance = Attendance::where('user_id', Auth::id())
                ->whereDate('created_at', Carbon::today())
                ->where('attendance_type', $this->attendanceType)
                ->first();
        }
    }

    protected function calculateAttendanceStatus()
    {
        if ($this->attendanceType === 'Dinas Luar') {
            $this->calculateDinasLuarStatus();
        } else {
            $this->calculateWfoStatus();
        }
    }

    protected function calculateDinasLuarStatus()
    {
        if (!$this->todayAttendance) {
            $this->canCheckInPagi = true;
            $this->canCheckInSiang = false;
            $this->canCheckOut = false;
            $this->canCheckIn = false;
        } else {
            $this->canCheckInPagi = false;
            $this->canCheckInSiang = $this->todayAttendance->check_in &&
                                   !$this->todayAttendance->absen_siang &&
                                   $this->isWithinSiangTimeWindow();
            $this->canCheckOut = $this->todayAttendance->absen_siang &&
                               !$this->todayAttendance->check_out &&
                               $this->isWithinSoreTimeWindow();
            $this->canCheckIn = false;
        }
    }

    protected function calculateWfoStatus()
    {
        if (!$this->todayAttendance) {
            $this->canCheckIn = true;
            $this->canCheckOut = false;
            $this->canCheckInPagi = false;
            $this->canCheckInSiang = false;
        } else {
            $this->canCheckIn = false;
            // WFO check-out hanya bisa setelah jam 15:00 dan sudah check-in
            $this->canCheckOut = $this->todayAttendance->check_in && 
                               !$this->todayAttendance->check_out &&
                               $this->isWithinSoreTimeWindow(); // Gunakan validasi jam 15:00 yang sama
            $this->canCheckInPagi = false;
            $this->canCheckInSiang = false;
        }
    }

    /**
     * Check if user can change attendance type
     */
    public function canChangeAttendanceType(): bool
    {
        // Check if there's any attendance today regardless of type
        $anyAttendanceToday = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->exists();

        return !$anyAttendanceToday;
    }

    /**
     * Get the locked attendance type if any
     */
    public function getLockedAttendanceType(): ?string
    {
        $anyAttendanceToday = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->first();

        return $anyAttendanceToday ? $anyAttendanceToday->attendance_type : null;
    }

    /**
     * Change attendance type (WFO or Dinas Luar)
     */
    public function updatedAttendanceType($value)
    {
        // Check if user can change attendance type
        if (!$this->canChangeAttendanceType()) {
            $lockedType = $this->getLockedAttendanceType();

            // Revert to locked type
            $this->attendanceType = $lockedType;

            Notification::make()
                ->warning()
                ->title('Tidak Dapat Mengubah Tipe Absensi')
                ->body("Anda sudah melakukan absensi {$lockedType} hari ini. Tidak dapat mengubah ke tipe absensi lain.")
                ->send();

            return;
        }

        $this->attendanceType = $value;
        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->info()
            ->title('Tipe Absensi Diubah')
            ->body("Absensi diubah ke: {$value}")
            ->send();
    }

    /**
     * Time window methods untuk validasi jam absensi
     * Berlaku untuk WFO dan Dinas Luar
     */
    protected function isWithinSiangTimeWindow(): bool
    {
        $currentTime = Carbon::now();
        $startTime = Carbon::today()->setTime(12, 0, 0);
        $endTime = Carbon::today()->setTime(14, 59, 59);
        return $currentTime->between($startTime, $endTime);
    }

    protected function isWithinSoreTimeWindow(): bool
    {
        $currentTime = Carbon::now();
        $startTime = Carbon::today()->setTime(15, 0, 0);
        return $currentTime->greaterThanOrEqualTo($startTime);
    }

    /**
     * Get current action based on attendance state
     */
    public function getCurrentAction()
    {
        if ($this->attendanceType === 'WFO') {
            if ($this->canCheckIn) {
                return 'check_in';
            } elseif ($this->canCheckOut) {
                return 'check_out';
            }
        } else { // Dinas Luar
            if ($this->canCheckInPagi) {
                return 'check_in_pagi';
            } elseif ($this->canCheckInSiang) {
                return 'check_in_siang';
            } elseif ($this->canCheckOut) {
                return 'check_out';
            }
        }
        return null;
    }

    /**
     * Get action title for UI
     */
    public function getActionTitle()
    {
        $action = $this->getCurrentAction();
        
        switch ($action) {
            case 'check_in':
                return 'Check In WFO';
            case 'check_out':
                return 'Check Out';
            case 'check_in_pagi':
                return 'Absensi Pagi (Dinas Luar)';
            case 'check_in_siang':
                return 'Absensi Siang (Dinas Luar)';
            default:
                return 'Absensi Selesai';
        }
    }

    /**
     * Get time window information
     */
    public function getTimeWindowInfo()
    {
        $now = Carbon::now();
        
        return [
            'current_time' => $now->format('H:i'),
            'siang_window' => [
                'start' => '12:00',
                'end' => '14:59',
                'is_active' => $this->isWithinSiangTimeWindow()
            ],
            'sore_window' => [
                'start' => '15:00',
                'end' => '23:59',
                'is_active' => $this->isWithinSoreTimeWindow()
            ]
        ];
    }

    /**
     * Get attendance progress information
     */
    public function getAttendanceProgress()
    {
        $progress = [
            'completed' => 0,
            'total' => 0,
            'check_in' => false,
            'check_in_siang' => false,
            'check_out' => false
        ];

        if ($this->attendanceType === 'WFO') {
            $progress['total'] = 2; // Check in and check out
            
            if ($this->todayAttendance) {
                if ($this->todayAttendance->check_in) {
                    $progress['completed']++;
                    $progress['check_in'] = true;
                }
                if ($this->todayAttendance->check_out) {
                    $progress['completed']++;
                    $progress['check_out'] = true;
                }
            }
        } else { // Dinas Luar
            $progress['total'] = 3; // Pagi, siang, sore
            
            if ($this->todayAttendance) {
                if ($this->todayAttendance->check_in) {
                    $progress['completed']++;
                    $progress['check_in'] = true;
                }
                if ($this->todayAttendance->absen_siang) {
                    $progress['completed']++;
                    $progress['check_in_siang'] = true;
                }
                if ($this->todayAttendance->check_out) {
                    $progress['completed']++;
                    $progress['check_out'] = true;
                }
            }
        }

        return $progress;
    }

    /**
     * WFO Attendance Methods
     */
    public function processCheckIn($photoData, $latitude, $longitude)
    {
        if (!$this->canCheckIn) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda sudah melakukan check in hari ini.')
                ->send();
            return;
        }

        // Check location for WFO
        if (!$this->isWithinOfficeRadius($latitude, $longitude)) {
            Notification::make()
                ->danger()
                ->title('Lokasi Tidak Valid')
                ->body('Anda harus berada dalam radius kantor untuk melakukan absensi WFO.')
                ->send();
            return;
        }

        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in_wfo');
        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        $currentTime = Carbon::now();
        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'office_working_hours_id' => $this->getNearestOfficeId($latitude, $longitude),
            'check_in' => $currentTime,
            'latitude_absen_masuk' => $latitude,
            'longitude_absen_masuk' => $longitude,
            'picture_absen_masuk' => $photoPath,
            'attendance_type' => 'WFO',
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Check In Berhasil')
            ->body('Check in WFO berhasil dilakukan.')
            ->send();
    }

    public function processCheckOut($photoData, $latitude, $longitude)
    {
        if ($this->attendanceType === 'WFO') {
            $this->processWfoCheckOut($photoData, $latitude, $longitude);
        } else {
            $this->processDinasLuarCheckOut($photoData, $latitude, $longitude);
        }
    }

    protected function processWfoCheckOut($photoData, $latitude, $longitude)
    {
        if (!$this->canCheckOut) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum check in atau sudah check out hari ini.')
                ->send();
            return;
        }

        // Validasi jam 15:00 untuk check-out WFO (sama seperti dinas luar)
        if (!$this->isWithinSoreTimeWindow()) {
            $currentTime = Carbon::now()->format('H:i');
            Notification::make()
                ->danger()
                ->title('Waktu Check-Out Belum Tepat')
                ->body("Check-out WFO hanya dapat dilakukan mulai jam 15:00. Waktu sekarang: {$currentTime}")
                ->send();
            return;
        }

        $photoPath = $this->savePhotoFromBase64($photoData, 'check_out_wfo');
        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        $this->todayAttendance->update([
            'check_out' => Carbon::now(),
            'latitude_absen_pulang' => $latitude,
            'longitude_absen_pulang' => $longitude,
            'picture_absen_pulang' => $photoPath,
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Check Out Berhasil')
            ->body('Check out WFO berhasil dilakukan.')
            ->send();
    }

    /**
     * Dinas Luar Attendance Methods
     */
    public function processCheckInPagi($photoData, $latitude, $longitude)
    {
        if (!$this->canCheckInPagi) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda sudah melakukan absensi pagi hari ini.')
                ->send();
            return;
        }

        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in_pagi');
        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        $currentTime = Carbon::now();
        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'office_working_hours_id' => null,
            'check_in' => $currentTime,
            'latitude_absen_masuk' => $latitude,
            'longitude_absen_masuk' => $longitude,
            'picture_absen_masuk' => $photoPath,
            'attendance_type' => 'Dinas Luar',
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Absensi Pagi Berhasil')
            ->body('Absensi pagi dinas luar berhasil. Jangan lupa absen siang dan sore.')
            ->send();
    }

    public function processCheckInSiang($photoData, $latitude, $longitude)
    {
        if (!$this->canCheckInSiang) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum absen pagi atau sudah absen siang hari ini.')
                ->send();
            return;
        }

        if (!$this->isWithinSiangTimeWindow()) {
            $currentTime = Carbon::now()->format('H:i');
            Notification::make()
                ->danger()
                ->title('Waktu Absensi Siang Tidak Tepat')
                ->body("Absensi siang hanya dapat dilakukan antara 12:00 - 14:59. Waktu sekarang: {$currentTime}")
                ->send();
            return;
        }

        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in_siang');
        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        $this->todayAttendance->update([
            'absen_siang' => Carbon::now(),
            'latitude_absen_siang' => $latitude,
            'longitude_absen_siang' => $longitude,
            'picture_absen_siang' => $photoPath,
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Absensi Siang Berhasil')
            ->body('Absensi siang berhasil dilakukan. Jangan lupa absen sore setelah jam 15:00.')
            ->send();
    }

    protected function processDinasLuarCheckOut($photoData, $latitude, $longitude)
    {
        if (!$this->canCheckOut) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum absen pagi dan siang, atau sudah absen sore hari ini.')
                ->send();
            return;
        }

        if (!$this->isWithinSoreTimeWindow()) {
            $currentTime = Carbon::now()->format('H:i');
            Notification::make()
                ->danger()
                ->title('Waktu Absensi Sore Belum Tepat')
                ->body("Absensi sore hanya dapat dilakukan mulai jam 15:00. Waktu sekarang: {$currentTime}")
                ->send();
            return;
        }

        $photoPath = $this->savePhotoFromBase64($photoData, 'check_out_sore');
        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        $this->todayAttendance->update([
            'check_out' => Carbon::now(),
            'latitude_absen_pulang' => $latitude,
            'longitude_absen_pulang' => $longitude,
            'picture_absen_pulang' => $photoPath,
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Absensi Sore Berhasil')
            ->body('Semua absensi dinas luar hari ini telah selesai.')
            ->send();
    }

    /**
     * Helper Methods
     */
    protected function getOffices(): array
    {
        return Office::select('id', 'nama_kantor', 'latitude', 'longitude', 'radius_meter')
            ->get()
            ->map(function ($office) {
                return [
                    'id' => $office->id,
                    'name' => $office->nama_kantor,
                    'latitude' => (float) $office->latitude,
                    'longitude' => (float) $office->longitude,
                    'radius' => (float) $office->radius_meter,
                ];
            })
            ->toArray();
    }

    protected function isWithinOfficeRadius($latitude, $longitude): bool
    {
        $offices = $this->getOffices();

        foreach ($offices as $office) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $office['latitude'],
                $office['longitude']
            );

            if ($distance <= $office['radius']) {
                return true;
            }
        }

        return false;
    }

    protected function getNearestOfficeId($latitude, $longitude): ?int
    {
        $offices = $this->getOffices();
        $nearestOffice = null;
        $nearestDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $office['latitude'],
                $office['longitude']
            );

            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestOffice = $office;
            }
        }

        return $nearestOffice ? $nearestOffice['id'] : null;
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371000; // Earth's radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    protected function savePhotoFromBase64($base64Data, $type)
    {
        try {
            $base64Data = preg_replace('#^data:image/[^;]+;base64,#', '', $base64Data);
            $imageData = base64_decode($base64Data);

            if ($imageData === false) {
                return false;
            }

            $attendanceDir = 'attendance';
            if (!Storage::disk('public')->exists($attendanceDir)) {
                Storage::disk('public')->makeDirectory($attendanceDir);
            }

            $filename = $attendanceDir . '/' . Auth::id() . '_' . $type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.jpg';
            $saved = Storage::disk('public')->put($filename, $imageData);

            return $saved ? $filename : false;
        } catch (\Exception $e) {
            Log::error('Error saving attendance photo: ' . $e->getMessage());
            return false;
        }
    }

    public function testPhotoSave($photoData)
    {
        $photoPath = $this->savePhotoFromBase64($photoData, 'test_combined');

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
