#!/bin/bash

# Microsoft Azure Deployment Script for Med-Predictor
# This script deploys the application to Azure Container Instances

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="med-predictor"
RESOURCE_GROUP="${AZURE_RESOURCE_GROUP:-med-predictor-rg}"
LOCATION="${AZURE_LOCATION:-eastus}"
REGISTRY_NAME="${AZURE_REGISTRY_NAME:-medpredictorregistry}"
CONTAINER_NAME="${AZURE_CONTAINER_NAME:-med-predictor-container}"
ACR_LOGIN_SERVER="${ACR_LOGIN_SERVER:-}"

echo -e "${BLUE}🚀 Azure Deployment Script for Med-Predictor${NC}"
echo "=================================================="

# Check prerequisites
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check Azure CLI
    if ! command -v az &> /dev/null; then
        echo -e "${RED}❌ Azure CLI is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check if authenticated
    if ! az account show &> /dev/null; then
        echo -e "${RED}❌ Not authenticated with Azure. Please run 'az login' first.${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ All prerequisites met${NC}"
}

# Set up Azure resources
setup_azure_resources() {
    echo -e "\n${YELLOW}🏗️  Setting up Azure resources...${NC}"
    
    # Create resource group
    if ! az group show --name "${RESOURCE_GROUP}" &> /dev/null; then
        echo "Creating resource group: ${RESOURCE_GROUP}"
        az group create --name "${RESOURCE_GROUP}" --location "${LOCATION}"
    else
        echo "Resource group already exists: ${RESOURCE_GROUP}"
    fi
    
    # Create container registry
    if ! az acr show --name "${REGISTRY_NAME}" --resource-group "${RESOURCE_GROUP}" &> /dev/null; then
        echo "Creating Azure Container Registry: ${REGISTRY_NAME}"
        az acr create \
            --resource-group "${RESOURCE_GROUP}" \
            --name "${REGISTRY_NAME}" \
            --sku Basic \
            --admin-enabled true
    else
        echo "Container registry already exists: ${REGISTRY_NAME}"
    fi
    
    # Get ACR login server
    ACR_LOGIN_SERVER=$(az acr show --name "${REGISTRY_NAME}" --resource-group "${RESOURCE_GROUP}" --query loginServer --output tsv)
    
    echo -e "${GREEN}✅ Azure resources setup complete${NC}"
}

# Build and push Docker image
build_and_push_image() {
    echo -e "\n${YELLOW}🔨 Building and pushing Docker image...${NC}"
    
    # Login to ACR
    echo "Logging in to Azure Container Registry..."
    az acr login --name "${REGISTRY_NAME}"
    
    # Build image
    echo "Building Docker image..."
    docker build -t "${PROJECT_NAME}:latest" .
    
    # Tag image for ACR
    docker tag "${PROJECT_NAME}:latest" "${ACR_LOGIN_SERVER}/${PROJECT_NAME}:latest"
    
    # Push image
    echo "Pushing image to Azure Container Registry..."
    docker push "${ACR_LOGIN_SERVER}/${PROJECT_NAME}:latest"
    
    echo -e "${GREEN}✅ Docker image pushed successfully${NC}"
}

# Deploy to Azure Container Instances
deploy_to_aci() {
    echo -e "\n${YELLOW}🚀 Deploying to Azure Container Instances...${NC}"
    
    # Check if container instance exists
    if az container show --name "${CONTAINER_NAME}" --resource-group "${RESOURCE_GROUP}" &> /dev/null; then
        echo "Updating existing container instance: ${CONTAINER_NAME}"
        az container delete --name "${CONTAINER_NAME}" --resource-group "${RESOURCE_GROUP}" --yes
    fi
    
    # Create container instance
    echo "Creating container instance: ${CONTAINER_NAME}"
    az container create \
        --resource-group "${RESOURCE_GROUP}" \
        --name "${CONTAINER_NAME}" \
        --image "${ACR_LOGIN_SERVER}/${PROJECT_NAME}:latest" \
        --dns-name-label "${PROJECT_NAME}-${RANDOM}" \
        --ports 80 \
        --memory 1.0 \
        --cpu 1.0 \
        --environment-variables \
            APP_ENV=production \
            APP_DEBUG=false \
        --registry-login-server "${ACR_LOGIN_SERVER}" \
        --registry-username $(az acr credential show --name "${REGISTRY_NAME}" --query username --output tsv) \
        --registry-password $(az acr credential show --name "${REGISTRY_NAME}" --query passwords[0].value --output tsv)
    
    # Get container instance details
    CONTAINER_IP=$(az container show \
        --resource-group "${RESOURCE_GROUP}" \
        --name "${CONTAINER_NAME}" \
        --query ipAddress.ip --output tsv)
    
    FQDN=$(az container show \
        --resource-group "${RESOURCE_GROUP}" \
        --name "${CONTAINER_NAME}" \
        --query ipAddress.fqdn --output tsv)
    
    echo -e "${GREEN}✅ Container instance deployed successfully${NC}"
    echo -e "${BLUE}Container IP: ${CONTAINER_IP}${NC}"
    echo -e "${BLUE}FQDN: ${FQDN}${NC}"
}

