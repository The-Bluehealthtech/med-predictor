#!/bin/bash

echo "🧪 Test Complet de l'Application FIT..."
echo "======================================="

# Vérifier que les conteneurs sont en cours d'exécution
if ! docker ps | grep -q fit-php; then
    echo "❌ Conteneur PHP-FPM non trouvé !"
    exit 1
fi

if ! docker ps | grep -q fit-nginx; then
    echo "❌ Conteneur Nginx non trouvé !"
    exit 1
fi

echo "✅ Conteneurs en cours d'exécution"

echo ""
echo "🧪 Test 1: Configuration Laravel..."
docker exec fit-php php artisan config:show app.debug
docker exec fit-php php artisan config:show app.env

echo ""
echo "🧪 Test 2: Routes disponibles..."
docker exec fit-php php artisan route:list | head -5

echo ""
echo "🧪 Test 3: Test de la base de données..."
docker exec fit-php php artisan config:show db.connection 2>/dev/null || echo "Configuration DB non trouvée"

echo ""
echo "🧪 Test 4: Test de session sans base de données..."
docker exec fit-php php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Laravel démarré avec succès' . PHP_EOL;
"

echo ""
echo "🧪 Test 5: Test web simple..."
echo "Test de l'endpoint de santé..."
curl -s http://localhost:8080/health

echo ""
echo "Test de la page principale..."
curl -s http://localhost:8080/ | head -10

echo ""
echo "🧪 Test 6: Vérification des logs..."
echo "Logs Nginx:"
docker logs fit-nginx --tail=3

echo ""
echo "Logs PHP-FPM:"
docker logs fit-php --tail=3

echo ""
echo "🎯 Tests terminés !"
echo "🔍 Si il y a encore des erreurs, vérifiez les logs ci-dessus"







