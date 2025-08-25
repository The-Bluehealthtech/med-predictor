#!/bin/bash

# Script simple de nettoyage final
# Usage: ./scripts/simple-final-cleanup.sh

echo "🧹 NETTOYAGE SIMPLE ET FINAL"
echo "============================"

# Créer une sauvegarde
BACKUP_DIR="storage/backups/simple-final-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Sauvegarde créée: $BACKUP_DIR"

# Nettoyer seulement les références problématiques
echo "🔍 Nettoyage des références problématiques..."

# Nettoyer PerformanceApiController dans api.php
if [ -f "routes/api.php" ]; then
    echo "  📝 Nettoyage de api.php..."
    
    # Supprimer la ligne use
    sed -i.tmp '/use App\\Http\\Controllers\\Api\\V1\\PerformanceApiController/d' routes/api.php
    
    # Supprimer les routes individuelles
    sed -i.tmp '/Route::get.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::post.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::put.*PerformanceApiController/d' routes/api.php
    sed -i.tmp '/Route::delete.*PerformanceApiController/d' routes/api.php
    
    # Supprimer l'apiResource
    sed -i.tmp '/Route::apiResource.*PerformanceApiController/d' routes/api.php
    
    echo "    ✅ api.php nettoyé"
fi

# Nettoyer les autres contrôleurs problématiques
PROBLEMATIC_CONTROLLERS=(
    "LicenseHistoryController"
    "AiIntelligenceController"
    "PerformanceAnalyticsController"
)

for controller in "${PROBLEMATIC_CONTROLLERS[@]}"; do
    echo "  🔍 Nettoyage de $controller..."
    
    # Nettoyer dans tous les fichiers de routes
    for route_file in routes/*.php; do
        if [ -f "$route_file" ]; then
            # Supprimer les lignes use
            sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" "$route_file" 2>/dev/null || true
            
            # Supprimer les références de classe
            sed -i.tmp "/$controller::class/d" "$route_file" 2>/dev/null || true
            sed -i.tmp "/$controller@/d" "$route_file" 2>/dev/null || true
        fi
    done
    
    echo "    ✅ $controller nettoyé"
done

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
echo "🎉 NETTOYAGE SIMPLE ET FINAL TERMINÉ !"
echo "======================================"
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
