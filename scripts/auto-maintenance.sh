#!/bin/bash

# Script de maintenance automatique
# Auteur: Assistant IA
# Date: $(date)
# Usage: Peut être exécuté via cron pour la maintenance automatique

# Configuration
LOG_FILE="/tmp/auto-maintenance.log"
MAX_LOG_SIZE="10M"
MAX_SESSION_AGE="7"  # jours
MAX_CACHE_AGE="3"    # jours
MAX_BACKUP_AGE="30"  # jours

# Variables
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Fonction de logging
log() {
    echo "[$TIMESTAMP] $1" | tee -a "$LOG_FILE"
}

# Fonction pour vérifier l'espace disque
check_disk_space() {
    local available_space=$(df -h . | awk 'NR==2 {print $4}')
    local used_percent=$(df -h . | awk 'NR==2 {print $5}' | sed 's/%//')
    
    log "💾 Espace disque disponible: $available_space"
    log "📊 Utilisation: $used_percent%"
    
    if [ "$used_percent" -gt 90 ]; then
        log "⚠️  ATTENTION: Espace disque critique (>90%)"
        return 1
    elif [ "$used_percent" -gt 80 ]; then
        log "⚠️  ATTENTION: Espace disque faible (>80%)"
        return 2
    else
        log "✅ Espace disque suffisant"
        return 0
    fi
}

# Fonction pour nettoyer les logs
cleanup_logs() {
    log "🗑️  Nettoyage des logs..."
    
    # Nettoyer les logs volumineux
    if [ -d "$PROJECT_ROOT/logs" ]; then
        local cleaned_logs=$(find "$PROJECT_ROOT/logs" -name "*.log" -size +$MAX_LOG_SIZE -delete -print | wc -l)
        log "   ✅ $cleaned_logs logs volumineux supprimés dans logs/"
    fi
    
    if [ -d "$PROJECT_ROOT/storage/logs" ]; then
        local cleaned_storage_logs=$(find "$PROJECT_ROOT/storage/logs" -name "*.log" -size +$MAX_LOG_SIZE -delete -print | wc -l)
        log "   ✅ $cleaned_storage_logs logs volumineux supprimés dans storage/logs/"
    fi
    
    # Nettoyer les logs anciens (>30 jours)
    if [ -d "$PROJECT_ROOT/logs" ]; then
        local cleaned_old_logs=$(find "$PROJECT_ROOT/logs" -name "*.log" -mtime +30 -delete -print | wc -l)
        log "   ✅ $cleaned_old_logs logs anciens supprimés dans logs/"
    fi
    
    if [ -d "$PROJECT_ROOT/storage/logs" ]; then
        local cleaned_old_storage_logs=$(find "$PROJECT_ROOT/storage/logs" -name "*.log" -mtime +30 -delete -print | wc -l)
        log "   ✅ $cleaned_old_storage_logs logs anciens supprimés dans storage/logs/"
    fi
}

