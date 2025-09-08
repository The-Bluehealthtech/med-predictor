# Image finale avec Apache et PHP
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code de l'application (en excluant les fichiers de configuration)
COPY . /var/www/html/

# Supprimer les fichiers de configuration qui ne doivent pas être dans l'image
RUN rm -f /var/www/html/.env* /var/www/html/laravel.env

# Installer les dépendances
WORKDIR /var/www/html
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --ignore-platform-reqs

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurer Apache pour Laravel
RUN a2enmod rewrite

# Copier notre configuration d'hôte virtuel pour pointer vers le dossier /public
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]