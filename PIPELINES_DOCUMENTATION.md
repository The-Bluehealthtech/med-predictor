# 🚀 Pipelines et Actions CI/CD - Med Predictor

## 📋 Vue d'ensemble

Ce document décrit la configuration complète des pipelines CI/CD pour le projet Med Predictor, incluant GitHub Actions et GitLab CI/CD.

## 🎯 Objectifs

- ✅ **Validation automatique** du code
- ✅ **Tests automatisés** (unitaires, intégration)
- ✅ **Audit de sécurité** des dépendances
- ✅ **Analyse de qualité** du code
- ✅ **Build optimisé** pour production
- ✅ **Déploiement automatisé** (staging/production)
- ✅ **Notifications** multi-canaux

## 🔧 Configuration GitHub Actions

### 📁 Fichiers de Configuration

| Fichier | Description | Statut |
|---------|-------------|--------|
| `.github/workflows/complete-pipeline.yml` | Pipeline principal complet | ✅ Actif |
| `.github/workflows/notifications-enhanced.yml` | Notifications avancées | ✅ Actif |
| `.github/workflows/advanced-ci.yml` | CI avancé existant | ✅ Actif |
| `.github/workflows/simple-ci.yml` | CI simple existant | ✅ Actif |

### 🚀 Pipeline Principal (`complete-pipeline.yml`)

#### Étapes du Pipeline

1. **🔍 Validation et Linting**
   - Validation de la syntaxe PHP
   - Vérification des fichiers de configuration
   - Validation des routes Laravel

2. **🧪 Tests Unitaires et d'Intégration**
   - Tests Laravel avec base de données MySQL
   - Tests avec Redis pour le cache
   - Rapport de couverture de code

3. **🔒 Audit de Sécurité**
   - Audit des dépendances Composer
   - Analyse statique avec PHPStan
   - Vérification des standards de code

4. **📊 Qualité du Code**
   - Analyse de complexité
   - Détection de code mort
   - Métriques de qualité

5. **🚀 Déploiement Staging**
   - Optimisation Laravel
   - Création du package de déploiement
   - Upload des artefacts

6. **📈 Rapport Final**
   - Génération du rapport de pipeline
   - Notifications automatiques

#### Déclencheurs

```yaml
on:
  push:
    branches: [ develop-v3, main, master ]
  pull_request:
    branches: [ develop-v3, main, master ]
  workflow_dispatch:
  schedule:
    - cron: '0 2 * * 1' # Tous les lundis à 2h
```

## 🔧 Configuration GitLab CI/CD

### 📁 Fichiers de Configuration

| Fichier | Description | Statut |
|---------|-------------|--------|
| `.gitlab-ci-complete.yml` | Pipeline complet optimisé | ✅ Nouveau |
| `.gitlab-ci.yml` | Pipeline minimal existant | ✅ Actif |
| `.gitlab-ci-optimized.yml` | Pipeline optimisé existant | ✅ Actif |

### 🚀 Pipeline Complet (`gitlab-ci-complete.yml`)

#### Étapes du Pipeline

1. **🔍 Validation**
   - Validation de la syntaxe PHP
   - Configuration de l'environnement
   - Vérification des dépendances

2. **🧪 Tests**
   - Tests avec MySQL et Redis
   - Migrations de base de données
   - Rapport de couverture

3. **🔒 Sécurité**
   - Audit des dépendances
   - Analyse statique
   - Vérification des standards

4. **📊 Qualité**
   - Analyse de complexité
   - Métriques de code
   - Détection de code mort

5. **📦 Build**
   - Optimisation Laravel
   - Création du package
   - Préparation du déploiement

6. **🚀 Déploiement Staging**
   - Déploiement manuel vers staging
   - Environnement configuré

7. **🚀 Déploiement Production**
   - Déploiement manuel vers production
   - Environnement sécurisé

8. **📈 Rapport**
   - Rapport final détaillé
   - Notifications automatiques

#### Variables d'Environnement

```yaml
variables:
  PHP_VERSION: "8.2"
  NODE_VERSION: "18"
  TEST_COVERAGE_THRESHOLD: "70"
  SECURITY_SCORE_THRESHOLD: "80"
  CODE_QUALITY_THRESHOLD: "85"
```

## 📧 Système de Notifications

### 🔔 Canaux de Notification

1. **Slack**
   - Canal: `#med-predictor`
   - Notifications en temps réel
   - Statuts détaillés

2. **Email**
   - Destinataire: `team@med-predictor.com`
   - Rapports détaillés
   - Alertes d'échec

3. **Microsoft Teams**
   - Notifications d'équipe
   - Intégration avec l'organisation

4. **Rapports Automatiques**
   - Génération de rapports détaillés
   - Métriques de performance
   - Recommandations d'action

### 🔧 Configuration des Secrets

#### GitHub Secrets

```bash
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
TEAMS_WEBHOOK_URL=https://outlook.office.com/webhook/...
```

#### GitLab Variables

```bash
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
TEAMS_WEBHOOK_URL=https://outlook.office.com/webhook/...
```

## 🎯 Seuils de Qualité

