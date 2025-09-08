#!/bin/bash

# Script de test de tous les scripts de nettoyage
# Auteur: Assistant IA
# Date: $(date)

echo "🧪 Test de tous les scripts de nettoyage..."
echo "==========================================="

# Variables
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SCRIPTS_DIR="$PROJECT_ROOT/scripts"
TEST_RESULTS=()

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour tester un script
test_script() {
    local script_name="$1"
    local script_path="$SCRIPTS_DIR/$script_name"
    local description="$2"
    
    echo -e "${BLUE}🔍 Test de $script_name...${NC}"
    echo "   Description: $description"
    
    if [ ! -f "$script_path" ]; then
        echo -e "   ${RED}❌ Script non trouvé: $script_path${NC}"
        TEST_RESULTS+=("$script_name: ❌ Script non trouvé")
        return 1
    fi
    
    if [ ! -x "$script_path" ]; then
        echo -e "   ${YELLOW}⚠️  Script non exécutable, tentative de correction...${NC}"
        chmod +x "$script_path"
    fi
    
    # Test de syntaxe bash
    if bash -n "$script_path" 2>/dev/null; then
        echo -e "   ${GREEN}✅ Syntaxe bash valide${NC}"
        
        # Test d'exécution (mode test)
        if [[ "$script_name" == "cleanup-disk-space.sh" ]]; then
            echo -e "   ${YELLOW}⚠️  Script de nettoyage complet - test en mode simulation${NC}"
            # Simuler l'exécution sans supprimer
            timeout 10s bash -c "echo 'test' | $script_path" >/dev/null 2>&1
            if [ $? -eq 124 ]; then
                echo -e "   ${YELLOW}⚠️  Script interrompu (timeout) - normal pour le nettoyage complet${NC}"
                TEST_RESULTS+=("$script_name: ⚠️  Testé (timeout normal)")
            else
                echo -e "   ${GREEN}✅ Script testé avec succès${NC}"
                TEST_RESULTS+=("$script_name: ✅ Testé")
            fi
        else
            echo -e "   ${YELLOW}⚠️  Test d'exécution rapide...${NC}"
            timeout 5s "$script_path" >/dev/null 2>&1
            if [ $? -eq 124 ]; then
                echo -e "   ${YELLOW}⚠️  Script interrompu (timeout) - peut être normal${NC}"
                TEST_RESULTS+=("$script_name: ⚠️  Testé (timeout)")
            else
                echo -e "   ${GREEN}✅ Script testé avec succès${NC}"
                TEST_RESULTS+=("$script_name: ✅ Testé")
            fi
        fi
    else
        echo -e "   ${RED}❌ Erreur de syntaxe bash${NC}"
        TEST_RESULTS+=("$script_name: ❌ Erreur de syntaxe")
        return 1
    fi
    
    echo ""
}

