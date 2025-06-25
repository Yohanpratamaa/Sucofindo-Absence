<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Tab Users - Data Dasar
        'nama',
        'npp',
        'email',
        'password',
        'nik',
        'alamat',
        'status_pegawai',
        'status',
        'role_user',
        'nomor_handphone',

        // Tab Jabatan - Data langsung tanpa foreign key
        'jabatan_nama',
        'jabatan_tunjangan',

        // Tab Posisi - Data langsung tanpa foreign key
        'posisi_nama',
        'posisi_tunjangan',

        // Tab Pendidikan - Data JSON array
        'pendidikan_list',

        // Tab Emergency - Data JSON array
        'emergency_contacts',

        // Tab Fasilitas - Data JSON array
        'fasilitas_list',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pendidikan_list' => 'array',
        'emergency_contacts' => 'array',
        'fasilitas_list' => 'array',
        'jabatan_tunjangan' => 'decimal:2',
        'posisi_tunjangan' => 'decimal:2',
    ];

    protected $hidden = [
        'password',
    ];

    // Mutator untuk password
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Accessor untuk total nilai fasilitas dari JSON
    public function getTotalNilaiFasilitasAttribute()
    {
        if (!$this->fasilitas_list) {
            return 0;
        }

        return collect($this->fasilitas_list)->sum('nilai_fasilitas') ?? 0;
    }

    // Accessor untuk jumlah fasilitas
    public function getJumlahFasilitasAttribute()
    {
        if (!$this->fasilitas_list) {
            return 0;
        }

        return count($this->fasilitas_list);
    }

    // Accessor untuk pendidikan terakhir
    public function getPendidikanTerakhirAttribute()
    {
        if (!$this->pendidikan_list) {
            return null;
        }

        $pendidikan = collect($this->pendidikan_list)->sortByDesc('thn_lulus')->first();
        return $pendidikan;
    }

    // Accessor untuk kontak darurat utama
    public function getKontakDaruratUtamaAttribute()
    {
        if (!$this->emergency_contacts) {
            return null;
        }

        return collect($this->emergency_contacts)->first();
    }

    // Relasi dengan Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_nama', 'nama');
    }

    // Relasi dengan Posisi
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_nama', 'nama');
    }

    // Relasi dengan Overtime Assignments (sebagai user yang ditugaskan)
    public function overtimeAssignments()
    {
        return $this->hasMany(OvertimeAssignment::class, 'user_id');
    }

    // Relasi dengan Overtime Assignments (sebagai yang menugaskan)
    public function assignedOvertimes()
    {
        return $this->hasMany(OvertimeAssignment::class, 'assigned_by');
    }

    // Relasi dengan Overtime Assignments (sebagai yang approve)
    public function approvedOvertimes()
    {
        return $this->hasMany(OvertimeAssignment::class, 'approved_by');
    }

    // Relasi dengan Attendance (absensi karyawan)
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    // Method untuk mendapatkan total tunjangan
    public function getTotalTunjanganAttribute()
    {
        return ($this->jabatan_tunjangan ?? 0) + ($this->posisi_tunjangan ?? 0);
    }

    // Method untuk format nama lengkap
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ' (' . $this->npp . ')';
    }

    // Scope untuk filter berdasarkan status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeResign($query)
    {
        return $query->where('status', 'resign');
    }

    // Scope untuk filter berdasarkan role
    public function scopeEmployee($query)
    {
        return $query->where('role_user', 'employee');
    }

    public function scopeSuperAdmin($query)
    {
        return $query->where('role_user', 'super admin');
    }

    public function scopeKepalaBidang($query)
    {
        return $query->where('role_user', 'Kepala Bidang');
    }
}
