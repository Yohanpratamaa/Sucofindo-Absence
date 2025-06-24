<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Izin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'jenis_izin',
        'keterangan',
        'dokumen_pendukung',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_akhir' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan Pegawai (user yang mengajukan izin)
    public function user()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }

    // Relasi dengan Admin yang approve
    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }

    // Accessor untuk status izin
    public function getStatusAttribute()
    {
        if ($this->approved_at && $this->approved_by) {
            return 'approved';
        } elseif ($this->approved_by && !$this->approved_at) {
            return 'rejected';
        } else {
            return 'pending';
        }
    }

    // Accessor untuk durasi izin
    public function getDurasiHariAttribute()
    {
        if ($this->tanggal_mulai && $this->tanggal_akhir) {
            $start = Carbon::parse($this->tanggal_mulai);
            $end = Carbon::parse($this->tanggal_akhir);
            return $start->diffInDays($end) + 1;
        }
        return 0;
    }

    // Accessor untuk format tanggal
    public function getPeriodeIzinAttribute()
    {
        if ($this->tanggal_mulai && $this->tanggal_akhir) {
            $start = Carbon::parse($this->tanggal_mulai);
            $end = Carbon::parse($this->tanggal_akhir);
            if ($start->equalTo($end)) {
                return $start->format('d M Y');
            }
            return $start->format('d M Y') . ' - ' . $end->format('d M Y');
        }
        return '-';
    }

    // Scope untuk filter berdasarkan status
    public function scopePending($query)
    {
        return $query->whereNull('approved_by')->whereNull('approved_at');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_by')->whereNotNull('approved_at');
    }

    public function scopeRejected($query)
    {
        return $query->whereNotNull('approved_by')->whereNull('approved_at');
    }

    // Scope untuk filter berdasarkan jenis izin
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis_izin', $jenis);
    }

    // Scope untuk filter berdasarkan user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Method untuk approve izin
    public function approve($approvedBy)
    {
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    // Method untuk reject izin
    public function reject($approvedBy)
    {
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => null,
        ]);
    }
}
