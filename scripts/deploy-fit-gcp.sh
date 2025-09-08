#!/bin/bash

# 🚀 Script de Déploiement FIT sur Google Cloud Platform
# Domaine: fit3.tbhc.uk

set -e  # Arrêter en cas d'erreur

# Configuration
PROJECT_ID="med-predictor-fit"  # Projet GCP avec facturation activée
DOMAIN="fit3.tbhc.uk"
ZONE="us-central1-a"
CLUSTER_NAME="fit-cluster"
NAMESPACE="fit-production"
IMAGE_TAG="v1.0.0"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonctions utilitaires
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

check_command() {
    if ! command -v $1 &> /dev/null; then
        log_error "$1 n'est pas installé. Veuillez l'installer d'abord."
        exit 1
    fi
}

# Vérification des prérequis
log_info "Vérification des prérequis..."
check_command gcloud
check_command kubectl
check_command docker

# Configuration GCP
log_info "Configuration de Google Cloud Platform..."
gcloud config set project $PROJECT_ID
gcloud config set compute/zone $ZONE

# Vérifier si le cluster existe
if ! gcloud container clusters describe $CLUSTER_NAME --zone=$ZONE &> /dev/null; then
    log_info "Création du cluster GKE..."
    gcloud container clusters create $CLUSTER_NAME \
        --zone=$ZONE \
        --num-nodes=1 \
        --machine-type=e2-small \
        --disk-size=50GB \
        --disk-type=pd-standard \
        --enable-autoscaling \
        --min-nodes=1 \
        --max-nodes=3 \
        --enable-network-policy \
        --enable-ip-alias \
        --enable-autorepair \
        --enable-autoupgrade
else
    log_info "Cluster GKE existant détecté."
fi

# Configuration kubectl
log_info "Configuration de kubectl..."
gcloud container clusters get-credentials $CLUSTER_NAME --zone=$ZONE

# Vérifier si le namespace existe
if ! kubectl get namespace $NAMESPACE &> /dev/null; then
    log_info "Création du namespace $NAMESPACE..."
    kubectl create namespace $NAMESPACE
else
    log_info "Namespace $NAMESPACE existe déjà."
fi

# Build et push de l'image Docker
log_info "Build de l'image Docker..."
docker build -f Dockerfile.simple -t gcr.io/$PROJECT_ID/fit-app:$IMAGE_TAG .
docker tag gcr.io/$PROJECT_ID/fit-app:$IMAGE_TAG gcr.io/$PROJECT_ID/fit-app:latest

log_info "Push de l'image vers Google Container Registry..."
docker push gcr.io/$PROJECT_ID/fit-app:$IMAGE_TAG
docker push gcr.io/$PROJECT_ID/fit-app:latest

# Configuration des secrets
log_info "Génération des secrets..."
./scripts/generate-secrets.sh

# Mise à jour de l'image dans le manifest
log_info "Mise à jour du manifest avec l'image GCR..."
sed -i.bak "s|gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.0|gcr.io/$PROJECT_ID/fit-app:$IMAGE_TAG|g" deploy/k8s/gcp/fit-production-deployment.yaml

# Déploiement de l'application
log_info "Déploiement de l'application FIT..."
kubectl apply -f deploy/k8s/gcp/fit-production-deployment.yaml
kubectl apply -f deploy/k8s/gcp/fit-production-database.yaml
kubectl apply -f deploy/k8s/gcp/fit-production-redis.yaml

# Installation de cert-manager si pas déjà installé
if ! kubectl get namespace cert-manager &> /dev/null; then
    log_info "Installation de cert-manager..."
    kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml
    
    # Attendre que cert-manager soit prêt
    log_info "Attente de cert-manager..."
    kubectl wait --for=condition=ready pod -l app.kubernetes.io/instance=cert-manager -n cert-manager --timeout=300s
fi

# Création du ClusterIssuer Let's Encrypt
log_info "Configuration du ClusterIssuer Let's Encrypt..."
cat <<EOF | kubectl apply -f -
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: admin@tbhc.uk
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF

# Installation de l'Ingress Controller si pas déjà installé
if ! kubectl get namespace ingress-nginx &> /dev/null; then
    log_info "Installation de l'Ingress Controller Nginx..."
    kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.0/deploy/static/provider/cloud/deploy.yaml
    
    # Attendre que l'Ingress Controller soit prêt
    log_info "Attente de l'Ingress Controller..."
    kubectl wait --for=condition=ready pod -l app.kubernetes.io/name=ingress-nginx -n ingress-nginx --timeout=300s
fi

# Déploiement de l'Ingress
log_info "Déploiement de l'Ingress..."
kubectl apply -f deploy/k8s/gcp/fit-production-ingress.yaml

# Attendre que l'application soit prête
log_info "Attente du déploiement de l'application..."
kubectl wait --for=condition=ready pod -l app=fit-app -n $NAMESPACE --timeout=600s

# Vérification finale
log_info "Vérification du déploiement..."
kubectl get pods -n $NAMESPACE
kubectl get services -n $NAMESPACE
kubectl get ingress -n $NAMESPACE

# Affichage des informations de connexion
log_success "🎉 Déploiement FIT terminé avec succès !"
echo ""
echo "🌐 Application accessible sur: https://$DOMAIN"
echo "📊 Dashboard Kubernetes: kubectl proxy"
echo "📝 Logs de l'application: kubectl logs -f deployment/fit-app -n $NAMESPACE"
echo "🔍 Statut des pods: kubectl get pods -n $NAMESPACE"
echo "🌍 Services: kubectl get services -n $NAMESPACE"
echo "🚪 Ingress: kubectl get ingress -n $NAMESPACE"
echo ""
log_info "Le certificat SSL sera généré automatiquement par Let's Encrypt dans les prochaines minutes."
log_info "Vérifiez le statut avec: kubectl get certificate -n $NAMESPACE"

























