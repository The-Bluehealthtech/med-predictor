# 🚀 Guide de Continuation du Déploiement GCP

## 📋 État Actuel

**✅ Accomplissements Réalisés :**
- Cluster GKE `fit-cluster` créé dans `us-central1-a`
- Image Docker `gcr.io/med-predictor-fit/fit-app:v1.0.0` construite et poussée
- Namespace `fit-production` créé
- Secrets générés et appliqués
- PVCs créés (MySQL, Redis, Storage)
- Services et StatefulSets déployés

## 🔄 Prochaines Étapes à Exécuter

### 1. Vérifier l'État Actuel

```bash
# Se connecter au cluster
gcloud container clusters get-credentials fit-cluster --zone=us-central1-a

# Vérifier les pods
kubectl get pods -n fit-production

# Vérifier les services
kubectl get services -n fit-production

# Vérifier les PVCs
kubectl get pvc -n fit-production
```

### 2. Installer l'Ingress Controller

```bash
# Installer nginx-ingress
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.0/deploy/static/provider/cloud/deploy.yaml

# Attendre que l'ingress soit prêt
kubectl wait --for=condition=ready pod -l app.kubernetes.io/name=ingress-nginx -n ingress-nginx --timeout=300s
```

### 3. Installer cert-manager

```bash
# Installer cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Attendre que cert-manager soit prêt
kubectl wait --for=condition=ready pod -l app.kubernetes.io/instance=cert-manager -n cert-manager --timeout=300s
```

### 4. Créer le ClusterIssuer Let's Encrypt

```bash
cat <<EOF | kubectl apply -f -
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: admin@tbhc.uk
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF
```

### 5. Déployer l'Ingress

```bash
# Appliquer l'ingress avec le domaine fit3.tbhc.uk
kubectl apply -f deploy/k8s/gcp/fit-production-ingress.yaml

# Vérifier l'ingress
kubectl get ingress -n fit-production
```

### 6. Obtenir l'IP Externe

```bash
# Obtenir l'IP du load balancer
kubectl get ingress fit-ingress -n fit-production -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
```

### 7. Configurer le DNS

- Pointer le domaine `fit3.tbhc.uk` vers l'IP externe obtenue
- Le certificat SSL sera généré automatiquement par Let's Encrypt

## 🎯 Résultat Attendu

Une fois terminé, votre application sera accessible sur :
- 🌐 **https://fit3.tbhc.uk** (application principale)
- 🏥 **https://fit3.tbhc.uk/health** (health check)
- 📊 **https://fit3.tbhc.uk/metrics** (métriques)

## 🚨 Dépannage

### Vérifier les Logs des Pods

```bash
# Logs de l'application
kubectl logs -f deployment/fit-app -n fit-production

# Logs de MySQL
kubectl logs -f statefulset/fit-mysql -n fit-production

# Logs de Redis
kubectl logs -f statefulset/fit-redis-simple -n fit-production
```

### Redémarrer les Pods si Nécessaire

```bash
# Redémarrer l'application
kubectl rollout restart deployment/fit-app -n fit-production

# Redémarrer MySQL
kubectl rollout restart statefulset/fit-mysql -n fit-production

# Redémarrer Redis
kubectl rollout restart statefulset/fit-redis-simple -n fit-production
```

### Vérifier les Événements

```bash
# Événements récents
kubectl get events -n fit-production --sort-by='.lastTimestamp'
```

### Commandes de Vérification

```bash
# Statut complet
kubectl get all -n fit-production

# Détails des pods
kubectl describe pods -n fit-production

# Statut des certificats SSL
kubectl get certificate -n fit-production

# Statut des secrets
kubectl get secrets -n fit-production
```

## 📞 Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs des pods
2. Consultez les événements Kubernetes
3. Vérifiez que tous les secrets sont présents
4. Assurez-vous que les PVCs sont liés
5. Vérifiez la configuration de l'ingress

## 🎉 Félicitations !

Votre application FIT sera bientôt accessible en production sur Google Cloud Platform avec :
- ✅ Infrastructure Kubernetes robuste
- ✅ Base de données MySQL persistante
- ✅ Cache Redis pour les performances
- ✅ SSL automatique avec Let's Encrypt
- ✅ Monitoring et logging intégrés

**Le déploiement est très avancé ! Il ne reste plus que quelques étapes pour avoir votre application FIT accessible en production.** 🚀
