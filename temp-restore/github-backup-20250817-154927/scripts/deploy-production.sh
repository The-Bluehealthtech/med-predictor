#!/bin/bash

# =============================================================================
# SCRIPT DE DÉPLOIEMENT PRODUCTION - MICROSERVICE FIT
# =============================================================================

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_message() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Configuration par défaut
DOMAIN=""
EMAIL=""
PRODUCTION_MODE=false
USE_LETSENCRYPT=true
SSL_CERT_PATH=""
SSL_KEY_PATH=""
SSL_CA_PATH=""

# Afficher l'aide
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -d, --domain DOMAIN        Nom de domaine pour le service"
    echo "  -e, --email EMAIL          Email pour Let's Encrypt"
    echo "  -p, --production           Mode production (désactive les logs de debug)"
    echo "  -c, --cert PATH            Chemin vers le certificat SSL personnalisé"
    echo "  -k, --key PATH             Chemin vers la clé privée SSL"
    echo "  -a, --ca PATH              Chemin vers le bundle CA"
    echo "  -h, --help                 Afficher cette aide"
    echo ""
    echo "Exemples:"
    echo "  $0 -d api.fit-service.com -e admin@fit-service.com -p"
    echo "  $0 -d api.fit-service.com -c /path/to/cert.pem -k /path/to/key.pem"
}

# Parser les arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -d|--domain)
            DOMAIN="$2"
            shift 2
            ;;
        -e|--email)
            EMAIL="$2"
            shift 2
            ;;
        -p|--production)
            PRODUCTION_MODE=true
            shift
            ;;
        -c|--cert)
            SSL_CERT_PATH="$2"
            USE_LETSENCRYPT=false
            shift 2
            ;;
        -k|--key)
            SSL_KEY_PATH="$2"
            USE_LETSENCRYPT=false
            shift 2
            ;;
        -a|--ca)
            SSL_CA_PATH="$2"
            USE_LETSENCRYPT=false
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        *)
            print_error "Option inconnue: $1"
            show_help
            exit 1
            ;;
    esac
done

# Vérification des prérequis
check_prerequisites() {
    print_message "Vérification des prérequis pour le déploiement..."
    
    # Vérifier Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js n'est pas installé"
        exit 1
    fi
    
    # Vérifier npm
    if ! command -v npm &> /dev/null; then
        print_error "npm n'est pas installé"
        exit 1
    fi
    
    # Vérifier PM2 pour la production
    if ! command -v pm2 &> /dev/null; then
        print_warning "PM2 non détecté - installation..."
        npm install -g pm2
    fi
    
    # Vérifier nginx
    if ! command -v nginx &> /dev/null; then
        print_warning "Nginx non détecté - installation requise"
        print_message "Installer Nginx: sudo apt-get install nginx (Ubuntu/Debian)"
        print_message "Ou: brew install nginx (macOS)"
    fi
    
    # Vérifier certbot pour Let's Encrypt
    if [ "$USE_LETSENCRYPT" = true ] && ! command -v certbot &> /dev/null; then
        print_warning "Certbot non détecté - installation requise pour Let's Encrypt"
        print_message "Installer Certbot: sudo apt-get install certbot (Ubuntu/Debian)"
        print_message "Ou: brew install certbot (macOS)"
    fi
    
    print_success "Prérequis vérifiés"
}

# Configuration de l'environnement de production
setup_production_environment() {
    print_message "Configuration de l'environnement de production..."
    
    # Créer le fichier .env.production
    if [ ! -f ".env.production" ]; then
        cp .env .env.production
        print_success "Fichier .env.production créé"
    fi
    
    # Mettre à jour les variables de production
    sed -i.bak 's/NODE_ENV=development/NODE_ENV=production/' .env.production
    sed -i.bak 's/LOG_LEVEL=info/LOG_LEVEL=warn/' .env.production
    sed -i.bak 's/SSL_ENABLED=false/SSL_ENABLED=true/' .env.production
    
    if [ -n "$DOMAIN" ]; then
        sed -i.bak "s|CORS_ORIGIN=.*|CORS_ORIGIN=https://$DOMAIN|" .env.production
        sed -i.bak "s|FIT_DASHBOARD_URL=.*|FIT_DASHBOARD_URL=https://$DOMAIN|" .env.production
    fi
    
    print_success "Environnement de production configuré"
}

# Configuration SSL/TLS
setup_ssl() {
    print_message "Configuration SSL/TLS..."
    
    if [ "$USE_LETSENCRYPT" = true ]; then
        setup_letsencrypt
    else
        setup_custom_ssl
    fi
}

