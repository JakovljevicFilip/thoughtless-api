FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache intl zip

# Copy opcache config
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html

# Copy application code
COPY . .

# Set proper permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies (no dev deps for prod)
RUN composer install --no-dev --optimize-autoloader

# Cache config, routes, and views for faster performance
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
