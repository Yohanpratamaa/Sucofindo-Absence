<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'office_working_hours_id',
        'check_in',
        'longitude_absen_masuk',
        'latitude_absen_masuk',
        'picture_absen_masuk',
        'absen_siang',
        'longitude_absen_siang',
        'latitude_absen_siang',
        'picture_absen_siang',
        'check_out',
        'longitude_absen_pulang',
        'latitude_absen_pulang',
        'picture_absen_pulang',
        'overtime',
        'attendance_type',
        'izin_id',
        'status_kehadiran',
        'keterangan_izin',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'absen_siang' => 'datetime',
        'check_out' => 'datetime',
        'longitude_absen_masuk' => 'decimal:8',
        'latitude_absen_masuk' => 'decimal:8',
        'longitude_absen_siang' => 'decimal:8',
        'latitude_absen_siang' => 'decimal:8',
        'longitude_absen_pulang' => 'decimal:8',
        'latitude_absen_pulang' => 'decimal:8',
        'overtime' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan Pegawai
    public function user()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }

    // Relasi dengan Office Schedule melalui office_working_hours_id
    public function officeSchedule()
    {
        return $this->belongsTo(OfficeSchedule::class, 'office_working_hours_id');
    }

    // Relasi dengan Izin
    public function izin()
    {
        return $this->belongsTo(Izin::class, 'izin_id');
    }

    // Accessor untuk format tanggal
    public function getTanggalAbsenAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    // Accessor untuk format check in
    public function getCheckInFormattedAttribute()
    {
        return $this->check_in ? $this->check_in->format('H:i') : '-';
    }

    // Accessor untuk format absen siang
    public function getAbsenSiangFormattedAttribute()
    {
        return $this->absen_siang ? $this->absen_siang->format('H:i') : '-';
    }

    // Accessor untuk format check out
    public function getCheckOutFormattedAttribute()
    {
        return $this->check_out ? $this->check_out->format('H:i') : '-';
    }

    // Accessor untuk durasi kerja
    public function getDurasiKerjaAttribute()
    {
        // Jika status adalah "Tidak Absensi", return durasi khusus
        if ($this->getStatusKehadiranAttribute() === 'Tidak Absensi') {
            // Jika tidak ada check_in sama sekali
            if (!$this->check_in) {
                return '0 jam 0 menit';
            }

            // Jika check_in tapi tidak ada check_out
            if (!$this->check_out) {
                return 'Belum checkout';
            }

            // Hitung durasi dari check_in hingga check_out (meskipun check-in terlalu sore)
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $totalMinutes = $checkIn->diffInMinutes($checkOut);

            // Jika ada absen siang, kurangi 1 jam untuk istirahat
            if ($this->absen_siang) {
                $totalMinutes = max(0, $totalMinutes - 60);
            }

            if ($totalMinutes <= 0) {
                return '0 jam 0 menit';
            }

            $hours = intval($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            if ($hours > 0 && $minutes > 0) {
                return $hours . ' jam ' . $minutes . ' menit';
            } elseif ($hours > 0) {
                return $hours . ' jam';
            } else {
                return $minutes . ' menit';
            }
        }

        // Logic normal untuk status lainnya
        if (!$this->check_in || !$this->check_out) {
            return '-';
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);

        // Hitung total durasi dalam menit (pastikan urutan parameter benar)
        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        // Jika ada absen siang, kurangi 1 jam untuk istirahat
        if ($this->absen_siang) {
            $totalMinutes = max(0, $totalMinutes - 60);
        }

        if ($totalMinutes <= 0) {
            return '0 jam 0 menit';
        }

        $hours = intval($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }

    // Accessor untuk format overtime
    public function getOvertimeFormattedAttribute()
    {
        if ($this->overtime <= 0) {
            return '-';
        }

        $hours = intval($this->overtime / 60);
        $minutes = $this->overtime % 60;

        return $hours . ' jam ' . $minutes . ' menit';
    }

    // Accessor untuk status kehadiran berdasarkan jadwal kantor
    public function getStatusKehadiranAttribute()
    {
        // Cek jika ada status_kehadiran yang sudah di-set manual (untuk izin)
        if (isset($this->attributes['status_kehadiran']) && !empty($this->attributes['status_kehadiran'])) {
            return $this->attributes['status_kehadiran'];
        }

        // Jika tidak ada check_in sama sekali, maka "Tidak Absensi"
        if (!$this->check_in) {
            return 'Tidak Absensi';
        }

        // Ambil jadwal kantor berdasarkan hari absensi
        $checkInDate = Carbon::parse($this->check_in);
        $dayOfWeek = strtolower($checkInDate->format('l')); // monday, tuesday, etc.

        // Cek apakah check-in dilakukan pada jam 17:00 atau setelahnya
        $eveningTime = Carbon::parse($this->check_in)->setTime(17, 0, 0);

        if ($checkInDate->greaterThanOrEqualTo($eveningTime)) {
            return 'Tidak Absensi';
        }

        // Cari jadwal kantor untuk hari tersebut
        $schedule = null;
        if ($this->officeSchedule && $this->officeSchedule->office_id) {
            $schedule = OfficeSchedule::getScheduleForDay($this->officeSchedule->office_id, $dayOfWeek);
        }

        // Jika tidak ada jadwal ditemukan, gunakan default 08:00
        $jamMasukStandar = $schedule && $schedule->start_time
            ? Carbon::parse($schedule->start_time)
            : Carbon::parse('08:00');

        // Set tanggal yang sama untuk perbandingan waktu
        $jamMasukStandar->setDate(
            $checkInDate->year,
            $checkInDate->month,
            $checkInDate->day
        );

        // Langsung cek apakah terlambat atau tepat waktu (tanpa toleransi)
        if ($checkInDate->greaterThan($jamMasukStandar)) {
            return 'Terlambat';
        }

        return 'Tepat Waktu';
    }

    // Accessor untuk warna status
    public function getStatusColorAttribute()
    {
        return match($this->status_kehadiran) {
            'Tepat Waktu' => 'success',
            'Terlambat' => 'warning',
            'Tidak Hadir' => 'danger',
            'Tidak Absensi' => 'danger',
            'Izin' => 'info',
            'Sakit' => 'warning',
            'Cuti' => 'primary',
            default => 'gray'
        };
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk filter berdasarkan tipe absensi
    public function scopeByType($query, $type)
    {
        return $query->where('attendance_type', $type);
    }

    // Scope untuk absensi hari ini (dengan timezone Jakarta)
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::now('Asia/Jakarta')->toDateString());
    }

    // Scope untuk absensi bulan ini (dengan timezone Jakarta)
    public function scopeThisMonth($query)
    {
        $now = Carbon::now('Asia/Jakarta');
        return $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
    }

    // Method untuk cek apakah sudah check in
    public function hasCheckedIn()
    {
        return !is_null($this->check_in);
    }

    // Method untuk cek apakah sudah check out
    public function hasCheckedOut()
    {
        return !is_null($this->check_out);
    }

    // Method untuk cek apakah sudah absen siang
    public function hasAbsenSiang()
    {
        return !is_null($this->absen_siang);
    }

    // Method untuk mendapatkan informasi detail keterlambatan
    public function getKeterlambatanDetailAttribute()
    {
        if (!$this->check_in) {
            return 'Tidak hadir';
        }

        // Ambil jadwal kantor berdasarkan hari absensi
        $checkInDate = Carbon::parse($this->check_in);
        $dayOfWeek = strtolower($checkInDate->format('l'));

        $schedule = null;
        if ($this->officeSchedule && $this->officeSchedule->office_id) {
            $schedule = OfficeSchedule::getScheduleForDay($this->officeSchedule->office_id, $dayOfWeek);
        }

        $jamMasukStandar = $schedule && $schedule->start_time
            ? Carbon::parse($schedule->start_time)
            : Carbon::parse('08:00');

        $jamMasukStandar->setDate(
            $checkInDate->year,
            $checkInDate->month,
            $checkInDate->day
        );

        if ($checkInDate->lessThanOrEqualTo($jamMasukStandar)) {
            return 'Tepat waktu';
        }

        $selisihMenit = $jamMasukStandar->diffInMinutes($checkInDate);
        return "Terlambat {$selisihMenit} menit";
    }

    // Method untuk mendapatkan jam masuk standar berdasarkan jadwal
    public function getJamMasukStandarAttribute()
    {
        $checkInDate = Carbon::parse($this->check_in ?? $this->created_at);
        $dayOfWeek = strtolower($checkInDate->format('l'));

        $schedule = null;
        if ($this->officeSchedule && $this->officeSchedule->office_id) {
            $schedule = OfficeSchedule::getScheduleForDay($this->officeSchedule->office_id, $dayOfWeek);
        }

        return $schedule && $schedule->start_time
            ? Carbon::parse($schedule->start_time)->format('H:i')
            : '08:00';
    }

    // Method untuk mendapatkan jam keluar standar berdasarkan jadwal
    public function getJamKeluarStandarAttribute()
    {
        $checkInDate = Carbon::parse($this->check_in ?? $this->created_at);
        $dayOfWeek = strtolower($checkInDate->format('l'));

        $schedule = null;
        if ($this->officeSchedule && $this->officeSchedule->office_id) {
            $schedule = OfficeSchedule::getScheduleForDay($this->officeSchedule->office_id, $dayOfWeek);
        }

        return $schedule && $schedule->end_time
            ? Carbon::parse($schedule->end_time)->format('H:i')
            : '17:00';
    }

    // Method untuk mengecek apakah attendance type memerlukan absen siang
    public function requiresAbsenSiang()
    {
        return $this->attendance_type === 'Dinas Luar';
    }

    // Method untuk mengecek validitas absensi berdasarkan type
    public function isValidAttendance()
    {
        if ($this->attendance_type === 'WFO') {
            // WFO: Cukup check in dan check out
            return $this->check_in && $this->check_out;
        } else {
            // Dinas Luar: Harus ada check in, absen siang, dan check out
            return $this->check_in && $this->absen_siang && $this->check_out;
        }
    }

    // Method untuk mendapatkan status absensi berdasarkan kelengkapan
    public function getKelengkapanAbsensiAttribute()
    {
        if ($this->attendance_type === 'WFO') {
            $completed = 0;
            $total = 2; // check in + check out

            if ($this->check_in) $completed++;
            if ($this->check_out) $completed++;

            return [
                'completed' => $completed,
                'total' => $total,
                'percentage' => ($completed / $total) * 100,
                'status' => $completed === $total ? 'Lengkap' : 'Belum Lengkap'
            ];
        } else {
            $completed = 0;
            $total = 3; // check in + absen siang + check out

            if ($this->check_in) $completed++;
            if ($this->absen_siang) $completed++;
            if ($this->check_out) $completed++;

            return [
                'completed' => $completed,
                'total' => $total,
                'percentage' => ($completed / $total) * 100,
                'status' => $completed === $total ? 'Lengkap' : 'Belum Lengkap'
            ];
        }
    }

    // Method untuk mendapatkan deskripsi requirement absensi
    public function getAbsensiRequirementAttribute()
    {
        if ($this->attendance_type === 'WFO') {
            return 'Check In + Check Out (Lokasi: Kantor)';
        } else {
            return 'Check In + Absen Siang + Check Out (Lokasi: Fleksibel)';
        }
    }

    // Accessor untuk URL gambar check in dengan validasi
    public function getPictureAbsenMasukUrlAttribute()
    {
        if (!$this->picture_absen_masuk) {
            return asset('images/no-image.png');
        }

        // Check if file exists
        if (Storage::disk('public')->exists($this->picture_absen_masuk)) {
            return asset('storage/' . $this->picture_absen_masuk);
        }

        // Log missing file
        Log::warning('Attendance image missing', [
            'id' => $this->id,
            'path' => $this->picture_absen_masuk,
            'field' => 'picture_absen_masuk'
        ]);

        return asset('images/no-image.png');
    }

    // Accessor untuk URL gambar check out dengan validasi
    public function getPictureAbsenPulangUrlAttribute()
    {
        if (!$this->picture_absen_pulang) {
            return asset('images/no-image.png');
        }

        // Check if file exists
        if (Storage::disk('public')->exists($this->picture_absen_pulang)) {
            return asset('storage/' . $this->picture_absen_pulang);
        }

        // Log missing file
        Log::warning('Attendance image missing', [
            'id' => $this->id,
            'path' => $this->picture_absen_pulang,
            'field' => 'picture_absen_pulang'
        ]);

        return asset('images/no-image.png');
    }

    // Accessor untuk URL gambar absen siang dengan validasi
    public function getPictureAbsenSiangUrlAttribute()
    {
        if (!$this->picture_absen_siang) {
            return asset('images/no-image.png');
        }

        // Check if file exists
        if (Storage::disk('public')->exists($this->picture_absen_siang)) {
            return asset('storage/' . $this->picture_absen_siang);
        }

        // Log missing file
        Log::warning('Attendance image missing', [
            'id' => $this->id,
            'path' => $this->picture_absen_siang,
            'field' => 'picture_absen_siang'
        ]);

        return asset('images/no-image.png');
    }

    // Method untuk mengecek apakah gambar ada
    public function hasValidImage($type = 'check_in')
    {
        $fieldMap = [
            'check_in' => 'picture_absen_masuk',
            'check_out' => 'picture_absen_pulang',
            'absen_siang' => 'picture_absen_siang'
        ];

        $field = $fieldMap[$type] ?? 'picture_absen_masuk';

        if (!$this->$field) {
            return false;
        }

        return Storage::disk('public')->exists($this->$field);
    }
}
