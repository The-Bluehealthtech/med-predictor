# 🚀 Guide de Déploiement CI/CD Complet - Med-Predictor

## 📋 Vue d'Ensemble

Ce guide décrit le processus complet de déploiement CI/CD pour le projet Med-Predictor. Le pipeline est configuré pour fonctionner avec GitLab CI/CD, Jenkins, GitHub Actions et Docker.

## 🏗️ Architecture du Pipeline

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Code Source   │───▶│   Pipeline      │───▶│   Déploiement   │
│   (Git)         │    │   CI/CD         │    │   Automatisé    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │   Tests &       │
                       │   Qualité       │
                       └─────────────────┘
```

## 🛠️ Prérequis

### Outils Requis
- **Git** (version 2.30+)
- **Docker** (version 20.10+)
- **Docker Compose** (version 2.0+)
- **PHP** (version 8.2+)
- **Composer** (version 2.0+)
- **Node.js** (version 16+)
- **NPM** (version 8+)

### Variables d'Environnement
Créez un fichier `.env` basé sur `env.ci-cd` :

```bash
cp env.ci-cd .env
# Éditez le fichier .env avec vos valeurs
```

## 🚀 Démarrage Rapide

### 1. Test du Pipeline Local

```bash
# Exécuter les tests CI/CD localement
./scripts/run-ci-tests.sh

# Vérifier la configuration Docker
docker-compose -f docker-compose.ci.yml config
```

### 2. Déploiement Automatisé

```bash
# Déploiement en staging
./deploy-ci-cd.sh staging

# Déploiement en production
./deploy-ci-cd.sh production
```

## 🔧 Configuration Détaillée

### GitLab CI/CD

Le pipeline principal est configuré dans `.gitlab-ci.yml` avec les étapes suivantes :

1. **Validation** - Vérification des prérequis
2. **Configuration** - Installation des dépendances
3. **Audit** - Analyse de la base de données
4. **Rapport** - Génération des rapports HTML
5. **Qualité** - Vérification des seuils de qualité
6. **Déploiement** - Déploiement conditionnel

#### Variables GitLab Requises

```yaml
# Dans GitLab > Settings > CI/CD > Variables
CLEANLINESS_THRESHOLD: 70
ORPHAN_TABLES_THRESHOLD: 30
UNUSED_MODELS_THRESHOLD: 15
AUDIT_TIMEOUT: 300
SLACK_WEBHOOK_URL: "https://hooks.slack.com/..."
TEAMS_WEBHOOK_URL: "https://..."
WEBHOOK_URL: "https://..."
```

### Jenkins

Le pipeline Jenkins est configuré dans `Jenkinsfile` avec :

- Installation automatique des dépendances
- Exécution des tests
- Audit de la base de données
- Génération de rapports
- Notifications

#### Configuration Jenkins

1. Créez un pipeline Jenkins
2. Pointez vers le `Jenkinsfile`
3. Configurez les credentials nécessaires
4. Activez les notifications

### GitHub Actions

Les workflows sont configurés dans `.github/workflows/` :

- `ci-cd.yml` - Pipeline principal
- `db-audit.yml` - Audit de base de données
- `deploy-fit.yml` - Déploiement spécifique

## 🐳 Configuration Docker

### Services CI/CD

Le fichier `docker-compose.ci.yml` définit :

- **audit-runner** - Service principal d'audit
- **sqlite-db** - Base de données de test
- **report-generator** - Générateur de rapports
- **notification-service** - Service de notifications
- **metrics-monitor** - Surveillance des métriques
- **cleanup-service** - Nettoyage automatique

### Construction d'Image

```bash
# Construire l'image CI/CD
docker build -f Dockerfile.ci -t med-predictor:ci .

# Exécuter les tests
docker run --rm med-predictor:ci
```

## 🧪 Tests et Qualité

### Types de Tests

1. **Tests de Syntaxe PHP**
   ```bash
   find app -name "*.php" -exec php -l {} \;
   ```

2. **Tests PHPUnit**
   ```bash
   php vendor/bin/phpunit --configuration phpunit.ci.xml
   ```

3. **Tests de Qualité**
   ```bash
   vendor/bin/php-cs-fixer fix --dry-run
   vendor/bin/phpstan analyse app
   vendor/bin/phpmd app text phpmd.xml
   ```

4. **Tests de Sécurité**
   ```bash
   composer audit
   ```

5. **Tests de Performance**
   ```bash
   # Test de temps de réponse
   curl -o /dev/null -s -w "%{time_total}" $APP_URL
   ```

### Seuils de Qualité

- **Score de Propreté** : ≥ 70%
- **Tables Orphelines** : ≤ 30%
- **Modèles Non Utilisés** : ≤ 15
- **Couverture de Tests** : ≥ 80%
- **Qualité du Code** : ≥ 80%

## 📊 Monitoring et Métriques

### Métriques Collectées

- **Système** : CPU, mémoire, disque, réseau
- **Application** : Temps de réponse, taux d'erreur, débit
- **Qualité** : Couverture de tests, score de qualité, sécurité
- **Performance** : Latence, utilisation des ressources

### Surveillance Continue

```bash
# Vérification de santé
curl -f $APP_URL/health

