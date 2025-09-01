#!/bin/bash

echo "🔧 Correction de la Configuration du Cache..."
echo "============================================"

# Vérifier que le conteneur PHP est en cours d'exécution
if ! docker ps | grep -q fit-php; then
    echo "❌ Conteneur PHP-FPM non trouvé !"
    exit 1
fi

echo "✅ Conteneur PHP-FPM trouvé"

# Créer une configuration temporaire avec cache en fichier
echo "🔧 Création de la configuration du cache..."

cat << 'EOF' > /tmp/.env.cache
APP_NAME="FIT - Football Injury Tracking"
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Configuration sans base de données pour tests locaux
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database/database.sqlite
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
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
echo "📝 Copie de la configuration dans le conteneur..."
docker cp /tmp/.env.cache fit-php:/var/www/html/.env

# Vérifier que la copie a réussi
if docker exec fit-php test -f /var/www/html/.env; then
    echo "✅ Configuration copiée avec succès"
else
    echo "❌ Échec de la copie de la configuration"
    exit 1
fi

# Nettoyer le cache Laravel
echo "🧹 Nettoyage du cache Laravel..."
docker exec fit-php php artisan config:clear
docker exec fit-php php artisan cache:clear

# Tester la nouvelle configuration
echo "🧪 Test de la nouvelle configuration..."
docker exec fit-php php artisan config:show cache.default
docker exec fit-php php artisan config:show session.driver

# Nettoyer le fichier temporaire
rm -f /tmp/.env.cache

echo ""
echo "🎉 Configuration du cache corrigée !"
echo "🌐 Testez maintenant: http://localhost:8080"
echo "📊 L'application devrait fonctionner avec le cache en fichier"







