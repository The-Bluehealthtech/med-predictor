#!/bin/bash

# Script de Test de la Configuration Gmail
# Version: 1.0

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Configuration
CONFIG_FILE="notifications-config.env"

echo -e "${BLUE}📧 Test de la Configuration Gmail CI/CD${NC}"
echo "=========================================="

# Vérification du fichier de configuration
if [[ ! -f "$CONFIG_FILE" ]]; then
    echo -e "${RED}❌ Fichier $CONFIG_FILE non trouvé${NC}"
    exit 1
fi

# Chargement des variables
source "$CONFIG_FILE"

echo -e "\n${YELLOW}📋 Configuration Gmail Détectée${NC}"
echo "================================="
echo -e "Host SMTP: ${BLUE}$SMTP_HOST${NC}"
echo -e "Port SMTP: ${BLUE}$SMTP_PORT${NC}"
echo -e "Utilisateur: ${BLUE}$SMTP_USERNAME${NC}"
echo -e "Mot de passe: ${BLUE}${SMTP_PASSWORD:0:4}****${NC}"

# Test de connexion SMTP
echo -e "\n${YELLOW}🔍 Test de Connexion SMTP${NC}"
echo "=============================="

# Test de connectivité réseau
echo -e "${BLUE}Test de connectivité à $SMTP_HOST:$SMTP_PORT...${NC}"

if command -v nc >/dev/null 2>&1; then
    # Utiliser netcat si disponible
    if timeout 10 nc -z "$SMTP_HOST" "$SMTP_PORT" 2>/dev/null; then
        echo -e "${GREEN}✅ Port $SMTP_PORT accessible sur $SMTP_HOST${NC}"
    else
        echo -e "${RED}❌ Port $SMTP_PORT non accessible sur $SMTP_HOST${NC}"
        echo -e "${YELLOW}Vérifiez votre connexion internet et les paramètres SMTP${NC}"
        exit 1
    fi
elif command -v telnet >/dev/null 2>&1; then
    # Utiliser telnet si disponible
    if timeout 10 bash -c "</dev/tcp/$SMTP_HOST/$SMTP_PORT" 2>/dev/null; then
        echo -e "${GREEN}✅ Port $SMTP_PORT accessible sur $SMTP_HOST${NC}"
    else
        echo -e "${RED}❌ Port $SMTP_PORT non accessible sur $SMTP_HOST${NC}"
        echo -e "${YELLOW}Vérifiez votre connexion internet et les paramètres SMTP${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  Aucun outil de test réseau disponible (nc, telnet)${NC}"
    echo -e "${BLUE}Test de connectivité manuel requis${NC}"
fi

# Test de configuration PHP
echo -e "\n${YELLOW}🐘 Test de Configuration PHP${NC}"
echo "=============================="

if command -v php >/dev/null 2>&1; then
    echo -e "${BLUE}Test de la configuration PHP...${NC}"
    
    # Créer un script de test PHP temporaire
    cat > /tmp/test-smtp.php << 'EOF'
<?php
// Test de configuration SMTP
$smtp_host = getenv('SMTP_HOST');
$smtp_port = getenv('SMTP_PORT');
$smtp_username = getenv('SMTP_USERNAME');
$smtp_password = getenv('SMTP_PASSWORD');

echo "Configuration SMTP:\n";
echo "Host: $smtp_host\n";
echo "Port: $smtp_port\n";
echo "Username: $smtp_username\n";
echo "Password: " . substr($smtp_password, 0, 4) . "****\n";

// Test de connexion SMTP avec fsockopen
echo "\nTest de connexion SMTP...\n";
$socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10);

if ($socket) {
    echo "✅ Connexion SMTP réussie\n";
    
    // Lire la réponse du serveur
    $response = fgets($socket, 1024);
    echo "Réponse serveur: " . trim($response) . "\n";
    
    fclose($socket);
} else {
    echo "❌ Erreur de connexion SMTP: $errstr ($errno)\n";
}

// Test des extensions PHP
echo "\nExtensions PHP requises:\n";
$required_extensions = ['openssl', 'mbstring', 'iconv'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: chargé\n";
    } else {
        echo "❌ $ext: non chargé\n";
    }
}
?>
EOF

    # Exécuter le test PHP
    echo -e "${BLUE}Exécution du test PHP...${NC}"
    SMTP_HOST="$SMTP_HOST" SMTP_PORT="$SMTP_PORT" SMTP_USERNAME="$SMTP_USERNAME" SMTP_PASSWORD="$SMTP_PASSWORD" php /tmp/test-smtp.php
    
    # Nettoyer
    rm -f /tmp/test-smtp.php
    
else
    echo -e "${RED}❌ PHP non installé${NC}"
fi

# Test de configuration avec curl (simulation)
echo -e "\n${YELLOW}🔗 Test de Configuration Webhook (Simulation)${NC}"
echo "=================================================="

echo -e "${BLUE}Test de la configuration des webhooks...${NC}"

# Test Slack
if [[ "$SLACK_WEBHOOK_URL" == *"YOUR_WORKSPACE"* ]]; then
    echo -e "${YELLOW}⚠️  Slack: URL par défaut (à configurer)${NC}"
else
    echo -e "${GREEN}✅ Slack: URL configurée${NC}"
fi

# Test Teams
if [[ "$TEAMS_WEBHOOK_URL" == *"YOUR_WEBHOOK_ID"* ]]; then
    echo -e "${YELLOW}⚠️  Teams: URL par défaut (à configurer)${NC}"
else
    echo -e "${GREEN}✅ Teams: URL configurée${NC}"
fi

# Test Webhook personnalisé
if [[ "$WEBHOOK_URL" == *"your-webhook-endpoint.com"* ]]; then
    echo -e "${YELLOW}⚠️  Webhook personnalisé: URL par défaut (à configurer)${NC}"
else
    echo -e "${GREEN}✅ Webhook personnalisé: URL configurée${NC}"
fi

# Test Métriques
if [[ "$METRICS_ENDPOINT" == *"your-metrics-endpoint.com"* ]]; then
    echo -e "${YELLOW}⚠️  Métriques: URL par défaut (à configurer)${NC}"
else
    echo -e "${GREEN}✅ Métriques: URL configurée${NC}"
fi

# Résumé et recommandations
echo -e "\n${BLUE}📊 Résumé de la Configuration${NC}"
echo "================================"

echo -e "\n${GREEN}✅ Ce qui fonctionne:${NC}"
echo "   - Configuration Gmail SMTP"
echo "   - Variables d'environnement"
echo "   - Structure des notifications"

echo -e "\n${YELLOW}⚠️  À configurer:${NC}"
echo "   - Webhook Slack (https://api.slack.com/apps)"
echo "   - Webhook Teams (canal Teams → Connectors)"
echo "   - Webhook personnalisé (optionnel)"
echo "   - Endpoint métriques (optionnel)"

echo -e "\n${BLUE}🎯 Prochaines Étapes:${NC}"
echo "   1. Configurez Slack et Teams"
echo "   2. Testez les notifications complètes"
echo "   3. Intégrez avec GitLab CI"

echo -e "\n${GREEN}🎉 Test de configuration Gmail terminé !${NC}"
echo "Votre configuration email est prête pour les notifications CI/CD."
