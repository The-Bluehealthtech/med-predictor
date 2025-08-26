#!/bin/bash

# Script d'Installation des Webhooks CI/CD
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
BACKUP_FILE="notifications-config.env.backup"

echo -e "${BLUE}🔧 Installation des Webhooks CI/CD${NC}"
echo "====================================="

# Vérification des prérequis
echo -e "\n${YELLOW}📋 Vérification des prérequis...${NC}"

if [[ ! -f "$CONFIG_FILE" ]]; then
    echo -e "${RED}❌ Fichier $CONFIG_FILE non trouvé${NC}"
    exit 1
fi

if [[ ! -f "$BACKUP_FILE" ]]; then
    echo -e "${YELLOW}⚠️  Création d'une sauvegarde...${NC}"
    cp "$CONFIG_FILE" "$BACKUP_FILE"
fi

echo -e "${GREEN}✅ Prérequis vérifiés${NC}"

# Fonction pour demander une valeur
ask_value() {
    local prompt="$1"
    local default="$2"
    local var_name="$3"
    
    echo -e "\n${CYAN}$prompt${NC}"
    if [[ -n "$default" ]]; then
        echo -e "${YELLOW}Valeur par défaut: $default${NC}"
    fi
    
    read -p "Entrez la valeur: " value
    
    if [[ -n "$value" ]]; then
        # Mettre à jour le fichier de configuration
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i.tmp "s|$var_name=.*|$var_name=$value|" "$CONFIG_FILE"
        else
            # Linux
            sed -i "s|$var_name=.*|$var_name=$value|" "$CONFIG_FILE"
        fi
        echo -e "${GREEN}✅ $var_name mis à jour${NC}"
    else
        echo -e "${YELLOW}⚠️  Valeur non modifiée${NC}"
    fi
}

# Configuration Slack
echo -e "\n${PURPLE}📱 Configuration Slack${NC}"
echo "=================="

echo -e "${BLUE}Instructions pour Slack:${NC}"
echo "1. Allez sur https://api.slack.com/apps"
echo "2. Créez une app 'CI/CD Notifications'"
echo "3. Activez 'Incoming Webhooks'"
echo "4. Créez un webhook pour le canal #ci-cd-alerts"
echo "5. Copiez l'URL du webhook"

ask_value "URL du webhook Slack:" "https://hooks.slack.com/services/YOUR_WORKSPACE/YOUR_CHANNEL/YOUR_TOKEN" "SLACK_WEBHOOK_URL"

ask_value "Canal Slack (avec #):" "#ci-cd-alerts" "SLACK_CHANNEL"

# Configuration Teams
echo -e "\n${PURPLE}💼 Configuration Microsoft Teams${NC}"
echo "================================"

echo -e "${BLUE}Instructions pour Teams:${NC}"
echo "1. Ouvrez le canal Teams"
echo "2. Cliquez sur '...' → 'Connectors'"
echo "3. Configurez 'Incoming Webhook'"
echo "4. Copiez l'URL du webhook"

ask_value "URL du webhook Teams:" "https://your-org.webhook.office.com/webhookb2/YOUR_WEBHOOK_ID" "TEAMS_WEBHOOK_URL"

# Configuration Email
echo -e "\n${PURPLE}📧 Configuration Email (Gmail)${NC}"
echo "================================"

echo -e "${BLUE}Instructions pour Gmail:${NC}"
echo "1. Activez l'authentification à 2 facteurs"
echo "2. Générez un mot de passe d'application"
echo "3. Utilisez ces informations"

ask_value "Adresse email Gmail:" "your-email@gmail.com" "SMTP_USERNAME"

ask_value "Mot de passe d'application Gmail:" "your-app-password" "SMTP_PASSWORD"

# Configuration Webhook personnalisé
echo -e "\n${PURPLE}🔗 Configuration Webhook Personnalisé (Optionnel)${NC}"
echo "================================================"

echo -e "${BLUE}Instructions:${NC}"
echo "1. Créez un endpoint HTTP pour recevoir des notifications"
echo "2. Configurez l'authentification si nécessaire"
echo "3. Laissez vide si vous n'en avez pas besoin"

ask_value "URL du webhook personnalisé (laissez vide si pas de webhook):" "" "WEBHOOK_URL"

# Configuration Métriques
echo -e "\n${PURPLE}📊 Configuration Métriques (Optionnel)${NC}"
echo "========================================="

echo -e "${BLUE}Instructions:${NC}"
echo "1. Créez un endpoint pour recevoir les métriques"
echo "2. Laissez vide si vous n'en avez pas besoin"

ask_value "Endpoint des métriques (laissez vide si pas d'endpoint):" "" "METRICS_ENDPOINT"

# Nettoyage des fichiers temporaires
if [[ "$OSTYPE" == "darwin"* ]]; then
    rm -f "$CONFIG_FILE.tmp"
fi

# Vérification finale
echo -e "\n${BLUE}🔍 Vérification de la configuration...${NC}"
echo "====================================="

if grep -q "YOUR_WORKSPACE" "$CONFIG_FILE"; then
    echo -e "${YELLOW}⚠️  Slack: URL par défaut détectée${NC}"
else
    echo -e "${GREEN}✅ Slack: URL configurée${NC}"
fi

if grep -q "YOUR_WEBHOOK_ID" "$CONFIG_FILE"; then
    echo -e "${YELLOW}⚠️  Teams: URL par défaut détectée${NC}"
else
    echo -e "${GREEN}✅ Teams: URL configurée${NC}"
fi

if grep -q "your-email@gmail.com" "$CONFIG_FILE"; then
    echo -e "${YELLOW}⚠️  Email: Configuration par défaut détectée${NC}"
else
    echo -e "${GREEN}✅ Email: Configuration personnalisée${NC}"
fi

# Test de la configuration
echo -e "\n${BLUE}🧪 Test de la configuration...${NC}"
echo "================================"

echo -e "${YELLOW}Exécution du script de test...${NC}"
if ./scripts/test-notifications.sh; then
    echo -e "${GREEN}✅ Configuration testée avec succès !${NC}"
else
    echo -e "${RED}❌ Erreurs détectées dans la configuration${NC}"
    echo -e "${YELLOW}Vérifiez les valeurs et relancez le test${NC}"
fi

# Résumé final
echo -e "\n${BLUE}📋 Résumé de l'Installation${NC}"
echo "================================"

echo -e "\n${GREEN}✅ Ce qui a été configuré:${NC}"
echo "   - Variables d'environnement mises à jour"
echo "   - Configuration Slack/Teams/Email"
echo "   - Fichier de sauvegarde créé"

echo -e "\n${YELLOW}⚠️  À faire manuellement:${NC}"
echo "   - Créer les apps Slack/Teams si pas encore fait"
echo " - Configurer l'authentification Gmail 2FA"
echo "   - Tester les notifications"

echo -e "\n${BLUE}🎯 Prochaines étapes:${NC}"
echo "   1. Vérifiez que les webhooks fonctionnent"
echo "   2. Testez avec le pipeline CI/CD"
echo "   3. Personnalisez les messages si nécessaire"

echo -e "\n${GREEN}🎉 Installation des webhooks terminée !${NC}"
echo "Vos notifications CI/CD sont maintenant configurées."
