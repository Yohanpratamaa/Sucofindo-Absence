#!/bin/bash

# ATTENDANCE IMAGES TROUBLESHOOT FOR RAILWAY
# Script untuk troubleshooting masalah gambar attendance di Railway

echo "🔍 ATTENDANCE IMAGES TROUBLESHOOT"
echo "=================================="

# Check environment
echo "Environment: ${RAILWAY_ENVIRONMENT:-LOCAL}"
echo "App URL: ${APP_URL:-'not set'}"
echo "Asset URL: ${ASSET_URL:-'not set'}"
echo ""

# Check directories
echo "📁 DIRECTORY STRUCTURE:"
echo "storage/app/public exists: $([ -d 'storage/app/public' ] && echo 'YES' || echo 'NO')"
echo "storage/app/public/attendance exists: $([ -d 'storage/app/public/attendance' ] && echo 'YES' || echo 'NO')"
echo "public/storage symlink exists: $([ -L 'public/storage' ] && echo 'YES' || echo 'NO')"

if [ -L "public/storage" ]; then
    echo "public/storage points to: $(readlink public/storage)"
    echo "symlink target exists: $([ -e public/storage ] && echo 'YES' || echo 'NO')"
fi
echo ""

# Check permissions
echo "🔐 PERMISSIONS:"
ls -la storage/app/ | grep public
if [ -d "storage/app/public/attendance" ]; then
    ls -la storage/app/public/ | grep attendance
    echo "Files in attendance directory:"
    ls -la storage/app/public/attendance/ | head -5
fi
echo ""

# Test PHP access
echo "🐘 PHP STORAGE TEST:"
php -r "
try {
    echo 'Storage disk: ' . (config('filesystems.default') ?: 'not set') . PHP_EOL;
    echo 'Public disk root: ' . storage_path('app/public') . PHP_EOL;
    echo 'Attendance dir exists via Storage: ' . (\\Illuminate\\Support\\Facades\\Storage::disk('public')->exists('attendance') ? 'YES' : 'NO') . PHP_EOL;

    \$files = glob(storage_path('app/public/attendance/*'));
    echo 'Files count: ' . count(\$files) . PHP_EOL;

    if (!empty(\$files)) {
        echo 'Sample files:' . PHP_EOL;
        foreach (array_slice(\$files, 0, 3) as \$file) {
            echo '  ' . basename(\$file) . ' (' . filesize(\$file) . ' bytes)' . PHP_EOL;
        }
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "🌐 URL GENERATION TEST:"
php -r "
\$testPath = 'attendance/test.jpg';
echo 'Test path: ' . \$testPath . PHP_EOL;
echo 'Asset URL: ' . asset('storage/' . \$testPath) . PHP_EOL;
echo 'Manual URL: ' . url('storage/' . \$testPath) . PHP_EOL;
echo 'Config asset_url: ' . (config('app.asset_url') ?: 'not set') . PHP_EOL;
"

echo ""
echo "✅ QUICK FIXES:"
echo "1. Recreate storage link:"
echo "   rm -f public/storage && php artisan storage:link --force"
echo ""
echo "2. Create attendance directory:"
echo "   mkdir -p storage/app/public/attendance"
echo ""
echo "3. Fix permissions:"
echo "   chmod -R 775 storage/app/public"
echo ""
echo "4. Test with new upload to verify fix"
echo ""

echo "Done! Check the output above for issues."
