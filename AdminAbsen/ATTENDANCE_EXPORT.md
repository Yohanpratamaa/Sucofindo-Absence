# ATTENDANCE EXPORT FEATURE

## Overview

Fitur ekspor laporan absensi memungkinkan Super Admin untuk mengekspor data absensi dalam format Excel (.xlsx) dan PDF untuk analisis offline. Tersedia dua jenis ekspor: data absensi detail dan rekap absensi.

## Fitur Export yang Tersedia

### 1. Export Data Absensi (AttendanceResource)

**Lokasi**: Menu "Data Absensi" > Header Actions

#### a. Export Semua ke Excel

-   **Button**: "Export Semua ke Excel" (hijau, icon download)
-   **Form Input**:
    -   Dari Tanggal (default: awal bulan)
    -   Sampai Tanggal (default: akhir bulan)
    -   Karyawan (opsional - jika kosong ekspor semua karyawan)
-   **Output**: File Excel (.xlsx) dengan data:
    -   Tanggal, Nama Karyawan, NPP, Jabatan
    -   Check In, Absen Siang, Check Out
    -   Durasi Kerja, Lembur, Status Kehadiran
    -   Tipe Absensi, Koordinat Lokasi
-   **Styling**: Header berwarna biru, kolom auto-width

#### b. Export Semua ke PDF

-   **Button**: "Export Semua ke PDF" (merah, icon document)
-   **Form Input**: Sama dengan Excel
-   **Output**: File PDF dengan:
    -   Header perusahaan dan periode
    -   Tabel data absensi yang rapi
    -   Ringkasan: total records, hari kerja
    -   Status kehadiran dengan color coding
    -   Footer dengan timestamp dan nomor halaman

### 2. Export Rekap Absensi (AttendanceReportResource)

**Lokasi**: Menu "Rekap Absensi" > Header Actions

#### a. Export Rekap ke Excel

-   **Button**: "Export Rekap ke Excel" (hijau, icon download)
-   **Form Input**:
    -   Dari Tanggal (default: awal bulan)
    -   Sampai Tanggal (default: akhir bulan)
-   **Output**: File Excel (.xlsx) dengan data rekap:
    -   Nama, NPP, Jabatan, Posisi
    -   Total Hadir, Total Terlambat, Total Tidak Checkout
    -   Total Lembur (jam), Rata-rata Kerja/Hari (jam)
    -   Tingkat Kehadiran (%), Status Karyawan

#### b. Export Rekap ke PDF

-   **Button**: "Export Rekap ke PDF" (merah, icon document)
-   **Form Input**: Sama dengan Excel
-   **Output**: File PDF dengan:
    -   Header rekap dan periode
    -   Tabel rekap statistik per karyawan
    -   Ringkasan: total karyawan, hari kerja, rata-rata kehadiran
    -   Tingkat kehadiran dengan color coding (hijau ≥90%, kuning ≥75%, merah <75%)

## Struktur File dan Package

### Dependencies

```json
{
    "maatwebsite/excel": "^3.1",
    "barryvdh/laravel-dompdf": "^3.1"
}
```

### Export Classes

```
app/Exports/
├── AttendanceExport.php          # Export data absensi detail
└── AttendanceReportExport.php    # Export rekap absensi
```

### PDF Views

```
resources/views/exports/
├── attendance-pdf.blade.php       # Template PDF data absensi
└── attendance-report-pdf.blade.php # Template PDF rekap absensi
```

### Config Files

```
config/
├── excel.php    # Konfigurasi Laravel Excel
└── dompdf.php   # Konfigurasi DomPDF
```

## Detail Implementation

### AttendanceExport.php

-   **Implements**: FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
-   **Features**:
    -   Filter berdasarkan tanggal dan user
    -   Header styling dengan background biru
    -   Auto column width
    -   Data mapping yang rapi

### AttendanceReportExport.php

-   **Implements**: Sama dengan AttendanceExport
-   **Features**:
    -   Query join dengan perhitungan statistik
    -   Kalkulasi tingkat kehadiran
    -   Format jam lembur dan durasi kerja

### PDF Templates

-   **Responsive design** dengan CSS yang rapi
-   **Color coding** untuk status dan tingkat kehadiran
-   **Summary section** dengan informasi penting
-   **Professional header** dengan logo perusahaan
-   **Footer** dengan timestamp dan pagination

## Cara Penggunaan

### Super Admin:

1. **Export Data Detail**:

    - Masuk ke menu "Data Absensi"
    - Klik "Export Semua ke Excel/PDF" di header
    - Pilih periode tanggal
    - Pilih karyawan (opsional)
    - Klik "Submit" untuk download

2. **Export Rekap**:
    - Masuk ke menu "Rekap Absensi"
    - Klik "Export Rekap ke Excel/PDF" di header
    - Pilih periode tanggal
    - Klik "Submit" untuk download

### File Naming Convention:

-   **Data Absensi**: `laporan_absensi_YYYY-MM-DD_to_YYYY-MM-DD.xlsx/.pdf`
-   **Rekap Absensi**: `rekap_absensi_YYYY-MM-DD_to_YYYY-MM-DD.xlsx/.pdf`

## Keamanan & Validasi

### Input Validation:

-   Tanggal mulai dan akhir wajib diisi
-   Validasi format tanggal
-   Sanitasi input untuk mencegah injection

### Access Control:

-   Hanya Super Admin yang bisa akses
-   Export dibatasi berdasarkan hak akses user
-   Logging aktivitas export (future enhancement)

### Performance:

-   Stream download untuk file besar
-   Memory efficient processing
-   Chunk processing untuk dataset besar (future enhancement)

## Troubleshooting

### Common Issues:

1. **Memory limit exceeded**: Tingkatkan memory_limit di php.ini
2. **Timeout**: Tingkatkan max_execution_time
3. **File tidak ter-download**: Periksa permission folder storage

### Error Handling:

-   Graceful error handling dengan notification
-   Fallback untuk data kosong
-   Validasi periode tanggal yang masuk akal

## Future Enhancements

### Planned Features:

1. **Email export**: Kirim laporan via email
2. **Scheduled exports**: Export otomatis berkala
3. **Multiple formats**: CSV, XML support
4. **Charts in PDF**: Grafik dan visualisasi
5. **Batch processing**: Export untuk data volume besar
6. **Custom templates**: Template PDF yang bisa dikustomisasi

## Technical Notes

### Excel Styling:

-   Header: Background #366092, font bold, white text
-   Auto-fit columns berdasarkan content
-   Freeze header row untuk navigasi mudah

### PDF Styling:

-   Font: Arial, 12px untuk content, 10px untuk header/footer
-   Page size: A4 landscape untuk tabel lebar
-   Color scheme: Konsisten dengan branding Sucofindo

### Performance Considerations:

-   Lazy loading untuk relasi
-   Efficient SQL queries dengan proper indexing
-   Stream processing untuk memory efficiency

## Status

✅ Excel export implemented with styling  
✅ PDF export with professional templates  
✅ Form validation and input filtering  
✅ Error handling and user feedback  
✅ Responsive design for PDF output  
✅ Security measures implemented  
✅ Documentation completed  
🔄 Ready for production use
