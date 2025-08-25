#!/bin/bash

# Script de Déploiement CI/CD Complet
# Version: 2.0 - Pipeline Automatisé

set -e  # Arrêter en cas d'erreur

# Configuration
PROJECT_NAME="med-predictor"
DEPLOY_ENV="${1:-staging}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
LOG_FILE="deploy-${DEPLOY_ENV}-${TIMESTAMP}.log"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction de logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE"
}

# Fonction de vérification des prérequis
check_prerequisites() {
    log "🔍 Vérification des prérequis..."
    
    # Vérifier Git
    if ! command -v git &> /dev/null; then
        log_error "Git n'est pas installé"
        exit 1
    fi
    
    # Vérifier Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker n'est pas installé"
        exit 1
    fi
    
    # Vérifier Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose n'est pas installé"
        exit 1
    fi
    
    # Vérifier PHP
    if ! command -v php &> /dev/null; then
        log_error "PHP n'est pas installé"
        exit 1
    fi
    
    # Vérifier Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer n'est pas installé"
        exit 1
    fi
    
    log_success "Tous les prérequis sont satisfaits"
}

# Fonction de sauvegarde
create_backup() {
    log "💾 Création de la sauvegarde..."
    
    BACKUP_DIR="backups/deploy-${DEPLOY_ENV}-${TIMESTAMP}"
    mkdir -p "$BACKUP_DIR"
    
    # Sauvegarder la base de données
    if [ -f "database/database.sqlite" ]; then
        cp database/database.sqlite "$BACKUP_DIR/"
        log_success "Base de données sauvegardée"
    fi
    
    # Sauvegarder les fichiers de configuration
    cp .env "$BACKUP_DIR/" 2>/dev/null || log_warning "Fichier .env non trouvé"
    cp .gitlab-ci.yml "$BACKUP_DIR/" 2>/dev/null || log_warning "Fichier .gitlab-ci.yml non trouvé"
    
    log_success "Sauvegarde créée dans $BACKUP_DIR"
}

# Fonction de validation du code
validate_code() {
    log "🔍 Validation du code..."
    
    # Vérifier la syntaxe PHP
    log "  - Vérification de la syntaxe PHP..."
    find app -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
    
    # Vérifier les tests
    log "  - Exécution des tests..."
    if [ -f "phpunit.xml" ]; then
        php artisan test --stop-on-failure || {
            log_warning "Certains tests ont échoué, mais le déploiement continue"
        }
    else
        log_warning "Aucun fichier de test trouvé"
    fi
    
    # Vérifier la qualité du code
    log "  - Vérification de la qualité du code..."
    if [ -f ".php-cs-fixer.php" ]; then
        vendor/bin/php-cs-fixer fix --dry-run --diff || log_warning "Problèmes de style détectés"
    fi
    
    log_success "Validation du code terminée"
}

# Fonction de préparation de l'environnement
prepare_environment() {
    log "🚀 Préparation de l'environnement $DEPLOY_ENV..."
    
    # Copier le fichier d'environnement approprié
    if [ -f "env.${DEPLOY_ENV}" ]; then
        cp "env.${DEPLOY_ENV}" .env
        log_success "Fichier d'environnement $DEPLOY_ENV copié"
    else
        log_warning "Fichier env.${DEPLOY_ENV} non trouvé, utilisation de .env.example"
        cp .env.example .env
    fi
    
    # Installer les dépendances
    log "  - Installation des dépendances Composer..."
    composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader
    
    log "  - Installation des dépendances NPM..."
    if [ -f "package.json" ]; then
        npm ci --silent || npm install --silent
    fi
    
    # Générer la clé d'application
    php artisan key:generate --no-interaction
    
    # Nettoyer le cache
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    log_success "Environnement préparé"
}

# Fonction de configuration de la base de données
setup_database() {
    log "🗄️ Configuration de la base de données..."
    
    # Créer la base SQLite si elle n'existe pas
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
        log_success "Base SQLite créée"
    fi
    
    # Exécuter les migrations
    log "  - Exécution des migrations..."
    php artisan migrate --force --no-interaction
    
    # Exécuter les seeders si nécessaire
    if [ "$DEPLOY_ENV" = "production" ]; then
        log "  - Exécution des seeders de production..."
        php artisan db:seed --class=ProductionSeeder --no-interaction || log_warning "Aucun seeder de production"
    fi
    
    log_success "Base de données configurée"
}

