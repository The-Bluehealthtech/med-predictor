#!/bin/bash

# Script de Test des Notifications Email CI/CD
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
TEST_EMAIL_FILE="test-email.php"

echo -e "${BLUE}📧 Test des Notifications Email CI/CD${NC}"
echo "=========================================="

# Vérification du fichier de configuration
if [[ ! -f "$CONFIG_FILE" ]]; then
    echo -e "${RED}❌ Fichier $CONFIG_FILE non trouvé${NC}"
    exit 1
fi

# Chargement des variables
source "$CONFIG_FILE"

echo -e "\n${YELLOW}📋 Configuration Email Détectée${NC}"
echo "================================="
echo -e "Host SMTP: ${BLUE}$SMTP_HOST${NC}"
echo -e "Port SMTP: ${BLUE}$SMTP_PORT${NC}"
echo -e "Utilisateur: ${BLUE}$SMTP_USERNAME${NC}"
echo -e "Mot de passe: ${BLUE}${SMTP_PASSWORD:0:4}****${NC}"

# Demande de l'email de test
echo -e "\n${CYAN}📬 Configuration du Test${NC}"
echo "=============================="
read -p "Email de destination pour le test (laissez vide pour utiliser $SMTP_USERNAME): " TEST_EMAIL

if [[ -z "$TEST_EMAIL" ]]; then
    TEST_EMAIL="$SMTP_USERNAME"
fi

echo -e "${GREEN}✅ Email de test: $TEST_EMAIL${NC}"

# Création du script PHP de test
echo -e "\n${YELLOW}🔧 Création du Script de Test Email${NC}"
echo "=========================================="

