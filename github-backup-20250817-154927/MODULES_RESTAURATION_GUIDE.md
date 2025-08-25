# Guide de Restauration des Modules DTN et RPM

## 🎯 Objectif

Restauration complète des modules DTN (Direction Technique Nationale) et RPM (Régulation & Préparation Matchs) dans l'application FIT.

## ✅ État de la Restauration

### Modules Restaurés

#### 🏆 Module DTN Manager

-   **Description** : Direction Technique Nationale - Gestion des Équipes Nationales
-   **Fonctionnalités** :
    -   Dashboard DTN
    -   Gestion des équipes nationales
    -   Sélections internationales
    -   Gestion des expatriés
    -   Interface médicale
    -   Planification technique
    -   Rapports DTN
    -   Paramètres DTN

#### ⚽ Module RPM

-   **Description** : Régulation & Préparation Matchs
-   **Fonctionnalités** :
    -   Dashboard RPM
    -   Calendrier d'entraînement
    -   Sessions d'entraînement
    -   Gestion des matchs
    -   Monitoring de charge
    -   Suivi de présence
    -   Rapports RPM
    -   Synchronisation
    -   Paramètres RPM

## 📁 Structure des Fichiers

### JavaScript/Vue.js

```
resources/js/modules/
├── index.js                    # Point d'entrée des modules
├── config.js                   # Configuration des modules
├── DTNManager/
│   ├── views/
│   │   └── DTNDashboard.vue    # Dashboard principal DTN
│   ├── components/             # Composants DTN
│   ├── router/
│   │   └── dtn.routes.js       # Routes Vue.js DTN
│   └── services/               # Services DTN
└── RPM/
    ├── views/
    │   └── RPMPortal.vue       # Dashboard principal RPM
    ├── components/             # Composants RPM
    ├── router/
    │   └── rpm.routes.js       # Routes Vue.js RPM
    └── services/               # Services RPM
```

### Laravel/PHP

```
app/Http/Controllers/
└── ModuleController.php        # Contrôleur pour les modules

resources/views/modules/
├── dtn/                        # Vues Blade DTN
│   ├── dashboard.blade.php
│   ├── teams.blade.php
│   ├── selections.blade.php
│   ├── expats.blade.php
│   ├── medical.blade.php
│   ├── planning.blade.php
│   ├── reports.blade.php
│   └── settings.blade.php
└── rpm/                        # Vues Blade RPM
    ├── dashboard.blade.php
    ├── calendar.blade.php
    ├── sessions.blade.php
    ├── matches.blade.php
    ├── load.blade.php
    ├── attendance.blade.php
    ├── reports.blade.php
    ├── sync.blade.php
    └── settings.blade.php
```

## 🔗 Routes Disponibles

### Routes DTN

-   `/dtn` - Dashboard DTN
-   `/dtn/teams` - Équipes nationales
-   `/dtn/selections` - Sélections internationales
-   `/dtn/expats` - Gestion des expatriés
-   `/dtn/medical` - Interface médicale
-   `/dtn/planning` - Planification technique
-   `/dtn/reports` - Rapports DTN
-   `/dtn/settings` - Paramètres DTN

### Routes RPM

-   `/rpm` - Dashboard RPM
-   `/rpm/calendar` - Calendrier d'entraînement
-   `/rpm/sessions` - Sessions d'entraînement
-   `/rpm/matches` - Gestion des matchs
-   `/rpm/load` - Monitoring de charge
-   `/rpm/attendance` - Suivi de présence
-   `/rpm/reports` - Rapports RPM
-   `/rpm/sync` - Synchronisation
-   `/rpm/settings` - Paramètres RPM

## 🔐 Permissions et Rôles

### Permissions DTN

-   `dtn_view` - Accès général au module DTN
-   `dtn_teams_view` - Voir les équipes
-   `dtn_teams_create` - Créer des équipes
-   `dtn_teams_edit` - Modifier les équipes
-   `dtn_selections_view` - Voir les sélections
-   `dtn_selections_create` - Créer des sélections
-   `dtn_selections_edit` - Modifier les sélections
-   `dtn_selections_manage` - Gérer les sélections
-   `dtn_expats_view` - Voir les expatriés
-   `dtn_medical_view` - Accès médical
-   `dtn_planning_view` - Voir la planification
-   `dtn_reports_view` - Voir les rapports
-   `dtn_settings` - Accès aux paramètres
-   `dtn_admin` - Administrateur DTN

### Permissions RPM

