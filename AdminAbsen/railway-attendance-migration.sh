#!/bin/bash
# RAILWAY ATTENDANCE IMAGES MIGRATION SCRIPT
# Script untuk memastikan gambar attendance yang sudah ada bisa diakses

echo "🔧 RAILWAY ATTENDANCE IMAGES MIGRATION"
echo "======================================"

# 1. Pastikan environment Railway
if [ -n "$RAILWAY_ENVIRONMENT" ]; then
    echo "✅ Running on Railway environment"
else
    echo "⚠️  Not running on Railway, proceeding anyway..."
fi

echo ""
echo "📁 SETTING UP STORAGE STRUCTURE..."

# 2. Buat directory structure yang diperlukan
mkdir -p storage/app/public/attendance
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 3. Set permissions
chmod -R 775 storage/app/public
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache

echo "✅ Storage directories created"

echo ""
echo "🔗 SETTING UP STORAGE LINK..."

# 4. Remove broken symlink jika ada
if [ -L "public/storage" ]; then
    echo "Removing existing storage link..."
    rm -f public/storage
fi

# 5. Pastikan target directory ada
if [ ! -d "storage/app/public" ]; then
    mkdir -p storage/app/public
fi

# 6. Create symlink dengan multiple methods
echo "Creating storage symlink..."

# Method 1: Laravel artisan
php artisan storage:link --force 2>/dev/null

# Method 2: Manual symlink jika artisan gagal
if [ ! -L "public/storage" ] || [ ! -e "public/storage" ]; then
    echo "Artisan failed, creating manual symlink..."
    ln -sf ../storage/app/public public/storage
fi

# Method 3: Absolute path jika relative gagal
if [ ! -L "public/storage" ] || [ ! -e "public/storage" ]; then
    echo "Relative symlink failed, using absolute path..."
    STORAGE_PATH=$(readlink -f storage/app/public)
    PUBLIC_PATH=$(readlink -f public)
    ln -sf "$STORAGE_PATH" "$PUBLIC_PATH/storage"
fi

# 7. Verify symlink
if [ -L "public/storage" ] && [ -e "public/storage" ]; then
    echo "✅ Storage symlink created successfully"
    echo "   Link: public/storage -> $(readlink public/storage)"
else
    echo "❌ Storage symlink creation failed"
    echo "   This will cause image access issues"
fi

echo ""
echo "📊 CHECKING ATTENDANCE IMAGES..."

# 8. Check existing attendance files
if [ -d "storage/app/public/attendance" ]; then
    FILE_COUNT=$(find storage/app/public/attendance -type f 2>/dev/null | wc -l)
    echo "Found $FILE_COUNT files in attendance directory"
    
    if [ $FILE_COUNT -gt 0 ]; then
        echo "Sample files:"
        ls -la storage/app/public/attendance/ | head -5
        
        # Test file permissions
        echo ""
        echo "Checking file permissions..."
        find storage/app/public/attendance -type f -exec chmod 644 {} \; 2>/dev/null
        find storage/app/public/attendance -type d -exec chmod 755 {} \; 2>/dev/null
        echo "✅ File permissions fixed"
    fi
else
    echo "⚠️  Attendance directory not found, creating..."
    mkdir -p storage/app/public/attendance
    chmod 755 storage/app/public/attendance
fi

echo ""
echo "🧪 TESTING CONFIGURATION..."

# 9. Test PHP configuration
php -r "
try {
    echo 'Testing PHP configuration...' . PHP_EOL;
    echo 'Storage path: ' . storage_path('app/public') . PHP_EOL;
    echo 'Public path: ' . public_path('storage') . PHP_EOL;
    echo 'Symlink exists: ' . (is_link(public_path('storage')) ? 'YES' : 'NO') . PHP_EOL;
    echo 'Symlink working: ' . (is_dir(public_path('storage')) ? 'YES' : 'NO') . PHP_EOL;
    
    if (class_exists('Illuminate\Support\Facades\Storage')) {
        require_once 'vendor/autoload.php';
        \$app = require_once 'bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        echo 'Laravel loaded: YES' . PHP_EOL;
        echo 'Default disk: ' . config('filesystems.default') . PHP_EOL;
        echo 'Public disk root: ' . config('filesystems.disks.public.root') . PHP_EOL;
        
        \$attendanceExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('attendance');
        echo 'Attendance dir via Storage: ' . (\$attendanceExists ? 'YES' : 'NO') . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "✅ MIGRATION COMPLETED"
echo ""
echo "🚀 NEXT STEPS:"
echo "1. Deploy to Railway (git push)"
echo "2. Check /test-attendance-images endpoint"
echo "3. Test image URLs: /storage/attendance/filename.jpg"
echo "4. Monitor logs for any remaining issues"
echo ""
echo "📋 TROUBLESHOOTING:"
echo "- If images still not showing, check Railway logs"
echo "- Verify ASSET_URL in Railway environment variables"
echo "- Test direct file access via storage link"
echo ""

echo "Script completed successfully!"
