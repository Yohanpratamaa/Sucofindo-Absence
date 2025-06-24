<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama_jaminan',
        'no_jaminan',
        'transport',
        'overtime_id',
        'payroll',
    ];

    protected $casts = [
        'transport' => 'integer',
        'overtime_id' => 'integer',
        'payroll' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan Pegawai
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_fasilitas');
    }
}
