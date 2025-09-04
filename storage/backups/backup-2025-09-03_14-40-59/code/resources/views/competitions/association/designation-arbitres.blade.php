@extends('layouts.app')

@section('title', 'Désignation des Arbitres')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Désignation des Arbitres</h1>
                    <p class="mt-2 text-gray-600">Gestion des arbitres et désignation pour les matchs</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportDesignations()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i>
                        Exporter
                    </button>
                    <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                    <select id="competitionFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les compétitions</option>
                        @foreach($competitions as $competition)
                            <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="dateFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="statutFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="À désigner">À désigner</option>
                        <option value="Désigné">Désigné</option>
                        <option value="Confirmé">Confirmé</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-2"></i>
                        Appliquer
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Matchs à Désigner</h3>
                        <p class="text-sm text-gray-500" id="matchsADesigner">{{ count($matchs) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Arbitres Disponibles</h3>
                        <p class="text-sm text-gray-500" id="arbitresDisponibles">{{ count(collect($arbitres)->where('disponible', true)) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Désignations en Attente</h3>
                        <p class="text-sm text-gray-500" id="designationsAttente">{{ count(collect($matchs)->where('statut', 'Désigné')) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Urgences</h3>
                        <p class="text-sm text-gray-500" id="urgences">{{ count(collect($matchs)->where('urgent', true)) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des matchs à désigner -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Matchs à Désigner</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($matchs as $match)
                            <tr class="hover:bg-gray-50 match-row" data-competition="{{ $match['competition_id'] }}" data-date="{{ $match['date']->format('Y-m-d') }}" data-statut="{{ $match['statut'] }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-bold text-sm">VS</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $match['domicile']->name ?? 'Club Domicile' }} vs {{ $match['exterieur']->name ?? 'Club Extérieur' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $match['domicile']->short_name ?? 'CD' }} vs {{ $match['exterieur']->short_name ?? 'CE' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $match['date']->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $match['heure'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $match['stade'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $match['competition'] }}</div>
                                    <div class="text-sm text-gray-500">Journée {{ $match['journee'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($match['statut'] === 'À désigner')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            À désigner
                                        </span>
                                    @elseif($match['statut'] === 'Désigné')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Désigné
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Confirmé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($match['arbitres_designes'])
                                        <div class="text-sm text-gray-900">
                                            <div>{{ $match['arbitres_designes']['principal'] ?? 'Non désigné' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $match['arbitres_designes']['assistant1'] ?? '' }} / {{ $match['arbitres_designes']['assistant2'] ?? '' }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Non désigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="designerArbitres({{ $match['id'] }})" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-user-plus mr-1"></i>
                                            Désigner
                                        </button>
                                        @if($match['statut'] !== 'À désigner')
                                            <button onclick="modifierDesignation({{ $match['id'] }})" 
                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-edit mr-1"></i>
                                                Modifier
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Liste des arbitres disponibles -->
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Arbitres Disponibles</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expérience</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponibilité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($arbitres as $arbitre)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-whistle text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $arbitre['nom'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $arbitre['type'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $arbitre['type'] === 'Principal' ? 'bg-blue-100 text-blue-800' : 
                                           ($arbitre['type'] === 'Assistant' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $arbitre['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $arbitre['experience'] }} ans</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $arbitre['matchs_officies'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($arbitre['disponible'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Indisponible
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $arbitre['note'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="voirProfil({{ $arbitre['id'] }})" 
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-eye mr-1"></i>
                                            Profil
                                        </button>
                                        <button onclick="gererDisponibilite({{ $arbitre['id'] }})" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Planning
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de désignation d'arbitres -->
<div id="designationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Désigner les Arbitres</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="modalContent">
                <!-- Le contenu sera généré dynamiquement -->
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour désigner des arbitres
function designerArbitres(matchId) {
    const modal = document.getElementById('designationModal');
    const content = document.getElementById('modalContent');
    
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Arbitre Principal</label>
                <select id="arbitrePrincipal" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un arbitre principal</option>
                    @foreach($arbitres as $arbitre)
                        @if($arbitre['type'] === 'Principal' && $arbitre['disponible'])
                            <option value="{{ $arbitre['id'] }}">{{ $arbitre['nom'] }} ({{ $arbitre['note'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assistant 1</label>
                <select id="assistant1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un assistant</option>
                    @foreach($arbitres as $arbitre)
                        @if($arbitre['type'] === 'Assistant' && $arbitre['disponible'])
                            <option value="{{ $arbitre['id'] }}">{{ $arbitre['nom'] }} ({{ $arbitre['note'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assistant 2</label>
                <select id="assistant2" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un assistant</option>
                    @foreach($arbitres as $arbitre)
                        @if($arbitre['type'] === 'Assistant' && $arbitre['disponible'])
                            <option value="{{ $arbitre['id'] }}">{{ $arbitre['nom'] }} ({{ $arbitre['note'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">VAR (optionnel)</label>
                <select id="var" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un VAR</option>
                    @foreach($arbitres as $arbitre)
                        @if($arbitre['type'] === 'VAR' && $arbitre['disponible'])
                            <option value="{{ $arbitre['id'] }}">{{ $arbitre['nom'] }} ({{ $arbitre['note'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </button>
                <button onclick="confirmerDesignation(${matchId})" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Confirmer
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Fonction pour confirmer la désignation
function confirmerDesignation(matchId) {
    const arbitrePrincipal = document.getElementById('arbitrePrincipal').value;
    const assistant1 = document.getElementById('assistant1').value;
    const assistant2 = document.getElementById('assistant2').value;
    const var = document.getElementById('var').value;
    
    if (!arbitrePrincipal || !assistant1 || !assistant2) {
        showNotification('Veuillez sélectionner au moins l\'arbitre principal et les deux assistants', 'error');
        return;
    }
    
    // Simulation de la sauvegarde
    showNotification('Désignation en cours...', 'info');
    
    setTimeout(() => {
        closeModal();
        showNotification('Arbitres désignés avec succès!', 'success');
        
        // Mettre à jour l'interface
        const matchRow = document.querySelector(`tr[data-match-id="${matchId}"]`);
        if (matchRow) {
            const statutCell = matchRow.querySelector('td:nth-child(5)');
            statutCell.innerHTML = `
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-clock mr-1"></i>
                    Désigné
                </span>
            `;
        }
    }, 1500);
}

// Fonction pour modifier une désignation
function modifierDesignation(matchId) {
    designerArbitres(matchId);
}

// Fonction pour voir le profil d'un arbitre
function voirProfil(arbitreId) {
    showNotification(`Ouverture du profil de l'arbitre #${arbitreId}`, 'info');
}

// Fonction pour gérer la disponibilité
function gererDisponibilite(arbitreId) {
    showNotification(`Ouverture du planning de l'arbitre #${arbitreId}`, 'info');
}

// Fonction pour fermer le modal
function closeModal() {
    document.getElementById('designationModal').classList.add('hidden');
}

// Fonction pour exporter les désignations
function exportDesignations() {
    showNotification('Export des désignations en cours...', 'info');
    
    setTimeout(() => {
        showNotification('Désignations exportées avec succès!', 'success');
    }, 2000);
}

// Fonction pour actualiser les données
function refreshData() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualisation...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Fonction pour appliquer les filtres
function applyFilters() {
    const competitionFilter = document.getElementById('competitionFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const statutFilter = document.getElementById('statutFilter').value;
    
    const matchRows = document.querySelectorAll('.match-row');
    matchRows.forEach(row => {
        let show = true;
        
        if (competitionFilter && row.dataset.competition !== competitionFilter) {
            show = false;
        }
        
        if (dateFilter && row.dataset.date !== dateFilter) {
            show = false;
        }
        
        if (statutFilter && row.dataset.statut !== statutFilter) {
            show = false;
        }
        
        row.style.display = show ? 'table-row' : 'none';
    });
    
    showNotification('Filtres appliqués!', 'success');
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection
