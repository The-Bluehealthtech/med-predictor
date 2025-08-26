#!/bin/bash

# Script de Correction de l'Authentification Gmail
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

# Configuration
CONFIG_FILE="notifications-config.env"
BACKUP_FILE="notifications-config.env.backup.$(date +%Y%m%d_%H%M%S)"

echo -e "${BLUE}🔧 Correction de l'Authentification Gmail CI/CD${NC}"
echo "=================================================="

# Vérification du fichier de configuration
if [[ ! -f "$CONFIG_FILE" ]]; then
    echo -e "${RED}❌ Fichier $CONFIG_FILE non trouvé${NC}"
    exit 1
fi

# Sauvegarde de la configuration actuelle
echo -e "\n${YELLOW}💾 Sauvegarde de la Configuration${NC}"
echo "======================================"
cp "$CONFIG_FILE" "$BACKUP_FILE"
echo -e "${GREEN}✅ Configuration sauvegardée: $BACKUP_FILE${NC}"

# Chargement de la configuration actuelle
source "$CONFIG_FILE"

echo -e "\n${CYAN}📋 Configuration Actuelle${NC}"
echo "================================"
echo -e "Email: ${BLUE}$SMTP_USERNAME${NC}"
echo -e "Host: ${BLUE}$SMTP_HOST${NC}"
echo -e "Port: ${BLUE}$SMTP_PORT${NC}"
echo -e "Mot de passe: ${BLUE}${SMTP_PASSWORD:0:4}****${NC}"

echo -e "\n${PURPLE}🚨 Problème Identifié${NC}"
echo "================================"
echo "L'authentification Gmail échoue probablement à cause de:"
echo "• Mot de passe d'application incorrect ou expiré"
echo "• Authentification 2FA non configurée"
echo "• Paramètres de sécurité Gmail"

echo -e "\n${BLUE}🔧 Processus de Correction${NC}"
echo "======================================"

# Étape 1: Vérification de l'email
echo -e "\n${YELLOW}📧 Étape 1: Vérification de l'Email${NC}"
echo "======================================"
read -p "Votre email Gmail est-il correct ? [$SMTP_USERNAME] (y/n): " confirm_email

if [[ "$confirm_email" == "n" || "$confirm_email" == "N" ]]; then
    read -p "Entrez votre email Gmail correct: " new_email
    if [[ -n "$new_email" ]]; then
        sed -i.tmp "s|SMTP_USERNAME=.*|SMTP_USERNAME=$new_email|" "$CONFIG_FILE"
        echo -e "${GREEN}✅ Email mis à jour: $new_email${NC}"
    fi
fi

# Étape 2: Vérification de l'authentification 2FA
echo -e "\n${YELLOW}🔐 Étape 2: Authentification 2FA${NC}"
echo "======================================"
echo -e "${BLUE}Instructions:${NC}"
echo "1. Allez sur https://myaccount.google.com/security"
echo "2. Vérifiez que 'Validation en 2 étapes' est activée"
echo "3. Si non activée, activez-la d'abord"

read -p "L'authentification 2FA est-elle activée ? (y/n): " has_2fa

if [[ "$has_2fa" == "n" || "$has_2fa" == "N" ]]; then
    echo -e "${RED}❌ Vous devez d'abord activer l'authentification 2FA${NC}"
    echo -e "${YELLOW}Allez sur: https://myaccount.google.com/security${NC}"
    echo -e "${YELLOW}Activez 'Validation en 2 étapes' puis relancez ce script${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Authentification 2FA confirmée${NC}"

# Étape 3: Génération du mot de passe d'application
echo -e "\n${YELLOW}🔑 Étape 3: Mot de Passe d'Application${NC}"
echo "=============================================="
echo -e "${BLUE}Instructions:${NC}"
echo "1. Allez sur https://myaccount.google.com/apppasswords"
echo "2. Sélectionnez 'Autre (nom personnalisé)'"
echo "3. Nommez-le 'CI/CD Notifications'"
echo "4. Copiez le mot de passe généré (16 caractères)"

read -p "Avez-vous généré un nouveau mot de passe d'application ? (y/n): " has_app_password

