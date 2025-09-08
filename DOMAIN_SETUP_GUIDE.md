# 🌐 Guide de Configuration de Domaine - Med-Predictor

## 🎯 Objectif

Ce guide vous explique comment lier votre projet med-predictor à votre domaine personnalisé avec SSL, CDN et monitoring.

## 📋 Prérequis

- Domaine enregistré (ex: `med-predictor.com`)
- Accès au gestionnaire DNS de votre domaine
- Cluster Kubernetes GKE configuré
- Google Cloud Platform activé

## 🚀 Étapes de Configuration

### 1. Configuration DNS

#### Option A: Cloudflare (Recommandé)
```bash
# Enregistrements DNS à créer dans Cloudflare
Type: A
Name: @
Content: [IP_EXTERNE_DU_CLUSTER]
Proxy: ✅ (Orange cloud)

Type: A  
Name: www
Content: [IP_EXTERNE_DU_CLUSTER]
Proxy: ✅ (Orange cloud)

Type: CNAME
Name: api
Content: med-predictor.com
Proxy: ✅ (Orange cloud)

Type: CNAME
Name: cdn
Content: med-predictor.com
Proxy: ✅ (Orange cloud)
```

#### Option B: Google Cloud DNS
```bash
# Créer une zone DNS dans GCP
gcloud dns managed-zones create med-predictor-zone \
    --dns-name="med-predictor.com." \
    --description="DNS zone for med-predictor"

# Ajouter les enregistrements
gcloud dns record-sets create med-predictor.com. \
    --zone=med-predictor-zone \
    --type=A \
    --ttl=300 \
    --rrdatas=[IP_EXTERNE_DU_CLUSTER]
```

### 2. Obtenir l'IP Externe du Cluster

```bash
# Obtenir l'IP du load balancer
kubectl get ingress med-predictor-ingress -n med-predictor -o jsonpath='{.status.loadBalancer.ingress[0].ip}'

# Ou obtenir l'IP du service
kubectl get service med-predictor-service -n med-predictor -o jsonpath='{.status.loadBalancer.ingress[0].ip}'
```

### 3. Configuration SSL avec Let's Encrypt

#### Installer cert-manager
```bash
# Installer cert-manager
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Vérifier l'installation
kubectl get pods -n cert-manager
```

#### Créer ClusterIssuer
```bash
# Créer le ClusterIssuer pour Let's Encrypt
kubectl apply -f - <<EOF
apiVersion: cert-manager.io/v1
kind: ClusterIssuer
metadata:
  name: letsencrypt-prod
spec:
  acme:
    server: https://acme-v02.api.letsencrypt.org/directory
    email: admin@med-predictor.com  # Remplacez par votre email
    privateKeySecretRef:
      name: letsencrypt-prod
    solvers:
    - http01:
        ingress:
          class: nginx
EOF
```

### 4. Configuration Ingress avec Votre Domaine

Créer/modifier le fichier d'ingress :

```yaml
# deploy/k8s/gcp/med-predictor-ingress.yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: med-predictor-ingress
  namespace: med-predictor
  labels:
    app: med-predictor
    environment: production
    platform: gcp
  annotations:
    nginx.ingress.kubernetes.io/rewrite-target: /
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
    nginx.ingress.kubernetes.io/force-ssl-redirect: "true"
    nginx.ingress.kubernetes.io/proxy-body-size: "50m"
    nginx.ingress.kubernetes.io/proxy-connect-timeout: "60"
    nginx.ingress.kubernetes.io/proxy-send-timeout: "60"
    nginx.ingress.kubernetes.io/proxy-read-timeout: "60"
    nginx.ingress.kubernetes.io/proxy-buffer-size: "128k"
    nginx.ingress.kubernetes.io/proxy-buffers-number: "4"
    nginx.ingress.kubernetes.io/ssl-ciphers: "ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384"
    nginx.ingress.kubernetes.io/ssl-protocols: "TLSv1.2 TLSv1.3"
    nginx.ingress.kubernetes.io/configuration-snippet: |
      more_set_headers "X-Frame-Options: DENY";
      more_set_headers "X-Content-Type-Options: nosniff";
      more_set_headers "X-XSS-Protection: 1; mode=block";
      more_set_headers "Referrer-Policy: strict-origin-when-cross-origin";
      more_set_headers "Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https:; frame-ancestors 'none';";
    cert-manager.io/cluster-issuer: "letsencrypt-prod"
    kubernetes.io/ingress.class: "nginx"
    external-dns.alpha.kubernetes.io/hostname: "med-predictor.com"
    external-dns.alpha.kubernetes.io/cloudflare-proxied: "false"
spec:
  tls:
  - hosts:
    - med-predictor.com
    - www.med-predictor.com
    - api.med-predictor.com
    secretName: med-predictor-tls
  rules:
  - host: med-predictor.com
    http:
      paths:
      - path: /
        pathType: Prefix
        backend:
          service:
            name: med-predictor-service
            port:
              number: 80
  - host: www.med-predictor.com
    http:
      paths:
      - path: /
        pathType: Prefix
        backend:
          service:
            name: med-predictor-service
            port:
              number: 80
  - host: api.med-predictor.com
    http:
      paths:
      - path: /api
        pathType: Prefix
        backend:
          service:
            name: med-predictor-service
            port:
              number: 80
```

