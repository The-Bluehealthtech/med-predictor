@extends('layouts.app')

@section('title', 'Visites - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏥 Gestion des Visites</h1>
                    <p class="text-gray-600 mt-2">Suivi des consultations médicales en cours</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('visits.create') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        + Nouvelle Visite
                    </a>
                    <a href="{{ route('appointments.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        📅 Rendez-vous
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aujourd'hui</p>
                        <p class="text-2xl font-semibold text-gray-900" id="today-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Enregistrées</p>
                        <p class="text-2xl font-semibold text-gray-900" id="registered-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En cours</p>
                        <p class="text-2xl font-semibold text-gray-900" id="in-progress-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Terminées</p>
                        <p class="text-2xl font-semibold text-gray-900" id="completed-count">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ request('date') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tous les statuts</option>
                        <option value="Planifié" {{ request('status') === 'Planifié' ? 'selected' : '' }}>Planifié</option>
                        <option value="Enregistré" {{ request('status') === 'Enregistré' ? 'selected' : '' }}>Enregistré</option>
                        <option value="En cours" {{ request('status') === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminé" {{ request('status') === 'Terminé' ? 'selected' : '' }}>Terminé</option>
                        <option value="Annulé" {{ request('status') === 'Annulé' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                    <select name="doctor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tous les médecins</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Athlète</label>
                    <select name="athlete_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tous les athlètes</option>
                        @foreach($athletes as $athlete)
                            <option value="{{ $athlete->id }}" {{ request('athlete_id') == $athlete->id ? 'selected' : '' }}>
                                {{ $athlete->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-4 flex space-x-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('visits.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        🗑️ Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des visites -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Visites ({{ $visits->total() }})</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Athlète
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Médecin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($visits as $visit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ substr($visit->athlete->name ?? 'N/A', 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $visit->athlete->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $visit->athlete->fifa_id ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $visit->doctor->name ?? 'Non assigné' }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $visit->visit_date->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $visit->visit_type)) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          class="getStatusClasses({{ $visit->status }})">
                                        {{ $visit->status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('visits.show', $visit) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            👁️ Voir
                                        </a>
                                        <a href="{{ route('visits.edit', $visit) }}" 
                                           class="text-green-600 hover:text-green-900">
                                            ✏️ Modifier
                                        </a>
                                        @if($visit->canStart())
                                            <button onclick="startVisit({{ $visit->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-900">
                                                ▶️ Démarrer
                                            </button>
                                        @endif
                                        @if($visit->canComplete())
                                            <button onclick="completeVisit({{ $visit->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                ✅ Terminer
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Aucune visite trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($visits->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Charger les statistiques
async function loadStatistics() {
    try {
        const response = await fetch('/visits/statistics/data');
        const stats = await response.json();
        
        document.getElementById('today-count').textContent = stats.total_today;
        document.getElementById('registered-count').textContent = stats.registered_today;
        document.getElementById('in-progress-count').textContent = stats.in_progress_today;
        document.getElementById('completed-count').textContent = stats.completed_today;
    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
    }
}

// Démarrer une visite
async function startVisit(visitId) {
    if (!confirm('Démarrer cette visite ?')) return;
    
    try {
        const response = await fetch(`/visits/${visitId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Erreur lors du démarrage de la visite');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du démarrage de la visite');
    }
}

// Terminer une visite
async function completeVisit(visitId) {
    if (!confirm('Terminer cette visite ?')) return;
    
    try {
        const response = await fetch(`/visits/${visitId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Erreur lors de la finalisation de la visite');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la finalisation de la visite');
    }
}

// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', loadStatistics);
</script>
@endpush

@push('styles')
<style>
.getStatusClasses {
    @apply inline-flex px-2 py-1 text-xs font-semibold rounded-full;
}

.getStatusClasses.Planifié {
    @apply bg-blue-100 text-blue-800;
}

.getStatusClasses.Enregistré {
    @apply bg-yellow-100 text-yellow-800;
}

.getStatusClasses.En cours {
    @apply bg-red-100 text-red-800;
}

.getStatusClasses.Terminé {
    @apply bg-green-100 text-green-800;
}

.getStatusClasses.Annulé {
    @apply bg-gray-100 text-gray-800;
}
</style>
@endpush 