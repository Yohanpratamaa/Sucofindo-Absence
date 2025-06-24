# Sistem Administrasi Absensi Pegawai Sucofindo

Sistem administrasi untuk mengelola data pegawai dengan fitur lengkap mencakup data pribadi, jabatan, posisi, pendidikan, kontak darurat, dan fasilitas.

## Fitur Utama

### 1. Manajemen Data Pegawai
- **Tab Users**: Data dasar pegawai (nama, NPP, email, NIK, status, role, alamat, nomor handphone)
- **Tab Jabatan**: Data jabatan dan tunjangan jabatan
- **Tab Posisi**: Data posisi dan tunjangan posisi
- **Tab Pendidikan**: Multiple data pendidikan (jenjang, sekolah/universitas, fakultas, jurusan, tahun masuk/lulus, IPK/nilai, ijazah)
- **Tab Kontak Darurat**: Multiple kontak darurat (hubungan, nama, nomor telepon)
- **Tab Fasilitas**: Multiple fasilitas pegawai (BPJS, asuransi, tunjangan, dll dengan nilai nominal)

### 2. Struktur Database Tunggal
- Hanya menggunakan **satu tabel `pegawais`** tanpa foreign key
- Data pendidikan, kontak darurat, dan fasilitas disimpan dalam format JSON
- Struktur database yang simpel dan mudah maintenance

### 3. Interface Admin dengan Filament
- Form input dengan tab yang terorganisir
- Tabel data dengan filter dan pencarian
- Aksi CRUD lengkap (Create, Read, Update, Delete)
- Responsive design

## Struktur Database

### Tabel `pegawais`

#### Tab Users (Data Dasar)
- `nama` - Nama lengkap pegawai
- `npp` - Nomor Pokok Pegawai (unique)
- `email` - Email pegawai (unique)
- `password` - Password (hashed)
- `nik` - Nomor Induk Kependudukan (unique)
- `status_pegawai` - Enum: PTT, LS
- `nomor_handphone` - Nomor handphone
- `status` - Enum: active, resign (default: active)
- `role_user` - Enum: super admin, employee, Kepala Bidang
- `alamat` - Alamat lengkap

#### Tab Jabatan
- `jabatan_nama` - Nama jabatan
- `jabatan_tunjangan` - Tunjangan jabatan (decimal)

#### Tab Posisi
- `posisi_nama` - Nama posisi
- `posisi_tunjangan` - Tunjangan posisi (decimal)

#### Tab Data JSON
- `pendidikan_list` - Array JSON data pendidikan
- `emergency_contacts` - Array JSON kontak darurat
- `fasilitas_list` - Array JSON data fasilitas

### Contoh Struktur JSON

#### Pendidikan List
```json
[
  {
    "jenjang": "S1",
    "sekolah_univ": "Universitas Indonesia",
    "fakultas_program_studi": "Ilmu Komputer",
    "jurusan": "Sistem Informasi",
    "thn_masuk": "2015-08-01",
    "thn_lulus": "2019-07-01",
    "ipk_nilai": "3.75",
    "ijazah": "path/to/file.pdf"
  }
]
```

#### Emergency Contacts
```json
[
  {
    "relationship": "Ayah",
    "nama_kontak": "David Doe",
    "no_emergency": "081234567891"
  }
]
```

#### Fasilitas List
```json
[
  {
    "nama_jaminan": "BPJS Kesehatan",
    "no_jaminan": "0001234567890",
    "jenis_fasilitas": "BPJS Kesehatan",
    "provider": "BPJS",
    "nilai_fasilitas": 150000
  }
]
```

## Instalasi dan Setup

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk Filament assets)

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd AdminAbsen
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sucofindo_absence
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run Migration & Seeder**
   ```bash
   php artisan migrate:fresh --force
   php artisan db:seed
   ```

6. **Create Admin User (Filament)**
   ```bash
   php artisan make:filament-user
   ```

7. **Run Server**
   ```bash
   php artisan serve
   ```

8. **Access Admin Panel**
   - URL: `http://localhost:8000/admin`
   - Login dengan user yang dibuat di step 6