### 5. Configuration CDN pour les Fichiers Statiques

#### Option A: Google Cloud CDN
```bash
# Activer Cloud CDN
gcloud compute backend-services create med-predictor-backend \
    --global \
    --protocol=HTTP \
    --port-name=http \
    --health-checks=med-predictor-health-check

# Créer le health check
gcloud compute health-checks create http med-predictor-health-check \
    --port=80 \
    --request-path=/health

# Créer la URL map avec CDN
gcloud compute url-maps create med-predictor-url-map \
    --default-service=med-predictor-backend

# Créer le proxy HTTPS
gcloud compute target-https-proxies create med-predictor-https-proxy \
    --url-map=med-predictor-url-map \
    --ssl-certificates=med-predictor-ssl-cert

# Créer la règle de forwarding
gcloud compute forwarding-rules create med-predictor-https-rule \
    --global \
    --target-https-proxy=med-predictor-https-proxy \
    --ports=443
```

#### Option B: Cloudflare CDN (Automatique)
Si vous utilisez Cloudflare, le CDN est automatiquement activé avec le proxy orange.

### 6. Configuration des Variables d'Environnement

Mettre à jour votre `.env` :

```env
# Configuration du domaine
APP_URL=https://med-predictor.com
APP_DOMAIN=med-predictor.com

# Configuration SSL
FORCE_HTTPS=true
SECURE_COOKIES=true

# Configuration CDN
CDN_URL=https://cdn.med-predictor.com
STATIC_URL=https://cdn.med-predictor.com

# Configuration GCS avec CDN
GOOGLE_CLOUD_STORAGE_BUCKET=med-predictor-storage
GCS_CDN_URL=https://storage.googleapis.com/med-predictor-storage
```

### 7. Script de Déploiement Complet

Créer le script de déploiement :

```bash
#!/bin/bash
# scripts/deploy-domain.sh

set -e

DOMAIN="${DOMAIN:-med-predictor.com}"
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
NAMESPACE="med-predictor"

echo "🚀 Déploiement du domaine: $DOMAIN"

# 1. Vérifier que le cluster est accessible
echo "📡 Vérification du cluster..."
kubectl cluster-info

# 2. Obtenir l'IP externe
echo "🔍 Récupération de l'IP externe..."
EXTERNAL_IP=$(kubectl get ingress med-predictor-ingress -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "")

if [ -z "$EXTERNAL_IP" ]; then
    echo "❌ IP externe non trouvée. Déployez d'abord l'ingress."
    exit 1
fi

echo "✅ IP externe: $EXTERNAL_IP"

# 3. Déployer l'ingress avec le domaine
echo "🌐 Déploiement de l'ingress..."
kubectl apply -f deploy/k8s/gcp/med-predictor-ingress.yaml

# 4. Attendre que le certificat SSL soit généré
echo "🔒 Attente de la génération du certificat SSL..."
kubectl wait --for=condition=ready certificate/med-predictor-tls -n $NAMESPACE --timeout=300s

# 5. Vérifier le statut
echo "📊 Statut du déploiement..."
kubectl get ingress -n $NAMESPACE
kubectl get certificates -n $NAMESPACE

echo "🎉 Déploiement terminé!"
echo "🌐 Votre domaine est maintenant accessible sur: https://$DOMAIN"
echo "📝 N'oubliez pas de configurer vos enregistrements DNS pour pointer vers: $EXTERNAL_IP"
```

