#!/bin/bash

# Script de déploiement Azure pour FIT Platform
# Usage: ./scripts/deploy-azure.sh [staging|production]

set -e

# Configuration
ENVIRONMENT=${1:-staging}
PROJECT_NAME="fit-platform"
RESOURCE_GROUP="${PROJECT_NAME}-${ENVIRONMENT}-rg"
LOCATION="westeurope"
CONTAINER_GROUP="${PROJECT_NAME}-app-${ENVIRONMENT}"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Déploiement Azure FIT Platform - ${ENVIRONMENT}${NC}"
echo "=================================================="

# Vérification des prérequis
check_prerequisites() {
    echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    if ! command -v az &> /dev/null; then
        echo -e "${RED}❌ Azure CLI n'est pas installé${NC}"
        echo "Installez Azure CLI: https://docs.microsoft.com/en-us/cli/azure/install-azure-cli"
        exit 1
    fi
    
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker n'est pas installé${NC}"
        exit 1
    fi
    
    # Vérifier la connexion à Azure
    if ! az account show &> /dev/null; then
        echo -e "${RED}❌ Vous n'êtes pas connecté à Azure${NC}"
        echo "Exécutez: az login"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
}

# Configuration du groupe de ressources
setup_resource_group() {
    echo -e "${YELLOW}🏗️  Configuration du groupe de ressources...${NC}"
    
    if ! az group show --name $RESOURCE_GROUP &> /dev/null; then
        echo "Création du groupe de ressources: $RESOURCE_GROUP"
        az group create --name $RESOURCE_GROUP --location $LOCATION
    fi
    
    echo -e "${GREEN}✅ Groupe de ressources configuré${NC}"
}

# Construction et push de l'image Docker
build_and_push_image() {
    echo -e "${YELLOW}🐳 Construction et push de l'image Docker...${NC}"
    
    # Créer le registry si nécessaire
    REGISTRY_NAME="${PROJECT_NAME}${ENVIRONMENT}registry"
    if ! az acr show --name $REGISTRY_NAME --resource-group $RESOURCE_GROUP &> /dev/null; then
        echo "Création du registry: $REGISTRY_NAME"
        az acr create --resource-group $RESOURCE_GROUP --name $REGISTRY_NAME --sku Basic
    fi
    
    # Login au registry
    az acr login --name $REGISTRY_NAME
    
    # Construire et pousser l'image
    IMAGE_TAG="${REGISTRY_NAME}.azurecr.io/${PROJECT_NAME}:${ENVIRONMENT}"
    docker build -t $IMAGE_TAG .
    docker push $IMAGE_TAG
    
    echo -e "${GREEN}✅ Image Docker poussée: $IMAGE_TAG${NC}"
}

# Déploiement sur Container Instances
deploy_to_aci() {
    echo -e "${YELLOW}🚀 Déploiement sur Container Instances...${NC}"
    
    # Créer le groupe de conteneurs
    az container create \
        --resource-group $RESOURCE_GROUP \
        --name $CONTAINER_GROUP \
        --image "${REGISTRY_NAME}.azurecr.io/${PROJECT_NAME}:${ENVIRONMENT}" \
        --registry-login-server "${REGISTRY_NAME}.azurecr.io" \
        --registry-username $(az acr credential show --name $REGISTRY_NAME --query username -o tsv) \
        --registry-password $(az acr credential show --name $REGISTRY_NAME --query passwords[0].value -o tsv) \
        --dns-name-label "${PROJECT_NAME}-${ENVIRONMENT}" \
        --ports 9000 \
        --memory 1.0 \
        --cpu 1.0 \
        --environment-variables \
            APP_ENV=$ENVIRONMENT \
            APP_DEBUG=false \
            DB_CONNECTION=mysql \
            DB_HOST=your-mysql-host \
            DB_PORT=3306 \
            DB_DATABASE=med_predictor \
            DB_USERNAME=med_user \
            DB_PASSWORD=med_password
    
    # Obtenir l'IP publique
    PUBLIC_IP=$(az container show --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP --query ipAddress.ip -o tsv)
    
    echo -e "${GREEN}✅ Service déployé sur Container Instances${NC}"
    echo "IP publique: $PUBLIC_IP"
    echo "URL: http://${PUBLIC_IP}:9000"
}

# Test du déploiement
test_deployment() {
    echo -e "${YELLOW}🧪 Test du déploiement...${NC}"
    
    PUBLIC_IP=$(az container show --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP --query ipAddress.ip -o tsv)
    
    echo "Test des endpoints de santé..."
    
    # Test de /health
    if curl -s "http://${PUBLIC_IP}:9000/health" | grep -q "healthy"; then
        echo -e "${GREEN}✅ Endpoint /health fonctionne${NC}"
    else
        echo -e "${RED}❌ Endpoint /health ne fonctionne pas${NC}"
    fi
    
    echo -e "${GREEN}✅ Tests terminés${NC}"
}

# Affichage des informations
show_connection_info() {
    echo -e "${BLUE}📋 Informations de connexion${NC}"
    echo "=================================================="
    echo "Groupe de ressources: $RESOURCE_GROUP"
    echo "Location: $LOCATION"
    echo "Container Group: $CONTAINER_GROUP"
    
    PUBLIC_IP=$(az container show --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP --query ipAddress.ip -o tsv)
    echo "IP publique: $PUBLIC_IP"
    echo "URL: http://${PUBLIC_IP}:9000"
    
    echo ""
    echo "Commandes utiles:"
    echo "Voir les logs: az container logs --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP"
    echo "Redémarrer: az container restart --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP"
    echo "Supprimer: az container delete --resource-group $RESOURCE_GROUP --name $CONTAINER_GROUP"
}

# Fonction principale
main() {
    check_prerequisites
    setup_resource_group
    build_and_push_image
    deploy_to_aci
    test_deployment
    show_connection_info
    
    echo -e "${GREEN}🎉 Déploiement Azure terminé avec succès !${NC}"
}

# Gestion des erreurs
trap 'echo -e "${RED}❌ Erreur lors du déploiement${NC}"; exit 1' ERR

# Exécution
main "$@"











