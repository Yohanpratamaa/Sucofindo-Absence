# Sistem Persetujuan Izin & Lembur - AdminAbsen

## Overview
Sistem persetujuan yang telah dikembangkan memungkinkan tracking lengkap siapa yang menyetujui atau menolak izin dan lembur berdasarkan akun yang login.

## Fitur Utama

### 1. **Tracking Persetujuan Otomatis**
- ✅ Setiap persetujuan/penolakan tercatat dengan ID user yang melakukan action
- ✅ Informasi lengkap tentang siapa yang menyetujui dan kapan
- ✅ Log aktivitas persetujuan ke system log

### 2. **Model Enhancements**

#### **Izin Model (`app/Models/Izin.php`)**
- `approve($approvedBy)` - Method untuk menyetujui izin dengan tracking user
- `reject($approvedBy)` - Method untuk menolak izin dengan tracking user
- `getApprovalInfoAttribute()` - Accessor untuk info lengkap persetujuan
- `getStatusBadgeAttribute()` - Accessor untuk badge status dengan info approver

#### **OvertimeAssignment Model (`app/Models/OvertimeAssignment.php`)**
- `accept($approvedBy)` - Method untuk menerima lembur dengan tracking user
- `reject($approvedBy)` - Method untuk menolak lembur dengan tracking user
- `reassign($newUserId, $assignBy)` - Method untuk assign ulang dengan tracking
- `getApprovalInfoAttribute()` - Accessor untuk info lengkap persetujuan
- `getStatusBadgeAttribute()` - Accessor untuk badge status dengan info approver

### 3. **Resource Improvements**

#### **IzinResource (`app/Filament/Resources/IzinResource.php`)**
- **Tabel Columns:**
  - Status badge dengan info approver
  - Kolom "Info Persetujuan" menampilkan siapa yang menyetujui dan kapan
  
- **Actions:**
  - Confirm dialog menampilkan nama user yang akan tercatat sebagai approver
  - Notifikasi menampilkan nama user yang melakukan approval
  - Bulk actions dengan info approver

- **ViewIzin Page:**
  - Infolist detail dengan section Status Persetujuan
  - Header actions untuk approve/reject langsung dari detail page
  - Info lengkap tentang siapa yang menyetujui

#### **OvertimeAssignmentResource (`app/Filament/Resources/OvertimeAssignmentResource.php`)**
- **Tabel Columns:**
  - Status badge dengan info approver
  - Kolom "Info Persetujuan" menampilkan siapa yang menyetujui dan kapan
  
- **Actions:**
  - Confirm dialog menampilkan nama user yang akan tercatat sebagai approver
  - Notifikasi menampilkan nama user yang melakukan approval

### 4. **Dashboard Widgets**

#### **ApprovalStatsWidget (`app/Filament/Widgets/ApprovalStatsWidget.php`)**
- Statistik total izin/lembur yang disetujui oleh user saat ini
- Statistik total izin/lembur yang ditolak oleh user saat ini
- Statistik pending yang perlu persetujuan
- Menampilkan nama user di description

#### **RecentApprovalActivityWidget (`app/Filament/Widgets/RecentApprovalActivityWidget.php`)**
- Tabel aktivitas persetujuan terbaru
- Menampilkan siapa yang memproses setiap persetujuan
- Link ke detail page untuk info lengkap

### 5. **System Logging**
- Setiap approval/rejection dicatat ke system log dengan format:
  ```
  Izin ID {ID} disetujui oleh {Nama} (ID: {UserID}) pada {Timestamp}
  Lembur ID {ID} ditolak oleh {Nama} (ID: {UserID}) pada {Timestamp}
  ```

## Cara Penggunaan

### **Menyetujui Izin:**
1. Buka halaman Manajemen Izin
2. Klik tombol "Setujui" pada izin yang ingin disetujui
3. Konfirmasi - sistem akan menampilkan nama Anda sebagai approver
4. Izin akan tercatat disetujui dengan nama dan waktu persetujuan

### **Menolak Izin:**
1. Buka halaman Manajemen Izin
2. Klik tombol "Tolak" pada izin yang ingin ditolak
3. Konfirmasi - sistem akan menampilkan nama Anda sebagai yang menolak
4. Izin akan tercatat ditolak dengan nama dan waktu penolakan

### **Bulk Actions:**
1. Pilih multiple izin dengan checkbox
2. Pilih "Setujui Terpilih" atau "Tolak Terpilih"
3. Konfirmasi - sistem menampilkan jumlah dan nama approver
4. Semua izin terpilih akan diproses dengan tracking yang sama

### **Melihat Detail:**
1. Klik tombol "View" pada izin/lembur
2. Lihat section "Status Persetujuan" untuk info lengkap
3. Dapat melakukan approve/reject langsung dari halaman detail

## Database Schema

### Tabel `izins`
- `approved_by` (foreign key ke users/pegawais)
- `approved_at` (timestamp approval, null jika ditolak)

### Tabel `overtime_assignments`
- `approved_by` (foreign key ke users/pegawais)
- `approved_at` (timestamp approval)
- `status` ('Assigned', 'Accepted', 'Rejected')

## Keamanan & Audit Trail
- ✅ Setiap action tercatat dengan user ID
- ✅ Timestamp akurat untuk setiap persetujuan
- ✅ System log untuk audit trail
- ✅ Relasi database yang aman dengan foreign key

## Customization

### Menambah Info Approver di Tempat Lain:
```php
// Untuk menampilkan info approval di view lain
$izin = Izin::find(1);
echo $izin->approval_info; // "Disetujui oleh Admin pada 25 Jun 2025 15:30"
```

### Custom Notification:
```php
// Di dalam action
$currentUser = Filament::auth()->user();
Notification::make()
    ->success()
    ->title('Izin Disetujui')
    ->body("Izin telah berhasil disetujui oleh {$currentUser->nama}")
    ->send();
```

Sistem ini memastikan akuntabilitas penuh dalam proses persetujuan dengan tracking lengkap siapa yang melakukan action dan kapan.
