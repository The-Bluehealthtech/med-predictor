#!/bin/bash

# Script de déploiement Google Cloud Platform pour FIT Platform
# Usage: ./scripts/deploy-gcp.sh [staging|production]

set -e

# Configuration
ENVIRONMENT=${1:-staging}
PROJECT_NAME="fit-platform"
PROJECT_ID="${PROJECT_NAME}-${ENVIRONMENT}"
REGION="europe-west1"
SERVICE_NAME="${PROJECT_NAME}-app"
IMAGE_NAME="gcr.io/${PROJECT_ID}/${PROJECT_NAME}"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Déploiement GCP FIT Platform - ${ENVIRONMENT}${NC}"
echo "=================================================="

# Vérification des prérequis
check_prerequisites() {
    echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    if ! command -v gcloud &> /dev/null; then
        echo -e "${RED}❌ gcloud CLI n'est pas installé${NC}"
        echo "Installez gcloud: https://cloud.google.com/sdk/docs/install"
        exit 1
    fi
    
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker n'est pas installé${NC}"
        echo "Installez Docker: https://docs.docker.com/get-docker/"
        exit 1
    fi
    
    # Vérifier la connexion à GCP
    if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
        echo -e "${RED}❌ Vous n'êtes pas connecté à GCP${NC}"
        echo "Exécutez: gcloud auth login"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
}

# Configuration du projet GCP
setup_gcp_project() {
    echo -e "${YELLOW}🏗️  Configuration du projet GCP...${NC}"
    
    # Créer le projet s'il n'existe pas
    if ! gcloud projects describe $PROJECT_ID &> /dev/null; then
        echo "Création du projet: $PROJECT_ID"
        gcloud projects create $PROJECT_ID --name="FIT Platform ${ENVIRONMENT}"
    fi
    
    # Définir le projet actif
    gcloud config set project $PROJECT_ID
    
    # Activer les APIs nécessaires
    echo "Activation des APIs GCP..."
    gcloud services enable cloudbuild.googleapis.com
    gcloud services enable run.googleapis.com
    gcloud services enable sqladmin.googleapis.com
    gcloud services enable secretmanager.googleapis.com
    gcloud services enable cloudresourcemanager.googleapis.com
    
    echo -e "${GREEN}✅ Projet GCP configuré${NC}"
}

# Configuration de la base de données Cloud SQL
setup_cloud_sql() {
    echo -e "${YELLOW}🗄️  Configuration de Cloud SQL...${NC}"
    
    INSTANCE_NAME="${PROJECT_NAME}-mysql-${ENVIRONMENT}"
    
    # Créer l'instance MySQL si elle n'existe pas
    if ! gcloud sql instances describe $INSTANCE_NAME &> /dev/null; then
        echo "Création de l'instance MySQL: $INSTANCE_NAME"
        gcloud sql instances create $INSTANCE_NAME \
            --database-version=MYSQL_8_0 \
            --tier=db-f1-micro \
            --region=$REGION \
            --storage-type=SSD \
            --storage-size=10GB \
            --backup-start-time="02:00" \
            --maintenance-window-day=SUN \
            --maintenance-window-hour="03:00" \
            --authorized-networks=0.0.0.0/0 \
            --require-ssl=false
    fi
    
    # Créer la base de données
    DB_NAME="med_predictor"
    if ! gcloud sql databases describe $DB_NAME --instance=$INSTANCE_NAME &> /dev/null; then
        echo "Création de la base de données: $DB_NAME"
        gcloud sql databases create $DB_NAME --instance=$INSTANCE_NAME
    fi
    
    # Créer l'utilisateur
    DB_USER="med_user"
    DB_PASSWORD="med_password_$(openssl rand -hex 8)"
    
    if ! gcloud sql users describe $DB_USER --instance=$INSTANCE_NAME &> /dev/null; then
        echo "Création de l'utilisateur: $DB_USER"
        gcloud sql users create $DB_USER \
            --instance=$INSTANCE_NAME \
            --password=$DB_PASSWORD
    fi
    
    # Obtenir l'IP de l'instance
    DB_IP=$(gcloud sql instances describe $INSTANCE_NAME --format="value(ipAddresses[0].ipAddress)")
    
    echo -e "${GREEN}✅ Cloud SQL configuré${NC}"
    echo "Instance: $INSTANCE_NAME"
    echo "Base de données: $DB_NAME"
    echo "Utilisateur: $DB_USER"
    echo "IP: $DB_IP"
}

# Configuration de Secret Manager
setup_secret_manager() {
    echo -e "${YELLOW}🔐 Configuration de Secret Manager...${NC}"
    
    # Créer les secrets
    echo "Création des secrets..."
    
    # Secret de la base de données
    echo $DB_PASSWORD | gcloud secrets create db-password --data-file=- --replication-policy="automatic"
    
    # Secret de l'application
    APP_KEY="base64:$(openssl rand -base64 32)"
    echo $APP_KEY | gcloud secrets create app-key --data-file=- --replication-policy="automatic"
    
    # Secret Redis
    REDIS_PASSWORD="redis_password_$(openssl rand -hex 8)"
    echo $REDIS_PASSWORD | gcloud secrets create redis-password --data-file=- --replication-policy="automatic"
    
    echo -e "${GREEN}✅ Secret Manager configuré${NC}"
}

