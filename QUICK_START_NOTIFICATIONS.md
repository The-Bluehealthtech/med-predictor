# 🚀 Démarrage Rapide - Notifications CI/CD

## ⚡ Configuration en 5 Minutes

### 1. **Installation Automatique** (Recommandé)
```bash
# Exécutez le script d'installation guidée
./scripts/setup-webhooks.sh
```

### 2. **Configuration Manuelle**
Éditez `notifications-config.env` avec vos vraies informations :
```bash
# Slack
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/VOTRE_WORKSPACE/VOTRE_CHANNEL/VOTRE_TOKEN

# Teams
TEAMS_WEBHOOK_URL=https://votre-org.webhook.office.com/webhookb2/VOTRE_WEBHOOK_ID

# Gmail
SMTP_USERNAME=votre-email@gmail.com
SMTP_PASSWORD=votre-mot-de-passe-app
```

### 3. **Test des Notifications**
```bash
# Test automatique
./scripts/test-notifications.sh

# Test manuel interactif
./scripts/test-webhooks-manually.sh
```

## 🔗 Liens de Configuration

### **Slack**
- [Créer une App Slack](https://api.slack.com/apps)
- [Documentation Webhooks](https://api.slack.com/messaging/webhooks)

### **Microsoft Teams**
- [Documentation Webhooks](https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/)

### **Gmail**
- [Authentification 2FA](https://myaccount.google.com/security)
- [Mots de passe d'application](https://myaccount.google.com/apppasswords)

## 📱 Exemples de Messages

### **Slack**
```json
{
  "text": "🎉 Pipeline CI/CD réussi !",
  "attachments": [{
    "color": "good",
    "title": "Déploiement Staging",
    "text": "Le déploiement en staging s'est bien passé"
  }]
}
```

### **Teams**
```json
{
  "@type": "MessageCard",
  "themeColor": "00FF00",
  "title": "Pipeline Réussi",
  "text": "🎉 Pipeline CI/CD réussi pour la branche develop"
}
```

## 🧪 Test Rapide

```bash
# Test Slack (remplacez par votre webhook)
curl -X POST -H 'Content-type: application/json' \
  --data '{"text":"🧪 Test rapide Slack"}' \
  https://hooks.slack.com/services/VOTRE_WEBHOOK

# Test Teams (remplacez par votre webhook)
curl -X POST -H 'Content-type: application/json' \
  --data '{"text":"🧪 Test rapide Teams"}' \
  https://votre-org.webhook.office.com/webhookb2/VOTRE_ID
```

## 🚨 Dépannage Rapide

### **Webhook non reçu**
- ✅ Vérifiez l'URL du webhook
- ✅ Vérifiez que l'app est installée (Slack/Teams)
- ✅ Testez avec curl

### **Erreur SMTP**
- ✅ Vérifiez l'authentification 2FA Gmail
- ✅ Vérifiez le mot de passe d'application
- ✅ Testez la connexion au port 587

### **Erreur de syntaxe**
- ✅ Vérifiez le format JSON
- ✅ Vérifiez les caractères spéciaux
- ✅ Utilisez le script de test

## 📚 Documentation Complète

- **Guide détaillé** : `NOTIFICATIONS_SETUP_GUIDE.md`
- **Configuration** : `notifications.yml`
- **Variables** : `notifications-config.env`

## 🎯 Prochaines Étapes

1. **Configurez vos webhooks** avec `./scripts/setup-webhooks.sh`
2. **Testez les notifications** avec `./scripts/test-webhooks-manually.sh`
3. **Intégrez avec GitLab CI** en ajoutant les variables dans GitLab
4. **Personnalisez les messages** selon vos besoins

---

**🎉 Vous êtes prêt !** Vos notifications CI/CD fonctionneront en quelques minutes.
