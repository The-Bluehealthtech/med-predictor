#!/bin/bash

# Script d'analyse de l'espace disque
# Auteur: Assistant IA
# Date: $(date)

echo "🔍 Analyse de l'espace disque..."
echo "================================="

# Variables
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Fonction pour afficher l'espace disque
show_disk_usage() {
    echo "💾 Espace disque disponible:"
    df -h . | grep -E "^/dev|^Filesystem"
    echo ""
}

# Fonction pour afficher la taille d'un dossier
get_dir_size() {
    local dir="$1"
    if [ -d "$dir" ]; then
        du -sh "$dir" 2>/dev/null | cut -f1
    else
        echo "0B"
    fi
}

# Afficher l'état initial
show_disk_usage

echo "📊 Analyse des dossiers principaux:"
echo "===================================="

# Analyser les dossiers principaux
declare -A main_dirs=(
    ["app"]="Application Laravel"
    ["resources"]="Ressources (vues, JS, CSS)"
    ["public"]="Fichiers publics"
    ["storage"]="Stockage et cache"
    ["database"]="Base de données"
    ["tests"]="Tests"
    ["docs"]="Documentation"
    ["scripts"]="Scripts"
    ["backups"]="Sauvegardes"
    ["deploy"]="Déploiement"
    ["k8s"]="Kubernetes"
    ["terraform"]="Terraform"
    ["docker"]="Docker"
    ["src"]="Code source"
    ["logs"]="Logs"
    ["tmp"]="Temporaire"
    ["coverage"]="Couverture de code"
    ["test-results"]="Résultats de tests"
)

echo "📁 Dossiers principaux:"
echo "-----------------------"
for dir in "${!main_dirs[@]}"; do
    if [ -d "$PROJECT_ROOT/$dir" ]; then
        size=$(get_dir_size "$PROJECT_ROOT/$dir")
        echo "   $dir: $size - ${main_dirs[$dir]}"
    fi
done

echo ""
echo "🔍 Analyse des gros dossiers (>100MB):"
echo "======================================"

# Trouver les dossiers de plus de 100MB
echo "📁 Dossiers > 100MB:"
echo "-------------------"
find "$PROJECT_ROOT" -type d -exec du -sh {} + 2>/dev/null | sort -hr | head -20 | while read size path; do
    if [[ "$size" =~ ^[0-9]+\.?[0-9]*[MG] ]]; then
        echo "   $size - $path"
    fi
done

echo ""
echo "📁 Dossiers > 50MB:"
echo "-------------------"
find "$PROJECT_ROOT" -type d -exec du -sh {} + 2>/dev/null | sort -hr | head -30 | while read size path; do
    if [[ "$size" =~ ^[0-9]+\.?[0-9]*[MG] ]]; then
        echo "   $size - $path"
    fi
done

echo ""
echo "📁 Dossiers > 10MB:"
echo "-------------------"
find "$PROJECT_ROOT" -type d -exec du -sh {} + 2>/dev/null | sort -hr | head -50 | while read size path; do
    if [[ "$size" =~ ^[0-9]+\.?[0-9]*[MG] ]]; then
        echo "   $size - $path"
    fi
done

echo ""
echo "📄 Fichiers volumineux (>10MB):"
echo "================================"

# Trouver les fichiers de plus de 10MB
find "$PROJECT_ROOT" -type f -size +10M -exec ls -lh {} + 2>/dev/null | sort -k5 -hr | head -20 | while read -r line; do
    if [[ "$line" =~ [0-9]+[MG] ]]; then
        echo "   $line"
    fi
done

echo ""
echo "📄 Fichiers volumineux (>1MB):"
echo "==============================="

# Trouver les fichiers de plus de 1MB
find "$PROJECT_ROOT" -type f -size +1M -exec ls -lh {} + 2>/dev/null | sort -k5 -hr | head -50 | while read -r line; do
    if [[ "$line" =~ [0-9]+[MG] ]]; then
        echo "   $line"
    fi
done

echo ""
echo "🗂️  Analyse des types de fichiers:"
echo "=================================="

# Analyser les types de fichiers les plus volumineux
echo "📊 Répartition par extension:"
echo "-----------------------------"

# Compter et mesurer les fichiers par extension
declare -A ext_sizes
declare -A ext_counts

