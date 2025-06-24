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
        // Users - sesuai ERD
        'nama',
        'npp',
        'email',
        'password',
        'nik',
        'alamat',
        'status_pegawai',
        'status',
        'role_user',

        // Foreign Keys
        'id_fasilitas',
        'id_jabatan',
        'id_posisi',
        'id_nomor_emergency',
        'id_pendidikan',

        // Data JSON untuk repeater
        'pendidikan_list',
        'emergency_contacts',
        'fasilitas_list', // Tambahan untuk fasilitas list

        // Data fasilitas langsung (untuk backward compatibility)
        'nama_jaminan',
        'no_jaminan',
        'transport',
        'overtime_rate',
        'payroll',
        'keterangan_fasilitas',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pendidikan_list' => 'array',
        'emergency_contacts' => 'array',
        'fasilitas_list' => 'array', // Cast ke array
        'transport' => 'integer',
        'overtime_rate' => 'integer',
        'payroll' => 'integer',
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

    // Relationships berdasarkan Foreign Keys di ERD
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi');
    }

    public function nomorEmergency()
    {
        return $this->belongsTo(NomorEmergency::class, 'id_nomor_emergency');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'id_pendidikan');
    }

    // Accessor untuk menghitung total nilai fasilitas
    public function getTotalNilaiFasilitasAttribute()
    {
        if (!$this->fasilitas_list) {
            return 0;
        }

        return collect($this->fasilitas_list)->sum('nilai_fasilitas');
    }

    // Accessor untuk mendapatkan fasilitas aktif
    public function getFasilitasAktifAttribute()
    {
        if (!$this->fasilitas_list) {
            return collect([]);
        }

        return collect($this->fasilitas_list)->where('status_fasilitas', 'aktif');
    }
}
