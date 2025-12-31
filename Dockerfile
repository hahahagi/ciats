FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    zip && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql zip mbstring gd xml bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress || true

# Copy app
COPY . .

# Generate key if missing (harmless if APP_KEY provided via secrets)
RUN php artisan key:generate --force || true
RUN php artisan config:cache || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy runtime configs
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

# Copy entrypoint script that will write secrets (e.g. Firebase JSON) to disk
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n"]
