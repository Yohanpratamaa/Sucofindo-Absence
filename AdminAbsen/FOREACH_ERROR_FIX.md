# Fix untuk Error "foreach() argument must be of type array|object, string given"

## Masalah yang Ditemukan

Error ini terjadi karena field JSON seperti `fasilitas_list`, `pendidikan_list`, dan `emergency_contacts` dalam database kadang tersimpan sebagai string JSON atau null, namun Filament's `RepeatableEntry` mengharapkan data berupa array untuk diiterasi.

## Solusi yang Diterapkan

### 1. Perbaikan di ViewPegawai.php

**File**: `app/Filament/Resources/PegawaiResource/Pages/ViewPegawai.php`

**Perubahan**:
- Menambahkan method `state()` pada setiap `RepeatableEntry` untuk memvalidasi dan mengonversi data
- Menambahkan tab lengkap untuk:
  - Data Pribadi
  - Jabatan & Posisi
  - Pendidikan
  - Kontak Darurat
  - Fasilitas

**Code Pattern yang Ditambahkan**:
```php
->state(function ($record) {
    $data = $record->field_name;
    
    // Jika null atau kosong, return array kosong
    if (empty($data)) {
        return [];
    }
    
    // Jika masih string JSON, decode dulu
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        return is_array($decoded) ? $decoded : [];
    }
    
    // Jika sudah array, return as-is
    if (is_array($data)) {
        return $data;
    }
    
    // Fallback: return array kosong
    return [];
})
```

### 2. Perbaikan di Model Pegawai.php

**File**: `app/Models/Pegawai.php`

**Perubahan**:
- Menambahkan **Accessor Methods** untuk memastikan field JSON selalu return array
- Menambahkan **Mutator Methods** untuk memastikan data disimpan sebagai JSON string

**Accessor Methods yang Ditambahkan**:
```php
public function getFasilitasListAttribute($value)
public function getPendidikanListAttribute($value)
public function getEmergencyContactsAttribute($value)
```

**Mutator Methods yang Ditambahkan**:
```php
public function setFasilitasListAttribute($value)
public function setPendidikanListAttribute($value)
public function setEmergencyContactsAttribute($value)
```

### 3. Struktur Tab yang Diperbaiki

#### Tab Data Pribadi
- Nama, NPP, Email, NIK
- Status Pegawai, Status Aktif, Role User
- Nomor HP, Alamat

#### Tab Jabatan & Posisi
- Jabatan & Tunjangan Jabatan
- Posisi & Tunjangan Posisi

#### Tab Pendidikan
- Jenjang, Sekolah/Universitas
- Fakultas, Jurusan
- Tahun Masuk, Tahun Lulus
- IPK/Nilai, Link Ijazah

#### Tab Kontak Darurat
- Hubungan, Nama Kontak
- Nomor Telepon

#### Tab Fasilitas
- Jenis Fasilitas, Nama Jaminan
- Nomor Jaminan, Provider
- Nilai Fasilitas, Status
- Tanggal Mulai/Berakhir, Keterangan

## Alur Perbaikan Error

### 1. **Root Cause**
Field JSON di database mungkin:
- Null
- String JSON yang belum di-decode
- Array yang sudah di-decode
- Data corrupt atau format lain

### 2. **Detection & Validation**
```php
// Cek apakah data kosong
if (empty($data)) return [];

// Cek apakah masih string JSON
if (is_string($data)) {
    $decoded = json_decode($data, true);
    return is_array($decoded) ? $decoded : [];
}

// Cek apakah sudah array
if (is_array($data)) return $data;

// Fallback
return [];
```

### 3. **Prevention**
- Accessor/Mutator di Model memastikan konsistensi data
- Cast 'array' di Model tetap dipertahankan untuk kompatibilitas
- Validation di level View untuk double-safety

## Testing

Setelah fix ini, test:

1. **View Pegawai dengan data fasilitas lengkap**
2. **View Pegawai dengan data fasilitas kosong/null**
3. **View Pegawai dengan data JSON corrupt**
4. **Create/Edit Pegawai untuk memastikan data tersimpan benar**

## Error Prevention

Untuk mencegah error serupa:

1. **Selalu validasi data JSON** sebelum iterasi
2. **Gunakan accessor/mutator** untuk field JSON
3. **Test dengan data edge cases** (null, empty, corrupt)
4. **Monitor logs** untuk error serupa

## Compatibility

Fix ini kompatibel dengan:
- ✅ Laravel casting system
- ✅ Filament RepeatableEntry
- ✅ Existing data di database
- ✅ Create/Edit operations

## Files Modified

1. `app/Filament/Resources/PegawaiResource/Pages/ViewPegawai.php`
   - Tambah validasi state untuk RepeatableEntry
   - Tambah tab lengkap untuk semua data

2. `app/Models/Pegawai.php`
   - Tambah accessor methods untuk safety
   - Tambah mutator methods untuk consistency
