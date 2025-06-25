# ATTENDANCE MANAGEMENT SYSTEM

## Overview
Sistem Manajemen Absensi AdminAbsen dirancang untuk memungkinkan Super Admin memantau dan mengevaluasi tingkat kehadiran karyawan secara komprehensif.

## Fitur Utama

### 1. Manajemen Absensi (`AttendanceResource`)
- **Lokasi Menu**: Sidebar > Manajemen Absensi
- **Fungsi**: Melihat detail data absensi individual karyawan
- **Fitur**:
  - View data absensi lengkap (check in, check out, absen siang)
  - Filter berdasarkan karyawan, tipe absensi, status kehadiran
  - Filter berdasarkan periode tanggal
  - Filter khusus (hari ini, bulan ini)
  - Auto refresh setiap 60 detik
  - View lokasi GPS dan foto absensi

### 2. Rekap Absensi (`AttendanceReportResource`)
- **Lokasi Menu**: Sidebar > Rekap Absensi
- **Fungsi**: Melihat ringkasan dan statistik kehadiran per karyawan
- **Fitur**:
  - Rekap per karyawan dengan metrik:
    - Total kehadiran
    - Total keterlambatan
    - Total tidak checkout
    - Total jam lembur
    - Rata-rata jam kerja per hari
    - Tingkat kehadiran (%)
  - Filter berdasarkan periode, jabatan
  - Filter khusus (kehadiran rendah <75%, sering terlambat >5x)
  - Link ke detail absensi karyawan

### 3. Dashboard Widget (`AttendanceOverviewWidget`)
- **Lokasi**: Dashboard utama
- **Metrik Real-time**:
  - Kehadiran hari ini (jumlah + persentase)
  - Tingkat kehadiran bulan ini
  - Tingkat keterlambatan bulan ini
  - Total lembur bulan ini
- **Visual**: Chart kehadiran 7 hari terakhir

## Struktur Database

### Tabel `attendances`
```sql
- id (PK)
- user_id (FK ke pegawais)
- office_working_hours_id (FK ke jam kerja)
- check_in (TIME)
- longitude_absen_masuk (DECIMAL)
- latitude_absen_masuk (DECIMAL)
- picture_absen_masuk (VARCHAR)
- absen_siang (TIME)
- longitude_absen_siang (DECIMAL)
- latitude_absen_siang (DECIMAL)
- picture_absen_siang (VARCHAR)
- check_out (TIME)
- longitude_absen_pulang (DECIMAL)
- latitude_absen_pulang (DECIMAL)
- picture_absen_pulang (VARCHAR)
- overtime (INT - menit)
- attendance_type (ENUM: WFO, Dinas Luar)
- created_at
- updated_at
```

## Model & Relasi

### `Attendance` Model
- **Relasi**:
  - `belongsTo(Pegawai::class, 'user_id')`
- **Accessor**:
  - `tanggal_absen`: Format tanggal dd MMM yyyy
  - `check_in_formatted`: Format jam HH:mm
  - `durasi_kerja`: Hitung durasi kerja (jam + menit)
  - `status_kehadiran`: Tepat Waktu/Terlambat/Tidak Hadir
  - `overtime_formatted`: Format jam lembur
- **Scope**:
  - `today()`: Absensi hari ini
  - `thisMonth()`: Absensi bulan ini
  - `byUser($userId)`: Filter by user
  - `byPeriod($start, $end)`: Filter by periode

### `Pegawai` Model (Updated)
- **Relasi tambahan**:
  - `hasMany(Attendance::class, 'user_id')`

## Sample Data
Sistem dilengkapi dengan `AttendanceSeeder` yang membuat:
- Data absensi untuk 30 hari kerja terakhir
- Variasi jam masuk (07:30 - 09:00)
- 90% tingkat kehadiran
- 80% absen siang
- 95% check out
- Variasi lembur dan keterlambatan
- Koordinat GPS dalam radius kantor

## Penggunaan

### Untuk Super Admin:
1. **Monitoring Harian**: Gunakan dashboard widget untuk cek kehadiran real-time
2. **Detail Absensi**: Akses "Manajemen Absensi" untuk detail per karyawan
3. **Evaluasi Bulanan**: Gunakan "Rekap Absensi" untuk analisis performa
4. **Filter & Search**: Manfaatkan filter untuk analisis spesifik

### Hak Akses:
- **Super Admin**: View only (tidak bisa create/edit/delete)
- **Employee**: Tidak ada akses (untuk input absensi via mobile app)

## Instalasi & Setup

1. **Migration**:
   ```bash
   php artisan migrate
   ```

2. **Seeder**:
   ```bash
   php artisan db:seed --class=AttendanceSeeder
   ```

3. **Update DatabaseSeeder**:
   - Tambahkan `AttendanceSeeder::class` ke dalam array `$this->call()`

## Teknologi
- **Framework**: Laravel 11 + Filament 3
- **Database**: MySQL
- **Frontend**: Filament Admin Panel
- **Charts**: Filament Widgets
- **Styling**: TailwindCSS (via Filament)

## File Structure
```
app/
├── Models/
│   └── Attendance.php
├── Filament/
│   ├── Resources/
│   │   ├── AttendanceResource.php
│   │   ├── AttendanceReportResource.php
│   │   └── AttendanceResource/Pages/
│   │       ├── ListAttendances.php
│   │       └── ViewAttendance.php
│   └── Widgets/
│       └── AttendanceOverviewWidget.php
database/
├── migrations/
│   └── 2025_06_25_013244_create_attendances_table.php
└── seeders/
    └── AttendanceSeeder.php
```

## Status
✅ Migration created and executed
✅ Model with relations and accessors
✅ Filament Resources (Attendance & Report)
✅ Dashboard Widget with stats
✅ Sample data seeder
✅ Admin view-only permissions
✅ Comprehensive filtering & search
✅ Real-time monitoring capabilities
