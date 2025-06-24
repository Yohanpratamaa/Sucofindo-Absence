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
        'id_jaminan',
        'id_jabatan',
        'id_posisi',
        'id_nomor_emergency',
        'id_pendidikan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    // public function jaminan()
    // {
    //     return $this->belongsTo(Jaminan::class, 'id_jaminan');
    // }

    // public function jabatan()
    // {
    //     return $this->belongsTo(Jabatan::class, 'id_jabatan');
    // }

    // public function posisi()
    // {
    //     return $this->belongsTo(Posisi::class, 'id_posisi');
    // }

    // public function nomorEmergency()
    // {
    //     return $this->belongsTo(NomorEmergency::class, 'id_nomor_emergency');
    // }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'id_pendidikan');
    }
}
