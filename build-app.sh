#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x build-app.sh`

# Exit the script if any command fails
set -e

# Instalar dependencias de PHP
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Instalar dependencias de Node.js
npm ci
npm run build

# Optimizar Laravel
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