-   `rpm_view` - Accès général au module RPM
-   `rpm_calendar_view` - Voir le calendrier
-   `rpm_sessions_view` - Voir les sessions
-   `rpm_sessions_create` - Créer des sessions
-   `rpm_sessions_edit` - Modifier les sessions
-   `rpm_matches_view` - Voir les matchs
-   `rpm_matches_create` - Créer des matchs
-   `rpm_matches_edit` - Modifier les matchs
-   `rpm_load_view` - Voir la charge
-   `rpm_attendance_view` - Voir la présence
-   `rpm_reports_view` - Voir les rapports
-   `rpm_sync` - Synchronisation
-   `rpm_settings` - Accès aux paramètres
-   `rpm_admin` - Administrateur RPM

### Rôles avec Accès

-   **System Admin** : Accès complet à tous les modules
-   **DTN Manager** : Accès complet au module DTN
-   **RPM Manager** : Accès complet au module RPM

## 🚀 Utilisation

### 1. Accès aux Modules

1. Se connecter avec un compte ayant les permissions appropriées
2. Naviguer vers `/dtn` pour le module DTN
3. Naviguer vers `/rpm` pour le module RPM

### 2. Navigation

-   Utiliser le menu de navigation pour accéder aux différentes sections
-   Chaque module a son propre dashboard avec des métriques spécifiques
-   Les permissions déterminent quelles sections sont accessibles

### 3. Fonctionnalités

-   **DTN** : Gestion des équipes nationales, sélections, expatriés, planification
-   **RPM** : Gestion des entraînements, matchs, charge de travail, présence

## 🔧 Configuration

### Variables d'Environnement

```env
# URLs des APIs des modules
VITE_DTN_API_URL=/api/dtn
VITE_RPM_API_URL=/api/rpm
```

### Configuration des Modules

Les modules sont configurés dans `resources/js/modules/config.js` avec :

-   Permissions requises
-   Routes disponibles
-   Configuration des APIs
-   Paramètres d'interface

## 🧪 Tests

### Test de Fonctionnement

1. **Vérifier les routes** : Accéder aux URLs des modules
2. **Tester les permissions** : Se connecter avec différents rôles
3. **Vérifier les composants** : S'assurer que les modules Vue.js se chargent
4. **Tester les fonctionnalités** : Utiliser les différentes sections

### Commandes de Test

```bash
# Vérifier les routes
php artisan route:list | grep -E "(dtn|rpm)"

# Vider les caches
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# Tester les modules
curl http://localhost:8000/dtn
curl http://localhost:8000/rpm
```

## 🐛 Dépannage

### Problèmes Courants

#### 1. Modules ne se chargent pas

-   Vérifier que Alpine.js/Vue.js est chargé
-   Vérifier la console pour les erreurs JavaScript
-   Vider le cache du navigateur

#### 2. Erreurs de permissions

-   Vérifier le rôle de l'utilisateur
-   S'assurer que les permissions sont correctement définies
-   Vérifier la configuration dans le contrôleur

#### 3. Routes non trouvées

-   Vérifier que les routes sont bien enregistrées
-   Vider le cache des routes : `php artisan route:clear`
-   Vérifier le fichier `routes/web.php`

#### 4. Composants Vue.js non chargés

-   Vérifier que les fichiers Vue.js existent
-   Vérifier les imports dans `app.js`
-   Vérifier la configuration webpack/Vite

### Logs et Debug

-   Vérifier les logs Laravel : `storage/logs/laravel.log`
-   Vérifier la console du navigateur pour les erreurs JavaScript
-   Utiliser les outils de développement pour inspecter les requêtes

## 📝 Maintenance

### Mises à Jour

1. Sauvegarder les modifications personnalisées
2. Mettre à jour les modules
3. Vérifier la compatibilité
4. Tester les fonctionnalités

### Sauvegarde

-   Sauvegarder les fichiers de configuration
-   Sauvegarder les données personnalisées
-   Documenter les modifications

## 🎉 Résultat

Les modules DTN et RPM sont maintenant complètement restaurés et fonctionnels :

✅ **9 routes DTN** configurées et opérationnelles  
✅ **10 routes RPM** configurées et opérationnelles  
✅ **ModuleController** créé avec toutes les méthodes  
✅ **Vues Blade** créées et fonctionnelles  
✅ **Permissions** définies et configurées  
✅ **Composants Vue.js** prêts à être utilisés  
✅ **7 system admins** peuvent accéder aux modules

Les modules sont prêts à être utilisés par les utilisateurs autorisés !
