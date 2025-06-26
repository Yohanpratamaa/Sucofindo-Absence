# Export PDF Implementation - Manajemen Absensi & Rekap Absensi

## Overview
Implementasi button export PDF untuk dua menu utama dalam sistem AdminAbsen:

1. **Manajemen Absensi** - Export Detail Absensi
2. **Rekap Absensi** - Export Rekap/Summary Absensi

## 1. Manajemen Absensi (AttendanceResource)

### Location
- **Resource**: `app/Filament/Resources/AttendanceResource.php`
- **Page**: `app/Filament/Resources/AttendanceResource/Pages/ListAttendances.php`
- **Template**: `resources/views/exports/attendance-pdf.blade.php`

### Export Button Features
**Button Name**: "Export Detail ke PDF"
- **Icon**: `heroicon-o-document-text`
- **Color**: `danger` (red)
- **Function**: Export detailed attendance records

### Form Filters
1. **Dari Tanggal** (required) - Start date
2. **Sampai Tanggal** (required) - End date  
3. **Karyawan** (optional) - Specific employee filter
4. **Tipe Absensi** (optional) - WFO or Dinas Luar

### PDF Content
- **Title**: Detail Data Absensi
- **Orientation**: Landscape (A4)
- **Headers**: Tanggal, Nama Karyawan, NPP, Jabatan, Check In, Check Out, Durasi Kerja, Status Kehadiran, Tipe Absensi
- **Data**: Individual attendance records with detailed information
- **Summary**: Total records, work days, employee name (if filtered), total attendance

### File Naming
```
detail_absensi_YYYY-MM-DD_to_YYYY-MM-DD.pdf
```

## 2. Rekap Absensi (AttendanceReportResource)

### Location
- **Resource**: `app/Filament/Resources/AttendanceReportResource.php`
- **Page**: `app/Filament/Resources/AttendanceReportResource/Pages/ListAttendanceReports.php`
- **Template**: `resources/views/exports/attendance-report-pdf.blade.php`

### Export Button Features

#### Primary Button: "Export Rekap ke PDF"
- **Icon**: `heroicon-o-document-text`
- **Color**: `danger` (red)
- **Function**: Export attendance summary/recap

#### Quick Button: "Export Bulan Ini"
- **Icon**: `heroicon-o-calendar-days`
- **Color**: `success` (green)
- **Function**: Quick export for current month

### Form Filters (Primary Export)
1. **Dari Tanggal** (required) - Start date
2. **Sampai Tanggal** (required) - End date
3. **Filter Jabatan** (optional) - Position/role filter

### PDF Content
- **Title**: Rekap Absensi Karyawan
- **Orientation**: Landscape (A4)
- **Headers**: Nama Karyawan, NPP, Jabatan, Total Hadir, Total Terlambat, Tidak Check Out, Total Lembur (Jam), Tingkat Kehadiran (%)
- **Data**: Summary statistics per employee
- **Summary**: Total employees, work days, average attendance percentage

### File Naming
```
# Regular export
rekap_absensi_YYYY-MM-DD_to_YYYY-MM-DD.pdf

# Quick export (current month)
rekap_absensi_MonthName_YYYY.pdf
```

## Technical Implementation

### Dependencies Required
```php
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
```

### Key Methods Added

#### ListAttendances.php
```php
- getDurationWork($attendance) - Calculate work duration
- getAttendanceStatus($attendance) - Determine attendance status
- getWorkDaysInPeriod($startDate, $endDate) - Count working days
```

#### ListAttendanceReports.php
```php
- getWorkDaysInPeriod($startDate, $endDate) - Count working days
```

### Error Handling
- Try-catch blocks for PDF generation
- User notifications for success/error states
- Proper exception messages

### Data Processing
- Dynamic query building based on filters
- Proper data formatting for PDF templates
- Summary statistics calculation
- Working days calculation (excluding weekends)

## Usage Instructions

### For Manajemen Absensi
1. Navigate to **Manajemen Absensi** menu
2. Click **"Export Detail ke PDF"** button
3. Fill in required date range
4. Optionally filter by employee or attendance type
5. Click submit to generate and download PDF

### For Rekap Absensi
1. Navigate to **Rekap Absensi** menu
2. Choose either:
   - **"Export Rekap ke PDF"** - Custom date range and filters
   - **"Export Bulan Ini"** - Quick export for current month
3. Fill in required fields (if custom export)
4. Click submit to generate and download PDF

## PDF Features
- Professional styling with company branding
- Color-coded status indicators
- Comprehensive data tables
- Summary statistics
- Proper headers and footers
- Responsive layout for print

## File Structure
```
app/Filament/Resources/
├── AttendanceResource/
│   └── Pages/
│       └── ListAttendances.php (Detail Export)
├── AttendanceReportResource/
│   └── Pages/
│       └── ListAttendanceReports.php (Recap Export)

resources/views/exports/
├── attendance-pdf.blade.php (Detail Template)
└── attendance-report-pdf.blade.php (Recap Template)
```

## Security & Performance
- Input validation on date ranges
- Proper error handling
- Memory-efficient PDF generation
- Stream download for large files
- User permission checks through Filament
