#!/bin/bash

# Google Cloud Storage Setup Script for Med-Predictor
# This script sets up GCS buckets, IAM permissions, and configurations

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
REGION="${GOOGLE_CLOUD_REGION:-us-central1}"
STORAGE_BUCKET="${GOOGLE_CLOUD_STORAGE_BUCKET:-med-predictor-storage}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
SERVICE_ACCOUNT_NAME="med-predictor-gcs-sa"

echo -e "${BLUE}🚀 Google Cloud Storage Setup for Med-Predictor${NC}"
echo "=================================================="

# Check prerequisites
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check gcloud CLI
    if ! command -v gcloud &> /dev/null; then
        echo -e "${RED}❌ Google Cloud CLI is not installed. Please install it first.${NC}"
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
    gcloud services enable storage.googleapis.com
    gcloud services enable storage-api.googleapis.com
    gcloud services enable iam.googleapis.com
    gcloud services enable cloudresourcemanager.googleapis.com
    
    echo -e "${GREEN}✅ GCP project setup complete${NC}"
}

# Create GCS buckets
create_buckets() {
    echo -e "\n${YELLOW}🪣 Creating GCS buckets...${NC}"
    
    # Create main storage bucket
    if ! gsutil ls -b "gs://${STORAGE_BUCKET}" &> /dev/null; then
        echo "Creating main storage bucket: ${STORAGE_BUCKET}"
        gsutil mb -l "${REGION}" "gs://${STORAGE_BUCKET}"
        
        # Set bucket permissions
        gsutil iam ch allUsers:objectViewer "gs://${STORAGE_BUCKET}"
        gsutil cors set cors.json "gs://${STORAGE_BUCKET}" || echo "CORS config file not found, skipping..."
    else
        echo "Main storage bucket already exists: ${STORAGE_BUCKET}"
    fi
    
    # Create backup bucket
    if ! gsutil ls -b "gs://${BACKUP_BUCKET}" &> /dev/null; then
        echo "Creating backup bucket: ${BACKUP_BUCKET}"
        gsutil mb -l "${REGION}" "gs://${BACKUP_BUCKET}"
        
        # Set backup bucket permissions (private)
        gsutil iam ch -d allUsers "gs://${BACKUP_BUCKET}"
    else
        echo "Backup bucket already exists: ${BACKUP_BUCKET}"
    fi
    
    echo -e "${GREEN}✅ GCS buckets created successfully${NC}"
}

# Create service account
create_service_account() {
    echo -e "\n${YELLOW}👤 Creating service account...${NC}"
    
    # Check if service account exists
    if ! gcloud iam service-accounts describe "${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" &> /dev/null; then
        echo "Creating service account: ${SERVICE_ACCOUNT_NAME}"
        gcloud iam service-accounts create "${SERVICE_ACCOUNT_NAME}" \
            --display-name="Med-Predictor GCS Service Account" \
            --description="Service account for Med-Predictor Google Cloud Storage access"
    else
        echo "Service account already exists: ${SERVICE_ACCOUNT_NAME}"
    fi
    
    # Grant storage permissions
    echo "Granting storage permissions..."
    gcloud projects add-iam-policy-binding "${PROJECT_ID}" \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="roles/storage.objectAdmin"
    
    gcloud projects add-iam-policy-binding "${PROJECT_ID}" \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="roles/storage.bucketAdmin"
    
    echo -e "${GREEN}✅ Service account created and configured${NC}"
}

# Generate service account key
generate_service_account_key() {
    echo -e "\n${YELLOW}🔑 Generating service account key...${NC}"
    
    KEY_FILE="service-account-key.json"
    
    if [ ! -f "${KEY_FILE}" ]; then
        echo "Generating service account key..."
        gcloud iam service-accounts keys create "${KEY_FILE}" \
            --iam-account="${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"
        
        echo -e "${GREEN}✅ Service account key generated: ${KEY_FILE}${NC}"
        echo -e "${YELLOW}⚠️  Keep this file secure and never commit it to version control!${NC}"
    else
        echo "Service account key already exists: ${KEY_FILE}"
    fi
}

# Create CORS configuration
create_cors_config() {
    echo -e "\n${YELLOW}🌐 Creating CORS configuration...${NC}"
    
    cat > cors.json << EOF
[
  {
    "origin": ["*"],
    "method": ["GET", "POST", "PUT", "DELETE", "HEAD"],
    "responseHeader": ["Content-Type", "Access-Control-Allow-Origin"],
    "maxAgeSeconds": 3600
  }
]
EOF
    
    echo -e "${GREEN}✅ CORS configuration created${NC}"
}

