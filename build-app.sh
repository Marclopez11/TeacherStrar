#!/bin/bash
set -e

# Instalar extensiones de PHP necesarias si no están instaladas
if ! php -m | grep -q "redis"; then
    pecl install redis
    docker-php-ext-enable redis
fi

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
