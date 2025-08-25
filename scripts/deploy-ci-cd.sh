#!/bin/bash

# Script de déploiement CI/CD pour l'audit de base de données
# Usage: ./scripts/deploy-ci-cd.sh [environment]

set -e

# Configuration par défaut
ENVIRONMENT=${1:-"local"}
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction d'affichage avec couleurs
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Fonction de vérification des prérequis
check_prerequisites() {
    print_status "Vérification des prérequis..."
    
    # Vérifier Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker n'est pas installé"
        exit 1
    fi
    
    # Vérifier Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose n'est pas installé"
        exit 1
    fi
    
    # Vérifier que nous sommes dans le bon répertoire
    if [ ! -f "$PROJECT_ROOT/artisan" ]; then
        print_error "Ce script doit être exécuté depuis la racine du projet Laravel"
        exit 1
    fi
    
    print_success "Tous les prérequis sont satisfaits"
}

# Fonction de configuration de l'environnement
configure_environment() {
    print_status "Configuration de l'environnement: $ENVIRONMENT"
    
    case $ENVIRONMENT in
        "local")
            COMPOSE_FILE="docker-compose.ci.yml"
            ENV_FILE=".env.local"
            ;;
        "staging")
            COMPOSE_FILE="docker-compose.ci.yml"
            ENV_FILE=".env.staging"
            export WEBHOOK_URL=${WEBHOOK_URL:-"http://localhost:8080/webhook"}
            ;;
        "production")
            COMPOSE_FILE="docker-compose.ci.yml"
            ENV_FILE=".env.production"
            export WEBHOOK_URL=${WEBHOOK_URL:-"https://your-webhook-url.com/webhook"}
            ;;
        *)
            print_error "Environnement non reconnu: $ENVIRONMENT"
            print_status "Environnements supportés: local, staging, production"
            exit 1
            ;;
    esac
    
    # Charger les variables d'environnement si le fichier existe
    if [ -f "$PROJECT_ROOT/$ENV_FILE" ]; then
        print_status "Chargement des variables d'environnement depuis $ENV_FILE"
        export $(cat "$PROJECT_ROOT/$ENV_FILE" | grep -v '^#' | xargs)
    fi
    
    print_success "Environnement configuré: $ENVIRONMENT"
}

# Fonction de construction des images Docker
build_images() {
    print_status "Construction des images Docker..."
    
    cd "$PROJECT_ROOT"
    
    # Construire l'image principale
    print_status "Construction de l'image audit-runner..."
    docker build -f Dockerfile.ci -t db-audit-runner:latest .
    
    print_success "Images Docker construites avec succès"
}

# Fonction de démarrage des services
start_services() {
    print_status "Démarrage des services CI/CD..."
    
    cd "$PROJECT_ROOT"
    
    # Arrêter les services existants
    print_status "Arrêt des services existants..."
    docker-compose -f docker-compose.ci.yml down --remove-orphans
    
    # Démarrer les services
    print_status "Démarrage des services..."
    docker-compose -f docker-compose.ci.yml up -d
    
    # Attendre que les services soient prêts
    print_status "Attente du démarrage des services..."
    sleep 10
    
    print_success "Services démarrés avec succès"
}

# Fonction d'exécution de l'audit
run_audit() {
    print_status "Exécution de l'audit de base de données..."
    
    cd "$PROJECT_ROOT"
    
    # Exécuter l'audit
    print_status "Lancement de l'audit..."
    docker-compose -f docker-compose.ci.yml exec -T audit-runner php artisan project:db:audit
    
    # Vérifier que l'audit s'est bien déroulé
    if [ $? -eq 0 ]; then
        print_success "Audit exécuté avec succès"
    else
        print_error "L'audit a échoué"
        exit 1
    fi
}

# Fonction de récupération des rapports
collect_reports() {
    print_status "Récupération des rapports d'audit..."
    
    cd "$PROJECT_ROOT"
    
    # Créer le répertoire de rapports local
    mkdir -p audit-reports
    
    # Copier les rapports depuis le conteneur
    print_status "Copie des rapports depuis le conteneur..."
    docker cp db-audit-runner:/var/www/storage/logs/db-audit-*.log audit-reports/ 2>/dev/null || true
    docker cp db-audit-runner:/var/www/audit-reports/ audit-reports/ 2>/dev/null || true
    
    # Lister les rapports disponibles
    if [ -d "audit-reports" ] && [ "$(ls -A audit-reports)" ]; then
        print_success "Rapports récupérés avec succès"
        print_status "Rapports disponibles:"
        ls -la audit-reports/
    else
        print_warning "Aucun rapport trouvé"
    fi
}

