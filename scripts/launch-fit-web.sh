#!/bin/bash

echo "🚀 Lancement de FIT avec Interface Web..."
echo "=========================================="

# Arrêter les conteneurs existants
echo "🧹 Nettoyage des conteneurs existants..."
docker stop fit-test-app 2>/dev/null
docker rm fit-test-app 2>/dev/null
docker stop fit-nginx 2>/dev/null
docker rm fit-nginx 2>/dev/null

# Créer un réseau Docker
echo "🌐 Création du réseau Docker..."
docker network create fit-network 2>/dev/null || echo "Réseau existant"

# Lancer le conteneur PHP-FPM
echo "🐘 Lancement du conteneur PHP-FPM..."
docker run -d \
    --name fit-php \
    --network fit-network \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    fit-local:test

# Attendre que PHP-FPM soit prêt
echo "⏳ Attente de PHP-FPM..."
sleep 10

# Lancer le conteneur Nginx
echo "🌐 Lancement du conteneur Nginx..."
docker run -d \
    --name fit-nginx \
    --network fit-network \
    -p 8080:80 \
    -v $(pwd):/var/www/html \
    -v $(pwd)/docker/nginx-simple.conf:/etc/nginx/conf.d/default.conf \
    nginx:alpine

# Attendre que Nginx soit prêt
echo "⏳ Attente de Nginx..."
sleep 5

# Vérifier l'état
echo "📊 État des conteneurs:"
docker ps | grep -E "(fit-php|fit-nginx)"

echo ""
echo "🎉 FIT est maintenant accessible sur: http://localhost:8080"
echo "🔍 Vérifiez les logs avec: docker logs fit-nginx"
echo "🐘 Logs PHP-FPM: docker logs fit-php"







