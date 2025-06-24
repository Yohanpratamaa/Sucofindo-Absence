# Manajemen Izin - AdminAbsen

## Overview

Sistem Manajemen Izin memungkinkan admin untuk mengelola pengajuan izin dari pegawai. Sistem ini **hanya untuk menerima dan menolak izin**, tidak untuk membuat izin baru.

## Fitur Utama

### 1. **View Only System**

-   Admin hanya bisa melihat, menyetujui, atau menolak izin
-   Tidak ada fitur create/edit izin dari admin panel
-   Pegawai mengajukan izin melalui sistem lain/mobile app

### 2. **Status Management**

-   **Pending**: Izin yang menunggu persetujuan
-   **Approved**: Izin yang telah disetujui
-   **Rejected**: Izin yang ditolak

### 3. **Bulk Actions**

-   Setujui multiple izin sekaligus
-   Tolak multiple izin sekaligus
-   Efficient workflow untuk admin

## Struktur Database

### Tabel `izins`

```sql
CREATE TABLE `izins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `jenis_izin` enum('sakit','cuti','izin') NOT NULL DEFAULT 'cuti',
  `keterangan` text,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `izins_user_id_tanggal_mulai_index` (`user_id`,`tanggal_mulai`),
  KEY `izins_approved_by_approved_at_index` (`approved_by`,`approved_at`),
  KEY `izins_jenis_izin_index` (`jenis_izin`)
);
```

### Field Descriptions

| Field               | Type      | Description                                       |
| ------------------- | --------- | ------------------------------------------------- |
| `user_id`           | bigint    | ID pegawai yang mengajukan (FK ke tabel pegawais) |
| `tanggal_mulai`     | date      | Tanggal mulai izin                                |
| `tanggal_akhir`     | date      | Tanggal akhir izin                                |
| `jenis_izin`        | enum      | Jenis izin: 'sakit', 'cuti', 'izin'               |
| `keterangan`        | text      | Alasan/keterangan izin                            |
| `dokumen_pendukung` | varchar   | Path file dokumen (opsional)                      |
| `approved_by`       | bigint    | ID admin yang approve/reject (FK ke pegawais)     |
| `approved_at`       | timestamp | Waktu approve (null jika rejected)                |

## Model Features

### Izin Model

#### Fillable Fields

```php
protected $fillable = [
    'user_id', 'tanggal_mulai', 'tanggal_akhir',
    'jenis_izin', 'keterangan', 'dokumen_pendukung',
    'approved_by', 'approved_at'
];
```

#### Relationships

```php
// Pegawai yang mengajukan izin
public function user()
{
    return $this->belongsTo(Pegawai::class, 'user_id');
}

// Admin yang approve/reject
public function approvedBy()
{
    return $this->belongsTo(Pegawai::class, 'approved_by');
}
```

#### Accessors

```php
// Status izin (computed)
public function getStatusAttribute()
{
    if ($this->approved_at && $this->approved_by) return 'approved';
    elseif ($this->approved_by && !$this->approved_at) return 'rejected';
    else return 'pending';
}

// Durasi izin dalam hari
public function getDurasiHariAttribute()
{
    return Carbon::parse($this->tanggal_mulai)->diffInDays($this->tanggal_akhir) + 1;
}

// Format periode izin
public function getPeriodeIzinAttribute()
{
    $start = Carbon::parse($this->tanggal_mulai);
    $end = Carbon::parse($this->tanggal_akhir);

    if ($start->equalTo($end)) {
        return $start->format('d M Y');
    }
    return $start->format('d M Y') . ' - ' . $end->format('d M Y');
}
```

#### Scopes

```php
// Filter by status
public function scopePending($query)
public function scopeApproved($query)
public function scopeRejected($query)

// Filter by jenis
public function scopeJenis($query, $jenis)

// Filter by user
public function scopeByUser($query, $userId)
```

#### Methods

```php
// Approve izin
public function approve($approvedBy)
{
    $this->update([
        'approved_by' => $approvedBy,
        'approved_at' => now(),
    ]);
}

