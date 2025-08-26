#!/bin/bash

# Script de Test Manuel des Webhooks
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

echo -e "${BLUE}🧪 Test Manuel des Webhooks CI/CD${NC}"
echo "====================================="

# Vérification du fichier de configuration
if [[ ! -f "$CONFIG_FILE" ]]; then
    echo -e "${RED}❌ Fichier $CONFIG_FILE non trouvé${NC}"
    echo -e "${YELLOW}Exécutez d'abord: ./scripts/setup-webhooks.sh${NC}"
    exit 1
fi

# Chargement des variables
source "$CONFIG_FILE"

# Fonction de test Slack
test_slack() {
    echo -e "\n${PURPLE}📱 Test Slack${NC}"
    echo "=========="
    
    if [[ "$SLACK_WEBHOOK_URL" == *"YOUR_WORKSPACE"* ]]; then
        echo -e "${YELLOW}⚠️  Webhook Slack non configuré${NC}"
        echo "Configurez d'abord le webhook Slack"
        return 1
    fi
    
    echo -e "${BLUE}Envoi d'un message de test...${NC}"
    
    # Message de test Slack
    message='{
        "text": "🧪 Test de notification Slack CI/CD",
        "attachments": [
            {
                "color": "good",
                "title": "Test CI/CD",
                "text": "Ceci est un test de notification Slack pour le pipeline CI/CD",
                "fields": [
                    {
                        "title": "Projet",
                        "value": "med-predictor",
                        "short": true
                    },
                    {
                        "title": "Environnement",
                        "value": "'$DEPLOY_ENVIRONMENT'",
                        "short": true
                    }
                ],
                "footer": "CI/CD Notifications",
                "ts": '$(date +%s)'
            }
        ]
    }'
    
    if curl -s -X POST -H 'Content-type: application/json' --data "$message" "$SLACK_WEBHOOK_URL" > /dev/null; then
        echo -e "${GREEN}✅ Message Slack envoyé avec succès !${NC}"
        echo -e "${YELLOW}Vérifiez le canal #ci-cd-alerts dans Slack${NC}"
        return 0
    else
        echo -e "${RED}❌ Erreur lors de l'envoi du message Slack${NC}"
        return 1
    fi
}

# Fonction de test Teams
test_teams() {
    echo -e "\n${PURPLE}💼 Test Microsoft Teams${NC}"
    echo "========================"
    
    if [[ "$TEAMS_WEBHOOK_URL" == *"YOUR_WEBHOOK_ID"* ]]; then
        echo -e "${YELLOW}⚠️  Webhook Teams non configuré${NC}"
        echo "Configurez d'abord le webhook Teams"
        return 1
    fi
    
    echo -e "${BLUE}Envoi d'un message de test...${NC}"
    
    # Message de test Teams
    message='{
        "@type": "MessageCard",
        "@context": "http://schema.org/extensions",
        "themeColor": "00FF00",
        "summary": "Test CI/CD",
        "sections": [
            {
                "activityTitle": "🧪 Test de notification Teams CI/CD",
                "activitySubtitle": "Projet: med-predictor",
                "activityImage": "https://img.icons8.com/color/48/000000/robot-face.png",
                "facts": [
                    {
                        "name": "Projet",
                        "value": "med-predictor"
                    },
                    {
                        "name": "Environnement",
                        "value": "'$DEPLOY_ENVIRONMENT'"
                    },
                    {
                        "name": "Statut",
                        "value": "Test réussi"
                    }
                ],
                "text": "Ceci est un test de notification Microsoft Teams pour le pipeline CI/CD"
            }
        ]
    }'
    
    if curl -s -X POST -H 'Content-type: application/json' --data "$message" "$TEAMS_WEBHOOK_URL" > /dev/null; then
        echo -e "${GREEN}✅ Message Teams envoyé avec succès !${NC}"
        echo -e "${YELLOW}Vérifiez le canal Teams configuré${NC}"
        return 0
    else
        echo -e "${RED}❌ Erreur lors de l'envoi du message Teams${NC}"
        return 1
    fi
}

# Fonction de test Email
test_email() {
    echo -e "\n${PURPLE}📧 Test Email (Gmail)${NC}"
    echo "========================"
    
    if [[ "$SMTP_USERNAME" == *"your-email@gmail.com"* ]]; then
        echo -e "${YELLOW}⚠️  Configuration email non configurée${NC}"
        echo "Configurez d'abord les paramètres SMTP"
        return 1
    fi
    
    echo -e "${BLUE}Test de la configuration SMTP...${NC}"
    
    # Test de connexion SMTP (sans envoi)
    if command -v telnet >/dev/null 2>&1; then
        echo -e "${BLUE}Test de connexion à $SMTP_HOST:$SMTP_PORT...${NC}"
        if timeout 10 bash -c "</dev/tcp/$SMTP_HOST/$SMTP_PORT" 2>/dev/null; then
            echo -e "${GREEN}✅ Connexion SMTP réussie${NC}"
        else
            echo -e "${RED}❌ Impossible de se connecter à $SMTP_HOST:$SMTP_PORT${NC}"
            return 1
        fi
    else
        echo -e "${YELLOW}⚠️  telnet non disponible, impossible de tester la connexion SMTP${NC}"
    fi
    
    echo -e "${GREEN}✅ Configuration email vérifiée${NC}"
    echo -e "${YELLOW}Pour tester l'envoi réel, utilisez un client email ou configurez un script d'envoi${NC}"
    return 0
}

