<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorEmergency extends Model
{
    use HasFactory;

    protected $table = 'nomor_emergencies';

    protected $fillable = [
        'user_id',
        'relationship',
        'nama',
        'no_emergency',
        'alamat',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }
}
