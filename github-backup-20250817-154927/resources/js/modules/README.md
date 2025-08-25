# Modules DTN et RPM pour FIT

Ce dossier contient les modules **DTN Manager** et **RPM (Régulation & Préparation Matchs)** intégrés dans la plateforme FIT (Football Intelligence & Tracking).

## 📁 Structure des Modules

```
resources/js/modules/
├── DTNManager/                    # Module Direction Technique Nationale
│   ├── components/               # Composants Vue.js
│   │   ├── NationalTeams.vue
│   │   ├── InternationalSelections.vue
│   │   ├── ExpatsClubSync.vue
│   │   ├── MedicalClubInterface.vue
│   │   └── TechnicalPlanning.vue
│   ├── services/                 # Services API
│   │   ├── FifaConnectService.js
│   │   ├── ClubBridgeAPI.js
│   │   └── FhirService.js
│   ├── views/                    # Vues principales
│   │   └── DTNDashboard.vue
│   ├── router/                   # Routes du module
│   │   └── dtn.routes.js
│   └── __tests__/               # Tests unitaires
│
├── RPM/                          # Module Régulation & Préparation Matchs
│   ├── components/               # Composants Vue.js
│   │   ├── TrainingCalendar.vue
│   │   ├── SessionEditor.vue
│   │   ├── MatchPreparation.vue
│   │   ├── PlayerLoadMonitor.vue
│   │   └── AttendanceTracker.vue
│   ├── services/                 # Services API
│   │   └── RpmSyncService.js
│   ├── views/                    # Vues principales
│   │   └── RPMPortal.vue
│   ├── router/                   # Routes du module
│   │   └── rpm.routes.js
│   └── __tests__/               # Tests unitaires
│
├── index.js                      # Point d'entrée des modules
├── __tests__/                    # Tests d'intégration
│   └── modules.test.js
└── README.md                     # Ce fichier
```

## 🏆 Module DTN Manager

### Objectif

Gérer les équipes nationales, les sélections officielles (CAF/FIFA), les convocations, la relation avec les clubs étrangers, et la coordination des données techniques et médicales.

### Fonctionnalités Principales

#### 📊 Équipes Nationales

-   Gestion des équipes (U15 à A, hommes, femmes, futsal)
-   Suivi des effectifs et des statuts
-   Planification des compétitions
-   Export vers FIFA Connect

#### 🌍 Sélections Internationales

-   Création et gestion des convocations
-   Intégration avec l'API FIFA
-   Notification automatique des clubs
-   Suivi des disponibilités

#### 🏥 Interface Médicale Clubs

-   Communication sécurisée avec les clubs étrangers
-   Échange de données médicales via HL7 FHIR
-   Suivi des joueurs expatriés
-   Alertes médicales

#### 🔗 Intégrations

-   **FIFA Connect API** : Convocations et données officielles
-   **ClubBridge API** : Communication avec les clubs
-   **HL7 FHIR** : Interopérabilité médicale
-   **OAuth2** : Authentification sécurisée

### Routes DTN

-   `/dtn/dashboard` - Dashboard principal
-   `/dtn/teams` - Gestion des équipes nationales
-   `/dtn/selections` - Sélections internationales
-   `/dtn/expats` - Joueurs expatriés
-   `/dtn/medical` - Interface médicale
-   `/dtn/planning` - Planification technique

## ⚽ Module RPM

### Objectif

Aider le staff technique à planifier les entraînements, gérer la charge, documenter les matchs amicaux, et synchroniser les données avec le module de performance.

### Fonctionnalités Principales

#### 📅 Calendrier d'Entraînement

-   Planification des sessions
-   Gestion des objectifs et durées
-   Suivi des présences
-   Intégration calendrier

#### 💪 Gestion de la Charge

-   Suivi RPE (Rate of Perceived Exertion)
-   Monitoring de la charge de travail
-   Détection des joueurs à risque
-   Calcul ACWR (Acute:Chronic Workload Ratio)

#### 🏆 Préparation Matchs

-   Planification des matchs amicaux
-   Objectifs tactiques
-   Formations et stratégies
-   Export vers module Performance

#### 📊 Synchronisation

-   Export automatique vers Performance
-   Données validées et tracées
-   Historique des sessions
-   Rapports détaillés

### Routes RPM

-   `/rpm/dashboard` - Dashboard principal
-   `/rpm/calendar` - Calendrier d'entraînement
-   `/rpm/sessions` - Gestion des sessions
-   `/rpm/matches` - Préparation des matchs
-   `/rpm/load` - Monitoring charge joueurs
-   `/rpm/attendance` - Suivi des présences

## 🔧 Installation et Configuration

### 1. Intégration dans l'Application

```javascript
// Dans resources/js/app.js
import { initializeModules } from "./modules/index";

// Initialiser les modules
await initializeModules(router, userPermissions);
```

### 2. Configuration des Variables d'Environnement