# Statut de la base de données
php artisan tinker --execute="echo DB::connection()->getPdo() ? 'OK' : 'KO';"
```

## 🔔 Notifications et Alertes

### Canaux Supportés

- **Slack** - Notifications en temps réel
- **Teams** - Intégration Microsoft
- **Email** - Alertes par email
- **Webhook** - Intégrations personnalisées

### Événements Surveillés

- Succès/échec de pipeline
- Succès/échec de déploiement
- Échec de tests
- Alertes de sécurité
- Dégradation de performance

## 🚀 Déploiement

### Stratégies

1. **Blue-Green** - Déploiement sans interruption
2. **Rolling** - Mise à jour progressive
3. **Canary** - Déploiement partiel

### Processus de Déploiement

```bash
# 1. Vérification des prérequis
./deploy-ci-cd.sh check

# 2. Sauvegarde automatique
./deploy-ci-cd.sh backup

# 3. Validation du code
./deploy-ci-cd.sh validate

# 4. Déploiement
./deploy-ci-cd.sh staging

# 5. Vérification post-déploiement
./deploy-ci-cd.sh verify
```

### Rollback Automatique

Le système détecte automatiquement les échecs et :

1. Arrête le déploiement
2. Restaure la version précédente
3. Envoie des notifications
4. Génère un rapport d'incident

## 📈 Rapports et Analytics

### Rapports Générés

- **Rapport d'Audit HTML** - Analyse complète de la base de données
- **Rapport de Tests** - Résultats des tests automatisés
- **Rapport de Couverture** - Analyse de la couverture de code
- **Rapport de Performance** - Métriques de performance
- **Rapport de Sécurité** - Vulnérabilités détectées

### Stockage des Rapports

- **Local** : `reports/` et `coverage/`
- **GitLab Artifacts** : Stockage automatique
- **S3** : Stockage cloud (optionnel)

## 🔒 Sécurité

### Vérifications Automatiques

- Analyse des dépendances
- Scan de vulnérabilités
- Vérification des permissions
- Audit de sécurité

### Bonnes Pratiques

- Variables d'environnement sécurisées
- Credentials chiffrés
- Accès limité aux environnements
- Logs d'audit complets

## 🚨 Gestion des Incidents

### Procédure d'Urgence

1. **Détection** - Surveillance automatique
2. **Alerte** - Notification immédiate
3. **Diagnostic** - Analyse automatique
4. **Correction** - Rollback ou correction
5. **Rapport** - Documentation de l'incident

### Contacts d'Urgence

- **DevOps** : devops@med-predictor.com
- **Tech Lead** : tech-lead@med-predictor.com
- **Sécurité** : security@med-predictor.com

## 📚 Maintenance

### Tâches Régulières

- **Quotidien** : Vérification des logs et métriques
- **Hebdomadaire** : Analyse des rapports de qualité
- **Mensuel** : Révision des seuils et alertes
- **Trimestriel** : Audit complet du pipeline

### Mise à Jour

```bash
# Mise à jour des dépendances
composer update
npm update

# Mise à jour des images Docker
docker-compose -f docker-compose.ci.yml pull

# Mise à jour de la configuration
git pull origin main
```

## 🎯 Bonnes Pratiques

### Développement

1. **Tests Locaux** - Exécutez les tests avant de pousser
2. **Validation** - Vérifiez la qualité du code
3. **Documentation** - Mettez à jour la documentation
4. **Review** - Demandez des reviews de code

### Déploiement

1. **Staging First** - Testez toujours en staging
2. **Monitoring** - Surveillez les métriques
3. **Rollback** - Gardez un plan de rollback
4. **Communication** - Informez l'équipe

### Opérations

1. **Logs** - Consultez les logs régulièrement
2. **Métriques** - Surveillez les performances
3. **Alertes** - Répondez aux alertes rapidement
4. **Documentation** - Documentez les incidents

## 🔧 Dépannage

### Problèmes Courants

#### Pipeline Échoue

```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier la configuration
php artisan config:clear
php artisan cache:clear

# Re-exécuter les tests
./scripts/run-ci-tests.sh
```

#### Déploiement Échoue

```bash
# Vérifier l'environnement
./deploy-ci-cd.sh check

# Vérifier les permissions
chmod -R 755 storage bootstrap/cache

# Vérifier la base de données
php artisan migrate:status
```

#### Tests Échouent

```bash
# Vérifier les dépendances
composer install
npm install

# Vérifier la configuration de test
cp .env.example .env.testing

# Exécuter les tests individuellement
php vendor/bin/phpunit --filter TestName
```

### Logs et Debug

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs CI/CD
tail -f deploy-*.log

# Logs Docker
docker-compose -f docker-compose.ci.yml logs

# Logs GitLab
# Voir dans GitLab > CI/CD > Pipelines
```

## 📞 Support

### Ressources

- **Documentation** : Ce guide et les README associés
- **Code Source** : Repository GitLab
- **Issues** : GitLab Issues pour les bugs
- **Wiki** : Documentation détaillée

### Contact

- **Équipe DevOps** : devops@med-predictor.com
- **Équipe Développement** : dev-team@med-predictor.com
- **Tech Lead** : tech-lead@med-predictor.com

## 🎉 Conclusion

Ce pipeline CI/CD complet offre :

✅ **Automatisation** - Déploiement sans intervention manuelle  
✅ **Qualité** - Tests et vérifications automatiques  
✅ **Sécurité** - Vérifications de sécurité intégrées  
✅ **Monitoring** - Surveillance continue des performances  
✅ **Rapports** - Documentation complète des déploiements  
✅ **Rollback** - Récupération automatique en cas de problème  

Le système est conçu pour être robuste, sécurisé et facile à maintenir. Suivez ce guide pour des déploiements réussis et une qualité de code optimale.

---

**Dernière mise à jour** : $(date)  
**Version** : 2.0  
**Maintenu par** : Équipe DevOps Med-Predictor
