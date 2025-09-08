#!/bin/bash

# Google Cloud Storage + Kubernetes Deployment Script
# This script deploys the complete GCS + K8s infrastructure

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
ZONE="${GOOGLE_CLOUD_ZONE:-us-central1-a}"
CLUSTER_NAME="${K8S_CLUSTER_NAME:-med-predictor-cluster}"
STORAGE_BUCKET="${GOOGLE_CLOUD_STORAGE_BUCKET:-med-predictor-storage}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
NAMESPACE="med-predictor"

echo -e "${BLUE}🚀 Google Cloud Storage + Kubernetes Deployment${NC}"
echo "=============================================="

# Check prerequisites
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check gcloud CLI
    if ! command -v gcloud &> /dev/null; then
        echo -e "${RED}❌ Google Cloud CLI is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check kubectl
    if ! command -v kubectl &> /dev/null; then
        echo -e "${RED}❌ kubectl is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check if authenticated
    if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
        echo -e "${RED}❌ Not authenticated with Google Cloud. Please run 'gcloud auth login' first.${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ All prerequisites met${NC}"
}

# Set up GCP project and enable APIs
setup_project() {
    echo -e "\n${YELLOW}🏗️  Setting up GCP project...${NC}"
    
    # Set project
    gcloud config set project "${PROJECT_ID}"
    
    # Enable required APIs
    echo "Enabling required APIs..."
    gcloud services enable container.googleapis.com
    gcloud services enable storage.googleapis.com
    gcloud services enable storage-api.googleapis.com
    gcloud services enable iam.googleapis.com
    gcloud services enable cloudresourcemanager.googleapis.com
    gcloud services enable compute.googleapis.com
    
    echo -e "${GREEN}✅ GCP project setup complete${NC}"
}

# Create GCS buckets
create_gcs_buckets() {
    echo -e "\n${YELLOW}🪣 Creating GCS buckets...${NC}"
    
    # Create main storage bucket
    if ! gsutil ls -b "gs://${STORAGE_BUCKET}" &> /dev/null; then
        echo "Creating main storage bucket: ${STORAGE_BUCKET}"
        gsutil mb -l "${REGION}" "gs://${STORAGE_BUCKET}"
        
        # Set bucket permissions
        gsutil iam ch allUsers:objectViewer "gs://${STORAGE_BUCKET}"
    else
        echo "Main storage bucket already exists: ${STORAGE_BUCKET}"
    fi
    
    # Create backup bucket
    if ! gsutil ls -b "gs://${BACKUP_BUCKET}" &> /dev/null; then
        echo "Creating backup bucket: ${BACKUP_BUCKET}"
        gsutil mb -l "${REGION}" "gs://${BACKUP_BUCKET}"
    else
        echo "Backup bucket already exists: ${BACKUP_BUCKET}"
    fi
    
    echo -e "${GREEN}✅ GCS buckets created successfully${NC}"
}

# Create GKE cluster
create_gke_cluster() {
    echo -e "\n${YELLOW}☸️  Creating GKE cluster...${NC}"
    
    # Check if cluster exists
    if ! gcloud container clusters describe "${CLUSTER_NAME}" --region="${REGION}" &> /dev/null; then
        echo "Creating GKE cluster: ${CLUSTER_NAME}"
        gcloud container clusters create "${CLUSTER_NAME}" \
            --region="${REGION}" \
            --num-nodes=3 \
            --machine-type=e2-medium \
            --disk-size=50GB \
            --disk-type=pd-standard \
            --enable-autoscaling \
            --min-nodes=1 \
            --max-nodes=5 \
            --enable-autorepair \
            --enable-autoupgrade \
            --enable-ip-alias \
            --network="default" \
            --subnetwork="default" \
            --enable-stackdriver-kubernetes \
            --enable-network-policy
    else
        echo "GKE cluster already exists: ${CLUSTER_NAME}"
    fi
    
    # Get cluster credentials
    echo "Getting cluster credentials..."
    gcloud container clusters get-credentials "${CLUSTER_NAME}" --region="${REGION}"
    
    echo -e "${GREEN}✅ GKE cluster created and configured${NC}"
}

# Create service account and key
create_service_account() {
    echo -e "\n${YELLOW}👤 Creating service account...${NC}"
    
    SERVICE_ACCOUNT_NAME="med-predictor-k8s-sa"
    
    # Check if service account exists
    if ! gcloud iam service-accounts describe "${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" &> /dev/null; then
        echo "Creating service account: ${SERVICE_ACCOUNT_NAME}"
        gcloud iam service-accounts create "${SERVICE_ACCOUNT_NAME}" \
            --display-name="Med-Predictor K8s Service Account" \
            --description="Service account for Med-Predictor Kubernetes deployment"
    else
        echo "Service account already exists: ${SERVICE_ACCOUNT_NAME}"
    fi
    
    # Grant required permissions
    echo "Granting permissions..."
    gcloud projects add-iam-policy-binding "${PROJECT_ID}" \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="roles/storage.objectAdmin"
    
    gcloud projects add-iam-policy-binding "${PROJECT_ID}" \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="roles/storage.bucketAdmin"
    
    gcloud projects add-iam-policy-binding "${PROJECT_ID}" \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="roles/container.developer"
    
    # Generate service account key
    KEY_FILE="k8s-service-account-key.json"
    if [ ! -f "${KEY_FILE}" ]; then
        echo "Generating service account key..."
        gcloud iam service-accounts keys create "${KEY_FILE}" \
            --iam-account="${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"
    fi
    
    echo -e "${GREEN}✅ Service account created and configured${NC}"
}

