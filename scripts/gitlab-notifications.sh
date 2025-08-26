#!/bin/bash

# Script de Notifications GitLab CI/CD
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
NOTIFICATION_SCRIPT="gitlab-notification.php"

# Chargement de la configuration
if [[ -f "$CONFIG_FILE" ]]; then
    source "$CONFIG_FILE"
else
    echo -e "${RED}❌ Fichier de configuration non trouvé: $CONFIG_FILE${NC}"
    exit 1
fi

# Variables GitLab CI (avec valeurs par défaut)
GITLAB_PROJECT_NAME="${CI_PROJECT_NAME:-med-predictor}"
GITLAB_BRANCH="${CI_COMMIT_REF_NAME:-develop-v3}"
GITLAB_COMMIT_SHA="${CI_COMMIT_SHA:-unknown}"
GITLAB_COMMIT_MESSAGE="${CI_COMMIT_MESSAGE:-No message}"
GITLAB_PIPELINE_ID="${CI_PIPELINE_ID:-unknown}"
GITLAB_JOB_ID="${CI_JOB_ID:-unknown}"
GITLAB_USER_NAME="${GITLAB_USER_NAME:-Unknown User}"
GITLAB_PROJECT_URL="${CI_PROJECT_URL:-https://gitlab.com/izhar7-group/izhar7-project}"

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
 * Notification GitLab CI/CD
 * Événement: $event_type
 * Statut: $status
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Configuration SMTP
\$smtp_host = '$SMTP_HOST';
\$smtp_port = $SMTP_PORT;
\$smtp_username = '$SMTP_USERNAME';
\$smtp_password = '$SMTP_PASSWORD';

// Destinataires
\$recipients = [
    '$SMTP_USERNAME', // Votre email principal
    // Ajoutez d'autres emails si nécessaire
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
    \$mail->setFrom(\$smtp_username, 'GitLab CI/CD - $GITLAB_PROJECT_NAME');
    
    // Ajouter les destinataires
    foreach (\$recipients as \$recipient) {
        \$mail->addAddress(\$recipient);
    }
    
    // Sujet et contenu
    \$mail->isHTML(true);
    \$mail->Subject = "🚀 $event_type - $GITLAB_PROJECT_NAME [$GITLAB_BRANCH]";
    
    // Corps de l'email en HTML
    \$mail->Body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>GitLab CI/CD Notification</title>
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
                <h1>🚀 GitLab CI/CD Notification</h1>
                <p>$GITLAB_PROJECT_NAME</p>
            </div>
            
            <div class="content">
                <div class="status">$event_type</div>
                <p>$message</p>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Projet</div>
                        <div class="info-value">$GITLAB_PROJECT_NAME</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Branche</div>
                        <div class="info-value">$GITLAB_BRANCH</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pipeline ID</div>
                        <div class="info-value">#$GITLAB_PIPELINE_ID</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Job ID</div>
                        <div class="info-value">#$GITLAB_JOB_ID</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Commit SHA</div>
                        <div class="info-value">' . substr('$GITLAB_COMMIT_SHA', 0, 8) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Utilisateur</div>
                        <div class="info-value">$GITLAB_USER_NAME</div>
                    </div>
                </div>
                
                <div class="commit-message">
                    <strong>Message de commit:</strong><br>
                    $GITLAB_COMMIT_MESSAGE
                </div>
                
                <div style="text-align: center; margin: 20px 0;">
                    <a href="$GITLAB_PROJECT_URL" class="button">Voir le Projet</a>
                    <a href="$GITLAB_PROJECT_URL/-/pipelines/$GITLAB_PIPELINE_ID" class="button">Voir le Pipeline</a>
                </div>
            </div>
            
            <div class="footer">
                <p>🔔 Notification automatique GitLab CI/CD</p>
                <p>Envoyé le ' . date('d/m/Y à H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    // Version texte simple
    \$mail->AltBody = "
GitLab CI/CD Notification - $GITLAB_PROJECT_NAME

$event_type
$message

Détails:
- Projet: $GITLAB_PROJECT_NAME
- Branche: $GITLAB_BRANCH
- Pipeline ID: #$GITLAB_PIPELINE_ID
- Job ID: #$GITLAB_JOB_ID
- Commit: ' . substr('$GITLAB_COMMIT_SHA', 0, 8) . '
- Utilisateur: $GITLAB_USER_NAME

Message de commit: $GITLAB_COMMIT_MESSAGE

Liens:
- Projet: $GITLAB_PROJECT_URL
- Pipeline: $GITLAB_PROJECT_URL/-/pipelines/$GITLAB_PIPELINE_ID

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
    echo -e "${BLUE}🔔 Script de Notifications GitLab CI/CD${NC}"
    echo "=============================================="
    
    # Vérification des variables d'environnement
    echo -e "\n${YELLOW}📋 Variables GitLab CI Détectées${NC}"
    echo "======================================"
    echo -e "Projet: ${BLUE}$GITLAB_PROJECT_NAME${NC}"
    echo -e "Branche: ${BLUE}$GITLAB_BRANCH${NC}"
    echo -e "Pipeline ID: ${BLUE}#$GITLAB_PIPELINE_ID${NC}"
    echo -e "Job ID: ${BLUE}#$GITLAB_JOB_ID${NC}"
    echo -e "Commit SHA: ${BLUE}${GITLAB_COMMIT_SHA:0:8}${NC}"
    
    # Déterminer le type d'événement
    local event_type="Pipeline"
    local status="Inconnu"
    local message="Pipeline GitLab CI/CD exécuté"
    local color="#007bff"
    
    # Vérifier le statut du pipeline
    if [[ "$CI_PIPELINE_STATUS" == "success" ]]; then
        status="Succès"
        message="Pipeline GitLab CI/CD exécuté avec succès !"
        color="#28a745"
    elif [[ "$CI_PIPELINE_STATUS" == "failed" ]]; then
        status="Échec"
        message="Pipeline GitLab CI/CD a échoué !"
        color="#dc3545"
    elif [[ "$CI_PIPELINE_STATUS" == "canceled" ]]; then
        status="Annulé"
        message="Pipeline GitLab CI/CD a été annulé."
        color="#6c757d"
    fi
    
    echo -e "\n${YELLOW}📊 Statut du Pipeline${NC}"
    echo "================================"
    echo -e "Statut: ${BLUE}$status${NC}"
    echo -e "Message: ${BLUE}$message${NC}"
    
    # Envoyer la notification
    echo -e "\n${YELLOW}📧 Envoi de la Notification${NC}"
    echo "======================================"
    
    if send_notification "$event_type" "$status" "$message" "$color"; then
        echo -e "\n${GREEN}🎉 Notification GitLab CI/CD envoyée avec succès !${NC}"
        echo -e "${BLUE}Vérifiez votre boîte de réception: $SMTP_USERNAME${NC}"
    else
        echo -e "\n${RED}❌ Échec de l'envoi de la notification${NC}"
        exit 1
    fi
}

# Exécution du script
main "$@"
