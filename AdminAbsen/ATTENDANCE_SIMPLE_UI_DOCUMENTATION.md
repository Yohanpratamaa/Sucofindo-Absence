# TAMPILAN ABSENSI SEDERHANA MENGGUNAKAN FILAMENT

## Overview
Tampilan menu absensi yang sederhana namun fungsional dengan menggunakan Filament untuk sistem absensi pegawai.

## Fitur Utama

### 1. Pemilihan Jenis Absensi
- **WFO (Work From Office)**: 2 tahap (Check In & Check Out)
- **Dinas Luar**: 3 tahap (Pagi, Siang, Sore)
- Radio button untuk memilih jenis absensi
- Otomatis terkunci setelah melakukan absensi pertama

### 2. Status Absensi Real-time
- Menampilkan waktu absensi yang sudah dilakukan
- Progress bar untuk menunjukkan kemajuan absensi harian
- Status visual dengan warna berbeda untuk setiap tahap

### 3. Informasi Jadwal (Khusus Dinas Luar)
- **Pagi**: Kapan saja (selalu aktif)
- **Siang**: 12:00 - 14:59
- **Sore**: ≥ 15:00
- Indikator status aktif/tidak aktif berdasarkan waktu

### 4. Form Absensi dengan Kamera
- Integrasi kamera untuk selfie
- Validasi lokasi GPS otomatis
- Preview foto sebelum submit
- Tombol foto ulang jika tidak puas

## Komponen UI

### Header Section
```blade
<!-- Pemilihan jenis absensi dengan radio button -->
<div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
    <h2>Pilih Jenis Absensi</h2>
    <input type="radio" wire:model.live="attendanceType" value="WFO">
    <input type="radio" wire:model.live="attendanceType" value="Dinas Luar">
</div>
```

### Status Display
```blade
<!-- Grid layout untuk menampilkan status absensi -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Cards untuk setiap tahap absensi -->
</div>

<!-- Progress bar -->
<div class="w-full bg-gray-200 rounded-full h-2">
    <div class="bg-blue-600 h-2 rounded-full"></div>
</div>
```

### Camera Integration
```blade
<!-- Video element untuk kamera -->
<video id="camera" autoplay muted class="w-full rounded-lg"></video>
<canvas id="snapshot" style="display: none;"></canvas>

<!-- Tombol kontrol kamera -->
<button id="startCamera">Buka Kamera</button>
<button id="takePhoto">Ambil Foto</button>
<button id="submitAttendance">Submit Absensi</button>
```

## Logika Bisnis

### WFO (Work From Office)
1. **Check In**: Kapan saja, validasi lokasi dalam radius kantor
2. **Check Out**: Setelah jam 15:00, tidak perlu validasi lokasi

### Dinas Luar
1. **Pagi**: Kapan saja, tanpa validasi lokasi
2. **Siang**: 12:00-14:59, tanpa validasi lokasi
3. **Sore**: Setelah jam 15:00, tanpa validasi lokasi

### Validasi
- Lokasi GPS otomatis diambil sebelum membuka kamera
- Foto wajib untuk semua jenis absensi
- Satu kali submit per tahap per hari
- Jenis absensi terkunci setelah absensi pertama

## JavaScript Functionality

### Camera Control
```javascript
function startCamera() {
    // 1. Ambil lokasi GPS
    // 2. Buka kamera setelah lokasi didapat
    // 3. Tampilkan preview
}

function takePhoto() {
    // 1. Capture dari video ke canvas
    // 2. Convert ke base64
    // 3. Tampilkan preview foto
    // 4. Stop camera stream
}

function submitAttendance() {
    // 1. Validasi foto dan lokasi
    // 2. Call Livewire method dengan data
    // 3. Handle response
    // 4. Reset form
}
```

### Error Handling
- Alert untuk error kamera
- Alert untuk error lokasi
- Loading state saat submit
- Prevent double submission

## File Structure

### Backend
- `app/Filament/Pegawai/Pages/AttendancePage.php` - Controller utama
- `app/Models/Attendance.php` - Model data absensi
- `app/Models/Office.php` - Model data kantor

### Frontend
- `resources/views/filament/pegawai/pages/attendance-simple.blade.php` - View utama

### Methods Yang Diperlukan
```php
// AttendancePage.php
public function getCurrentAction()           // Aksi saat ini
public function getActionTitle()            // Title untuk UI
public function getTimeWindowInfo()         // Info jadwal waktu
public function getAttendanceProgress()     // Progress absensi
public function getOffices()               // Data kantor untuk validasi

// Processing methods
public function processCheckIn()           // WFO check in
public function processCheckOut()          // WFO/Dinas Luar check out
public function processCheckInPagi()       // Dinas Luar pagi
public function processCheckInSiang()      // Dinas Luar siang
```

## Kelebihan Tampilan Ini

### ✅ Kesederhanaan
- Layout bersih dan tidak rumit
- Satu halaman untuk semua fitur
- Navigasi yang intuitif

### ✅ Responsive Design
- Grid system yang responsif
- Mobile-friendly interface
- Touch-friendly buttons

### ✅ Real-time Updates
- Livewire untuk update otomatis
- Progress tracking langsung
- Status feedback instan

### ✅ User Experience
- Visual feedback yang jelas
- Error handling yang baik
- Loading states yang informatif

### ✅ Accessibility
- Semantic HTML
- ARIA labels pada tombol
- Keyboard navigation support

## Testing Checklist

- [ ] Pemilihan jenis absensi berfungsi
- [ ] Radio button terkunci setelah absensi
- [ ] Kamera dapat dibuka dan menutup
- [ ] Foto dapat diambil dan preview
- [ ] Lokasi GPS terdeteksi
- [ ] Submit absensi berhasil
- [ ] Progress bar update otomatis
- [ ] Validasi waktu berfungsi
- [ ] Error handling bekerja
- [ ] Responsive di mobile

## Status
✅ **SELESAI** - Tampilan absensi sederhana dan fungsional menggunakan Filament

Tampilan ini mengutamakan kemudahan penggunaan sambil tetap mempertahankan semua fungsionalitas yang diperlukan untuk sistem absensi pegawai.
