# Fitur Export PDF dan Excel - Manajemen Izin & Lembur

## Overview
Sistem absensi Sucofindo telah dilengkapi dengan fitur export data dalam format PDF dan Excel untuk:
- **Manajemen Izin** (IzinResource)
- **Manajemen Lembur** (OvertimeAssignmentResource)

## Implementasi Export

### 1. Export Excel

#### IzinExport Class
File: `app/Exports/IzinExport.php`

**Fitur:**
- Export data izin dengan filter periode, karyawan, jenis izin, dan status
- Format Excel dengan styling header
- Column widths yang optimal
- Mapping data dengan format Indonesia

**Kolom Export:**
1. Tanggal Pengajuan
2. Nama Karyawan
3. NPP
4. Jabatan
5. Jenis Izin
6. Tanggal Mulai
7. Tanggal Akhir
8. Durasi (Hari)
9. Keterangan
10. Status
11. Disetujui Oleh
12. Tanggal Disetujui
13. Info Persetujuan

#### OvertimeAssignmentExport Class
File: `app/Exports/OvertimeAssignmentExport.php`

**Fitur:**
- Export data lembur dengan filter periode, karyawan, status, dan assigner
- Format Excel dengan styling header hijau
- Optimized column widths
- Comprehensive data mapping

**Kolom Export:**
1. Waktu Penugasan
2. Nama Karyawan
3. NPP
4. Jabatan
5. ID Lembur
6. Ditugaskan Oleh
7. Status
8. Durasi Assignment
9. Disetujui Oleh
10. Tanggal Disetujui
11. Di-assign Ulang Oleh
12. Info Persetujuan

### 2. Export PDF

#### Template Izin PDF
File: `resources/views/exports/izin-pdf.blade.php`

**Fitur:**
- Layout responsive dengan styling CSS
- Header dengan informasi periode dan total data
- Tabel data dengan warna status
- Summary/ringkasan data di bawah tabel
- Footer dengan timestamp dan nama perusahaan

**Color Coding:**
- Status Pending: Orange
- Status Approved: Green
- Status Rejected: Red
- Jenis Sakit: Orange
- Jenis Cuti: Blue
- Jenis Izin: Cyan

#### Template Lembur PDF
File: `resources/views/exports/overtime-pdf.blade.php`

**Fitur:**
- Professional layout dengan CSS styling
- Comprehensive header information
- Color-coded status indicators
- Summary statistics
- Company branding

## Implementasi UI (Filament)

### IzinResource - ListIzins Page
File: `app/Filament/Resources/IzinResource/Pages/ListIzins.php`

**Export Actions:**
1. **Export ke Excel**
   - Icon: document-arrow-down (green)
   - Form filters: periode, karyawan, jenis izin, status
   - Download langsung file Excel

2. **Export ke PDF**
   - Icon: document-text (red)
   - Form filters: periode, karyawan, jenis izin, status
   - Stream download PDF

### OvertimeAssignmentResource - ListOvertimeAssignments Page
File: `app/Filament/Resources/OvertimeAssignmentResource/Pages/ListOvertimeAssignments.php`

**Export Actions:**
1. **Export ke Excel**
   - Icon: document-arrow-down (green)
   - Form filters: periode, karyawan, status, assigned_by
   - Download langsung file Excel

2. **Export ke PDF**
   - Icon: document-text (red)
   - Form filters: periode, karyawan, status, assigned_by
   - Stream download PDF

## Form Filter Export

### Common Filters
- **Dari Tanggal**: Default awal bulan ini
- **Sampai Tanggal**: Default akhir bulan ini
- **Karyawan**: Dropdown semua pegawai (opsional)

### Izin-Specific Filters
- **Jenis Izin**: Sakit, Cuti, Izin Khusus (opsional)
- **Status**: Menunggu, Disetujui, Ditolak (opsional)

### Lembur-Specific Filters
- **Status**: Ditugaskan, Diterima, Ditolak (opsional)
- **Ditugaskan Oleh**: Dropdown semua pegawai (opsional)

## Filename Convention

### Excel Files
- Izin: `laporan_izin_YYYY-MM-DD_to_YYYY-MM-DD.xlsx`
- Lembur: `laporan_lembur_YYYY-MM-DD_to_YYYY-MM-DD.xlsx`

### PDF Files
- Izin: `laporan_izin_YYYY-MM-DD_to_YYYY-MM-DD.pdf`
- Lembur: `laporan_lembur_YYYY-MM-DD_to_YYYY-MM-DD.pdf`

## Error Handling

### Try-Catch Implementation
- Semua export actions wrapped dalam try-catch
- Error notifications dengan Filament Notification
- User-friendly error messages
- Graceful fallback (return null)

### Common Error Scenarios
- Invalid date range
- No data found
- Memory limit exceeded (large datasets)
- File permission issues

## Testing Data

### Izin Sample Data
```bash
php artisan db:seed --class=IzinSeeder
```
- 10 data izin sample
- 5 pending, 3 approved, 2 rejected
- Berbagai jenis izin (sakit, cuti, izin khusus)

### Lembur Sample Data
```bash
php artisan db:seed --class=OvertimeAssignmentSeeder
```
- 7 data lembur assignment
- Berbagai status (assigned, accepted, rejected)
- Multiple assigners dan assignees

## Usage Instructions

### 1. Access Export Features
1. Login ke admin panel: `http://localhost:8000/admin`
2. Navigate ke **Manajemen Izin** atau **Manajemen Lembur**
3. Klik tombol **Export ke Excel** atau **Export ke PDF** di header

### 2. Configure Export Parameters
1. Pilih **periode tanggal** (required)
2. Filter berdasarkan **karyawan** (optional)
3. Filter berdasarkan **kriteria spesifik** (optional)
4. Klik **Submit** untuk download

### 3. File Download
- Excel: Download langsung ke browser
- PDF: Stream download dengan viewer browser

## Dependencies

### Required Packages
- `maatwebsite/excel`: Excel export functionality
- `barryvdh/dompdf`: PDF generation
- `filament/filament`: UI framework

### Configuration Files
- `config/excel.php`: Excel configuration
- `config/dompdf.php`: PDF configuration

## Status Implementation

✅ **COMPLETED:**
- IzinExport class dengan full filtering
- OvertimeAssignmentExport class dengan full filtering
- PDF templates untuk kedua modul
- UI integration di Filament Resources
- Error handling dan notifications
- Sample data untuk testing
- Documentation lengkap

**Ready for Production Use!**
