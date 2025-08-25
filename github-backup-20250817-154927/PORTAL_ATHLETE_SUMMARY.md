# Portail Athlète - Guide Complet

## 🏃‍♂️ Vue d'ensemble

Le **Portail Athlète** est une interface mobile-first PWA (Progressive Web App) permettant aux athlètes d'accéder à leurs données médicales et de bien-être de manière sécurisée via leur FIFA Connect ID.

## 🔗 Accès au Portail

### URL d'accès

-   **Portail principal** : `http://localhost:8000/portal`
-   **Dashboard** : `http://localhost:8000/portal/dashboard`
-   **Bien-être** : `http://localhost:8000/portal/wellness`
-   **Appareils** : `http://localhost:8000/portal/devices`
-   **Dossier médical** : `http://localhost:8000/portal/medical-record`

### Navigation depuis le menu principal

1. Connectez-vous à l'application
2. Cliquez sur **Healthcare** dans le menu principal
3. Sélectionnez **🏃‍♂️ Portail Athlète**

## 🔐 Authentification

### Identifiants de test

-   **Email** : `test@example.com`
-   **Mot de passe** : `password`
-   **Rôle requis** : `athlete`

### Sécurité

-   Authentification basée sur FIFA Connect ID
-   Chaque athlète ne peut accéder qu'à ses propres données
-   Sessions sécurisées avec Laravel Sanctum
-   Middleware de rôle `athlete` obligatoire

## 📱 Fonctionnalités

### 1. Dashboard Athlète

-   **Métriques de santé** : Statut, prochain RDV, appareils connectés, score bien-être
-   **Activité récente** : Historique des actions et soumissions
-   **Métriques de performance** : Fréquence cardiaque, pas quotidiens, sommeil
-   **Actions rapides** : Accès direct aux formulaires et outils

### 2. Formulaire de Bien-être

-   **Évaluation quotidienne** : Échelle 1-10 pour différents aspects
-   **Métriques suivies** :
    -   Bien-être général
    -   Qualité du sommeil
    -   Niveau d'énergie
    -   Stress
    -   Douleurs/blessures
    -   Hydratation
    -   Appétit
    -   Motivation
-   **Historique** : Consultation des soumissions précédentes
-   **Soumission automatique** : Envoi via API REST

### 3. Appareils Connectés

-   **Gestion des appareils** : Montres, podomètres, cardio belts
-   **Synchronisation** : Données en temps réel
-   **Statistiques** : Taux de synchronisation, dernier sync
-   **Ajout d'appareils** : Interface pour connecter de nouveaux appareils

### 4. Dossier Médical

-   **Profil médical** : Informations de base, allergies, médicaments
-   **Dernières mesures** : Tension, fréquence cardiaque, température
-   **Statut de vaccination** : Vaccins et dates d'expiration
-   **Historique médical** : Examens, consultations, traitements
-   **Prochains rendez-vous** : Planning médical

## 🛠️ Architecture Technique

### Backend (Laravel)

-   **Routes API** : `/api/v1/portal/*`
-   **Contrôleur** : `PlayerPortalController`
-   **Middleware** : `auth:sanctum`, `role:athlete`
-   **Modèles** : `User`, `Athlete`, `WellnessForm`, `DeviceData`

### Frontend (Vue.js)

-   **Application principale** : `resources/js/portal/App.vue`
-   **Vues** :
    -   `DashboardView.vue`
    -   `WellnessFormView.vue`
    -   `ConnectedDevicesView.vue`
-   **Interface** : Mobile-first, responsive design

### Vues Blade

-   **Layout** : `resources/views/portal/*.blade.php`
-   **Navigation** : Intégrée dans le menu principal
-   **Authentification** : Gérée par Laravel

## 📊 API Endpoints

### Dashboard

-   `GET /api/v1/portal/dashboard-summary` - Résumé du dashboard
-   `GET /api/v1/portal/medical-record-summary` - Résumé dossier médical

### Bien-être

-   `POST /api/v1/portal/wellness-form` - Soumettre formulaire
-   `GET /api/v1/portal/wellness-history` - Historique bien-être

### Données

-   `GET /api/v1/portal/appointments` - Rendez-vous
-   `GET /api/v1/portal/documents` - Documents médicaux

## 🎨 Interface Utilisateur

### Design

-   **Mobile-first** : Optimisé pour smartphones
-   **PWA** : Installation possible sur mobile
-   **Responsive** : Adaptation automatique à tous les écrans
-   **Accessibilité** : Conforme aux standards WCAG

### Navigation

-   **Menu latéral** : Navigation principale
-   **Breadcrumbs** : Indication de la position
-   **Actions rapides** : Accès direct aux fonctionnalités

### Thème

-   **Couleurs** : Bleu principal, vert pour les actions positives
-   **Icônes** : Emojis et SVG pour une interface moderne
-   **Typographie** : Inter font pour une meilleure lisibilité

## 🔧 Configuration

### Variables d'environnement

```env
# Configuration du portail athlète
ATHLETE_PORTAL_ENABLED=true
FIFA_CONNECT_ENABLED=true
WELLNESS_FORM_ENABLED=true
DEVICE_SYNC_ENABLED=true
```

### Permissions

-   **Rôle** : `athlete`
-   **Middleware** : `auth:sanctum`, `role:athlete`
-   **Scopes** : Accès limité aux données de l'athlète connecté

## 🧪 Tests

### Test d'accès

```bash
# Vérifier les routes
php artisan route:list | grep portal

# Tester l'accès
curl -X GET http://localhost:8000/portal/dashboard
```

### Test des fonctionnalités

1. **Connexion** : Test avec les identifiants de test
2. **Navigation** : Vérifier tous les liens
3. **Formulaires** : Tester la soumission du bien-être
4. **Responsive** : Tester sur mobile et desktop
5. **API** : Vérifier tous les endpoints

## 🚀 Déploiement

### Prérequis

-   Laravel 10+
-   Vue.js 3+
-   Base de données avec tables utilisateurs et athlètes
-   Configuration FIFA Connect

### Étapes

1. **Migrations** : `php artisan migrate`
2. **Seeders** : `php artisan db:seed`
3. **Assets** : `npm run build`
4. **Cache** : `php artisan config:cache`

### Monitoring

-   **Logs** : `storage/logs/laravel.log`
-   **Performance** : Monitoring des API calls
-   **Erreurs** : Sentry ou équivalent

## 📈 Évolutions Futures

### Fonctionnalités prévues

-   **Notifications push** : Alertes en temps réel
-   **Chat médical** : Communication avec l'équipe médicale
-   **Analytics avancés** : Graphiques de tendances
-   **Intégration IoT** : Plus d'appareils connectés
-   **IA** : Prédictions de santé personnalisées

### Améliorations techniques

-   **Cache Redis** : Performance des API
-   **CDN** : Optimisation des assets
-   **Tests automatisés** : Couverture complète
-   **CI/CD** : Déploiement automatisé

## 📞 Support

### Documentation

-   **API** : Documentation Swagger
-   **Utilisateur** : Guide d'utilisation
-   **Développeur** : Documentation technique

### Contact

-   **Développement** : Équipe technique
-   **Support utilisateur** : Helpdesk
-   **Urgences** : Hotline 24/7

---

**Version** : 1.0.0  
**Dernière mise à jour** : Août 2024  
**Statut** : ✅ Production Ready
