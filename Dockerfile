FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json primero para aprovechar la caché de Docker
COPY package*.json ./

# Instalar todas las dependencias incluyendo las de desarrollo
RUN npm install

# Copiar el resto de los archivos
COPY . .

# Dar permisos de ejecución a los scripts
RUN chmod +x ./build-app.sh ./run-worker.sh ./run-cron.sh

# Copiar archivo .env
COPY .env .env

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Construir assets
RUN npm run build

# Limpiar caché npm
RUN npm cache clean --force

# Optimizar Laravel
RUN php artisan optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Exponer puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
