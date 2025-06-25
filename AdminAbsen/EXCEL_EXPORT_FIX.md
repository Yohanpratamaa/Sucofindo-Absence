# Excel Export Fix Documentation

## Masalah yang Diperbaiki

### 1. Error pada Export Excel

**Masalah**: Export Excel tidak berfungsi dan menghasilkan file yang tidak bisa dibuka.

**Penyebab**:

-   Penggunaan `Excel::raw()` dengan `response()->streamDownload()` yang tidak kompatibel
-   Relasi pada form menggunakan `relationship()` yang tidak valid untuk model Pegawai
-   Method download yang tidak tepat

**Solusi**:

-   Mengganti `Excel::download()` menjadi metode `Excel::raw()` dengan temporary file
-   Menggunakan `response()->download()` dengan file sementara
-   Mengganti `relationship()` menjadi `options()` dengan `Pegawai::pluck()`

### 2. Error pada Perhitungan Durasi Kerja

**Masalah**: Durasi kerja menampilkan "0 jam" atau nilai negatif.

**Penyebab**:

-   Urutan parameter pada `diffInMinutes()` yang salah
-   `$checkOut->diffInMinutes($checkIn)` menghasilkan nilai negatif

**Solusi**:

-   Mengganti urutan parameter menjadi `$checkIn->diffInMinutes($checkOut)`
-   Memperbaiki logika perhitungan dengan pengecekan absen siang
-   Menambahkan format yang lebih baik untuk output durasi

### 3. Error Handling yang Kurang

**Masalah**: Tidak ada error handling yang proper saat export gagal.

**Solusi**:

-   Menambahkan try-catch pada action export
-   Menampilkan notifikasi error yang user-friendly menggunakan Filament
-   Validasi data sebelum proses export

## Perubahan yang Dilakukan

### File: `app/Filament/Resources/AttendanceResource/Pages/ListAttendances.php`

1. **Perbaikan Export Excel Action**:

```php
// SEBELUM
return Excel::download(new AttendanceExport(...), $filename);

// SESUDAH
$export = new AttendanceExport($data['start_date'], $data['end_date'], $data['user_id'] ?? null);
$tempFile = tempnam(sys_get_temp_dir(), 'attendance_export_');
$content = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
file_put_contents($tempFile, $content);
return response()->download($tempFile, $filename, [...])->deleteFileAfterSend(true);
```

2. **Perbaikan Form Selection**:

```php
// SEBELUM
Forms\Components\Select::make('user_id')
    ->relationship('user', 'nama')

// SESUDAH
Forms\Components\Select::make('user_id')
    ->options(\App\Models\Pegawai::pluck('nama', 'id'))
```

3. **Penambahan Error Handling**:

```php
try {
    // export logic
} catch (\Exception $e) {
    \Filament\Notifications\Notification::make()
        ->title('Export Error')
        ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
        ->danger()
        ->send();
    return null;
}
```

### File: `app/Exports/AttendanceExport.php`

1. **Perbaikan Method Duration**:

```php
// SEBELUM
$durationMinutes = $checkOut->diffInMinutes($checkIn) - 60;

// SESUDAH
$totalMinutes = $checkIn->diffInMinutes($checkOut);
if ($attendance->absen_siang) {
    $totalMinutes = max(0, $totalMinutes - 60);
}
```

2. **Peningkatan Format Output**:

```php
if ($hours > 0 && $minutes > 0) {
    return $hours . ' jam ' . $minutes . ' menit';
} elseif ($hours > 0) {
    return $hours . ' jam';
} else {
    return $minutes . ' menit';
}
```

### File: `app/Models/Attendance.php`

1. **Perbaikan Accessor `getDurasiKerjaAttribute()`**:

-   Memperbaiki urutan parameter `diffInMinutes`
-   Menambahkan logika yang sama dengan export class
-   Memberikan format output yang konsisten

## Testing

### Test yang Dilakukan:

1. **Basic Excel Functionality Test**: ✅ Berhasil
2. **AttendanceExport Class Test**: ✅ Berhasil
3. **Duration Calculation Test**: ✅ Berhasil
4. **File Generation Test**: ✅ Berhasil (9565 bytes)
5. **Sample Data Test**: ✅ Durasi: "8 jam 29 menit"

### Hasil Test:

-   File Excel berhasil dibuat dengan ukuran yang sesuai
-   Header file valid (504b0304 - ZIP signature untuk XLSX)
-   Durasi kerja dihitung dengan benar
-   Data mapping berfungsi dengan baik

## Status Perbaikan

✅ **SELESAI**: Export Excel sudah berfungsi dengan baik
✅ **SELESAI**: Perhitungan durasi kerja sudah benar  
✅ **SELESAI**: Error handling sudah ditambahkan
✅ **SELESAI**: Form selection sudah diperbaiki

## Cara Penggunaan

1. Buka halaman Attendance di panel admin
2. Klik tombol "Export Semua ke Excel"
3. Pilih tanggal mulai dan selesai
4. (Opsional) Pilih karyawan tertentu
5. Klik "Export" - file akan otomatis terdownload

## Notes

-   File Excel akan otomatis terdownload dengan nama format: `laporan_absensi_YYYY-MM-DD_to_YYYY-MM-DD.xlsx`
-   Durasi kerja akan otomatis mengurangi 1 jam istirahat jika ada data absen siang
-   Export mendukung filter berdasarkan periode dan karyawan tertentu
-   File sementara akan otomatis dihapus setelah download selesai
