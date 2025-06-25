<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    // Relasi dengan Office Working Hours (jika ada tabel tersebut - di comment dulu)
    // public function officeWorkingHours()
    // {
    //     return $this->belongsTo(OfficeWorkingHours::class, 'office_working_hours_id');
    // }

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

    // Accessor untuk status kehadiran
    public function getStatusKehadiranAttribute()
    {
        if (!$this->check_in) {
            return 'Tidak Hadir';
        }

        $checkIn = Carbon::parse($this->check_in);
        $jamMasukStandar = Carbon::parse('08:00');

        if ($checkIn->greaterThan($jamMasukStandar)) {
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

    // Scope untuk absensi hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Scope untuk absensi bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
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
}
