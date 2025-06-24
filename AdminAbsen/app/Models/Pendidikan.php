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
        'thn_masuk',
        'thn_lulus',
        'ipk',
        'gelar',
    ];

    protected $casts = [
        'thn_masuk' => 'date',
        'thn_lulus' => 'date',
        'ipk' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // public function pegawai()
    // {
    //     return $this->belongsTo(Pegawai::class, 'user_id');
    // }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_pendidikan');
    }
}
