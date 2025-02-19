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
    npm \
    nginx

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

# Configurar nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

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

# Configurar permisos
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage

# Exponer puerto
EXPOSE 80

# Copiar script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Comando para iniciar la aplicación
CMD ["/start.sh"]
