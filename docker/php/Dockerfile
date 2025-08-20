FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libicu-dev libonig-dev libxml2-dev supervisor \
    && docker-php-ext-install pdo pdo_pgsql opcache intl zip \
    && rm -rf /var/lib/apt/lists/*

# Copy opcache config
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies (prod)
RUN composer install --no-dev --optimize-autoloader

# Copy Supervisor config
COPY ./docker/php/supervisord.ini /etc/supervisor/conf.d/supervisord.conf

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
