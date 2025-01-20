# Usar una versión específica de PHP
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock
COPY composer*.json ./

# Instalar dependencias
RUN composer install --no-scripts --no-autoloader

# Copiar el resto del código
COPY . .

# Generar autoload files
RUN composer dump-autoload

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html/storage

# Exponer puerto
EXPOSE 8000

# Comando para ejecutar la aplicación
CMD php artisan serve --host=0.0.0.0 --port=8000