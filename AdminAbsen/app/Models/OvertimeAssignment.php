<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OvertimeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_by',
        'overtime_id',
        'assigned_at',
        'approved_by',
        'approved_at',
        'assign_by',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan Pegawai (user yang ditugaskan lembur)
    public function user()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }

    // Relasi dengan Admin yang menugaskan
    public function assignedBy()
    {
        return $this->belongsTo(Pegawai::class, 'assigned_by');
    }

    // Relasi dengan Admin yang approve
    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }

    // Relasi dengan yang assign ulang
    public function assignBy()
    {
        return $this->belongsTo(Pegawai::class, 'assign_by');
    }

    // Accessor untuk format status
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'Assigned' => 'Ditugaskan',
            'Accepted' => 'Diterima',
            'Rejected' => 'Ditolak',
            default => ucfirst($this->status)
        };
    }

    // Accessor untuk format waktu assignment
    public function getAssignedAtFormattedAttribute()
    {
        return $this->assigned_at ? $this->assigned_at->format('d M Y H:i') : '-';
    }

    // Accessor untuk format waktu approval
    public function getApprovedAtFormattedAttribute()
    {
        return $this->approved_at ? $this->approved_at->format('d M Y H:i') : '-';
    }

    // Accessor untuk durasi sejak assignment
    public function getDurasiAssignmentAttribute()
    {
        if (!$this->assigned_at) return '-';

        return Carbon::parse($this->assigned_at)->diffForHumans();
    }

    // Scope untuk filter berdasarkan status
    public function scopeAssigned($query)
    {
        return $query->where('status', 'Assigned');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'Accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    // Scope untuk filter berdasarkan user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk filter berdasarkan yang menugaskan
    public function scopeByAssigner($query, $assignerId)
    {
        return $query->where('assigned_by', $assignerId);
    }

    // Method untuk accept overtime
    public function accept($approvedBy)
    {
        $this->update([
            'status' => 'Accepted',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    // Method untuk reject overtime
    public function reject($approvedBy)
    {
        $this->update([
            'status' => 'Rejected',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    // Method untuk reassign overtime
    public function reassign($newUserId, $assignBy)
    {
        $this->update([
            'user_id' => $newUserId,
            'assign_by' => $assignBy,
            'status' => 'Assigned',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    // Method untuk check apakah bisa diubah statusnya
    public function canChangeStatus()
    {
        return $this->status === 'Assigned';
    }
}
