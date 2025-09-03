#!/bin/bash

# Script de déploiement Kubernetes pour FIT Platform
# Usage: ./scripts/deploy-k8s.sh [staging|production]

set -e

# Configuration
ENVIRONMENT=${1:-staging}
PROJECT_NAME="fit-platform"
NAMESPACE="${PROJECT_NAME}-${ENVIRONMENT}"
K8S_DIR="k8s"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Déploiement Kubernetes FIT Platform - ${ENVIRONMENT}${NC}"
echo "=================================================="

# Vérification des prérequis
check_prerequisites() {
    echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    if ! command -v kubectl &> /dev/null; then
        echo -e "${RED}❌ kubectl n'est pas installé${NC}"
        echo "Installez kubectl: https://kubernetes.io/docs/tasks/tools/"
        exit 1
    fi
    
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker n'est pas installé${NC}"
        echo "Installez Docker: https://docs.docker.com/get-docker/"
        exit 1
    fi
    
    # Vérifier la connexion au cluster
    if ! kubectl cluster-info &> /dev/null; then
        echo -e "${RED}❌ Impossible de se connecter au cluster Kubernetes${NC}"
        echo "Vérifiez votre configuration kubectl et la connexion au cluster"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
}

# Création du namespace
create_namespace() {
    echo -e "${YELLOW}🏗️  Création du namespace ${NAMESPACE}...${NC}"
    
    if kubectl get namespace $NAMESPACE &> /dev/null; then
        echo -e "${YELLOW}⚠️  Le namespace ${NAMESPACE} existe déjà${NC}"
    else
        kubectl create namespace $NAMESPACE
        echo -e "${GREEN}✅ Namespace ${NAMESPACE} créé${NC}"
    fi
}

# Construction de l'image Docker
build_docker_image() {
    echo -e "${YELLOW}🐳 Construction de l'image Docker...${NC}"
    
    # Tag de l'image
    IMAGE_TAG="${PROJECT_NAME}:${ENVIRONMENT}-$(date +%Y%m%d-%H%M%S)"
    
    echo "Construction de l'image: $IMAGE_TAG"
    docker build -t $IMAGE_TAG .
    
    # Si on utilise un registry distant
    if [ ! -z "$DOCKER_REGISTRY" ]; then
        REMOTE_IMAGE="${DOCKER_REGISTRY}/${PROJECT_NAME}:${ENVIRONMENT}-$(date +%Y%m%d-%H%M%S)"
        docker tag $IMAGE_TAG $REMOTE_IMAGE
        docker push $REMOTE_IMAGE
        IMAGE_TAG=$REMOTE_IMAGE
    fi
    
    echo -e "${GREEN}✅ Image Docker construite: $IMAGE_TAG${NC}"
}

# Mise à jour des secrets
update_secrets() {
    echo -e "${YELLOW}🔐 Mise à jour des secrets...${NC}"
    
    # Générer un mot de passe root MySQL sécurisé
    MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32)
    
    # Créer le secret avec les mots de passe
    kubectl create secret generic fit-platform-secrets \
        --namespace=$NAMESPACE \
        --from-literal=db-username=med_user \
        --from-literal=db-password=med_password \
        --from-literal=db-root-password=$MYSQL_ROOT_PASSWORD \
        --from-literal=redis-password=redis_password \
        --from-literal=app-key=base64_encoded_app_key \
        --dry-run=client -o yaml | kubectl apply -f -
    
    echo -e "${GREEN}✅ Secrets mis à jour${NC}"
}

# Déploiement des composants
deploy_components() {
    echo -e "${YELLOW}🚀 Déploiement des composants...${NC}"
    
    # Déployer la base de données et Redis
    echo "Déploiement de MySQL et Redis..."
    kubectl apply -f $K8S_DIR/database.yaml -n $NAMESPACE
    
    # Attendre que MySQL soit prêt
    echo "Attente que MySQL soit prêt..."
    kubectl wait --for=condition=ready pod -l app=fit-platform,tier=database -n $NAMESPACE --timeout=300s
    
    # Déployer l'application
    echo "Déploiement de l'application..."
    kubectl apply -f $K8S_DIR/deployment.yaml -n $NAMESPACE
    
    # Déployer les services
    echo "Déploiement des services..."
    kubectl apply -f $K8S_DIR/services.yaml -n $NAMESPACE
    
    # Déployer l'ingress et HPA
    echo "Déploiement de l'ingress et HPA..."
    kubectl apply -f $K8S_DIR/ingress.yaml -n $NAMESPACE
    
    echo -e "${GREEN}✅ Composants déployés${NC}"
}

