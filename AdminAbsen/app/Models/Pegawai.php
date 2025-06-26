<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Pegawai extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, Notifiable;

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

        // Remember token for authentication
        'remember_token',
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
        'remember_token',
    ];

    // Implement required methods for Authenticatable
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // Method required by Filament for user display
    public function getUserName(): string
    {
        // Ensure we always return a non-null string
        try {
            // Method 1: Use accessor methods (recommended)
            $nama = $this->getAttribute('nama');
            $email = $this->getAttribute('email');
            $npp = $this->getAttribute('npp');

            // Return the first non-empty value
            $result = $nama ?: ($email ?: ($npp ?: 'User'));

            // Ensure result is string and not null
            return is_string($result) && !empty($result) ? $result : 'User';
        } catch (\Exception $e) {
            // Log error for debugging
            \Illuminate\Support\Facades\Log::error('getUserName error: ' . $e->getMessage());
            // Fallback if any error occurs
            return 'User';
        }
    }

    // Alternative method for Filament name display
    public function getFilamentName(): string
    {
        return $this->getUserName();
    }

    // Override getNameAttribute to ensure compatibility with Filament
    public function getNameAttribute(): string
    {
        return $this->getUserName();
    }

    // Ensure email is always available as fallback
    public function getEmailForPasswordReset(): string
    {
        return $this->getAttribute('email') ?: 'noreply@example.com';
    }

    // Method required by Filament for panel access control
    public function canAccessPanel(Panel $panel): bool
    {
        // Ensure user has role_user attribute
        if (!$this->role_user) {
            return false;
        }

        // Allow access based on role and panel
        switch ($panel->getId()) {
            case 'admin':
                return in_array($this->role_user, ['super admin', 'admin']);
            case 'pegawai':
                return $this->role_user === 'employee';
            case 'kepala-bidang':
                return $this->role_user === 'Kepala Bidang';
            default:
                return false;
        }
    }

    // Additional Filament user methods for better compatibility
    public function getFilamentAvatarUrl(): ?string
    {
        return null; // You can return avatar URL if you have one
    }

    // Override the name attribute for better Filament integration
    protected function getNameColumn(): string
    {
        return 'nama';
    }

    // Mutator untuk password
    public function setPasswordAttribute($value)
    {
        if ($value) {
            // Check if password is already hashed (starts with $2y$ for bcrypt)
            if (preg_match('/^\$2[ayb]\$.{56}$/', $value)) {
                // Already hashed, use as-is
                $this->attributes['password'] = $value;
            } else {
                // Plain text, hash it
                $this->attributes['password'] = Hash::make($value);
            }
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
