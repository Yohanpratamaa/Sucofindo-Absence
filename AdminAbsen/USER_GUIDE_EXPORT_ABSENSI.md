# 📊 Panduan Penggunaan Fitur Export Laporan Absensi

## Untuk Kepala Bidang

### 🎯 Cara Mengakses Fitur Export

1. **Login** ke sistem sebagai Kepala Bidang
2. Pilih menu **"Laporan"** di sidebar kiri
3. Klik **"Laporan Absensi"**
4. Anda akan melihat tabel rekap absensi tim dengan 4 tombol export di bagian atas

---

## 📋 Jenis Export yang Tersedia

### 1. 📗 Export Rekap Tim (Excel)
**Kapan digunakan**: Untuk analisis data tim secara detail di Excel
**Tombol**: Hijau dengan icon download
**Output**: File Excel (.xlsx) dengan data:
- Nama dan NPP karyawan
- Total kehadiran, terlambat, tidak checkout
- Total jam lembur dan rata-rata kerja per hari
- Tingkat kehadiran dalam persentase

### 2. 📄 Export Rekap Tim (PDF)
**Kapan digunakan**: Untuk laporan formal atau presentasi
**Tombol**: Merah dengan icon dokumen
**Output**: File PDF dengan:
- Header resmi PT. Sucofindo
- Ringkasan statistik tim
- Tabel rekap dengan badge warna-warni
- Format siap print dan profesional

### 3. 📊 Export Detail per Karyawan (Excel)
**Kapan digunakan**: Untuk melihat detail absensi harian karyawan tertentu
**Tombol**: Biru dengan icon download  
**Output**: File Excel dengan detail harian:
- Tanggal dan hari
- Jam check in/out
- Durasi kerja dan lembur
- Status kehadiran dan lokasi

### 4. 📋 Export Detail per Karyawan (PDF)
**Kapan digunakan**: Untuk evaluasi individual atau dokumentasi
**Tombol**: Kuning dengan icon dokumen
**Output**: File PDF dengan:
- Profil karyawan lengkap
- Statistik individual
- Detail absensi harian
- Format profesional

---

## 🔧 Cara Menggunakan Export

### Langkah-langkah Export:

1. **Klik tombol export** yang diinginkan
2. **Modal form akan muncul** dengan pilihan:
   - 📅 **Dari Tanggal**: Pilih tanggal mulai periode
   - 📅 **Sampai Tanggal**: Pilih tanggal akhir periode  
   - 👤 **Pilih Karyawan**: (Khusus untuk export detail individual)

3. **Isi form** sesuai kebutuhan:
   - Default periode adalah bulan ini
   - Gunakan date picker untuk memilih tanggal
   - Ketik nama karyawan untuk search (export individual)

4. **Klik tombol export** di modal
5. **File akan otomatis terdownload** dengan nama yang deskriptif

### 💡 Tips Penggunaan:

- **Periode Bulanan**: Gunakan tanggal 1 s/d 31 untuk laporan bulanan
- **Periode Mingguan**: Pilih Senin s/d Jumat untuk laporan mingguan  
- **Periode Custom**: Bebas memilih tanggal sesuai kebutuhan
- **Export Excel**: Terbaik untuk analisis data dan kalkulasi
- **Export PDF**: Terbaik untuk laporan formal dan dokumentasi

---

## 📁 Format Nama File

File yang didownload akan memiliki nama deskriptif:

### Rekap Tim:
```
rekap_absensi_tim_2024-01-01_to_2024-01-31.xlsx
rekap_absensi_tim_2024-01-01_to_2024-01-31.pdf
```

### Detail Individual:
```
detail_absensi_Budi_Santoso_2024-01-01_to_2024-01-31.xlsx  
detail_absensi_Budi_Santoso_2024-01-01_to_2024-01-31.pdf
```

---

## 🎨 Fitur Visual Export

### Excel Features:
- ✅ Header dengan background biru dan teks putih
- ✅ Kolom auto-width untuk readability  
- ✅ Data terformat dengan baik
- ✅ Ready untuk pivot table dan chart

### PDF Features:
- ✅ Header resmi PT. Sucofindo dengan logo
- ✅ Badge berwarna untuk status:
  - 🟢 Hijau: Good performance
  - 🟡 Kuning: Warning/attention needed
  - 🔴 Merah: Issues yang perlu ditindaklanjuti
- ✅ Summary statistics box
- ✅ Footer dengan timestamp
- ✅ Professional layout siap print

---

## ⚡ Troubleshooting

### Jika Export Gagal:
1. **Pastikan internet stabil** - Export membutuhkan koneksi
2. **Refresh halaman** dan coba lagi
3. **Periksa periode tanggal** - jangan terlalu lama (max 3 bulan)
4. **Pastikan ada data** dalam periode yang dipilih

### Jika File Tidak Terdownload:
1. **Cek popup blocker** browser
2. **Izinkan download** dari domain sistem
3. **Cek folder Downloads** default browser
4. **Coba browser lain** jika masih bermasalah

### Jika Data Kosong:
1. **Periksa periode tanggal** - mungkin tidak ada absensi
2. **Pastikan karyawan aktif** dalam sistem
3. **Coba periode yang berbeda**

---

## 📞 Bantuan dan Support

Jika mengalami kesulitan:
1. **Hubungi IT Support** untuk masalah teknis
2. **Hubungi HR** untuk pertanyaan data karyawan  
3. **Baca manual ini** untuk panduan lengkap

---

## 🔄 Update dan Maintenance

Fitur ini akan terus diperbaharui dengan:
- ✅ Format export tambahan
- ✅ Filter dan opsi yang lebih lengkap  
- ✅ Automation dan schedule export
- ✅ Integration dengan sistem lain

**Terakhir diupdate**: {{ date('d/m/Y') }}  
**Versi**: 1.0  
**Status**: Production Ready ✅
