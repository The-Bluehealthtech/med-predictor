#!/bin/bash

# Script de Diagnostic de l'Authentification Gmail
# Version: 1.0

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Diagnostic de l'Authentification Gmail CI/CD${NC}"
echo "=================================================="

# Chargement de la configuration
source "notifications-config.env"

echo -e "\n${YELLOW}📋 Configuration Actuelle${NC}"
echo "================================"
echo -e "Email: ${BLUE}$SMTP_USERNAME${NC}"
echo -e "Host: ${BLUE}$SMTP_HOST${NC}"
echo -e "Port: ${BLUE}$SMTP_PORT${NC}"

echo -e "\n${PURPLE}🚨 Problème Détecté: Authentification Échouée${NC}"
echo "=================================================="
echo "L'erreur 'Could not authenticate' indique un problème avec:"
echo "1. L'authentification à 2 facteurs (2FA)"
echo "2. Le mot de passe d'application"
echo "3. Les paramètres de sécurité Gmail"

echo -e "\n${CYAN}🔧 Solutions Recommandées${NC}"
echo "================================"

echo -e "\n${GREEN}✅ Solution 1: Vérifier l'Authentification 2FA${NC}"
echo "1. Allez sur https://myaccount.google.com/security"
echo "2. Vérifiez que 'Validation en 2 étapes' est activée"
echo "3. Si non activée, activez-la d'abord"

echo -e "\n${GREEN}✅ Solution 2: Générer un Mot de Passe d'Application${NC}"
echo "1. Allez sur https://myaccount.google.com/apppasswords"
echo "2. Sélectionnez 'Autre (nom personnalisé)'"
echo "3. Nommez-le 'CI/CD Notifications'"
echo "4. Copiez le mot de passe généré (16 caractères)"

echo -e "\n${GREEN}✅ Solution 3: Vérifier les Paramètres de Sécurité${NC}"
echo "1. Allez sur https://myaccount.google.com/security"
echo "2. Vérifiez 'Connexions moins sécurisées' (désactivé)"
echo "3. Vérifiez 'Accès à l'application' (activé)"

echo -e "\n${YELLOW}⚠️  Test de Connexion Alternative${NC}"
echo "======================================"

# Test avec différents ports et méthodes
echo -e "${BLUE}Test de connectivité sur différents ports...${NC}"

# Test port 587 (TLS)
if nc -z -w 5 smtp.gmail.com 587 2>/dev/null; then
    echo -e "${GREEN}✅ Port 587 (TLS): Ouvert${NC}"
else
    echo -e "${RED}❌ Port 587 (TLS): Fermé${NC}"
fi

# Test port 465 (SSL)
if nc -z -w 5 smtp.gmail.com 465 2>/dev/null; then
    echo -e "${GREEN}✅ Port 465 (SSL): Ouvert${NC}"
else
    echo -e "${RED}❌ Port 465 (SSL): Fermé${NC}"
fi

# Test port 25 (Standard)
if nc -z -w 5 smtp.gmail.com 25 2>/dev/null; then
    echo -e "${GREEN}✅ Port 25 (Standard): Ouvert${NC}"
else
    echo -e "${RED}❌ Port 25 (Standard): Fermé${NC}"
fi

echo -e "\n${CYAN}🔧 Configuration Alternative Recommandée${NC}"
echo "=============================================="

echo -e "\n${BLUE}Option 1: Port 465 avec SSL (Plus Sécurisé)${NC}"
echo "Modifiez notifications-config.env:"
echo "SMTP_PORT=465"
echo "SMTP_ENCRYPTION=ssl"

echo -e "\n${BLUE}Option 2: Port 587 avec TLS (Recommandé)${NC}"
echo "Modifiez notifications-config.env:"
echo "SMTP_PORT=587"
echo "SMTP_ENCRYPTION=tls"

echo -e "\n${YELLOW}📝 Instructions de Correction${NC}"
echo "======================================"

echo "1. ${GREEN}Vérifiez votre authentification 2FA Gmail${NC}"
echo "2. ${GREEN}Générez un nouveau mot de passe d'application${NC}"
echo "3. ${GREEN}Mettez à jour notifications-config.env${NC}"
echo "4. ${GREEN}Relancez le test: ./scripts/test-email-notifications.sh${NC}"

echo -e "\n${PURPLE}🔗 Liens Utiles${NC}"
echo "================"
echo "• Sécurité Google: https://myaccount.google.com/security"
echo "• Mots de passe d'application: https://myaccount.google.com/apppasswords"
echo "• Aide Gmail: https://support.google.com/mail/answer/7126229"

echo -e "\n${GREEN}🎯 Prochaines Étapes${NC}"
echo "======================"
echo "1. Corrigez l'authentification Gmail"
echo "2. Relancez le test d'email"
echo "3. Configurez les notifications automatiques"

echo -e "\n${BLUE}💡 Conseil${NC}"
echo "========"
echo "Le problème vient probablement du mot de passe d'application."
echo "Générez-en un nouveau et mettez à jour votre configuration."

echo -e "\n${GREEN}🎉 Diagnostic terminé !${NC}"
echo "Suivez les instructions ci-dessus pour résoudre le problème."
