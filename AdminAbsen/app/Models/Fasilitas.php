<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'user_id',
        'nama_jaminan',
        'no_jaminan',
        'jenis_fasilitas',
        'provider',
        'nilai_fasilitas',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status_fasilitas',
        'keterangan',
        'dokumen_path',
        'transport',
        'overtime_id',
        'payroll',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'nilai_fasilitas' => 'integer',
        'transport' => 'integer',
        'overtime_id' => 'integer',
        'payroll' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }
}
