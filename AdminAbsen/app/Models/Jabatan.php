<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jabatans';

    protected $fillable = [
        'nama',
        'tunjangan',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'tunjangan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk filter berdasarkan status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessor untuk format tunjangan
    public function getTunjanganFormattedAttribute()
    {
        return 'Rp ' . number_format((float)$this->tunjangan, 0, ',', '.');
    }

    // Method untuk mendapatkan pegawai yang menggunakan jabatan ini
    public function getPegawaiCountAttribute()
    {
        // Import model Pegawai untuk menghitung
        return \App\Models\Pegawai::where('jabatan_nama', $this->nama)->count();
    }
}
