<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ManajemenIzin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'manajemen_izins';

    protected $fillable = [
        'nama_izin',
        'kode_izin',
        'deskripsi',
        'max_hari',
        'perlu_dokumen',
        'auto_approve',
        'kategori',
        'warna_badge',
        'is_active',
        'urutan_tampil',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'perlu_dokumen' => 'boolean',
        'auto_approve' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relasi dengan user yang membuat
    public function createdBy()
    {
        return $this->belongsTo(Pegawai::class, 'created_by');
    }

    // Relasi dengan user yang mengupdate
    public function updatedBy()
    {
        return $this->belongsTo(Pegawai::class, 'updated_by');
    }

    // Relasi dengan izin yang menggunakan template ini
    public function izins()
    {
        return $this->hasMany(Izin::class, 'jenis_izin', 'kode_izin');
    }

    // Scope untuk yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk diurutkan
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan_tampil', 'asc')->orderBy('nama_izin', 'asc');
    }

    // Accessor untuk mendapatkan opsi select
    public static function getSelectOptions()
    {
        try {
            return static::active()->ordered()->pluck('nama_izin', 'kode_izin')->toArray();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting ManajemenIzin select options', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    // Accessor untuk badge
    public function getBadgeColorAttribute()
    {
        $colors = [
            'primary' => '#3b82f6',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'info' => '#06b6d4',
            'secondary' => '#6b7280',
        ];

        return $colors[$this->warna_badge] ?? $colors['primary'];
    }

    // Method untuk mendapatkan informasi lengkap izin
    public function getInfoLengkapAttribute()
    {
        $info = [];

        if ($this->max_hari) {
            $info[] = "Maksimal {$this->max_hari} hari";
        }

        if ($this->perlu_dokumen) {
            $info[] = "Memerlukan dokumen pendukung";
        }

        if ($this->auto_approve) {
            $info[] = "Otomatis disetujui";
        }

        return implode(' • ', $info);
    }

    // Method untuk validasi pengajuan izin
    public function validatePengajuan($tanggalMulai, $tanggalAkhir, $dokumen = null)
    {
        $errors = [];

        // Validasi maksimal hari
        if ($this->max_hari) {
            $hari = \Carbon\Carbon::parse($tanggalMulai)->diffInDays(\Carbon\Carbon::parse($tanggalAkhir)) + 1;
            if ($hari > $this->max_hari) {
                $errors[] = "Durasi izin melebihi batas maksimal {$this->max_hari} hari";
            }
        }

        // Validasi dokumen pendukung
        if ($this->perlu_dokumen && !$dokumen) {
            $errors[] = "Dokumen pendukung wajib diunggah untuk jenis izin ini";
        }

        return $errors;
    }

    // Event listeners
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
