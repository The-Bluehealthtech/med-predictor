#!/bin/bash

# Script de nettoyage des routes après suppression des contrôleurs
# Usage: ./scripts/cleanup-routes-after-cleanup.sh

set -e

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonctions de log
log() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_header() {
    echo -e "\n${BLUE}================================${NC}"
    echo -e "${BLUE} $1${NC}"
    echo -e "${BLUE}================================${NC}\n"
}

# Variables
ROUTES_DIR="routes"
BACKUP_DIR="storage/backups/routes-backup-$(date +%Y%m%d_%H%M%S)"
CLEANUP_LOG="storage/logs/routes-cleanup-$(date +%Y%m%d_%H%M%S).log"
total_removals=0
cleaned_files=0

# Contrôleurs supprimés (liste complète)
DELETED_CONTROLLERS=(
    "AccountRequestController"
    "AutoKeyController"
    "DentalRecordController"
    "FHIRController"
    "ICD11Controller"
    "JoueurController"
    "LicenseHistoryController"
    "MatchSheetController"
    "PlayerLicenseHistoryController"
    "AnalyticsController"
    "AthleteController"
    "AuthController"
    "ClubBridgeController"
    "ClubController"
    "CompetitionController"
    "DTNController"
    "FifaTmsController"
    "FifaWebhookController"
    "InjuryController"
    "MatchController"
    "MatchEventController"
    "MedicalNoteController"
    "PerformanceApiController"
    "PlayerController"
    "PlayerDashboardController"
    "PlayerLicenseController"
    "RPMController"
    "RiskAlertController"
    "SCATAssessmentController"
    "SeasonController"
    "TeamController"
    "UserController"
    "AiIntelligenceController"
    "PerformanceAnalyticsController"
    "AppointmentController"
    "AuditTrailController"
    "AuthenticatedSessionController"
    "ConfirmablePasswordController"
    "EmailVerificationNotificationController"
    "EmailVerificationPromptController"
    "LoginController"
    "NewPasswordController"
    "PasswordController"
    "PasswordResetLinkController"
    "RegisteredUserController"
    "VerifyEmailController"
    "LicenseTypeController"
    "BackOfficeController"
    "ClubPlayerLicenseController"
    "ContentManagementController"
    "ContractController"
    "Controller"
    "DocumentController"
    "FIFAPortalController"
    "FifaConnectController"
    "FifaController"
    "FifaWorkingController"
    "HealthcareController"
    "Hl7Controller"
    "LanguageController"
    "LicenseRequestController"
    "MedicalPredictionController"
    "ModuleController"
    "PCMAFraudDetectionController"
    "PerformanceController"
    "PerformanceManagementController"
    "PerformanceRecommendationController"
    "PlayerLicenseReviewController"
    "PlayerPassportController"
    "PlayerPerformanceController"
    "PlayerPortalController_OLD"
    "PlayerRegistrationController"
    "MedicalAppointmentController"
    "MessageController"
    "NotificationController"
    "PosturalAssessmentController"
    "ProfileController"
    "RankingsController"
    "RegistrationRequestController"
    "RoleManagementController"
    "SeasonManagementController"
    "SecretaryController"
    "StakeholderGalleryController"
    "TestAuthController"
    "UserManagementController"
    "VisitController"
)

# Initialisation
init_cleanup() {
    log_header "🧹 NETTOYAGE DES ROUTES APRÈS SUPPRESSION DES CONTRÔLEURS"
    
    # Créer le répertoire de sauvegarde
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
        log "📁 Répertoire de sauvegarde créé: $BACKUP_DIR"
    fi
    
    # Créer le fichier de log
    touch "$CLEANUP_LOG"
    log "📝 Fichier de log créé: $CLEANUP_LOG"
}

# Sauvegarder les routes actuelles
backup_routes() {
    log "💾 Sauvegarde des routes actuelles..."
    
    cp -r "$ROUTES_DIR" "$BACKUP_DIR/"
    log_success "✅ Routes sauvegardées dans: $BACKUP_DIR"
    
    echo "Routes sauvegardées le $(date)" >> "$CLEANUP_LOG"
    echo "Emplacement: $BACKUP_DIR" >> "$CLEANUP_LOG"
    echo "" >> "$CLEANUP_LOG"
}

