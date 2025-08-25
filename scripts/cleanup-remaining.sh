#!/bin/bash

# Script de nettoyage des dernières références
# Usage: ./scripts/cleanup-remaining.sh

echo "🧹 NETTOYAGE DES DERNIÈRES RÉFÉRENCES"
echo "======================================"

# Créer une sauvegarde
BACKUP_DIR="storage/backups/remaining-cleanup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Sauvegarde créée: $BACKUP_DIR"

# Contrôleurs restants à nettoyer
REMAINING_CONTROLLERS=(
    "PerformanceApiController"
    "LicenseHistoryController"
    "AiIntelligenceController"
    "PerformanceAnalyticsController"
)

# Nettoyer chaque contrôleur restant
for controller in "${REMAINING_CONTROLLERS[@]}"; do
    echo "🔍 Nettoyage de $controller..."
    
    # Nettoyer dans api.php
    if [ -f "routes/api.php" ]; then
        # Supprimer les lignes use
        sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" routes/api.php 2>/dev/null || true
        
        # Supprimer les routes utilisant ce contrôleur
        sed -i.tmp "/$controller::class/d" routes/api.php 2>/dev/null || true
        sed -i.tmp "/$controller@/d" routes/api.php 2>/dev/null || true
        
        # Supprimer les blocs de routes complets
        sed -i.tmp "/Route::.*$controller/,/});/d" routes/api.php 2>/dev/null || true
        
        echo "  ✅ api.php nettoyé pour $controller"
    fi
    
    # Nettoyer dans web.php
    if [ -f "routes/web.php" ]; then
        sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" routes/web.php 2>/dev/null || true
        sed -i.tmp "/$controller::class/d" routes/web.php 2>/dev/null || true
        sed -i.tmp "/$controller@/d" routes/web.php 2>/dev/null || true
        echo "  ✅ web.php nettoyé pour $controller"
    fi
    
    # Nettoyer dans api-v3.php
    if [ -f "routes/api-v3.php" ]; then
        sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" routes/api-v3.php 2>/dev/null || true
        sed -i.tmp "/$controller::class/d" routes/api-v3.php 2>/dev/null || true
        sed -i.tmp "/$controller@/d" routes/api-v3.php 2>/dev/null || true
        echo "  ✅ api-v3.php nettoyé pour $controller"
    fi
done

# Nettoyer les fichiers temporaires
rm -f routes/*.tmp 2>/dev/null || true

echo "✅ Nettoyage des références restantes terminé !"

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
echo "🎉 NETTOYAGE DES RÉFÉRENCES RESTANTES TERMINÉ !"
echo "================================================"
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
