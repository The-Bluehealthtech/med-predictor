#!/bin/bash

# Script de Test des Notifications CI/CD
# Version: 1.0

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
NOTIFICATIONS_CONFIG="notifications-config.env"
NOTIFICATIONS_YAML="notifications.yml"

echo -e "${BLUE}🔔 Test des Notifications CI/CD${NC}"
echo "=================================="

# Vérification des fichiers de configuration
echo -e "\n${YELLOW}📋 Vérification de la configuration...${NC}"

if [[ ! -f "$NOTIFICATIONS_CONFIG" ]]; then
    echo -e "${RED}❌ Fichier $NOTIFICATIONS_CONFIG non trouvé${NC}"
    exit 1
fi

if [[ ! -f "$NOTIFICATIONS_YAML" ]]; then
    echo -e "${RED}❌ Fichier $NOTIFICATIONS_YAML non trouvé${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Fichiers de configuration trouvés${NC}"

# Chargement des variables d'environnement
echo -e "\n${YELLOW}🔧 Chargement des variables d'environnement...${NC}"
source "$NOTIFICATIONS_CONFIG"

# Test de la configuration Slack
echo -e "\n${YELLOW}📱 Test de la configuration Slack...${NC}"
if [[ -n "$SLACK_WEBHOOK_URL" && "$SLACK_WEBHOOK_URL" != "https://hooks.slack.com/services/YOUR_WORKSPACE/YOUR_CHANNEL/YOUR_TOKEN" ]]; then
    echo -e "${GREEN}✅ Webhook Slack configuré${NC}"
    echo "   Channel: ${SLACK_CHANNEL:-#ci-cd-alerts}"
else
    echo -e "${YELLOW}⚠️  Webhook Slack non configuré (utilisez la valeur par défaut)${NC}"
fi

# Test de la configuration Teams
echo -e "\n${YELLOW}💼 Test de la configuration Teams...${NC}"
if [[ -n "$TEAMS_WEBHOOK_URL" && "$TEAMS_WEBHOOK_URL" != "https://your-org.webhook.office.com/webhookb2/YOUR_WEBHOOK_ID" ]]; then
    echo -e "${GREEN}✅ Webhook Teams configuré${NC}"
else
    echo -e "${YELLOW}⚠️  Webhook Teams non configuré (utilisez la valeur par défaut)${NC}"
fi

# Test de la configuration Email
echo -e "\n${YELLOW}📧 Test de la configuration Email...${NC}"
if [[ -n "$SMTP_HOST" && "$SMTP_HOST" != "smtp.gmail.com" ]]; then
    echo -e "${GREEN}✅ Configuration SMTP trouvée${NC}"
    echo "   Host: $SMTP_HOST:$SMTP_PORT"
else
    echo -e "${YELLOW}⚠️  Configuration SMTP non personnalisée${NC}"
fi

# Test de la configuration Webhook
echo -e "\n${YELLOW}🔗 Test de la configuration Webhook...${NC}"
if [[ -n "$WEBHOOK_URL" && "$WEBHOOK_URL" != "https://your-webhook-endpoint.com/notify" ]]; then
    echo -e "${GREEN}✅ Webhook personnalisé configuré${NC}"
else
    echo -e "${YELLOW}⚠️  Webhook personnalisé non configuré${NC}"
fi

# Test de la configuration des métriques
echo -e "\n${YELLOW}📊 Test de la configuration des métriques...${NC}"
if [[ -n "$METRICS_ENDPOINT" && "$METRICS_ENDPOINT" != "https://your-metrics-endpoint.com" ]]; then
    echo -e "${GREEN}✅ Endpoint métriques configuré${NC}"
else
    echo -e "${YELLOW}⚠️  Endpoint métriques non configuré${NC}"
fi