cat > "$TEST_EMAIL_FILE" << 'EOF'
<?php
/**
 * Test d'envoi d'email CI/CD avec PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Configuration depuis les variables d'environnement
$smtp_host = getenv('SMTP_HOST');
$smtp_port = getenv('SMTP_PORT');
$smtp_username = getenv('SMTP_USERNAME');
$smtp_password = getenv('SMTP_PASSWORD');
$test_email = getenv('TEST_EMAIL');

echo "🧪 Test d'envoi d'email CI/CD\n";
echo "==============================\n\n";

echo "📋 Configuration SMTP:\n";
echo "   Host: $smtp_host\n";
echo "   Port: $smtp_port\n";
echo "   Username: $smtp_username\n";
echo "   Password: " . substr($smtp_password, 0, 4) . "****\n";
echo "   Test Email: $test_email\n\n";

try {
    // Créer une instance PHPMailer
    $mail = new PHPMailer(true);
    
    // Configuration du serveur
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;
    
    // Configuration de l'email
    $mail->setFrom($smtp_username, 'CI/CD System - Med-Predictor');
    $mail->addAddress($test_email, 'Test Recipient');
    
    // Sujet et contenu
    $mail->isHTML(true);
    $mail->Subject = '🧪 Test Notification Email CI/CD - Med-Predictor';
    
    // Corps de l'email en HTML
    $mail->Body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Test CI/CD</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { background: #4CAF50; color: white; padding: 20px; border-radius: 5px; }
            .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .footer { background: #333; color: white; padding: 10px; text-align: center; border-radius: 5px; }
            .success { color: #4CAF50; font-weight: bold; }
            .info { color: #2196F3; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🧪 Test Notification Email CI/CD</h1>
            <p>Projet: Med-Predictor</p>
        </div>
        
        <div class="content">
            <h2>✅ Test Réussi !</h2>
            <p>Ceci est un email de test pour vérifier que votre système de notifications CI/CD fonctionne correctement.</p>
            
            <h3>📊 Détails du Test</h3>
            <ul>
                <li><span class="success">Date:</span> ' . date('Y-m-d H:i:s') . '</li>
                <li><span class="success">Projet:</span> Med-Predictor</li>
                <li><span class="success">Environnement:</span> ' . getenv('DEPLOY_ENVIRONMENT') . '</li>
                <li><span class="success">Type:</span> Test de notification</li>
            </ul>
            
            <h3>🔧 Configuration Testée</h3>
            <ul>
                <li><span class="info">SMTP Host:</span> ' . $smtp_host . '</li>
                <li><span class="info">SMTP Port:</span> ' . $smtp_port . '</li>
                <li><span class="info">Authentification:</span> Gmail 2FA</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>🚀 Votre pipeline CI/CD est maintenant prêt à envoyer des notifications automatiques !</p>
        </div>
    </body>
    </html>';
    
    // Version texte simple
    $mail->AltBody = '
    Test Notification Email CI/CD - Med-Predictor
    
    ✅ Test Réussi !
    
    Ceci est un email de test pour vérifier que votre système de notifications CI/CD fonctionne correctement.
    
    📊 Détails du Test:
    - Date: ' . date('Y-m-d H:i:s') . '
    - Projet: Med-Predictor
    - Environnement: ' . getenv('DEPLOY_ENVIRONMENT') . '
    - Type: Test de notification
    
    🚀 Votre pipeline CI/CD est maintenant prêt à envoyer des notifications automatiques !
    ';
    
    // Envoi de l'email
    echo "📤 Envoi de l'email de test...\n";
    $mail->send();
    
    echo "✅ Email envoyé avec succès !\n";
    echo "📧 Vérifiez votre boîte de réception: $test_email\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de l'envoi: {$mail->ErrorInfo}\n";
    exit(1);
}

echo "\n🎉 Test d'email terminé avec succès !\n";
echo "Votre système de notifications email CI/CD fonctionne parfaitement.\n";
?>
EOF

echo -e "${GREEN}✅ Script de test créé: $TEST_EMAIL_FILE${NC}"

# Test de la configuration SMTP
echo -e "\n${YELLOW}🔍 Test de la Configuration SMTP${NC}"
echo "======================================"

# Test de connectivité
echo -e "${BLUE}Test de connectivité à $SMTP_HOST:$SMTP_PORT...${NC}"
if nc -z -w 5 "$SMTP_HOST" "$SMTP_PORT" 2>/dev/null; then
    echo -e "${GREEN}✅ Port $SMTP_PORT accessible sur $SMTP_HOST${NC}"
else
    echo -e "${RED}❌ Port $SMTP_PORT non accessible sur $SMTP_HOST${NC}"
    exit 1
fi

# Exécution du test d'email
echo -e "\n${YELLOW}📧 Exécution du Test d'Email${NC}"
echo "================================"

echo -e "${BLUE}Envoi de l'email de test...${NC}"
if SMTP_HOST="$SMTP_HOST" SMTP_PORT="$SMTP_PORT" SMTP_USERNAME="$SMTP_USERNAME" SMTP_PASSWORD="$SMTP_PASSWORD" TEST_EMAIL="$TEST_EMAIL" DEPLOY_ENVIRONMENT="$DEPLOY_ENVIRONMENT" php "$TEST_EMAIL_FILE"; then
    echo -e "\n${GREEN}🎉 Test d'email réussi !${NC}"
    
    # Nettoyage
    rm -f "$TEST_EMAIL_FILE"
    
    # Résumé
    echo -e "\n${BLUE}📊 Résumé du Test Email${NC}"
    echo "================================"
    echo -e "${GREEN}✅ Configuration SMTP: Fonctionnelle${NC}"
    echo -e "${GREEN}✅ Connexion réseau: Ouverte${NC}"
    echo -e "${GREEN}✅ Authentification: Réussie${NC}"
    echo -e "${GREEN}✅ Envoi d'email: Réussi${NC}"
    
    echo -e "\n${YELLOW}📧 Vérifiez votre boîte de réception:${NC}"
    echo "   Email: $TEST_EMAIL"
    echo "   Sujet: 🧪 Test Notification Email CI/CD - Med-Predictor"
    
    echo -e "\n${BLUE}🎯 Prochaines Étapes:${NC}"
    echo "   1. ✅ Email testé et fonctionnel"
    echo "   2. 🔧 Configurez Slack et Teams si souhaité"
    echo "   3. 🚀 Intégrez avec votre pipeline GitLab CI"
    echo "   4. 📊 Testez les notifications automatiques"
    
    echo -e "\n${GREEN}🎉 Votre système de notifications email CI/CD est 100% opérationnel !${NC}"
    
else
    echo -e "\n${RED}❌ Test d'email échoué${NC}"
    echo -e "${YELLOW}Vérifiez votre configuration et réessayez${NC}"
    
    # Nettoyage en cas d'échec
    rm -f "$TEST_EMAIL_FILE"
    exit 1
fi
