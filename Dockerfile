# ./Dockerfile
FROM php:8.2-apache

# system deps
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# enable apache rewrite
RUN a2enmod rewrite

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy app
WORKDIR /var/www/html
COPY . .

# install deps & cache config
RUN composer install --no-dev --optimize-autoloader \
    && cp .env.example .env \
    && php artisan key:generate \
    && php artisan config:cache && php artisan route:cache

CMD ["apache2-foreground"]
