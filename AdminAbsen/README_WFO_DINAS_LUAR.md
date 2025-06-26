# Implementasi Aturan WFO vs Dinas Luar - Sistem Absensi Sucofindo

## Overview
Sistem absensi ini telah diimplementasikan dengan aturan yang berbeda untuk pegawai WFO (Work From Office) dan Dinas Luar, sesuai dengan requirement yang diminta.

## Aturan Absensi

### WFO (Work From Office)
- **Absensi Required**: Check In + Check Out (2 kali absen)
- **Absen Siang**: TIDAK diperlukan (`absen_siang` = `null`)
- **Lokasi**: Harus berada di area kantor (dalam radius yang ditentukan)
- **Foto**: Wajib untuk Check In dan Check Out
- **GPS Coordinates**: Harus berada dalam radius kantor

### Dinas Luar
- **Absensi Required**: Check In + Absen Siang + Check Out (3 kali absen)
- **Absen Siang**: WAJIB (`absen_siang` wajib diisi)
- **Lokasi**: Fleksibel sesuai penugasan (tidak terikat lokasi kantor)
- **Foto**: Wajib untuk semua 3 absen (Check In, Siang, Check Out)
- **GPS Coordinates**: Fleksibel di mana saja sesuai tempat penugasan

## Implementasi Database

### Model Attendance
File: `app/Models/Attendance.php`

**Helper Methods yang telah ditambahkan:**
- `requiresAbsenSiang()`: Mengecek apakah attendance type memerlukan absen siang
- `isValidAttendance()`: Mengecek validitas absensi berdasarkan type
- `getKelengkapanAbsensiAttribute()`: Menghitung kelengkapan absensi
- `getAbsensiRequirementAttribute()`: Menampilkan requirement absensi

### Database Seeder

#### OfficeSeeder
- Membuat hanya 1 kantor: Sucofindo Bandung
- Koordinat: `-6.9431000, 107.5851494`
- Radius: 300 meter

#### AttendanceSeeder
- Generate data WFO: 70% probabilitas, koordinat dalam radius kantor, tidak ada absen siang
- Generate data Dinas Luar: 30% probabilitas, koordinat fleksibel, WAJIB absen siang

#### AttendanceTestSeeder
Membuat 4 test cases khusus:
1. **WFO Lengkap**: Check In + Check Out (lokasi kantor)
2. **WFO Tidak Lengkap**: Hanya Check In
3. **Dinas Luar Lengkap**: Check In + Siang + Check Out (lokasi fleksibel)
4. **Dinas Luar Tidak Lengkap**: Check In + Siang (tanpa Check Out)

## Implementasi UI (Filament)

### AttendanceResource
File: `app/Filament/Resources/AttendanceResource.php`

**Form Features:**
- **Dynamic Sections**: Section absen siang collapse untuk WFO
- **Helper Text**: Menjelaskan requirement untuk setiap tipe
- **Visual Indicators**: Emoji dan warna untuk membedakan WFO vs Dinas Luar
- **Information Display**: Menampilkan requirement, kelengkapan, dan status

**Table Features:**
- **Badge Columns**: Tipe attendance dengan warna berbeda
- **Kelengkapan Status**: Menampilkan progress absensi (2/2 untuk WFO, 3/3 untuk Dinas Luar)
- **Location Info**: Menampilkan informasi lokasi dan jarak dari kantor
- **Smart Filters**: Filter kelengkapan berdasarkan aturan masing-masing tipe

## Testing

### Jalankan Test Seeder
```bash
php artisan db:seed --class=AttendanceTestSeeder
```

### Verifikasi di Admin Panel
1. Akses: `http://localhost:8000/admin`
2. Login dengan credentials admin
3. Navigate ke "Manajemen Absensi"
4. Lihat data test cases yang sudah dibuat

## Validasi Aturan

### WFO Validation
- ✅ Check In wajib
- ✅ Check Out wajib
- ❌ Absen Siang tidak diperlukan
- ✅ Lokasi harus di area kantor
- ✅ Foto wajib untuk Check In dan Check Out

### Dinas Luar Validation
- ✅ Check In wajib
- ✅ Absen Siang wajib
- ✅ Check Out wajib
- ✅ Lokasi fleksibel
- ✅ Foto wajib untuk semua 3 absen

## File Structure

```
AdminAbsen/
├── app/
│   ├── Models/
│   │   └── Attendance.php (Helper methods & business logic)
│   └── Filament/Resources/
│       └── AttendanceResource.php (UI & validation)
├── database/
│   └── seeders/
│       ├── OfficeSeeder.php (Kantor data)
│       ├── OfficeScheduleSeeder.php (Jadwal kantor)
│       ├── AttendanceSeeder.php (Data sample)
│       └── AttendanceTestSeeder.php (Test cases)
└── README_WFO_DINAS_LUAR.md (This file)
```

## Status Implementasi

✅ **COMPLETED:**
- Aturan WFO: Check In + Check Out only
- Aturan Dinas Luar: Check In + Siang + Check Out
- Lokasi kantor untuk WFO
- Lokasi fleksibel untuk Dinas Luar
- Foto wajib sesuai requirement
- UI yang menampilkan perbedaan aturan
- Database seeder dengan test cases
- Validasi kelengkapan berdasarkan tipe

Sistem ini siap untuk production dan telah ditest dengan berbagai skenario.
