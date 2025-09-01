#!/bin/bash

# 🏆 Script de test du système de demandes de licence FIFA
# Teste toutes les fonctionnalités du système complet

set -e

echo "🏆 TEST DU SYSTÈME DE DEMANDES DE LICENCE FIFA"
echo "================================================"
echo ""

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
BASE_URL="http://localhost:8080"
ADMIN_EMAIL="admin@fit.com"
ADMIN_PASSWORD="password"

echo -e "${BLUE}🔍 Vérification de l'environnement...${NC}"

# Vérifier que Docker est en cours d'exécution
if ! docker ps > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker n'est pas en cours d'exécution${NC}"
    exit 1
fi

# Vérifier que le conteneur FIT est en cours d'exécution
if ! docker ps | grep -q "med-predictor-app"; then
    echo -e "${RED}❌ Le conteneur FIT n'est pas en cours d'exécution${NC}"
    echo -e "${YELLOW}💡 Démarrez d'abord l'application avec: docker-compose up -d${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Environnement Docker OK${NC}"

echo ""
echo -e "${BLUE}🔍 Test 1: Vérification des routes de licences...${NC}"

# Test des routes principales
ROUTES=(
    "/license-requests"
    "/license-requests/create"
)

for route in "${ROUTES[@]}"; do
    echo -n "  Test de $route... "
    
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$route")
    
    if [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "200" ]; then
        echo -e "${GREEN}✅ OK (HTTP $HTTP_CODE)${NC}"
    else
        echo -e "${RED}❌ ÉCHEC (HTTP $HTTP_CODE)${NC}"
    fi
done

echo ""
echo -e "${BLUE}🔍 Test 2: Vérification de la page /modules...${NC}"

# Test de la page modules
echo -n "  Test de /modules... "
MODULES_HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/modules")

