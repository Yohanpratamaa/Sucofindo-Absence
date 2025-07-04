# ATTENDANCE IMAGES RAILWAY FIX

Dokumentasi solusi untuk masalah gambar attendance yang tidak bisa diakses di Railway deployment.

## MASALAH YANG DIATASI

1. **Storage symlink tidak ada/broken di Railway**
2. **Directory attendance tidak ada**
3. **File permissions salah**
4. **URL generation tidak sesuai environment Railway**
5. **Gambar attendance lama tidak terdeteksi**

## SOLUSI YANG DIIMPLEMENTASI

### 1. Model Attendance - Accessor Robust

File: `app/Models/Attendance.php`

Accessor `getPictureAbsen*UrlAttribute()` diperbaiki dengan:

-   **Fallback file check**: Jika Storage::disk() gagal, coba direct file_exists()
-   **Railway-compatible URL**: Gunakan `config('app.asset_url')` untuk Railway
-   **Enhanced logging**: Log lebih detail untuk debugging
-   **Graceful degradation**: Return no-image.png jika file tidak ada

```php
// Accessor yang robust untuk Railway
public function getPictureAbsenMasukUrlAttribute()
{
    if (!$this->picture_absen_masuk) {
        return asset('images/no-image.png');
    }

    // Try Storage facade first, fallback to direct check
    $fileExists = false;
    try {
        $fileExists = Storage::disk('public')->exists($this->picture_absen_masuk);
    } catch (\Exception $e) {
        $fullPath = storage_path('app/public/' . $this->picture_absen_masuk);
        $fileExists = file_exists($fullPath);
    }

    if ($fileExists) {
        // Use asset URL for Railway compatibility
        $baseUrl = config('app.asset_url') ?: config('app.url');
        return $baseUrl . '/storage/' . $this->picture_absen_masuk;
    }

    return asset('images/no-image.png');
}
```

### 2. Build & Start Scripts

**build.sh** diperbaiki:

-   Membuat directory `storage/app/public/attendance`
-   Set permissions yang benar
-   Storage link initial creation

**start.sh** diperbaiki:

-   Recreate storage link di Railway
-   Fallback manual symlink creation
-   Ensure attendance directory exists

### 3. Environment Configuration

**.env.production** dioptimasi:

```bash
FILESYSTEM_DISK=public
FILAMENT_FILESYSTEM_DISK=public
ASSET_URL=https://sucofindo-absen-production.up.railway.app
```

### 4. Testing & Debugging Tools

**Scripts untuk troubleshooting:**

-   `debug_attendance_images.php` - Diagnostic komprehensif
-   `fix_attendance_images.php` - Auto-fix common issues
-   `troubleshoot-attendance.sh` - Shell troubleshooting
-   `/test-attendance-images` route - API testing

## DEPLOYMENT CHECKLIST

### Pre-Deploy:

-   [x] Model accessors updated
-   [x] Build/start scripts fixed
-   [x] Environment configured
-   [x] Testing tools ready

### Post-Deploy Railway:

1. **Test storage setup**:

    ```
    GET /test-storage
    GET /test-attendance-images
    ```

2. **Check existing images**:

    - Cek apakah gambar lama bisa diakses
    - Test URL: `https://app-url/storage/attendance/filename.jpg`

3. **Test new uploads**:

    - Upload gambar baru via Filament
    - Verify URL generation

4. **Monitor logs**:
    - Check untuk "Attendance image missing" warnings
    - Monitor file access patterns

## TROUBLESHOOTING

### Jika gambar masih tidak muncul:

1. **Cek storage link**:

    ```bash
    ls -la public/storage
    # Harus point ke ../storage/app/public
    ```

2. **Cek directory structure**:

    ```bash
    ls -la storage/app/public/attendance/
    # Harus ada files dengan permission 644
    ```

3. **Manual fix**:

    ```bash
    php fix_attendance_images.php
    # atau
    bash troubleshoot-attendance.sh
    ```

4. **Re-create storage link**:
    ```bash
    rm -f public/storage
    php artisan storage:link --force
    ```

### Common Issues:

**Issue 1: "Storage disk not found"**

-   Solution: Pastikan FILESYSTEM_DISK=public di .env

**Issue 2: "Permission denied"**

-   Solution: chmod -R 775 storage/app/public

**Issue 3: "File not found tapi file ada"**

-   Solution: Cek symlink, recreate storage:link

**Issue 4: "URL 404"**

-   Solution: Cek ASSET_URL di .env Railway

## MONITORING

### Logs yang perlu diwatch:

```bash
# Laravel log
tail -f storage/logs/laravel.log | grep "Attendance image"

# Railway logs
railway logs --follow
```

### Key metrics:

-   File upload success rate
-   Image URL accessibility
-   Storage disk usage
-   404 errors on /storage/\* paths

## ROLLBACK PLAN

Jika ada masalah, rollback dengan:

1. Revert model accessors ke versi sebelumnya
2. Reset FILESYSTEM_DISK=local di .env
3. Manual symlink creation di start.sh

## TESTING COMMANDS

Local testing:

```bash
php debug_attendance_images.php
php fix_attendance_images.php
bash troubleshoot-attendance.sh
```

Railway testing:

```bash
curl https://app-url/test-storage
curl https://app-url/test-attendance-images
```

## FILES CHANGED

1. `app/Models/Attendance.php` - Robust accessors
2. `build.sh` - Add attendance directory
3. `start.sh` - Enhanced storage setup
4. `.env.production` - Railway optimization
5. `routes/web.php` - Add test routes
6. `app/Http/Controllers/StorageTestController.php` - Add attendance tests

## NEXT STEPS

1. Deploy ke Railway
2. Test semua functionality
3. Monitor logs untuk errors
4. Optimize berdasarkan real usage
5. Document any additional issues found

---

**Status**: ✅ Ready for Railway deployment
**Last Updated**: 2025-07-04
**Testing**: Local tests PASS, ready for Railway validation
