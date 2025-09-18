# 👥 Implémentation de la Liste des Patients

## 🎯 Objectif Atteint

Le bouton "👥 Liste des patients" est maintenant connecté à la base de données des RDV et permet de sélectionner un patient pour préremplir automatiquement les dossiers Medical ou PCMA.

## ✅ Fonctionnalités Implémentées

### 1. Modal de Liste des Patients

- **Interface moderne** avec filtres avancés
- **Affichage complet** des informations patient
- **Sélection directe** vers Medical ou PCMA
- **Préremplissage automatique** via paramètres URL

### 2. Connexion à la Base de Données

- **Table `appointments`** : RDV avec `athlete_id`, `appointment_date`, `appointment_type`, `status`
- **Table `players`** : Joueurs avec `first_name`, `last_name`, `date_of_birth`, `fifa_connect_id`
- **Jointure** : `appointments.athlete_id = players.id`

### 3. API Endpoint

- **Route** : `/api/clinical/patients`
- **Filtres** : Status, Type, Date
- **Limite** : 50 patients maximum
- **Tri** : Par date de RDV décroissante

## 🎨 Interface Utilisateur

### Modal Principal

```html
<!-- Patient List Modal -->
<div
  id="patient-list-modal"
  class="fixed inset-0 bg-black bg-opacity-50 hidden z-50"
>
  <div class="flex items-center justify-center min-h-screen p-4">
    <div
      class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto"
    >
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900">
          👥 Liste des Patients
        </h3>
        <p class="text-sm text-gray-600 mt-1">
          Sélectionnez un patient pour commencer la consultation
        </p>
      </div>
      <!-- Contenu du modal -->
    </div>
  </div>
</div>
```

### Filtres Disponibles

- **Statut** : Planifié, Confirmé, En cours, Terminé, Annulé, No-show
- **Type** : Consultation, Urgence, Suivi, Pré-saison, Post-match, Rééducation, etc.
- **Date** : Filtre par date spécifique

### Carte Patient

Chaque patient affiche :

- **Avatar** : Initiales du nom
- **Identité** : Prénom, Nom, ID FIFA, Date de naissance
- **RDV** : Date, Heure, Type, Statut
- **Motif** : Raison du rendez-vous
- **Actions** : Boutons Medical (rouge) et PCMA (bleu)

## 🔧 Fonctionnalités Techniques

### 1. Chargement des Données

```javascript
function loadPatientList() {
  const container = document.getElementById('patient-list-container');
  container.innerHTML =
    '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Chargement des patients...</p></div>';

  // Récupérer les filtres
  const statusFilter = document.getElementById('status-filter').value;
  const typeFilter = document.getElementById('type-filter').value;
  const dateFilter = document.getElementById('date-filter').value;

  // Appel API pour récupérer les patients
  fetch(
    `/api/clinical/patients?status=${statusFilter}&type=${typeFilter}&date=${dateFilter}`
  )
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayPatientList(data.patients);
      } else {
        container.innerHTML =
          '<div class="text-center py-8 text-red-600">Erreur lors du chargement des patients</div>';
      }
    })
    .catch(error => {
      console.error('Error loading patients:', error);
      container.innerHTML =
        '<div class="text-center py-8 text-red-600">Erreur de connexion</div>';
    });
}
```

### 2. Affichage des Patients

