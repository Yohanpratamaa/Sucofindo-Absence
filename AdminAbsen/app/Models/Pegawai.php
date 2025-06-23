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
        // Akses
        'username',
        'password',
        'role',
        'is_active',
        'last_login',

        // Informasi Umum
        'nip',
        'nama',
        'email',
        'phone',
        'gender',
        'tanggal_lahir',
        'tempat_lahir',
        'agama',
        'status_perkawinan',
        'kewarganegaraan',
        'alamat',
        'jabatan',
        'divisi',
        'tanggal_masuk',
        'status_karyawan',

        // Pendidikan
        'pendidikan_terakhir',
        'nama_sekolah',
        'jurusan',
        'tahun_lulus',
        'ipk_nilai',
        'akreditasi',
        'sertifikat_keahlian',

        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'emergency_contact_phone_2',
        'emergency_contact_address',
        'emergency_contact_name_2',
        'emergency_contact_relation_2',
        'emergency_contact_phone_alt',

        // Jaminan
        'no_bpjs_kesehatan',
        'no_bpjs_ketenagakerjaan',
        'no_ktp',
        'no_npwp',
        'no_rekening',
        'nama_bank',
        'nama_pemilik_rekening',
        'jenis_rekening',
        'asuransi_nama',
        'asuransi_no_polis',
        'asuransi_mulai',
        'asuransi_berakhir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'ipk_nilai' => 'decimal:2',
        'asuransi_mulai' => 'date',
        'asuransi_berakhir' => 'date',
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
}
