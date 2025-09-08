#!/bin/bash

echo "🚀 Déploiement rapide de FIT..."

# Variables
VERSION=${1:-"v1.0.0"}
NAMESPACE="fit-production"
REGISTRY="your-registry"

# 1. Build de l'image Docker
echo "📦 Build de l'image Docker..."
docker build -f Dockerfile.optimized -t fit-app:$VERSION .

# 2. Test rapide de l'image
echo "🧪 Test rapide de l'image..."
docker run -d --name fit-test -p 9000:9000 fit-app:$VERSION
sleep 5
docker logs fit-test
docker stop fit-test && docker rm fit-test

# 3. Tag et push (optionnel)
echo "🏷️ Tag de l'image..."
docker tag fit-app:$VERSION $REGISTRY/fit-app:$VERSION

# 4. Déploiement Kubernetes
echo "☸️ Déploiement Kubernetes..."
kubectl create namespace $NAMESPACE --dry-run=client -o yaml | kubectl apply -f -

# Appliquer les manifests
kubectl apply -f deploy/k8s/fit-deployment.yaml
kubectl apply -f deploy/k8s/fit-database.yaml

# 5. Vérification rapide
echo "🔍 Vérification du déploiement..."
kubectl get pods -n $NAMESPACE
kubectl get services -n $NAMESPACE

echo "✅ Déploiement terminé !"
echo "🌐 Accédez à: https://fit.yourdomain.com"

