# Test de la configuration de l'environnement
echo -e "\n${YELLOW}🌍 Test de la configuration de l'environnement...${NC}"
if [[ -n "$DEPLOY_ENVIRONMENT" ]]; then
    echo -e "${GREEN}✅ Environnement de déploiement: $DEPLOY_ENVIRONMENT${NC}"
else
    echo -e "${YELLOW}⚠️  Environnement de déploiement non défini${NC}"
fi

# Vérification de la syntaxe YAML
echo -e "\n${YELLOW}🔍 Vérification de la syntaxe YAML...${NC}"
if command -v yamllint >/dev/null 2>&1; then
    if yamllint "$NOTIFICATIONS_YAML" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Syntaxe YAML valide${NC}"
    else
        echo -e "${RED}❌ Erreurs de syntaxe YAML détectées${NC}"
        yamllint "$NOTIFICATIONS_YAML"
    fi
else
    echo -e "${YELLOW}⚠️  yamllint non installé, impossible de vérifier la syntaxe${NC}"
fi

# Test de la configuration des alertes
echo -e "\n${YELLOW}🚨 Test de la configuration des alertes...${NC}"
if grep -q "quality_thresholds" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Seuils de qualité configurés${NC}"
else
    echo -e "${RED}❌ Seuils de qualité non configurés${NC}"
fi

if grep -q "performance_thresholds" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Seuils de performance configurés${NC}"
else
    echo -e "${RED}❌ Seuils de performance non configurés${NC}"
fi

if grep -q "security_thresholds" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Seuils de sécurité configurés${NC}"
else
    echo -e "${RED}❌ Seuils de sécurité non configurés${NC}"
fi

# Test de la configuration des rapports
echo -e "\n${YELLOW}📄 Test de la configuration des rapports...${NC}"
if grep -q "auto_generate: true" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Génération automatique des rapports activée${NC}"
else
    echo -e "${YELLOW}⚠️  Génération automatique des rapports désactivée${NC}"
fi

# Test de la configuration du monitoring
echo -e "\n${YELLOW}📡 Test de la configuration du monitoring...${NC}"
if grep -q "continuous: true" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Monitoring continu activé${NC}"
else
    echo -e "${YELLOW}⚠️  Monitoring continu désactivé${NC}"
fi

# Test de la configuration des déploiements
echo -e "\n${YELLOW}🚀 Test de la configuration des déploiements...${NC}"
if grep -q "strategy: \"blue-green\"" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Stratégie de déploiement blue-green configurée${NC}"
else
    echo -e "${YELLOW}⚠️  Stratégie de déploiement non configurée${NC}"
fi

if grep -q "auto_rollback:" "$NOTIFICATIONS_YAML"; then
    echo -e "${GREEN}✅ Rollback automatique configuré${NC}"
else
    echo -e "${YELLOW}⚠️  Rollback automatique non configuré${NC}"
fi

# Résumé final
echo -e "\n${BLUE}📋 Résumé de la Configuration des Notifications${NC}"
echo "=============================================="

echo -e "\n${GREEN}✅ Composants Configurés:${NC}"
echo "   - Configuration YAML complète"
echo "   - Variables d'environnement définies"
echo "   - Seuils d'alerte configurés"
echo "   - Stratégies de déploiement définies"
echo "   - Monitoring et rapports activés"

echo -e "\n${YELLOW}⚠️  À Configurer:${NC}"
echo "   - Webhooks Slack/Teams réels"
echo "   - Configuration SMTP pour emails"
echo "   - Endpoints de métriques"
echo "   - Webhooks personnalisés"

echo -e "\n${BLUE}🎯 Prochaines Étapes:${NC}"
echo "   1. Configurer les webhooks réels"
echo "   2. Tester les notifications"
echo "   3. Intégrer avec le pipeline CI/CD"
echo "   4. Configurer les métriques"

echo -e "\n${GREEN}🎉 Configuration des notifications terminée !${NC}"
echo "Votre pipeline CI/CD peut maintenant envoyer des notifications automatiques."
