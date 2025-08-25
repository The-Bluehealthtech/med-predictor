# 🎯 Portail Joueur FIFA Connect - Documentation

## 📋 Vue d'ensemble

Le **Portail Joueur FIFA Connect** est une application web dynamique qui affiche les données des joueurs en temps réel, connectée à une base de données via une API REST. Contrairement à la version statique (`/portail-patient`), cette version récupère toutes les données depuis la base de données.

## 🌐 URLs disponibles

### **Portail Patient (Statique)**

-   **URL :** `http://localhost:8001/portail-patient`
-   **Type :** Page HTML statique avec données codées en dur
-   **Utilisation :** Version de référence, démonstration

### **Portail Joueur (Dynamique)**

-   **URL :** `http://localhost:8001/portail-joueur`
-   **Type :** Application Vue.js dynamique connectée à l'API
-   **Utilisation :** Version de production avec données réelles

## 🗄️ Structure de la base de données

### Table `joueurs`

```sql
CREATE TABLE joueurs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    fifa_id VARCHAR UNIQUE NOT NULL,
    nom VARCHAR NOT NULL,
    prenom VARCHAR NOT NULL,
    date_naissance DATE NOT NULL,
    nationalite VARCHAR NOT NULL,
    poste VARCHAR NOT NULL,
    taille_cm INTEGER NOT NULL,
    poids_kg INTEGER NOT NULL,
    club VARCHAR NOT NULL,
    club_logo VARCHAR NULLABLE,
    pays_drapeau VARCHAR NULLABLE,
    photo_url VARCHAR NULLABLE,

    -- Statistiques sportives
    buts INTEGER DEFAULT 0,
    passes_decisives INTEGER DEFAULT 0,
    matchs INTEGER DEFAULT 0,
    minutes_jouees INTEGER DEFAULT 0,
    note_moyenne DECIMAL(3,1) DEFAULT 0.0,

    -- Données FIFA
    fifa_ovr INTEGER DEFAULT 0,
    fifa_pot INTEGER DEFAULT 0,
    score_fit INTEGER DEFAULT 0,
    risque_blessure INTEGER DEFAULT 0,
    valeur_marchande DECIMAL(10,2) DEFAULT 0.00,

    -- Données médicales (JSON)
    historique_blessures JSON NULLABLE,
    donnees_sante JSON NULLABLE,
    statistiques_physiques JSON NULLABLE,
    statistiques_techniques JSON NULLABLE,
    statistiques_offensives JSON NULLABLE,

    -- Données des appareils connectés (JSON)
    donnees_devices JSON NULLABLE,
    donnees_dopage JSON NULLABLE,
    donnees_sdoh JSON NULLABLE,

    -- Notifications et alertes (JSON)
    notifications JSON NULLABLE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔌 API REST

### Endpoints disponibles

#### **1. Liste des joueurs**

```http
GET /api/joueurs
```

**Réponse :**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "fifa_id": "FIFA2025001",
            "nom": "Ben Salah",
            "prenom": "Youssef",
            "poste": "Milieu",
            "club": "ES Tunis",
            "fifa_ovr": 82,
            "score_fit": 87
        }
    ],
    "message": "Liste des joueurs récupérée avec succès"
}
```

#### **2. Détails d'un joueur**

```http
GET /api/joueurs/{id}
GET /api/joueurs/fifa/{fifaId}
```

