# 🚀 Guide de Configuration GitHub Actions avec Notifications Email

## 📋 Vue d'Ensemble

Ce guide vous explique comment configurer vos notifications email avec GitHub Actions. Maintenant que nous avons identifié que vous utilisez GitHub et non GitLab, voici la configuration complète.

## ✅ **CE QUI EST MAINTENANT CONFIGURÉ :**

### **1. Workflow GitHub Actions** : `.github/workflows/ci-cd-notifications.yml`
- **Validation et Tests** : PHP, audit du projet
- **Build** : Compilation et optimisation
- **Déploiement** : Staging et production
- **Notifications** : Email automatique pour chaque événement

### **2. Script de Notifications** : `scripts/github-notifications.sh`
- **Adapté pour GitHub** : Variables `GITHUB_*`
- **Notifications SMTP** : Via PHPMailer
- **Design professionnel** : Emails HTML avec couleurs

## 🔧 **CONFIGURATION REQUISE DANS GITHUB :**

### **ÉTAPE 1 : Variables d'Environnement (Secrets)**

Allez dans votre repository GitHub :
1. **Settings** → **Secrets and variables** → **Actions**
2. **New repository secret** pour chaque variable :

```bash
# Configuration SMTP Gmail
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=im0668@gmail.com
SMTP_PASSWORD=qzneatcozirhlpst

# Configuration Staging (optionnel)
STAGING_USER=your_staging_user
STAGING_HOST=your_staging_host
STAGING_PATH=/path/to/staging
STAGING_URL=https://staging.yourdomain.com

# Configuration Production (optionnel)
PRODUCTION_USER=your_production_user
PRODUCTION_HOST=your_production_host
PRODUCTION_PATH=/path/to/production
PRODUCTION_URL=https://yourdomain.com

# Clés SSH (optionnel)
SSH_PRIVATE_KEY=your_private_key
```

### **ÉTAPE 2 : Environnements de Déploiement**

#### **Environnement Staging :**
1. **Settings** → **Environments** → **New environment**
2. **Environment name** : `staging`
3. **Protection rules** : Optionnel
4. **Environment variables** : Ajoutez les variables staging

#### **Environnement Production :**
1. **Settings** → **Environments** → **New environment**
2. **Environment name** : `production`
3. **Protection rules** : **Recommandé** (approbation requise)
4. **Environment variables** : Ajoutez les variables production

## 🎯 **DÉCLENCHEMENT AUTOMATIQUE :**

### **Triggers du Workflow :**
- **Push** sur `develop-v3` ou `main`
- **Pull Request** vers `develop-v3` ou `main`
- **Workflow Dispatch** (déclenchement manuel)

### **Jobs et Dépendances :**
```
validate-and-test → build → [notify-success/notify-failure]
                    ↓
              [deploy-staging/deploy-production]
```

## 📧 **TYPES DE NOTIFICATIONS :**