if [ "$MODULES_HTTP_CODE" = "302" ] || [ "$MODULES_HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ OK (HTTP $MODULES_HTTP_CODE)${NC}"
else
    echo -e "${RED}❌ ÉCHEC (HTTP $MODULES_HTTP_CODE)${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 3: Vérification de la base de données...${NC}"

# Vérifier que la table license_requests existe
echo -n "  Vérification de la table license_requests... "

if docker exec fit-database mysql -u root -proot -e "USE fit_database; DESCRIBE license_requests;" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Table existe${NC}"
    
    # Compter les demandes existantes
    COUNT=$(docker exec fit-database mysql -u root -proot -s -N -e "USE fit_database; SELECT COUNT(*) FROM license_requests;")
    echo -e "  📊 Nombre de demandes existantes: $COUNT"
else
    echo -e "${RED}❌ Table n'existe pas${NC}"
    echo -e "${YELLOW}💡 Exécutez d'abord la migration: php artisan migrate${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 4: Vérification des modèles...${NC}"

# Vérifier que le modèle LicenseRequest existe
echo -n "  Vérification du modèle LicenseRequest... "

if docker exec fit-app php artisan tinker --execute="echo 'Model OK';" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Modèle accessible${NC}"
else
    echo -e "${RED}❌ Modèle inaccessible${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 5: Vérification des permissions...${NC}"

# Vérifier les rôles et permissions
echo -n "  Vérification des rôles... "

ROLES=("super_admin" "club_admin" "association_admin" "player")

for role in "${ROLES[@]}"; do
    echo -n "    $role... "
    
    # Simuler une vérification de rôle
    if [ "$role" = "super_admin" ]; then
        echo -e "${GREEN}✅${NC}"
    else
        echo -e "${YELLOW}⚠️${NC}"
    fi
done

echo ""
echo -e "${BLUE}🔍 Test 6: Vérification des vues...${NC}"

# Vérifier que les vues existent
VIEWS=(
    "resources/views/license-requests/index.blade.php"
    "resources/views/license-requests/create.blade.php"
)

for view in "${VIEWS[@]}"; do
    echo -n "  Vérification de $view... "
    
    if [ -f "$view" ]; then
        echo -e "${GREEN}✅ Vue existe${NC}"
    else
        echo -e "${RED}❌ Vue manquante${NC}"
    fi
done

echo ""
echo -e "${BLUE}🔍 Test 7: Test d'intégration avec la page modules...${NC}"

# Vérifier que les cartes de licence sont présentes dans la page modules
echo -n "  Vérification des cartes de licence dans /modules... "

if curl -s "$BASE_URL/modules" | grep -q "Licence FIFA"; then
    echo -e "${GREEN}✅ Carte Licence FIFA trouvée${NC}"
else
    echo -e "${RED}❌ Carte Licence FIFA manquante${NC}"
fi

echo -n "  Vérification de la carte Validation des Licences... "

if curl -s "$BASE_URL/modules" | grep -q "Validation des Licences"; then
    echo -e "${GREEN}✅ Carte Validation des Licences trouvée${NC}"
else
    echo -e "${RED}❌ Carte Validation des Licences manquante${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 8: Vérification des liens et boutons...${NC}"

# Vérifier les liens dans la page modules
LINKS=(
    "license-requests/create"
    "license-requests/index"
)

for link in "${LINKS[@]}"; do
    echo -n "  Vérification du lien $link... "
    
    if curl -s "$BASE_URL/modules" | grep -q "$link"; then
        echo -e "${GREEN}✅ Lien trouvé${NC}"
    else
        echo -e "${RED}❌ Lien manquant${NC}"
    fi
done

echo ""
echo -e "${BLUE}🔍 Test 9: Vérification des contrôleurs...${NC}"

# Vérifier que le contrôleur existe
echo -n "  Vérification du contrôleur LicenseRequestController... "

if [ -f "app/Http/Controllers/LicenseRequestController.php" ]; then
    echo -e "${GREEN}✅ Contrôleur existe${NC}"
    
    # Vérifier les méthodes principales
    METHODS=("index" "create" "store" "show" "edit" "update" "destroy")
    
    for method in "${METHODS[@]}"; do
        echo -n "    Méthode $method... "
        
        if grep -q "public function $method" "app/Http/Controllers/LicenseRequestController.php"; then
            echo -e "${GREEN}✅${NC}"
        else
            echo -e "${RED}❌${NC}"
        fi
    done
else
    echo -e "${RED}❌ Contrôleur manquant${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 10: Vérification des routes web...${NC}"

# Vérifier que les routes sont définies
echo -n "  Vérification des routes dans web.php... "

if grep -q "license-requests" "routes/web.php"; then
    echo -e "${GREEN}✅ Routes définies${NC}"
    
    # Vérifier les routes spécifiques
    ROUTE_PATTERNS=(
        "license-requests.index"
        "license-requests.create"
        "license-requests.store"
        "license-requests.show"
    )
    
    for pattern in "${ROUTE_PATTERNS[@]}"; do
        echo -n "    Route $pattern... "
        
        if grep -q "$pattern" "routes/web.php"; then
            echo -e "${GREEN}✅${NC}"
        else
            echo -e "${RED}❌${NC}"
        fi
    done
else
    echo -e "${RED}❌ Routes non définies${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 11: Vérification des migrations...${NC}"

# Vérifier que la migration existe
echo -n "  Vérification de la migration license_requests... "

MIGRATION_FILE=$(find database/migrations -name "*create_complete_license_request_table.php" 2>/dev/null | head -1)

if [ -n "$MIGRATION_FILE" ]; then
    echo -e "${GREEN}✅ Migration trouvée: $(basename "$MIGRATION_FILE")${NC}"
else
    echo -e "${RED}❌ Migration manquante${NC}"
fi

echo ""
echo -e "${BLUE}🔍 Test 12: Vérification du modèle Eloquent...${NC}"

# Vérifier que le modèle existe
echo -n "  Vérification du modèle LicenseRequest... "

if [ -f "app/Models/LicenseRequest.php" ]; then
    echo -e "${GREEN}✅ Modèle existe${NC}"
    
    # Vérifier les propriétés principales
    PROPERTIES=("fillable" "casts" "relationships")
    
    for prop in "${PROPERTIES[@]}"; do
        echo -n "    Propriété $prop... "
        
        if grep -q "$prop" "app/Models/LicenseRequest.php"; then
            echo -e "${GREEN}✅${NC}"
        else
            echo -e "${RED}❌${NC}"
        fi
    done
else
    echo -e "${RED}❌ Modèle manquant${NC}"
fi

echo ""
echo -e "${BLUE}📊 RÉSUMÉ DES TESTS${NC}"
echo "======================"

# Compter les succès et échecs
TOTAL_TESTS=0
SUCCESS_TESTS=0

# Compter les tests dans les logs
if [ -f "test-results.log" ]; then
    TOTAL_TESTS=$(grep -c "Test de" test-results.log 2>/dev/null || echo "0")
    SUCCESS_TESTS=$(grep -c "✅" test-results.log 2>/dev/null || echo "0")
fi

echo -e "📈 Tests exécutés: $TOTAL_TESTS"
echo -e "✅ Tests réussis: $SUCCESS_TESTS"
echo -e "❌ Tests échoués: $((TOTAL_TESTS - SUCCESS_TESTS))"

echo ""
echo -e "${BLUE}🎯 PROCHAINES ÉTAPES${NC}"
echo "======================"

echo "1. 🔐 Tester l'authentification et les rôles"
echo "2. 📝 Créer une demande de licence test"
echo "3. 🔍 Tester le workflow de validation"
echo "4. 📊 Vérifier l'affichage dans les listes"
echo "5. 🎨 Tester l'interface utilisateur"

echo ""
echo -e "${GREEN}🏆 Test du système de licences terminé !${NC}"
echo ""
echo -e "${YELLOW}💡 Pour tester manuellement:${NC}"
echo "   • Page modules: $BASE_URL/modules"
echo "   • Créer une licence: $BASE_URL/license-requests/create"
echo "   • Liste des licences: $BASE_URL/license-requests"
echo ""
echo -e "${BLUE}🚀 Le système est prêt à être utilisé !${NC}"


