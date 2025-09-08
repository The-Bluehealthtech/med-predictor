#!/bin/bash

# Script de déploiement Terraform pour FIT Platform
# Usage: ./scripts/deploy-terraform.sh [staging|production]

set -e

# Configuration
ENVIRONMENT=${1:-staging}
PROJECT_NAME="fit-platform"
REGION="eu-west-3"
TERRAFORM_DIR="terraform"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Déploiement Terraform FIT Platform - ${ENVIRONMENT}${NC}"
echo "=================================================="

# Vérification des prérequis
check_prerequisites() {
    echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    if ! command -v terraform &> /dev/null; then
        echo -e "${RED}❌ Terraform n'est pas installé${NC}"
        echo "Installez Terraform: https://www.terraform.io/downloads.html"
        exit 1
    fi
    
    if ! command -v aws &> /dev/null; then
        echo -e "${RED}❌ AWS CLI n'est pas installé${NC}"
        echo "Installez AWS CLI: https://aws.amazon.com/cli/"
        exit 1
    fi
    
    if ! aws sts get-caller-identity &> /dev/null; then
        echo -e "${RED}❌ AWS CLI n'est pas configuré${NC}"
        echo "Configurez AWS CLI: aws configure"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
}

# Initialisation Terraform
init_terraform() {
    echo -e "${YELLOW}🔧 Initialisation Terraform...${NC}"
    
    cd $TERRAFORM_DIR
    
    # Création du fichier terraform.tfvars
    cat > terraform.tfvars << EOF
aws_region = "$REGION"
project_name = "$PROJECT_NAME"
environment = "$ENVIRONMENT"
container_image = "$PROJECT_NAME:$ENVIRONMENT"
EOF
    
    terraform init
    echo -e "${GREEN}✅ Terraform initialisé${NC}"
}

# Planification du déploiement
plan_deployment() {
    echo -e "${YELLOW}📋 Planification du déploiement...${NC}"
    
    terraform plan -var-file="terraform.tfvars" -out=tfplan
    
    echo -e "${GREEN}✅ Planification terminée${NC}"
    echo -e "${YELLOW}📋 Vérifiez le plan ci-dessus et confirmez le déploiement${NC}"
    
    read -p "Continuer avec le déploiement ? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}⚠️ Déploiement annulé${NC}"
        exit 0
    fi
}

# Déploiement
deploy() {
    echo -e "${YELLOW}🚀 Déploiement en cours...${NC}"
    
    terraform apply tfplan
    
    echo -e "${GREEN}✅ Déploiement terminé${NC}"
}

# Affichage des informations
show_info() {
    echo -e "${GREEN}🎉 Déploiement réussi !${NC}"
    echo "=================================================="
    
    ALB_DNS=$(terraform output -raw alb_dns_name 2>/dev/null || echo "N/A")
    ECS_CLUSTER=$(terraform output -raw ecs_cluster_name 2>/dev/null || echo "N/A")
    RDS_ENDPOINT=$(terraform output -raw rds_endpoint 2>/dev/null || echo "N/A")
    
    echo -e "${BLUE}📊 Informations du déploiement:${NC}"
    echo "   Load Balancer: http://$ALB_DNS"
    echo "   ECS Cluster: $ECS_CLUSTER"
    echo "   RDS Endpoint: $RDS_ENDPOINT"
    echo ""
    echo -e "${YELLOW}🔍 Vérification de la santé:${NC}"
    echo "   curl http://$ALB_DNS/health"
    echo "   curl http://$ALB_DNS/ready"
}

# Nettoyage
cleanup() {
    echo -e "${YELLOW}🧹 Nettoyage...${NC}"
    rm -f tfplan
    cd ..
}

# Fonction principale
main() {
    check_prerequisites
    init_terraform
    plan_deployment
    deploy
    show_info
    cleanup
}

# Gestion des erreurs
trap 'echo -e "${RED}❌ Erreur lors du déploiement${NC}"; cleanup; exit 1' ERR

# Exécution
main












