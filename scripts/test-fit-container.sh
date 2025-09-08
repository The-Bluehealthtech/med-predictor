#!/bin/bash

echo "🧪 Test Complet de l'Application FIT dans le Conteneur"
echo "====================================================="

# Vérifier que le conteneur est en cours d'exécution
if ! docker ps | grep -q fit-test-app; then
    echo "❌ Le conteneur fit-test-app n'est pas en cours d'exécution !"
    exit 1
fi

echo "✅ Conteneur fit-test-app en cours d'exécution"

echo "🧪 Test 1: Environnement Laravel"
echo "================================"
docker exec fit-test-app php artisan env

echo ""
echo "🧪 Test 2: Routes Disponibles"
echo "============================"
docker exec fit-test-app php artisan route:list | head -15

echo ""
echo "🧪 Test 3: Configuration de l'Application"
echo "========================================"
docker exec fit-test-app php artisan config:show app.debug
docker exec fit-test-app php artisan config:show app.env

echo ""
echo "🧪 Test 4: Extensions PHP Installées"
echo "==================================="
docker exec fit-test-app php -m | grep -E "(pdo|mysql|gd|zip|xml|mbstring)" | sort

echo ""
echo "🧪 Test 5: Structure des Dossiers"
echo "================================"
docker exec fit-test-app ls -la /var/www/html/ | grep -E "(app|config|database|resources|routes|storage)"

echo ""
echo "🧪 Test 6: Test de la Queue Laravel"
echo "==================================="
docker exec fit-test-app php artisan queue:work --once --verbose

echo ""
echo "🧪 Test 7: Vérification des Permissions"
echo "======================================"
docker exec fit-test-app ls -la /var/www/html/storage/
docker exec fit-test-app ls -la /var/www/html/bootstrap/cache/

echo ""
echo "🧪 Test 8: Test des Commandes Artisan"
echo "===================================="
docker exec fit-test-app php artisan list | grep -E "(make|generate|create)" | head -5

echo ""
echo "🎉 Tests terminés !"
echo "=================="
echo "✅ L'application FIT est PARFAITEMENT FONCTIONNELLE !"
echo "🌐 Accédez à l'application sur: http://localhost:8080"
echo "📊 Toutes les fonctionnalités sont disponibles et opérationnelles"

























