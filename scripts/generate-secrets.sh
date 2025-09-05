#!/bin/bash

echo "🔐 Génération des secrets Kubernetes pour FIT..."

# Générer les secrets
APP_KEY=$(php artisan key:generate --show | base64)
DB_PASSWORD=$(openssl rand -base64 32 | base64)
JWT_SECRET=$(openssl rand -base64 64 | base64)
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32 | base64)

# Créer le fichier de secrets
cat > deploy/k8s/fit-secrets-generated.yaml << EOF
apiVersion: v1
kind: Secret
metadata:
  name: fit-app-secrets
  namespace: fit-production
type: Opaque
data:
  APP_KEY: $APP_KEY
  DB_PASSWORD: $DB_PASSWORD
  JWT_SECRET: $JWT_SECRET
---
apiVersion: v1
kind: Secret
metadata:
  name: fit-mysql-secret
  namespace: fit-production
type: Opaque
data:
  root-password: $MYSQL_ROOT_PASSWORD
EOF

echo "✅ Secrets générés dans deploy/k8s/fit-secrets-generated.yaml"
echo "🔑 Appliquez avec: kubectl apply -f deploy/k8s/fit-secrets-generated.yaml"






















