#!/bin/bash

# Google Cloud Platform Deployment Script for Med-Predictor
# This script deploys the application to GCP Cloud Run

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="med-predictor"
PROJECT_ID="${GCP_PROJECT_ID:-med-predictor-project}"
REGION="${GCP_REGION:-us-central1}"
SERVICE_NAME="${SERVICE_NAME:-med-predictor-service}"
IMAGE_NAME="gcr.io/${PROJECT_ID}/${PROJECT_NAME}"

echo -e "${BLUE}🚀 GCP Deployment Script for Med-Predictor${NC}"
echo "=================================================="

# Check prerequisites
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check gcloud CLI
    if ! command -v gcloud &> /dev/null; then
        echo -e "${RED}❌ Google Cloud CLI is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check if authenticated
    if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
        echo -e "${RED}❌ Not authenticated with Google Cloud. Please run 'gcloud auth login' first.${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ All prerequisites met${NC}"
}

# Set up GCP project
setup_project() {
    echo -e "\n${YELLOW}🏗️  Setting up GCP project...${NC}"
    
    # Check if project exists
    if ! gcloud projects describe "${PROJECT_ID}" &> /dev/null; then
        echo "Creating GCP project: ${PROJECT_ID}"
        gcloud projects create "${PROJECT_ID}" --name="Med-Predictor Project"
    fi
    
    # Set project
    gcloud config set project "${PROJECT_ID}"
    
    # Enable required APIs
    echo "Enabling required APIs..."
    gcloud services enable cloudbuild.googleapis.com
    gcloud services enable run.googleapis.com
    gcloud services enable containerregistry.googleapis.com
    gcloud services enable secretmanager.googleapis.com
    
    echo -e "${GREEN}✅ GCP project setup complete${NC}"
}

# Build and push Docker image
build_and_push_image() {
    echo -e "\n${YELLOW}🔨 Building and pushing Docker image...${NC}"
    
    # Configure Docker for GCR
    gcloud auth configure-docker
    
    # Build image
    echo "Building Docker image..."
    docker build -t "${IMAGE_NAME}:latest" .
    
    # Push image
    echo "Pushing image to Google Container Registry..."
    docker push "${IMAGE_NAME}:latest"
    
    echo -e "${GREEN}✅ Docker image pushed successfully${NC}"
}

# Deploy to Cloud Run
deploy_to_cloud_run() {
    echo -e "\n${YELLOW}🚀 Deploying to Cloud Run...${NC}"
    
    # Deploy service
    gcloud run deploy "${SERVICE_NAME}" \
        --image "${IMAGE_NAME}:latest" \
        --platform managed \
        --region "${REGION}" \
        --allow-unauthenticated \
        --port 80 \
        --memory 1Gi \
        --cpu 1 \
        --max-instances 10 \
        --set-env-vars "APP_ENV=production,APP_DEBUG=false" \
        --set-cloudsql-instances "${PROJECT_ID}:${REGION}:med-predictor-db" \
        --add-cloudsql-instances "${PROJECT_ID}:${REGION}:med-predictor-db"
    
    # Get service URL
    SERVICE_URL=$(gcloud run services describe "${SERVICE_NAME}" --region "${REGION}" --format="value(status.url)")
    
    echo -e "${GREEN}✅ Service deployed successfully${NC}"
    echo -e "${BLUE}Service URL: ${SERVICE_URL}${NC}"
}

# Create Cloud SQL instance
create_database() {
    echo -e "\n${YELLOW}🗄️  Setting up Cloud SQL database...${NC}"
    
    INSTANCE_NAME="med-predictor-db"
    
    # Check if instance exists
    if ! gcloud sql instances describe "${INSTANCE_NAME}" &> /dev/null; then
        echo "Creating Cloud SQL instance: ${INSTANCE_NAME}"
        gcloud sql instances create "${INSTANCE_NAME}" \
            --database-version=MYSQL_8_0 \
            --tier=db-f1-micro \
            --region="${REGION}" \
            --root-password="root-password-123" \
            --storage-type=SSD \
            --storage-size=10GB \
            --backup-start-time="02:00" \
            --enable-bin-log \
            --maintenance-window-day=SUN \
            --maintenance-window-hour=02
    else
        echo "Cloud SQL instance already exists: ${INSTANCE_NAME}"
    fi
    
    # Create database
    echo "Creating database..."
    gcloud sql databases create med_predictor --instance="${INSTANCE_NAME}"
    
    # Create user
    echo "Creating database user..."
    gcloud sql users create med_user \
        --instance="${INSTANCE_NAME}" \
        --password="med-password-123"
    
    echo -e "${GREEN}✅ Database setup complete${NC}"
}

# Set up secrets
setup_secrets() {
    echo -e "\n${YELLOW}🔐 Setting up secrets...${NC}"
    
    # Create secrets
    echo "DB_PASSWORD" | gcloud secrets create db-password --data-file=-
    echo "med-password-123" | gcloud secrets versions add db-password --data-file=-
    
    echo "SMTP_PASSWORD" | gcloud secrets create smtp-password --data-file=-
    echo "your-smtp-password" | gcloud secrets versions add smtp-password --data-file=-
    
    echo -e "${GREEN}✅ Secrets configured${NC}"
}

# Main deployment function
main() {
    echo -e "${BLUE}Starting deployment to Google Cloud Platform...${NC}"
    
    check_prerequisites
    setup_project
    create_database
    setup_secrets
    build_and_push_image
    deploy_to_cloud_run
    
    echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${BLUE}Your application is now running on Google Cloud Run.${NC}"
    echo -e "${YELLOW}Note: Remember to update your environment variables with the actual secret values.${NC}"
}

# Run main function
main "$@"









