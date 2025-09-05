# 🚀 Plan de Déploiement FIT (Football Injury Tracking)

**Date:** 27 août 2025  
**Projet:** med-predictor  
**Phase:** Déploiement Production  

## 📋 Vue d'Ensemble du Déploiement

### 🎯 Objectifs du Déploiement
1. **Déployer FIT en production** avec une infrastructure robuste
2. **Assurer la haute disponibilité** et la scalabilité
3. **Configurer la surveillance** et le monitoring
4. **Mettre en place la sécurité** et la conformité
5. **Optimiser les performances** de l'application

### 🏗️ Architecture Cible
- **Frontend:** Vue.js/React avec Tailwind CSS
- **Backend:** Laravel 10 avec PHP 8.2
- **Base de données:** MySQL 8.0 avec Redis pour le cache
- **Infrastructure:** Kubernetes sur AWS/GCP/Azure
- **Monitoring:** Prometheus + Grafana
- **CI/CD:** GitHub Actions avec déploiement automatique

## 🔧 Préparation du Déploiement

### 1️⃣ Vérification des Prérequis
```bash
# Vérifier les dépendances
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# Vérifier la configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier la base de données
php artisan migrate:status
php artisan db:seed --class=ProductionSeeder
```

### 2️⃣ Configuration de l'Environnement
```bash
# Copier et configurer .env.production
cp .env.example .env.production
# Configurer les variables de production
```

### 3️⃣ Tests de Validation
```bash
# Tests unitaires
php artisan test --testsuite=Unit

# Tests d'intégration
php artisan test --testsuite=Feature

# Tests de performance
php artisan test --testsuite=Performance
```

## 🐳 Déploiement Docker

### 1️⃣ Optimisation du Dockerfile
Le Dockerfile actuel est basique et peut être optimisé :

```dockerfile
# Multi-stage build pour optimiser la taille
FROM php:8.2-fpm-alpine AS base
# ... configuration de base

FROM base AS composer
# ... installation des dépendances

FROM base AS final
# ... copie des fichiers optimisés
```

### 2️⃣ Build et Test de l'Image
```bash
# Build de l'image
docker build -t fit-app:latest .

# Test de l'image
docker run -d --name fit-test -p 9000:9000 fit-app:latest

# Vérification des logs
docker logs fit-test
```

### 3️⃣ Push vers le Registry
```bash
# Tag de l'image
docker tag fit-app:latest your-registry/fit-app:v1.0.0

# Push vers le registry
docker push your-registry/fit-app:v1.0.0
```

## ☸️ Déploiement Kubernetes

### 1️⃣ Configuration des Manifests
Créer des manifests Kubernetes complets :

```yaml
# deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: fit-app
  labels:
    app: fit-app
    version: v1.0.0
spec:
  replicas: 3
  selector:
    matchLabels:
      app: fit-app
  template:
    metadata:
      labels:
        app: fit-app
        version: v1.0.0
    spec:
      containers:
      - name: fit-app
        image: your-registry/fit-app:v1.0.0
        ports:
        - containerPort: 9000
          name: http
        env:
        - name: APP_ENV
          value: "production"
        - name: APP_DEBUG
          value: "false"
        resources:
          requests:
            memory: "512Mi"
            cpu: "500m"
          limits:
            memory: "1Gi"
            cpu: "1000m"
        livenessProbe:
          httpGet:
            path: /health
            port: 9000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /health
            port: 9000
          initialDelaySeconds: 5
          periodSeconds: 5
        volumeMounts:
        - name: storage
          mountPath: /var/www/html/storage
        - name: cache
          mountPath: /var/www/html/bootstrap/cache
      volumes:
      - name: storage
        persistentVolumeClaim:
          claimName: fit-storage-pvc
      - name: cache
        emptyDir: {}
```

### 2️⃣ Services et Ingress
```yaml
# service.yaml
apiVersion: v1
kind: Service
metadata:
  name: fit-service
  labels:
    app: fit-app
spec:
  type: ClusterIP
  ports:
  - port: 80
    targetPort: 9000
    protocol: TCP
    name: http
  selector:
    app: fit-app

---
# ingress.yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: fit-ingress
  annotations:
    nginx.ingress.kubernetes.io/rewrite-target: /
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
    cert-manager.io/cluster-issuer: "letsencrypt-prod"
spec:
  tls:
  - hosts:
    - fit.yourdomain.com
    secretName: fit-tls
  rules:
  - host: fit.yourdomain.com
    http:
      paths:
      - path: /
        pathType: Prefix
        backend:
          service:
            name: fit-service
            port:
              number: 80
```

