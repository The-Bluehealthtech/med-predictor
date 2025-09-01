#!/bin/bash

echo "🧪 TEST COMPLET DU SYSTÈME DE CRÉATION DE JOUEURS"
echo "=================================================="

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test 1: Vérifier que l'application est accessible
echo -e "\n${BLUE}1. Test d'accessibilité de l'application...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"; then
    echo -e "${GREEN}✅ Application accessible${NC}"
else
    echo -e "${RED}❌ Application non accessible${NC}"
    exit 1
fi

# Test 2: Vérifier la route de création de joueurs
echo -e "\n${BLUE}2. Test de la route de création de joueurs...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/player-registration/create | grep -q "200\|302"; then
    echo -e "${GREEN}✅ Route de création accessible${NC}"
else
    echo -e "${RED}❌ Route de création non accessible${NC}"
fi

# Test 3: Vérifier la route de liste des joueurs
echo -e "\n${BLUE}3. Test de la route de liste des joueurs...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/player-registration | grep -q "200\|302"; then
    echo -e "${GREEN}✅ Route de liste accessible${NC}"
else
    echo -e "${RED}❌ Route de liste non accessible${NC}"
fi

# Test 4: Vérifier que le contrôleur existe
echo -e "\n${BLUE}4. Vérification du contrôleur...${NC}"
if [ -f "app/Http/Controllers/PlayerRegistrationController.php" ]; then
    echo -e "${GREEN}✅ Contrôleur PlayerRegistrationController existe${NC}"
    
    # Vérifier les méthodes
    if grep -q "public function create" app/Http/Controllers/PlayerRegistrationController.php; then
        echo -e "${GREEN}✅ Méthode create() trouvée${NC}"
    else
        echo -e "${RED}❌ Méthode create() manquante${NC}"
    fi
    
    if grep -q "public function store" app/Http/Controllers/PlayerRegistrationController.php; then
        echo -e "${GREEN}✅ Méthode store() trouvée${NC}"
    else
        echo -e "${RED}❌ Méthode store() manquante${NC}"
    fi
    
    if grep -q "public function index" app/Http/Controllers/PlayerRegistrationController.php; then
        echo -e "${GREEN}✅ Méthode index() trouvée${NC}"
    else
        echo -e "${RED}❌ Méthode index() manquante${NC}"
    fi
else
    echo -e "${RED}❌ Contrôleur PlayerRegistrationController manquant${NC}"
fi

# Test 5: Vérifier que les vues existent
echo -e "\n${BLUE}5. Vérification des vues...${NC}"
if [ -f "resources/views/modules/player-registration/create.blade.php" ]; then
    echo -e "${GREEN}✅ Vue create.blade.php existe${NC}"
else
    echo -e "${RED}❌ Vue create.blade.php manquante${NC}"
fi

if [ -f "resources/views/modules/player-registration/index.blade.php" ]; then
    echo -e "${GREEN}✅ Vue index.blade.php existe${NC}"
else
    echo -e "${RED}❌ Vue index.blade.php manquante${NC}"
fi

# Test 6: Vérifier les routes
echo -e "\n${BLUE}6. Vérification des routes...${NC}"
if grep -q "player-registration.index" routes/web.php; then
    echo -e "${GREEN}✅ Route player-registration.index définie${NC}"
else
    echo -e "${RED}❌ Route player-registration.index manquante${NC}"
fi

if grep -q "player-registration.create" routes/web.php; then
    echo -e "${GREEN}✅ Route player-registration.create définie${NC}"
else
    echo -e "${RED}❌ Route player-registration.create manquante${NC}"
fi

if grep -q "player-registration.store" routes/web.php; then
    echo -e "${GREEN}✅ Route player-registration.store définie${NC}"
else
    echo -e "${RED}❌ Route player-registration.store manquante${NC}"
fi

# Test 7: Vérifier la base de données
echo -e "\n${BLUE}7. Vérification de la base de données...${NC}"
if docker exec fit-php php artisan tinker --execute="echo 'DB OK';" 2>/dev/null | grep -q "DB OK"; then
    echo -e "${GREEN}✅ Base de données accessible${NC}"
    
    # Vérifier la table players
    if docker exec fit-php php artisan tinker --execute="echo 'Players table: ' . (Schema::hasTable('players') ? 'OK' : 'MISSING');" 2>/dev/null | grep -q "OK"; then
        echo -e "${GREEN}✅ Table players existe${NC}"
    else
        echo -e "${RED}❌ Table players manquante${NC}"
    fi
else
    echo -e "${RED}❌ Base de données non accessible${NC}"
fi

# Test 8: Vérifier le stockage
echo -e "\n${BLUE}8. Vérification du stockage...${NC}"
if docker exec fit-web-1 php artisan storage:link 2>/dev/null; then
    echo -e "${GREEN}✅ Lien de stockage créé${NC}"
else
    echo -e "${YELLOW}⚠️ Lien de stockage déjà existant ou erreur${NC}"
fi

# Test 9: Vérifier les permissions
echo -e "\n${BLUE}9. Vérification des permissions...${NC}"
if [ -d "storage/app/public" ] && [ -w "storage/app/public" ]; then
    echo -e "${GREEN}✅ Dossier de stockage accessible en écriture${NC}"
else
    echo -e "${RED}❌ Problème de permissions sur le stockage${NC}"
fi

# Test 10: Vérifier le modèle Player
echo -e "\n${BLUE}10. Vérification du modèle Player...${NC}"
if [ -f "app/Models/Player.php" ]; then
    echo -e "${GREEN}✅ Modèle Player existe${NC}"
    
    if grep -q "player_picture" app/Models/Player.php; then
        echo -e "${GREEN}✅ Champ player_picture dans le modèle${NC}"
    else
        echo -e "${RED}❌ Champ player_picture manquant dans le modèle${NC}"
    fi
    
    if grep -q "generateFifaConnectId" app/Models/Player.php; then
        echo -e "${GREEN}✅ Méthode generateFifaConnectId() trouvée${NC}"
    else
        echo -e "${RED}❌ Méthode generateFifaConnectId() manquante${NC}"
    fi
else
    echo -e "${RED}❌ Modèle Player manquant${NC}"
fi

echo -e "\n${BLUE}📋 RÉSUMÉ DES TESTS${NC}"
echo "=================="

echo -e "\n${GREEN}🎯 POUR TESTER MANUELLEMENT :${NC}"
echo "1. Allez sur : http://localhost:8080/player-registration/create"
echo "2. Remplissez le formulaire avec une photo"
echo "3. Testez avec un fichier non-image pour voir l'erreur"
echo "4. Testez avec une image trop volumineuse (>5MB)"
echo "5. Vérifiez la prévisualisation de l'image"
echo "6. Soumettez le formulaire"
echo "7. Vérifiez la liste : http://localhost:8080/player-registration"

echo -e "\n${YELLOW}⚠️ NOTES IMPORTANTES :${NC}"
echo "- Les photos sont stockées dans storage/app/public/player_photos"
echo "- La taille maximale est de 5MB"
echo "- Formats acceptés : JPG, PNG, JPEG"
echo "- Les erreurs sont affichées en temps réel"

echo -e "\n${BLUE}🚀 Le système est prêt pour les tests !${NC}"


