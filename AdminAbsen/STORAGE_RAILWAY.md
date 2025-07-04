# 📁 STORAGE & FILE UPLOAD CONFIGURATION FOR RAILWAY

## 🎯 Overview

Konfigurasi ini memastikan bahwa `php artisan storage:link` dan file upload berfungsi dengan baik di Railway deployment.

## ⚙️ Configuration Files Modified

### 1. **start.sh** - Enhanced Storage Link Setup

```bash
# Remove existing storage link if it exists
if [ -L "public/storage" ]; then
    rm public/storage
fi

# Create storage link - Railway compatible
php artisan storage:link --force

# Verify storage link was created
if [ -L "public/storage" ]; then
    echo "✅ Storage link created successfully"
else
    echo "⚠️ Storage link creation failed, creating manual symlink..."
    ln -sf ../storage/app/public public/storage
fi

# Ensure storage/app/public has proper permissions and structure
mkdir -p storage/app/public/uploads
mkdir -p storage/app/public/images
mkdir -p storage/app/public/avatars
chmod -R 775 storage/app/public
```

### 2. **build.sh** - Build-time Storage Setup

```bash
# Setup storage directories and permissions
mkdir -p storage/app/public/uploads
mkdir -p storage/app/public/images
mkdir -p storage/app/public/avatars
mkdir -p storage/app/public/documents
chmod -R 775 storage/app/public

# Create initial storage link
php artisan storage:link --force
```

### 3. **Environment Variables**

```bash
# Storage configuration
FILESYSTEM_DISK=public
FILESYSTEM_DRIVER=local
ASSET_URL=https://sucofindo-absen-production.up.railway.app
```

### 4. **config/filesystems.php** - Enhanced Permissions

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
    'permissions' => [
        'file' => [
            'public' => 0644,
            'private' => 0600,
        ],
        'dir' => [
            'public' => 0755,
            'private' => 0700,
        ],
    ],
],
```

## 🧪 Testing Storage

### 1. **Automated Test Route**

Access: `https://sucofindo-absen-production.up.railway.app/test-storage`

This endpoint will test:

-   ✅ Storage link exists
-   ✅ Storage directory is writable
-   ✅ File creation works
-   ✅ File is web accessible
-   ✅ File cleanup works

### 2. **Manual Upload Test**

```bash
# Test file upload
curl -X POST \
  https://sucofindo-absen-production.up.railway.app/test-upload \
  -F "file=@test-image.jpg"
```

### 3. **Command Line Test**

```bash
# Run storage test script
./test-storage.sh
```

## 📂 Directory Structure After Deployment

```
public/
├── storage -> ../storage/app/public (symlink)
└── ...

storage/
└── app/
    └── public/
        ├── uploads/     (untuk file uploads)
        ├── images/      (untuk gambar)
        ├── avatars/     (untuk avatar users)
        └── documents/   (untuk dokumen)
```

## 🔗 How File URLs Work

### Storage Path → Web URL

-   **File Path**: `storage/app/public/uploads/image.jpg`
-   **Web URL**: `https://your-app.railway.app/storage/uploads/image.jpg`

### In Laravel Code

```php
// Store file
$path = $request->file('image')->store('uploads', 'public');

// Get URL
$url = asset('storage/' . $path);
// Results in: https://your-app.railway.app/storage/uploads/filename.jpg
```

## 🛠️ Troubleshooting

### Issue: Storage link not working

**Solution:**

1. Check if symlink exists: `ls -la public/storage`
2. Recreate manually: `ln -sf ../storage/app/public public/storage`
3. Check permissions: `chmod -R 775 storage/app/public`

### Issue: Files not accessible via web

**Solution:**

1. Verify storage link points to correct location
2. Check file permissions (should be 644)
3. Ensure `ASSET_URL` is set correctly

### Issue: Permission denied on file upload

**Solution:**

1. Check directory permissions: `chmod -R 775 storage/app/public`
2. Ensure Railway container has write access
3. Verify disk configuration in `config/filesystems.php`

## 🚀 Usage in Filament

### File Upload in Filament Form

```php
Forms\Components\FileUpload::make('avatar')
    ->disk('public')
    ->directory('avatars')
    ->image()
    ->imageEditor()
    ->required(),
```

### Display Image in Filament

```php
Tables\Columns\ImageColumn::make('avatar')
    ->disk('public')
    ->size(40)
    ->circular(),
```

## ✅ Verification Checklist

After deployment, verify:

-   [ ] `/test-storage` endpoint returns all `true` values
-   [ ] Storage symlink exists: `public/storage -> ../storage/app/public`
-   [ ] Upload directories exist with proper permissions
-   [ ] File upload works via `/test-upload`
-   [ ] Uploaded files are accessible via browser
-   [ ] Filament file uploads work in admin panel

## 🎯 Expected Results

When properly configured:

-   ✅ `storage:link` command succeeds
-   ✅ File uploads save to `storage/app/public/`
-   ✅ Files are accessible via `https://app.railway.app/storage/path`
-   ✅ Filament file uploads work seamlessly
-   ✅ Images display correctly in admin panel

---

**Status**: ✅ Ready for Railway deployment with full storage support
**Last Updated**: July 2025