find "$PROJECT_ROOT" -type f -name "*.*" 2>/dev/null | while read -r file; do
    ext="${file##*.}"
    if [[ "$ext" =~ ^[a-zA-Z0-9]+$ ]] && [[ ${#ext} -le 10 ]]; then
        size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo "0")
        if [[ "$size" =~ ^[0-9]+$ ]]; then
            ext_sizes["$ext"]=$((ext_sizes["$ext"] + size))
            ext_counts["$ext"]=$((ext_counts["$ext"] + 1))
        fi
    fi
done

# Afficher les résultats (cette partie peut ne pas fonctionner parfaitement à cause de la subshell)
echo "   Note: L'analyse des extensions peut être limitée par la structure du script"

echo ""
echo "📁 Analyse des dossiers de sauvegarde:"
echo "======================================"

# Analyser spécifiquement les dossiers de sauvegarde
backup_dirs=("backups" "storage/backups" "final-cleanup-20250827_091649")

for backup_dir in "${backup_dirs[@]}"; do
    if [ -d "$PROJECT_ROOT/$backup_dir" ]; then
        echo "📁 $backup_dir:"
        echo "   Taille totale: $(get_dir_size "$PROJECT_ROOT/$backup_dir")"
        echo "   Contenu:"
        if [ -d "$PROJECT_ROOT/$backup_dir" ]; then
            ls -la "$PROJECT_ROOT/$backup_dir" | head -10
            if [ "$(ls -1 "$PROJECT_ROOT/$backup_dir" | wc -l)" -gt 10 ]; then
                echo "   ... et $(($(ls -1 "$PROJECT_ROOT/$backup_dir" | wc -l) - 10)) autres éléments"
            fi
        fi
        echo ""
    fi
done

echo ""
echo "🗑️  Analyse des dossiers temporaires:"
echo "====================================="

# Analyser les dossiers temporaires
temp_dirs=("tmp" "temp-restore" "coverage" "test-results" "storage/framework/cache" "storage/framework/views" "bootstrap/cache")

for temp_dir in "${temp_dirs[@]}"; do
    if [ -d "$PROJECT_ROOT/$temp_dir" ]; then
        echo "📁 $temp_dir:"
        echo "   Taille: $(get_dir_size "$PROJECT_ROOT/$temp_dir")"
        echo "   Éléments: $(ls -1 "$PROJECT_ROOT/$temp_dir" | wc -l)"
        echo ""
    fi
done

echo ""
echo "📊 Résumé de l'analyse:"
echo "======================="

# Calculer la taille totale du projet
total_size=$(du -sh "$PROJECT_ROOT" 2>/dev/null | cut -f1)
echo "   📁 Taille totale du projet: $total_size"

# Compter les fichiers et dossiers
file_count=$(find "$PROJECT_ROOT" -type f 2>/dev/null | wc -l)
dir_count=$(find "$PROJECT_ROOT" -type d 2>/dev/null | wc -l)

echo "   📄 Nombre total de fichiers: $file_count"
echo "   📁 Nombre total de dossiers: $dir_count"

# Identifier les plus gros consommateurs d'espace
echo ""
echo "🎯 Recommandations de nettoyage:"
echo "================================"

echo "   🗑️  Dossiers prioritaires à nettoyer:"
echo "      - backups/ (sauvegardes anciennes)"
echo "      - storage/backups/ (sauvegardes de stockage)"
echo "      - final-cleanup-20250827_091649/ (nettoyage final)"
echo "      - storage/framework/cache/ (cache Laravel)"
echo "      - storage/framework/views/ (vues compilées)"
echo "      - bootstrap/cache/ (cache bootstrap)"
echo "      - coverage/ (couverture de code)"
echo "      - test-results/ (résultats de tests)"

echo ""
echo "   💡 Actions recommandées:"
echo "      - Exécuter le script de nettoyage complet: ./scripts/cleanup-disk-space.sh"
echo "      - Exécuter le script de nettoyage rapide: ./scripts/quick-cleanup.sh"
echo "      - Configurer la rotation automatique des logs"
echo "      - Nettoyer les caches après les mises à jour"
echo "      - Surveiller régulièrement l'espace disque"

echo ""
echo "🔍 Analyse terminée!"
echo "==================="






















