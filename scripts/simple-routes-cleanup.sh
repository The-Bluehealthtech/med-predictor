#!/bin/bash

# Script simple de nettoyage des routes après suppression des contrôleurs
# Usage: ./scripts/simple-routes-cleanup.sh

echo "🧹 Nettoyage des références aux contrôleurs supprimés..."

# Créer une sauvegarde des routes
BACKUP_DIR="storage/backups/routes-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Routes sauvegardées dans: $BACKUP_DIR"

# Liste des contrôleurs supprimés
CONTROLLERS=(
    "EmailVerificationPromptController"
    "AuthenticatedSessionController"
    "ConfirmablePasswordController"
    "EmailVerificationNotificationController"
    "NewPasswordController"
    "PasswordController"
    "PasswordResetLinkController"
    "RegisteredUserController"
    "VerifyEmailController"
    "LoginController"
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

# Nettoyer chaque contrôleur
for controller in "${CONTROLLERS[@]}"; do
    echo "🔍 Nettoyage de $controller..."
    
    # Supprimer les lignes use
    sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" routes/*.php 2>/dev/null || true
    
    # Supprimer les routes utilisant ce contrôleur
    sed -i.tmp "/$controller::class/d" routes/*.php 2>/dev/null || true
    sed -i.tmp "/$controller@/d" routes/*.php 2>/dev/null || true
    
    # Nettoyer les fichiers temporaires
    rm -f routes/*.tmp 2>/dev/null || true
done

echo "✅ Nettoyage terminé !"

# Vérifier la syntaxe PHP
echo "🔍 Vérification de la syntaxe PHP..."
for file in routes/*.php; do
    if [ -f "$file" ]; then
        if php -l "$file" >/dev/null 2>&1; then
            echo "  ✅ $(basename "$file"): OK"
        else
            echo "  ❌ $(basename "$file"): Erreur"
        fi
    fi
done

# Tester les routes
echo "🧪 Test des routes..."
if php artisan route:list --compact >/dev/null 2>&1; then
    echo "✅ Routes fonctionnelles !"
else
    echo "❌ Erreur dans les routes"
    php artisan route:list --compact 2>&1 | head -5
fi

echo ""
echo "📁 Fichiers générés:"
echo "  - Sauvegarde: $BACKUP_DIR"
echo "  - Routes nettoyées: routes/"
echo ""
echo "🔄 Restauration (si nécessaire):"
echo "  cp -r $BACKUP_DIR/* routes/"
