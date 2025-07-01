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
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 2;

    public $currentLocation = null;
    public $todayAttendance = null;
    public $canCheckInPagi = false;
    public $canCheckInSiang = false;
    public $canCheckOut = false;

    public function mount()
    {
        Log::info('DinaslLuarAttendance mounted', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Unknown'
        ]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        Log::info('Mount completed', [
            'canCheckInPagi' => $this->canCheckInPagi,
            'canCheckInSiang' => $this->canCheckInSiang,
            'canCheckOut' => $this->canCheckOut,
            'todayAttendance' => $this->todayAttendance ? $this->todayAttendance->id : null
        ]);
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
        Log::info('calculateAttendanceStatus called', [
            'user_id' => Auth::id(),
            'today_attendance' => $this->todayAttendance ? $this->todayAttendance->id : null
        ]);

        if (!$this->todayAttendance) {
            // Belum ada absensi hari ini, bisa check in pagi
            $this->canCheckInPagi = true;
            $this->canCheckInSiang = false;
            $this->canCheckOut = false;
            Log::info('No attendance today, can check in pagi');
        } else {
            // Sudah ada absensi hari ini
            $this->canCheckInPagi = is_null($this->todayAttendance->check_in);

            // Bisa absen siang jika sudah check in pagi tapi belum absen siang
            $this->canCheckInSiang = !is_null($this->todayAttendance->check_in) && is_null($this->todayAttendance->absen_siang);

            // Bisa check out jika sudah absen siang tapi belum check out
            // ATAU jika sudah check in pagi tapi belum check out (untuk fleksibilitas)
            $this->canCheckOut = !is_null($this->todayAttendance->check_in) && is_null($this->todayAttendance->check_out);

            Log::info('Attendance status calculated', [
                'check_in' => $this->todayAttendance->check_in,
                'absen_siang' => $this->todayAttendance->absen_siang,
                'check_out' => $this->todayAttendance->check_out,
                'canCheckInPagi' => $this->canCheckInPagi,
                'canCheckInSiang' => $this->canCheckInSiang,
                'canCheckOut' => $this->canCheckOut
            ]);
        }
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

        // Dispatch event for auto-refresh
        $this->dispatch('attendance-submitted');
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

        if (!$this->canCheckInSiang) {
            Log::warning('Check in siang not allowed', ['user_id' => Auth::id()]);
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Anda belum absen pagi atau sudah absen siang hari ini.')
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
            ->body('Absensi siang berhasil dilakukan. Jangan lupa absen sore.')
            ->send();

        // Dispatch event for auto-refresh
        $this->dispatch('attendance-submitted');
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
                ->body('Anda belum absen pagi atau sudah absen sore hari ini.')
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
        $updateData = [
            'check_out' => Carbon::now(),
            'latitude_absen_pulang' => $latitude,
            'longitude_absen_pulang' => $longitude,
            'picture_absen_pulang' => $photoPath,
        ];

        // Jika belum absen siang, isi juga waktu absen siang dengan waktu yang sama
        if (is_null($this->todayAttendance->absen_siang)) {
            $updateData['absen_siang'] = Carbon::now();
            $updateData['latitude_absen_siang'] = $latitude;
            $updateData['longitude_absen_siang'] = $longitude;
            $updateData['picture_absen_siang'] = $photoPath; // Use same photo for flexibility
        }

        $this->todayAttendance->update($updateData);

        Log::info('Attendance updated for check out', ['id' => $this->todayAttendance->id]);

        $this->loadTodayAttendance();
        $this->calculateAttendanceStatus();

        $message = is_null($this->todayAttendance->absen_siang)
            ? 'Absensi sore berhasil (otomatis mengisi absen siang). Semua absensi hari ini telah selesai.'
            : 'Absensi sore berhasil. Semua absensi hari ini telah selesai.';

        Notification::make()
            ->success()
            ->title('Absensi Sore Berhasil')
            ->body($message)
            ->send();

        // Dispatch event for auto-refresh
        $this->dispatch('attendance-submitted');
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

    // Test method untuk debugging foto
    public function testPhotoSave($photoData)
    {
        Log::info('testPhotoSave called', [
            'user_id' => Auth::id(),
            'photo_data_length' => strlen($photoData),
            'photo_starts_with' => substr($photoData, 0, 50)
        ]);

        $photoPath = $this->savePhotoFromBase64($photoData, 'test_dinas_luar');

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
