#!/bin/bash

# Script de déploiement AWS pour FIT Platform
# Usage: ./scripts/deploy-aws.sh [staging|production]

set -e

# Configuration
ENVIRONMENT=${1:-staging}
PROJECT_NAME="fit-platform"
REGION="eu-west-3"
CLUSTER_NAME="${PROJECT_NAME}-${ENVIRONMENT}"
SERVICE_NAME="${PROJECT_NAME}-app"
TASK_FAMILY="${PROJECT_NAME}-task"
REPOSITORY_NAME="${PROJECT_NAME}-${ENVIRONMENT}"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Déploiement AWS FIT Platform - ${ENVIRONMENT}${NC}"
echo "=================================================="

# Vérification des prérequis
check_prerequisites() {
    echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    if ! command -v aws &> /dev/null; then
        echo -e "${RED}❌ AWS CLI non installé${NC}"
        exit 1
    fi
    
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker non installé${NC}"
        exit 1
    fi
    
    if ! aws sts get-caller-identity &> /dev/null; then
        echo -e "${RED}❌ AWS non configuré${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
}

# Configuration AWS
configure_aws() {
    echo -e "${YELLOW}⚙️  Configuration AWS...${NC}"
    
    aws configure set default.region $REGION
    
    # Création du bucket S3 pour les logs et artefacts
    BUCKET_NAME="${PROJECT_NAME}-${ENVIRONMENT}-$(date +%Y%m%d)"
    aws s3 mb s3://$BUCKET_NAME --region $REGION || echo "Bucket existe déjà"
    
    echo -e "${GREEN}✅ AWS configuré${NC}"
}

# Construction et push de l'image Docker
build_and_push_image() {
    echo -e "${YELLOW}🐳 Construction et push de l'image Docker...${NC}"
    
    # Login à ECR
    aws ecr get-login-password --region $REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$REGION.amazonaws.com
    
    # Création du repository ECR
    aws ecr create-repository --repository-name $REPOSITORY_NAME --region $REGION || echo "Repository existe déjà"
    
    # Construction de l'image
    docker build -t $REPOSITORY_NAME .
    
    # Tag et push
    REPOSITORY_URI=$(aws ecr describe-repositories --repository-names $REPOSITORY_NAME --region $REGION --query 'repositories[0].repositoryUri' --output text)
    docker tag $REPOSITORY_NAME:latest $REPOSITORY_URI:latest
    docker push $REPOSITORY_URI:latest
    
    echo -e "${GREEN}✅ Image Docker poussée: $REPOSITORY_URI:latest${NC}"
}

# Création du cluster ECS
create_ecs_cluster() {
    echo -e "${YELLOW}🏗️  Création du cluster ECS...${NC}"
    
    aws ecs create-cluster \
        --cluster-name $CLUSTER_NAME \
        --capacity-providers FARGATE \
        --default-capacity-provider-strategy capacityProvider=FARGATE,weight=1 \
        --region $REGION || echo "Cluster existe déjà"
    
    echo -e "${GREEN}✅ Cluster ECS créé: $CLUSTER_NAME${NC}"
}

# Création de la base de données RDS
create_rds_database() {
    echo -e "${YELLOW}🗄️  Création de la base de données RDS...${NC}"
    
    DB_INSTANCE_ID="${PROJECT_NAME}-${ENVIRONMENT}-db"
    DB_NAME="fit_platform_${ENVIRONMENT}"
    
    # Création du subnet group
    aws rds create-db-subnet-group \
        --db-subnet-group-name "${PROJECT_NAME}-${ENVIRONMENT}-subnet-group" \
        --db-subnet-group-description "Subnet group for ${PROJECT_NAME} ${ENVIRONMENT}" \
        --subnet-ids subnet-12345678 subnet-87654321 \
        --region $REGION || echo "Subnet group existe déjà"
    
    # Création de l'instance RDS
    aws rds create-db-instance \
        --db-instance-identifier $DB_INSTANCE_ID \
        --db-instance-class db.t3.micro \
        --engine mysql \
        --engine-version 8.0.28 \
        --master-username admin \
        --master-user-password "SecurePassword123!" \
        --db-name $DB_NAME \
        --allocated-storage 20 \
        --storage-type gp2 \
        --vpc-security-group-ids sg-12345678 \
        --db-subnet-group-name "${PROJECT_NAME}-${ENVIRONMENT}-subnet-group" \
        --backup-retention-period 7 \
        --region $REGION || echo "Instance RDS existe déjà"
    
    echo -e "${GREEN}✅ Base de données RDS créée: $DB_INSTANCE_ID${NC}"
}

