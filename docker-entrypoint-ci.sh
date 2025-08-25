#!/bin/sh

# Script d'entrée pour l'environnement CI/CD
set -e

echo "🚀 Démarrage de l'environnement CI/CD..."

# Vérification des variables d'environnement
if [ -z "$COMPOSER_CACHE_DIR" ]; then
    echo "⚠️  COMPOSER_CACHE_DIR non définie, utilisation de la valeur par défaut"
    export COMPOSER_CACHE_DIR="/var/www/.composer-cache"
fi

if [ -z "$AUDIT_REPORT_DIR" ]; then
    echo "⚠️  AUDIT_REPORT_DIR non définie, utilisation de la valeur par défaut"
    export AUDIT_REPORT_DIR="/var/www/audit-reports"
fi

# Création des répertoires nécessaires
echo "📁 Création des répertoires..."
mkdir -p "$COMPOSER_CACHE_DIR"
mkdir -p "$AUDIT_REPORT_DIR"
mkdir -p /var/www/storage/logs
mkdir -p /var/www/database

# Vérification de la présence de Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer non trouvé, installation..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Vérification de la présence de Laravel
if [ ! -f "artisan" ]; then
    echo "❌ Laravel non trouvé dans le répertoire de travail"
    exit 1
fi

# Configuration de l'environnement
echo "⚙️  Configuration de l'environnement..."
if [ ! -f ".env" ]; then
    echo "📋 Copie du fichier .env.example..."
    cp .env.example .env
fi

# Génération de la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate --no-interaction

# Configuration de la base de données SQLite
echo "🗄️  Configuration de la base de données SQLite..."
if [ ! -f "database/database.sqlite" ]; then
    echo "📝 Création de la base SQLite..."
    touch database/database.sqlite
fi

# Mise à jour de la configuration de la base
if ! grep -q "DB_CONNECTION=sqlite" .env; then
    echo "DB_CONNECTION=sqlite" >> .env
fi

if ! grep -q "DB_DATABASE=database/database.sqlite" .env; then
    echo "DB_DATABASE=database/database.sqlite" >> .env
fi

# Exécution des migrations
echo "🔄 Exécution des migrations..."
php artisan migrate --force --no-interaction

# Vérification de la commande d'audit
echo "🔍 Vérification de la commande d'audit..."
if ! php artisan list | grep -q "project:db:audit"; then
    echo "❌ Commande project:db:audit non trouvée"
    echo "📋 Commandes disponibles:"
    php artisan list
    exit 1
fi

# Nettoyage du cache
echo "🧹 Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Affichage des informations de l'environnement
echo "📊 Informations de l'environnement:"
echo "   PHP Version: $(php -v | head -1)"
echo "   Composer Version: $(composer --version | head -1)"
echo "   Laravel Version: $(php artisan --version)"
echo "   Répertoire de travail: $(pwd)"
echo "   Cache Composer: $COMPOSER_CACHE_DIR"
echo "   Rapports d'audit: $AUDIT_REPORT_DIR"

# Exécution de la commande passée en paramètre ou de la commande par défaut
if [ $# -eq 0 ]; then
    echo "🚀 Exécution de la commande par défaut: project:db:audit"
    exec php artisan project:db:audit
else
    echo "🚀 Exécution de la commande: $@"
    exec "$@"
fi
