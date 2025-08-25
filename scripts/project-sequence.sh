#!/bin/bash

# Script de séquence complète pour l'audit et le nettoyage du projet
# Usage: ./scripts/project-sequence.sh [--dry-run] [--skip-backup]

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Variables
DRY_RUN=false
SKIP_BACKUP=false
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
LOG_FILE="storage/logs/project-sequence-${TIMESTAMP}.log"

# Fonctions de log
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}✅${NC} $1" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}⚠️${NC} $1" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}❌${NC} $1" | tee -a "$LOG_FILE"
}

log_step() {
    echo -e "${PURPLE}🔹${NC} $1" | tee -a "$LOG_FILE"
}

log_header() {
    echo -e "${CYAN}📋${NC} $1" | tee -a "$LOG_FILE"
}

# Vérification des arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --skip-backup)
            SKIP_BACKUP=true
            shift
            ;;
        *)
            echo "Usage: $0 [--dry-run] [--skip-backup]"
            exit 1
            ;;
    esac
    shift
done

# Initialisation
init_sequence() {
    log_header "🚀 DÉMARRAGE DE LA SÉQUENCE COMPLÈTE DU PROJET"
    log "Timestamp: $TIMESTAMP"
    log "Mode DRY-RUN: $DRY_RUN"
    log "Sauvegarde ignorée: $SKIP_BACKUP"
    log "Log: $LOG_FILE"
    
    # Créer le répertoire de logs si nécessaire
    mkdir -p storage/logs
    
    # Vérifier que Laravel est disponible
    if ! command -v php artisan &> /dev/null; then
        log_error "Laravel Artisan non trouvé. Assurez-vous d'être dans le répertoire du projet."
        exit 1
    fi
}

# Étape 1: Audit de base de données
step1_db_audit() {
    log_header "ÉTAPE 1: AUDIT DE BASE DE DONNÉES"
    log_step "Exécution de project:db:audit..."
    
    if php artisan project:db:audit; then
        log_success "Audit de base de données terminé"
    else
        log_error "Échec de l'audit de base de données"
        return 1
    fi
}

# Étape 2: Audit du projet
step2_project_audit() {
    log_header "ÉTAPE 2: AUDIT DU PROJET"
    log_step "Exécution de project:audit..."
    
    if php artisan project:audit --format=json; then
        log_success "Audit du projet terminé"
    else
        log_error "Échec de l'audit du projet"
        return 1
    fi
}

# Étape 3: Sauvegarde initiale
step3_initial_backup() {
    if [ "$SKIP_BACKUP" = true ]; then
        log_warning "Sauvegarde initiale ignorée (--skip-backup)"
        return 0
    fi
    
    log_header "ÉTAPE 3: SAUVEGARDE INITIALE"
    log_step "Création de la sauvegarde complète..."
    
    if php artisan project:backup --type=full --compress; then
        log_success "Sauvegarde initiale terminée"
    else
        log_error "Échec de la sauvegarde initiale"
        return 1
    fi
}

# Étape 4: Phase de debug ciblée
step4_debug_phase() {
    log_header "ÉTAPE 4: PHASE DE DEBUG CIBLÉE"
    log_step "Vérification des erreurs critiques..."
    
    # Vérifier les erreurs dans les logs
    if [ -f "storage/logs/laravel.log" ]; then
        error_count=$(grep -c "ERROR\|FATAL\|CRITICAL" storage/logs/laravel.log || echo "0")
        if [ "$error_count" -gt 0 ]; then
            log_warning "Détecté $error_count erreurs dans les logs Laravel"
            log_step "Analyse des erreurs récentes..."
            tail -20 storage/logs/laravel.log | grep -E "ERROR|FATAL|CRITICAL" | head -5
        else
            log_success "Aucune erreur critique détectée"
        fi
    fi
    
    # Vérifier la syntaxe PHP
    log_step "Vérification de la syntaxe PHP..."
    php_files=$(find app resources -name "*.php" -type f | wc -l)
    log "Vérification de $php_files fichiers PHP..."
    
    syntax_errors=0
    while IFS= read -r -d '' file; do
        if ! php -l "$file" > /dev/null 2>&1; then
            log_error "Erreur de syntaxe dans: $file"
            ((syntax_errors++))
        fi
    done < <(find app resources -name "*.php" -type f -print0)
    
    if [ "$syntax_errors" -eq 0 ]; then
        log_success "Aucune erreur de syntaxe PHP détectée"
    else
        log_warning "Détecté $syntax_errors erreur(s) de syntaxe PHP"
    fi
}

# Étape 5: Nettoyage progressif
step5_progressive_cleanup() {
    log_header "ÉTAPE 5: NETTOYAGE PROGRESSIF"
    log_step "Analyse des éléments orphelins..."
    
    if [ "$DRY_RUN" = true ]; then
        log "Mode DRY-RUN: Aucune suppression ne sera effectuée"
        if php artisan project:routes:clean --dry-run; then
            log_success "Analyse de nettoyage terminée (DRY-RUN)"
        else
            log_error "Échec de l'analyse de nettoyage"
            return 1
        fi
    else
        log "Mode EXÉCUTION: Suppression des éléments orphelins"
        if php artisan project:routes:clean --force; then
            log_success "Nettoyage terminé"
        else
            log_error "Échec du nettoyage"
            return 1
        fi
    fi
}