# Configuration Let's Encrypt
setup_letsencrypt() {
    if [ -z "$DOMAIN" ] || [ -z "$EMAIL" ]; then
        print_error "Domaine et email requis pour Let's Encrypt"
        exit 1
    fi
    
    print_message "Configuration Let's Encrypt pour $DOMAIN..."
    
    # Créer le dossier pour les certificats
    sudo mkdir -p /etc/ssl/fit-service
    
    # Obtenir le certificat
    sudo certbot certonly --standalone \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        -d "$DOMAIN"
    
    # Copier les certificats
    sudo cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem /etc/ssl/fit-service/certificate.pem
    sudo cp /etc/letsencrypt/live/$DOMAIN/privkey.pem /etc/ssl/fit-service/private-key.pem
    sudo cp /etc/letsencrypt/live/$DOMAIN/chain.pem /etc/ssl/fit-service/ca-bundle.pem
    
    # Définir les permissions
    sudo chown -R $USER:$USER /etc/ssl/fit-service
    sudo chmod 600 /etc/ssl/fit-service/private-key.pem
    sudo chmod 644 /etc/ssl/fit-service/certificate.pem
    sudo chmod 644 /etc/ssl/fit-service/ca-bundle.pem
    
    # Mettre à jour .env.production
    sed -i.bak "s|SSL_KEY_PATH=.*|SSL_KEY_PATH=/etc/ssl/fit-service/private-key.pem|" .env.production
    sed -i.bak "s|SSL_CERT_PATH=.*|SSL_CERT_PATH=/etc/ssl/fit-service/certificate.pem|" .env.production
    sed -i.bak "s|SSL_CA_PATH=.*|SSL_CA_PATH=/etc/ssl/fit-service/ca-bundle.pem|" .env.production
    
    # Configurer le renouvellement automatique
    setup_ssl_renewal
    
    print_success "SSL Let's Encrypt configuré"
}

# Configuration SSL personnalisé
setup_custom_ssl() {
    if [ -z "$SSL_CERT_PATH" ] || [ -z "$SSL_KEY_PATH" ]; then
        print_error "Certificat et clé SSL requis"
        exit 1
    fi
    
    print_message "Configuration SSL personnalisé..."
    
    # Vérifier que les fichiers existent
    if [ ! -f "$SSL_CERT_PATH" ]; then
        print_error "Certificat SSL non trouvé: $SSL_CERT_PATH"
        exit 1
    fi
    
    if [ ! -f "$SSL_KEY_PATH" ]; then
        print_error "Clé privée SSL non trouvée: $SSL_KEY_PATH"
        exit 1
    fi
    
    # Créer le dossier pour les certificats
    sudo mkdir -p /etc/ssl/fit-service
    
    # Copier les certificats
    sudo cp "$SSL_CERT_PATH" /etc/ssl/fit-service/certificate.pem
    sudo cp "$SSL_KEY_PATH" /etc/ssl/fit-service/private-key.pem
    
    if [ -n "$SSL_CA_PATH" ]; then
        sudo cp "$SSL_CA_PATH" /etc/ssl/fit-service/ca-bundle.pem
    fi
    
    # Définir les permissions
    sudo chown -R $USER:$USER /etc/ssl/fit-service
    sudo chmod 600 /etc/ssl/fit-service/private-key.pem
    sudo chmod 644 /etc/ssl/fit-service/certificate.pem
    
    if [ -f "/etc/ssl/fit-service/ca-bundle.pem" ]; then
        sudo chmod 644 /etc/ssl/fit-service/ca-bundle.pem
    fi
    
    # Mettre à jour .env.production
    sed -i.bak "s|SSL_KEY_PATH=.*|SSL_KEY_PATH=/etc/ssl/fit-service/private-key.pem|" .env.production
    sed -i.bak "s|SSL_CERT_PATH=.*|SSL_CERT_PATH=/etc/ssl/fit-service/certificate.pem|" .env.production
    
    if [ -n "$SSL_CA_PATH" ]; then
        sed -i.bak "s|SSL_CA_PATH=.*|SSL_CA_PATH=/etc/ssl/fit-service/ca-bundle.pem|" .env.production
    fi
    
    print_success "SSL personnalisé configuré"
}

# Configuration du renouvellement SSL
setup_ssl_renewal() {
    print_message "Configuration du renouvellement automatique SSL..."
    
    # Créer le script de renouvellement
    cat > scripts/renew-ssl.sh << 'EOF'
#!/bin/bash

# Script de renouvellement SSL pour FIT Service

echo "Renouvellement des certificats SSL..."

# Renouveler les certificats Let's Encrypt
sudo certbot renew --quiet

# Redémarrer le service FIT
pm2 restart fit-service

# Redémarrer Nginx
sudo systemctl reload nginx

echo "Renouvellement SSL terminé"
EOF
    
    chmod +x scripts/renew-ssl.sh
    
    # Ajouter au cron (renouvellement tous les jours à 2h du matin)
    (crontab -l 2>/dev/null; echo "0 2 * * * /path/to/fit-service/scripts/renew-ssl.sh") | crontab -
    
    print_success "Renouvellement SSL configuré"
}

