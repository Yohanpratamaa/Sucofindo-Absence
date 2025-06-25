# Custom Dashboard Documentation

## Overview

Dokumentasi ini menjelaskan cara menghilangkan branding default Filament dan membuat dashboard custom untuk sistem Smart Absens.

## Perubahan yang Dilakukan

### 1. Custom Dashboard Page

**File**: `app/Filament/Pages/Dashboard.php`

Dashboard page custom yang menggantikan default Filament dashboard dengan fitur:

-   Custom view template
-   Menghilangkan FilamentInfoWidget
-   Hanya menampilkan AccountWidget

### 2. Custom Dashboard View

**File**: `resources/views/filament/pages/dashboard.blade.php`

Template Blade custom dengan fitur:

-   **Statistik Cards**: Total karyawan, absensi hari ini, dan bulan ini
-   **Welcome Section**: Menampilkan nama user yang login
-   **Recent Attendance**: 5 absensi terbaru dengan status
-   **Quick Actions**: Link cepat ke menu utama
-   **System Info**: Informasi sistem tanpa branding Filament
-   **Custom CSS**: Menyembunyikan semua elemen branding Filament

### 3. AdminPanelProvider Updates

**File**: `app/Providers/Filament/AdminPanelProvider.php`

Perubahan:

-   Menghapus `Widgets\FilamentInfoWidget::class` dari widgets
-   Menggunakan custom dashboard page `\App\Filament\Pages\Dashboard::class`
-   Menambahkan custom CSS via `renderHook` untuk menyembunyikan branding

### 4. FilamentServiceProvider Updates

**File**: `app/Providers/FilamentServiceProvider.php`

Perubahan:

-   Menambahkan custom CSS untuk menyembunyikan branding
-   Custom navigation setup
-   Render hook untuk inject CSS

### 5. Custom CSS

**File**: `resources/css/custom-dashboard.css`

CSS khusus untuk:

-   Menyembunyikan widget FilamentInfo
-   Menyembunyikan footer Filament
-   Menyembunyikan link GitHub dan Documentation
-   Custom styling untuk dashboard cards

## Fitur Dashboard Custom

### Statistics Cards

1. **Welcome Card**: Menampilkan nama user yang sedang login
2. **Total Karyawan**: Jumlah pegawai di database
3. **Absensi Hari Ini**: Jumlah absensi untuk hari ini
4. **Absensi Bulan Ini**: Jumlah absensi untuk bulan berjalan

### Recent Attendance Section

-   Menampilkan 5 absensi terbaru
-   Menampilkan nama, tanggal/waktu, dan status kehadiran
-   Link ke halaman manajemen absensi lengkap

### Quick Actions Section

-   Link cepat ke Manajemen Pegawai
-   Link cepat ke Manajemen Absensi
-   Link cepat ke Manajemen Izin (jika tersedia)
-   Link cepat ke Penugasan Lembur (jika tersedia)

### System Information

-   Nama sistem: "Sistem Absensi Sucofindo"
-   Versi Laravel
-   Versi PHP
-   Tanpa branding Filament

## Elemen yang Disembunyikan

### CSS Selectors yang Digunakan:

```css
/* Widget Filament Info */
.fi-widget[data-widget="filament-widgets-filament-info-widget"],
.filament-widgets-filament-info-widget,
.fi-wi-info {
    display: none !important;
}

/* Links Filament */
a[href*="filamentphp.com"],
a[href*="github.com/filamentphp"] {
    display: none !important;
}

/* Footer */
.fi-footer,
.fi-simple-footer {
    display: none !important;
}
```

## Data yang Ditampilkan

### Database Queries:

```php
// Total Pegawai
\App\Models\Pegawai::count()

// Absensi Hari Ini
\App\Models\Attendance::today()->count()

// Absensi Bulan Ini
\App\Models\Attendance::thisMonth()->count()

// Absensi Terbaru
\App\Models\Attendance::with('user')
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get()
```

## Penggunaan

### Mengakses Dashboard:

1. Login ke admin panel
2. Dashboard custom akan muncul otomatis tanpa branding Filament
3. Semua link dan statistik berfungsi secara real-time

### Modifikasi:

-   Edit `resources/views/filament/pages/dashboard.blade.php` untuk mengubah layout
-   Edit `app/Filament/Pages/Dashboard.php` untuk mengubah logika
-   Edit CSS di view untuk mengubah styling

## Testing

### Cara Test:

1. Jalankan server: `php artisan serve`
2. Buka admin panel di browser
3. Verifikasi tidak ada tulisan "filament", "v3.3.28", atau link GitHub/Documentation
4. Pastikan semua statistik menampilkan data yang benar
5. Test semua link quick actions

### Checklist:

-   ✅ Dashboard menampilkan custom content
-   ✅ Tidak ada widget FilamentInfo
-   ✅ Tidak ada link Filament di topbar
-   ✅ Tidak ada footer Filament
-   ✅ Statistik menampilkan data real
-   ✅ Quick actions berfungsi
-   ✅ Recent attendance tampil
-   ✅ System info tanpa branding

## Maintenance

### File yang Perlu Diperhatikan:

1. `app/Filament/Pages/Dashboard.php` - Logic dashboard
2. `resources/views/filament/pages/dashboard.blade.php` - Template
3. `app/Providers/Filament/AdminPanelProvider.php` - Panel config
4. `resources/css/custom-dashboard.css` - Styling

### Update Filament:

Jika update Filament, perhatikan:

-   Selector CSS mungkin berubah
-   Widget structure mungkin berubah
-   Perlu adjust custom CSS sesuai versi baru

## Troubleshooting

### Jika Branding Masih Muncul:

1. Clear cache: `php artisan config:clear && php artisan view:clear`
2. Check browser cache (hard refresh)
3. Pastikan CSS injection berhasil (inspect element)
4. Verify FilamentInfoWidget tidak terdaftar di widgets

### Jika Statistik Tidak Muncul:

1. Check database connection
2. Verify model relationships
3. Check console untuk error JavaScript
4. Pastikan data seeder sudah dijalankan
