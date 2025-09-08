# 🚀 Déploiement FIT sur Google Cloud Platform - Guide Rapide

**Domaine cible :** fit3.tbhc.uk  
**Plateforme :** Google Cloud Platform (GCP)  
**Temps estimé :** 30-45 minutes  

## ⚡ **Déploiement en Une Commande**

```bash
./scripts/deploy-fit-gcp.sh
```

## 📋 **Prérequis**

### 1️⃣ **Outils Installés**
```bash
# Google Cloud CLI
curl https://sdk.cloud.google.com | bash
exec -l $SHELL

# Docker
# kubectl (installé automatiquement avec gcloud)
```

### 2️⃣ **Configuration GCP**
```bash
# Authentification
gcloud auth login

# Vérifier le projet
gcloud config get-value project

# Si différent, configurer le bon projet
gcloud config set project YOUR_PROJECT_ID
```

## 🔧 **Configuration Rapide**

### 1️⃣ **Modifier le Script de Déploiement**
Éditez `scripts/deploy-fit-gcp.sh` et remplacez :
```bash
PROJECT_ID="YOUR_PROJECT_ID"  # Votre ID de projet GCP
```

### 2️⃣ **Vérifier le Domaine**
Le domaine `fit3.tbhc.uk` est déjà configuré dans tous les manifests.

## 🚀 **Étapes de Déploiement**

### **Phase 1 : Infrastructure GCP**
- ✅ Création du cluster GKE (si nécessaire)
- ✅ Configuration de kubectl
- ✅ Création du namespace

### **Phase 2 : Application**
- ✅ Build de l'image Docker
- ✅ Push vers Google Container Registry
- ✅ Déploiement des manifests Kubernetes

### **Phase 3 : Configuration**
- ✅ Installation de cert-manager
- ✅ Configuration Let's Encrypt
- ✅ Installation de l'Ingress Controller
- ✅ Configuration de l'Ingress

## 📁 **Structure des Fichiers GCP**

```
deploy/k8s/gcp/
├── fit-production-deployment.yaml  # Application principale
├── fit-production-database.yaml    # Base de données MySQL
├── fit-production-redis.yaml       # Cache Redis
└── fit-production-ingress.yaml     # Ingress et SSL
```

## 🌐 **Accès à l'Application**

### **URL de Production**
- **Application :** https://fit3.tbhc.uk
- **Health Check :** https://fit3.tbhc.uk/health
- **Metrics :** https://fit3.tbhc.uk/metrics

### **Dashboard Kubernetes**
```bash
kubectl proxy
# Puis ouvrir : http://localhost:8001/api/v1/namespaces/kubernetes-dashboard/services/https:kubernetes-dashboard:/proxy/
```

## 🔍 **Vérification du Déploiement**

### **1. Statut des Pods**
```bash
kubectl get pods -n fit-production
```

### **2. Services**
```bash
kubectl get services -n fit-production
```

### **3. Ingress**
```bash
kubectl get ingress -n fit-production
```

### **4. Certificats SSL**
```bash
kubectl get certificate -n fit-production
```

## 🚨 **Dépannage Rapide**

### **Problème : Pods en CrashLoopBackOff**
```bash
# Voir les logs
kubectl logs -f deployment/fit-app -n fit-production

# Redémarrer le déploiement
kubectl rollout restart deployment/fit-app -n fit-production
```

### **Problème : Certificat SSL non généré**
```bash
# Vérifier cert-manager
kubectl get pods -n cert-manager

# Vérifier les événements
kubectl get events -n fit-production --sort-by='.lastTimestamp'
```

### **Problème : Ingress non accessible**
```bash
# Vérifier l'Ingress Controller
kubectl get pods -n ingress-nginx

# Vérifier la configuration DNS
nslookup fit3.tbhc.uk
```

## 📊 **Monitoring et Logs**

### **Logs de l'Application**
```bash
kubectl logs -f deployment/fit-app -n fit-production
```

### **Logs de la Base de Données**
```bash
kubectl logs -f fit-mysql-0 -n fit-production
```

### **Logs Redis**
```bash
kubectl logs -f fit-redis-0 -n fit-production
```

## 🔄 **Mise à Jour de l'Application**

### **1. Build et Push de la Nouvelle Image**
```bash
docker build -f Dockerfile.optimized -t gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.1 .
docker push gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.1
```

### **2. Mise à Jour du Déploiement**
```bash
kubectl set image deployment/fit-app fit-app=gcr.io/YOUR_PROJECT_ID/fit-app:v1.0.1 -n fit-production
```

### **3. Vérification**
```bash
kubectl rollout status deployment/fit-app -n fit-production
```

## 🚨 **Procédures d'Urgence**

### **Rollback Rapide**
```bash
kubectl rollout undo deployment/fit-app -n fit-production
```

### **Arrêt d'Urgence**
```bash
kubectl scale deployment/fit-app --replicas=0 -n fit-production
```

## 📞 **Support**

**En cas de problème :**
1. Vérifier les logs avec `kubectl logs`
2. Vérifier les événements avec `kubectl get events`
3. Vérifier le statut des pods avec `kubectl get pods`
4. Consulter la documentation GCP

---

## 🎯 **Objectif : FIT déployé et opérationnel sur fit3.tbhc.uk en 30 minutes !**

**Prochaine étape :** Exécuter `./scripts/deploy-fit-gcp.sh` et suivre les instructions à l'écran.

























