# 🚀 Plan de Déploiement FIT en Production - Google Cloud

**Domaine cible :** fit3.tbhc.uk  
**Plateforme :** Google Cloud Platform (GCP)  
**Date :** 27 août 2025  

## 📊 **État Actuel (Local)**
- ✅ Cluster Minikube fonctionnel
- ✅ Infrastructure de base opérationnelle (MySQL, Redis, Nginx, Prometheus, Grafana)
- ❌ Application Laravel en échec (problème de queue)
- ❌ Migrations de base de données échouées

## 🎯 **Objectif Final**
Déployer FIT en production sur GCP avec le domaine `fit3.tbhc.uk` avec une infrastructure robuste et scalable.

---

## **Phase 1 : Correction de l'Application (Local) - PRIORITÉ 1**

### 🔧 **1.1 Diagnostiquer le Problème Laravel Queue**
```bash
# Vérifier la configuration de la queue
kubectl exec -it med-predictor-app-5c78887f4c-frfqn -n med-predictor -- php artisan queue:work --verbose

# Vérifier la configuration Redis
kubectl exec -it med-predictor-redis-5d669c98f9-8fjm7 -n med-predictor -- redis-cli ping

# Vérifier la connexion à la base de données
kubectl exec -it med-predictor-app-5c78887f4c-frfqn -n med-predictor -- php artisan tinker
# Dans tinker : DB::connection()->getPdo();
```

### 🔧 **1.2 Corriger la Configuration de la Queue**
```bash
# Vérifier les variables d'environnement
kubectl get configmap -n med-predictor med-predictor-config -o yaml

# Créer un ConfigMap pour la configuration Laravel
kubectl create configmap laravel-config -n med-predictor --from-literal=QUEUE_CONNECTION=redis --from-literal=REDIS_HOST=med-predictor-redis --from-literal=REDIS_PORT=6379
```

### 🔧 **1.3 Relancer l'Application**
```bash
# Redémarrer le déploiement
kubectl rollout restart deployment/med-predictor-app -n med-predictor

# Vérifier le statut
kubectl get pods -n med-predictor -l tier=backend
```

---

## **Phase 2 : Préparation Google Cloud Platform**

### ☁️ **2.1 Configuration GCP**
```bash
# Installer Google Cloud CLI
curl https://sdk.cloud.google.com | bash
exec -l $SHELL

# Authentification
gcloud auth login
gcloud config set project YOUR_PROJECT_ID

# Activer les APIs nécessaires
gcloud services enable container.googleapis.com
gcloud services enable compute.googleapis.com
gcloud services enable dns.googleapis.com
gcloud services enable cloudbuild.googleapis.com
```

### ☁️ **2.2 Créer le Cluster GKE**
```bash
# Créer le cluster GKE
gcloud container clusters create fit-cluster \
  --zone=europe-west1-a \
  --num-nodes=3 \
  --machine-type=e2-standard-2 \
  --enable-autoscaling \
  --min-nodes=1 \
  --max-nodes=10 \
  --enable-network-policy \
  --enable-ip-alias

# Configurer kubectl
gcloud container clusters get-credentials fit-cluster --zone=europe-west1-a
```

### ☁️ **2.3 Configuration DNS et Certificats**
```bash
# Créer une zone DNS pour tbhc.uk
gcloud dns managed-zones create fit-zone \
  --dns-name="tbhc.uk." \
  --description="Zone DNS pour FIT"

# Ajouter les enregistrements DNS
gcloud dns record-sets transaction start --zone="fit-zone"
gcloud dns record-sets transaction add fit3.tbhc.uk. --name="fit3.tbhc.uk." --ttl=300 --type=A --zone="fit-zone"
gcloud dns record-sets transaction execute --zone="fit-zone"
```

---

## **Phase 3 : Déploiement sur GCP**

### 🐳 **3.1 Build et Push de l'Image Docker**
```bash
# Build de l'image optimisée
docker build -f Dockerfile.optimized -t gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.0 .

# Tag et push vers Google Container Registry
docker tag gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.0 gcr.io/YOUR_PROJECT_ID/fit-app:latest
docker push gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.0
docker push gcr.io/YOUR_PROJECT_ID/fit-app:latest
```