# Fonction de vérification de la qualité
check_quality() {
    print_status "Vérification de la qualité..."
    
    cd "$PROJECT_ROOT"
    
    # Trouver le rapport le plus récent
    LATEST_REPORT=$(ls -t audit-reports/db-audit-*.log 2>/dev/null | head -1)
    
    if [ -z "$LATEST_REPORT" ]; then
        print_warning "Aucun rapport d'audit trouvé pour la vérification de qualité"
        return 0
    fi
    
    print_status "Analyse du rapport: $LATEST_REPORT"
    
    # Extraire les métriques clés
    CLEANLINESS_SCORE=$(grep -o '"cleanliness_score": "[^"]*"' "$LATEST_REPORT" | cut -d'"' -f4 | sed 's/%//')
    TOTAL_TABLES=$(grep -o '"total_tables": [0-9]*' "$LATEST_REPORT" | cut -d':' -f2 | tr -d ' ')
    ORPHAN_TABLES=$(grep -o '"orphan_tables": \[[^]]*\]' "$LATEST_REPORT" | grep -o '"[^"]*"' | wc -l)
    
    print_status "Métriques extraites:"
    echo "  - Score de propreté: ${CLEANLINESS_SCORE}%"
    echo "  - Tables totales: ${TOTAL_TABLES}"
    echo "  - Tables orphelines: ${ORPHAN_TABLES}"
    
    # Vérifier les critères de qualité
    if [ "$CLEANLINESS_SCORE" -lt 70 ]; then
        print_error "Score de propreté critique: ${CLEANLINESS_SCORE}%"
        return 1
    fi
    
    if [ "$ORPHAN_TABLES" -gt 20 ]; then
        print_warning "Trop de tables orphelines: ${ORPHAN_TABLES}"
    fi
    
    print_success "Critères de qualité respectés"
    return 0
}

# Fonction de nettoyage
cleanup() {
    print_status "Nettoyage de l'environnement..."
    
    cd "$PROJECT_ROOT"
    
    # Arrêter et supprimer les conteneurs
    docker-compose -f docker-compose.ci.yml down --remove-orphans
    
    # Supprimer les images temporaires
    docker image prune -f
    
    print_success "Nettoyage terminé"
}

# Fonction d'affichage de l'aide
show_help() {
    echo "Usage: $0 [environment] [options]"
    echo ""
    echo "Environnements:"
    echo "  local     - Environnement local (défaut)"
    echo "  staging   - Environnement de staging"
    echo "  production - Environnement de production"
    echo ""
    echo "Options:"
    echo "  --help, -h    - Afficher cette aide"
    echo "  --cleanup     - Nettoyer l'environnement après l'audit"
    echo "  --no-build    - Ne pas reconstruire les images Docker"
    echo "  --no-start    - Ne pas démarrer les services"
    echo ""
    echo "Exemples:"
    echo "  $0                    # Déploiement local"
    echo "  $0 staging            # Déploiement staging"
    echo "  $0 production --cleanup # Déploiement production avec nettoyage"
}

# Fonction principale
main() {
    print_status "🚀 Démarrage du déploiement CI/CD pour l'audit de base de données"
    print_status "Environnement: $ENVIRONMENT"
    print_status "Répertoire du projet: $PROJECT_ROOT"
    
    # Variables pour les options
    CLEANUP_AFTER=false
    BUILD_IMAGES=true
    START_SERVICES=true
    
    # Traitement des arguments
    shift
    while [[ $# -gt 0 ]]; do
        case $1 in
            --help|-h)
                show_help
                exit 0
                ;;
            --cleanup)
                CLEANUP_AFTER=true
                shift
                ;;
            --no-build)
                BUILD_IMAGES=false
                shift
                ;;
            --no-start)
                START_SERVICES=false
                shift
                ;;
            *)
                print_error "Option inconnue: $1"
                show_help
                exit 1
                ;;
        esac
    done
    
    # Vérification des prérequis
    check_prerequisites
    
    # Configuration de l'environnement
    configure_environment
    
    # Construction des images si demandé
    if [ "$BUILD_IMAGES" = true ]; then
        build_images
    fi
    
    # Démarrage des services si demandé
    if [ "$START_SERVICES" = true ]; then
        start_services
    fi
    
    # Exécution de l'audit
    run_audit
    
    # Récupération des rapports
    collect_reports
    
    # Vérification de la qualité
    if check_quality; then
        print_success "🎉 Audit terminé avec succès !"
    else
        print_error "❌ Audit échoué - critères de qualité non respectés"
        exit 1
    fi
    
    # Nettoyage si demandé
    if [ "$CLEANUP_AFTER" = true ]; then
        cleanup
    fi
    
    print_success "✅ Déploiement CI/CD terminé avec succès"
}

# Exécution du script principal
main "$@"
