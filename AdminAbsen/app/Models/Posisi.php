<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;

    protected $table = 'posisis';

    protected $fillable = [
        'nama',
        'tunjangan',
    ];

    protected $casts = [
        'tunjangan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_posisi');
    }
}
