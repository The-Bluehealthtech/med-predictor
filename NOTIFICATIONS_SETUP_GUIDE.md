# 🔔 Guide de Configuration des Notifications CI/CD

## 📋 Vue d'Ensemble

Ce guide vous explique comment configurer les notifications automatiques pour votre pipeline CI/CD, incluant Slack, Microsoft Teams, Email et Webhooks personnalisés.

## 🚀 Configuration Rapide

### 1. Slack

#### Créer un Webhook Slack
1. Allez sur [api.slack.com/apps](https://api.slack.com/apps)
2. Cliquez sur "Create New App" → "From scratch"
3. Donnez un nom à votre app (ex: "CI/CD Notifications")
4. Sélectionnez votre workspace
5. Dans "OAuth & Permissions", ajoutez le scope `incoming-webhook`
6. Dans "Incoming Webhooks", activez et créez un webhook
7. Copiez l'URL du webhook

#### Configuration
```bash
# Éditez notifications-config.env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR_WORKSPACE/YOUR_CHANNEL/YOUR_TOKEN
SLACK_CHANNEL=#ci-cd-alerts
```

### 2. Microsoft Teams

#### Créer un Webhook Teams
1. Ouvrez le canal Teams où vous voulez recevoir les notifications
2. Cliquez sur "..." → "Connectors"
3. Cherchez "Incoming Webhook" et configurez-le
4. Donnez un nom (ex: "CI/CD Alerts")
5. Copiez l'URL du webhook

#### Configuration
```bash
# Éditez notifications-config.env
TEAMS_WEBHOOK_URL=https://your-org.webhook.office.com/webhookb2/YOUR_WEBHOOK_ID
```

### 3. Email (Gmail)

#### Configuration Gmail
1. Activez l'authentification à 2 facteurs sur votre compte Gmail
2. Générez un mot de passe d'application
3. Utilisez ces informations dans la configuration

#### Configuration
```bash
# Éditez notifications-config.env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

### 4. Webhook Personnalisé

#### Créer un Endpoint
1. Créez un endpoint HTTP qui peut recevoir des notifications
2. Configurez l'authentification si nécessaire
3. Testez avec des outils comme Postman ou curl

#### Configuration
```bash
# Éditez notifications-config.env
WEBHOOK_URL=https://your-webhook-endpoint.com/notify
```

## 🔧 Configuration Avancée

### Variables d'Environnement

```bash
# notifications-config.env
export SLACK_WEBHOOK_URL="https://hooks.slack.com/services/..."
export TEAMS_WEBHOOK_URL="https://your-org.webhook.office.com/..."
export SMTP_HOST="smtp.gmail.com"
export SMTP_PORT="587"
export SMTP_USERNAME="your-email@gmail.com"
export SMTP_PASSWORD="your-app-password"
export WEBHOOK_URL="https://your-webhook-endpoint.com/notify"
export METRICS_ENDPOINT="https://your-metrics-endpoint.com"
export DEPLOY_ENVIRONMENT="staging"
```

### Intégration avec GitLab CI

```yaml
# .gitlab-ci.yml
variables:
  SLACK_WEBHOOK_URL: $SLACK_WEBHOOK_URL
  TEAMS_WEBHOOK_URL: $TEAMS_WEBHOOK_URL

stages:
  - test
  - build
  - deploy

notify_success:
  stage: .post
  script:
    - curl -X POST -H 'Content-type: application/json' --data '{"text":"🎉 Pipeline réussi pour $CI_COMMIT_REF_NAME"}' $SLACK_WEBHOOK_URL
  when: on_success

notify_failure:
  stage: .post
  script:
    - curl -X POST -H 'Content-type: application/json' --data '{"text":"❌ Pipeline échoué pour $CI_COMMIT_REF_NAME"}' $SLACK_WEBHOOK_URL
  when: on_failure
```

### Intégration avec Jenkins

```groovy
// Jenkinsfile
pipeline {
    agent any
    
    environment {
        SLACK_WEBHOOK_URL = credentials('slack-webhook')
        TEAMS_WEBHOOK_URL = credentials('teams-webhook')
    }
    
    stages {
        stage('Test') {
            steps {
                echo 'Running tests...'
            }
            post {
                success {
                    script {
                        def message = "🎉 Tests réussis pour ${env.BRANCH_NAME}"
                        sh "curl -X POST -H 'Content-type: application/json' --data '{\"text\":\"${message}\"}' ${SLACK_WEBHOOK_URL}"
                    }
                }
                failure {
                    script {
                        def message = "❌ Tests échoués pour ${env.BRANCH_NAME}"
                        sh "curl -X POST -H 'Content-type: application/json' --data '{\"text\":\"${message}\"}' ${SLACK_WEBHOOK_URL}"
                    }
                }
            }
        }
    }
}
```

## 🧪 Test des Notifications

### Test Manuel

```bash
# Test Slack
curl -X POST -H 'Content-type: application/json' \
  --data '{"text":"🧪 Test de notification Slack"}' \
  $SLACK_WEBHOOK_URL

# Test Teams
curl -X POST -H 'Content-type: application/json' \
  --data '{"text":"🧪 Test de notification Teams"}' \
  $TEAMS_WEBHOOK_URL

# Test Email
echo "Test de notification email" | mail -s "Test CI/CD" recipient@example.com
```

### Test Automatique

```bash
# Exécutez le script de test
./scripts/test-notifications.sh
```

## 📊 Types de Notifications

### 1. Pipeline GitLab CI/CD
- **Succès** : Pipeline réussi
- **Échec** : Pipeline échoué
- **Annulation** : Pipeline annulé

### 2. Déploiements
- **Pré-déploiement** : Notification avant déploiement
- **Succès** : Déploiement réussi
- **Échec** : Déploiement échoué
- **Rollback** : Retour en arrière automatique

### 3. Tests
- **Succès** : Tous les tests passent
- **Échec** : Tests échoués
- **Couverture** : Rapport de couverture de code

### 4. Sécurité
- **Vulnérabilités** : Alertes de sécurité
- **Audit** : Résultats d'audit de sécurité

### 5. Performance
- **Dégradation** : Baisse de performance
- **Métriques** : Rapports de performance

## 🚨 Seuils d'Alerte

### Qualité du Code
- **Score de propreté** : 70%
- **Tables orphelines** : 30%
- **Modèles inutilisés** : 15
- **Couverture de tests** : 80%

### Performance
- **Temps de réponse** : 2.0s
- **Utilisation mémoire** : 512MB
- **Utilisation CPU** : 80%
- **Utilisation disque** : 85%

### Sécurité
- **Vulnérabilités critiques** : 0
- **Vulnérabilités élevées** : 2
- **Vulnérabilités moyennes** : 5

## 📈 Métriques et Rapports

### Génération Automatique
- **Rapports HTML** : Interface web interactive
- **Rapports JSON** : Données structurées
- **Rapports PDF** : Documentation imprimable
- **Rapports XML** : Intégration avec d'autres outils

### Rétention
- **Durée de conservation** : 30 jours
- **Stockage local** : Sauvegarde locale
- **Artifacts GitLab** : Intégration GitLab
- **Stockage S3** : Sauvegarde cloud (optionnel)

## 🔒 Sécurité

### Authentification
- **Webhooks sécurisés** : Utilisez HTTPS
- **Tokens d'accès** : Stockez les secrets de manière sécurisée
- **Validation des données** : Vérifiez les entrées

### Variables d'Environnement
- **Ne commitez jamais** les secrets dans Git
- **Utilisez GitLab CI Variables** pour les secrets
- **Chiffrez** les mots de passe sensibles

## 🛠️ Dépannage

### Problèmes Courants

#### Webhook Slack non reçu
1. Vérifiez l'URL du webhook
2. Assurez-vous que l'app Slack est installée dans le canal
3. Vérifiez les permissions de l'app

#### Webhook Teams non reçu
1. Vérifiez l'URL du webhook
2. Assurez-vous que le connecteur est activé
3. Vérifiez les permissions du canal

#### Emails non reçus
1. Vérifiez la configuration SMTP
2. Assurez-vous que l'authentification à 2 facteurs est activée
3. Vérifiez le mot de passe d'application

### Logs et Debug

```bash
# Activer le mode debug
export DEBUG=true

# Vérifier les logs
tail -f logs/notifications.log

# Tester la connectivité
curl -v $SLACK_WEBHOOK_URL
```

## 📚 Ressources

### Documentation Officielle
- [Slack API](https://api.slack.com/)
- [Microsoft Teams Webhooks](https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/)
- [GitLab CI/CD](https://docs.gitlab.com/ee/ci/)
- [Jenkins Pipeline](https://www.jenkins.io/doc/book/pipeline/)

### Outils Utiles
- [Slack Block Kit Builder](https://app.slack.com/block-kit-builder)
- [Teams Message Card Playground](https://messagecardplayground.azurewebsites.net/)
- [Postman](https://www.postman.com/) pour tester les webhooks

## 🎯 Prochaines Étapes

1. **Configurez vos webhooks réels** (Slack, Teams, Email)
2. **Testez les notifications** avec le script de test
3. **Intégrez avec votre pipeline CI/CD** (GitLab, Jenkins)
4. **Configurez les métriques** et alertes personnalisées
5. **Surveillez et ajustez** les seuils d'alerte

---

**🎉 Félicitations !** Votre système de notifications CI/CD est maintenant configuré et prêt à vous tenir informé de tous les événements importants de votre pipeline.