### 📊 Métriques Configurables

| Métrique | Seuil | Description |
|----------|-------|-------------|
| Couverture de Tests | 70% | Pourcentage de code testé |
| Score de Sécurité | 80% | Évaluation de la sécurité |
| Qualité du Code | 85% | Standards de code |

### 🔧 Configuration

```yaml
# Variables d'environnement configurables
TEST_COVERAGE_THRESHOLD: "${TEST_COVERAGE_THRESHOLD:-70}"
SECURITY_SCORE_THRESHOLD: "${SECURITY_SCORE_THRESHOLD:-80}"
CODE_QUALITY_THRESHOLD: "${CODE_QUALITY_THRESHOLD:-85}"
```

## 🚀 Déploiement

### 🌍 Environnements

1. **Staging**
   - URL: `https://staging.med-predictor.com`
   - Branche: `develop-v3`
   - Déploiement: Automatique après tests

2. **Production**
   - URL: `https://med-predictor.com`
   - Branche: `main` ou `master`
   - Déploiement: Manuel après validation

### 📦 Processus de Déploiement

1. **Build**
   - Installation des dépendances
   - Optimisation Laravel
   - Création du package

2. **Validation**
   - Tests automatisés
   - Vérifications de sécurité
   - Contrôles de qualité

3. **Déploiement**
   - Upload du package
   - Configuration de l'environnement
   - Vérification post-déploiement

## 🔍 Monitoring et Alertes

### 📊 Métriques Surveillées

- ✅ **Temps d'exécution** des pipelines
- ✅ **Taux de réussite** des déploiements
- ✅ **Couverture de code** des tests
- ✅ **Vulnérabilités** de sécurité
- ✅ **Qualité du code** (complexité, standards)

### 🚨 Alertes Automatiques

- ❌ **Échec de pipeline** → Notification immédiate
- ⚠️ **Seuils dépassés** → Alerte d'équipe
- 🔒 **Vulnérabilités** → Alerte sécurité
- 📉 **Performance** → Alerte monitoring

## 🛠️ Utilisation

### 🚀 Démarrage Rapide

1. **Activation des Pipelines**
   ```bash
   # Les pipelines se déclenchent automatiquement sur push/PR
   git push origin develop-v3
   ```

2. **Monitoring**
   - GitHub: Actions → Pipelines
   - GitLab: CI/CD → Pipelines

3. **Notifications**
   - Configurer les webhooks Slack/Teams
   - Vérifier les emails automatiques

### 🔧 Configuration Avancée

1. **Modification des Seuils**
   ```bash
   # Variables d'environnement
   export TEST_COVERAGE_THRESHOLD=80
   export SECURITY_SCORE_THRESHOLD=90
   ```

2. **Ajout de Tests**
   ```bash
   # Créer de nouveaux tests
   php artisan make:test NouveauTest
   ```

3. **Personnalisation des Notifications**
   ```yaml
   # Modifier les canaux de notification
   channels:
     - slack
     - email
     - teams
   ```

## 📈 Métriques et Rapports

### 📊 Rapports Automatiques

- **Rapport de Pipeline**: Statut détaillé de chaque étape
- **Rapport de Qualité**: Métriques de code et recommandations
- **Rapport de Sécurité**: Vulnérabilités et actions requises
- **Rapport de Performance**: Temps d'exécution et optimisations

### 📈 Tableau de Bord

```yaml
# Métriques disponibles
metrics:
  pipeline_success_rate: "95%"
  average_execution_time: "15 minutes"
  test_coverage: "75%"
  security_score: "85%"
  code_quality: "90%"
```

## 🔧 Maintenance

### 🛠️ Tâches Régulières

1. **Mise à jour des Dépendances**
   ```bash
   composer update
   npm update
   ```

2. **Révision des Seuils**
   - Ajuster les seuils selon les besoins
   - Analyser les métriques historiques

3. **Optimisation des Pipelines**
   - Réduire les temps d'exécution
   - Améliorer la parallélisation

### 🔄 Mise à Jour

```bash
# Mise à jour des workflows
git pull origin main
git checkout develop-v3
git merge main
git push origin develop-v3
```

## 📞 Support

### 🆘 En Cas de Problème

1. **Pipeline en Échec**
   - Consulter les logs détaillés
   - Vérifier les tests locaux
   - Contacter l'équipe DevOps

2. **Notifications Manquantes**
   - Vérifier la configuration des webhooks
   - Tester les connexions
   - Consulter les logs de notification

3. **Performance Dégradée**
   - Analyser les métriques de pipeline
   - Optimiser les étapes lentes
   - Considérer l'upgrade des ressources

### 📧 Contacts

- **DevOps Team**: devops@med-predictor.com
- **Security Team**: security@med-predictor.com
- **Development Team**: dev@med-predictor.com

---

## 🎉 Conclusion

Les pipelines CI/CD de Med Predictor offrent une solution complète et automatisée pour le développement, les tests, la sécurité et le déploiement. Cette configuration garantit la qualité du code et la fiabilité des déploiements tout en fournissant une visibilité complète sur le processus de développement.

**🚀 Prêt pour la production !**