# Configuration Nginx
setup_nginx() {
    if [ -z "$DOMAIN" ]; then
        print_warning "Domaine non spécifié - configuration Nginx ignorée"
        return
    fi
    
    print_message "Configuration Nginx..."
    
    # Créer la configuration Nginx
    cat > nginx-fit-service.conf << EOF
server {
    listen 80;
    server_name $DOMAIN;
    
    # Redirection vers HTTPS
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name $DOMAIN;
    
    # Configuration SSL
    ssl_certificate /etc/ssl/fit-service/certificate.pem;
    ssl_certificate_key /etc/ssl/fit-service/private-key.pem;
    
    # Configuration SSL sécurisée
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Headers de sécurité
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Proxy vers le service FIT
    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }
    
    # Endpoint de santé
    location /health {
        proxy_pass http://localhost:3000/health;
        access_log off;
    }
    
    # Logs
    access_log /var/log/nginx/fit-service-access.log;
    error_log /var/log/nginx/fit-service-error.log;
}
EOF
    
    # Installer la configuration Nginx
    if command -v nginx &> /dev/null; then
        sudo cp nginx-fit-service.conf /etc/nginx/sites-available/fit-service
        sudo ln -sf /etc/nginx/sites-available/fit-service /etc/nginx/sites-enabled/
        sudo nginx -t
        sudo systemctl reload nginx
        
        print_success "Configuration Nginx installée"
    else
        print_warning "Nginx non installé - configuration sauvegardée dans nginx-fit-service.conf"
    fi
}

# Configuration PM2
setup_pm2() {
    print_message "Configuration PM2..."
    
    # Créer le fichier de configuration PM2
    cat > ecosystem.config.js << 'EOF'
module.exports = {
  apps: [{
    name: 'fit-service',
    script: 'src/app.js',
    instances: 'max',
    exec_mode: 'cluster',
    env: {
      NODE_ENV: 'development'
    },
    env_production: {
      NODE_ENV: 'production',
      PORT: 3000
    },
    error_file: './logs/pm2-error.log',
    out_file: './logs/pm2-out.log',
    log_file: './logs/pm2-combined.log',
    time: true,
    max_memory_restart: '1G',
    node_args: '--max-old-space-size=1024',
    watch: false,
    ignore_watch: ['node_modules', 'logs', 'backups'],
    restart_delay: 4000,
    max_restarts: 10,
    min_uptime: '10s'
  }]
};
EOF
    
    # Installer les dépendances de production
    npm ci --only=production
    
    # Démarrer avec PM2
    pm2 start ecosystem.config.js --env production
    
    # Sauvegarder la configuration PM2
    pm2 save
    
    # Configurer le démarrage automatique
    pm2 startup
    
    print_success "PM2 configuré et service démarré"
}

# Configuration du monitoring
setup_monitoring() {
    print_message "Configuration du monitoring..."
    
    # Créer le script de monitoring
    cat > scripts/monitor-production.sh << 'EOF'
#!/bin/bash

# Script de monitoring pour la production

LOG_FILE="logs/monitoring.log"
ALERT_EMAIL="admin@fit-service.com"

# Fonction de log
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# Vérifier le service FIT
check_fit_service() {
    if ! curl -f -s https://localhost/health > /dev/null; then
        log "ERREUR: Service FIT non accessible"
        echo "Service FIT non accessible" | mail -s "Alerte FIT Service" "$ALERT_EMAIL"
        pm2 restart fit-service
        return 1
    fi
    log "Service FIT: OK"
    return 0
}

# Vérifier l'utilisation mémoire
check_memory() {
    MEMORY_USAGE=$(pm2 show fit-service | grep "memory" | awk '{print $4}' | sed 's/MB//')
    if [ "$MEMORY_USAGE" -gt 800 ]; then
        log "ALERTE: Utilisation mémoire élevée: ${MEMORY_USAGE}MB"
        echo "Utilisation mémoire élevée: ${MEMORY_USAGE}MB" | mail -s "Alerte Mémoire FIT Service" "$ALERT_EMAIL"
    fi
}

# Vérifier les certificats SSL
check_ssl() {
    if [ -f "/etc/ssl/fit-service/certificate.pem" ]; then
        EXPIRY=$(openssl x509 -enddate -noout -in /etc/ssl/fit-service/certificate.pem | cut -d= -f2)
        EXPIRY_DATE=$(date -d "$EXPIRY" +%s)
        CURRENT_DATE=$(date +%s)
        DAYS_LEFT=$(( (EXPIRY_DATE - CURRENT_DATE) / 86400 ))
        
        if [ "$DAYS_LEFT" -lt 30 ]; then
            log "ALERTE: Certificat SSL expire dans $DAYS_LEFT jours"
            echo "Certificat SSL expire dans $DAYS_LEFT jours" | mail -s "Alerte SSL FIT Service" "$ALERT_EMAIL"
        fi
    fi
}

# Exécuter les vérifications
check_fit_service
check_memory
check_ssl

log "Monitoring terminé"
EOF
    
    chmod +x scripts/monitor-production.sh
    
    # Ajouter au cron (vérification toutes les 5 minutes)
    (crontab -l 2>/dev/null; echo "*/5 * * * * /path/to/fit-service/scripts/monitor-production.sh") | crontab -
    
    print_success "Monitoring configuré"
}

