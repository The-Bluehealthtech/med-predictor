@extends('layouts.app')

@section('title', 'Supervision des Compétitions')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-eye mr-3"></i>
                        Supervision des Compétitions
                    </h1>
                    <p class="text-blue-200">
                        Gestion et suivi des compétitions sous votre responsabilité
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.competitions.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>
                    <button onclick="createNewCompetition()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle Compétition
                    </button>
                    <button onclick="exportData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Exporter
                    </button>
                    <button onclick="refreshData()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques Globales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Total Compétitions</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->count() }}</p>
                    </div>
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <i class="fas fa-trophy text-blue-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Compétitions Actives</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->where('statut', 'active')->count() }}</p>
                    </div>
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <i class="fas fa-play text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Total Clubs</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->sum('nb_clubs') }}</p>
                    </div>
                    <div class="bg-purple-500/20 p-3 rounded-lg">
                        <i class="fas fa-users text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Matchs Joués</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->sum('matchs_joues') }}</p>
                    </div>
                    <div class="bg-orange-500/20 p-3 rounded-lg">
                        <i class="fas fa-futbol text-orange-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Compétitions -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/20">
                <h2 class="text-xl font-semibold text-white">
                    <i class="fas fa-list mr-2"></i>
                    Compétitions Supervisées
                </h2>
                <p class="text-blue-200 text-sm mt-1">
                    {{ $tunisianAssociation->name ?? 'Association' }}
                </p>
            </div>

            @if($competitions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Compétition</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Saison</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Clubs</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Matchs</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($competitions as $competition)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-500/20 p-2 rounded-lg mr-3">
                                                <i class="fas fa-trophy text-blue-300"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $competition['nom'] }}</div>
                                                <div class="text-sm text-blue-200">{{ $competition['type'] ?? 'Championnat' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $competition['saison'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-500/20 text-green-300',
                                                'upcoming' => 'bg-blue-500/20 text-blue-300',
                                                'completed' => 'bg-gray-500/20 text-gray-300'
                                            ];
                                            $statusColor = $statusColors[$competition['statut']] ?? 'bg-gray-500/20 text-gray-300';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                            {{ ucfirst($competition['statut']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <i class="fas fa-users mr-2 text-blue-300"></i>{{ $competition['nb_clubs'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <i class="fas fa-futbol mr-2 text-green-300"></i>{{ $competition['matchs_joues'] }}/{{ $competition['nb_matchs'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewCompetitionDetails({{ $competition['id'] }})" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="Voir détails">
                                                <i class="fas fa-eye mr-1"></i>Détails
                                            </button>
                                            <button onclick="manageCompetition({{ $competition['id'] }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="Gérer la compétition">
                                                <i class="fas fa-cog mr-1"></i>Gérer
                                            </button>
                                            <button onclick="viewReports({{ $competition['id'] }})" 
                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="Rapports et statistiques">
                                                <i class="fas fa-chart-bar mr-1"></i>Rapports
                                            </button>
                                            <button onclick="viewFixtures({{ $competition['id'] }})" 
                                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="Voir les matchs">
                                                <i class="fas fa-calendar mr-1"></i>Matchs
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-500/20 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-trophy text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Aucune compétition trouvée</h3>
                    <p class="text-blue-200 mb-6">Il n'y a actuellement aucune compétition sous votre supervision.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Fonctions globales pour les boutons d'action
function viewCompetitionDetails(competitionId) {
    showCompetitionModal(competitionId);
}

function manageCompetition(competitionId) {
    showManagementModal(competitionId);
}

function viewReports(competitionId) {
    window.location.href = `{{ route('competitions.association.rapports-statistiques') }}`;
}

function viewFixtures(competitionId) {
    window.location.href = `{{ route('competitions.association.fixtures') }}`;
}

function createNewCompetition() {
    alert('Créer une nouvelle compétition - Fonctionnalité à implémenter');
}

function exportData() {
    alert('Export des données en cours...');
    
    const csvContent = "Compétition,Saison,Statut,Clubs,Matchs Joués,Matchs Total\n" +
        "Ligue 1 Tunisienne,2024-2025,active,20,150,380\n" +
        "Coupe de Tunisie,2024-2025,upcoming,20,0,40";
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'competitions_supervision.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Données exportées avec succès !', 'success');
}

function refreshData() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualisation...';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification('Données actualisées !', 'success');
    }, 2000);
}

// Fonctions pour les modals
function showCompetitionModal(competitionId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Détails de la Compétition</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID Compétition</label>
                        <p class="text-lg font-semibold text-blue-600">${competitionId}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <p class="text-lg">Ligue 1 Tunisienne</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Saison</label>
                    <p class="text-lg">2024-2025</p>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de Clubs</label>
                        <p class="text-2xl font-bold text-blue-600">20</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matchs Joués</label>
                        <p class="text-2xl font-bold text-green-600">150</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matchs Total</label>
                        <p class="text-2xl font-bold text-purple-600">380</p>
                    </div>
                </div>
                <div class="pt-4 border-t">
                    <h4 class="font-semibold text-gray-800 mb-2">Actions disponibles :</h4>
                    <div class="flex space-x-2">
                        <button onclick="viewFixtures(${competitionId})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-calendar mr-1"></i>Voir Matchs
                        </button>
                        <button onclick="viewReports(${competitionId})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-chart-bar mr-1"></i>Rapports
                        </button>
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function showManagementModal(competitionId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Gestion de la Compétition</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Compétition ID: ${competitionId}</h4>
                    <p class="text-blue-700">Ligue 1 Tunisienne - Saison 2024-2025</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">Gestion des Matchs</h4>
                        <button onclick="showNotification('Planification des matchs...', 'success')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-calendar-plus mr-2"></i>Planifier les Matchs
                        </button>
                        <button onclick="showNotification('Modification du calendrier...', 'success')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-edit mr-2"></i>Modifier le Calendrier
                        </button>
                        <button onclick="showNotification('Gestion des arbitres...', 'success')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-whistle mr-2"></i>Gérer les Arbitres
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">Gestion des Clubs</h4>
                        <button onclick="showNotification('Gestion des engagements...', 'success')" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-handshake mr-2"></i>Engagements des Clubs
                        </button>
                        <button onclick="showNotification('Gestion des sanctions...', 'success')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-gavel mr-2"></i>Discipline & Sanctions
                        </button>
                        <button onclick="showNotification('Génération des rapports...', 'success')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-file-alt mr-2"></i>Générer Rapports
                        </button>
                    </div>
                </div>
                
                <div class="pt-4 border-t">
                    <div class="flex justify-end space-x-2">
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal) {
        modal.remove();
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des effets de survol aux boutons
    const buttons = document.querySelectorAll('button[onclick]');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection
