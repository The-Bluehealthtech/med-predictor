@extends('layouts.app')

@section('title', 'Visite #' . $visit->id . ' - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏥 Visite #{{ $visit->id }}</h1>
                    <p class="text-gray-600 mt-2">
                        {{ $visit->athlete->name ?? 'Athlète inconnu' }} - 
                        {{ $visit->visit_date->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <span 
                        class="px-3 py-1 rounded-full text-sm font-medium
                        @if($visit->status === 'Enregistré') bg-yellow-100 text-yellow-800
                        @elseif($visit->status === 'En cours') bg-blue-100 text-blue-800
                        @elseif($visit->status === 'Terminé') bg-green-100 text-green-800
                        @elseif($visit->status === 'Annulé') bg-red-100 text-red-800
                        @elseif($visit->status === 'En attente') bg-gray-100 text-gray-800
                        @else bg-gray-100 text-gray-800 @endif"
                    >
                        {{ $visit->status }}
                    </span>
                    <a href="{{ route('visits.edit', $visit) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('visits.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations de base -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Informations de l'athlète -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Informations Athlète</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <p class="text-sm text-gray-900">{{ $visit->athlete->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">FIFA ID</label>
                        <p class="text-sm text-gray-900">{{ $visit->athlete->fifa_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                        <p class="text-sm text-gray-900">{{ $visit->athlete->dob ? $visit->athlete->dob->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nationalité</label>
                        <p class="text-sm text-gray-900">{{ $visit->athlete->nationality ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations de la visite -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🏥 Détails de la Visite</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type de visite</label>
                        <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $visit->visit_type)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date et heure</label>
                        <p class="text-sm text-gray-900">{{ $visit->visit_date->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Médecin</label>
                        <p class="text-sm text-gray-900">{{ $visit->doctor->name ?? 'Non assigné' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span 
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($visit->status === 'Enregistré') bg-yellow-100 text-yellow-800
                            @elseif($visit->status === 'En cours') bg-blue-100 text-blue-800
                            @elseif($visit->status === 'Terminé') bg-green-100 text-green-800
                            @elseif($visit->status === 'Annulé') bg-red-100 text-red-800
                            @elseif($visit->status === 'En attente') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800 @endif"
                        >
                            {{ $visit->status }}
                        </span>
                    </div>
                    @if($visit->administrative_data && isset($visit->administrative_data['complaint_data']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Motif de consultation</label>
                        <p class="text-sm text-gray-900">
                            @php
                                $complaintLabels = [
                                    'blessure_musculaire' => 'Blessure musculaire',
                                    'blessure_articulaire' => 'Blessure articulaire',
                                    'fracture' => 'Fracture',
                                    'entorse' => 'Entorse',
                                    'contusion' => 'Contusion',
                                    'commotion_cerebrale' => 'Commotion cérébrale',
                                    'fatigue' => 'Fatigue',
                                    'douleur' => 'Douleur',
                                    'examen_routine' => 'Examen de routine',
                                    'suivi_traitement' => 'Suivi de traitement',
                                    'evaluation_pre_saison' => 'Évaluation pré-saison',
                                    'evaluation_post_match' => 'Évaluation post-match',
                                    'rehabilitation' => 'Réhabilitation',
                                    'evaluation_cardiaque' => 'Évaluation cardiaque',
                                    'autre' => 'Autre'
                                ];
                                $complaintValue = $visit->administrative_data['complaint_data']['complaint'] ?? '';
                                $complaintLabel = $complaintLabels[$complaintValue] ?? $complaintValue;
                            @endphp
                            {{ $complaintLabel }}
                        </p>
                        @if(isset($visit->administrative_data['complaint_data']['complaint_notes']))
                        <div class="mt-2">
                            <label class="block text-sm font-medium text-gray-700">Notes supplémentaires</label>
                            <p class="text-sm text-gray-900">{{ $visit->administrative_data['complaint_data']['complaint_notes'] }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rendez-vous associé -->
            @if($visit->appointment)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Rendez-vous Associé</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date du RDV</label>
                        <p class="text-sm text-gray-900">{{ $visit->appointment->appointment_date->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Durée</label>
                        <p class="text-sm text-gray-900">{{ $visit->appointment->duration_minutes }} minutes</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Motif</label>
                        <p class="text-sm text-gray-900">{{ $visit->appointment->reason ?? 'Non spécifié' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut RDV</label>
                        <span 
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($visit->appointment->status === 'Planifié') bg-blue-100 text-blue-800
                            @elseif($visit->appointment->status === 'Confirmé') bg-green-100 text-green-800
                            @elseif($visit->appointment->status === 'Enregistré') bg-yellow-100 text-yellow-800
                            @elseif($visit->appointment->status === 'En cours') bg-purple-100 text-purple-800
                            @elseif($visit->appointment->status === 'Terminé') bg-gray-100 text-gray-800
                            @elseif($visit->appointment->status === 'Annulé') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif"
                        >
                            {{ $visit->appointment->status }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Actions</h3>
            <div class="space-y-3">
                @if($visit->status === 'Enregistré')
                    <div class="space-y-2">
                        <button 
                            onclick="alert('Starting visit {{ $visit->id }}'); startVisit({{ $visit->id }})"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            ▶️ Démarrer la visite (Test)
                        </button>
                        <button 
                            onclick="alert('Modal test for visit: {{ $visit->id }}'); showStartVisitModal({{ $visit->id }})"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            🏥 Modal Test
                        </button>
                        <button 
                            onclick="testDirectStart({{ $visit->id }})"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            🔧 Direct Test (No CSRF)
                        </button>
                    </div>
                @elseif($visit->status === 'En cours')
                    <div class="space-y-2">
                        <button 
                            onclick="completeVisit({{ $visit->id }})"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            ✅ Terminer la visite
                        </button>
                        <button 
                            onclick="cancelVisit({{ $visit->id }})"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            ❌ Annuler la visite
                        </button>
                    </div>
                @elseif($visit->status === 'Terminé')
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Visite terminée
                        </span>
                    </div>
                @elseif($visit->status === 'Annulé')
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ❌ Visite annulée
                        </span>
                    </div>
                @endif
                
                @if($visit->status === 'En cours' || $visit->status === 'Terminé')
                    <div class="mt-4">
                        <a href="{{ route('health-records.create', ['visit_id' => $visit->id]) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-center">
                            📋 Créer un dossier médical
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documents -->
        @if($visit->documents && $visit->documents->count() > 0)
        <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Documents</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($visit->documents as $document)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $document->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $document->type }}</p>
                        </div>
                        <a href="{{ route('documents.download', $document) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            📥 Télécharger
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Historique des visites -->
        @if($visit->athlete && $visit->athlete->visits && $visit->athlete->visits->count() > 1)
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Historique des Visites</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médecin</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($visit->athlete->visits->sortByDesc('visit_date')->take(5) as $pastVisit)
                        <tr class="{{ $pastVisit->id === $visit->id ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pastVisit->visit_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst(str_replace('_', ' ', $pastVisit->visit_type)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($pastVisit->status === 'Enregistré') bg-yellow-100 text-yellow-800
                                    @elseif($pastVisit->status === 'En cours') bg-blue-100 text-blue-800
                                    @elseif($pastVisit->status === 'Terminé') bg-green-100 text-green-800
                                    @elseif($pastVisit->status === 'Annulé') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $pastVisit->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pastVisit->doctor->name ?? 'Non assigné' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal pour démarrer une visite -->
<div id="startVisitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white z-[10000]">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🏥 Démarrer la Visite</h3>
            <form id="startVisitForm">
                <div class="mb-4">
                    <label for="complaint" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif de consultation (optionnel)
                    </label>
                    <select id="complaint" name="complaint" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Sélectionner un motif --</option>
                        <option value="blessure_musculaire">Blessure musculaire</option>
                        <option value="blessure_articulaire">Blessure articulaire</option>
                        <option value="fracture">Fracture</option>
                        <option value="entorse">Entorse</option>
                        <option value="contusion">Contusion</option>
                        <option value="commotion_cerebrale">Commotion cérébrale</option>
                        <option value="fatigue">Fatigue</option>
                        <option value="douleur">Douleur</option>
                        <option value="examen_routine">Examen de routine</option>
                        <option value="suivi_traitement">Suivi de traitement</option>
                        <option value="evaluation_pre_saison">Évaluation pré-saison</option>
                        <option value="evaluation_post_match">Évaluation post-match</option>
                        <option value="rehabilitation">Réhabilitation</option>
                        <option value="evaluation_cardiaque">Évaluation cardiaque</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="complaint_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes supplémentaires (optionnel)
                    </label>
                    <textarea id="complaint_notes" name="complaint_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Décrivez les symptômes ou le motif de consultation..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideStartVisitModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Démarrer la visite
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let currentVisitId = null;

// Démarrer une visite (ancienne fonction - gardée pour compatibilité)
async function startVisit(visitId) {
    console.log('startVisit called with visitId:', visitId);
    if (!confirm('Démarrer cette visite ?')) return;
    
    // Get CSRF token with fallback
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF token from meta:', csrfToken);
    
    if (!csrfToken) {
        // Fallback: get from cookie
        csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
        if (csrfToken) {
            csrfToken = decodeURIComponent(csrfToken);
        }
        console.log('CSRF token from cookie:', csrfToken);
    }
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Erreur: Token de sécurité manquant. Veuillez recharger la page.');
        return;
    }
    
    try {
        console.log('Sending request to:', `/visits/${visitId}/start`);
        console.log('Request headers:', {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        });
        
        const response = await fetch(`/visits/${visitId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (response.status === 401 || response.status === 403) {
            alert('Erreur: Vous devez être connecté pour effectuer cette action.');
            window.location.href = '/login';
            return;
        }
        
        if (response.ok) {
            const responseText = await response.text();
            console.log('Response text:', responseText);
            console.log('Visit started successfully, reloading page...');
            window.location.reload();
        } else {
            const errorData = await response.json();
            console.error('Failed to start visit:', errorData.message);
            alert('Erreur: ' + (errorData.message || 'Impossible de démarrer cette visite.'));
        }
    } catch (error) {
        console.error('Error starting visit:', error);
        alert('Une erreur est survenue lors du démarrage de la visite.');
    }
}

// Afficher le modal pour démarrer une visite
function showStartVisitModal(visitId) {
    console.log('showStartVisitModal called with visitId:', visitId);
    currentVisitId = visitId;
    const modal = document.getElementById('startVisitModal');
    console.log('Modal element:', modal);
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal should now be visible');
    } else {
        console.error('Modal element not found!');
    }
}

// Masquer le modal
function hideStartVisitModal() {
    document.getElementById('startVisitModal').classList.add('hidden');
    document.getElementById('startVisitForm').reset();
    currentVisitId = null;
}

// Test direct start without CSRF (for debugging)
async function testDirectStart(visitId) {
    console.log('testDirectStart called with visitId:', visitId);
    if (!confirm('Test direct start for visit ' + visitId + '?')) return;
    
    try {
        console.log('Sending direct request to:', `/visits/${visitId}/start`);
        
        const response = await fetch(`/visits/${visitId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (response.ok) {
            console.log('Direct test successful, reloading page...');
            window.location.reload();
        } else {
            const errorText = await response.text();
            console.error('Direct test failed:', errorText);
            alert('Test direct échoué: ' + errorText);
        }
    } catch (error) {
        console.error('Error in direct test:', error);
        alert('Erreur lors du test direct: ' + error.message);
    }
}

// Terminer une visite
async function completeVisit(visitId) {
    if (!confirm('Terminer cette visite ?')) return;
    
    // Get CSRF token with fallback
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        // Fallback: get from cookie
        csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
        if (csrfToken) {
            csrfToken = decodeURIComponent(csrfToken);
        }
    }
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Erreur: Token de sécurité manquant. Veuillez recharger la page.');
        return;
    }
    
    try {
        const response = await fetch(`/visits/${visitId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({})
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            const errorData = await response.json();
            alert('Erreur: ' + (errorData.message || 'Impossible de terminer cette visite.'));
        }
    } catch (error) {
        console.error('Error completing visit:', error);
        alert('Une erreur est survenue lors de la finalisation de la visite.');
    }
}

// Annuler une visite
async function cancelVisit(visitId) {
    if (!confirm('Annuler cette visite ?')) return;
    
    // Get CSRF token with fallback
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        // Fallback: get from cookie
        csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
        if (csrfToken) {
            csrfToken = decodeURIComponent(csrfToken);
        }
    }
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Erreur: Token de sécurité manquant. Veuillez recharger la page.');
        return;
    }
    
    try {
        const response = await fetch(`/visits/${visitId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({})
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            const errorData = await response.json();
            alert('Erreur: ' + (errorData.message || 'Impossible d\'annuler cette visite.'));
        }
    } catch (error) {
        console.error('Error canceling visit:', error);
        alert('Une erreur est survenue lors de l\'annulation de la visite.');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing visit functions...');
    
    // Gérer la soumission du formulaire de démarrage
    const startVisitForm = document.getElementById('startVisitForm');
    if (startVisitForm) {
        startVisitForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!currentVisitId) {
                alert('Erreur: ID de visite manquant');
                return;
            }
            
            const complaint = document.getElementById('complaint').value;
            const complaintNotes = document.getElementById('complaint_notes').value;
            
            // Get CSRF token with fallback
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                // Fallback: get from cookie
                csrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
                if (csrfToken) {
                    csrfToken = decodeURIComponent(csrfToken);
                }
            }
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Erreur: Token de sécurité manquant. Veuillez recharger la page.');
                return;
            }
            
            try {
                console.log('Sending request to:', `/visits/${currentVisitId}/start`);
                const response = await fetch(`/visits/${currentVisitId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        complaint: complaint,
                        complaint_notes: complaintNotes
                    })
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (response.status === 401 || response.status === 403) {
                    alert('Erreur: Vous devez être connecté pour effectuer cette action.');
                    window.location.href = '/login';
                    return;
                }
                
                if (response.status === 419) {
                    alert('Erreur: Token de sécurité expiré. Veuillez recharger la page.');
                    window.location.reload();
                    return;
                }
                
                if (response.ok) {
                    console.log('Visit started successfully, reloading page...');
                    hideStartVisitModal();
                    window.location.reload();
                } else {
                    const errorData = await response.json();
                    console.error('Failed to start visit:', errorData.message);
                    alert('Erreur: ' + (errorData.message || 'Impossible de démarrer cette visite.'));
                }
            } catch (error) {
                console.error('Error starting visit:', error);
                alert('Une erreur est survenue lors du démarrage de la visite.');
            }
        });
    }
    
    console.log('Visit functions initialized successfully');
});
</script>
@endsection 