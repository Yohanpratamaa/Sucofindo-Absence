# Implementasi Absensi Dinas Luar

## Overview
Dokumentasi ini menjelaskan implementasi fitur Absensi Dinas Luar untuk pegawai yang berbeda dengan Absensi WFO. Fitur ini memungkinkan pegawai melakukan 3 kali absensi (pagi, siang, sore) tanpa batasan radius kantor.

## Perbedaan dengan Absensi WFO

### Absensi WFO:
- Hanya 2 kali absensi (check-in dan check-out)
- Harus berada dalam radius kantor
- Menggunakan jadwal kantor untuk menentukan status keterlambatan

### Absensi Dinas Luar:
- 3 kali absensi (pagi, siang, sore)
- Tidak ada batasan radius lokasi
- Menggunakan batas waktu tetap untuk menentukan keterlambatan
- Wajib mengambil foto selfie untuk setiap sesi absensi
- Lokasi GPS dicatat untuk setiap absensi

## Fitur yang Diimplementasikan

### 1. Tiga Sesi Absensi
- **Absen Pagi**: Check-in awal hari
- **Absen Siang**: Konfirmasi kehadiran di tengah hari
- **Absen Sore**: Check-out akhir hari

### 2. Status Keterlambatan
- Batas waktu absen pagi: 08:30
- Status "Terlambat" jika absen pagi setelah 08:30
- Status "Tepat Waktu" jika absen pagi sebelum/tepat 08:30

### 3. Progress Tracking
- Progress bar menampilkan persentase absensi yang sudah dilakukan
- Visual indicator untuk setiap sesi yang sudah selesai
- Badge status pada dashboard

### 4. Location Tracking
- GPS coordinates dicatat untuk setiap sesi absensi
- Tidak ada validasi radius (berbeda dengan WFO)
- Informasi lokasi ditampilkan untuk transparansi

## Struktur File

### PHP Controller
**File**: `app/Filament/Pegawai/Pages/DinaslLuarAttendance.php`

**Methods Utama**:
- `processCheckInPagi()` - Menangani absensi pagi
- `processCheckInSiang()` - Menangani absensi siang
- `processCheckOut()` - Menangani absensi sore
- `getAttendanceProgress()` - Menghitung progress absensi
- `calculateAttendanceStatus()` - Menentukan aksi yang tersedia

### Blade Template
**File**: `resources/views/filament/pegawai/pages/dinas-luar-attendance.blade.php`

**Fitur UI**:
- Header dengan progress bar
- Status lokasi real-time
- Camera interface dengan kontrol lengkap
- Action buttons yang dinamis sesuai sesi

### Widget Dashboard
**File**: `app/Filament/Pegawai/Widgets/DinaslLuarAttendanceStatusWidget.php`
**File**: `resources/views/filament/pegawai/widgets/dinas-luar-attendance-status-widget.blade.php`

**Fitur Widget**:
- Progress visual dengan step indicator
- Statistik bulanan
- Quick action buttons
- Status real-time

## Alur Kerja Absensi

### 1. Absensi Pagi
```php
// Kondisi: Belum ada absensi hari ini
// Action: processCheckInPagi()
// Status: Menentukan "Tepat Waktu" atau "Terlambat" berdasarkan jam 08:30
```

### 2. Absensi Siang
```php
// Kondisi: Sudah absen pagi, belum absen siang
// Action: processCheckInSiang()
// Status: Tidak ada pengecekan keterlambatan untuk siang
```

### 3. Absensi Sore
```php
// Kondisi: Sudah absen pagi dan siang, belum absen sore
// Action: processCheckOut()
// Status: Menyelesaikan absensi hari ini
```

## Implementasi Teknis

### 1. Database Schema
Menggunakan tabel `attendances` yang sama dengan WFO:
```sql
- user_id: FK ke pegawai
- attendance_type: 'Dinas Luar'
- check_in: Timestamp absen pagi
- absen_siang: Timestamp absen siang
- check_out: Timestamp absen sore
- latitude_absen_masuk: GPS absen pagi
- longitude_absen_masuk: GPS absen pagi
- latitude_absen_siang: GPS absen siang
- longitude_absen_siang: GPS absen siang
- latitude_absen_pulang: GPS absen sore
- longitude_absen_pulang: GPS absen sore
- picture_absen_masuk: Foto absen pagi
- picture_absen_siang: Foto absen siang
- picture_absen_pulang: Foto absen sore
```

### 2. Logika Status Keterlambatan
```php
// Untuk absensi pagi
$currentTime = Carbon::now();
$lateThresholdPagi = Carbon::parse('08:30')
    ->setDate($currentTime->year, $currentTime->month, $currentTime->day);

if ($currentTime->greaterThan($lateThresholdPagi)) {
    $attendanceStatus = 'Terlambat';
    $isLate = true;
} else {
    $attendanceStatus = 'Tepat Waktu';
    $isLate = false;
}
```

### 3. Progress Calculation
```php
public function getAttendanceProgress()
{
    $pagi = !is_null($this->todayAttendance->check_in);
    $siang = !is_null($this->todayAttendance->absen_siang);
    $sore = !is_null($this->todayAttendance->check_out);

    $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
    $percentage = round(($completed / 3) * 100);

    return [
        'pagi' => $pagi,
        'siang' => $siang,
        'sore' => $sore,
        'percentage' => $percentage
    ];
}
```

## User Experience

### 1. Tampilan Dashboard
- Widget menampilkan progress absensi hari ini
- Quick action button untuk sesi yang perlu dilakukan
- Statistik bulanan untuk dinas luar

### 2. Halaman Absensi
- Informasi sesi yang sedang aktif
- Progress bar dengan visual indicator
- Camera interface yang sama dengan WFO
- Location tracking tanpa validasi radius

### 3. Notifications
- Feedback langsung setelah setiap sesi absensi
- Status keterlambatan untuk absen pagi
- Reminder untuk sesi berikutnya

## JavaScript Integration

### Location Tracking
```javascript
function getCurrentLocation() {
    // Mendapatkan GPS coordinates
    // Menampilkan informasi lokasi
    // Tidak ada validasi radius
}

function updateLocationStatus() {
    // Update UI dengan informasi lokasi
    // Menampilkan latitude/longitude
    // Konfirmasi bahwa lokasi akan dicatat
}
```

### Camera Management
- Sama dengan implementasi WFO
- Photo capture dan compression
- Preview dan retake functionality

### Dynamic Action Handling
```javascript
function submitAttendance() {
    // Menentukan method berdasarkan currentAction
    switch(currentAction) {
        case 'pagi': methodName = 'processCheckInPagi'; break;
        case 'siang': methodName = 'processCheckInSiang'; break;
        case 'sore': methodName = 'processCheckOut'; break;
    }
    // Call appropriate Livewire method
}
```

## Testing Scenarios

### Scenario 1: Absensi Pagi Tepat Waktu
- Waktu: 08:15
- Expected: Status = "Tepat Waktu"
- Next Action: Absen Siang tersedia

### Scenario 2: Absensi Pagi Terlambat
- Waktu: 08:45
- Expected: Status = "Terlambat"
- Next Action: Absen Siang tersedia

### Scenario 3: Progress Lengkap
- Absen Pagi: ✅ 08:20
- Absen Siang: ✅ 12:30
- Absen Sore: ✅ 17:15
- Expected: Progress = 100%, Status = Selesai

### Scenario 4: Progress Parsial
- Absen Pagi: ✅ 08:20
- Absen Siang: ❌ Belum
- Absen Sore: ❌ Belum
- Expected: Progress = 33%, Next Action = Absen Siang

## Integrasi dengan Sistem Existing

### 1. Model Attendance
- Menggunakan model yang sama dengan WFO
- Field `attendance_type` membedakan 'WFO' dan 'Dinas Luar'
- Accessor `status_kehadiran` tetap berfungsi

### 2. Navigation Menu
- Menu terpisah dari WFO
- Icon: `heroicon-o-map-pin`
- Navigation Group: 'Absensi'
- Sort Order: 2 (setelah WFO)

### 3. Widget Integration
- Widget terpisah untuk dinas luar
- Dapat ditampilkan bersamaan dengan widget WFO
- Column span: full width

## Benefits

1. **Flexibility**: Tidak terikat lokasi kantor
2. **Accountability**: 3 titik check-in per hari
3. **Transparency**: GPS tracking untuk semua sesi
4. **Visual Progress**: Clear indication of completion
5. **Real-time Status**: Immediate feedback on attendance status
6. **Consistent UX**: Interface mirip dengan WFO tapi disesuaikan

## Files Created/Modified

### New Files:
- `app/Filament/Pegawai/Pages/DinaslLuarAttendance.php`
- `resources/views/filament/pegawai/pages/dinas-luar-attendance.blade.php`
- `app/Filament/Pegawai/Widgets/DinaslLuarAttendanceStatusWidget.php`
- `resources/views/filament/pegawai/widgets/dinas-luar-attendance-status-widget.blade.php`

### Database:
- Menggunakan tabel `attendances` existing
- Field `attendance_type = 'Dinas Luar'` untuk membedakan

## Conclusion

Implementasi Absensi Dinas Luar menyediakan solusi komprehensif untuk tracking kehadiran pegawai yang bertugas di luar kantor. Dengan 3 sesi absensi per hari, GPS tracking, dan interface yang user-friendly, sistem ini memastikan akuntabilitas dan fleksibilitas yang dibutuhkan untuk pekerjaan dinas luar.
