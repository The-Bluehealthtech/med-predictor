#!/bin/bash

echo "🏆 Configuration du Système de Licences et Upload de Photos..."

# Vérifier que Docker est en cours d'exécution
if ! docker ps > /dev/null 2>&1; then
    echo "❌ Docker n'est pas en cours d'exécution. Démarrez Docker d'abord."
    exit 1
fi

echo "📁 Configuration du stockage Laravel..."

# Créer le lien symbolique pour le stockage
docker exec fit-php php artisan storage:link

# Créer les répertoires de stockage
docker exec fit-php mkdir -p storage/app/public/player_photos
docker exec fit-php mkdir -p storage/app/public/association_logos
docker exec fit-php mkdir -p storage/app/public/club_logos

# Définir les permissions
docker exec fit-php chmod -R 775 storage/app/public
docker exec fit-php chown -R www-data:www-data storage/app/public

echo "🗄️ Exécution des migrations..."

# Exécuter les migrations
docker exec fit-php php artisan migrate

echo "🔄 Nettoyage du cache..."

# Nettoyer le cache
docker exec fit-php php artisan config:clear
docker exec fit-php php artisan cache:clear
docker exec fit-php php artisan route:clear
docker exec fit-php php artisan view:clear

echo "✅ Configuration terminée !"
echo ""
echo "🎯 Vous pouvez maintenant :"
echo "   1. Aller sur http://localhost:8080/licenses/upload-photo pour uploader des photos"
echo "   2. Voir la liste des licences sur http://localhost:8080/licenses/list"
echo "   3. Les photos seront stockées dans storage/app/public/player_photos"
echo ""
echo "💡 Le système permet aux clubs d'uploader des vraies photos de leurs joueurs"
echo "   lors de la création des licences, remplaçant les avatars génériques."