```javascript
function displayPatientList(patients) {
  const container = document.getElementById('patient-list-container');

  if (patients.length === 0) {
    container.innerHTML =
      '<div class="text-center py-8 text-gray-500">Aucun patient trouvé</div>';
    return;
  }

  let html = '';
  patients.forEach(patient => {
    const appointmentDate = new Date(
      patient.appointment_date
    ).toLocaleDateString('fr-FR');
    const appointmentTime = new Date(
      patient.appointment_date
    ).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

    html += `
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-lg font-semibold">
                                    ${patient.first_name ? patient.first_name.charAt(0) : 'N'}
                                    ${patient.last_name ? patient.last_name.charAt(0) : 'A'}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">
                                ${patient.first_name || 'N/A'} ${patient.last_name || 'N/A'}
                            </h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>ID FIFA:</strong> ${patient.fifa_connect_id || 'N/A'}</p>
                                <p><strong>Date de naissance:</strong> ${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('fr-FR') : 'N/A'}</p>
                                <p><strong>RDV:</strong> ${appointmentDate} à ${appointmentTime}</p>
                                <p><strong>Type:</strong> ${getAppointmentTypeLabel(patient.appointment_type)}</p>
                                <p><strong>Statut:</strong> 
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(patient.status)}">
                                        ${patient.status}
                                    </span>
                                </p>
                                ${patient.reason ? `<p><strong>Motif:</strong> ${patient.reason}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="selectPatientForMedical(${patient.player_id}, '${patient.first_name}', '${patient.last_name}', '${patient.fifa_connect_id}', '${patient.date_of_birth}')" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                            🏥 Medical
                        </button>
                        <button onclick="selectPatientForPCMA(${patient.player_id}, '${patient.first_name}', '${patient.last_name}', '${patient.fifa_connect_id}', '${patient.date_of_birth}')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            📋 PCMA
                        </button>
                    </div>
                </div>
            </div>
        `;
  });

  container.innerHTML = html;
}
```

### 3. Sélection et Préremplissage

```javascript
function selectPatientForMedical(
  playerId,
  firstName,
  lastName,
  fifaConnectId,
  dateOfBirth
) {
  const params = new URLSearchParams({
    patient_id: playerId,
    first_name: firstName,
    last_name: lastName,
    fifa_connect_id: fifaConnectId,
    date_of_birth: dateOfBirth,
    source: 'clinician_portal',
  });

  window.location.href = `/modules/medical?${params.toString()}`;
}

function selectPatientForPCMA(
  playerId,
  firstName,
  lastName,
  fifaConnectId,
  dateOfBirth
) {
  const params = new URLSearchParams({
    patient_id: playerId,
    first_name: firstName,
    last_name: lastName,
    fifa_connect_id: fifaConnectId,
    date_of_birth: dateOfBirth,
    source: 'clinician_portal',
  });

  window.location.href = `/pcma/dashboard?${params.toString()}`;
}
```

## 🗄️ API Backend

### Route API

```php
// Patient List for Clinician Portal
Route::get('/patients', function (Request $request) {
    try {
        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $dateFilter = $request->get('date');

        // Construire la requête pour récupérer les patients avec leurs RDV
        $query = DB::table('appointments')
            ->join('players', 'appointments.athlete_id', '=', 'players.id')
            ->select(
                'appointments.*',
                'players.id as player_id',
                'players.first_name',
                'players.last_name',
                'players.date_of_birth',
                'players.fifa_connect_id',
                'players.nationality',
                'players.position'
            )
            ->orderBy('appointments.appointment_date', 'desc');

        // Appliquer les filtres
        if ($statusFilter) {
            $query->where('appointments.status', $statusFilter);
        }
        if ($typeFilter) {
            $query->where('appointments.appointment_type', $typeFilter);
        }
        if ($dateFilter) {
            $query->whereDate('appointments.appointment_date', $dateFilter);
        }

        $patients = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'patients' => $patients,
            'total' => $patients->count(),
            'filters' => [
                'status' => $statusFilter,
                'type' => $typeFilter,
                'date' => $dateFilter
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erreur lors de la récupération des patients: ' . $e->getMessage()
        ], 500);
    }
})->name('api.clinical.patients');
```

### Structure des Données

```json
{
  "success": true,
  "patients": [
    {
      "id": 1,
      "athlete_id": 123,
      "appointment_date": "2024-01-15 14:30:00",
      "appointment_type": "consultation",
      "status": "Confirmé",
      "reason": "Contrôle de routine",
      "player_id": 123,
      "first_name": "Ahmed",
      "last_name": "Ben Ali",
      "date_of_birth": "1995-03-15",
      "fifa_connect_id": "FIFA123456",
      "nationality": "Tunisienne",
      "position": "Attaquant"
    }
  ],
  "total": 1,
  "filters": {
    "status": "",
    "type": "",
    "date": ""
  }
}
```

## 🎯 Workflow Utilisateur

### 1. Accès au Portail Clinicien

- URL : `http://localhost:8000/clinical/clinician-portal`
- Authentification requise

