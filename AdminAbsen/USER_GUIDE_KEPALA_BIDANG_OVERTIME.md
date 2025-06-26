# 📚 User Guide: Manajemen Lembur Kepala Bidang

## 🎯 **Panduan Penggunaan Sistem Lembur untuk Kepala Bidang**

---

## 🚀 **Akses Panel Kepala Bidang**

### 1. **Login ke System**
```
URL: http://localhost/Sucofindo-Absen/AdminAbsen/public/kepala-bidang
Credentials:
- Username: supervisor@sucofindo.com
- Password: password
```

### 2. **Navigasi Menu**
- Pilih menu **"Persetujuan"** → **"Pengajuan Lembur"**
- Icon: 🕐 (Clock)

---

## 📋 **Fitur Utama**

### 🔹 **A. Melihat Daftar Pengajuan Lembur**

#### **Table Columns:**
| Kolom | Deskripsi |
|-------|-----------|
| **Nama Pegawai** | Nama lengkap karyawan |
| **NPP** | Nomor Pokok Pegawai |
| **ID Lembur** | Unique identifier untuk proyek/task lembur |
| **Ditugaskan Oleh** | Nama yang menugaskan lembur |
| **Waktu Penugasan** | Tanggal dan jam assignment |
| **Status** | Ditugaskan 🟡 / Diterima 🟢 / Ditolak 🔴 |
| **Info Persetujuan** | Detail approval/rejection |

#### **Filter Options:**
- **Status**: Filter berdasarkan status approval
- **Pegawai**: Filter berdasarkan nama karyawan
- **Date Range**: Filter berdasarkan periode tertentu

---

### 🔹 **B. Menyetujui/Menolak Lembur**

#### **Single Action (Per Row):**

1. **💚 Approve (Setujui)**
   - Klik tombol **"Setujui"** (hijau)
   - Konfirmasi di modal popup
   - Status berubah menjadi **"Diterima"**

2. **❌ Reject (Tolak)**
   - Klik tombol **"Tolak"** (merah)
   - Konfirmasi di modal popup
   - Status berubah menjadi **"Ditolak"**

3. **🔄 Reassign (Assign Ulang)**
   - Klik tombol **"Assign Ulang"** (biru)
   - Pilih karyawan baru dari dropdown
   - Sistem akan update assignment

#### **Bulk Action (Multiple Records):**

1. **Select Multiple Records**
   - Centang checkbox di baris yang diinginkan
   - Atau klik **"Select All"** untuk semua

2. **Bulk Approve**
   - Klik **"Setujui yang Dipilih"**
   - Konfirmasi di modal
   - Semua record terpilih akan di-approve

3. **Bulk Reject**
   - Klik **"Tolak Terpilih"**
   - Konfirmasi di modal
   - Semua record terpilih akan di-reject

---

### 🔹 **C. Menugaskan Lembur Baru**

#### **Steps:**
1. **Klik "Assign Lembur Baru"** (button hijau di header)

2. **Isi Form:**
   ```
   📝 Form Fields:
   ├── Pegawai: [Dropdown Employee] ← Required
   ├── ID Lembur: [Text Input] ← Required & Unique
   └── Waktu Penugasan: [DateTime Picker] ← Default: Now
   ```

3. **Auto-filled Fields:**
   - **Ditugaskan Oleh**: Current Kepala Bidang (auto)
   - **Status**: "Assigned" (auto)

4. **Submit**: Klik **"Simpan"**

#### **Validation Rules:**
- ✅ **Pegawai**: Harus dipilih (Employee role only)
- ✅ **ID Lembur**: Harus unique (format: OT-YYYY-001)
- ✅ **Waktu Penugasan**: Tidak boleh kosong

---

### 🔹 **D. Melihat Detail Lembur**

#### **Access:**
- Klik **nama pegawai** di table, atau
- Klik icon **"View"** di action column

#### **Information Sections:**

1. **👤 Informasi Pegawai**
   - Nama Lengkap & NPP
   - Jabatan & Posisi

2. **📋 Detail Penugasan Lembur**
   - ID Lembur dengan badge
   - Waktu penugasan
   - Ditugaskan oleh

3. **📊 Status Penugasan**
   - Status current dengan color badge
   - Informasi persetujuan lengkap