# Construction et push de l'image Docker
build_and_push_image() {
    echo -e "${YELLOW}🐳 Construction et push de l'image Docker...${NC}"
    
    # Tag de l'image
    IMAGE_TAG="${IMAGE_NAME}:${ENVIRONMENT}-$(date +%Y%m%d-%H%M%S)"
    
    echo "Construction de l'image: $IMAGE_TAG"
    docker build -t $IMAGE_TAG .
    
    # Configuration de Docker pour GCR
    gcloud auth configure-docker
    
    # Push de l'image
    echo "Push de l'image vers GCR..."
    docker push $IMAGE_TAG
    
    echo -e "${GREEN}✅ Image Docker poussée: $IMAGE_TAG${NC}"
}

# Déploiement sur Cloud Run
deploy_to_cloud_run() {
    echo -e "${YELLOW}🚀 Déploiement sur Cloud Run...${NC}"
    
    # Variables d'environnement
    ENV_VARS="APP_ENV=${ENVIRONMENT},APP_DEBUG=false,DB_CONNECTION=mysql,DB_HOST=${DB_IP},DB_PORT=3306,DB_DATABASE=${DB_NAME},DB_USERNAME=${DB_USER}"
    
    # Déployer le service
    echo "Déploiement du service Cloud Run..."
    gcloud run deploy $SERVICE_NAME \
        --image=$IMAGE_TAG \
        --region=$REGION \
        --platform=managed \
        --allow-unauthenticated \
        --memory=1Gi \
        --cpu=1 \
        --max-instances=10 \
        --min-instances=1 \
        --set-env-vars=$ENV_VARS \
        --set-secrets=DB_PASSWORD=db-password:latest,APP_KEY=app-key:latest,REDIS_PASSWORD=redis-password:latest \
        --port=9000 \
        --concurrency=80 \
        --timeout=300
    
    # Obtenir l'URL du service
    SERVICE_URL=$(gcloud run services describe $SERVICE_NAME --region=$REGION --format="value(status.url)")
    
    echo -e "${GREEN}✅ Service déployé sur Cloud Run${NC}"
    echo "URL: $SERVICE_URL"
}

# Configuration du domaine personnalisé
setup_custom_domain() {
    echo -e "${YELLOW}🌐 Configuration du domaine personnalisé...${NC}"
    
    if [ ! -z "$CUSTOM_DOMAIN" ]; then
        echo "Configuration du domaine: $CUSTOM_DOMAIN"
        
        # Mapper le domaine au service
        gcloud run domain-mappings create \
            --service=$SERVICE_NAME \
            --domain=$CUSTOM_DOMAIN \
            --region=$REGION
        
        echo -e "${GREEN}✅ Domaine personnalisé configuré${NC}"
    else
        echo -e "${YELLOW}⚠️  Aucun domaine personnalisé configuré${NC}"
    fi
}

# Test du déploiement
test_deployment() {
    echo -e "${YELLOW}🧪 Test du déploiement...${NC}"
    
    # Obtenir l'URL du service
    SERVICE_URL=$(gcloud run services describe $SERVICE_NAME --region=$REGION --format="value(status.url)")
    
    echo "Test des endpoints de santé..."
    
    # Test de /health
    if curl -s "${SERVICE_URL}/health" | grep -q "healthy"; then
        echo -e "${GREEN}✅ Endpoint /health fonctionne${NC}"
    else
        echo -e "${RED}❌ Endpoint /health ne fonctionne pas${NC}"
    fi
    
    # Test de /ready
    if curl -s "${SERVICE_URL}/ready" | grep -q "ready"; then
        echo -e "${GREEN}✅ Endpoint /ready fonctionne${NC}"
    else
        echo -e "${RED}❌ Endpoint /ready ne fonctionne pas${NC}"
    fi
    
    echo -e "${GREEN}✅ Tests terminés${NC}"
}

# Affichage des informations de connexion
show_connection_info() {
    echo -e "${BLUE}📋 Informations de connexion${NC}"
    echo "=================================================="
    echo "Projet GCP: $PROJECT_ID"
    echo "Région: $REGION"
    echo "Service: $SERVICE_NAME"
    echo "Base de données: $DB_NAME"
    echo "Instance MySQL: $INSTANCE_NAME"
    
    echo ""
    echo "URLs:"
    echo "Service: $SERVICE_URL"
    if [ ! -z "$CUSTOM_DOMAIN" ]; then
        echo "Domaine: https://$CUSTOM_DOMAIN"
    fi
    
    echo ""
    echo "Commandes utiles:"
    echo "Voir les logs: gcloud logs read --project=$PROJECT_ID --filter='resource.type=cloud_run_revision'"
    echo "Redémarrer: gcloud run services replace $SERVICE_NAME --region=$REGION"
    echo "Supprimer: gcloud run services delete $SERVICE_NAME --region=$REGION"
}

# Fonction principale
main() {
    check_prerequisites
    setup_gcp_project
    setup_cloud_sql
    setup_secret_manager
    build_and_push_image
    deploy_to_cloud_run
    setup_custom_domain
    test_deployment
    show_connection_info
    
    echo -e "${GREEN}🎉 Déploiement GCP terminé avec succès !${NC}"
    echo ""
    echo "Prochaines étapes:"
    echo "1. Configurer votre DNS pour pointer vers Cloud Run"
    echo "2. Configurer les certificats SSL automatiques"
    echo "3. Configurer le monitoring et les alertes"
    echo "4. Tester les fonctionnalités de l'application"
}

# Gestion des erreurs
trap 'echo -e "${RED}❌ Erreur lors du déploiement${NC}"; exit 1' ERR

# Exécution
main "$@"










