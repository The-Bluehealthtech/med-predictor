#!/bin/bash

# AWS Deployment Script for Med-Predictor
# This script deploys the application to AWS ECS/Fargate

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="med-predictor"
AWS_REGION="${AWS_REGION:-us-east-1}"
ECR_REPOSITORY="${ECR_REPOSITORY:-med-predictor}"
CLUSTER_NAME="${CLUSTER_NAME:-med-predictor-cluster}"
SERVICE_NAME="${SERVICE_NAME:-med-predictor-service}"
TASK_DEFINITION="${TASK_DEFINITION:-med-predictor-task}"

echo -e "${BLUE}🚀 AWS Deployment Script for Med-Predictor${NC}"
echo "=================================================="

# Check prerequisites
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check AWS CLI
    if ! command -v aws &> /dev/null; then
        echo -e "${RED}❌ AWS CLI is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker is not installed. Please install it first.${NC}"
        exit 1
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        echo -e "${RED}❌ AWS credentials not configured. Please run 'aws configure' first.${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ All prerequisites met${NC}"
}

# Build and push Docker image
build_and_push_image() {
    echo -e "\n${YELLOW}🔨 Building and pushing Docker image...${NC}"
    
    # Get AWS account ID
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    ECR_URI="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPOSITORY}"
    
    # Create ECR repository if it doesn't exist
    if ! aws ecr describe-repositories --repository-names "${ECR_REPOSITORY}" --region "${AWS_REGION}" &> /dev/null; then
        echo "Creating ECR repository: ${ECR_REPOSITORY}"
        aws ecr create-repository --repository-name "${ECR_REPOSITORY}" --region "${AWS_REGION}"
    fi
    
    # Get ECR login token
    echo "Logging in to ECR..."
    aws ecr get-login-password --region "${AWS_REGION}" | docker login --username AWS --password-stdin "${ECR_URI}"
    
    # Build image
    echo "Building Docker image..."
    docker build -t "${PROJECT_NAME}:latest" .
    
    # Tag and push image
    echo "Tagging and pushing image..."
    docker tag "${PROJECT_NAME}:latest" "${ECR_URI}:latest"
    docker push "${ECR_URI}:latest"
    
    echo -e "${GREEN}✅ Docker image pushed successfully${NC}"
}

# Create ECS cluster
create_cluster() {
    echo -e "\n${YELLOW}🏗️  Creating ECS cluster...${NC}"
    
    if ! aws ecs describe-clusters --clusters "${CLUSTER_NAME}" --region "${AWS_REGION}" &> /dev/null; then
        echo "Creating ECS cluster: ${CLUSTER_NAME}"
        aws ecs create-cluster \
            --cluster-name "${CLUSTER_NAME}" \
            --region "${AWS_REGION}" \
            --capacity-providers FARGATE \
            --default-capacity-provider-strategy capacityProvider=FARGATE,weight=1
    else
        echo "ECS cluster already exists: ${CLUSTER_NAME}"
    fi
    
    echo -e "${GREEN}✅ ECS cluster ready${NC}"
}

# Create task definition
create_task_definition() {
    echo -e "\n${YELLOW}📋 Creating ECS task definition...${NC}"
    
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    ECR_URI="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPOSITORY}"
    
    # Create task definition JSON
    cat > task-definition.json << EOF
{
    "family": "${TASK_DEFINITION}",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "512",
    "memory": "1024",
    "executionRoleArn": "arn:aws:iam::${AWS_ACCOUNT_ID}:role/ecsTaskExecutionRole",
    "taskRoleArn": "arn:aws:iam::${AWS_ACCOUNT_ID}:role/ecsTaskExecutionRole",
    "containerDefinitions": [
        {
            "name": "${PROJECT_NAME}",
            "image": "${ECR_URI}:latest",
            "portMappings": [
                {
                    "containerPort": 80,
                    "protocol": "tcp"
                }
            ],
            "essential": true,
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/${PROJECT_NAME}",
                    "awslogs-region": "${AWS_REGION}",
                    "awslogs-stream-prefix": "ecs"
                }
            },
            "environment": [
                {
                    "name": "APP_ENV",
                    "value": "production"
                },
                {
                    "name": "APP_DEBUG",
                    "value": "false"
                }
            ],
            "secrets": [
                {
                    "name": "DB_PASSWORD",
                    "valueFrom": "arn:aws:secretsmanager:${AWS_REGION}:${AWS_ACCOUNT_ID}:secret:med-predictor/db-password"
                }
            ]
        }
    ]
}
EOF
    
    # Register task definition
    aws ecs register-task-definition \
        --cli-input-json file://task-definition.json \
        --region "${AWS_REGION}"
    
    echo -e "${GREEN}✅ Task definition created${NC}"
}

# Create service
create_service() {
    echo -e "\n${YELLOW}🚀 Creating ECS service...${NC}"
    
    # Get default VPC and subnets
    VPC_ID=$(aws ec2 describe-vpcs --filters "Name=isDefault,Values=true" --query 'Vpcs[0].VpcId' --output text --region "${AWS_REGION}")
    SUBNET_IDS=$(aws ec2 describe-subnets --filters "Name=vpc-id,Values=${VPC_ID}" --query 'Subnets[0:2].SubnetId' --output text --region "${AWS_REGION}" | tr '\t' ',')
    
    # Create security group if it doesn't exist
    SG_NAME="${PROJECT_NAME}-sg"
    if ! aws ec2 describe-security-groups --filters "Name=group-name,Values=${SG_NAME}" --region "${AWS_REGION}" &> /dev/null; then
        echo "Creating security group: ${SG_NAME}"
        SG_ID=$(aws ec2 create-security-group \
            --group-name "${SG_NAME}" \
            --description "Security group for ${PROJECT_NAME}" \
            --vpc-id "${VPC_ID}" \
            --region "${AWS_REGION}" \
            --query 'GroupId' --output text)
        
        # Add inbound rules
        aws ec2 authorize-security-group-ingress \
            --group-id "${SG_ID}" \
            --protocol tcp \
            --port 80 \
            --cidr 0.0.0.0/0 \
            --region "${AWS_REGION}"
    else
        SG_ID=$(aws ec2 describe-security-groups --filters "Name=group-name,Values=${SG_NAME}" --query 'SecurityGroups[0].GroupId' --output text --region "${AWS_REGION}")
    fi
    
    # Create service
    if ! aws ecs describe-services --services "${SERVICE_NAME}" --cluster "${CLUSTER_NAME}" --region "${AWS_REGION}" &> /dev/null; then
        echo "Creating ECS service: ${SERVICE_NAME}"
        aws ecs create-service \
            --cluster "${CLUSTER_NAME}" \
            --service-name "${SERVICE_NAME}" \
            --task-definition "${TASK_DEFINITION}" \
            --desired-count 1 \
            --launch-type FARGATE \
            --network-configuration "awsvpcConfiguration={subnets=[${SUBNET_IDS}],securityGroups=[${SG_ID}],assignPublicIp=ENABLED}" \
            --region "${AWS_REGION}"
    else
        echo "ECS service already exists: ${SERVICE_NAME}"
    fi
    
    echo -e "${GREEN}✅ ECS service created${NC}"
}

# Main deployment function
main() {
    echo -e "${BLUE}Starting deployment to AWS...${NC}"
    
    check_prerequisites
    build_and_push_image
    create_cluster
    create_task_definition
    create_service
    
    echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${BLUE}Your application is now running on AWS ECS.${NC}"
    echo -e "${YELLOW}Note: You may need to create IAM roles and configure additional AWS services.${NC}"
}

# Run main function
main "$@"