#### **Header Actions (in Detail View):**
- **✏️ Edit**: Jika status masih "Assigned"
- **✅ Setujui**: Quick approve action
- **❌ Tolak**: Quick reject action
- **🗑️ Delete**: Hapus assignment (jika masih Assigned)

---

## 🛡️ **Business Rules & Restrictions**

### 🔒 **Authorization:**
```
Role Access:
├── Kepala Bidang: ✅ Full Access (Create, View, Approve, Reject, Assign)
├── Super Admin: ⚠️ View Only (NO Approve/Reject)
└── Employee: 👁️ View Own Assignments Only
```

### 📊 **Status Workflow:**
```
Lembur Lifecycle:
Assigned → [Approve] → Accepted ✅
    ↓
[Reject] → Rejected ❌
    ↓
[Reassign] → Assigned (to new employee)
```

### ⚡ **System Rules:**
- ✅ Hanya status **"Assigned"** yang bisa di-approve/reject
- ✅ Super Admin **TIDAK BISA** approve/reject
- ✅ ID Lembur harus **UNIQUE** di seluruh sistem
- ✅ Semua actions tercatat dalam **audit log**

---

## 🎯 **Tips & Best Practices**

### 💡 **Efficient Workflow:**

1. **Daily Review**
   - Check pengajuan lembur setiap hari
   - Prioritaskan approval berdasarkan urgency
   - Gunakan bulk action untuk efficiency

2. **ID Lembur Naming Convention**
   ```
   Format Recommended:
   OT-2025-001  (OT-Year-Sequence)
   PROJ-001     (Project-Sequence)
   MAINT-2025-01 (Maintenance-Year-Sequence)
   ```

3. **Filter Usage**
   - Gunakan filter **Status** untuk fokus pada pending
   - Filter **Pegawai** untuk review individual performance
   - Filter **Date Range** untuk reporting period

4. **Reassignment Strategy**
   - Reassign jika employee overloaded
   - Consider skill match saat reassign
   - Monitor reassignment patterns

---

## 🚨 **Troubleshooting**

### ❓ **Common Issues:**

1. **"Akses Ditolak" Error**
   ```
   Cause: Super Admin trying to approve/reject
   Solution: Use Kepala Bidang account
   ```

2. **"ID Lembur sudah ada" Error**
   ```
   Cause: Duplicate overtime ID
   Solution: Use different unique ID
   ```

3. **Employee tidak muncul di dropdown**
   ```
   Cause: Employee status not active
   Solution: Check employee status in master data
   ```

4. **Action buttons tidak muncul**
   ```
   Cause: Status bukan "Assigned" atau unauthorized
   Solution: Check status and user role
   ```

---

## 📊 **Reporting & Analytics**

### 📈 **Built-in Analytics:**
- **Total Assignments**: Count di table header
- **Status Distribution**: Visual badges in table
- **Assignment Timeline**: Timestamps in detail view
- **Approval History**: Complete audit trail

### 📋 **Manual Reporting:**
1. **Export to Excel** (Admin panel feature)
2. **Filter by Date Range** untuk periode report
3. **Use Status Filter** untuk analyze approval rates
4. **Monitor Reassignment Frequency**

---

## 🔧 **Advanced Features**

### 🎛️ **Power User Tips:**

1. **Keyboard Shortcuts:**
   - `Ctrl+Click` untuk multi-select
   - `Enter` untuk confirm modals
   - `Esc` untuk cancel operations

2. **Quick Actions:**
   - Double-click nama pegawai → Quick view
   - Right-click row → Context menu (browser)

3. **Bulk Operations:**
   - Select All → Bulk action untuk mass approval
   - Use filters → Select filtered → Bulk action

---

## 📞 **Support & Contact**

### 🆘 **Need Help?**
- **System Admin**: IT Department
- **Process Questions**: HR Department  
- **Technical Issues**: Development Team

### 📧 **Feedback:**
Kirim feedback dan suggestion untuk improvement ke tim development.

---

## 🎉 **Ready to Use!**

Sistem Manajemen Lembur Kepala Bidang siap digunakan untuk meningkatkan efficiency dalam approval workflow dan management tim yang lebih baik!

**Happy Managing! 🚀**
