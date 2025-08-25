#!/bin/bash

# Script final simple de nettoyage - Nettoie tout en une fois
# Usage: ./scripts/simple-cleanup-final.sh

set -e

echo "🧹 NETTOYAGE FINAL SIMPLE"
echo "=========================="

# Créer une sauvegarde
BACKUP_DIR="storage/backups/simple-final-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Sauvegarde créée: $BACKUP_DIR"

# Nettoyer les références aux contrôleurs supprimés
echo "🔍 Nettoyage des références aux contrôleurs supprimés..."

# 1. Nettoyer PerformanceApiController
echo "  🔍 Nettoyage de PerformanceApiController..."
if [ -f "routes/api.php" ]; then
    # Supprimer la ligne use
    sed -i.tmp '/use App\\Http\\Controllers\\Api\\V1\\PerformanceApiController/d' routes/api.php
    
    # Supprimer les routes
    sed -i.tmp '/PerformanceApiController::class/d' routes/api.php
    
    echo "    ✅ api.php nettoyé"
fi

# 2. Nettoyer AiIntelligenceController
echo "  🔍 Nettoyage de AiIntelligenceController..."
if [ -f "routes/api-v3.php" ]; then
    # Supprimer la ligne use
    sed -i.tmp '/use App\\Http\\Controllers\\Api\\V3\\AiIntelligenceController/d' routes/api-v3.php
    
    # Supprimer les références
    sed -i.tmp '/AiIntelligenceController::class/d' routes/api-v3.php
    
    echo "    ✅ api-v3.php nettoyé"
fi

# 3. Nettoyer PerformanceAnalyticsController
echo "  🔍 Nettoyage de PerformanceAnalyticsController..."
if [ -f "routes/api-v3.php" ]; then
    # Supprimer la ligne use
    sed -i.tmp '/use App\\Http\\Controllers\\Api\\V3\\PerformanceAnalyticsController/d' routes/api-v3.php
    
    # Supprimer les références
    sed -i.tmp '/PerformanceAnalyticsController::class/d' routes/api-v3.php
    
    echo "    ✅ PerformanceAnalyticsController nettoyé"
fi

# 4. Nettoyer LicenseHistoryController
echo "  🔍 Nettoyage de LicenseHistoryController..."
if [ -f "routes/web.php" ]; then
    # Supprimer les références
    sed -i.tmp '/LicenseHistoryController::class/d' routes/web.php
    
    echo "    ✅ web.php nettoyé"
fi

# Nettoyer les fichiers temporaires
rm -f routes/*.tmp 2>/dev/null || true

echo "✅ Nettoyage simple terminé !"

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

echo ""
echo "🎉 NETTOYAGE FINAL SIMPLE TERMINÉ !"
echo "==================================="
echo "📁 Fichiers générés:"
echo "  - Sauvegarde: $BACKUP_DIR"
echo ""
echo "📊 Statut:"
echo "  - Syntaxe PHP: $(if [ $syntax_errors -eq 0 ]; then echo "✅ OK"; else echo "❌ $syntax_errors erreur(s)"; fi)"
echo "  - Routes: $(if [ "$ROUTES_WORKING" = true ]; then echo "✅ OK"; else echo "❌ ERREUR"; fi)"
echo "  - Application: $(if [ "$APP_WORKING" = true ]; then echo "✅ OK"; else echo "❌ ERREUR"; fi)"
echo ""
if [ "$APP_WORKING" = true ]; then
    echo "🚀 L'application est prête pour le déploiement !"
else
    echo "⚠️  Des problèmes persistent. Vérifiez les logs et corrigez les erreurs."
fi
