<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikans';

    protected $fillable = [
        'user_id',
        'jenjang',
        'nama_univ',
        'jurusan',
        'fakultas_program_studi',
        'thn_masuk',
        'thn_lulus',
        'ipk',
        'gelar',
        'ijazah_path',
    ];

    protected $casts = [
        'thn_masuk' => 'date',
        'thn_lulus' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }
}
