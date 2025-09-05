# 🚀 Déploiement FIT - Guide Rapide

## ⚡ Déploiement en Une Commande

```bash
./deploy-fit-one-command.sh
```

## 📋 Prérequis

- Docker installé et en cours d'exécution
- kubectl configuré avec accès au cluster Kubernetes
- Cluster Kubernetes avec Ingress Controller et cert-manager

## 🔧 Configuration Rapide

### 1. Modifier le domaine
Éditez `deploy/k8s/fit-deployment.yaml` et remplacez `fit.yourdomain.com` par votre domaine.

### 2. Modifier le registry Docker
Éditez `scripts/deploy-fit.sh` et remplacez `your-registry` par votre registry.

## 📁 Structure des Fichiers

```
deploy/k8s/
├── fit-deployment.yaml      # Application principale
├── fit-database.yaml        # Base de données MySQL
├── fit-namespace.yaml       # Namespace Kubernetes
└── fit-secrets.yaml         # Template des secrets

scripts/
├── deploy-fit.sh            # Script de déploiement détaillé
├── generate-secrets.sh      # Génération des secrets
└── deploy-fit-one-command.sh # Déploiement en une commande
```

## 🎯 Commandes Utiles

```bash
# Vérifier le statut
kubectl get pods -n fit-production

# Voir les logs
kubectl logs -f deployment/fit-app -n fit-production

# Accéder à la base de données
kubectl exec -it fit-mysql-0 -n fit-production -- mysql -u root -p

# Redémarrer l'application
kubectl rollout restart deployment/fit-app -n fit-production
```

## 🚨 Dépannage

### Problème de connexion à la base de données
```bash
kubectl get secrets -n fit-production
kubectl describe pod -l app=fit-app -n fit-production
```

### Problème d'Ingress
```bash
kubectl get ingress -n fit-production
kubectl describe ingress fit-ingress -n fit-production
```

## ✅ Vérification du Déploiement

1. **Pods en cours d'exécution** : `kubectl get pods -n fit-production`
2. **Services accessibles** : `kubectl get svc -n fit-production`
3. **Ingress configuré** : `kubectl get ingress -n fit-production`
4. **Application accessible** : Visitez `https://fit.yourdomain.com`

---

**🎯 Objectif :** Déployer FIT en production en moins de 5 minutes !






