## Fitur Admin Panel

### Dashboard Pegawai
- **List View**: Tabel dengan kolom utama (NPP, Nama, Email, Status, Jabatan, Posisi)
- **Create**: Form multi-tab untuk input data lengkap
- **Edit**: Update data dengan form yang sama seperti create
- **View**: Detail view data pegawai
- **Delete**: Soft delete dengan konfirmasi

### Filter & Search
- Filter berdasarkan status pegawai (PTT/LS)
- Filter berdasarkan status aktif/resign
- Filter berdasarkan role user
- Search di nama, NPP, email, NIK

### Column Toggles
- Total nilai fasilitas (hidden by default)
- Jumlah fasilitas (hidden by default)
- Nomor handphone (hidden by default)
- Created at (hidden by default)

## Model & Accessor

### Pegawai Model Features

#### Accessors
- `total_nilai_fasilitas` - Menghitung total nilai dari fasilitas_list JSON
- `jumlah_fasilitas` - Menghitung jumlah fasilitas
- `pendidikan_terakhir` - Mendapatkan pendidikan dengan tahun lulus terbaru
- `kontak_darurat_utama` - Mendapatkan kontak darurat pertama
- `total_tunjangan` - Total tunjangan jabatan + posisi
- `nama_lengkap` - Format "Nama (NPP)"

#### Scopes
- `active()` - Filter pegawai aktif
- `resign()` - Filter pegawai resign
- `employee()` - Filter role employee
- `superAdmin()` - Filter role super admin
- `kepalaBidang()` - Filter role kepala bidang

#### Security
- Password otomatis di-hash menggunakan Hash facade
- Hidden attribute untuk password di JSON response

## Sample Data

Project sudah include sample data untuk testing:
- 2 pegawai dengan data lengkap di semua tab
- 1 pegawai dengan role "employee"
- 1 pegawai dengan role "Kepala Bidang"
- Data pendidikan multiple (SD/SMA/S1/S2)
- Data kontak darurat (ayah, ibu, suami)
- Data fasilitas (BPJS, asuransi, tunjangan)

## Keuntungan Arsitektur Single Table

### Pros
1. **Simplicity**: Hanya satu tabel, mudah maintenance
2. **Performance**: Tidak ada JOIN, query lebih cepat
3. **Flexibility**: JSON field mudah di-extend
4. **Atomic Operations**: Semua data dalam satu transaksi
5. **Easy Backup**: Hanya perlu backup satu tabel

### Cons
1. **JSON Indexing**: Agak terbatas untuk complex queries di JSON field
2. **Data Validation**: Perlu validasi extra untuk JSON structure
3. **Storage**: Possible duplication pada reference data

### Best Practices Implemented
1. **Validation**: Comprehensive validation di form dan backend
2. **Type Casting**: Proper casting untuk JSON fields di model
3. **Indexing**: Database indexes untuk performance
4. **Error Handling**: Graceful error handling di form submissions
5. **Security**: Password hashing, input sanitization

## API Endpoints (Future)

Struktur siap untuk API development:
- `GET /api/pegawai` - List pegawai
- `POST /api/pegawai` - Create pegawai
- `GET /api/pegawai/{id}` - Detail pegawai
- `PUT /api/pegawai/{id}` - Update pegawai
- `DELETE /api/pegawai/{id}` - Delete pegawai

## Maintenance

### Regular Tasks
1. **Database Backup**: Regular backup tabel pegawais
2. **Log Monitoring**: Check Laravel logs for errors
3. **Performance**: Monitor query performance untuk JSON fields
4. **Updates**: Keep Laravel & Filament updated

### Troubleshooting
1. **Migration Issues**: Use `php artisan migrate:fresh --force`
2. **Cache Issues**: Run `php artisan cache:clear`
3. **Config Issues**: Run `php artisan config:clear`
4. **View Issues**: Run `php artisan view:clear`

---

**Developed by**: Developer Team
**Version**: 1.0.0
**Last Updated**: June 24, 2025
