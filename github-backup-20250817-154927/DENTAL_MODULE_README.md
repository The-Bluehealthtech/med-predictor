# 🦷 Module Diagramme Dentaire Interactif

## Vue d'ensemble

Ce module fournit un **diagramme dentaire interactif complet** pour la gestion des dossiers dentaires dans l'application Med Predictor. Il permet aux professionnels de santé de documenter l'état de chaque dent selon la notation FDI (Fédération Dentaire Internationale).

## 🚀 Fonctionnalités

### ✅ Fonctionnalités Implémentées

-   **Diagramme SVG Interactif** : Affichage des 32 dents avec notation FDI
-   **Coloration Dynamique** : Chaque dent change de couleur selon son état
-   **Panneau d'Annotation** : Interface pour modifier l'état et ajouter des notes
-   **Sauvegarde Automatique** : Persistance des données en temps réel
-   **Statistiques en Temps Réel** : Compteurs par type d'état dentaire
-   **API REST Complète** : Backend Laravel avec validation
-   **Interface Responsive** : Compatible mobile et desktop
-   **Gestion des Patients** : Sélection et historique des patients

### 🎨 États Dentaires Supportés

| État              | Couleur  | Description                 |
| ----------------- | -------- | --------------------------- |
| **Sain**          | 🟢 Vert  | Dent en bon état            |
| **Carie**         | 🔴 Rouge | Carie détectée              |
| **Couronne**      | 🟡 Jaune | Dent couronnée              |
| **Extrait**       | ⚫ Gris  | Dent extraite               |
| **En Traitement** | 🔵 Bleu  | Dent en cours de traitement |

## 📁 Structure du Projet

```
med-predictor/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/DentalRecordController.php    # API REST
│   │   └── DentalChartController.php         # Contrôleur Vue
│   └── Models/
│       ├── DentalRecord.php                  # Modèle principal
│       └── Patient.php                       # Modèle Patient
├── database/
│   ├── migrations/
│   │   ├── create_dental_records_table.php   # Migration données
│   │   └── create_patients_table.php         # Migration patients
│   └── seeders/
│       └── PatientSeeder.php                 # Données de test
├── public/js/
│   └── dental-chart.js                       # Composant Vue.js
├── resources/views/
│   └── health-records/
│       └── dental-chart.blade.php            # Vue principale
└── routes/
    ├── api.php                               # Routes API
    └── web.php                               # Routes Web
```

## 🛠️ Installation et Configuration

### 1. Migrations

```bash
# Exécuter les migrations
php artisan migrate --force

# Créer les données de test
php artisan db:seed --class=PatientSeeder --force
```

### 2. Vérification des Routes

```bash
# Lister les routes disponibles
php artisan route:list | grep dental
```

### 3. Accès au Module

-   **URL principale** : `http://localhost:8001/dental-chart`
-   **API Base URL** : `http://localhost:8001/api/dental-records`

## 🔧 Utilisation

### Interface Utilisateur

1. **Sélection du Patient** : Choisir un patient dans la liste déroulante
2. **Diagramme Interactif** : Cliquer sur une dent pour ouvrir le panneau d'annotation
3. **Modification d'État** : Sélectionner le nouvel état et ajouter des notes
4. **Sauvegarde** : Les données sont sauvegardées automatiquement

### API Endpoints

#### GET `/api/dental-records`

Récupérer les enregistrements dentaires d'un patient

```bash
curl -X GET "http://localhost:8001/api/dental-records?patient_id=1"
```

#### POST `/api/dental-records`

Créer un nouvel enregistrement dentaire

```bash
curl -X POST "http://localhost:8001/api/dental-records" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "dental_data": {"11": {"status": "cavity", "notes": "Carie détectée"}},
    "notes": "Examen complet"
  }'
```

#### PATCH `/api/dental-records/{id}/tooth`

Mettre à jour l'état d'une dent spécifique

```bash
curl -X PATCH "http://localhost:8001/api/dental-records/1/tooth" \
  -H "Content-Type: application/json" \
  -d '{
    "tooth_number": "11",
    "status": "crown",
    "notes": "Couronne posée"
  }'
```

## 🎯 Composants Techniques

### Frontend (Vue.js 3)

