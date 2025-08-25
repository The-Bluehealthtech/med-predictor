#!/bin/bash

# Script intelligent de nettoyage des routes
# Usage: ./scripts/smart-routes-cleanup.sh

echo "🧹 Nettoyage intelligent des références aux contrôleurs supprimés..."

# Créer une sauvegarde
BACKUP_DIR="storage/backups/routes-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r routes/ "$BACKUP_DIR/"
echo "✅ Routes sauvegardées dans: $BACKUP_DIR"

# Fonction pour nettoyer un fichier de routes
clean_route_file() {
    local file="$1"
    local controller="$2"
    local temp_file="$file.tmp"
    
    echo "  📝 Nettoyage de $(basename "$file") pour $controller..."
    
    # Créer une copie temporaire
    cp "$file" "$temp_file"
    
    # Supprimer les lignes use problématiques
    sed -i.tmp "/use App\\\\Http\\\\Controllers.*$controller/d" "$temp_file" 2>/dev/null || true
    
    # Supprimer les routes utilisant ce contrôleur (plus prudent)
    sed -i.tmp "/Route::.*$controller::class/d" "$temp_file" 2>/dev/null || true
    sed -i.tmp "/Route::.*$controller@/d" "$temp_file" 2>/dev/null || true
    
    # Vérifier la syntaxe PHP
    if php -l "$temp_file" >/dev/null 2>&1; then
        mv "$temp_file" "$file"
        echo "    ✅ $(basename "$file") nettoyé avec succès"
        return 0
    else
        echo "    ❌ $(basename "$file") a une erreur de syntaxe, annulation"
        rm -f "$temp_file"
        return 1
    fi
}

# Contrôleurs critiques à nettoyer en premier
CRITICAL_CONTROLLERS=(
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
)

# Nettoyer les contrôleurs critiques d'abord
echo "🔍 Nettoyage des contrôleurs critiques..."
for controller in "${CRITICAL_CONTROLLERS[@]}"; do
    echo "🔍 Nettoyage de $controller..."
    
    for route_file in routes/*.php; do
        if [ -f "$route_file" ]; then
            if grep -q "$controller" "$route_file"; then
                clean_route_file "$route_file" "$controller"
            fi
        fi
    done
done

# Nettoyer les fichiers temporaires
rm -f routes/*.tmp 2>/dev/null || true

echo "✅ Nettoyage des contrôleurs critiques terminé !"

# Vérifier la syntaxe de tous les fichiers
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
    if php artisan route:list --compact >/dev/null 2>&1; then
        echo "✅ Routes fonctionnelles !"
    else
        echo "❌ Erreur dans les routes"
        php artisan route:list --compact 2>&1 | head -5
    fi
else
    echo "❌ Impossible de tester les routes à cause des erreurs de syntaxe"
fi

echo ""
echo "📁 Fichiers générés:"
echo "  - Sauvegarde: $BACKUP_DIR"
echo "  - Routes nettoyées: routes/"
echo "  - Erreurs de syntaxe: $syntax_errors"
echo ""
echo "🔄 Restauration (si nécessaire):"
echo "  cp -r $BACKUP_DIR/* routes/"
