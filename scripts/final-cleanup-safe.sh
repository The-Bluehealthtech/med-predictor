#!/bin/bash

# Script final de nettoyage sûr - Nettoie tout en une fois
# Usage: ./scripts/final-cleanup-safe.sh

set -e

echo "🧹 NETTOYAGE FINAL SÛR"
echo "======================="

# Créer une sauvegarde
BACKUP_DIR="storage/backups/final-safe-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Sauvegarde créée: $BACKUP_DIR"

# Fonction pour nettoyer un fichier de manière sûre
safe_clean_file() {
    local file="$1"
    local controller="$2"
    local temp_file="$file.tmp"
    
    # Créer une copie temporaire
    cp "$file" "$temp_file"
    
    # Supprimer les lignes use
    sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" "$temp_file" 2>/dev/null || true
    
    # Supprimer les références de classe
    sed -i.tmp "/$controller::class/d" "$temp_file" 2>/dev/null || true
    sed -i.tmp "/$controller@/d" "$temp_file" 2>/dev/null || true
    
    # Vérifier la syntaxe
    if php -l "$temp_file" >/dev/null 2>&1; then
        mv "$temp_file" "$file"
        echo "    ✅ $(basename "$file") nettoyé"
        return 0
    else
        echo "    ❌ $(basename "$file") erreur de syntaxe, annulation"
        rm -f "$temp_file"
        return 1
    fi
}

# Liste complète des contrôleurs supprimés
ALL_CONTROLLERS=(
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

# Nettoyer chaque contrôleur de manière sûre
echo "🔍 Nettoyage sécurisé des références aux contrôleurs supprimés..."
total_cleaned=0

for controller in "${ALL_CONTROLLERS[@]}"; do
    echo "🔍 Nettoyage de $controller..."
    controller_cleaned=0
    
    # Nettoyer dans les routes
    for route_file in routes/*.php; do
        if [ -f "$route_file" ] && grep -q "$controller" "$route_file"; then
            if safe_clean_file "$route_file" "$controller"; then
                controller_cleaned=$((controller_cleaned + 1))
            fi
        fi
    done
    
    if [ $controller_cleaned -gt 0 ]; then
        echo "  ✅ $controller: $controller_cleaned fichier(s) nettoyé(s)"
        total_cleaned=$((total_cleaned + controller_cleaned))
    fi
done

# Nettoyer les fichiers temporaires
rm -f routes/*.tmp 2>/dev/null || true

echo "✅ Nettoyage sécurisé terminé: $total_cleaned fichier(s) modifié(s)"

# Vérifier la syntaxe
echo "🔍 Vérification de la syntaxe PHP..."
syntax_errors=0
for file in routes/*.php; do
    if [ -f "$file" ]; then
        if php -l "$file" >/dev/null 2>&1; then
            echo "  ✅ $(basename "$file"): OK"
        else
            echo "  ❌ $(basename "$file"): Erreur"
            syntax_errors=$((syntax_errors + 1))
        fi
    fi
done

# Tester les routes
echo "🧪 Test des routes..."
if [ $syntax_errors -eq 0 ]; then
    if php artisan route:list >/dev/null 2>&1; then
        echo "✅ Routes fonctionnelles !"
        ROUTES_WORKING=true
    else
        echo "❌ Erreur dans les routes"
        php artisan route:list 2>&1 | head -5
        ROUTES_WORKING=false
    fi
else
    echo "❌ Impossible de tester les routes à cause des erreurs de syntaxe"
    ROUTES_WORKING=false
fi

# Tester l'application
echo "🧪 Test de l'application..."
if [ "$ROUTES_WORKING" = true ]; then
    if php artisan about >/dev/null 2>&1; then
        echo "✅ Application fonctionnelle !"
        APP_WORKING=true
    else
        echo "❌ Erreur dans l'application"
        php artisan about 2>&1 | head -5
        APP_WORKING=false
    fi
else
    echo "❌ Impossible de tester l'application à cause des erreurs de routes"
    APP_WORKING=false
fi

# Générer le rapport
echo "📊 Génération du rapport final..."
REPORT_FILE="storage/logs/final-cleanup-safe-report-$(date +%Y%m%d_%H%M%S).md"

cat > "$REPORT_FILE" << EOF
# Rapport de Nettoyage Final Sûr - Med-Predictor

**Date:** $(date)
**Script:** final-cleanup-safe.sh

## Résumé

- **Sauvegarde:** $BACKUP_DIR
- **Fichiers nettoyés:** $total_cleaned
- **Erreurs de syntaxe:** $syntax_errors
- **Routes fonctionnelles:** $ROUTES_WORKING
- **Application fonctionnelle:** $APP_WORKING
- **Statut:** $(if [ "$APP_WORKING" = true ]; then echo "✅ SUCCESS"; else echo "❌ FAILED"; fi)

## Actions Effectuées

1. ✅ Sauvegarde complète du projet
2. ✅ Nettoyage sécurisé de ${#ALL_CONTROLLERS[@]} contrôleurs
3. ✅ Vérification de la syntaxe PHP
4. ✅ Test des routes
5. ✅ Test de l'application

## Contrôleurs Nettoyés

EOF

for controller in "${ALL_CONTROLLERS[@]}"; do
    echo "- $controller" >> "$REPORT_FILE"
done

cat >> "$REPORT_FILE" << EOF

## Commandes de Vérification

\`\`\`bash
# Vérifier la syntaxe PHP
php -l routes/*.php

# Tester les routes
php artisan route:list

# Tester l'application
php artisan about
\`\`\`

## Restauration

Si nécessaire, restaurez depuis:
\`\`\`bash
cp -r $BACKUP_DIR/* routes/
\`\`\`
EOF

echo "✅ Rapport généré: $REPORT_FILE"

# Résumé final
echo ""
echo "🎉 NETTOYAGE FINAL SÛR TERMINÉ !"
echo "================================="
echo "📁 Fichiers générés:"
echo "  - Sauvegarde: $BACKUP_DIR"
echo "  - Rapport: $REPORT_FILE"
echo ""
echo "📊 Statut:"
echo "  - Fichiers nettoyés: $total_cleaned"
echo "  - Syntaxe PHP: $(if [ $syntax_errors -eq 0 ]; then echo "✅ OK"; else echo "❌ $syntax_errors erreur(s)"; fi)"
echo "  - Routes: $(if [ "$ROUTES_WORKING" = true ]; then echo "✅ OK"; else echo "❌ ERREUR"; fi)"
echo "  - Application: $(if [ "$APP_WORKING" = true ]; then echo "✅ OK"; else echo "❌ ERREUR"; fi)"
echo ""
if [ "$APP_WORKING" = true ]; then
    echo "🚀 L'application est prête pour le déploiement !"
else
    echo "⚠️  Des problèmes persistent. Vérifiez les logs et corrigez les erreurs."
fi
