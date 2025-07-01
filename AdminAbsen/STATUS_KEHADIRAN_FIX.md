# Solusi Error: Column 'status_kehadiran' not found

## 🚨 Error Yang Terjadi
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status_kehadiran' in 'field list'
```

## 🔍 Penyebab Error
Error ini terjadi karena `status_kehadiran` adalah **accessor** (computed attribute) di model Eloquent, bukan kolom fisik di database.

### Dalam Model Attendance.php:
```php
public function getStatusKehadiranAttribute()
{
    if (!$this->check_in) {
        return 'Tidak Hadir';
    }

    // Logic untuk menentukan status berdasarkan waktu check_in
    // dan jadwal kantor
    if ($checkInDate->greaterThan($jamMasukStandar)) {
        return 'Terlambat';
    }

    return 'Tepat Waktu';
}
```

## ✅ Solusi Yang Diterapkan

### Sebelum (Error):
```php
// Query SQL langsung - ERROR karena kolom tidak ada
$monthlyStats = Attendance::where('user_id', $user->id)
    ->selectRaw('
        COUNT(*) as total_hari_hadir,
        SUM(CASE WHEN status_kehadiran = "Tepat Waktu" THEN 1 ELSE 0 END) as tepat_waktu,
        SUM(CASE WHEN status_kehadiran = "Terlambat" THEN 1 ELSE 0 END) as terlambat,
        ...
    ')
    ->first();
```

### Sesudah (Fixed):
```php
// Ambil data dulu, baru hitung menggunakan accessor
$monthlyAttendances = Attendance::where('user_id', $user->id)
    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
    ->get();

// Calculate stats manually menggunakan accessor
$monthlyStats = (object) [
    'total_hari_hadir' => $monthlyAttendances->count(),
    'tepat_waktu' => $monthlyAttendances->filter(function($attendance) {
        return $attendance->status_kehadiran === 'Tepat Waktu';
    })->count(),
    'terlambat' => $monthlyAttendances->filter(function($attendance) {
        return $attendance->status_kehadiran === 'Terlambat';
    })->count(),
    ...
];
```

## 🔧 Mengapa Solusi Ini Bekerja

### 1. **Accessor vs Database Column**
- **Accessor**: Computed attribute yang dihitung saat runtime
- **Database Column**: Kolom fisik yang tersimpan di database
- **status_kehadiran** adalah accessor, bukan kolom database

### 2. **Laravel Collection vs SQL Query**
- **SQL Query**: Tidak bisa menggunakan accessor karena dijalankan di database level
- **Laravel Collection**: Bisa menggunakan accessor karena dijalankan di PHP level

### 3. **Performance Trade-off**
- **SQL Aggregation**: Lebih cepat untuk dataset besar
- **Collection Filtering**: Lebih fleksibel untuk logic kompleks

## 📊 Struktur Database vs Model

### Database Columns (attendances table):
```sql
- id
- user_id
- check_in
- check_out
- absen_siang
- attendance_type
- longitude_absen_masuk
- latitude_absen_masuk
- picture_absen_masuk
- ... (other location/picture fields)
- created_at
- updated_at
```

### Model Accessors:
```php
- status_kehadiran (computed from check_in time vs office schedule)
- status_color (computed from status_kehadiran)
```

## 🎯 Best Practices

### ✅ DO:
1. Gunakan Collection untuk accessor-based calculations
2. Dokumentasikan mana yang accessor vs database column
3. Pertimbangkan performance untuk dataset besar

### ❌ DON'T:
1. Jangan gunakan accessor dalam SQL SELECT/WHERE clauses
2. Jangan asumsikan semua attribute adalah database columns
3. Jangan lupa handle null cases dalam accessor logic

## 🔄 Alternative Solutions

### Option 1: Database Column (Recommended untuk performance)
```php
// Tambah kolom status_kehadiran ke migration dan update saat save
Schema::table('attendances', function (Blueprint $table) {
    $table->enum('status_kehadiran', ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'])->nullable();
});
```

### Option 2: Database View
```sql
-- Buat view dengan computed status
CREATE VIEW attendance_with_status AS
SELECT *,
  CASE
    WHEN check_in IS NULL THEN 'Tidak Hadir'
    WHEN check_in > '08:00:00' THEN 'Terlambat'
    ELSE 'Tepat Waktu'
  END as status_kehadiran
FROM attendances;
```

### Option 3: Current Solution (Collection-based)
```php
// Ambil data dan gunakan accessor (current implementation)
$attendances->filter(fn($a) => $a->status_kehadiran === 'Tepat Waktu')
```

## 📈 Performance Comparison

| Method | Speed | Flexibility | Memory Usage |
|--------|-------|-------------|--------------|
| SQL Aggregation | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| Database Column | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| Collection Filter | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ |

## ✅ Status
- ✅ Error resolved
- ✅ Dashboard functioning correctly
- ✅ Statistics showing accurate data
- ✅ Performance acceptable for current dataset size