### 8. Tests de Validation

#### Script de Test
```bash
#!/bin/bash
# scripts/test-domain.sh

DOMAIN="${DOMAIN:-med-predictor.com}"

echo "🧪 Test du domaine: $DOMAIN"

# Test HTTP (doit rediriger vers HTTPS)
echo "📡 Test HTTP..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN)
echo "Status HTTP: $HTTP_STATUS"

# Test HTTPS
echo "🔒 Test HTTPS..."
HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN)
echo "Status HTTPS: $HTTPS_STATUS"

# Test API
echo "🔌 Test API..."
API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://api.$DOMAIN/api/health)
echo "Status API: $API_STATUS"

# Test SSL
echo "🔐 Test SSL..."
SSL_INFO=$(echo | openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | openssl x509 -noout -dates)
echo "Informations SSL:"
echo "$SSL_INFO"

# Test CDN
echo "⚡ Test CDN..."
CDN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://cdn.$DOMAIN)
echo "Status CDN: $CDN_STATUS"

echo "✅ Tests terminés!"
```

## 🔧 Configuration Avancée

### Monitoring avec Prometheus
```yaml
# deploy/k8s/gcp/monitoring.yaml
apiVersion: v1
kind: ServiceMonitor
metadata:
  name: med-predictor-monitor
  namespace: med-predictor
spec:
  selector:
    matchLabels:
      app: med-predictor
  endpoints:
  - port: http
    path: /metrics
```

### Logs avec ELK Stack
```yaml
# deploy/k8s/gcp/logging.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: fluentd-config
  namespace: med-predictor
data:
  fluent.conf: |
    <source>
      @type tail
      path /var/log/containers/*med-predictor*.log
      pos_file /var/log/fluentd-containers.log.pos
      tag kubernetes.*
      format json
    </source>
```

## 🚨 Dépannage

### Problèmes Courants

1. **Certificat SSL ne se génère pas**
   ```bash
   # Vérifier les logs cert-manager
   kubectl logs -n cert-manager deployment/cert-manager
   
   # Vérifier le ClusterIssuer
   kubectl describe clusterissuer letsencrypt-prod
   ```

2. **DNS ne pointe pas vers la bonne IP**
   ```bash
   # Vérifier la résolution DNS
   nslookup med-predictor.com
   dig med-predictor.com
   ```

3. **Ingress ne fonctionne pas**
   ```bash
   # Vérifier les logs nginx-ingress
   kubectl logs -n ingress-nginx deployment/ingress-nginx-controller
   
   # Vérifier l'ingress
   kubectl describe ingress med-predictor-ingress -n med-predictor
   ```

## 📊 Monitoring et Alertes

### Configuration Cloudflare Analytics
- Accéder au dashboard Cloudflare
- Activer Analytics
- Configurer les alertes pour les erreurs 5xx

### Configuration Google Cloud Monitoring
```bash
# Activer Cloud Monitoring
gcloud services enable monitoring.googleapis.com

# Créer des alertes
gcloud alpha monitoring policies create --policy-from-file=alert-policy.yaml
```

## 🎯 Résultat Final

Après configuration complète, vous aurez :

- ✅ **Domaine principal** : `https://med-predictor.com`
- ✅ **Sous-domaine API** : `https://api.med-predictor.com`
- ✅ **Sous-domaine CDN** : `https://cdn.med-predictor.com`
- ✅ **SSL automatique** avec Let's Encrypt
- ✅ **CDN activé** pour les performances
- ✅ **Monitoring** et alertes configurés
- ✅ **Logs centralisés** pour le debugging

Votre application sera accessible de manière professionnelle avec une infrastructure robuste et sécurisée ! 🚀

