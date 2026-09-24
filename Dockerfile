# Stage 1: build frontend assets
FROM node:20-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run production


# Stage 2: Laravel / Apache
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Never package local environment files
RUN rm -f .env .env.* laravel.env

RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader

# Copy compiled Laravel Mix assets from frontend stage
COPY --from=frontend /app/public /var/www/html/public

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN a2enmod rewrite

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Render supplies PORT at runtime.
# Apache configuration is rewritten before startup to listen on that port.
CMD ["sh", "bin/render-start.sh"]
