#!/bin/bash

echo "🚀 Déploiement FIT en une commande !"

# Vérifier les prérequis
command -v docker >/dev/null 2>&1 || { echo "❌ Docker requis"; exit 1; }
command -v kubectl >/dev/null 2>&1 || { echo "❌ kubectl requis"; exit 1; }

# 1. Générer les secrets
echo "🔐 Génération des secrets..."
./scripts/generate-secrets.sh

# 2. Créer le namespace
echo "📁 Création du namespace..."
kubectl apply -f deploy/k8s/fit-namespace.yaml

# 3. Appliquer les secrets
echo "🔑 Application des secrets..."
kubectl apply -f deploy/k8s/fit-secrets-generated.yaml

# 4. Déployer l'application
echo "☸️ Déploiement de l'application..."
kubectl apply -f deploy/k8s/fit-deployment.yaml
kubectl apply -f deploy/k8s/fit-database.yaml

# 5. Attendre que tout soit prêt
echo "⏳ Attente du déploiement..."
kubectl wait --for=condition=ready pod -l app=fit-app -n fit-production --timeout=300s

echo "✅ FIT déployé avec succès !"
echo "🌐 Accédez à: https://fit.yourdomain.com"
echo "📊 Status: kubectl get pods -n fit-production"
















