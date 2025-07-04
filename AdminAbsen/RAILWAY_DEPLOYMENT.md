# RAILWAY DEPLOYMENT GUIDE - SUCOFINDO ABSEN

## 📋 Persiapan Deployment

### 1. **File Konfigurasi yang Telah Disiapkan:**

-   ✅ `.env.production` - Konfigurasi environment production
-   ✅ `railway.toml` - Konfigurasi Railway deployment
-   ✅ `build.sh` - Script build otomatis
-   ✅ `start.sh` - Script startup aplikasi
-   ✅ `.railwayignore` - File yang diabaikan saat deployment
-   ✅ `Dockerfile` - Container configuration (opsional)

### 2. **Environment Variables yang Sudah Dikonfigurasi:**

#### **Database:**

```bash
DB_CONNECTION=mysql
DB_HOST=shinkansen.proxy.rlwy.net
DB_PORT=48280
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=pubDKkZSPsMqfKtMNHvNpTYYsxxhwPhY
```

#### **Filament Configuration:**

```bash
FILAMENT_ENABLED=true
FILAMENT_PATH=admin
FILAMENT_HTTPS=true
FILAMENT_BRAND_NAME="Sucofindo Absen"
```

#### **Railway Optimizations:**

```bash
RAILWAY_STATIC_URL=https://sucofindo-absen-production.up.railway.app
FORCE_HTTPS=true
ASSET_URL=https://sucofindo-absen-production.up.railway.app
```

## 🚀 Proses Deployment

### 1. **Auto Build Process:**

Railway akan otomatis menjalankan:

-   Install Composer dependencies
-   Install NPM dependencies
-   Build frontend assets (Vite)
-   Cache Laravel configurations
-   Cache Filament components
-   Optimize untuk production

### 2. **Health Check:**

-   **Path**: `/admin`
-   **Timeout**: 300 seconds
-   **Restart Policy**: On failure (max 3 retries)

## 🔧 Akses Aplikasi

### **URLs:**

-   **Main App**: `https://sucofindo-absen-production.up.railway.app`
-   **Admin Panel**: `https://sucofindo-absen-production.up.railway.app/admin`
-   **Analisis Absensi**: `https://sucofindo-absen-production.up.railway.app/admin/kepala-bidang/attendance-analytics`

## ✅ Fitur yang Dikonfigurasi

### **Security:**

-   ✅ HTTPS enforcement
-   ✅ Secure cookies
-   ✅ CSRF protection
-   ✅ Trusted proxies

### **Performance:**

-   ✅ Config caching
-   ✅ Route caching
-   ✅ View caching
-   ✅ Filament component caching
-   ✅ Asset optimization

### **Filament Features:**

-   ✅ Dark mode support
-   ✅ Responsive design
-   ✅ Multi-panel (Admin, Kepala Bidang)
-   ✅ Analytics dashboard
-   ✅ Native components

## 🐛 Troubleshooting

### **Jika Filament tidak muncul:**

1. Check healthcheck: `/admin`
2. Verify environment variables
3. Check logs: `railway logs`
4. Restart service: `railway redeploy`

### **Jika database error:**

1. Verify database credentials
2. Check database connection
3. Run migrations: `php artisan migrate --force`

### **Jika assets tidak load:**

1. Check `ASSET_URL` configuration
2. Verify Vite build process
3. Check `public/build` directory

## 📝 Maintenance

### **Regular Tasks:**

-   Monitor application logs
-   Check database performance
-   Update dependencies (monthly)
-   Backup database (automated via Railway)

### **Updates:**

-   Push to main branch
-   Railway akan otomatis deploy
-   Monitor health check status
-   Verify functionality

## 🎯 Next Steps

1. **Deploy ke Railway**
2. **Verify health check**
3. **Test admin access**
4. **Test analytics dashboard**
5. **Configure production monitoring**

---

**Status**: ✅ Ready for Railway deployment
**Updated**: July 2025
**Environment**: Production
