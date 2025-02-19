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

# Copiar archivos de la aplicación
COPY . .

# Dar permisos de ejecución a los scripts
RUN chmod +x ./build-app.sh ./run-worker.sh ./run-cron.sh

# Ejecutar script de construcción
RUN ./build-app.sh

# Exponer puerto
EXPOSE 80

# Comando para iniciar la aplicación
CMD ["./start.sh"]