// Reject izin
public function reject($approvedBy)
{
    $this->update([
        'approved_by' => $approvedBy,
        'approved_at' => null,
    ]);
}
```

## Filament Resource Features

### Navigation

-   **Label**: "Manajemen Izin"
-   **Icon**: heroicon-o-calendar-days
-   **Sort**: 3 (setelah Master Data)

### Table Columns

1. **Nama Pegawai** - Searchable, sortable
2. **NPP** - Searchable, sortable
3. **Jenis Izin** - Badge dengan warna (cuti=primary, sakit=warning, izin=info)
4. **Periode Izin** - Format tanggal dengan accessor
5. **Durasi** - Jumlah hari dengan suffix "hari"
6. **Status** - Badge dengan warna (pending=warning, approved=success, rejected=danger)
7. **Disetujui Oleh** - Hidden by default
8. **Tanggal Disetujui** - Hidden by default
9. **Diajukan Pada** - Hidden by default

### Filters

1. **Jenis Izin** - Dropdown: Sakit, Cuti, Izin Khusus
2. **Status** - Dropdown: Menunggu, Disetujui, Ditolak (menggunakan scope)
3. **Periode Tanggal** - Custom filter dengan 2 date picker

### Actions

#### Single Actions

1. **View** - Melihat detail izin (form read-only)
2. **Approve** - Setujui izin dengan konfirmasi
3. **Reject** - Tolak izin dengan konfirmasi

#### Bulk Actions

1. **Setujui Terpilih** - Approve multiple pending izin
2. **Tolak Terpilih** - Reject multiple pending izin

### Form (View Only)

```php
// Section 1: Detail Izin
- Nama Pegawai (disabled select)
- Jenis Izin (disabled select)
- Tanggal Mulai (disabled date picker)
- Tanggal Akhir (disabled date picker)
- Keterangan (disabled textarea)
- Dokumen Pendukung (disabled file upload)

// Section 2: Status Persetujuan (visible jika sudah ada approval)
- Disetujui Oleh (disabled select)
- Tanggal Disetujui (disabled datetime picker)
```

## Data Contoh (Seeder)

### 10 Data Izin Sample:

1. **Sakit** - Demam tinggi (Pending)
2. **Cuti** - Liburan keluarga (Approved)
3. **Izin** - Pernikahan saudara (Rejected)
4. **Sakit** - Operasi usus buntu (Pending)
5. **Cuti** - Cuti melahirkan (Approved)
6. **Izin** - Urus dokumen (Pending)
7. **Sakit** - COVID-19 isolasi (Approved)
8. **Cuti** - Umroh keluarga (Pending)
9. **Izin** - Wisuda anak (Rejected)
10. **Cuti** - Reuni sekolah (Pending)

### Status Distribution:

-   **5 Pending** - Menunggu persetujuan
-   **3 Approved** - Telah disetujui
-   **2 Rejected** - Ditolak

## Workflow Penggunaan

### 1. **Akses Menu**

-   Buka menu "Manajemen Izin" di sidebar
-   Lihat daftar semua pengajuan izin

### 2. **Filter & Search**

-   Filter berdasarkan jenis izin atau status
-   Filter berdasarkan periode tanggal
-   Search berdasarkan nama/NPP pegawai

### 3. **Review Izin**

-   Klik "View" untuk melihat detail lengkap
-   Cek keterangan dan dokumen pendukung
-   Tentukan keputusan approve/reject

### 4. **Approve/Reject**

-   **Single**: Klik tombol "Setujui" atau "Tolak" di row action
-   **Bulk**: Select multiple izin → gunakan bulk action
-   Konfirmasi keputusan di modal popup

### 5. **Notifikasi**

-   Sistem otomatis menampilkan notifikasi success
-   Status izin langsung terupdate di table

## Features & Benefits

### Admin Benefits

-   **Centralized Management**: Semua izin di satu tempat
-   **Efficient Workflow**: Bulk approve/reject untuk produktivitas
-   **Detailed View**: Lengkap dengan dokumen pendukung
-   **Filter & Search**: Mudah mencari izin spesifik
-   **Status Tracking**: Riwayat approval dengan timestamp

### System Benefits

-   **Read-Only Approach**: Admin tidak bisa mengubah data izin
-   **Audit Trail**: Tracking siapa yang approve/reject
-   **Responsive Design**: Filament UI yang modern
-   **Performance**: Index database untuk query cepat

## Migration & Seeding Commands

```bash
# Run migration
php artisan migrate

# Seed data contoh
php artisan db:seed --class=IzinSeeder

# Atau seed semua
php artisan db:seed
```

## API Integration (Future)

Sistem ini dirancang untuk integrasi dengan:

-   **Mobile App**: Employee self-service untuk pengajuan izin
-   **External HR System**: Import/export data izin
-   **Notification System**: Email/SMS notification approval

## File Locations

```
📁 Database
├── 📄 migrations/2025_06_24_094356_create_izins_table.php
├── 📄 seeders/IzinSeeder.php

📁 Models
├── 📄 app/Models/Izin.php

📁 Filament Resources
├── 📄 app/Filament/Resources/IzinResource.php
├── 📄 app/Filament/Resources/IzinResource/Pages/ListIzins.php
├── 📄 app/Filament/Resources/IzinResource/Pages/ViewIzin.php

📁 Documentation
├── 📄 IZIN_MANAGEMENT.md
```