```env
# FIFA Connect
VUE_APP_FIFA_API_URL=https://api.fifa.com/v1
VUE_APP_FIFA_API_KEY=your_fifa_api_key
VUE_APP_FIFA_CLIENT_ID=your_client_id
VUE_APP_FIFA_CLIENT_SECRET=your_client_secret

# ClubBridge API
VUE_APP_CLUB_BRIDGE_URL=/api/club
VUE_APP_CLUB_BRIDGE_KEY=your_club_bridge_key

# FHIR
VUE_APP_FHIR_URL=/api/fhir
VUE_APP_FHIR_CLIENT_ID=your_fhir_client_id
VUE_APP_FHIR_CLIENT_SECRET=your_fhir_client_secret

# RPM
VUE_APP_RPM_API_URL=/api/rpm
VUE_APP_RPM_CLIENT_ID=your_rpm_client_id
VUE_APP_RPM_CLIENT_SECRET=your_rpm_client_secret

# Performance Module
VUE_APP_PERFORMANCE_API_URL=/api/performance
```

### 3. Permissions Utilisateur

#### Permissions DTN

-   `dtn_view` - Accès au module DTN
-   `dtn_teams_view` - Voir les équipes nationales
-   `dtn_teams_create` - Créer des équipes
-   `dtn_teams_edit` - Modifier les équipes
-   `dtn_selections_view` - Voir les sélections
-   `dtn_selections_create` - Créer des sélections
-   `dtn_selections_edit` - Modifier les sélections
-   `dtn_selections_manage` - Gérer les sélections
-   `dtn_expats_view` - Voir les joueurs expatriés
-   `dtn_medical_view` - Accès aux données médicales
-   `dtn_planning_view` - Planification technique
-   `dtn_reports_view` - Voir les rapports
-   `dtn_settings` - Paramètres DTN
-   `dtn_admin` - Administrateur DTN

#### Permissions RPM

-   `rpm_view` - Accès au module RPM
-   `rpm_calendar_view` - Voir le calendrier
-   `rpm_sessions_view` - Voir les sessions
-   `rpm_sessions_create` - Créer des sessions
-   `rpm_sessions_edit` - Modifier les sessions
-   `rpm_matches_view` - Voir les matchs
-   `rpm_matches_create` - Créer des matchs
-   `rpm_matches_edit` - Modifier les matchs
-   `rpm_load_view` - Voir la charge des joueurs
-   `rpm_attendance_view` - Voir les présences
-   `rpm_reports_view` - Voir les rapports
-   `rpm_sync` - Synchronisation Performance
-   `rpm_settings` - Paramètres RPM
-   `rpm_admin` - Administrateur RPM

## 🧪 Tests

### Exécuter les Tests

```bash
# Tests unitaires
npm run test:unit

# Tests d'intégration
npm run test:integration

# Tests spécifiques aux modules
npm run test:modules
```

### Structure des Tests

```javascript
// Exemple de test pour DTN
describe("Module DTN Manager", () => {
    it("devrait se monter correctement", () => {
        const wrapper = mount(DTNDashboard);
        expect(wrapper.exists()).toBe(true);
    });
});

// Exemple de test pour RPM
describe("Module RPM", () => {
    it("devrait afficher les statistiques", () => {
        const wrapper = mount(RPMPortal);
        expect(wrapper.vm.stats.weeklySessions).toBe(12);
    });
});
```

## 🔒 Sécurité

### Authentification

-   OAuth2 pour tous les accès sensibles
-   JWT pour les sessions utilisateur
-   Gestion des permissions granulaires

### Protection des Données

-   Chiffrement des données médicales
-   Respect du RGPD
-   Logs d'audit complets
-   Contrôle d'accès par rôle

### Standards FIFA

-   Conformité aux standards FIFA Connect
-   Validation des données selon les règles FIFA
-   Traçabilité des convocations
-   Export sécurisé vers FIFA

## 📈 Performance

### Optimisations

-   Lazy loading des composants
-   Mise en cache des données
-   Pagination des listes
-   Compression des réponses API

### Monitoring

-   Métriques de performance
-   Logs d'erreurs
-   Alertes automatiques
-   Tableaux de bord de santé

## 🔄 Maintenance

### Mises à Jour

-   Compatibilité avec les nouvelles versions FIFA
-   Mise à jour des standards FHIR
-   Amélioration des performances
-   Correction des bugs

### Support

-   Documentation technique
-   Guide utilisateur
-   Formation des équipes
-   Support technique

## 📞 Support

Pour toute question ou problème :

1. **Documentation technique** : Consultez les commentaires dans le code
2. **Tests** : Exécutez les tests pour valider le fonctionnement
3. **Logs** : Vérifiez les logs d'application
4. **Support** : Contactez l'équipe technique

---

**Version** : 1.0.0  
**Dernière mise à jour** : 2024  
**Compatibilité** : Vue.js 3, Laravel 12, PHP 8.4
