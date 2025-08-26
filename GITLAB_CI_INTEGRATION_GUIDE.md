# 🚀 Guide d'Intégration GitLab CI avec Notifications Email

## 📋 Vue d'Ensemble

Ce guide vous explique comment vos notifications email sont maintenant intégrées avec votre pipeline GitLab CI/CD. Chaque exécution de pipeline déclenchera automatiquement des notifications par email.

## ✅ **CE QUI EST MAINTENANT CONFIGURÉ :**

### **1. Pipeline GitLab CI avec Notifications**
- **Validation YAML** : Vérification de la syntaxe
- **Tests PHP** : Exécution des tests avec couverture
- **Audit du projet** : Analyse automatique du code
- **Build** : Compilation et optimisation
- **Déploiement** : Staging et production
- **Notifications** : Email automatique pour chaque événement

### **2. Types de Notifications Automatiques**
- **🎉 Pipeline Réussi** : Email de succès avec détails
- **❌ Pipeline Échoué** : Email d'échec avec diagnostic
- **⏹️ Pipeline Annulé** : Email d'annulation
- **📊 Rapports d'Audit** : Résultats de qualité du code

## 🔧 **CONFIGURATION ACTUELLE :**

### **Variables GitLab CI Configurées :**
```yaml
variables:
  NOTIFICATION_EMAIL: "im0668@gmail.com"
  NOTIFICATION_SCRIPT: "scripts/gitlab-notifications.sh"
  PHP_VERSION: "8.2"
  PHPUNIT_COVERAGE_MIN: "80"
```

### **Stages du Pipeline :**
1. **validate** : Validation YAML et syntaxe
2. **test** : Tests PHP et audit du projet
3. **build** : Compilation de l'application
4. **deploy** : Déploiement (staging/production)
5. **notify** : Envoi des notifications email

## 📧 **FORMAT DES NOTIFICATIONS EMAIL :**

### **Design Professionnel :**
- **Header coloré** selon le statut (vert=succès, rouge=échec)
- **Grille d'informations** : Projet, branche, pipeline, commit
- **Message de commit** affiché
- **Boutons d'action** : Liens vers le projet et le pipeline
- **Responsive design** pour mobile et desktop

### **Contenu des Emails :**
- **Sujet** : `🚀 Pipeline - med-predictor [develop-v3]`
- **Statut** : Succès/Échec/Annulé
- **Détails techniques** : IDs, SHA, utilisateur
- **Liens directs** vers GitLab
- **Timestamp** d'envoi

## 🎯 **DÉCLENCHEMENT AUTOMATIQUE :**

### **Notifications de Succès :**
- **Déclencheur** : `$CI_PIPELINE_STATUS == "success"`
- **Moment** : Après les stages `test` et `build`
- **Destinataire** : `im0668@gmail.com`

### **Notifications d'Échec :**
- **Déclencheur** : `$CI_PIPELINE_STATUS == "failed"`
- **Moment** : Immédiatement après l'échec
- **Destinataire** : `im0668@gmail.com`

### **Notifications d'Annulation :**
- **Déclencheur** : `$CI_PIPELINE_STATUS == "canceled"`
- **Moment** : Immédiatement après l'annulation
- **Destinataire** : `im0668@gmail.com`

## 🔍 **VARIABLES GITLAB CI UTILISÉES :**

### **Variables Automatiques :**
```bash
CI_PROJECT_NAME          # Nom du projet (izhar7-project)
CI_COMMIT_REF_NAME       # Nom de la branche (develop-v3)
CI_COMMIT_SHA           # SHA du commit
CI_COMMIT_MESSAGE       # Message du commit
CI_PIPELINE_ID          # ID du pipeline
CI_JOB_ID               # ID du job
CI_PROJECT_URL          # URL du projet GitLab
```

### **Variables Personnalisées :**
```bash
NOTIFICATION_EMAIL       # Email de destination
NOTIFICATION_SCRIPT      # Script de notification
PHP_VERSION             # Version PHP utilisée
```