# Fonction de test Webhook personnalisé
test_custom_webhook() {
    echo -e "\n${PURPLE}🔗 Test Webhook Personnalisé${NC}"
    echo "================================"
    
    if [[ -z "$WEBHOOK_URL" || "$WEBHOOK_URL" == *"your-webhook-endpoint.com"* ]]; then
        echo -e "${YELLOW}⚠️  Webhook personnalisé non configuré${NC}"
        echo "Configurez d'abord le webhook personnalisé ou laissez vide"
        return 1
    fi
    
    echo -e "${BLUE}Envoi d'un message de test...${NC}"
    
    # Message de test webhook personnalisé
    message='{
        "event": "test_notification",
        "timestamp": "'$(date -u +%Y-%m-%dT%H:%M:%SZ)'",
        "project": "med-predictor",
        "environment": "'$DEPLOY_ENVIRONMENT'",
        "message": "Test de notification webhook personnalisé CI/CD",
        "status": "success"
    }'
    
    if curl -s -X POST -H 'Content-type: application/json' --data "$message" "$WEBHOOK_URL" > /dev/null; then
        echo -e "${GREEN}✅ Message webhook personnalisé envoyé avec succès !${NC}"
        return 0
    else
        echo -e "${RED}❌ Erreur lors de l'envoi du message webhook personnalisé${NC}"
        return 1
    fi
}

# Fonction de test des métriques
test_metrics() {
    echo -e "\n${PURPLE}📊 Test Endpoint Métriques${NC}"
    echo "================================"
    
    if [[ -z "$METRICS_ENDPOINT" || "$METRICS_ENDPOINT" == *"your-metrics-endpoint.com"* ]]; then
        echo -e "${YELLOW}⚠️  Endpoint métriques non configuré${NC}"
        echo "Configurez d'abord l'endpoint métriques ou laissez vide"
        return 1
    fi
    
    echo -e "${BLUE}Envoi de métriques de test...${NC}"
    
    # Métriques de test
    metrics='{
        "timestamp": "'$(date -u +%Y-%m-%dT%H:%M:%SZ)'",
        "project": "med-predictor",
        "environment": "'$DEPLOY_ENVIRONMENT'",
        "metrics": {
            "test_coverage": 85,
            "code_quality": 90,
            "performance_score": 95,
            "security_score": 88
        }
    }'
    
    if curl -s -X POST -H 'Content-type: application/json' --data "$metrics" "$METRICS_ENDPOINT" > /dev/null; then
        echo -e "${GREEN}✅ Métriques envoyées avec succès !${NC}"
        return 0
    else
        echo -e "${RED}❌ Erreur lors de l'envoi des métriques${NC}"
        return 1
    fi
}

# Menu principal
show_menu() {
    echo -e "\n${BLUE}🎯 Menu de Test des Webhooks${NC}"
    echo "================================"
    echo "1. Test Slack"
    echo "2. Test Microsoft Teams"
    echo "3. Test Email (Gmail)"
    echo "4. Test Webhook Personnalisé"
    echo "5. Test Endpoint Métriques"
    echo "6. Test de tous les webhooks"
    echo "7. Quitter"
    echo ""
}

# Exécution des tests
run_tests() {
    local success_count=0
    local total_count=0
    
    echo -e "\n${BLUE}🚀 Exécution de tous les tests...${NC}"
    echo "====================================="
    
    # Test Slack
    if test_slack; then
        ((success_count++))
    fi
    ((total_count++))
    
    # Test Teams
    if test_teams; then
        ((success_count++))
    fi
    ((total_count++))
    
    # Test Email
    if test_email; then
        ((success_count++))
    fi
    ((total_count++))
    
    # Test Webhook personnalisé
    if test_custom_webhook; then
        ((success_count++))
    fi
    ((total_count++))
    
    # Test Métriques
    if test_metrics; then
        ((success_count++))
    fi
    ((total_count++))
    
    # Résumé
    echo -e "\n${BLUE}📊 Résumé des Tests${NC}"
    echo "====================="
    echo -e "${GREEN}✅ Tests réussis: $success_count/$total_count${NC}"
    
    if [[ $success_count -eq $total_count ]]; then
        echo -e "${GREEN}🎉 Tous les webhooks fonctionnent parfaitement !${NC}"
    elif [[ $success_count -gt 0 ]]; then
        echo -e "${YELLOW}⚠️  Certains webhooks fonctionnent, d'autres ont des problèmes${NC}"
    else
        echo -e "${RED}❌ Aucun webhook ne fonctionne, vérifiez la configuration${NC}"
    fi
}

# Boucle principale
while true; do
    show_menu
    read -p "Choisissez une option (1-7): " choice
    
    case $choice in
        1)
            test_slack
            ;;
        2)
            test_teams
            ;;
        3)
            test_email
            ;;
        4)
            test_custom_webhook
            ;;
        5)
            test_metrics
            ;;
        6)
            run_tests
            ;;
        7)
            echo -e "${GREEN}👋 Au revoir !${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}❌ Option invalide, choisissez 1-7${NC}"
            ;;
    esac
    
    echo ""
    read -p "Appuyez sur Entrée pour continuer..."
done
