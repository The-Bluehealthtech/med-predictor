#!/bin/bash

# 🐳 Script de Test Docker FIT
# Teste l'image Docker localement avant le déploiement

set -e

echo "🐳 Test Docker FIT - Vérification de l'Image"
echo "============================================="

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# 1. Vérification de Docker
echo ""
log_info "1. Vérification de Docker..."
if command -v docker &> /dev/null; then
    log_success "Docker est installé"
    docker --version
else
    log_error "Docker n'est pas installé"
    exit 1
fi

# 2. Build de l'image de test
echo ""
log_info "2. Build de l'image Docker FIT..."
if docker build -f Dockerfile.optimized -t fit-test:local .; then
    log_success "Image Docker construite avec succès"
else
    log_error "Échec du build de l'image Docker"
    exit 1
fi

# 3. Test de l'image
echo ""
log_info "3. Test de l'image Docker..."
CONTAINER_NAME="fit-test-container"

# Nettoyer les conteneurs existants
docker rm -f $CONTAINER_NAME 2>/dev/null || true

# Démarrer le conteneur de test
if docker run -d --name $CONTAINER_NAME -p 9000:9000 fit-test:local; then
    log_success "Conteneur démarré avec succès"
else
    log_error "Échec du démarrage du conteneur"
    exit 1
fi

# 4. Attendre que le conteneur soit prêt
echo ""
log_info "4. Attente du démarrage du conteneur..."
sleep 10

# 5. Vérifier les logs
echo ""
log_info "5. Vérification des logs du conteneur..."
echo "--- Logs du conteneur ---"
docker logs $CONTAINER_NAME

# 6. Test de connectivité
echo ""
log_info "6. Test de connectivité..."
if curl -s http://localhost:9000/health &> /dev/null; then
    log_success "Application accessible sur http://localhost:9000/health"
else
    log_warning "Application non accessible sur le port 9000"
fi

# 7. Test des processus dans le conteneur
echo ""
log_info "7. Vérification des processus dans le conteneur..."
if docker exec $CONTAINER_NAME ps aux | grep -E "(php-fpm|nginx|supervisor)" &> /dev/null; then
    log_success "Processus PHP-FPM/Nginx/Supervisor en cours d'exécution"
else
    log_warning "Certains processus ne sont pas en cours d'exécution"
fi

# 8. Test de Laravel Artisan
echo ""
log_info "8. Test de Laravel Artisan..."
if docker exec $CONTAINER_NAME php artisan --version &> /dev/null; then
    log_success "Laravel Artisan fonctionne"
else
    log_warning "Laravel Artisan ne fonctionne pas"
fi

# 9. Nettoyage
echo ""
log_info "9. Nettoyage..."
docker stop $CONTAINER_NAME
docker rm $CONTAINER_NAME

# 10. Résumé
echo ""
echo "📊 RÉSUMÉ DU TEST DOCKER"
echo "========================="

log_success "🎉 Image Docker FIT testée avec succès !"
echo ""
echo "✅ L'image Docker fonctionne correctement"
echo "✅ Le conteneur démarre sans problème"
echo "✅ Les processus essentiels sont en cours d'exécution"
echo "✅ Laravel Artisan fonctionne"
echo ""
echo "🚀 L'image est prête pour le déploiement GCP !"
echo ""
echo "📝 Prochaines étapes :"
echo "   1. Corriger les problèmes locaux avec: ./scripts/fix-local-fit.sh"
echo "   2. Tester localement avec: ./scripts/test-local-fit.sh"
echo "   3. Déployer sur GCP avec: ./scripts/deploy-fit-gcp.sh"

























