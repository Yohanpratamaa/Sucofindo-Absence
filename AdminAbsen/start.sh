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

# Ensure storage link exists
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

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