### **1. Notifications de Succès** 🎉
- **Déclencheur** : `validate-and-test` et `build` réussis
- **Contenu** : Détails du workflow, liens GitHub
- **Couleur** : Vert (#28a745)

### **2. Notifications d'Échec** ❌
- **Déclencheur** : Échec de n'importe quel job
- **Contenu** : Diagnostic de l'échec, liens de debug
- **Couleur** : Rouge (#dc3545)

### **3. Notifications d'Annulation** ⏹️
- **Déclencheur** : Workflow annulé manuellement
- **Contenu** : Informations sur l'annulation
- **Couleur** : Gris (#6c757d)

## 🚀 **COMMENT TESTER :**

### **Test Local (Déjà Fait) :**
```bash
# Test avec variables simulées
GITHUB_REPOSITORY="izharmahjoub/med-predictor" \
GITHUB_REF_NAME="develop-v3" \
GITHUB_SHA="test123" \
GITHUB_RUN_ID="12345" \
GITHUB_WORKFLOW="CI/CD Pipeline" \
GITHUB_ACTOR="izharmahjoub1" \
CI_PIPELINE_STATUS="success" \
./scripts/github-notifications.sh
```

### **Test dans GitHub :**
1. **Poussez du code** sur `develop-v3`
2. **Le workflow se déclenche** automatiquement
3. **Vous recevez un email** selon le résultat

## 📱 **FORMAT DES NOTIFICATIONS EMAIL :**

### **Design Professionnel :**
- **Header coloré** selon le statut
- **Grille d'informations** : Projet, branche, workflow, run ID
- **Message de commit** affiché
- **Boutons d'action** : Liens vers GitHub
- **Responsive design** pour tous les appareils

### **Contenu des Emails :**
- **Sujet** : `🚀 Workflow - izharmahjoub/med-predictor [develop-v3]`
- **Statut** : Succès/Échec/Annulé
- **Détails techniques** : IDs, SHA, utilisateur
- **Liens directs** vers GitHub
- **Timestamp** d'envoi

## 🔧 **PERSONNALISATION :**

### **Ajouter d'Autres Destinataires :**
Modifiez `scripts/github-notifications.sh` :
```php
$recipients = [
    'im0668@gmail.com',         // Email principal
    'dev-team@company.com',     // Équipe de développement
    'qa-team@company.com',      // Équipe de test
];
```

### **Modifier le Format des Emails :**
- **Template HTML** : Dans la fonction `send_notification`
- **Couleurs** : Variables `$color` selon le statut
- **Contenu** : Ajoutez des sections personnalisées

### **Ajouter d'Autres Types de Notifications :**
- **Tests de couverture** : Seuil personnalisable
- **Métriques de qualité** : Seuils d'audit
- **Déploiements** : Notifications pré/post

## 🚨 **DÉPANNAGE :**

### **Problèmes Courants :**

#### **1. Notifications non reçues :**
- ✅ Vérifiez vos secrets GitHub (SMTP_*)
- ✅ Vérifiez votre boîte de réception
- ✅ Vérifiez les spams
- ✅ Testez localement

#### **2. Workflow échoue :**
- ✅ Vérifiez les logs GitHub Actions
- ✅ Testez les tests localement
- ✅ Vérifiez la configuration PHP

#### **3. Variables manquantes :**
- ✅ Vérifiez les secrets GitHub
- ✅ Vérifiez les environnements
- ✅ Redémarrez le workflow

### **Logs et Debug :**
```bash
# Dans GitHub Actions
- Cliquez sur le job qui échoue
- Regardez les logs détaillés
- Vérifiez les variables d'environnement

# Test local
./scripts/github-notifications.sh
```

## 🎯 **PROCHAINES ÉTAPES :**

### **1. ✅ Configuration GitHub Actions (TERMINÉ)**
Votre workflow GitHub Actions est maintenant configuré !

### **2. 🔧 Configuration des Secrets (À FAIRE)**
- Ajoutez vos secrets SMTP dans GitHub
- Configurez les environnements staging/production

### **3. 🚀 Test du Workflow**
- Poussez du code pour déclencher le workflow
- Vérifiez les notifications email

### **4. 📱 Ajout Slack/Teams (Optionnel)**
Si vous voulez des notifications multi-plateformes

## 🎉 **FÉLICITATIONS !**

**Votre système CI/CD GitHub Actions est maintenant configuré avec des notifications email professionnelles !**

- ✅ **Workflow GitHub Actions** : Validation, tests, build, déploiement
- ✅ **Notifications automatiques** : Email pour chaque événement
- ✅ **Configuration flexible** : Staging et production
- ✅ **Intégration complète** : GitHub + SMTP + PHPMailer

**Chaque fois que vous poussez du code, vous recevrez automatiquement un email détaillé sur le statut de votre workflow !** 🚀

---

**📧 Configurez maintenant vos secrets GitHub et testez le workflow !**