# Création de la définition de tâche ECS
create_task_definition() {
    echo -e "${YELLOW}📋 Création de la définition de tâche...${NC}"
    
    REPOSITORY_URI=$(aws ecr describe-repositories --repository-names $REPOSITORY_NAME --region $REGION --query 'repositories[0].repositoryUri' --output text)
    
    cat > task-definition.json << EOF
{
    "family": "$TASK_FAMILY",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "256",
    "memory": "512",
    "executionRoleArn": "arn:aws:iam::${AWS_ACCOUNT_ID}:role/ecsTaskExecutionRole",
    "taskRoleArn": "arn:aws:iam::${AWS_ACCOUNT_ID}:role/ecsTaskRole",
    "containerDefinitions": [
        {
            "name": "$SERVICE_NAME",
            "image": "$REPOSITORY_URI:latest",
            "portMappings": [
                {
                    "containerPort": 9000,
                    "protocol": "tcp"
                }
            ],
            "environment": [
                {
                    "name": "APP_ENV",
                    "value": "$ENVIRONMENT"
                },
                {
                    "name": "DB_CONNECTION",
                    "value": "mysql"
                },
                {
                    "name": "DB_HOST",
                    "value": "${PROJECT_NAME}-${ENVIRONMENT}-db.${REGION}.rds.amazonaws.com"
                },
                {
                    "name": "DB_DATABASE",
                    "value": "fit_platform_${ENVIRONMENT}"
                },
                {
                    "name": "DB_USERNAME",
                    "value": "admin"
                },
                {
                    "name": "DB_PASSWORD",
                    "value": "SecurePassword123!"
                }
            ],
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/$TASK_FAMILY",
                    "awslogs-region": "$REGION",
                    "awslogs-stream-prefix": "ecs"
                }
            }
        }
    ]
}
EOF
    
    aws ecs register-task-definition --cli-input-json file://task-definition.json --region $REGION
    
    echo -e "${GREEN}✅ Définition de tâche créée: $TASK_FAMILY${NC}"
}

# Création du service ECS
create_ecs_service() {
    echo -e "${YELLOW}🚀 Création du service ECS...${NC}"
    
    # Création du load balancer
    aws elbv2 create-load-balancer \
        --name "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
        --subnets subnet-12345678 subnet-87654321 \
        --security-groups sg-12345678 \
        --region $REGION || echo "Load balancer existe déjà"
    
    # Création du service
    aws ecs create-service \
        --cluster $CLUSTER_NAME \
        --service-name $SERVICE_NAME \
        --task-definition $TASK_FAMILY \
        --desired-count 2 \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[subnet-12345678,subnet-87654321],securityGroups=[sg-12345678],assignPublicIp=ENABLED}" \
        --region $REGION || echo "Service existe déjà"
    
    echo -e "${GREEN}✅ Service ECS créé: $SERVICE_NAME${NC}"
}

# Configuration du monitoring
setup_monitoring() {
    echo -e "${YELLOW}📊 Configuration du monitoring...${NC}"
    
    # Création du dashboard CloudWatch
    aws cloudwatch put-dashboard \
        --dashboard-name "${PROJECT_NAME}-${ENVIRONMENT}-dashboard" \
        --dashboard-body file://cloudwatch-dashboard.json \
        --region $REGION || echo "Dashboard existe déjà"
    
    # Configuration des alertes
    aws cloudwatch put-metric-alarm \
        --alarm-name "${PROJECT_NAME}-${ENVIRONMENT}-cpu-high" \
        --alarm-description "CPU usage is high" \
        --metric-name CPUUtilization \
        --namespace AWS/ECS \
        --statistic Average \
        --period 300 \
        --threshold 80 \
        --comparison-operator GreaterThanThreshold \
        --evaluation-periods 2 \
        --region $REGION || echo "Alarme existe déjà"
    
    echo -e "${GREEN}✅ Monitoring configuré${NC}"
}

# Déploiement final
deploy() {
    echo -e "${YELLOW}🚀 Déploiement en cours...${NC}"
    
    # Mise à jour du service
    aws ecs update-service \
        --cluster $CLUSTER_NAME \
        --service $SERVICE_NAME \
        --force-new-deployment \
        --region $REGION
    
    # Attente du déploiement
    echo "⏳ Attente du déploiement..."
    aws ecs wait services-stable \
        --cluster $CLUSTER_NAME \
        --services $SERVICE_NAME \
        --region $REGION
    
    echo -e "${GREEN}✅ Déploiement terminé avec succès !${NC}"
}

# Fonction principale
main() {
    check_prerequisites
    configure_aws
    build_and_push_image
    create_ecs_cluster
    create_rds_database
    create_task_definition
    create_ecs_service
    setup_monitoring
    deploy
    
    echo -e "${GREEN}🎉 FIT Platform déployé avec succès sur AWS !${NC}"
    echo -e "${BLUE}🌐 URL: http://${PROJECT_NAME}-${ENVIRONMENT}-alb.${REGION}.elb.amazonaws.com${NC}"
    echo -e "${BLUE}📊 Dashboard: https://${REGION}.console.aws.amazon.com/cloudwatch/home?region=${REGION}#dashboards:name=${PROJECT_NAME}-${ENVIRONMENT}-dashboard${NC}"
}

# Exécution
main "$@"









