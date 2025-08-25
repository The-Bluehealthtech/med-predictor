#!/bin/bash

# =============================================================================
# SCRIPT DE MONITORING - MICROSERVICE FIT
# =============================================================================

set -e

# Configuration
SERVICE_NAME="fit-service"
SERVICE_URL="${FIT_SERVICE_URL:-http://localhost:3000}"
HEALTH_ENDPOINT="${SERVICE_URL}/health"
METRICS_ENDPOINT="${SERVICE_URL}/metrics"
LOG_FILE="${LOG_FILE:-logs/monitoring.log}"
ALERT_EMAIL="${ALERT_EMAIL:-admin@example.com}"
SLACK_WEBHOOK="${SLACK_WEBHOOK:-}"
CHECK_INTERVAL="${CHECK_INTERVAL:-60}"
MAX_RETRIES="${MAX_RETRIES:-3}"

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction de logging
log() {
    local level=$1
    shift
    local message="$*"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    case $level in
        "INFO")
            echo -e "${BLUE}[${timestamp}] INFO: ${message}${NC}"
            ;;
        "SUCCESS")
            echo -e "${GREEN}[${timestamp}] SUCCESS: ${message}${NC}"
            ;;
        "WARNING")
            echo -e "${YELLOW}[${timestamp}] WARNING: ${message}${NC}"
            ;;
        "ERROR")
            echo -e "${RED}[${timestamp}] ERROR: ${message}${NC}"
            ;;
    esac
    
    echo "[${timestamp}] ${level}: ${message}" >> "$LOG_FILE"
}

# Fonction d'envoi d'alerte par email
send_email_alert() {
    local subject="$1"
    local message="$2"
    
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "$subject" "$ALERT_EMAIL"
        log "INFO" "Alerte email envoyée à $ALERT_EMAIL"
    else
        log "WARNING" "Commande 'mail' non disponible, impossible d'envoyer l'alerte email"
    fi
}

# Fonction d'envoi d'alerte Slack
send_slack_alert() {
    local message="$1"
    
    if [[ -n "$SLACK_WEBHOOK" ]]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"🚨 $message\"}" \
            "$SLACK_WEBHOOK" > /dev/null 2>&1
        log "INFO" "Alerte Slack envoyée"
    fi
}

# Fonction de vérification de la santé du service
check_health() {
    local retries=0
    local max_retries=$MAX_RETRIES
    
    while [ $retries -lt $max_retries ]; do
        if curl -f -s "$HEALTH_ENDPOINT" > /dev/null 2>&1; then
            return 0
        fi
        
        retries=$((retries + 1))
        if [ $retries -lt $max_retries ]; then
            log "WARNING" "Tentative $retries/$max_retries échouée, nouvelle tentative dans 5 secondes..."
            sleep 5
        fi
    done
    
    return 1
}

# Fonction de récupération des métriques
get_metrics() {
    if curl -f -s "$METRICS_ENDPOINT" > /dev/null 2>&1; then
        local metrics=$(curl -s "$METRICS_ENDPOINT")
        echo "$metrics"
    else
        echo "{}"
    fi
}

# Fonction de vérification des bases de données
check_databases() {
    local all_healthy=true
    
    # Vérification MongoDB
    if ! mongosh --eval "db.runCommand('ping')" > /dev/null 2>&1; then
        log "ERROR" "MongoDB n'est pas accessible"
        all_healthy=false
    fi
    
    # Vérification PostgreSQL
    if ! PGPASSWORD=fit_password psql -h localhost -U fit_user -d fit_database -c "SELECT 1;" > /dev/null 2>&1; then
        log "ERROR" "PostgreSQL n'est pas accessible"
        all_healthy=false
    fi
    
    # Vérification Redis
    if ! redis-cli ping > /dev/null 2>&1; then
        log "ERROR" "Redis n'est pas accessible"
        all_healthy=false
    fi
    
    if [ "$all_healthy" = true ]; then
        log "SUCCESS" "Toutes les bases de données sont accessibles"
        return 0
    else
        return 1
    fi
}