# Create lifecycle policy
create_lifecycle_policy() {
    echo -e "\n${YELLOW}📋 Creating lifecycle policy...${NC}"
    
    cat > lifecycle.json << EOF
{
  "rule": [
    {
      "action": {
        "type": "Delete"
      },
      "condition": {
        "age": 30,
        "matchesPrefix": ["temp/"]
      }
    },
    {
      "action": {
        "type": "SetStorageClass",
        "storageClass": "NEARLINE"
      },
      "condition": {
        "age": 90,
        "matchesPrefix": ["backups/"]
      }
    },
    {
      "action": {
        "type": "SetStorageClass",
        "storageClass": "COLDLINE"
      },
      "condition": {
        "age": 365,
        "matchesPrefix": ["archives/"]
      }
    }
  ]
}
EOF
    
    # Apply lifecycle policy to backup bucket
    gsutil lifecycle set lifecycle.json "gs://${BACKUP_BUCKET}"
    
    echo -e "${GREEN}✅ Lifecycle policy created and applied${NC}"
}

# Test GCS connection
test_gcs_connection() {
    echo -e "\n${YELLOW}🧪 Testing GCS connection...${NC}"
    
    # Test bucket access
    if gsutil ls "gs://${STORAGE_BUCKET}" &> /dev/null; then
        echo -e "${GREEN}✅ Main bucket accessible${NC}"
    else
        echo -e "${RED}❌ Main bucket not accessible${NC}"
        return 1
    fi
    
    if gsutil ls "gs://${BACKUP_BUCKET}" &> /dev/null; then
        echo -e "${GREEN}✅ Backup bucket accessible${NC}"
    else
        echo -e "${RED}❌ Backup bucket not accessible${NC}"
        return 1
    fi
    
    # Test file upload
    echo "Testing file upload..."
    echo "Hello from Med-Predictor!" > test-file.txt
    gsutil cp test-file.txt "gs://${STORAGE_BUCKET}/test/"
    rm test-file.txt
    
    echo -e "${GREEN}✅ GCS connection test successful${NC}"
}

# Generate environment configuration
generate_env_config() {
    echo -e "\n${YELLOW}📝 Generating environment configuration...${NC}"
    
    cat > gcs-env-config.txt << EOF
# Add these variables to your .env file:

# Google Cloud Storage Configuration
GOOGLE_CLOUD_PROJECT_ID=${PROJECT_ID}
GOOGLE_CLOUD_REGION=${REGION}
GOOGLE_CLOUD_STORAGE_BUCKET=${STORAGE_BUCKET}
GOOGLE_CLOUD_STORAGE_PATH_PREFIX=uploads/
GOOGLE_CLOUD_KEY_FILE=$(pwd)/service-account-key.json

# Storage Configuration
FILESYSTEM_DISK=gcs
DEFAULT_STORAGE_DISK=gcs

# Backup Configuration
GCS_BACKUP_ENABLED=true
GCS_BACKUP_BUCKET=${BACKUP_BUCKET}
GCS_BACKUP_RETENTION_DAYS=30

# Service Account Email
GCS_SERVICE_ACCOUNT_EMAIL=${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com
EOF
    
    echo -e "${GREEN}✅ Environment configuration generated: gcs-env-config.txt${NC}"
}

# Main setup function
main() {
    echo -e "${BLUE}Starting Google Cloud Storage setup...${NC}"
    
    check_prerequisites
    setup_project
    create_cors_config
    create_lifecycle_policy
    create_buckets
    create_service_account
    generate_service_account_key
    test_gcs_connection
    generate_env_config
    
    echo -e "\n${GREEN}🎉 Google Cloud Storage setup completed successfully!${NC}"
    echo -e "${BLUE}Next steps:${NC}"
    echo -e "${YELLOW}1. Copy the variables from gcs-env-config.txt to your .env file${NC}"
    echo -e "${YELLOW}2. Install dependencies: composer install${NC}"
    echo -e "${YELLOW}3. Test the integration: php artisan storage:link${NC}"
    echo -e "${YELLOW}4. Run: php artisan config:cache${NC}"
}

# Run main function
main "$@"