# Create Kubernetes namespace
create_namespace() {
    echo -e "\n${YELLOW}📦 Creating Kubernetes namespace...${NC}"
    
    kubectl create namespace "${NAMESPACE}" --dry-run=client -o yaml | kubectl apply -f -
    
    echo -e "${GREEN}✅ Namespace created: ${NAMESPACE}${NC}"
}

# Deploy GCS storage configuration
deploy_gcs_storage() {
    echo -e "\n${YELLOW}💾 Deploying GCS storage configuration...${NC}"
    
    # Update the secret with the service account key
    KEY_FILE="k8s-service-account-key.json"
    if [ -f "${KEY_FILE}" ]; then
        # Encode the service account key
        SERVICE_ACCOUNT_KEY_B64=$(cat "${KEY_FILE}" | base64 -w 0)
        
        # Update the secret in the YAML file
        sed -i "s/service-account-key: \"\"/service-account-key: \"${SERVICE_ACCOUNT_KEY_B64}\"/" deploy/k8s/gcp/gcs-storage.yaml
    fi
    
    # Apply the GCS storage configuration
    kubectl apply -f deploy/k8s/gcp/gcs-storage.yaml
    
    echo -e "${GREEN}✅ GCS storage configuration deployed${NC}"
}

# Deploy the main application
deploy_application() {
    echo -e "\n${YELLOW}🚀 Deploying application...${NC}"
    
    # Apply the main deployment
    kubectl apply -f deploy/k8s/gcp/fit-production-deployment.yaml
    
    # Apply the database configuration
    kubectl apply -f deploy/k8s/gcp/fit-production-database.yaml
    
    # Apply the Redis configuration
    kubectl apply -f deploy/k8s/gcp/fit-production-redis.yaml
    
    # Apply the ingress configuration
    kubectl apply -f deploy/k8s/gcp/fit-production-ingress.yaml
    
    echo -e "${GREEN}✅ Application deployed successfully${NC}"
}

# Wait for deployment to be ready
wait_for_deployment() {
    echo -e "\n${YELLOW}⏳ Waiting for deployment to be ready...${NC}"
    
    # Wait for pods to be ready
    kubectl wait --for=condition=ready pod -l app=med-predictor --namespace="${NAMESPACE}" --timeout=300s
    
    echo -e "${GREEN}✅ Deployment is ready${NC}"
}

# Get deployment information
get_deployment_info() {
    echo -e "\n${YELLOW}📊 Deployment Information${NC}"
    echo "=========================="
    
    # Get service URLs
    echo -e "${BLUE}Service URLs:${NC}"
    kubectl get services --namespace="${NAMESPACE}"
    
    # Get ingress information
    echo -e "\n${BLUE}Ingress Information:${NC}"
    kubectl get ingress --namespace="${NAMESPACE}"
    
    # Get pod status
    echo -e "\n${BLUE}Pod Status:${NC}"
    kubectl get pods --namespace="${NAMESPACE}"
    
    # Get storage information
    echo -e "\n${BLUE}Storage Information:${NC}"
    kubectl get pvc --namespace="${NAMESPACE}"
    
    echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
}

# Test the deployment
test_deployment() {
    echo -e "\n${YELLOW}🧪 Testing deployment...${NC}"
    
    # Get the external IP
    EXTERNAL_IP=$(kubectl get ingress med-predictor-ingress --namespace="${NAMESPACE}" -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "")
    
    if [ -n "${EXTERNAL_IP}" ]; then
        echo "Testing application at: http://${EXTERNAL_IP}"
        
        # Test health endpoint
        if curl -f "http://${EXTERNAL_IP}/health" &> /dev/null; then
            echo -e "${GREEN}✅ Application is responding${NC}"
        else
            echo -e "${YELLOW}⚠️  Application may still be starting up${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  External IP not available yet${NC}"
    fi
    
    # Test GCS integration
    echo "Testing GCS integration..."
    kubectl exec -it deployment/med-predictor-deployment --namespace="${NAMESPACE}" -- php artisan tinker --execute="
        use App\Services\GcsService;
        try {
            \$gcsService = app(GcsService::class);
            \$stats = \$gcsService->getStorageStats();
            echo 'SUCCESS: GCS integration working - Files: ' . \$stats['public_files_count'];
        } catch (Exception \$e) {
            echo 'ERROR: ' . \$e->getMessage();
        }
    "
}

# Main deployment function
main() {
    echo -e "${BLUE}Starting Google Cloud Storage + Kubernetes deployment...${NC}"
    
    check_prerequisites
    setup_project
    create_gcs_buckets
    create_gke_cluster
    create_service_account
    create_namespace
    deploy_gcs_storage
    deploy_application
    wait_for_deployment
    get_deployment_info
    test_deployment
    
    echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${BLUE}Next steps:${NC}"
    echo -e "${YELLOW}1. Configure your domain name to point to the external IP${NC}"
    echo -e "${YELLOW}2. Set up SSL certificates${NC}"
    echo -e "${YELLOW}3. Configure monitoring and logging${NC}"
    echo -e "${YELLOW}4. Test file uploads through the application${NC}"
}

# Run main function
main "$@"

