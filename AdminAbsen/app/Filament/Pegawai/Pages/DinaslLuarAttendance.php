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

class DinaslLuarAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Absensi Dinas Luar';
    protected static ?string $title = 'Absensi Dinas Luar';
    protected static string $view = 'filament.pegawai.pages.dinas-luar-attendance';

    public static function getNavigationGroup(): ?string
    {
        return 'Absensi Pegawai';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    // Hide from navigation (replaced by combined AttendancePage)
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $currentLocation = null;
    public $todayAttendance = null;
    public $canCheckInPagi = false;
    public $canCheckInSiang = false;
    public $canCheckOut = false;

    public function mount()
    {
        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();
    }

    protected function loadTodayAttendance()
    {
        $this->todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->where('attendance_type', 'Dinas Luar')
            ->first();
    }

    protected function calculateAttendanceStatus()
    {
        if (!$this->todayAttendance) {
            // Belum ada absensi hari ini, bisa check in pagi
            $this->canCheckInPagi = true;
            $this->canCheckInSiang = false;
            $this->canCheckOut = false;
        } else {
            // Sudah ada absensi hari ini
            $this->canCheckInPagi = false;

            // Bisa absen siang jika sudah check in pagi tapi belum absen siang DAN dalam waktu yang diizinkan
            $this->canCheckInSiang = $this->todayAttendance->check_in &&
                                   !$this->todayAttendance->absen_siang &&
                                   $this->isWithinSiangTimeWindow();

            // Bisa check out jika sudah absen siang tapi belum check out DAN dalam waktu yang diizinkan
            $this->canCheckOut = $this->todayAttendance->absen_siang &&
                               !$this->todayAttendance->check_out &&
                               $this->isWithinSoreTimeWindow();
        }
    }

    /**
     * Check if current time is within allowed Absensi Siang window (12:00-14:59)
     */
    protected function isWithinSiangTimeWindow(): bool
    {
        $currentTime = Carbon::now();
        $startTime = Carbon::today()->setTime(12, 0, 0); // 12:00
        $endTime = Carbon::today()->setTime(14, 59, 59); // 14:59:59

        return $currentTime->between($startTime, $endTime);
    }

    /**
     * Check if current time is within allowed Check Out window (>= 15:00)
     */
    protected function isWithinSoreTimeWindow(): bool
    {
        $currentTime = Carbon::now();
        $startTime = Carbon::today()->setTime(15, 0, 0); // 15:00

        return $currentTime->greaterThanOrEqualTo($startTime);
    }

    /**
     * Get time window information for frontend display
     */
    public function getTimeWindowInfo(): array
    {
        $currentTime = Carbon::now();

        return [
            'current_time' => $currentTime->format('H:i:s'),
            'siang_window' => [
                'start' => '12:00',
                'end' => '14:59',
                'is_active' => $this->isWithinSiangTimeWindow()
            ],
            'sore_window' => [
                'start' => '15:00',
                'end' => null,
                'is_active' => $this->isWithinSoreTimeWindow()
            ]
        ];
    }

    public function processCheckInPagi($photoData, $latitude, $longitude)
    {
        Log::info('processCheckInPagi called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'can_check_in_pagi' => $this->canCheckInPagi
        ]);

        if (!$this->canCheckInPagi) {
            Log::warning('Check in pagi not allowed', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda sudah melakukan absensi pagi hari ini.')
                ->send();
            return;
        }

        // Simpan foto selfie
        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in_pagi');
        Log::info('Photo save result', ['path' => $photoPath]);

        if (!$photoPath) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal menyimpan foto selfie.')
                ->send();
            return;
        }

        // Tentukan status kehadiran berdasarkan waktu absen pagi
        $currentTime = Carbon::now();
        $attendanceStatus = 'Tepat Waktu';
        $isLate = false;

        // Untuk dinas luar, batas waktu absen pagi adalah 08:30
        $lateThresholdPagi = Carbon::parse('08:30')
            ->setDate($currentTime->year, $currentTime->month, $currentTime->day);

        if ($currentTime->greaterThan($lateThresholdPagi)) {
            $attendanceStatus = 'Terlambat';
            $isLate = true;
        }

        // Buat record attendance
        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'office_working_hours_id' => null, // Dinas luar tidak terikat office schedule
            'check_in' => $currentTime,
            'latitude_absen_masuk' => $latitude,
            'longitude_absen_masuk' => $longitude,
            'picture_absen_masuk' => $photoPath,
            'attendance_type' => 'Dinas Luar',
        ]);

        Log::info('Attendance created for dinas luar', [
            'id' => $attendance->id,
            'status' => $attendanceStatus,
            'is_late' => $isLate
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        // Notifikasi dengan status kehadiran
        $notificationBody = $isLate
            ? 'Absensi pagi berhasil. Status: Terlambat. Jangan lupa absen siang dan sore.'
            : 'Absensi pagi berhasil. Status: Tepat Waktu. Jangan lupa absen siang dan sore.';

        Notification::make()
            ->success()
            ->title('Absensi Pagi Berhasil')
            ->body($notificationBody)
            ->send();
    }

    public function processCheckInSiang($photoData, $latitude, $longitude)
    {
        Log::info('processCheckInSiang called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'can_check_in_siang' => $this->canCheckInSiang
        ]);

        // Check if user can perform absensi siang (has checked in pagi and hasn't checked in siang)
        if (!$this->todayAttendance || !$this->todayAttendance->check_in || $this->todayAttendance->absen_siang) {
            Log::warning('Check in siang not allowed - prerequisites not met', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum absen pagi atau sudah absen siang hari ini.')
                ->send();
            return;
        }

        // Check time window (12:00 - 14:59)
        if (!$this->isWithinSiangTimeWindow()) {
            $currentTime = Carbon::now()->format('H:i');
            Log::warning('Check in siang attempted outside time window', [
                'user_id' => Auth::id(),
                'current_time' => $currentTime
            ]);

            Notification::make()
                ->danger()
                ->title('Waktu Absensi Siang Tidak Tepat')
                ->body("Absensi siang hanya dapat dilakukan antara 12:00 - 14:59. Waktu sekarang: {$currentTime}")
                ->send();
            return;
        }

        // Simpan foto selfie
        $photoPath = $this->savePhotoFromBase64($photoData, 'check_in_siang');
        Log::info('Photo save result for siang', ['path' => $photoPath]);

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
            'absen_siang' => Carbon::now(),
            'latitude_absen_siang' => $latitude,
            'longitude_absen_siang' => $longitude,
            'picture_absen_siang' => $photoPath,
        ]);

        Log::info('Attendance updated for absen siang', ['id' => $this->todayAttendance->id]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Notification::make()
            ->success()
            ->title('Absensi Siang Berhasil')
            ->body('Absensi siang berhasil dilakukan. Jangan lupa absen sore setelah jam 15:00.')
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

        // Check if user can perform check out (has checked in siang and hasn't checked out)
        if (!$this->todayAttendance || !$this->todayAttendance->absen_siang || $this->todayAttendance->check_out) {
            Log::warning('Check out not allowed - prerequisites not met', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum absen pagi dan siang, atau sudah absen sore hari ini.')
                ->send();
            return;
        }

        // Check time window (>= 15:00)
        if (!$this->isWithinSoreTimeWindow()) {
            $currentTime = Carbon::now()->format('H:i');
            Log::warning('Check out attempted outside time window', [
                'user_id' => Auth::id(),
                'current_time' => $currentTime
            ]);

            Notification::make()
                ->danger()
                ->title('Waktu Absensi Sore Belum Tepat')
                ->body("Absensi sore hanya dapat dilakukan mulai jam 15:00. Waktu sekarang: {$currentTime}")
                ->send();
            return;
        }

        // Simpan foto selfie
        $photoPath = $this->savePhotoFromBase64($photoData, 'check_out_sore');
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
            ->title('Absensi Sore Berhasil')
            ->body('Semua absensi hari ini telah selesai. Terima kasih.')
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

    public function getCurrentAttendanceStatus()
    {
        if (!$this->todayAttendance) {
            return [
                'status' => 'Belum Absen',
                'color' => 'gray',
                'check_in' => null,
                'absen_siang' => null,
                'check_out' => null
            ];
        }

        return [
            'status' => $this->todayAttendance->status_kehadiran,
            'color' => $this->todayAttendance->status_color,
            'check_in' => $this->todayAttendance->check_in_formatted,
            'absen_siang' => $this->todayAttendance->absen_siang_formatted,
            'check_out' => $this->todayAttendance->check_out_formatted
        ];
    }

    public function getAttendanceProgress()
    {
        if (!$this->todayAttendance) {
            return [
                'pagi' => false,
                'siang' => false,
                'sore' => false,
                'percentage' => 0
            ];
        }

        $pagi = !is_null($this->todayAttendance->check_in);
        $siang = !is_null($this->todayAttendance->absen_siang);
        $sore = !is_null($this->todayAttendance->check_out);

        $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
        $percentage = round(($completed / 3) * 100);

        return [
            'pagi' => $pagi,
            'siang' => $siang,
            'sore' => $sore,
            'percentage' => $percentage
        ];
    }

    public function getViewData(): array
    {
        return [
            'canCheckInPagi' => $this->canCheckInPagi,
            'canCheckInSiang' => $this->canCheckInSiang,
            'canCheckOut' => $this->canCheckOut,
            'todayAttendance' => $this->todayAttendance,
        ];
    }

    /**
     * Get the current action for the UI
     */
    public function getCurrentAction(): ?string
    {
        if ($this->canCheckInPagi) {
            return 'pagi';
        } elseif ($this->canCheckInSiang) {
            return 'siang';
        } elseif ($this->canCheckOut) {
            return 'sore';
        }

        return null;
    }

    /**
     * Get the action title for the UI
     */
    public function getActionTitle(): string
    {
        $currentAction = $this->getCurrentAction();

        return match($currentAction) {
            'pagi' => 'Absensi Pagi - Dinas Luar',
            'siang' => 'Absensi Siang - Dinas Luar',
            'sore' => 'Absensi Sore - Dinas Luar',
            default => 'Tidak Ada Aksi Tersedia'
        };
    }

    /**
     * Debug method to check current state
     */
    public function debugCurrentState(): array
    {
        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        return [
            'canCheckInPagi' => $this->canCheckInPagi,
            'canCheckInSiang' => $this->canCheckInSiang,
            'canCheckOut' => $this->canCheckOut,
            'currentAction' => $this->getCurrentAction(),
            'actionTitle' => $this->getActionTitle(),
            'todayAttendance' => $this->todayAttendance ? 'exists' : 'null',
            'timeWindowInfo' => $this->getTimeWindowInfo(),
        ];
    }

    // Test method untuk debugging time windows
    public function testTimeWindows()
    {
        $currentTime = Carbon::now();
        $timeInfo = $this->getTimeWindowInfo();

        Log::info('Time window test', [
            'current_time' => $currentTime->format('H:i:s'),
            'siang_window_active' => $timeInfo['siang_window']['is_active'],
            'sore_window_active' => $timeInfo['sore_window']['is_active'],
            'is_within_siang' => $this->isWithinSiangTimeWindow(),
            'is_within_sore' => $this->isWithinSoreTimeWindow(),
        ]);

        $message = "Waktu saat ini: {$currentTime->format('H:i:s')}\n";
        $message .= "Siang window (12:00-14:59): " . ($timeInfo['siang_window']['is_active'] ? 'AKTIF' : 'TIDAK AKTIF') . "\n";
        $message .= "Sore window (>=15:00): " . ($timeInfo['sore_window']['is_active'] ? 'AKTIF' : 'TIDAK AKTIF');

        Notification::make()
            ->info()
            ->title('Test Time Windows')
            ->body($message)
            ->send();

        return $timeInfo;
    }
}
