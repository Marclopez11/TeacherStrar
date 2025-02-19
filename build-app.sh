#!/bin/bash
set -e

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Optimizar Laravel
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