### ☸️ **3.2 Déploiement Kubernetes sur GCP**
```bash
# Créer le namespace
kubectl apply -f deploy/k8s/fit-namespace.yaml

# Appliquer les secrets (générer d'abord)
./scripts/generate-secrets.sh

# Déployer l'application
kubectl apply -f deploy/k8s/fit-deployment.yaml
kubectl apply -f deploy/k8s/fit-database.yaml

# Vérifier le déploiement
kubectl get pods -n fit-production
kubectl get services -n fit-production
```

### 🌐 **3.3 Configuration Ingress et SSL**
```bash
# Installer cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Créer le ClusterIssuer Let's Encrypt
kubectl apply -f - <<EOF
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: your-email@tbhc.uk
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF

# Appliquer l'Ingress
kubectl apply -f deploy/k8s/fit-ingress.yaml
```

---

## **Phase 4 : Tests et Validation**

### 🧪 **4.1 Tests de Connectivité**
```bash
# Test de la base de données
kubectl exec -it fit-mysql-0 -n fit-production -- mysql -u root -p

# Test de Redis
kubectl exec -it fit-redis-0 -n fit-production -- redis-cli ping

# Test de l'application
curl -I https://fit3.tbhc.uk/health
```

### 🧪 **4.2 Tests de Performance**
```bash
# Installation de k6
curl -L https://github.com/grafana/k6/releases/download/v0.45.0/k6-v0.45.0-linux-amd64.tar.gz | tar xz

# Test de charge
./k6 run scripts/load-test.js
```

---

## **Phase 5 : Monitoring et Surveillance**

### 📊 **5.1 Configuration Prometheus/Grafana**
```bash
# Déployer Prometheus
kubectl apply -f monitoring/prometheus-config.yaml

# Déployer Grafana
kubectl apply -f monitoring/grafana-deployment.yaml

# Configurer les dashboards
kubectl apply -f monitoring/grafana-dashboards.yaml
```

### 📊 **5.2 Alertes et Notifications**
```bash
# Configurer les alertes Prometheus
kubectl apply -f monitoring/prometheus-rules.yaml

# Configurer les notifications (Slack, email)
kubectl apply -f monitoring/alertmanager-config.yaml
```

---

## **Phase 6 : Sécurité et Optimisation**

### 🔒 **6.1 Sécurité**
```bash
# Network Policies
kubectl apply -f security/network-policies.yaml

# Pod Security Standards
kubectl apply -f security/pod-security.yaml

# RBAC
kubectl apply -f security/rbac.yaml
```

### ⚡ **6.2 Optimisation**
```bash
# HPA (Horizontal Pod Autoscaler)
kubectl apply -f scaling/hpa.yaml

# VPA (Vertical Pod Autoscaler)
kubectl apply -f scaling/vpa.yaml

# Resource Quotas
kubectl apply -f scaling/resource-quotas.yaml
```

---

## **📋 Checklist de Déploiement**

### ✅ **Pré-déploiement**
- [ ] Application Laravel corrigée et testée localement
- [ ] Cluster GKE créé et configuré
- [ ] DNS configuré pour fit3.tbhc.uk
- [ ] Images Docker construites et poussées vers GCR

### ✅ **Déploiement**
- [ ] Namespace créé
- [ ] Secrets appliqués
- [ ] Application déployée
- [ ] Base de données déployée
- [ ] Ingress configuré
- [ ] SSL configuré

### ✅ **Post-déploiement**
- [ ] Tests de connectivité passés
- [ ] Tests de performance validés
- [ ] Monitoring configuré
- [ ] Alertes configurées
- [ ] Documentation mise à jour

---

## **🚨 Procédures d'Urgence**

### 🔄 **Rollback Rapide**
```bash
# Rollback du déploiement
kubectl rollout undo deployment/fit-app -n fit-production

# Rollback de la base de données
kubectl rollout undo statefulset/fit-mysql -n fit-production
```

### 🛑 **Arrêt d'Urgence**
```bash
# Arrêter tous les pods
kubectl scale deployment/fit-app --replicas=0 -n fit-production

# Redémarrer
kubectl scale deployment/fit-app --replicas=3 -n fit-production
```

---

## **📞 Support et Contact**

**Équipe de Déploiement :** Disponible 24/7 pendant le déploiement  
**Escalade :** En cas de problème critique, contacter l'équipe DevOps  

---

**🎯 Objectif :** FIT déployé et opérationnel sur fit3.tbhc.uk en moins de 2 heures !
















