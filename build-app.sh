#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x build-app.sh`

# Exit the script if any command fails
set -e

echo "🚀 Starting build process..."

echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

echo "📦 Installing Node.js dependencies..."
npm install --no-audit --no-fund

echo "🎨 Building CSS..."
npm run prod

echo "🧹 Clearing Laravel cache..."
php artisan optimize:clear

echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
