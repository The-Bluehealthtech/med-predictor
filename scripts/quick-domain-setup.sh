#!/bin/bash

# Configuration Rapide de Domaine - Med-Predictor
# Script simplifié pour configurer rapidement votre domaine

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Configuration Rapide de Domaine${NC}"
echo "=================================="

# Demander le domaine à l'utilisateur
read -p "🌐 Entrez votre domaine (ex: med-predictor.com): " DOMAIN
read -p "📧 Entrez votre email pour SSL (ex: admin@med-predictor.com): " EMAIL

if [ -z "$DOMAIN" ] || [ -z "$EMAIL" ]; then
    echo -e "${RED}❌ Domaine et email requis${NC}"
    exit 1
fi

echo -e "${YELLOW}Configuration pour: ${DOMAIN}${NC}"
echo -e "${YELLOW}Email SSL: ${EMAIL}${NC}"

# Exporter les variables
export DOMAIN="$DOMAIN"
export SSL_EMAIL="$EMAIL"

# Exécuter le script de configuration
echo -e "\n${BLUE}Démarrage de la configuration...${NC}"
./scripts/setup-domain.sh

echo -e "\n${GREEN}🎉 Configuration terminée!${NC}"
echo -e "${BLUE}Prochaines étapes:${NC}"
echo -e "${YELLOW}1. Configurez vos DNS selon dns-setup-instructions.txt${NC}"
echo -e "${YELLOW}2. Attendez 5-30 minutes pour la propagation DNS${NC}"
echo -e "${YELLOW}3. Testez avec: DOMAIN=${DOMAIN} ./scripts/test-domain.sh${NC}"

# Proposer de tester immédiatement
read -p "🧪 Voulez-vous tester maintenant? (y/n): " TEST_NOW
if [ "$TEST_NOW" = "y" ] || [ "$TEST_NOW" = "Y" ]; then
    echo -e "\n${BLUE}Démarrage des tests...${NC}"
    DOMAIN="$DOMAIN" ./scripts/test-domain.sh
fi