# Fonction pour nettoyer les caches
cleanup_caches() {
    log "🗑️  Nettoyage des caches..."
    
    # Cache Laravel
    if [ -d "$PROJECT_ROOT/bootstrap/cache" ]; then
        rm -rf "$PROJECT_ROOT/bootstrap/cache"/*
        log "   ✅ Cache bootstrap nettoyé"
    fi
    
    if [ -d "$PROJECT_ROOT/storage/framework/cache" ]; then
        rm -rf "$PROJECT_ROOT/storage/framework/cache"/*
        log "   ✅ Cache framework nettoyé"
    fi
    
    if [ -d "$PROJECT_ROOT/storage/framework/views" ]; then
        rm -rf "$PROJECT_ROOT/storage/framework/views"/*
        log "   ✅ Vues compilées nettoyées"
    fi
    
    # Cache PHP
    find "$PROJECT_ROOT" -name ".php-cs-fixer.cache" -delete 2>/dev/null
    find "$PROJECT_ROOT" -name ".phpunit.result.cache" -delete 2>/dev/null
    log "   ✅ Caches PHP supprimés"
    
    # Cache Node.js
    if [ -d "$PROJECT_ROOT/node_modules/.cache" ]; then
        rm -rf "$PROJECT_ROOT/node_modules/.cache"
        log "   ✅ Cache Node.js supprimé"
    fi
}

# Fonction pour nettoyer les sessions
cleanup_sessions() {
    log "🗑️  Nettoyage des sessions..."
    
    if [ -d "$PROJECT_ROOT/storage/framework/sessions" ]; then
        cd "$PROJECT_ROOT/storage/framework/sessions"
        
        # Supprimer les sessions anciennes
        local old_sessions=$(find . -type f -mtime +$MAX_SESSION_AGE -delete -print | wc -l)
        log "   ✅ $old_sessions sessions anciennes supprimées"
        
        # Garder seulement les 20 sessions les plus récentes
        local total_sessions=$(ls -1 | wc -l)
        if [ "$total_sessions" -gt 20 ]; then
            local removed_sessions=$(ls -t | tail -n +21 | xargs -r rm -f | wc -l)
            log "   ✅ $removed_sessions sessions supplémentaires supprimées (20 conservées)"
        fi
        
        cd "$PROJECT_ROOT"
    fi
}

# Fonction pour nettoyer les fichiers temporaires
cleanup_temp_files() {
    log "🗑️  Nettoyage des fichiers temporaires..."
    
    # Fichiers temporaires de développement
    local temp_files=$(find "$PROJECT_ROOT" -name "*.tmp" -o -name "*.temp" -o -name "*.swp" -o -name "*.swo" -o -name "*~" -delete -print | wc -l)
    log "   ✅ $temp_files fichiers temporaires supprimés"
    
    # Fichiers de sauvegarde épars
    local backup_files=$(find "$PROJECT_ROOT" -name "*.backup" -o -name "*.bak" -o -name "*.old" -o -name "*.orig" -delete -print | wc -l)
    log "   ✅ $backup_files fichiers de sauvegarde supprimés"
    
    # Fichiers système
    local system_files=$(find "$PROJECT_ROOT" -name ".DS_Store" -o -name "Thumbs.db" -o -name "._*" -delete -print | wc -l)
    log "   ✅ $system_files fichiers système supprimés"
}

# Fonction pour nettoyer les dossiers temporaires
cleanup_temp_dirs() {
    log "🗑️  Nettoyage des dossiers temporaires..."
    
    local temp_dirs=("$PROJECT_ROOT/tmp" "$PROJECT_ROOT/temp-restore" "$PROJECT_ROOT/coverage" "$PROJECT_ROOT/test-results")
    
    for temp_dir in "${temp_dirs[@]}"; do
        if [ -d "$temp_dir" ]; then
            local file_count=$(ls -1 "$temp_dir" | wc -l)
            if [ "$file_count" -gt 0 ]; then
                rm -rf "$temp_dir"/*
                log "   ✅ $temp_dir nettoyé ($file_count fichiers supprimés)"
            fi
        fi
    done
}

# Fonction pour nettoyer les sauvegardes anciennes
cleanup_old_backups() {
    log "🗑️  Nettoyage des sauvegardes anciennes..."
    
    # Sauvegardes dans storage/backups
    if [ -d "$PROJECT_ROOT/storage/backups" ]; then
        local old_backups=$(find "$PROJECT_ROOT/storage/backups" -type d -mtime +$MAX_BACKUP_AGE -delete -print | wc -l)
        log "   ✅ $old_backups sauvegardes anciennes supprimées dans storage/backups"
    fi
    
    # Fichiers de sauvegarde épars
    local old_backup_files=$(find "$PROJECT_ROOT" -name "*.backup" -mtime +$MAX_BACKUP_AGE -delete -print | wc -l)
    log "   ✅ $old_backup_files fichiers de sauvegarde anciens supprimés"
}

# Fonction pour nettoyer les composants temporaires
cleanup_components() {
    log "🗑️  Nettoyage des composants temporaires..."
    
    local component_dirs=(
        "$PROJECT_ROOT/resources/js/components"
        "$PROJECT_ROOT/app/Models"
        "$PROJECT_ROOT/app/Http/Controllers"
        "$PROJECT_ROOT/app/Services"
        "$PROJECT_ROOT/resources/views"
        "$PROJECT_ROOT/routes"
        "$PROJECT_ROOT/config"
        "$PROJECT_ROOT/database/migrations"
        "$PROJECT_ROOT/database/seeders"
        "$PROJECT_ROOT/database/factories"
        "$PROJECT_ROOT/app/Providers"
        "$PROJECT_ROOT/app/Policies"
        "$PROJECT_ROOT/app/Observers"
        "$PROJECT_ROOT/app/Notifications"
        "$PROJECT_ROOT/app/Jobs"
        "$PROJECT_ROOT/app/Events"
        "$PROJECT_ROOT/app/Listeners"
        "$PROJECT_ROOT/app/Exports"
        "$PROJECT_ROOT/app/Imports"
        "$PROJECT_ROOT/app/Helpers"
        "$PROJECT_ROOT/app/Scopes"
        "$PROJECT_ROOT/app/Exceptions"
        "$PROJECT_ROOT/app/Console/Commands"
        "$PROJECT_ROOT/bootstrap"
        "$PROJECT_ROOT/public"
        "$PROJECT_ROOT/resources"
        "$PROJECT_ROOT/scripts"
        "$PROJECT_ROOT/src"
        "$PROJECT_ROOT/deploy"
        "$PROJECT_ROOT/k8s"
        "$PROJECT_ROOT/terraform"
        "$PROJECT_ROOT/docker"
        "$PROJECT_ROOT/docs"
        "$PROJECT_ROOT/tests"
        "$PROJECT_ROOT/storage"
    )
    
    local total_cleaned=0
    
    for dir in "${component_dirs[@]}"; do
        if [ -d "$dir" ]; then
            local cleaned=$(find "$dir" -name "*.backup" -o -name "*.bak" -delete -print | wc -l)
            if [ "$cleaned" -gt 0 ]; then
                total_cleaned=$((total_cleaned + cleaned))
            fi
        fi
    done
    
    log "   ✅ $total_cleaned composants temporaires supprimés"
}

# Fonction pour nettoyer les dossiers vides
cleanup_empty_dirs() {
    log "🗑️  Nettoyage des dossiers vides..."
    
    local empty_dirs=$(find "$PROJECT_ROOT" -type d -empty -delete -print | wc -l)
    log "   ✅ $empty_dirs dossiers vides supprimés"
}

# Fonction pour nettoyer les fichiers de déploiement
cleanup_deployment_files() {
    log "🗑️  Nettoyage des fichiers de déploiement..."
    
    # Fichiers de déploiement temporaires
    if [ -d "$PROJECT_ROOT/deploy-manual/public" ]; then
        local deploy_files=$(ls -1 "$PROJECT_ROOT/deploy-manual/public" | wc -l)
        if [ "$deploy_files" -gt 0 ]; then
            rm -rf "$PROJECT_ROOT/deploy-manual/public"/*
            log "   ✅ $deploy_files fichiers de déploiement temporaires supprimés"
        fi
    fi
    
    # Logs de déploiement
    local deploy_logs=$(find "$PROJECT_ROOT" -name "deploy-*.log" -o -name "deployment-*.log" -delete -print | wc -l)
    log "   ✅ $deploy_logs logs de déploiement supprimés"
}

# Fonction pour nettoyer les bases de données temporaires
cleanup_temp_databases() {
    log "🗑️  Nettoyage des bases de données temporaires..."
    
    local temp_dbs=$(find "$PROJECT_ROOT" -name "*.sqlite" -not -path "*/database/*" -o -name "*.db" -not -path "*/database/*" -delete -print | wc -l)
    log "   ✅ $temp_dbs bases de données temporaires supprimées"
}

# Fonction pour nettoyer les configurations temporaires
cleanup_temp_configs() {
    log "🗑️  Nettoyage des configurations temporaires..."
    
    local temp_configs=$(find "$PROJECT_ROOT" -name "*.env.*" -not -name ".env.example" -delete -print | wc -l)
    log "   ✅ $temp_configs configurations temporaires supprimées"
}

# Fonction pour nettoyer la documentation temporaire
cleanup_temp_docs() {
    log "🗑️  Nettoyage de la documentation temporaire..."
    
    local temp_docs=$(find "$PROJECT_ROOT" -name "*.md.bak" -delete -print | wc -l)
    log "   ✅ $temp_docs fichiers de documentation temporaires supprimés"
}

# Fonction pour nettoyer les scripts temporaires
cleanup_temp_scripts() {
    log "🗑️  Nettoyage des scripts temporaires..."
    
    local temp_scripts=$(find "$PROJECT_ROOT/scripts" -name "*.tmp" -o -name "*.bak" -o -name "*.old" -delete -print | wc -l)
    log "   ✅ $temp_scripts scripts temporaires supprimés"
}

# Fonction pour nettoyer les fichiers de build
cleanup_build_files() {
    log "🗑️  Nettoyage des fichiers de build..."
    
    if [ -d "$PROJECT_ROOT/public/build" ]; then
        local source_maps=$(find "$PROJECT_ROOT/public/build" -name "*.map" -delete -print | wc -l)
        log "   ✅ $source_maps source maps supprimés"
    fi
}

# Fonction principale
main() {
    log "🚀 Début de la maintenance automatique..."
    log "📁 Projet: $PROJECT_ROOT"
    
    # Vérifier l'espace disque
    local disk_status=$(check_disk_space)
    
    # Nettoyage de base (toujours effectué)
    cleanup_logs
    cleanup_caches
    cleanup_sessions
    cleanup_temp_files
    cleanup_temp_dirs
    cleanup_components
    cleanup_empty_dirs
    cleanup_deployment_files
    cleanup_temp_databases
    cleanup_temp_configs
    cleanup_temp_docs
    cleanup_temp_scripts
    cleanup_build_files
    
    # Nettoyage avancé si l'espace disque est faible
    if [ "$disk_status" -eq 1 ] || [ "$disk_status" -eq 2 ]; then
        log "⚠️  Espace disque faible, nettoyage avancé activé..."
        cleanup_old_backups
    fi
    
    # Vérifier l'espace disque après nettoyage
    log "🔍 Vérification de l'espace disque après nettoyage..."
    check_disk_space
    
    log "✅ Maintenance automatique terminée avec succès!"
    
    # Nettoyer le fichier de log s'il devient trop volumineux
    if [ -f "$LOG_FILE" ] && [ "$(stat -f%z "$LOG_FILE" 2>/dev/null || stat -c%s "$LOG_FILE" 2>/dev/null || echo "0")" -gt 10485760 ]; then
        echo "" > "$LOG_FILE"
        log "🧹 Fichier de log nettoyé (trop volumineux)"
    fi
}

# Exécution du script
main "$@"

























