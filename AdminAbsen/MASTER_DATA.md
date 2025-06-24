# Master Data Jabatan dan Posisi

## Overview

Sistem Master Data memungkinkan pengelolaan data jabatan dan posisi secara terpisah dari data pegawai. Data master ini kemudian digunakan dalam form pegawai sebagai dropdown selection dengan auto-fill tunjangan.

## Struktur Database

### Tabel `jabatans`

```sql
CREATE TABLE `jabatans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deskripsi` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jabatans_nama_unique` (`nama`),
  KEY `jabatans_status_nama_index` (`status`,`nama`)
);
```

### Tabel `posisis`

```sql
CREATE TABLE `posisis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deskripsi` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posisis_nama_unique` (`nama`),
  KEY `posisis_status_nama_index` (`status`,`nama`)
);
```

## Data Master Default

### Jabatan

1. **Direktur Utama** - Rp 15.000.000
2. **Direktur Operasional** - Rp 12.000.000
3. **Manager** - Rp 8.000.000
4. **Supervisor** - Rp 5.000.000
5. **Staff Senior** - Rp 3.000.000
6. **Staff** - Rp 2.000.000
7. **Staff Junior** - Rp 1.500.000
8. **Magang** - Rp 500.000

### Posisi

1. **Kepala Divisi IT** - Rp 6.000.000
2. **Kepala Divisi HRD** - Rp 5.500.000
3. **Kepala Divisi Keuangan** - Rp 6.000.000
4. **Kepala Divisi Marketing** - Rp 5.000.000
5. **Software Developer** - Rp 4.000.000
6. **System Administrator** - Rp 3.500.000
7. **Database Administrator** - Rp 3.800.000
8. **HR Specialist** - Rp 3.000.000
9. **Accounting Staff** - Rp 2.500.000
10. **Marketing Staff** - Rp 2.800.000
11. **Customer Service** - Rp 2.000.000
12. **Administrative Staff** - Rp 1.800.000

## Fitur Resource Manager

### JabatanResource

-   **Navigation Group**: Master Data (sort order: 1)
-   **Icon**: heroicon-o-briefcase
-   **Form Fields**:
    -   Nama Jabatan (required, unique)
    -   Tunjangan (numeric, prefix Rp)
    -   Deskripsi (textarea)
    -   Status (select: active/inactive)

### PosisiResource

-   **Navigation Group**: Master Data (sort order: 2)
-   **Icon**: heroicon-o-briefcase
-   **Form Fields**: Sama seperti JabatanResource

### Table Columns

-   **Nama**: Searchable, sortable
-   **Tunjangan**: Formatted dengan accessor (Rp 8.000.000,00)
-   **Deskripsi**: Limited 50 chars dengan tooltip
-   **Status**: Badge (green=active, red=inactive)
-   **Jumlah Pegawai**: Count relationship
-   **Created/Updated**: Toggleable, hidden by default

### Filters & Actions

-   **Filter**: Status dropdown
-   **Actions**: View, Edit, Delete
-   **Bulk Actions**: Delete multiple
-   **Default Sort**: Nama ascending

## Model Features

### Jabatan & Posisi Models

```php
// Fillable fields
protected $fillable = ['nama', 'tunjangan', 'deskripsi', 'status'];

// Casts
protected $casts = [
    'tunjangan' => 'decimal:2',
    'status' => 'string',
];

// Accessor untuk format tunjangan
public function getTunjanganFormattedAttribute()
{
    return 'Rp ' . number_format($this->tunjangan, 0, ',', '.');
}

// Accessor untuk hitung pegawai
public function getPegawaiCountAttribute()
{
    return Pegawai::where('jabatan_nama', $this->nama)->count();
}

// Scope untuk status active
public function scopeActive($query)
{
    return $query->where('status', 'active');
}
```

## Integrasi dengan Form Pegawai

### Dropdown Implementation

```php
Forms\Components\Select::make('jabatan_nama')
    ->label('Nama Jabatan')
    ->required()
    ->searchable()
    ->preload()
    ->options(fn () => Jabatan::where('status', 'active')->pluck('nama', 'nama'))
    ->live()
    ->afterStateUpdated(function (Forms\Set $set, $state) {
        if ($state) {
            $jabatan = Jabatan::where('nama', $state)->first();
            $set('jabatan_tunjangan', $jabatan?->tunjangan ?? 0);
        } else {
            $set('jabatan_tunjangan', 0);
        }
    })
```

### Auto-Fill Tunjangan

-   Field tunjangan menjadi read-only
-   Otomatis terisi saat pilihan jabatan/posisi berubah
-   Menggunakan `live()` dan `afterStateUpdated()` callback

### Search & Preload

-   **Searchable**: User bisa mengetik untuk mencari
-   **Preload**: Data dimuat saat form dibuka
-   **Filter Active**: Hanya menampilkan data dengan status active

## Seeder Command

```bash
# Seed data master
php artisan db:seed --class=JabatanSeeder
php artisan db:seed --class=PosisiSeeder

# Atau seed semua (termasuk pegawai)
php artisan db:seed
```

## Migration Command

```bash
# Jalankan migration untuk tabel master
php artisan migrate

# Fresh migrate dengan seeder
php artisan migrate:fresh --seed
```

## Workflow Penggunaan

1. **Setup Master Data**:

    - Buka menu Jabatan, tambah data jabatan
    - Buka menu Posisi, tambah data posisi

2. **Input Pegawai**:

    - Form pegawai akan menampilkan dropdown berisi data master
    - Pilih jabatan/posisi dari dropdown
    - Tunjangan otomatis terisi

3. **Maintenance**:
    - Update master data akan mempengaruhi dropdown
    - Non-aktifkan data master jika tidak digunakan
    - Data lama di pegawai tetap tersimpan

## Benefits

-   **Konsistensi Data**: Jabatan/posisi terstandardisasi
-   **Easy Maintenance**: Update master data sekali, berlaku untuk semua
-   **User Friendly**: Dropdown dengan pencarian
-   **Data Integrity**: Validasi unique di level database
-   **Reporting**: Bisa agregasi berdasarkan jabatan/posisi
