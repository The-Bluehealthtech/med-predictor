#!/bin/bash

echo "🔧 Configuration de SQLite pour Tests Locaux..."
echo "=============================================="

# Vérifier que le conteneur PHP est en cours d'exécution
if ! docker ps | grep -q fit-php; then
    echo "❌ Conteneur PHP-FPM non trouvé !"
    exit 1
fi

echo "✅ Conteneur PHP-FPM trouvé"

# Créer le dossier database s'il n'existe pas
echo "📁 Création du dossier database..."
docker exec fit-php mkdir -p /var/www/html/database

# Créer un fichier SQLite vide
echo "🗄️ Création du fichier SQLite..."
docker exec fit-php touch /var/www/html/database/database.sqlite

# Vérifier que le fichier a été créé
if docker exec fit-php test -f /var/www/html/database/database.sqlite; then
    echo "✅ Fichier SQLite créé avec succès"
else
    echo "❌ Échec de la création du fichier SQLite"
    exit 1
fi

# Configurer les permissions
echo "🔒 Configuration des permissions..."
docker exec fit-php chown www-data:www-data /var/www/html/database/database.sqlite
docker exec fit-php chmod 664 /var/www/html/database/database.sqlite

# Nettoyer le cache Laravel
echo "🧹 Nettoyage du cache Laravel..."
docker exec fit-php php artisan config:clear
docker exec fit-php php artisan cache:clear

# Tester la connexion à la base de données
echo "🧪 Test de la connexion à la base de données..."
docker exec fit-php php artisan tinker --execute="echo 'Test DB: '; try { DB::connection()->getPdo(); echo 'Connexion OK'; } catch(Exception \$e) { echo 'ERREUR: ' . \$e->getMessage(); }"

echo ""
echo "🎉 Configuration SQLite terminée !"
echo "🌐 Testez maintenant: http://localhost:8080"
echo "📊 L'application devrait fonctionner avec SQLite local"







