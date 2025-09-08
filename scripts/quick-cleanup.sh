#!/bin/bash

# Script de nettoyage rapide quotidien
# Auteur: Assistant IA
# Date: $(date)

echo "🧹 Nettoyage rapide quotidien..."
echo "================================="

# Variables
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Fonction pour afficher l'espace disque
show_disk_usage() {
    echo "💾 Espace disque disponible:"
    df -h . | grep -E "^/dev|^Filesystem"
    echo ""
}

# Afficher l'état initial
show_disk_usage

echo "🚀 Début du nettoyage rapide..."

# 1. Nettoyer les caches Laravel
echo "🗑️  Nettoyage des caches Laravel..."
if [ -d "$PROJECT_ROOT/bootstrap/cache" ]; then
    rm -rf "$PROJECT_ROOT/bootstrap/cache"/*
    echo "   ✅ Cache bootstrap nettoyé"
fi

if [ -d "$PROJECT_ROOT/storage/framework/cache" ]; then
    rm -rf "$PROJECT_ROOT/storage/framework/cache"/*
    echo "   ✅ Cache framework nettoyé"
fi

if [ -d "$PROJECT_ROOT/storage/framework/views" ]; then
    rm -rf "$PROJECT_ROOT/storage/framework/views"/*
    echo "   ✅ Vues compilées nettoyées"
fi

# 2. Nettoyer les sessions (garder les 10 plus récentes)
echo "🗑️  Nettoyage des sessions..."
if [ -d "$PROJECT_ROOT/storage/framework/sessions" ]; then
    cd "$PROJECT_ROOT/storage/framework/sessions"
    # Garder seulement les 10 sessions les plus récentes
    ls -t | tail -n +11 | xargs -r rm -f
    echo "   ✅ Sessions anciennes supprimées (10 plus récentes conservées)"
    cd "$PROJECT_ROOT"
fi

# 3. Nettoyer les logs volumineux (>10MB)
echo "🗑️  Nettoyage des logs volumineux..."
if [ -d "$PROJECT_ROOT/logs" ]; then
    find "$PROJECT_ROOT/logs" -name "*.log" -size +10M -exec rm -f {} \;
    echo "   ✅ Logs volumineux (>10MB) supprimés"
fi

if [ -d "$PROJECT_ROOT/storage/logs" ]; then
    find "$PROJECT_ROOT/storage/logs" -name "*.log" -size +10M -exec rm -f {} \;
    echo "   ✅ Logs storage volumineux (>10MB) supprimés"
fi

# 4. Nettoyer les fichiers temporaires de développement
echo "🗑️  Nettoyage des fichiers temporaires..."
find "$PROJECT_ROOT" -name "*.tmp" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "*.temp" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "*.swp" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "*.swo" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "*~" -delete 2>/dev/null
echo "   ✅ Fichiers temporaires supprimés"

# 5. Nettoyer les caches PHP
echo "🗑️  Nettoyage des caches PHP..."
find "$PROJECT_ROOT" -name ".php-cs-fixer.cache" -delete 2>/dev/null
find "$PROJECT_ROOT" -name ".phpunit.result.cache" -delete 2>/dev/null
echo "   ✅ Caches PHP supprimés"

# 6. Nettoyer les fichiers de test temporaires
echo "🗑️  Nettoyage des tests temporaires..."
if [ -d "$PROJECT_ROOT/test-results" ]; then
    rm -rf "$PROJECT_ROOT/test-results"/*
    echo "   ✅ Résultats de tests supprimés"
fi

if [ -d "$PROJECT_ROOT/coverage" ]; then
    rm -rf "$PROJECT_ROOT/coverage"/*
    echo "   ✅ Fichiers de couverture supprimés"
fi

# 7. Nettoyer les dossiers temporaires
echo "🗑️  Nettoyage des dossiers temporaires..."
for temp_dir in "$PROJECT_ROOT/tmp" "$PROJECT_ROOT/temp-restore"; do
    if [ -d "$temp_dir" ]; then
        rm -rf "$temp_dir"/*
        echo "   ✅ $temp_dir nettoyé"
    fi
done

# 8. Nettoyer les fichiers système
echo "🗑️  Nettoyage des fichiers système..."
find "$PROJECT_ROOT" -name ".DS_Store" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "Thumbs.db" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "._*" -delete 2>/dev/null
echo "   ✅ Fichiers système supprimés"

# 9. Nettoyer les caches de composants
echo "🗑️  Nettoyage des caches de composants..."
if [ -d "$PROJECT_ROOT/node_modules/.cache" ]; then
    rm -rf "$PROJECT_ROOT/node_modules/.cache"
    echo "   ✅ Cache Node.js supprimé"
fi

# 10. Nettoyer les fichiers de build temporaires
echo "🗑️  Nettoyage des fichiers de build..."
if [ -d "$PROJECT_ROOT/public/build" ]; then
    find "$PROJECT_ROOT/public/build" -name "*.map" -delete 2>/dev/null
    echo "   ✅ Source maps supprimés"
fi

# 11. Nettoyer les fichiers de déploiement temporaires
echo "🗑️  Nettoyage des déploiements temporaires..."
if [ -d "$PROJECT_ROOT/deploy-manual/public" ]; then
    rm -rf "$PROJECT_ROOT/deploy-manual/public"/*
    echo "   ✅ Fichiers de déploiement temporaires supprimés"
fi

# 12. Nettoyer les fichiers de logs de déploiement
echo "🗑️  Nettoyage des logs de déploiement..."
find "$PROJECT_ROOT" -name "deploy-*.log" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "deployment-*.log" -delete 2>/dev/null
echo "   ✅ Logs de déploiement supprimés"

# 13. Nettoyer les fichiers de base de données temporaires
echo "🗑️  Nettoyage des bases de données temporaires..."
find "$PROJECT_ROOT" -name "*.sqlite" -not -path "*/database/*" -delete 2>/dev/null
find "$PROJECT_ROOT" -name "*.db" -not -path "*/database/*" -delete 2>/dev/null
echo "   ✅ Bases de données temporaires supprimées"

# 14. Nettoyer les fichiers de configuration temporaires
echo "🗑️  Nettoyage des configurations temporaires..."
find "$PROJECT_ROOT" -name "*.env.*" -not -name ".env.example" -delete 2>/dev/null
echo "   ✅ Configurations temporaires supprimées"

# 15. Nettoyer les fichiers de documentation temporaires
echo "🗑️  Nettoyage de la documentation temporaire..."
find "$PROJECT_ROOT" -name "*.md.bak" -delete 2>/dev/null
echo "   ✅ Documentation temporaire supprimée"

# 16. Nettoyer les fichiers de scripts temporaires
echo "🗑️  Nettoyage des scripts temporaires..."
find "$PROJECT_ROOT/scripts" -name "*.tmp" -delete 2>/dev/null
find "$PROJECT_ROOT/scripts" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Scripts temporaires supprimés"

# 17. Nettoyer les composants temporaires
echo "🗑️  Nettoyage des composants temporaires..."
find "$PROJECT_ROOT/resources/js/components" -name "*.bak" -delete 2>/dev/null
find "$PROJECT_ROOT/resources/js/components" -name "*.backup" -delete 2>/dev/null
echo "   ✅ Composants temporaires supprimés"

# 18. Nettoyer les routes temporaires
echo "🗑️  Nettoyage des routes temporaires..."
find "$PROJECT_ROOT/routes" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/routes" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Routes temporaires supprimées"

# 19. Nettoyer les modèles temporaires
echo "🗑️  Nettoyage des modèles temporaires..."
find "$PROJECT_ROOT/app/Models" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Models" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Modèles temporaires supprimés"

# 20. Nettoyer les contrôleurs temporaires
echo "🗑️  Nettoyage des contrôleurs temporaires..."
find "$PROJECT_ROOT/app/Http/Controllers" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Http/Controllers" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Contrôleurs temporaires supprimés"

# 21. Nettoyer les services temporaires
echo "🗑️  Nettoyage des services temporaires..."
find "$PROJECT_ROOT/app/Services" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Services" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Services temporaires supprimés"

# 22. Nettoyer les vues temporaires
echo "🗑️  Nettoyage des vues temporaires..."
find "$PROJECT_ROOT/resources/views" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/resources/views" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Vues temporaires supprimées"

# 23. Nettoyer les tests temporaires
echo "🗑️  Nettoyage des tests temporaires..."
find "$PROJECT_ROOT/tests" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/tests" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Tests temporaires supprimés"

# 24. Nettoyer les configurations temporaires
echo "🗑️  Nettoyage des configurations temporaires..."
find "$PROJECT_ROOT/config" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/config" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Configurations temporaires supprimées"

# 25. Nettoyer les migrations temporaires
echo "🗑️  Nettoyage des migrations temporaires..."
find "$PROJECT_ROOT/database/migrations" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/database/migrations" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Migrations temporaires supprimées"

# 26. Nettoyer les seeders temporaires
echo "🗑️  Nettoyage des seeders temporaires..."
find "$PROJECT_ROOT/database/seeders" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/database/seeders" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Seeders temporaires supprimés"

# 27. Nettoyer les factories temporaires
echo "🗑️  Nettoyage des factories temporaires..."
find "$PROJECT_ROOT/database/factories" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/database/factories" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Factories temporaires supprimées"

# 28. Nettoyer les providers temporaires
echo "🗑️  Nettoyage des providers temporaires..."
find "$PROJECT_ROOT/app/Providers" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Providers" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Providers temporaires supprimés"

# 29. Nettoyer les policies temporaires
echo "🗑️  Nettoyage des policies temporaires..."
find "$PROJECT_ROOT/app/Policies" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Policies" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Policies temporaires supprimées"

# 30. Nettoyer les observers temporaires
echo "🗑️  Nettoyage des observers temporaires..."
find "$PROJECT_ROOT/app/Observers" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Observers" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Observers temporaires supprimés"

# 31. Nettoyer les notifications temporaires
echo "🗑️  Nettoyage des notifications temporaires..."
find "$PROJECT_ROOT/app/Notifications" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Notifications" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Notifications temporaires supprimées"

# 32. Nettoyer les jobs temporaires
echo "🗑️  Nettoyage des jobs temporaires..."
find "$PROJECT_ROOT/app/Jobs" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Jobs" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Jobs temporaires supprimés"

# 33. Nettoyer les events temporaires
echo "🗑️  Nettoyage des events temporaires..."
find "$PROJECT_ROOT/app/Events" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Events" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Events temporaires supprimés"

# 34. Nettoyer les listeners temporaires
echo "🗑️  Nettoyage des listeners temporaires..."
find "$PROJECT_ROOT/app/Listeners" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Listeners" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Listeners temporaires supprimés"

# 35. Nettoyer les exports temporaires
echo "🗑️  Nettoyage des exports temporaires..."
find "$PROJECT_ROOT/app/Exports" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Exports" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Exports temporaires supprimés"

# 36. Nettoyer les imports temporaires
echo "🗑️  Nettoyage des imports temporaires..."
find "$PROJECT_ROOT/app/Imports" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Imports" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Imports temporaires supprimés"

# 37. Nettoyer les helpers temporaires
echo "🗑️  Nettoyage des helpers temporaires..."
find "$PROJECT_ROOT/app/Helpers" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Helpers" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Helpers temporaires supprimés"

# 38. Nettoyer les scopes temporaires
echo "🗑️  Nettoyage des scopes temporaires..."
find "$PROJECT_ROOT/app/Scopes" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Scopes" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Scopes temporaires supprimés"

# 39. Nettoyer les exceptions temporaires
echo "🗑️  Nettoyage des exceptions temporaires..."
find "$PROJECT_ROOT/app/Exceptions" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Exceptions" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Exceptions temporaires supprimées"

# 40. Nettoyer les commandes console temporaires
echo "🗑️  Nettoyage des commandes console temporaires..."
find "$PROJECT_ROOT/app/Console/Commands" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Console/Commands" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Commandes console temporaires supprimées"

# 41. Nettoyer les kernels temporaires
echo "🗑️  Nettoyage des kernels temporaires..."
find "$PROJECT_ROOT/app/Console" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/app/Console" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Kernels temporaires supprimés"

# 42. Nettoyer les fichiers bootstrap temporaires
echo "🗑️  Nettoyage des fichiers bootstrap temporaires..."
find "$PROJECT_ROOT/bootstrap" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/bootstrap" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Fichiers bootstrap temporaires supprimés"

# 43. Nettoyer les fichiers public temporaires
echo "🗑️  Nettoyage des fichiers public temporaires..."
find "$PROJECT_ROOT/public" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/public" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Fichiers public temporaires supprimés"

# 44. Nettoyer les fichiers resources temporaires
echo "🗑️  Nettoyage des fichiers resources temporaires..."
find "$PROJECT_ROOT/resources" -name "*.backup" -delete 2>/dev/null
find "$PROJECT_ROOT/resources" -name "*.bak" -delete 2>/dev/null
echo "   ✅ Fichiers resources temporaires supprimés"

# 45. Nettoyage final et optimisation
echo "🗑️  Nettoyage final et optimisation..."

# Supprimer les dossiers vides
find "$PROJECT_ROOT" -type d -empty -delete 2>/dev/null

echo "   ✅ Nettoyage final terminé"

# Afficher l'état final
echo ""
echo "🎉 Nettoyage rapide terminé avec succès!"
echo "=========================================="

show_disk_usage

echo ""
echo "📋 Résumé des actions effectuées:"
echo "   ✅ Caches Laravel nettoyés"
echo "   ✅ Sessions anciennes supprimées"
echo "   ✅ Logs volumineux supprimés"
echo "   ✅ Fichiers temporaires supprimés"
echo "   ✅ Caches PHP supprimés"
echo "   ✅ Tests temporaires supprimés"
echo "   ✅ Dossiers temporaires nettoyés"
echo "   ✅ Fichiers système supprimés"
echo "   ✅ Composants temporaires supprimés"
echo "   ✅ Dossiers vides supprimés"

echo ""
echo "💡 Conseils pour maintenir un disque propre:"
echo "   - Exécutez ce script quotidiennement"
echo "   - Surveillez l'espace disque avec 'df -h'"
echo "   - Utilisez 'du -sh *' pour identifier les gros dossiers"
echo "   - Configurez la rotation automatique des logs"
echo "   - Nettoyez les caches après les mises à jour"

echo ""
echo "🔍 Espace libéré estimé: $(du -sh "$PROJECT_ROOT" 2>/dev/null | cut -f1)"
echo "🎯 Nettoyage rapide terminé avec succès!"

























