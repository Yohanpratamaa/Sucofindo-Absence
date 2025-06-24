<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorEmergency extends Model
{
    use HasFactory;

    protected $table = 'nomor_emergencies';

    protected $fillable = [
        'relationship',
        'nama',
        'no_emergency',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_nomor_emergency');
    }
}