```javascript
// Composant principal
const DentalChart = {
    name: "DentalChart",
    props: ["patientId", "recordId", "apiBaseUrl", "csrfToken"],
    setup(props) {
        // État réactif
        const dentalData = reactive({});
        const selectedTooth = ref(null);
        const showAnnotationPanel = ref(false);

        // Méthodes
        const handleToothClick = (toothNumber) => {
            openAnnotationPanel(toothNumber);
        };

        const saveAnnotation = async () => {
            await updateToothStatus(
                selectedTooth.value,
                annotationData.status,
                annotationData.notes
            );
        };

        return { dentalData, selectedTooth, handleToothClick, saveAnnotation };
    },
};
```

### Backend (Laravel)

```php
// Modèle DentalRecord
class DentalRecord extends Model
{
    protected $fillable = [
        'patient_id', 'user_id', 'dental_data',
        'notes', 'status', 'examined_at'
    ];

    protected $casts = [
        'dental_data' => 'array',
        'examined_at' => 'datetime',
    ];

    public function updateToothStatus(string $toothNumber, string $status, string $notes = ''): void
    {
        $dentalData = $this->dental_data ?? [];
        $dentalData[$toothNumber] = [
            'status' => $status,
            'notes' => $notes,
            'color' => $this->getStatusColor($status),
            'last_updated' => now()->toISOString(),
        ];

        $this->update(['dental_data' => $dentalData]);
    }
}
```

## 📊 Structure des Données

### Format JSON des Données Dentaires

```json
{
    "11": {
        "status": "healthy",
        "notes": "",
        "color": "#4ade80",
        "last_updated": "2025-08-06T10:30:00.000Z"
    },
    "12": {
        "status": "cavity",
        "notes": "Carie détectée sur la face mésiale",
        "color": "#ef4444",
        "last_updated": "2025-08-06T10:35:00.000Z"
    }
}
```

### Notation FDI

Le diagramme utilise la notation FDI (Fédération Dentaire Internationale) :

-   **Quadrant 1** (haut droite) : Dents 11-18
-   **Quadrant 2** (haut gauche) : Dents 21-28
-   **Quadrant 3** (bas gauche) : Dents 31-38
-   **Quadrant 4** (bas droite) : Dents 41-48

## 🎨 Personnalisation

### Couleurs des États

```javascript
const statusColors = {
    healthy: "#4ade80", // Vert
    cavity: "#ef4444", // Rouge
    crown: "#fbbf24", // Jaune
    extracted: "#6b7280", // Gris
    treatment: "#3b82f6", // Bleu
};
```

### Styles CSS

```css
.tooth {
    cursor: pointer;
    transition: all 0.2s ease;
}

.tooth:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

.tooth.selected {
    stroke: #3b82f6;
    stroke-width: 3;
}
```

## 🔒 Sécurité

-   **CSRF Protection** : Tous les appels API incluent le token CSRF
-   **Validation** : Validation Laravel côté serveur
-   **Authentification** : Intégration avec le système d'auth existant
-   **Sanitisation** : Nettoyage des données d'entrée

## 🚀 Évolutions Futures

### Fonctionnalités Prévues

-   [ ] **Diagramme 3D** : Visualisation en trois dimensions
-   [ ] **Historique des Modifications** : Traçabilité des changements
-   [ ] **Export PDF** : Génération de rapports dentaires
-   [ ] **Intégration IA** : Analyse automatique des images
-   [ ] **Synchronisation** : Sync avec d'autres systèmes
-   [ ] **Notifications** : Alertes pour les traitements urgents

### Améliorations Techniques

-   [ ] **Performance** : Optimisation des requêtes
-   [ ] **Cache** : Mise en cache des données fréquentes
-   [ ] **Tests** : Couverture de tests complète
-   [ ] **Documentation API** : Documentation Swagger/OpenAPI

## 🐛 Dépannage

### Problèmes Courants

1. **Erreur 419 (CSRF)** : Vérifier que le token CSRF est inclus
2. **Données non sauvegardées** : Vérifier les logs Laravel
3. **Vue.js non chargé** : Vérifier les CDN dans le template

### Logs de Debug

```bash
# Vérifier les logs Laravel
tail -f storage/logs/laravel.log

# Vérifier les erreurs JavaScript
# Ouvrir la console du navigateur (F12)
```

## 📞 Support

Pour toute question ou problème :

1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Tester l'API** : Utiliser Postman ou curl
3. **Console navigateur** : Vérifier les erreurs JavaScript
4. **Documentation Laravel** : https://laravel.com/docs

---

**Module créé avec ❤️ pour Med Predictor**