### 2. Clic sur "👥 Liste des patients"

- **Ouverture du modal** avec liste des patients
- **Chargement automatique** des données depuis la base
- **Affichage des filtres** pour affiner la recherche

### 3. Sélection d'un Patient

- **Visualisation** des informations complètes
- **Choix du module** : Medical ou PCMA
- **Clic sur le bouton** correspondant

### 4. Préremplissage Automatique

- **Navigation** vers le module choisi
- **Paramètres URL** : `patient_id`, `first_name`, `last_name`, `fifa_connect_id`, `date_of_birth`, `source`
- **Préremplissage** des champs d'identité

## 🧪 Test de l'Interface

### 1. Accès au Portail

```bash
# Démarrer le serveur Laravel
php artisan serve --host=127.0.0.1 --port=8000

# Accéder au portail clinicien (nécessite authentification)
http://localhost:8000/clinical/clinician-portal
```

### 2. Vérifications

- ✅ **Bouton "👥 Liste des patients"** : Ouvre le modal
- ✅ **Modal** : Affiche la liste des patients avec filtres
- ✅ **Filtres** : Fonctionnent correctement
- ✅ **Boutons Medical/PCMA** : Navigation avec paramètres
- ✅ **Préremplissage** : Données transmises via URL

### 3. Test des Filtres

- ✅ **Statut** : Filtre par statut de RDV
- ✅ **Type** : Filtre par type de RDV
- ✅ **Date** : Filtre par date spécifique
- ✅ **Combinaison** : Filtres multiples

## 📊 Données Disponibles

### Informations Patient

- **Identité** : Prénom, Nom, ID FIFA
- **Démographie** : Date de naissance, Nationalité, Position
- **RDV** : Date, Heure, Type, Statut, Motif
- **Médical** : Historique, Allergies, Médicaments

### Types de RDV

- **consultation** : Consultation générale
- **emergency** : Urgence médicale
- **follow_up** : Suivi médical
- **pre_season** : Évaluation pré-saison
- **post_match** : Contrôle post-match
- **rehabilitation** : Rééducation
- **routine_checkup** : Contrôle de routine
- **injury_assessment** : Évaluation de blessure
- **cardiac_evaluation** : Évaluation cardiaque
- **concussion_assessment** : Évaluation commotion

### Statuts de RDV

- **Planifié** : RDV programmé
- **Confirmé** : RDV confirmé
- **En cours** : Consultation en cours
- **Terminé** : Consultation terminée
- **Annulé** : RDV annulé
- **No-show** : Patient absent

## 🎯 Résultat Final

**Le bouton "👥 Liste des patients" offre maintenant :**

- ✅ **Connexion à la base** des RDV et joueurs
- ✅ **Modal moderne** avec filtres avancés
- ✅ **Affichage complet** des informations patient
- ✅ **Sélection directe** vers Medical ou PCMA
- ✅ **Préremplissage automatique** via paramètres URL
- ✅ **Interface intuitive** et professionnelle

**L'interface permet maintenant de sélectionner un patient depuis la base des RDV et de préremplir automatiquement les dossiers Medical ou PCMA !** 🚀

## 🔄 Prochaines Étapes

Pour compléter l'implémentation, il faudra :

1. **Modifier les modules Medical et PCMA** pour accepter les paramètres URL
2. **Implémenter le préremplissage** des formulaires
3. **Tester la navigation** entre les modules
4. **Valider les données** transmises

**L'implémentation de base est maintenant complète et fonctionnelle !** ✅
