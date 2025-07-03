# Dokumentasi Fitur Absensi Terkunci Setelah Jam 17:00

## 📋 Ringkasan Fitur

Fitur ini mengimplementasikan sistem penguncian absensi pegawai yang berlaku setelah jam 17:00. Ketika pegawai mencoba melakukan absensi setelah jam tersebut, menu absensi akan terkunci dan data absensi akan otomatis tercatat sebagai "Tidak Absensi".

## 🎯 Ketentuan Fitur

1. **Penguncian Waktu**: Absensi terkunci setelah jam 17:00
2. **Auto-Create**: Jika pegawai belum absen hingga jam 17:00, data "Tidak Absensi" dibuat otomatis
3. **Check-in Terlambat**: Jika pegawai check-in setelah jam 17:00, status tetap "Tidak Absensi"
4. **UI Lock**: Menu absensi pegawai menampilkan status terkunci dengan notifikasi jelas

## 🔧 File yang Dimodifikasi

### 1. Model Attendance (`app/Models/Attendance.php`)

-   **Method**: `getStatusKehadiranAttribute()`
-   **Logic**: Jika `check_in` null atau >= 17:00, maka status = "Tidak Absensi"

### 2. AttendancePage (`app/Filament/Pegawai/Pages/AttendancePage.php`)

-   **Method Baru**:
    -   `isAttendanceLocked()` - Cek apakah waktu >= 17:00
    -   `checkAndCreateTidakAbsensi()` - Auto-create record jika belum absen
-   **Method Modified**:
    -   `calculateWfoStatus()` - Kunci aksi WFO setelah jam 17:00
    -   `calculateDinasLuarStatus()` - Kunci aksi Dinas Luar setelah jam 17:00
    -   `processCheckIn()`, `processCheckInPagi()`, `processCheckInSiang()` - Validasi waktu

### 3. View Blade (`resources/views/filament/pegawai/pages/attendance-page.blade.php`)

-   **Notifikasi**: Alert merah ketika absensi terkunci
-   **Tombol**: Disable tombol kamera dengan pesan "Absensi Terkunci"
-   **Status**: Tampilan waktu saat ini dan pesan lock

### 4. Resource Views (`app/Filament/Pegawai/Resources/`)

-   **MyAttendanceResource.php**: Background merah untuk status "Tidak Absensi"
-   **MyAllAttendanceResource.php**: Filter dan tampilan "Tidak Absensi"

### 5. Database Migration

-   **File**: `2025_07_03_060107_make_check_in_nullable_in_attendances_table.php`
-   **Perubahan**: Kolom `check_in` di tabel `attendances` dibuat nullable

## ✅ Fitur yang Telah Diimplementasikan

1. **✅ Penguncian Waktu**: Absensi terkunci setelah jam 17:00
2. **✅ Auto-Create**: Record "Tidak Absensi" dibuat otomatis
3. **✅ Validasi Check-in**: Tolak absensi setelah jam 17:00 dengan notifikasi
4. **✅ UI Lock**: Tampilan terkunci di frontend
5. **✅ Status Detection**: Model mendeteksi check-in >= 17:00 sebagai "Tidak Absensi"
6. **✅ Visual Indicator**: Background merah untuk record "Tidak Absensi"
7. **✅ Database**: Kolom check_in nullable untuk mendukung auto-create

## 🚀 Cara Kerja

1. **Sebelum Jam 17:00**: Absensi normal berjalan seperti biasa
2. **Tepat/Setelah Jam 17:00**:
    - Menu absensi terkunci
    - Tombol kamera disabled
    - Alert merah ditampilkan
    - Auto-create "Tidak Absensi" jika belum absen
3. **Check-in Setelah 17:00**: Status tetap "Tidak Absensi" meski berhasil check-in

## 🎨 Tampilan UI

-   **Alert Terkunci**: Background merah dengan icon lock
-   **Tombol Disabled**: "🔒 Absensi Terkunci (Setelah 17:00)"
-   **Status Real-time**: Menampilkan waktu saat ini
-   **Table Record**: Background merah untuk "Tidak Absensi"

## 🧪 Testing

Fitur telah ditest dengan berbagai skenario:

-   ✅ Check-in normal (sebelum 17:00)
-   ✅ Check-in tepat jam 17:00
-   ✅ Check-in setelah 17:00
-   ✅ Tidak check-in sama sekali
-   ✅ Auto-create functionality
-   ✅ UI lock behavior

## 📊 Data Status

Status "Tidak Absensi" muncul ketika:

1. Tidak ada check-in sama sekali (`check_in` = null)
2. Check-in dilakukan pada jam 17:00 atau setelahnya
3. Auto-create dijalankan setelah jam 17:00

---

**Status**: ✅ **AKTIF dan BERFUNGSI SEMPURNA**
**Tanggal Implementasi**: 3 Juli 2025
