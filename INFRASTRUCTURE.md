# Infrastructure & DevOps - FIT Platform

## 🏗️ Architecture Globale

### Composants Principaux
- **Application Laravel** : Container PHP-FPM + Nginx
- **Base de données** : MySQL 8.0
- **Cache & Sessions** : Redis
- **Reverse Proxy** : Nginx
- **Process Manager** : Supervisor

## 🐳 Environnement Local (Docker)

### Démarrage Rapide
```bash
# Démarrer l'environnement
docker-compose up -d

# Vérifier l'état
docker-compose ps

# Accéder à l'application
open http://localhost:8080

# Accéder à Adminer (DB)
open http://localhost:8081
```

### Services Disponibles
- **App** : `http://localhost:8080` (Laravel + PHP-FPM)
- **MySQL** : `localhost:3306`
- **Redis** : `localhost:6379`
- **Adminer** : `http://localhost:8081`

## ☁️ Déploiement Cloud

### AWS (ECS + Fargate)
```bash
# Déploiement staging
./scripts/deploy-aws.sh staging

# Déploiement production
./scripts/deploy-aws.sh production
```

**Services AWS utilisés :**
- **ECS** : Orchestration des conteneurs
- **Fargate** : Serveurs sans serveur
- **RDS** : Base de données MySQL
- **ElastiCache** : Cache Redis
- **ALB** : Load Balancer
- **CloudWatch** : Monitoring

### Google Cloud Platform
```bash
# Déploiement Cloud Run
./scripts/deploy-gcp.sh staging
```

**Services GCP utilisés :**
- **Cloud Run** : Conteneurs sans serveur
- **Cloud SQL** : Base de données MySQL
- **Secret Manager** : Gestion des secrets
- **Cloud Build** : CI/CD automatisé

### Azure
```bash
# Déploiement Container Instances
./scripts/deploy-azure.sh staging
```

**Services Azure utilisés :**
- **ACI** : Container Instances
- **Azure Database** : MySQL géré
- **Key Vault** : Gestion des secrets
- **Container Registry** : Stockage des images

## 🚀 Kubernetes

### Déploiement Local (Minikube)
```bash
# Démarrer Minikube
minikube start

# Déployer l'application
kubectl apply -f k8s/

# Accéder à l'application
minikube service fit-platform-app
```

### Déploiement Production
```bash
# Créer le namespace
kubectl create namespace fit-platform

# Déployer avec variables d'environnement
kubectl apply -f k8s/ -n fit-platform
```

## 🏗️ Infrastructure as Code (Terraform)

### Initialisation
```bash
cd terraform
terraform init
terraform plan
terraform apply
```

### Variables Configurables
- **Region** : `eu-west-3` (par défaut)
- **Environment** : `staging` ou `production`
- **VPC CIDR** : `10.0.0.0/16`
- **Instance Types** : `t3.micro` (staging), `t3.small` (production)

## 📊 Monitoring & Observabilité

### Endpoints de Santé
- **Health Check** : `GET /health`
- **Readiness Probe** : `GET /ready`
- **Liveness Probe** : `GET /health`

### Métriques Collectées
- **Performance** : Temps de réponse, débit
- **Ressources** : CPU, mémoire, disque
- **Base de données** : Connexions, requêtes
- **Cache** : Hit ratio, évictions

## 🔐 Sécurité

### Gestion des Secrets
- **Développement** : Fichier `.env`
- **Staging** : Variables d'environnement
- **Production** : Secret Manager / Key Vault

### Chiffrement
- **Transit** : TLS 1.3
- **Stockage** : Chiffrement au repos
- **Base de données** : Chiffrement des données sensibles

## 🚦 CI/CD Pipeline

### GitHub Actions
- **Tests** : PHPUnit, Code Quality
- **Build** : Docker image
- **Deploy** : Staging automatique, Production manuel
- **Notifications** : Email, Slack, Teams

### Déploiement Automatique
```yaml
# Déclencheurs
on:
  push:
    branches: [develop, main]
  pull_request:
    branches: [main]
```

## 📋 Checklist de Déploiement

### Pré-déploiement
- [ ] Tests unitaires passent
- [ ] Tests d'intégration passent
- [ ] Code review approuvé
- [ ] Variables d'environnement configurées

### Déploiement
- [ ] Backup de la base de données
- [ ] Déploiement en staging
- [ ] Tests de régression
- [ ] Validation des fonctionnalités
- [ ] Déploiement en production

### Post-déploiement
- [ ] Monitoring des métriques
- [ ] Vérification des logs
- [ ] Tests de charge (si nécessaire)
- [ ] Documentation des changements

## 🛠️ Maintenance

### Mises à jour
```bash
# Mise à jour des dépendances
composer update
npm update

# Mise à jour de l'infrastructure
terraform plan
terraform apply
```

### Sauvegarde
```bash
# Base de données
./scripts/backup-db.sh

# Fichiers d'application
./scripts/backup-files.sh
```

### Restauration
```bash
# Restaurer la base de données
./scripts/restore-db.sh backup-file.sql

# Restaurer les fichiers
./scripts/restore-files.sh backup-archive.tar.gz
```

## 📞 Support & Dépannage

### Logs Importants
- **Application** : `storage/logs/laravel.log`
- **Nginx** : `/var/log/nginx/`
- **PHP-FPM** : `/var/log/php-fpm/`
- **Docker** : `docker-compose logs [service]`

### Commandes Utiles
```bash
# Vérifier l'état des services
docker-compose ps
kubectl get pods

# Redémarrer un service
docker-compose restart [service]
kubectl rollout restart deployment/[name]

# Voir les logs en temps réel
docker-compose logs -f [service]
kubectl logs -f deployment/[name]
```

## 🔄 Prochaines Étapes

### Phase 1 : Infrastructure de Base ✅
- [x] Docker Compose local
- [x] Endpoints de santé
- [x] Scripts de déploiement cloud

### Phase 2 : Orchestration & Scaling
- [ ] Kubernetes HPA (Horizontal Pod Autoscaler)
- [ ] Load balancing avancé
- [ ] Service mesh (Istio)

### Phase 3 : Observabilité Avancée
- [ ] Prometheus + Grafana
- [ ] Distributed tracing (Jaeger)
- [ ] Centralized logging (ELK Stack)

### Phase 4 : Sécurité Avancée
- [ ] WAF (Web Application Firewall)
- [ ] Network policies
- [ ] Secrets rotation automatique

---

**Documentation mise à jour le :** $(date)
**Version :** 1.0.0
**Maintenu par :** Équipe DevOps FIT Platform










