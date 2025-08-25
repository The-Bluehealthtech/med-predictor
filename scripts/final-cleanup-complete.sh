#!/bin/bash

# Script de nettoyage final complet - Nettoie tout en une fois
# Usage: ./scripts/final-cleanup-complete.sh

set -e

echo "🧹 NETTOYAGE FINAL COMPLET"
echo "==========================="

# Créer une sauvegarde
BACKUP_DIR="storage/backups/final-complete-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Sauvegarde créée: $BACKUP_DIR"

# Nettoyer api.php de manière agressive
echo "🔍 Nettoyage agressif de api.php..."
if [ -f "routes/api.php" ]; then
    # Supprimer toutes les références aux contrôleurs supprimés
    sed -i.tmp '/use App\\Http\\Controllers.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/PerformanceApiController::class/d' routes/api.php
    sed -i.tmp '/PerformanceApiController@/d' routes/api.php
    
    # Supprimer les blocs de routes complets
    sed -i.tmp '/Route::.*PerformanceApiController/,/});/d' routes/api.php
    
    # Supprimer les routes individuelles
    sed -i.tmp '/Route::.*analytics.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*export.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*bulk-import.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*dashboard.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*compare.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*trends.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::.*generate-alerts.*PerformanceApiController/d' routes/api.php
    
    # Supprimer les apiResource
    sed -i.tmp '/Route::apiResource.*PerformanceApiController/d' routes/api.php
    
    echo "  ✅ api.php nettoyé agressivement"
fi

# Nettoyer web.php de manière agressive
echo "🔍 Nettoyage agressif de web.php..."
if [ -f "routes/web.php" ]; then
    # Supprimer toutes les références aux contrôleurs supprimés
    sed -i.tmp '/use App\\Http\\Controllers.*PerformanceApiController/d' routes/web.php
    sed -i.tmp '/PerformanceApiController::class/d' routes/web.php
    sed -i.tmp '/PerformanceApiController@/d' routes/web.php
    
    echo "  ✅ web.php nettoyé agressivement"
fi

# Nettoyer api-v3.php de manière agressive
echo "🔍 Nettoyage agressif de api-v3.php..."
if [ -f "routes/api-v3.php" ]; then
    # Supprimer toutes les références aux contrôleurs supprimés
    sed -i.tmp '/use App\\Http\\Controllers.*PerformanceApiController/d' routes/api-v3.php
    sed -i.tmp '/PerformanceApiController::class/d' routes/api-v3.php
    sed -i.tmp '/PerformanceApiController@/d' routes/api-v3.php
    
    echo "  ✅ api-v3.php nettoyé agressivement"
fi

# Nettoyer les fichiers temporaires
rm -f routes/*.tmp 2>/dev/null || true

echo "✅ Nettoyage agressif terminé !"

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
echo "🎉 NETTOYAGE FINAL COMPLET TERMINÉ !"
echo "===================================="
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
