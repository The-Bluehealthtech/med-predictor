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
    python3 python3-venv \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN python3 -m venv /opt/fit-imaging \
    && /opt/fit-imaging/bin/pip install --no-cache-dir pydicom==3.0.1 numpy==2.2.6 Pillow==11.3.0
RUN printf 'upload_max_filesize=20M\npost_max_size=105M\nmax_file_uploads=100\nmemory_limit=512M\n' > /usr/local/etc/php/conf.d/medical-imaging.ini

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

# Never ship stale compiled Blade output from the build context.
RUN rm -rf storage/framework/views/* \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN a2enmod rewrite

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Render supplies PORT at runtime.
# Apache configuration is rewritten before startup to listen on that port.
CMD ["sh", "bin/render-start.sh"]