# Fonction de vérification de l'espace disque
check_disk_space() {
    local threshold=90
    local usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
    
    if [ "$usage" -gt "$threshold" ]; then
        log "WARNING" "Espace disque critique: ${usage}% utilisé"
        send_email_alert "Alerte Espace Disque" "L'espace disque est à ${usage}% d'utilisation"
        send_slack_alert "⚠️ Espace disque critique: ${usage}% utilisé"
        return 1
    fi
    
    return 0
}

# Fonction de vérification de la mémoire
check_memory() {
    local threshold=90
    local usage=$(free | grep Mem | awk '{printf("%.0f", $3/$2 * 100.0)}')
    
    if [ "$usage" -gt "$threshold" ]; then
        log "WARNING" "Utilisation mémoire élevée: ${usage}%"
        send_email_alert "Alerte Mémoire" "L'utilisation mémoire est à ${usage}%"
        send_slack_alert "⚠️ Utilisation mémoire élevée: ${usage}%"
        return 1
    fi
    
    return 0
}

# Fonction de sauvegarde des logs
rotate_logs() {
    local max_size_mb=100
    local log_size_mb=$(du -m "$LOG_FILE" 2>/dev/null | cut -f1 || echo 0)
    
    if [ "$log_size_mb" -gt "$max_size_mb" ]; then
        local backup_file="${LOG_FILE}.$(date +%Y%m%d_%H%M%S)"
        mv "$LOG_FILE" "$backup_file"
        log "INFO" "Logs rotés vers $backup_file"
    fi
}

# Fonction de nettoyage des anciens logs
cleanup_old_logs() {
    find logs/ -name "*.log.*" -mtime +7 -delete 2>/dev/null || true
    find logs/ -name "*.backup" -mtime +30 -delete 2>/dev/null || true
}

# Fonction principale de monitoring
main() {
    log "INFO" "Démarrage du monitoring pour $SERVICE_NAME"
    log "INFO" "URL du service: $SERVICE_URL"
    log "INFO" "Intervalle de vérification: ${CHECK_INTERVAL}s"
    
    # Création du dossier de logs si nécessaire
    mkdir -p "$(dirname "$LOG_FILE")"
    
    # Boucle principale de monitoring
    while true; do
        log "INFO" "=== Vérification de santé ==="
        
        # Vérification de la santé du service
        if check_health; then
            log "SUCCESS" "Service $SERVICE_NAME en bonne santé"
            
            # Récupération des métriques
            local metrics=$(get_metrics)
            if [[ "$metrics" != "{}" ]]; then
                log "INFO" "Métriques récupérées avec succès"
            fi
        else
            log "ERROR" "Service $SERVICE_NAME inaccessible après $MAX_RETRIES tentatives"
            send_email_alert "Alerte Service FIT" "Le service FIT n'est pas accessible"
            send_slack_alert "🚨 Service FIT inaccessible"
        fi
        
        # Vérification des bases de données
        if ! check_databases; then
            send_email_alert "Alerte Bases de Données" "Une ou plusieurs bases de données ne sont pas accessibles"
            send_slack_alert "🚨 Problème avec les bases de données"
        fi
        
        # Vérification de l'espace disque
        check_disk_space
        
        # Vérification de la mémoire
        check_memory
        
        # Rotation des logs si nécessaire
        rotate_logs
        
        # Nettoyage des anciens logs (une fois par jour)
        if [ "$(date +%H)" = "02" ] && [ "$(date +%M)" -lt 5 ]; then
            cleanup_old_logs
        fi
        
        log "INFO" "Prochaine vérification dans ${CHECK_INTERVAL} secondes"
        sleep "$CHECK_INTERVAL"
    done
}

# Fonction d'arrêt gracieux
cleanup() {
    log "INFO" "Arrêt du monitoring..."
    exit 0
}

# Gestion des signaux
trap cleanup SIGTERM SIGINT

# Vérification des prérequis
if ! command -v curl &> /dev/null; then
    echo "ERROR: curl n'est pas installé"
    exit 1
fi

if ! command -v mongosh &> /dev/null; then
    echo "ERROR: mongosh n'est pas installé"
    exit 1
fi

if ! command -v psql &> /dev/null; then
    echo "ERROR: psql n'est pas installé"
    exit 1
fi

if ! command -v redis-cli &> /dev/null; then
    echo "ERROR: redis-cli n'est pas installé"
    exit 1
fi

# Démarrage du monitoring
main "$@" 