**Réponse :**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "fifa_id": "FIFA2025001",
        "nom": "Ben Salah",
        "prenom": "Youssef",
        "date_naissance": "1995-04-12T00:00:00.000000Z",
        "nationalite": "Tunisie",
        "poste": "Milieu",
        "taille_cm": 178,
        "poids_kg": 75,
        "club": "ES Tunis",
        "fifa_ovr": 82,
        "fifa_pot": 85,
        "score_fit": 87,
        "risque_blessure": 12,
        "valeur_marchande": "15000000.00"
    }
}
```

#### **3. Statistiques d'un joueur**

```http
GET /api/joueurs/{id}/stats
```

**Réponse :**

```json
{
    "success": true,
    "data": {
        "sportives": {
            "buts": 8,
            "passes_decisives": 12,
            "matchs": 30,
            "minutes_jouees": 2700,
            "note_moyenne": 7.8,
            "buts_par_match": 0.27,
            "passes_par_match": 0.4
        },
        "fifa": {
            "ovr": 82,
            "pot": 85,
            "score_fit": 87,
            "risque_blessure": 12,
            "valeur_marchande": "15000000.00"
        }
    }
}
```

#### **4. Données de santé**

```http
GET /api/joueurs/{id}/health
```

**Réponse :**

```json
{
    "success": true,
    "data": {
        "donnees_sante": {...},
        "historique_blessures": [...],
        "donnees_devices": {...},
        "donnees_dopage": {...},
        "donnees_sdoh": [...]
    }
}
```

#### **5. Notifications**

```http
GET /api/joueurs/{id}/notifications
```

**Réponse :**

```json
{
    "success": true,
    "data": {
        "nationalTeam": [...],
        "trainingSessions": [...],
        "matches": [...],
        "medicalAppointments": [...],
        "socialAlerts": [...]
    }
}
```

## 🎨 Interface utilisateur

### **Fonctionnalités principales**

1. **Sélecteur de joueur** : Menu déroulant pour choisir un joueur
2. **Hero Zone** : Affichage des informations principales du joueur
3. **Navigation par onglets** : 6 onglets pour organiser les données
4. **Données dynamiques** : Toutes les informations sont récupérées via l'API
5. **Graphiques interactifs** : Chart.js pour visualiser les performances

### **Onglets disponibles**

-   **Performance** : Statistiques sportives et graphiques
-   **Notifications** : Alertes et messages avec filtres
-   **Santé & Bien-être** : Scores FIT et indicateurs de santé
-   **Médical** : Dossiers médicaux et évaluations PCMA
-   **Devices** : Appareils connectés et données IoT
-   **Dopage** : Contrôles antidopage et déclarations ATU

## 🚀 Utilisation

### **1. Accéder au portail**

```
http://localhost:8001/portail-joueur
```

### **2. Sélectionner un joueur**

-   Utiliser le menu déroulant en haut de la page
-   Choisir parmi les joueurs disponibles dans la base de données

### **3. Naviguer entre les onglets**

-   Cliquer sur les onglets pour afficher différentes catégories de données
-   Chaque onglet charge ses données spécifiques depuis l'API

### **4. Visualiser les données**

-   Toutes les informations sont affichées en temps réel
-   Les graphiques se mettent à jour automatiquement
-   Les données sont formatées pour une lecture optimale

## 🔧 Configuration et maintenance

### **Ajouter un nouveau joueur**

1. **Insérer dans la base de données :**

```sql
INSERT INTO joueurs (fifa_id, nom, prenom, date_naissance, nationalite, poste, taille_cm, poids_kg, club, fifa_ovr, score_fit)
VALUES ('FIFA2025006', 'Nouveau', 'Joueur', '2000-01-01', 'France', 'Attaquant', 180, 75, 'Club FC', 80, 85);
```

2. **Le joueur apparaîtra automatiquement** dans le sélecteur du portail

### **Modifier les données d'un joueur**

1. **Mettre à jour la base de données :**

```sql
UPDATE joueurs SET fifa_ovr = 85, score_fit = 90 WHERE fifa_id = 'FIFA2025001';
```

2. **Les changements seront visibles** immédiatement dans le portail

### **Ajouter de nouveaux champs**

1. **Modifier la migration** si nécessaire
2. **Mettre à jour le modèle** `Joueur`
3. **Adapter l'API** pour inclure les nouveaux champs
4. **Modifier l'interface** pour afficher les nouvelles données

## 📊 Données d'exemple

### **Joueurs disponibles par défaut**

1. **Youssef Ben Salah** (FIFA2025001)

    - Club : ES Tunis
    - Poste : Milieu
    - FIFA OVR : 82
    - Score FIT : 87

2. **Moussa Diallo** (FIFA2025002)
    - Club : Al Ahly
    - Poste : Attaquant
    - FIFA OVR : 85
    - Score FIT : 89

## 🔍 Débogage

### **Vérifier l'API**

```bash
# Tester la liste des joueurs
curl http://localhost:8001/api/joueurs

# Tester un joueur spécifique
curl http://localhost:8001/api/joueurs/fifa/FIFA2025001
```

### **Vérifier la base de données**

```bash
# Accéder à la base SQLite
sqlite3 database/database.sqlite

# Lister les joueurs
SELECT fifa_id, nom, prenom, club FROM joueurs;
```

### **Logs Laravel**

```bash
# Voir les logs d'erreur
tail -f storage/logs/laravel.log
```

## 🎯 Avantages du système dynamique

1. **Données en temps réel** : Mise à jour automatique des informations
2. **Scalabilité** : Facile d'ajouter de nouveaux joueurs et champs
3. **Maintenance** : Centralisation des données dans la base
4. **API REST** : Interface standard pour l'intégration
5. **Flexibilité** : Adaptation facile aux besoins changeants
6. **Performance** : Chargement à la demande des données

## 🔮 Évolutions futures

-   **Authentification** : Système de connexion pour les joueurs
-   **Notifications push** : Alertes en temps réel
-   **Export de données** : Génération de rapports PDF/Excel
-   **Intégration FIFA** : Synchronisation avec l'API officielle FIFA
-   **Mobile** : Application mobile native
-   **Analytics** : Tableaux de bord avancés pour les entraîneurs

---

**📝 Note :** Ce portail remplace progressivement la version statique et constitue la base pour un système de gestion complet des joueurs FIFA.