# Create Azure Database for MySQL
create_database() {
    echo -e "\n${YELLOW}🗄️  Setting up Azure Database for MySQL...${NC}"
    
    SERVER_NAME="med-predictor-mysql"
    ADMIN_USER="medadmin"
    ADMIN_PASSWORD="MedPassword123!"
    DATABASE_NAME="med_predictor"
    
    # Check if server exists
    if ! az mysql server show --name "${SERVER_NAME}" --resource-group "${RESOURCE_GROUP}" &> /dev/null; then
        echo "Creating MySQL server: ${SERVER_NAME}"
        az mysql server create \
            --resource-group "${RESOURCE_GROUP}" \
            --name "${SERVER_NAME}" \
            --location "${LOCATION}" \
            --admin-user "${ADMIN_USER}" \
            --admin-password "${ADMIN_PASSWORD}" \
            --sku-name B_Gen5_1 \
            --version 8.0 \
            --ssl-enforcement Disabled
    else
        echo "MySQL server already exists: ${SERVER_NAME}"
    fi
    
    # Create database
    echo "Creating database: ${DATABASE_NAME}"
    az mysql db create \
        --resource-group "${RESOURCE_GROUP}" \
        --server-name "${SERVER_NAME}" \
        --name "${DATABASE_NAME}"
    
    # Create firewall rule for Azure services
    echo "Creating firewall rule for Azure services..."
    az mysql server firewall-rule create \
        --resource-group "${RESOURCE_GROUP}" \
        --server-name "${SERVER_NAME}" \
        --name "AllowAzureServices" \
        --start-ip-address 0.0.0.0 \
        --end-ip-address 0.0.0.0
    
    echo -e "${GREEN}✅ Database setup complete${NC}"
    echo -e "${BLUE}Server: ${SERVER_NAME}.mysql.database.azure.com${NC}"
    echo -e "${BLUE}Database: ${DATABASE_NAME}${NC}"
    echo -e "${BLUE}Username: ${ADMIN_USER}@${SERVER_NAME}${NC}"
}

# Set up Azure Key Vault for secrets
setup_key_vault() {
    echo -e "\n${YELLOW}🔐 Setting up Azure Key Vault...${NC}"
    
    KEY_VAULT_NAME="med-predictor-kv"
    
    # Check if key vault exists
    if ! az keyvault show --name "${KEY_VAULT_NAME}" --resource-group "${RESOURCE_GROUP}" &> /dev/null; then
        echo "Creating Key Vault: ${KEY_VAULT_NAME}"
        az keyvault create \
            --resource-group "${RESOURCE_GROUP}" \
            --name "${KEY_VAULT_NAME}" \
            --location "${LOCATION}" \
            --sku standard
    else
        echo "Key Vault already exists: ${KEY_VAULT_NAME}"
    fi
    
    # Store secrets
    echo "Storing secrets in Key Vault..."
    az keyvault secret set --vault-name "${KEY_VAULT_NAME}" --name "DB-PASSWORD" --value "MedPassword123!"
    az keyvault secret set --vault-name "${KEY_VAULT_NAME}" --name "SMTP-PASSWORD" --value "your-smtp-password"
    
    echo -e "${GREEN}✅ Key Vault setup complete${NC}"
}

# Main deployment function
main() {
    echo -e "${BLUE}Starting deployment to Microsoft Azure...${NC}"
    
    check_prerequisites
    setup_azure_resources
    create_database
    setup_key_vault
    build_and_push_image
    deploy_to_aci
    
    echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${BLUE}Your application is now running on Azure Container Instances.${NC}"
    echo -e "${YELLOW}Note: Remember to update your environment variables with the actual secret values.${NC}"
}

# Run main function
main "$@"