### 3️⃣ Base de Données et Cache
```yaml
# database.yaml
apiVersion: apps/v1
kind: StatefulSet
metadata:
  name: fit-mysql
spec:
  serviceName: fit-mysql
  replicas: 1
  selector:
    matchLabels:
      app: fit-mysql
  template:
    metadata:
      labels:
        app: fit-mysql
    spec:
      containers:
      - name: mysql
        image: mysql:8.0
        env:
        - name: MYSQL_ROOT_PASSWORD
          valueFrom:
            secretKeyRef:
              name: fit-mysql-secret
              key: root-password
        - name: MYSQL_DATABASE
          value: "fit_production"
        ports:
        - containerPort: 3306
          name: mysql
        volumeMounts:
        - name: mysql-data
          mountPath: /var/lib/mysql
  volumeClaimTemplates:
  - metadata:
      name: mysql-data
    spec:
      accessModes: ["ReadWriteOnce"]
      resources:
        requests:
          storage: 10Gi
```

## 🚀 Déploiement Automatisé

### 1️⃣ Pipeline CI/CD
```yaml
# .github/workflows/deploy.yml
name: Deploy FIT to Production

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: php artisan test

  build:
    needs: test
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Build Docker image
      run: docker build -t fit-app:${{ github.sha }} .
    - name: Push to registry
      run: |
        echo ${{ secrets.REGISTRY_PASSWORD }} | docker login -u ${{ secrets.REGISTRY_USERNAME }} --password-stdin
        docker tag fit-app:${{ github.sha }} ${{ secrets.REGISTRY_URL }}/fit-app:${{ github.sha }}
        docker push ${{ secrets.REGISTRY_URL }}/fit-app:${{ github.sha }}

  deploy:
    needs: build
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Deploy to Kubernetes
      run: |
        kubectl set image deployment/fit-app fit-app=${{ secrets.REGISTRY_URL }}/fit-app:${{ github.sha }}
        kubectl rollout status deployment/fit-app
```

### 2️⃣ Scripts de Déploiement
```bash
#!/bin/bash
# scripts/deploy-production.sh

echo "🚀 Déploiement de FIT en production..."

# Variables
VERSION=$1
NAMESPACE="fit-production"
REGISTRY="your-registry"

# Vérification des prérequis
if [ -z "$VERSION" ]; then
    echo "❌ Version manquante. Usage: ./deploy-production.sh v1.0.0"
    exit 1
fi

# Mise à jour des images
echo "📦 Mise à jour des images..."
kubectl set image deployment/fit-app fit-app=$REGISTRY/fit-app:$VERSION -n $NAMESPACE

# Vérification du déploiement
echo "🔍 Vérification du déploiement..."
kubectl rollout status deployment/fit-app -n $NAMESPACE

# Vérification de la santé
echo "🏥 Vérification de la santé..."
kubectl get pods -n $NAMESPACE
kubectl get services -n $NAMESPACE

echo "✅ Déploiement terminé avec succès!"
```

## 📊 Monitoring et Surveillance

### 1️⃣ Configuration Prometheus
```yaml
# monitoring/prometheus-config.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: prometheus-config
data:
  prometheus.yml: |
    global:
      scrape_interval: 15s
    scrape_configs:
    - job_name: 'fit-app'
      static_configs:
      - targets: ['fit-service:80']
      metrics_path: /metrics
```

### 2️⃣ Dashboard Grafana
```json
{
  "dashboard": {
    "title": "FIT Application Dashboard",
    "panels": [
      {
        "title": "Request Rate",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(http_requests_total[5m])",
            "legendFormat": "{{method}} {{route}}"
          }
        ]
      },
      {
        "title": "Response Time",
        "type": "graph",
        "targets": [
          {
            "expr": "histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m]))",
            "legendFormat": "95th percentile"
          }
        ]
      }
    ]
  }
}
```

## 🔒 Sécurité et Conformité

### 1️⃣ Configuration des Secrets
```yaml
# secrets.yaml
apiVersion: v1
kind: Secret
metadata:
  name: fit-app-secrets
type: Opaque
data:
  APP_KEY: <base64-encoded-key>
  DB_PASSWORD: <base64-encoded-password>
  REDIS_PASSWORD: <base64-encoded-password>
  JWT_SECRET: <base64-encoded-secret>
```

