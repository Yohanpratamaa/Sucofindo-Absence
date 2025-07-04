#!/bin/bash
# Railway Build Script untuk Laravel + Filament

echo "🚀 Starting Railway build process..."

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear any existing caches
echo "🧹 Clearing existing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Cache Filament components
echo "🎨 Caching Filament components..."
php artisan filament:cache-components

# Install and build frontend assets
if [ -f "package.json" ]; then
    echo "🎯 Building frontend assets..."
    npm ci --production=false
    npm run build
fi

# Final optimizations
echo "🚀 Final optimizations..."
php artisan optimize

# Setup storage directories and permissions
echo "📁 Setting up storage structure..."
mkdir -p storage/app/public/uploads
mkdir -p storage/app/public/images
mkdir -p storage/app/public/avatars
mkdir -p storage/app/public/documents
mkdir -p storage/app/public/attendance
chmod -R 775 storage/app/public

# Create storage link - will be recreated in start.sh for Railway
echo "🔗 Creating initial storage link..."
php artisan storage:link --force

# Railway-specific attendance images migration
echo "📸 Setting up attendance images for Railway..."
if [ -n "$RAILWAY_ENVIRONMENT" ]; then
    bash railway-attendance-migration.sh
fi

echo "✅ Build completed successfully!"