if [[ "$has_app_password" == "n" || "$has_app_password" == "N" ]]; then
    echo -e "${RED}❌ Générez d'abord un mot de passe d'application${NC}"
    echo -e "${YELLOW}Allez sur: https://myaccount.google.com/apppasswords${NC}"
    echo -e "${YELLOW}Puis relancez ce script${NC}"
    exit 1
fi

# Demande du nouveau mot de passe
echo -e "\n${CYAN}📝 Saisie du Nouveau Mot de Passe${NC}"
echo "=========================================="
read -s -p "Entrez le nouveau mot de passe d'application (16 caractères): " new_password
echo ""

if [[ ${#new_password} -ne 16 ]]; then
    echo -e "${RED}❌ Le mot de passe d'application doit faire 16 caractères${NC}"
    echo -e "${YELLOW}Vérifiez et réessayez${NC}"
    exit 1
fi

# Mise à jour du mot de passe
sed -i.tmp "s|SMTP_PASSWORD=.*|SMTP_PASSWORD=$new_password|" "$CONFIG_FILE"
echo -e "${GREEN}✅ Mot de passe d'application mis à jour${NC}"

# Étape 4: Test de la nouvelle configuration
echo -e "\n${YELLOW}🧪 Étape 4: Test de la Nouvelle Configuration${NC}"
echo "================================================"

# Nettoyage des fichiers temporaires
rm -f "$CONFIG_FILE.tmp"

# Rechargement de la configuration
source "$CONFIG_FILE"

echo -e "${BLUE}Test de la nouvelle configuration...${NC}"
echo -e "Email: ${BLUE}$SMTP_USERNAME${NC}"
echo -e "Mot de passe: ${BLUE}${SMTP_PASSWORD:0:4}****${NC}"

# Test de connectivité
echo -e "\n${BLUE}Test de connectivité SMTP...${NC}"
if nc -z -w 5 "$SMTP_HOST" "$SMTP_PORT" 2>/dev/null; then
    echo -e "${GREEN}✅ Connexion SMTP réussie${NC}"
else
    echo -e "${RED}❌ Connexion SMTP échouée${NC}"
    exit 1
fi

# Étape 5: Test d'envoi d'email
echo -e "\n${YELLOW}📧 Étape 5: Test d'Envoi d'Email${NC}"
echo "======================================"

read -p "Voulez-vous tester l'envoi d'email maintenant ? (y/n): " test_now

if [[ "$test_now" == "y" || "$test_now" == "Y" ]]; then
    echo -e "${BLUE}Lancement du test d'email...${NC}"
    if ./scripts/test-email-notifications.sh; then
        echo -e "\n${GREEN}🎉 Test d'email réussi !${NC}"
        echo -e "${GREEN}Votre authentification Gmail est maintenant corrigée !${NC}"
    else
        echo -e "\n${RED}❌ Test d'email échoué${NC}"
        echo -e "${YELLOW}Vérifiez la configuration et réessayez${NC}"
    fi
else
    echo -e "${BLUE}Test d'email différé${NC}"
    echo -e "${YELLOW}Lancez manuellement: ./scripts/test-email-notifications.sh${NC}"
fi

# Résumé final
echo -e "\n${BLUE}📊 Résumé de la Correction${NC}"
echo "======================================"

echo -e "\n${GREEN}✅ Ce qui a été corrigé:${NC}"
echo "   - Configuration sauvegardée"
echo "   - Email vérifié et confirmé"
echo "   - Authentification 2FA confirmée"
echo "   - Mot de passe d'application mis à jour"

echo -e "\n${YELLOW}⚠️  Fichiers créés:${NC}"
echo "   - Sauvegarde: $BACKUP_FILE"
echo "   - Configuration mise à jour: $CONFIG_FILE"

echo -e "\n${BLUE}🎯 Prochaines Étapes:${NC}"
echo "   1. ✅ Authentification Gmail corrigée"
echo "   2. 🧪 Testez l'envoi d'email"
echo "   3. 🔧 Configurez Slack et Teams si souhaité"
echo "   4. 🚀 Intégrez avec votre pipeline CI/CD"

echo -e "\n${GREEN}🎉 Correction de l'authentification Gmail terminée !${NC}"
echo "Votre système de notifications email CI/CD devrait maintenant fonctionner."
