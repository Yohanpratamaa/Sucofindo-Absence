# Sucofindo Absence Management System

Sistem manajemen absensi karyawan berbasis web menggunakan Laravel dan Filament PHP untuk PT. Sucofindo.

## 📋 Deskripsi

Sistem ini dirancang untuk mengelola data karyawan dan absensi dengan interface yang modern dan user-friendly menggunakan Filament Admin Panel. Sistem ini mencakup manajemen data pegawai lengkap dengan informasi personal, pendidikan, kontak darurat, dan data jaminan sosial.

## 🚀 Fitur Utama

- **Manajemen Data Pegawai**

  - Data akses (username, password, role)
  - Informasi umum (biodata lengkap)
  - Riwayat pendidikan
  - Kontak darurat
  - Data jaminan (BPJS, KTP, NPWP, rekening bank)

- **Dashboard Admin**

  - Interface modern menggunakan Filament
  - Navigation yang terorganisir dengan grup
  - Form dengan tabs untuk kemudahan input data

- **Sistem Autentikasi**
  - Multi-role user (Admin, HR, Manager, Supervisor, Staff)
  - Session management

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL
- **Frontend**: Blade Templates + Tailwind CSS (via Filament)
- **Icons**: Heroicons
- **Package Manager**: Composer, NPM

## 📋 Prerequisites

Pastikan sistem Anda memiliki:

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web Server (Apache/Nginx) atau Laravel Valet

## 🔧 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/Sucofindo-Absence.git
cd Sucofindo-Absence/AdminAbsen
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adminabsen
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Create Database

```bash
# Buat database MySQL
mysql -u root -p
CREATE DATABASE adminabsen;
exit
```

### 6. Database Migration

```bash
# Jalankan migrasi database
php artisan migrate

# (Opsional) Jalankan seeder jika tersedia
php artisan db:seed
```

### 7. Build Assets

```bash
# Build assets untuk production
npm run build

# Atau untuk development dengan hot reload
npm run dev
```

### 8. Storage Link

```bash
# Buat symbolic link untuk storage
php artisan storage:link
```

### 9. Optimize Application

```bash
# Optimize untuk production
php artisan optimize
```

## 🚀 Menjalankan Aplikasi

### Development Server

```bash
# Jalankan Laravel development server
php artisan serve

# Atau menggunakan concurrently (dengan queue dan vite)
composer run dev
```

### Production Server

```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔐 Akses Admin Panel

1. Buka browser dan akses: `http://localhost:8000/admin`
2. Login menggunakan akun admin yang telah dibuat
3. Mulai mengelola data pegawai

## 📁 Struktur Direktori

```
AdminAbsen/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   └── PegawaiResource.php     # Resource untuk manajemen pegawai
│   │   ├── Pages/                      # Custom pages
│   │   └── Widgets/                    # Dashboard widgets
│   ├── Models/
│   │   └── Pegawai.php                 # Model pegawai
│   └── Providers/
├── database/
│   └── migrations/                     # Database migrations
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
└── public/
    └── build/                          # Compiled assets
```

## 🔧 Konfigurasi Tambahan

### Mail Configuration (Opsional)

Jika ingin menggunakan fitur email, konfigurasi di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Queue Configuration (Opsional)

Untuk background jobs:

```env
QUEUE_CONNECTION=database
```

Kemudian jalankan queue worker:

```bash
php artisan queue:work
```

## 🧪 Testing

```bash
# Jalankan test suite
php artisan test

# Atau menggunakan composer script
composer run test
```

## 🔧 Troubleshooting

### Error: Class not found

```bash
composer dump-autoload
php artisan clear-compiled
```

### Permission Issues

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### NPM Build Issues

```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Database Connection Error

1. Pastikan MySQL service berjalan
2. Cek konfigurasi database di `.env`
3. Pastikan database sudah dibuat

## 📚 Command Reference

```bash
# Development
php artisan serve                    # Start development server
npm run dev                         # Start Vite dev server
composer run dev                    # Start all services concurrently

# Database
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed    # Fresh migration with seeders
php artisan migrate:rollback        # Rollback migrations

# Cache Management
php artisan cache:clear             # Clear application cache
php artisan config:clear            # Clear config cache
php artisan view:clear              # Clear view cache
php artisan route:clear             # Clear route cache

# Filament
php artisan filament:upgrade        # Upgrade filament
php artisan make:filament-resource  # Create new resource
php artisan make:filament-page      # Create new page
php artisan make:filament-widget    # Create new widget

# Production
php artisan optimize                # Optimize application
php artisan config:cache            # Cache configurations
php artisan route:cache             # Cache routes
php artisan view:cache              # Cache views
```

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Create Pull Request

## 📝 License

Project ini menggunakan [MIT License](LICENSE).

## 👥 Team

- **Developer**: [Your Name]
- **Company**: PT. Sucofindo
- **Version**: 1.0.0

## 📞 Support

Jika mengalami masalah atau membutuhkan bantuan:

1. Buka issue di repository GitHub
2. Hubungi tim development
3. Baca dokumentasi Laravel: https://laravel.com/docs
4. Baca dokumentasi Filament: https://filamentphp.com/docs

---

⭐ Jangan lupa berikan star jika project ini membantu!
