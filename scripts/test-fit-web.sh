#!/bin/bash

echo "🧪 Test Web de l'Application FIT..."
echo "==================================="

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

echo "🧪 Test 1: Endpoint de santé..."
curl -s http://localhost:8080/health

echo ""
echo "🧪 Test 2: Page principale (HEAD)..."
curl -s -I http://localhost:8080/ | head -3

echo ""
echo "🧪 Test 3: Page principale (GET)..."
curl -s http://localhost:8080/ | head -5

echo ""
echo "🧪 Test 4: Test direct PHP..."
docker exec fit-php php -r "echo 'PHP version: ' . phpversion() . PHP_EOL;"

echo ""
echo "🧪 Test 5: Test Laravel..."
docker exec fit-php php artisan env

echo ""
echo "🧪 Test 6: Vérification des logs..."
echo "Logs Nginx:"
docker logs fit-nginx --tail=3

echo ""
echo "Logs PHP-FPM:"
docker logs fit-php --tail=3

echo ""
echo "🎯 Diagnostic terminé !"