## 🚀 **COMMENT TESTER :**

### **1. Test Local (Déjà Fait) :**
```bash
./scripts/gitlab-notifications.sh
```

### **2. Test avec Variables GitLab :**
```bash
# Simuler un pipeline réussi
CI_PIPELINE_STATUS="success" ./scripts/gitlab-notifications.sh

# Simuler un pipeline échoué
CI_PIPELINE_STATUS="failed" ./scripts/gitlab-notifications.sh
```

### **3. Test dans GitLab :**
- **Poussez du code** sur la branche `develop-v3`
- **Le pipeline se déclenchera automatiquement**
- **Vous recevrez un email** selon le résultat

## 📱 **EXEMPLES DE NOTIFICATIONS :**

### **Pipeline Réussi :**
```
🚀 Pipeline - med-predictor [develop-v3]
✅ Pipeline GitLab CI/CD exécuté avec succès !

📊 Détails :
- Projet: med-predictor
- Branche: develop-v3
- Pipeline ID: #12345
- Commit: a1b2c3d4
- Utilisateur: izhar

🔗 Actions :
[Voir le Projet] [Voir le Pipeline]
```

### **Pipeline Échoué :**
```
🚀 Pipeline - med-predictor [develop-v3]
❌ Pipeline GitLab CI/CD a échoué !

📊 Détails :
- Projet: med-predictor
- Branche: develop-v3
- Pipeline ID: #12345
- Commit: a1b2c3d4
- Utilisateur: izhar

🔗 Actions :
[Voir le Projet] [Voir le Pipeline]
```

## 🔧 **PERSONNALISATION :**

### **Ajouter d'Autres Destinataires :**
Modifiez `scripts/gitlab-notifications.sh` :
```php
$recipients = [
    'im0668@gmail.com',     // Votre email principal
    'dev-team@company.com',  // Équipe de développement
    'qa-team@company.com',   // Équipe de test
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
- ✅ Vérifiez votre boîte de réception
- ✅ Vérifiez les spams
- ✅ Testez localement : `./scripts/gitlab-notifications.sh`

#### **2. Pipeline échoue :**
- ✅ Vérifiez les logs GitLab CI
- ✅ Testez les tests localement
- ✅ Vérifiez la configuration PHP

#### **3. Variables manquantes :**
- ✅ Vérifiez `.gitlab-ci.yml`
- ✅ Vérifiez `notifications-config.env`
- ✅ Redémarrez le pipeline

### **Logs et Debug :**
```bash
# Activer le debug SMTP
export SMTP_DEBUG=true

# Tester la configuration
./scripts/test-email-notifications.sh

# Vérifier les variables
echo $CI_PROJECT_NAME
echo $CI_COMMIT_REF_NAME
```

## 🎯 **PROCHAINES ÉTAPES :**

### **1. ✅ Notifications Email Intégrées (TERMINÉ)**
Vos notifications email sont maintenant 100% intégrées avec GitLab CI !

### **2. 🔧 Configuration GitLab (Optionnel)**
- **Variables d'environnement** dans GitLab
- **Secrets** pour les déploiements
- **Permissions** des pipelines

### **3. 📱 Ajout Slack/Teams (Optionnel)**
Si vous voulez des notifications multi-plateformes

### **4. 🚀 Déploiement Automatique**
- **Staging** : Déploiement automatique sur `develop-v3`
- **Production** : Déploiement manuel sur `main`

## 🎉 **FÉLICITATIONS !**

**Votre pipeline GitLab CI est maintenant entièrement automatisé avec des notifications email professionnelles !**

- ✅ **Notifications automatiques** pour chaque pipeline
- ✅ **Emails designés** avec toutes les informations
- ✅ **Intégration complète** avec GitLab CI
- ✅ **Configuration flexible** et personnalisable

**Chaque fois que vous poussez du code, vous recevrez automatiquement un email détaillé sur le statut de votre pipeline !** 🚀

---

**📧 Testez maintenant en poussant du code sur votre branche `develop-v3` !**
