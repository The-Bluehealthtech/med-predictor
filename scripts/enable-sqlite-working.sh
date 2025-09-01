#!/bin/bash

echo "🔧 Activation de SQLite Fonctionnel pour FIT..."
echo "================================================"

# Vérifier que le conteneur PHP est en cours d'exécution
if ! docker ps | grep -q fit-php; then
    echo "❌ Conteneur PHP-FPM non trouvé !"
    exit 1
fi

echo "✅ Conteneur PHP-FPM trouvé"

# Créer une configuration SQLite fonctionnelle
echo "🔧 Création de la configuration SQLite..."

cat << 'EOF' > /tmp/.env.sqlite
APP_NAME="FIT - Football Injury Tracking"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Configuration SQLite fonctionnelle
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# Configuration cache et sessions en fichier
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=fit_

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Désactiver Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MEMCACHED_HOST=127.0.0.1

MAIL_MAILER=log
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

# Copier la configuration dans le conteneur
echo "📝 Copie de la configuration SQLite..."
docker cp /tmp/.env.sqlite fit-php:/var/www/html/.env

# Vérifier que la copie a réussi
if docker exec fit-php test -f /var/www/html/.env; then
    echo "✅ Configuration copiée avec succès"
else
    echo "❌ Échec de la copie de la configuration"
    exit 1
fi

# Créer la base de données SQLite
echo "🗄️ Création de la base de données SQLite..."
docker exec fit-php sh -c "mkdir -p /var/www/html/database"
docker exec fit-php sh -c "touch /var/www/html/database/database.sqlite"
docker exec fit-php sh -c "chown -R www-data:www-data /var/www/html/database"
docker exec fit-php sh -c "chmod 664 /var/www/html/database/database.sqlite"

# Vérifier que la base de données existe
if docker exec fit-php test -f /var/www/html/database/database.sqlite; then
    echo "✅ Base de données SQLite créée"
else
    echo "❌ Échec de la création de la base de données"
    exit 1
fi

# Supprimer le cache
echo "🧹 Suppression du cache..."
docker exec fit-php sh -c "rm -rf /var/www/html/bootstrap/cache/*"

# Nettoyer le cache Laravel
echo "🧹 Nettoyage du cache Laravel..."
docker exec fit-php php artisan config:clear
docker exec fit-php php artisan cache:clear

# Générer une nouvelle clé d'application
echo "🔑 Génération de la clé d'application..."
docker exec fit-php php artisan key:generate

# Créer les tables de base de données
echo "🏗️ Création des tables de base de données..."
docker exec fit-php php artisan migrate --force

# Vérifier la configuration
echo "🧪 Test de la configuration..."
docker exec fit-php php artisan config:show db.connection
docker exec fit-php php artisan config:show db.database

# Nettoyer le fichier temporaire
rm -f /tmp/.env.sqlite

echo ""
echo "🎉 Configuration SQLite terminée !"
echo "🗄️ Base de données : /var/www/html/database/database.sqlite"
echo "🌐 Testez maintenant: http://localhost:8080"
echo "📊 L'application peut maintenant sauvegarder des données !"