# Étape 6: Sauvegarde finale
step6_final_backup() {
    if [ "$SKIP_BACKUP" = true ]; then
        log_warning "Sauvegarde finale ignorée (--skip-backup)"
        return 0
    fi
    
    log_header "ÉTAPE 6: SAUVEGARDE FINALE"
    log_step "Création de la sauvegarde finale..."
    
    if php artisan project:backup --type=full --compress; then
        log_success "Sauvegarde finale terminée"
    else
        log_error "Échec de la sauvegarde finale"
        return 1
    fi
}

# Étape 7: Génération du rapport final
step7_final_report() {
    log_header "ÉTAPE 7: RAPPORT FINAL"
    log_step "Génération du rapport de séquence..."
    
    # Créer le rapport final
    report_file="storage/logs/sequence-report-${TIMESTAMP}.md"
    
    cat > "$report_file" << EOF
# Rapport de Séquence Projet - Med-Predictor

**Date:** $(date '+%Y-%m-%d %H:%M:%S')
**Mode:** $([ "$DRY_RUN" = true ] && echo "DRY-RUN" || echo "EXÉCUTION")
**Sauvegarde:** $([ "$SKIP_BACKUP" = true ] && echo "Ignorée" || echo "Effectuée")

## Résumé des Étapes

### ✅ Étape 1: Audit de Base de Données
- Commande: \`php artisan project:db:audit\`
- Statut: Terminé
- Fichier: \`storage/logs/db-audit-*.log\`

### ✅ Étape 2: Audit du Projet
- Commande: \`php artisan project:audit --format=json\`
- Statut: Terminé
- Fichier: \`storage/logs/project-audit-*.json\`

### ✅ Étape 3: Sauvegarde Initiale
- Commande: \`php artisan project:backup --type=full --compress\`
- Statut: $([ "$SKIP_BACKUP" = true ] && echo "Ignorée" || echo "Terminée")
- Fichier: \`storage/backups/backup-*.zip\`

### ✅ Étape 4: Phase de Debug Ciblée
- Vérification des erreurs critiques
- Vérification de la syntaxe PHP
- Statut: Terminé

### ✅ Étape 5: Nettoyage Progressif
- Commande: \`php artisan project:routes:clean\`
- Mode: $([ "$DRY_RUN" = true ] && echo "DRY-RUN" || echo "EXÉCUTION")
- Statut: Terminé

### ✅ Étape 6: Sauvegarde Finale
- Commande: \`php artisan project:backup --type=full --compress\`
- Statut: $([ "$SKIP_BACKUP" = true ] && echo "Ignorée" || echo "Terminée")
- Fichier: \`storage/backups/backup-*.zip\`

## Fichiers Générés

- **Log de séquence:** \`$LOG_FILE\`
- **Rapport final:** \`$report_file\`
- **Audits:** \`storage/logs/\`
- **Sauvegardes:** \`storage/backups/\`

## Prochaines Actions Recommandées

1. **Vérifier les rapports d'audit** générés
2. **Analyser les éléments orphelins** identifiés
3. **Corriger les erreurs critiques** détectées
4. **Tester l'application** après le nettoyage
5. **Déployer en staging** si tout est OK
6. **Tag de release** après validation

## Commandes Utiles

\`\`\`bash
# Vérifier l'état actuel
php artisan project:db:audit
php artisan project:audit

# Nettoyer (mode dry-run d'abord)
php artisan project:routes:clean --dry-run
php artisan project:routes:clean --force

# Créer une sauvegarde
php artisan project:backup --type=full --compress

# Relancer la séquence complète
./scripts/project-sequence.sh
\`\`\`
EOF

    log_success "Rapport final généré: $report_file"
}

# Fonction principale
main() {
    log "🚀 Démarrage de la séquence complète..."
    
    # Initialisation
    init_sequence
    
    # Exécution des étapes
    local step=1
    local total_steps=7
    
    # Étape 1: Audit DB
    log_header "PROGRESSION: $step/$total_steps"
    if step1_db_audit; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 2: Audit Projet
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step2_project_audit; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 3: Sauvegarde Initiale
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step3_initial_backup; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 4: Debug Ciblé
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step4_debug_phase; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 5: Nettoyage Progressif
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step5_progressive_cleanup; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 6: Sauvegarde Finale
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step6_final_backup; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Étape 7: Rapport Final
    ((step++))
    log_header "PROGRESSION: $step/$total_steps"
    if step7_final_report; then
        log_success "Étape $step/$total_steps terminée"
    else
        log_error "Étape $step/$total_steps échouée"
        exit 1
    fi
    
    # Finalisation
    log_header "🎉 SÉQUENCE COMPLÈTE TERMINÉE AVEC SUCCÈS !"
    log "📁 Consultez les rapports dans: storage/logs/"
    log "💾 Sauvegardes dans: storage/backups/"
    log "📊 Log complet: $LOG_FILE"
    
    if [ "$DRY_RUN" = true ]; then
        log_warning "Mode DRY-RUN: Aucune modification n'a été effectuée"
        log "Relancez sans --dry-run pour effectuer les vraies modifications"
    fi
}

# Gestion des erreurs
trap 'log_error "Erreur à la ligne $LINENO. Arrêt de la séquence."; exit 1' ERR

# Exécution
main "$@"