# Vérification du déploiement
verify_deployment() {
    echo -e "${YELLOW}🔍 Vérification du déploiement...${NC}"
    
    # Attendre que tous les pods soient prêts
    echo "Attente que tous les pods soient prêts..."
    kubectl wait --for=condition=ready pod -l app=fit-platform -n $NAMESPACE --timeout=600s
    
    # Vérifier l'état des déploiements
    echo "Vérification des déploiements..."
    kubectl get deployments -n $NAMESPACE
    
    # Vérifier l'état des services
    echo "Vérification des services..."
    kubectl get services -n $NAMESPACE
    
    # Vérifier l'état des pods
    echo "Vérification des pods..."
    kubectl get pods -n $NAMESPACE
    
    echo -e "${GREEN}✅ Déploiement vérifié${NC}"
}

# Test des endpoints de santé
test_health_endpoints() {
    echo -e "${YELLOW}🏥 Test des endpoints de santé...${NC}"
    
    # Obtenir l'IP du service nginx
    NGINX_IP=$(kubectl get service fit-platform-nginx-service -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}')
    
    if [ -z "$NGINX_IP" ]; then
        echo -e "${YELLOW}⚠️  Service LoadBalancer non encore assigné, utilisation du port-forward...${NC}"
        
        # Port-forward pour les tests
        kubectl port-forward service/fit-platform-nginx-service 8080:80 -n $NAMESPACE &
        PF_PID=$!
        sleep 5
        
        # Test des endpoints
        echo "Test de /health..."
        if curl -s http://localhost:8080/health | grep -q "healthy"; then
            echo -e "${GREEN}✅ Endpoint /health fonctionne${NC}"
        else
            echo -e "${RED}❌ Endpoint /health ne fonctionne pas${NC}"
        fi
        
        echo "Test de /ready..."
        if curl -s http://localhost:8080/ready | grep -q "ready"; then
            echo -e "${GREEN}✅ Endpoint /ready fonctionne${NC}"
        else
            echo -e "${RED}❌ Endpoint /ready ne fonctionne pas${NC}"
        fi
        
        # Arrêter le port-forward
        kill $PF_PID
    else
        echo "Test des endpoints via LoadBalancer IP: $NGINX_IP"
        # Tests via l'IP du LoadBalancer
        curl -s http://$NGINX_IP/health
        curl -s http://$NGINX_IP/ready
    fi
}

# Affichage des informations de connexion
show_connection_info() {
    echo -e "${BLUE}📋 Informations de connexion${NC}"
    echo "=================================================="
    echo "Namespace: $NAMESPACE"
    echo "Services:"
    kubectl get services -n $NAMESPACE
    
    echo ""
    echo "Pods:"
    kubectl get pods -n $NAMESPACE
    
    echo ""
    echo "Pour accéder aux logs:"
    echo "kubectl logs -f deployment/fit-platform-app -n $NAMESPACE"
    echo "kubectl logs -f deployment/fit-platform-nginx -n $NAMESPACE"
    
    echo ""
    echo "Pour accéder au shell d'un pod:"
    echo "kubectl exec -it deployment/fit-platform-app -n $NAMESPACE -- /bin/bash"
}

# Fonction principale
main() {
    check_prerequisites
    create_namespace
    build_docker_image
    update_secrets
    deploy_components
    verify_deployment
    test_health_endpoints
    show_connection_info
    
    echo -e "${GREEN}🎉 Déploiement Kubernetes terminé avec succès !${NC}"
    echo ""
    echo "Prochaines étapes:"
    echo "1. Configurer votre DNS pour pointer vers l'IP du LoadBalancer"
    echo "2. Configurer cert-manager pour les certificats SSL"
    echo "3. Configurer le monitoring et les alertes"
    echo "4. Tester les fonctionnalités de l'application"
}

# Gestion des erreurs
trap 'echo -e "${RED}❌ Erreur lors du déploiement${NC}"; exit 1' ERR

# Exécution
main "$@"










