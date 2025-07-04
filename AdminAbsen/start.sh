#!/bin/bash
# Railway Start Script untuk Laravel + Filament

echo "🌟 Starting Sucofindo Absen application..."

# Ensure storage directories exist with proper permissions
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure storage link exists and is properly configured for Railway
echo "🔗 Setting up storage link for Railway..."

# Remove existing storage link if it exists
if [ -L "public/storage" ]; then
    rm public/storage
fi

# Create storage link - Railway compatible
php artisan storage:link --force

# Verify storage link was created
if [ -L "public/storage" ]; then
    echo "✅ Storage link created successfully"
    ls -la public/storage
else
    echo "⚠️ Storage link creation failed, creating manual symlink..."
    # Manual symlink creation as fallback
    ln -sf ../storage/app/public public/storage
    echo "✅ Manual storage link created"
fi

# Ensure storage/app/public has proper permissions and structure
mkdir -p storage/app/public/uploads
mkdir -p storage/app/public/images
mkdir -p storage/app/public/avatars
mkdir -p storage/app/public/attendance
chmod -R 775 storage/app/public

# Clear any leftover caches from build
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application
echo "✅ Starting PHP server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