### 2️⃣ Network Policies
```yaml
# network-policy.yaml
apiVersion: networking.k8s.io/v1
kind: NetworkPolicy
metadata:
  name: fit-app-network-policy
spec:
  podSelector:
    matchLabels:
      app: fit-app
  policyTypes:
  - Ingress
  - Egress
  ingress:
  - from:
    - namespaceSelector:
        matchLabels:
          name: ingress-nginx
    ports:
    - protocol: TCP
      port: 9000
  egress:
  - to:
    - namespaceSelector:
        matchLabels:
          name: database
    ports:
    - protocol: TCP
      port: 3306
```

## 📈 Tests de Performance

### 1️⃣ Tests de Charge
```bash
# Installation de k6
curl -L https://github.com/grafana/k6/releases/download/v0.45.0/k6-v0.45.0-linux-amd64.tar.gz | tar xz

# Test de charge
k6 run scripts/load-test.js
```

### 2️⃣ Script de Test de Charge
```javascript
// scripts/load-test.js
import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '2m', target: 100 }, // Montée en charge
    { duration: '5m', target: 100 }, // Charge constante
    { duration: '2m', target: 0 },   // Descente
  ],
};

export default function() {
  let response = http.get('https://fit.yourdomain.com/health');
  
  check(response, {
    'status is 200': (r) => r.status === 200,
    'response time < 500ms': (r) => r.timings.duration < 500,
  });
  
  sleep(1);
}
```

## 🚨 Procédures de Rollback

### 1️⃣ Script de Rollback
```bash
#!/bin/bash
# scripts/rollback.sh

echo "🔄 Rollback de FIT..."

# Variables
PREVIOUS_VERSION=$1
NAMESPACE="fit-production"

if [ -z "$PREVIOUS_VERSION" ]; then
    echo "❌ Version précédente manquante. Usage: ./rollback.sh v0.9.0"
    exit 1
fi

# Rollback du déploiement
echo "📦 Rollback vers la version $PREVIOUS_VERSION..."
kubectl rollout undo deployment/fit-app -n $NAMESPACE

# Vérification du rollback
echo "🔍 Vérification du rollback..."
kubectl rollout status deployment/fit-app -n $NAMESPACE

echo "✅ Rollback terminé avec succès!"
```

### 2️⃣ Procédure d'Urgence
```bash
# Arrêt d'urgence
kubectl scale deployment/fit-app --replicas=0 -n fit-production

# Redémarrage
kubectl scale deployment/fit-app --replicas=3 -n fit-production
```

## 📋 Checklist de Déploiement

### ✅ Pré-déploiement
- [ ] Tests unitaires et d'intégration passés
- [ ] Configuration de production validée
- [ ] Base de données migrée et seedée
- [ ] Images Docker construites et testées
- [ ] Secrets et configurations sécurisés

### ✅ Déploiement
- [ ] Manifestes Kubernetes appliqués
- [ ] Services et ingress configurés
- [ ] Base de données déployée
- [ ] Application déployée et accessible
- [ ] Monitoring et alerting configurés

### ✅ Post-déploiement
- [ ] Tests de santé passés
- [ ] Tests de performance validés
- [ ] Surveillance active
- [ ] Documentation mise à jour
- [ ] Équipe notifiée

## 🎯 Prochaines Étapes

### 1️⃣ Phase Immédiate (Cette semaine)
1. **Finaliser la configuration** de l'environnement de production
2. **Construire et tester** les images Docker
3. **Préparer les manifests** Kubernetes
4. **Configurer le pipeline** CI/CD

### 2️⃣ Phase Court Terme (2-3 semaines)
1. **Déployer en staging** pour validation
2. **Effectuer les tests** de charge et de sécurité
3. **Former l'équipe** aux procédures de déploiement
4. **Préparer la documentation** utilisateur

### 3️⃣ Phase Moyen Terme (1-2 mois)
1. **Déploiement en production** avec monitoring
2. **Optimisation des performances** basée sur les métriques
3. **Mise en place des alertes** et procédures d'urgence
4. **Formation des utilisateurs** finaux

---

**🎯 Objectif:** Déployer FIT en production avec une infrastructure robuste, scalable et sécurisée, en respectant les meilleures pratiques DevOps et Kubernetes.

**📧 Support:** L'équipe de déploiement est disponible pour assister à chaque étape du processus.






















