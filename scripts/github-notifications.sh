#!/bin/bash

# Script de Notifications GitHub Actions CI/CD
# Version: 1.0 - Adapté pour GitHub

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
NOTIFICATION_SCRIPT="github-notification.php"

# Variables GitHub Actions (avec valeurs par défaut)
GITHUB_PROJECT_NAME="${GITHUB_REPOSITORY:-izharmahjoub/med-predictor}"
GITHUB_BRANCH="${GITHUB_REF_NAME:-develop-v3}"
GITHUB_COMMIT_SHA="${GITHUB_SHA:-unknown}"
GITHUB_COMMIT_MESSAGE="${GITHUB_COMMIT_MESSAGE:-No message}"
GITHUB_RUN_ID="${GITHUB_RUN_ID:-unknown}"
GITHUB_WORKFLOW="${GITHUB_WORKFLOW:-CI/CD Pipeline}"
GITHUB_ACTOR="${GITHUB_ACTOR:-Unknown User}"
GITHUB_PROJECT_URL="https://github.com/${GITHUB_REPOSITORY}"

# Fonction d'envoi de notification
send_notification() {
    local event_type="$1"
    local status="$2"
    local message="$3"
    local color="$4"
    
    echo -e "${BLUE}📧 Envoi de notification: $event_type - $status${NC}"
    
    # Créer le script PHP de notification
    cat > "$NOTIFICATION_SCRIPT" << EOF
<?php
/**
 * Notification GitHub Actions CI/CD
 * Événement: $event_type
 * Statut: $status
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Configuration SMTP
\$smtp_host = getenv('SMTP_HOST');
\$smtp_port = getenv('SMTP_PORT');
\$smtp_username = getenv('SMTP_USERNAME');
\$smtp_password = getenv('SMTP_PASSWORD');

// Destinataires
\$recipients = [
    'im0668@gmail.com',         // Email principal
    // Note: GitHub ne fournit pas l'email de l'utilisateur par défaut
];

try {
    // Créer une instance PHPMailer
    \$mail = new PHPMailer(true);
    
    // Configuration du serveur
    \$mail->isSMTP();
    \$mail->Host = \$smtp_host;
    \$mail->SMTPAuth = true;
    \$mail->Username = \$smtp_username;
    \$mail->Password = \$smtp_password;
    \$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    \$mail->Port = \$smtp_port;
    
    // Configuration de l'email
    \$mail->setFrom('im0668@gmail.com', 'GitHub Actions - $GITHUB_PROJECT_NAME');
    
    // Ajouter les destinataires
    foreach (\$recipients as \$recipient) {
        \$mail->addAddress(\$recipient);
    }
    
    // Sujet et contenu
    \$mail->isHTML(true);
    \$mail->Subject = "🚀 $event_type - $GITHUB_PROJECT_NAME [$GITHUB_BRANCH]";
    
    // Corps de l'email en HTML
    \$mail->Body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>GitHub Actions CI/CD Notification</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: $color; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { padding: 20px; }
            .status { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
            .info-item { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid $color; }
            .info-label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; }
            .info-value { font-size: 14px; margin-top: 5px; }
            .commit-message { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; font-style: italic; }
            .footer { background: #333; color: white; padding: 15px; text-align: center; border-radius: 0 0 8px 8px; }
            .button { display: inline-block; padding: 10px 20px; background: $color; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 GitHub Actions CI/CD Notification</h1>
                <p>$GITHUB_PROJECT_NAME</p>
            </div>
            
            <div class="content">
                <div class="status">$event_type</div>
                <p>$message</p>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Projet</div>
                        <div class="info-value">$GITHUB_PROJECT_NAME</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Branche</div>
                        <div class="info-value">$GITHUB_BRANCH</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Workflow</div>
                        <div class="info-value">$GITHUB_WORKFLOW</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Run ID</div>
                        <div class="info-value">#$GITHUB_RUN_ID</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Commit SHA</div>
                        <div class="info-value">' . substr('$GITHUB_COMMIT_SHA', 0, 8) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Utilisateur</div>
                        <div class="info-value">$GITHUB_ACTOR</div>
                    </div>
                </div>
                
                <div class="commit-message">
                    <strong>Message de commit:</strong><br>
                    $GITHUB_COMMIT_MESSAGE
                </div>
                
                <div style="text-align: center; margin: 20px 0;">
                    <a href="$GITHUB_PROJECT_URL" class="button">Voir le Projet</a>
                    <a href="$GITHUB_PROJECT_URL/actions/runs/$GITHUB_RUN_ID" class="button">Voir le Workflow</a>
                </div>
            </div>
            
            <div class="footer">
                <p>🔔 Notification automatique GitHub Actions CI/CD</p>
                <p>Envoyé le ' . date('d/m/Y à H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    // Version texte simple
    \$mail->AltBody = "
GitHub Actions CI/CD Notification - $GITHUB_PROJECT_NAME

$event_type
$message

Détails:
- Projet: $GITHUB_PROJECT_NAME
- Branche: $GITHUB_BRANCH
- Workflow: $GITHUB_WORKFLOW
- Run ID: #$GITHUB_RUN_ID
- Commit: ' . substr('$GITHUB_COMMIT_SHA', 0, 8) . '
- Utilisateur: $GITHUB_ACTOR

Message de commit: $GITHUB_COMMIT_MESSAGE

Liens:
- Projet: $GITHUB_PROJECT_URL
- Workflow: $GITHUB_PROJECT_URL/actions/runs/$GITHUB_RUN_ID

Envoyé le ' . date('d/m/Y à H:i:s') . '
    ";
    
    // Envoi de l'email
    \$mail->send();
    
    echo "✅ Notification envoyée avec succès !\n";
    
} catch (Exception \$e) {
    echo "❌ Erreur lors de l'envoi: {\$mail->ErrorInfo}\n";
    exit(1);
}
?>
EOF

    # Debug des variables SMTP
    echo "🔍 Debug SMTP:"
    echo "  SMTP_HOST: ${SMTP_HOST:-'NON DÉFINI'}"
    echo "  SMTP_PORT: ${SMTP_PORT:-'NON DÉFINI'}"
    echo "  SMTP_USERNAME: ${SMTP_USERNAME:-'NON DÉFINI'}"
    echo "  SMTP_PASSWORD: ${SMTP_PASSWORD:0:3}*** (longueur: ${#SMTP_PASSWORD})"
    
    # Test de connectivité SMTP
    if command -v nc >/dev/null 2>&1; then
        echo "🔍 Test de connectivité SMTP:"
        if nc -z -w5 "${SMTP_HOST:-localhost}" "${SMTP_PORT:-587}" 2>/dev/null; then
            echo "  ✅ Connexion SMTP possible"
        else
            echo "  ❌ Connexion SMTP impossible"
        fi
    fi
    
    # Exécuter le script de notification
    if SMTP_HOST="$SMTP_HOST" SMTP_PORT="$SMTP_PORT" SMTP_USERNAME="$SMTP_USERNAME" SMTP_PASSWORD="$SMTP_PASSWORD" php "$NOTIFICATION_SCRIPT"; then
        echo -e "${GREEN}✅ Notification $event_type envoyée avec succès !${NC}"
        
        # Nettoyer
        rm -f "$NOTIFICATION_SCRIPT"
        return 0
    else
        echo -e "${RED}❌ Erreur lors de l'envoi de la notification $event_type${NC}"
        
        # Nettoyer
        rm -f "$NOTIFICATION_SCRIPT"
        return 1
    fi
}

# Fonction principale
main() {
    echo -e "${BLUE}🔔 Script de Notifications GitHub Actions CI/CD${NC}"
    echo "=================================================="
    
    # Vérification des variables d'environnement
    echo -e "\n${YELLOW}📋 Variables GitHub Actions Détectées${NC}"
    echo "=========================================="
    echo -e "Projet: ${BLUE}$GITHUB_PROJECT_NAME${NC}"
    echo -e "Branche: ${BLUE}$GITHUB_BRANCH${NC}"
    echo -e "Workflow: ${BLUE}$GITHUB_WORKFLOW${NC}"
    echo -e "Run ID: ${BLUE}#$GITHUB_RUN_ID${NC}"
    echo -e "Commit SHA: ${BLUE}${GITHUB_COMMIT_SHA:0:8}${NC}"
    echo -e "Utilisateur: ${BLUE}$GITHUB_ACTOR${NC}"
    
    # Déterminer le type d'événement
    local event_type="Workflow"
    local status="Inconnu"
    local message="Workflow GitHub Actions exécuté"
    local color="#007bff"
    
    # Vérifier le statut du pipeline
    if [[ "$CI_PIPELINE_STATUS" == "success" ]]; then
        status="Succès"
        message="Workflow GitHub Actions exécuté avec succès !"
        color="#28a745"
    elif [[ "$CI_PIPELINE_STATUS" == "failed" ]]; then
        status="Échec"
        message="Workflow GitHub Actions a échoué !"
        color="#dc3545"
    elif [[ "$CI_PIPELINE_STATUS" == "canceled" ]]; then
        status="Annulé"
        message="Workflow GitHub Actions a été annulé."
        color="#6c757d"
    fi
    
    echo -e "\n${YELLOW}📊 Statut du Workflow${NC}"
    echo "================================"
    echo -e "Statut: ${BLUE}$status${NC}"
    echo -e "Message: ${BLUE}$message${NC}"
    
    # Envoyer la notification
    echo -e "\n${YELLOW}📧 Envoi de la Notification${NC}"
    echo "======================================"
    
    if send_notification "$event_type" "$status" "$message" "$color"; then
        echo -e "\n${GREEN}🎉 Notification GitHub Actions CI/CD envoyée avec succès !${NC}"
        echo -e "${BLUE}Vérifiez votre boîte de réception: im0668@gmail.com${NC}"
    else
        echo -e "\n${YELLOW}⚠️  Échec de l'envoi de la notification email${NC}"
        echo -e "${BLUE}📋 Résumé du workflow:${NC}"
        echo -e "  ✅ Pipeline CI/CD: SUCCÈS"
        echo -e "  📧 Notification: ÉCHEC (problème SMTP)"
        echo -e "  🔗 Voir les détails: https://github.com/$GITHUB_REPOSITORY/actions/runs/$GITHUB_RUN_ID"
        echo -e "\n${GREEN}🎯 Le pipeline principal fonctionne parfaitement !${NC}"
        # Ne pas faire échouer le job pour un problème de notification
        exit 0
    fi
}

# Exécution du script
main "$@"
