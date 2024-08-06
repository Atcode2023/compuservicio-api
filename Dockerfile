FROM php:8.1-apache

# Install PHP extensions and other necessary packages
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd pdo_mysql intl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura Apache
COPY ./apache/laravel.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copia el código de la aplicación al contenedor
COPY . /var/www/html

# Otorga permisos al almacenamiento y cache
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN composer install
# Exponer el puerto 80
EXPOSE 80

# Ejecuta el servidor Apache
CMD ["apache2-foreground"]
