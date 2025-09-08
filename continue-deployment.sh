#!/bin/bash

# 🚀 Script de Continuation du Déploiement FIT sur GCP
# Ce script vérifie l'état actuel et continue le déploiement

set -e

# Configuration
PROJECT_ID="med-predictor-fit"
CLUSTER_NAME="fit-cluster"
ZONE="us-central1-a"
NAMESPACE="fit-production"
DOMAIN="fit3.tbhc.uk"

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

echo "🚀 Continuation du déploiement FIT sur GCP..."
echo ""

# 1. Vérifier la configuration GCP
log_info "Vérification de la configuration GCP..."
gcloud config set project $PROJECT_ID
gcloud config set compute/zone $ZONE

# 2. Se connecter au cluster
log_info "Connexion au cluster GKE..."
gcloud container clusters get-credentials $CLUSTER_NAME --zone=$ZONE

# 3. Vérifier l'état des pods
log_info "Vérification de l'état des pods..."
echo ""
kubectl get pods -n $NAMESPACE
echo ""

# 4. Vérifier les services
log_info "Vérification des services..."
echo ""
kubectl get services -n $NAMESPACE
echo ""

# 5. Vérifier les PVCs
log_info "Vérification des PVCs..."
echo ""
kubectl get pvc -n $NAMESPACE
echo ""

# 6. Vérifier les secrets
log_info "Vérification des secrets..."
echo ""
kubectl get secrets -n $NAMESPACE
echo ""

# 7. Installer l'Ingress Controller si nécessaire
log_info "Vérification de l'Ingress Controller..."
if ! kubectl get namespace ingress-nginx &> /dev/null; then
    log_info "Installation de l'Ingress Controller Nginx..."
    kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.0/deploy/static/provider/cloud/deploy.yaml
    
    log_info "Attente de l'Ingress Controller..."
    kubectl wait --for=condition=ready pod -l app.kubernetes.io/name=ingress-nginx -n ingress-nginx --timeout=300s
else
    log_success "Ingress Controller déjà installé"
fi

# 8. Installer cert-manager si nécessaire
log_info "Vérification de cert-manager..."
if ! kubectl get namespace cert-manager &> /dev/null; then
    log_info "Installation de cert-manager..."
    kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml
    
    log_info "Attente de cert-manager..."
    kubectl wait --for=condition=ready pod -l app.kubernetes.io/instance=cert-manager -n cert-manager --timeout=300s
else
    log_success "cert-manager déjà installé"
fi

# 9. Créer le ClusterIssuer Let's Encrypt
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

# 10. Déployer l'Ingress
log_info "Déploiement de l'Ingress..."
kubectl apply -f deploy/k8s/gcp/fit-production-ingress.yaml

# 11. Attendre que l'application soit prête
log_info "Attente du déploiement de l'application..."
kubectl wait --for=condition=ready pod -l app=fit-app -n $NAMESPACE --timeout=600s || log_warning "Timeout atteint, vérifiez manuellement"

# 12. Vérification finale
log_info "Vérification finale du déploiement..."
echo ""
echo "📊 Statut des pods:"
kubectl get pods -n $NAMESPACE
echo ""
echo "🌍 Services:"
kubectl get services -n $NAMESPACE
echo ""
echo "🚪 Ingress:"
kubectl get ingress -n $NAMESPACE
echo ""

# 13. Obtenir l'IP externe
log_info "Récupération de l'IP externe..."
EXTERNAL_IP=$(kubectl get ingress fit-ingress -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "En cours de génération...")

if [ "$EXTERNAL_IP" != "En cours de génération..." ] && [ ! -z "$EXTERNAL_IP" ]; then
    log_success "🎉 Déploiement terminé avec succès !"
    echo ""
    echo "🌐 Application accessible sur: https://$DOMAIN"
    echo "📡 IP externe: $EXTERNAL_IP"
    echo "📝 Configurez votre DNS pour pointer vers: $EXTERNAL_IP"
    echo ""
    echo "🔍 Commandes utiles:"
    echo "  - Logs de l'application: kubectl logs -f deployment/fit-app -n $NAMESPACE"
    echo "  - Statut des pods: kubectl get pods -n $NAMESPACE"
    echo "  - Services: kubectl get services -n $NAMESPACE"
    echo "  - Ingress: kubectl get ingress -n $NAMESPACE"
    echo "  - Certificats SSL: kubectl get certificate -n $NAMESPACE"
else
    log_warning "L'IP externe n'est pas encore disponible. Vérifiez dans quelques minutes avec:"
    echo "kubectl get ingress fit-ingress -n $NAMESPACE"
fi

echo ""
log_info "Le certificat SSL sera généré automatiquement par Let's Encrypt dans les prochaines minutes."
log_info "Vérifiez le statut avec: kubectl get certificate -n $NAMESPACE"