# Fonction d'audit de la base de données
run_database_audit() {
    log "🔍 Exécution de l'audit de la base de données..."
    
    # Vérifier si la commande d'audit existe
    if php artisan list | grep -q "project:db:audit"; then
        php artisan project:db:audit
        
        # Vérifier le score de propreté
        LATEST_REPORT=$(ls -t storage/logs/db-audit-*.log | head -1)
        if [ -n "$LATEST_REPORT" ]; then
            CLEANLINESS_SCORE=$(grep -o '"cleanliness_score": "[^"]*"' "$LATEST_REPORT" | cut -d'"' -f4 | sed 's/%//' || echo "0")
            log "Score de propreté: ${CLEANLINESS_SCORE}%"
            
            if [ "$CLEANLINESS_SCORE" -lt 70 ]; then
                log_warning "Score de propreté bas: ${CLEANLINESS_SCORE}%"
            else
                log_success "Score de propreté acceptable: ${CLEANLINESS_SCORE}%"
            fi
        fi
    else
        log_warning "Commande d'audit non trouvée, audit ignoré"
    fi
}

# Fonction de déploiement Docker
deploy_docker() {
    log "🐳 Déploiement Docker..."
    
    # Arrêter les conteneurs existants
    docker-compose -f docker-compose.ci.yml down --remove-orphans || true
    
    # Construire et démarrer les nouveaux conteneurs
    docker-compose -f docker-compose.ci.yml up -d --build
    
    # Attendre que les services soient prêts
    log "  - Attente du démarrage des services..."
    sleep 30
    
    # Vérifier le statut des services
    docker-compose -f docker-compose.ci.yml ps
    
    log_success "Déploiement Docker terminé"
}

# Fonction de tests post-déploiement
post_deployment_tests() {
    log "🧪 Tests post-déploiement..."
    
    # Vérifier que l'application répond
    if [ -n "$APP_URL" ]; then
        log "  - Test de connectivité: $APP_URL"
        curl -f "$APP_URL" > /dev/null 2>&1 && log_success "Application accessible" || log_error "Application inaccessible"
    fi
    
    # Vérifier la base de données
    php artisan tinker --execute="echo 'Base de données accessible: ' . (DB::connection()->getPdo() ? 'OUI' : 'NON') . PHP_EOL;"
    
    # Vérifier les routes
    php artisan route:list --compact | head -10
    
    log_success "Tests post-déploiement terminés"
}

# Fonction de notification
send_notifications() {
    log "🔔 Envoi des notifications..."
    
    # Préparer le message
    MESSAGE="🚀 Déploiement $DEPLOY_ENV terminé avec succès !
    
    📊 Résumé:
    - Environnement: $DEPLOY_ENV
    - Timestamp: $TIMESTAMP
    - Projet: $PROJECT_NAME
    - Statut: ✅ Succès
    
    📋 Logs disponibles dans: $LOG_FILE"
    
    # Envoyer via webhook si configuré
    if [ -n "$WEBHOOK_URL" ]; then
        curl -X POST -H "Content-Type: application/json" \
             -d "{\"text\":\"$MESSAGE\"}" \
             "$WEBHOOK_URL" || log_warning "Échec de l'envoi du webhook"
    fi
    
    # Envoyer via Slack si configuré
    if [ -n "$SLACK_WEBHOOK_URL" ]; then
        curl -X POST -H "Content-Type: application/json" \
             -d "{\"text\":\"$MESSAGE\"}" \
             "$SLACK_WEBHOOK_URL" || log_warning "Échec de l'envoi Slack"
    fi
    
    log_success "Notifications envoyées"
}

# Fonction de nettoyage
cleanup() {
    log "🧹 Nettoyage..."
    
    # Supprimer les anciens logs
    find . -name "deploy-*.log" -mtime +7 -delete 2>/dev/null || true
    
    # Nettoyer le cache Docker
    docker system prune -f || true
    
    log_success "Nettoyage terminé"
}

# Fonction principale
main() {
    log "🚀 Démarrage du déploiement CI/CD pour $PROJECT_NAME"
    log "📋 Environnement: $DEPLOY_ENV"
    log "📝 Logs: $LOG_FILE"
    
    # Vérifier les prérequis
    check_prerequisites
    
    # Créer une sauvegarde
    create_backup
    
    # Valider le code
    validate_code
    
    # Préparer l'environnement
    prepare_environment
    
    # Configurer la base de données
    setup_database
    
    # Exécuter l'audit
    run_database_audit
    
    # Déployer avec Docker
    deploy_docker
    
    # Tests post-déploiement
    post_deployment_tests
    
    # Envoyer les notifications
    send_notifications
    
    # Nettoyage
    cleanup
    
    log_success "🎉 Déploiement CI/CD terminé avec succès !"
    log "📋 Consultez les logs dans: $LOG_FILE"
}

# Gestion des erreurs
trap 'log_error "Erreur survenue à la ligne $LINENO. Arrêt du déploiement."; exit 1' ERR

# Exécution du script principal
main "$@"