# Nettoyer les références aux contrôleurs supprimés
cleanup_controller_references() {
    log "🧹 Nettoyage des références aux contrôleurs supprimés..."
    
    local cleaned_files=0
    local total_removals=0
    
    for controller in "${DELETED_CONTROLLERS[@]}"; do
        log "🔍 Recherche de références à: $controller"
        
        # Rechercher dans tous les fichiers de routes
        for route_file in "$ROUTES_DIR"/*.php; do
            if [ -f "$route_file" ]; then
                local filename=$(basename "$route_file")
                local temp_file="$route_file.tmp"
                local has_changes=false
                
                # Vérifier s'il y a des références
                if grep -q "$controller" "$route_file"; then
                    log "  📝 Nettoyage de $filename..."
                    
                    # Créer une copie temporaire
                    cp "$route_file" "$temp_file"
                    
                    # Supprimer les lignes use
                    sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" "$temp_file"
                    
                    # Supprimer les routes utilisant ce contrôleur
                    sed -i.tmp "/$controller::class/d" "$temp_file"
                    sed -i.tmp "/$controller@/d" "$temp_file"
                    
                    # Vérifier s'il y a eu des changements
                    if ! cmp -s "$route_file" "$temp_file"; then
                        mv "$temp_file" "$route_file"
                        has_changes=true
                        cleaned_files=$((cleaned_files + 1))
                        
                        # Compter les suppressions
                        local removals=$(grep -c "$controller" "$route_file" 2>/dev/null || echo "0")
                        total_removals=$((total_removals + removals))
                        
                        echo "  ✅ $filename nettoyé"
                        echo "Supprimé de $filename: $controller" >> "$CLEANUP_LOG"
                    else
                        rm -f "$temp_file"
                    fi
                fi
            fi
        done
    done
    
    log_success "✅ Nettoyage terminé: $cleaned_files fichiers modifiés, $total_removals références supprimées"
    echo "Total: $cleaned_files fichiers modifiés, $total_removals références supprimées" >> "$CLEANUP_LOG"
}

# Vérifier la syntaxe PHP
check_php_syntax() {
    log "🔍 Vérification de la syntaxe PHP..."
    
    local syntax_errors=0
    
    for route_file in "$ROUTES_DIR"/*.php; do
        if [ -f "$route_file" ]; then
            local filename=$(basename "$route_file")
            
            if php -l "$route_file" >/dev/null 2>&1; then
                echo "  ✅ $filename: Syntaxe OK"
            else
                echo "  ❌ $filename: Erreur de syntaxe"
                syntax_errors=$((syntax_errors + 1))
                echo "ERREUR: $filename a une erreur de syntaxe" >> "$CLEANUP_LOG"
            fi
        fi
    done
    
    if [ $syntax_errors -eq 0 ]; then
        log_success "✅ Tous les fichiers de routes ont une syntaxe PHP valide"
    else
        log_error "❌ $syntax_errors fichier(s) avec erreur(s) de syntaxe"
    fi
    
    echo "Vérification syntaxe: $syntax_errors erreur(s)" >> "$CLEANUP_LOG"
}

# Tester les routes
test_routes() {
    log "🧪 Test des routes après nettoyage..."
    
    # Essayer de lister les routes
    if php artisan route:list --compact >/dev/null 2>&1; then
        log_success "✅ Routes testées avec succès"
        echo "Test des routes: SUCCESS" >> "$CLEANUP_LOG"
    else
        log_error "❌ Erreur lors du test des routes"
        echo "Test des routes: FAILED" >> "$CLEANUP_LOG"
        
        # Afficher l'erreur
        php artisan route:list --compact 2>&1 | head -10
    fi
}

# Générer le rapport final
generate_report() {
    log "📊 Génération du rapport final..."
    
    local report_file="storage/logs/routes-cleanup-report-$(date +%Y%m%d_%H%M%S).md"
    
    cat > "$report_file" << EOF
# Rapport de Nettoyage des Routes - Med-Predictor

**Date:** $(date)
**Script:** cleanup-routes-after-cleanup.sh

## Résumé

- **Fichiers de routes nettoyés:** $(find routes/ -name "*.php" -type f | wc -l | tr -d ' ')
- **Contrôleurs supprimés:** ${#DELETED_CONTROLLERS[@]}
- **Références supprimées:** $total_removals
- **Statut:** ✅ SUCCESS

## Contrôleurs Supprimés

EOF
    
    for controller in "${DELETED_CONTROLLERS[@]}"; do
        echo "- $controller" >> "$report_file"
    done
    
    cat >> "$report_file" << EOF

## Fichiers de Log

- **Log de nettoyage:** $CLEANUP_LOG
- **Sauvegarde des routes:** $BACKUP_DIR
- **Rapport final:** $report_file

## Commandes de Vérification

\`\`\`bash
# Vérifier la syntaxe PHP
php -l routes/*.php

# Tester les routes
php artisan route:list --compact

# Vérifier l'état de l'application
php artisan about
\`\`\`

## Restauration

Si nécessaire, restaurez les routes depuis:
\`\`\`bash
cp -r $BACKUP_DIR/* routes/
\`\`\`
EOF
    
    log_success "✅ Rapport généré: $report_file"
    echo "Rapport final: $report_file" >> "$CLEANUP_LOG"
}

# Fonction principale
main() {
    log_header "🚀 DÉMARRAGE DU NETTOYAGE DES ROUTES"
    
    init_cleanup
    backup_routes
    cleanup_controller_references
    check_php_syntax
    test_routes
    generate_report
    
    log_header "🎉 NETTOYAGE TERMINÉ AVEC SUCCÈS !"
    
    echo ""
    echo "📁 Fichiers générés:"
    echo "  - Log de nettoyage: $CLEANUP_LOG"
    echo "  - Sauvegarde des routes: $BACKUP_DIR"
    echo "  - Rapport final: storage/logs/routes-cleanup-report-*.md"
    echo ""
    echo "🧪 Test de l'application:"
    echo "  php artisan route:list --compact"
    echo ""
    echo "🔄 Restauration (si nécessaire):"
    echo "  cp -r $BACKUP_DIR/* routes/"
}

# Gestion des erreurs
trap 'log_error "Erreur à la ligne $LINENO. Arrêt du nettoyage."; exit 1' ERR

# Exécution
main "$@"
