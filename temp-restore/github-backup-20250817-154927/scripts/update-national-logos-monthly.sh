#!/bin/bash

# ⚽ Script de mise à jour automatique mensuelle des logos des ligues nationales pour FIT
# 📅 Créé le : $(date)
# 🎯 Objectif : Automatiser la synchronisation mensuelle des logos officiels des ligues nationales

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
LOG_FILE="$PROJECT_ROOT/logs/national-logos-update.log"
LOCK_FILE="$PROJECT_ROOT/tmp/national-logos-update.lock"
NODE_SCRIPT="$SCRIPT_DIR/update-national-logos.js"

# Créer les répertoires nécessaires
mkdir -p "$PROJECT_ROOT/logs"
mkdir -p "$PROJECT_ROOT/tmp"

# Fonction de logging
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Vérifier si une mise à jour est déjà en cours
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE" 2>/dev/null)
    if ps -p "$PID" > /dev/null 2>&1; then
        log "⚠️  Mise à jour déjà en cours (PID: $PID), arrêt"
        exit 1
    else
        log "🔓 Suppression du fichier de verrouillage obsolète"
        rm -f "$LOCK_FILE"
    fi
fi

# Créer le fichier de verrouillage
echo $$ > "$LOCK_FILE"

# Fonction de nettoyage
cleanup() {
    log "🧹 Nettoyage en cours..."
    rm -f "$LOCK_FILE"
    log "✅ Nettoyage terminé"
}

# Capturer les signaux d'arrêt
trap cleanup EXIT INT TERM

# Vérifier que Node.js est disponible
if ! command -v node &> /dev/null; then
    log "❌ ERREUR : Node.js n'est pas installé ou n'est pas dans le PATH"
    exit 1
fi

# Vérifier que le script Node.js existe
if [ ! -f "$NODE_SCRIPT" ]; then
    log "❌ ERREUR : Script Node.js introuvable : $NODE_SCRIPT"
    exit 1
fi

# Vérifier que le répertoire de sortie existe
OUTPUT_DIR="$PROJECT_ROOT/public/associations"
if [ ! -d "$OUTPUT_DIR" ]; then
    log "📁 Création du répertoire de sortie : $OUTPUT_DIR"
    mkdir -p "$OUTPUT_DIR"
fi

# Début de la mise à jour
log "🚀 DÉBUT DE LA MISE À JOUR AUTOMATIQUE DES LOGOS DES LIGUES NATIONALES"
log "📁 Répertoire de travail : $PROJECT_ROOT"
log "📁 Répertoire de sortie : $OUTPUT_DIR"
log "🔧 Script Node.js : $NODE_SCRIPT"

# Vérifier l'espace disque disponible (minimum 100MB)
AVAILABLE_SPACE=$(df "$OUTPUT_DIR" | awk 'NR==2 {print $4}')
if [ "$AVAILABLE_SPACE" -lt 102400 ]; then
    log "⚠️  ATTENTION : Espace disque insuffisant (${AVAILABLE_SPACE}KB disponible, 100MB requis)"
    log "⏭️  Mise à jour reportée"
    exit 1
fi

log "💾 Espace disque disponible : ${AVAILABLE_SPACE}KB"

# Lancer le script Node.js
log "⚡ Lancement du script de mise à jour..."
cd "$PROJECT_ROOT"

if node "$NODE_SCRIPT" 2>&1 | tee -a "$LOG_FILE"; then
    log "✅ MISE À JOUR TERMINÉE AVEC SUCCÈS"
    
    # Compter les fichiers téléchargés
    TOTAL_LOGOS=$(find "$OUTPUT_DIR" -name "*.png" -o -name "*.jpg" -o -name "*.jpeg" -o -name "*.svg" | wc -l)
    log "📊 Total des logos disponibles : $TOTAL_LOGOS"
    
    # Vérifier la taille du répertoire
    DIRECTORY_SIZE=$(du -sh "$OUTPUT_DIR" | cut -f1)
    log "📁 Taille du répertoire des logos : $DIRECTORY_SIZE"
    
    # Créer un rapport de succès
    SUCCESS_REPORT="$PROJECT_ROOT/logs/national-logos-success-$(date +%Y%m%d).log"
    {
        echo "=== RAPPORT DE SUCCÈS - MISE À JOUR DES LOGOS DES LIGUES NATIONALES ==="
        echo "Date : $(date)"
        echo "Statut : SUCCÈS"
        echo "Logos disponibles : $TOTAL_LOGOS"
        echo "Taille du répertoire : $DIRECTORY_SIZE"
        echo "Dernière mise à jour : $(date)"
        echo "================================================================"
    } > "$SUCCESS_REPORT"
    
    log "📋 Rapport de succès créé : $SUCCESS_REPORT"
    
else
    log "❌ ERREUR : La mise à jour a échoué"
    
    # Créer un rapport d'erreur
    ERROR_REPORT="$PROJECT_ROOT/logs/national-logos-error-$(date +%Y%m%d).log"
    {
        echo "=== RAPPORT D'ERREUR - MISE À JOUR DES LOGOS DES LIGUES NATIONALES ==="
        echo "Date : $(date)"
        echo "Statut : ÉCHEC"
        echo "Dernière tentative : $(date)"
        echo "Consultez le log principal : $LOG_FILE"
        echo "================================================================"
    } > "$ERROR_REPORT"
    
    log "📋 Rapport d'erreur créé : $ERROR_REPORT"
    exit 1
fi

# Nettoyage des anciens logs (garder seulement les 30 derniers jours)
log "🧹 Nettoyage des anciens logs..."
find "$PROJECT_ROOT/logs" -name "national-logos-*.log" -mtime +30 -delete 2>/dev/null || true

# Nettoyage des anciens rapports (garder seulement les 90 derniers jours)
log "🧹 Nettoyage des anciens rapports..."
find "$PROJECT_ROOT/logs" -name "national-logos-*-*.log" -mtime +90 -delete 2>/dev/null || true

log "🎉 MISE À JOUR AUTOMATIQUE TERMINÉE AVEC SUCCÈS !"
log "📅 Prochaine mise à jour automatique : $(date -d '+1 month' '+%Y-%m-%d')"

exit 0

