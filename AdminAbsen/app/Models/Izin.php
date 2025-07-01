<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    // Relasi dengan ManajemenIzin (master data jenis izin)
    public function jenisIzinData()
    {
        return $this->belongsTo(ManajemenIzin::class, 'jenis_izin', 'kode_izin');
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
        $approver = Pegawai::find($approvedBy);
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        // Log approval action
        Log::info("Izin ID {$this->id} disetujui oleh {$approver->nama} (ID: {$approvedBy}) pada " . now());
    }

    // Method untuk reject izin
    public function reject($approvedBy)
    {
        $approver = Pegawai::find($approvedBy);
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => null,
        ]);

        // Log rejection action
        Log::info("Izin ID {$this->id} ditolak oleh {$approver->nama} (ID: {$approvedBy}) pada " . now());
    }

    // Accessor untuk mendapatkan informasi lengkap persetujuan
    public function getApprovalInfoAttribute()
    {
        if (!$this->approved_by) {
            return 'Belum diproses';
        }

        $approver = $this->approvedBy;
        $approverName = $approver ? $approver->nama : 'Unknown';
        $approvalDate = $this->approved_at ? $this->approved_at->format('d M Y H:i') : 'Ditolak';

        if ($this->approved_at) {
            return "Disetujui oleh {$approverName} pada {$approvalDate}";
        } else {
            return "Ditolak oleh {$approverName} pada " . $this->updated_at->format('d M Y H:i');
        }
    }

    // Accessor untuk badge status dengan info approver
    public function getStatusBadgeAttribute()
    {
        if ($this->approved_at && $this->approved_by) {
            return [
                'status' => 'approved',
                'label' => 'Disetujui',
                'color' => 'success',
                'approver' => $this->approvedBy->nama ?? 'Unknown'
            ];
        } elseif ($this->approved_by && !$this->approved_at) {
            return [
                'status' => 'rejected',
                'label' => 'Ditolak',
                'color' => 'danger',
                'approver' => $this->approvedBy->nama ?? 'Unknown'
            ];
        } else {
            return [
                'status' => 'pending',
                'label' => 'Menunggu',
                'color' => 'warning',
                'approver' => null
            ];
        }
    }
}