# Configuration des sauvegardes
setup_backups() {
    print_message "Configuration des sauvegardes..."
    
    # Créer le script de sauvegarde
    cat > scripts/backup.sh << 'EOF'
#!/bin/bash

# Script de sauvegarde pour FIT Service

BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="fit-backup-$DATE"

# Créer le dossier de sauvegarde
mkdir -p "$BACKUP_DIR"

# Sauvegarder MongoDB
if command -v mongodump &> /dev/null; then
    mongodump --uri="mongodb://localhost:27017/fit_database" --out="$BACKUP_DIR/$BACKUP_NAME-mongo"
fi

# Sauvegarder PostgreSQL
if command -v pg_dump &> /dev/null; then
    PGPASSWORD=fit_password pg_dump -h localhost -U fit_user fit_database > "$BACKUP_DIR/$BACKUP_NAME-postgres.sql"
fi

# Sauvegarder Redis
if command -v redis-cli &> /dev/null; then
    redis-cli --rdb "$BACKUP_DIR/$BACKUP_NAME-redis.rdb"
fi

# Sauvegarder les fichiers de configuration
tar -czf "$BACKUP_DIR/$BACKUP_NAME-config.tar.gz" .env.production ecosystem.config.js

# Nettoyer les anciennes sauvegardes (garder 30 jours)
find "$BACKUP_DIR" -name "fit-backup-*" -mtime +30 -delete

echo "Sauvegarde terminée: $BACKUP_NAME"
EOF
    
    chmod +x scripts/backup.sh
    
    # Ajouter au cron (sauvegarde quotidienne à 2h du matin)
    (crontab -l 2>/dev/null; echo "0 2 * * * /path/to/fit-service/scripts/backup.sh") | crontab -
    
    print_success "Sauvegardes configurées"
}

# Vérification finale
final_checks() {
    print_message "Vérification finale du déploiement..."
    
    # Vérifier que le service fonctionne
    sleep 5
    if curl -f -s http://localhost:3000/health > /dev/null; then
        print_success "Service FIT accessible localement"
    else
        print_error "Service FIT non accessible localement"
        exit 1
    fi
    
    # Vérifier SSL si configuré
    if [ "$USE_LETSENCRYPT" = true ] && [ -n "$DOMAIN" ]; then
        if curl -f -s https://$DOMAIN/health > /dev/null; then
            print_success "Service FIT accessible via HTTPS"
        else
            print_warning "Service FIT non accessible via HTTPS"
        fi
    fi
    
    # Afficher les informations de déploiement
    echo ""
    echo "============================================================================="
    print_success "DÉPLOIEMENT PRODUCTION TERMINÉ!"
    echo "============================================================================="
    echo ""
    echo "Informations du déploiement:"
    echo "  - Service: http://localhost:3000"
    if [ -n "$DOMAIN" ]; then
        echo "  - Domaine: https://$DOMAIN"
    fi
    echo "  - PM2: pm2 status"
    echo "  - Logs: pm2 logs fit-service"
    echo "  - Monitoring: scripts/monitor-production.sh"
    echo "  - Sauvegardes: scripts/backup.sh"
    echo ""
    echo "Commandes utiles:"
    echo "  pm2 restart fit-service    # Redémarrer le service"
    echo "  pm2 stop fit-service       # Arrêter le service"
    echo "  pm2 logs fit-service       # Voir les logs"
    echo "  pm2 monit                  # Monitorer en temps réel"
    echo ""
}

# Fonction principale
main() {
    echo "============================================================================="
    echo "DÉPLOIEMENT PRODUCTION - MICROSERVICE FIT"
    echo "============================================================================="
    echo ""
    
    check_prerequisites
    setup_production_environment
    setup_ssl
    setup_nginx
    setup_pm2
    setup_monitoring
    setup_backups
    final_checks
}

# Exécuter le script principal
main "$@" 