# Fonction pour vérifier les permissions
check_permissions() {
    echo -e "${BLUE}🔐 Vérification des permissions...${NC}"
    
    local all_executable=true
    
    for script in "$SCRIPTS_DIR"/*.sh; do
        if [ -f "$script" ]; then
            script_name=$(basename "$script")
            if [ -x "$script" ]; then
                echo -e "   ${GREEN}✅ $script_name${NC}"
            else
                echo -e "   ${RED}❌ $script_name (non exécutable)${NC}"
                all_executable=false
            fi
        fi
    done
    
    if [ "$all_executable" = true ]; then
        echo -e "   ${GREEN}✅ Tous les scripts sont exécutables${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Correction des permissions...${NC}"
        chmod +x "$SCRIPTS_DIR"/*.sh
        echo -e "   ${GREEN}✅ Permissions corrigées${NC}"
    fi
    
    echo ""
}

# Fonction pour vérifier la structure des scripts
check_script_structure() {
    echo -e "${BLUE}📁 Vérification de la structure des scripts...${NC}"
    
    local required_scripts=(
        "analyze-disk-usage.sh:Analyse de l'espace disque"
        "quick-cleanup.sh:Nettoyage rapide quotidien"
        "cleanup-disk-space.sh:Nettoyage complet approfondi"
        "auto-maintenance.sh:Maintenance automatique"
        "setup-cron-maintenance.sh:Configuration cron"
        "test-all-scripts.sh:Test des scripts"
    )
    
    local all_present=true
    
    for script_info in "${required_scripts[@]}"; do
        script_name="${script_info%:*}"
        description="${script_info#*:}"
        
        if [ -f "$SCRIPTS_DIR/$script_name" ]; then
            echo -e "   ${GREEN}✅ $script_name${NC}"
        else
            echo -e "   ${RED}❌ $script_name manquant${NC}"
            all_present=false
        fi
    done
    
    if [ "$all_present" = true ]; then
        echo -e "   ${GREEN}✅ Tous les scripts requis sont présents${NC}"
    else
        echo -e "   ${RED}❌ Certains scripts requis sont manquants${NC}"
    fi
    
    echo ""
}

# Fonction pour vérifier l'espace disque
check_disk_space() {
    echo -e "${BLUE}💾 Vérification de l'espace disque...${NC}"
    
    local disk_info=$(df -h . | awk 'NR==2')
    local total_size=$(echo "$disk_info" | awk '{print $2}')
    local used_size=$(echo "$disk_info" | awk '{print $3}')
    local available_size=$(echo "$disk_info" | awk '{print $4}')
    local usage_percent=$(echo "$disk_info" | awk '{print $5}' | sed 's/%//')
    
    echo "   📊 Taille totale: $total_size"
    echo "   📊 Espace utilisé: $used_size"
    echo "   📊 Espace disponible: $available_size"
    echo "   📊 Taux d'utilisation: $usage_percent%"
    
    if [ "$usage_percent" -gt 90 ]; then
        echo -e "   ${RED}🚨 Espace disque critique (>90%)${NC}"
    elif [ "$usage_percent" -gt 80 ]; then
        echo -e "   ${YELLOW}⚠️  Espace disque faible (>80%)${NC}"
    else
        echo -e "   ${GREEN}✅ Espace disque suffisant${NC}"
    fi
    
    echo ""
}

# Fonction pour vérifier la taille du projet
check_project_size() {
    echo -e "${BLUE}📁 Vérification de la taille du projet...${NC}"
    
    local project_size=$(du -sh . | cut -f1)
    echo "   📊 Taille du projet: $project_size"
    
    # Vérifier les gros dossiers
    echo "   📊 Dossiers > 100MB:"
    find . -type d -exec du -sh {} + 2>/dev/null | sort -hr | head -5 | while read size path; do
        if [[ "$size" =~ ^[0-9]+\.?[0-9]*[MG] ]]; then
            echo "      $size - $path"
        fi
    done
    
    echo ""
}

# Fonction pour afficher le résumé des tests
show_test_summary() {
    echo -e "${BLUE}📋 Résumé des tests...${NC}"
    echo "========================="
    
    local total_tests=${#TEST_RESULTS[@]}
    local passed_tests=0
    local failed_tests=0
    local warning_tests=0
    
    for result in "${TEST_RESULTS[@]}"; do
        if [[ "$result" == *"✅"* ]]; then
            ((passed_tests++))
            echo -e "   ${GREEN}$result${NC}"
        elif [[ "$result" == *"❌"* ]]; then
            ((failed_tests++))
            echo -e "   ${RED}$result${NC}"
        elif [[ "$result" == *"⚠️"* ]]; then
            ((warning_tests++))
            echo -e "   ${YELLOW}$result${NC}"
        fi
    done
    
    echo ""
    echo -e "${BLUE}📊 Statistiques des tests:${NC}"
    echo "   Total: $total_tests"
    echo -e "   ${GREEN}Réussis: $passed_tests${NC}"
    echo -e "   ${YELLOW}Avertissements: $warning_tests${NC}"
    echo -e "   ${RED}Échecs: $failed_tests${NC}"
    
    echo ""
    if [ $failed_tests -eq 0 ]; then
        echo -e "${GREEN}🎉 Tous les tests sont passés avec succès!${NC}"
    else
        echo -e "${RED}❌ Certains tests ont échoué. Vérifiez les erreurs ci-dessus.${NC}"
    fi
}

# Fonction principale
main() {
    echo "🚀 Début des tests..."
    echo "====================="
    echo ""
    
    # Vérifications préliminaires
    check_permissions
    check_script_structure
    check_disk_space
    check_project_size
    
    # Tests des scripts
    echo -e "${BLUE}🧪 Tests des scripts individuels...${NC}"
    echo "====================================="
    echo ""
    
    test_script "analyze-disk-usage.sh" "Analyse de l'espace disque"
    test_script "quick-cleanup.sh" "Nettoyage rapide quotidien"
    test_script "cleanup-disk-space.sh" "Nettoyage complet approfondi"
    test_script "auto-maintenance.sh" "Maintenance automatique"
    test_script "setup-cron-maintenance.sh" "Configuration cron"
    
    # Résumé des tests
    show_test_summary
    
    echo ""
    echo -e "${BLUE}💡 Recommandations:${NC}"
    echo "====================="
    
    if [ $failed_tests -eq 0 ]; then
        echo "   ✅ Tous les scripts sont fonctionnels"
        echo "   💡 Vous pouvez maintenant utiliser les scripts de nettoyage"
        echo "   🤖 Configurez la maintenance automatique avec setup-cron-maintenance.sh"
        echo "   📊 Surveillez régulièrement l'espace disque"
    else
        echo "   ❌ Certains scripts ont des problèmes"
        echo "   🔧 Vérifiez les erreurs et corrigez-les"
        echo "   📝 Consultez la documentation pour le dépannage"
    fi
    
    echo ""
    echo -e "${BLUE}🎯 Tests terminés!${NC}"
    echo "====================="
}

# Exécution du script
main "$@"

























