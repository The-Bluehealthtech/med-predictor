# Multi-stage Dockerfile for Laravel Application
# Stage 1: Composer dependencies
FROM composer:2.6 as composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Stage 2: Build assets (if you have frontend build process)
FROM node:18-alpine as node

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --only=production

COPY resources/ ./resources/
RUN npm run build

# Stage 3: Production runtime
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    postgresql-client \
    sqlite \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
        ctype \
        iconv \
        intl

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --from=composer /app/vendor/ ./vendor/
COPY --from=node /app/public/build/ ./public/build/
COPY . .

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod +x /entrypoint.sh

# Generate Laravel key and optimize
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Start services
ENTRYPOINT ["/entrypoint.sh"]
