# Pembatasan Approval untuk Super Admin

## Deskripsi Fitur

Fitur ini mengimplementasikan pembatasan untuk Super Admin agar tidak dapat melakukan approval atau reject terhadap:
- Izin/Cuti pegawai
- Lembur/Overtime Assignment

Super Admin dianggap sebagai role yang fokus pada manajemen sistem dan data, bukan pada proses approval operasional sehari-hari.

## Implementasi

### 1. Model Pegawai
Menambahkan method helper untuk mengecek role Super Admin:

```php
// Method untuk mengecek apakah user adalah super admin
public function isSuperAdmin(): bool
{
    return $this->role_user === 'super admin';
}

// Method untuk mengecek apakah user dapat melakukan approval
public function canApprove(): bool
{
    return !$this->isSuperAdmin();
}
```

### 2. IzinResource (Panel Admin)
**Single Actions:**
- Action `approve` dan `reject` hanya visible jika user bukan Super Admin
- Kondisi: `$record->status === 'pending' && !$currentUser->isSuperAdmin()`

**Bulk Actions:**
- Bulk action group hanya visible jika user bukan Super Admin
- Pengecekan tambahan di dalam action untuk mencegah eksekusi

### 3. OvertimeAssignmentResource (Panel Admin)
**Single Actions:**
- Action `accept` dan `reject` hanya visible jika user bukan Super Admin  
- Kondisi: `$record->canChangeStatus() && !$currentUser->isSuperAdmin()`

**Bulk Actions:**
- Bulk action group hanya visible jika user bukan Super Admin
- Pengecekan tambahan di dalam action untuk mencegah eksekusi

### 4. IzinApprovalResource (Panel Kepala Bidang)
**Single Actions:**
- Action `approve` dan `reject` hanya visible jika user bukan Super Admin
- Kondisi: `is_null($record->approved_by) && !$currentUser->isSuperAdmin()`

**Bulk Actions:**
- Setiap bulk action hanya visible jika user bukan Super Admin
- Pengecekan tambahan di dalam action untuk mencegah eksekusi

## Pesan Error

Ketika Super Admin mencoba melakukan approval/reject (baik single maupun bulk), akan muncul notifikasi:

```
Titel: "Akses Ditolak"
Pesan: "Super Admin tidak diperbolehkan melakukan approval/reject izin/lembur."
Type: Danger (merah)
```

## Behavior yang Diimplementasikan

1. **UI Level**: Tombol/action tidak akan terlihat untuk Super Admin
2. **Action Level**: Double protection dengan pengecekan di dalam action
3. **Konsistensi**: Implementasi sama di semua resource terkait
4. **User Experience**: Pesan error yang jelas dan informatif

## Role yang Masih Dapat Melakukan Approval

- **Kepala Bidang**: Dapat approve/reject izin melalui panel Kepala Bidang
- **Admin biasa** (jika ada): Dapat approve/reject di panel Admin
- **Role lain** yang memiliki akses ke panel Admin

## Testing

### Test Case 1: Login sebagai Super Admin
1. Login dengan akun Super Admin
2. Buka Manajemen Izin
3. Verifikasi tombol "Setujui" dan "Tolak" tidak terlihat
4. Verifikasi bulk actions tidak terlihat
5. Ulangi untuk Manajemen Lembur

### Test Case 2: Login sebagai Kepala Bidang (Super Admin)
1. Login dengan akun Kepala Bidang yang role_user = 'super admin'
2. Buka Persetujuan Izin
3. Verifikasi tombol approval tidak terlihat
4. Verifikasi bulk actions tidak terlihat

### Test Case 3: Login sebagai role non-Super Admin
1. Login dengan akun Employee atau Kepala Bidang biasa
2. Buka resource izin/lembur sesuai panel
3. Verifikasi tombol approval masih terlihat dan berfungsi

## Keamanan

- **Double Protection**: UI dan action level
- **Role Based**: Menggunakan attribut `role_user` di database
- **Consistent**: Implementasi sama di semua resource
- **Fail Safe**: Jika terjadi bypass UI, action akan tetap dicegah

## Maintenance

Jika ada resource baru yang menangani approval/reject, pastikan untuk:
1. Menambahkan pengecekan `!$currentUser->isSuperAdmin()` pada visible condition
2. Menambahkan pengecekan di dalam action sebagai backup
3. Memberikan notifikasi error yang konsisten
4. Testing untuk memastikan pembatasan berfungsi

## Customization

Untuk mengubah role yang dibatasi, ubah method `isSuperAdmin()` di model Pegawai:

```php
public function isSuperAdmin(): bool
{
    // Contoh: tambahkan role lain yang dibatasi
    return in_array($this->role_user, ['super admin', 'system admin']);
}
```
