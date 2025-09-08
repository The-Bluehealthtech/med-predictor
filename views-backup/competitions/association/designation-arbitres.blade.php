@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Désignation des Arbitres</h1>
                        <p class="text-green-100 mt-2">Gérer les assignations d'arbitres aux matchs</p>
                    </div>
                    <div class="text-right">
                        <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="competitionFilter" class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                        <select id="competitionFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les compétitions</option>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}" {{ $competitionId == $competition->id ? 'selected' : '' }}>
                                    {{ $competition->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                        <input type="date" id="dateFrom" value="{{ $dateFrom->format('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                        <input type="date" id="dateTo" value="{{ $dateTo->format('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button onclick="applyFilters()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des matchs -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Matchs à désigner</h2>
                
                @if($matches->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitres assignés</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matches as $match)
                                <tr class="match-row" data-match-id="{{ $match->id }}" data-competition="{{ $match->competition->id ?? '' }}" data-date="{{ $match->match_date ? $match->match_date->format('Y-m-d') : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->club->name ?? 'TBD' }} vs {{ $match->awayTeam->club->name ?? 'TBD' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->competition->name ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->venue ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Aucun arbitre assigné
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="designerArbitres({{ $match->id }})" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-xs">
                                            Désigner Arbitres
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun match à désigner</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous les matchs ont déjà des arbitres assignés ou aucun match ne correspond aux critères.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de désignation -->
<div id="designationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Désigner des Arbitres</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="modalContent">
                <!-- Le contenu sera généré dynamiquement -->
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour ouvrir le modal de désignation
function designerArbitres(matchId) {
    const modal = document.getElementById('designationModal');
    const modalContent = document.getElementById('modalContent');
    
    // Récupérer les informations du match
    const matchRow = document.querySelector(`tr[data-match-id="${matchId}"]`);
    const matchInfo = {
        homeTeam: matchRow.querySelector('td:nth-child(1)').textContent.trim(),
        date: matchRow.querySelector('td:nth-child(2)').textContent.trim(),
        competition: matchRow.querySelector('td:nth-child(3)').textContent.trim(),
        venue: matchRow.querySelector('td:nth-child(4)').textContent.trim()
    };
    
    // Générer le contenu du modal
    modalContent.innerHTML = `
        <div class="mb-4">
            <h4 class="font-medium text-gray-900 mb-2">Match sélectionné</h4>
            <div class="bg-gray-50 p-3 rounded-md text-sm">
                <div><strong>${matchInfo.homeTeam}</strong></div>
                <div class="text-gray-600">${matchInfo.date} - ${matchInfo.venue}</div>
                <div class="text-gray-600">${matchInfo.competition}</div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Arbitre Principal *</label>
                <select id="arbitrePrincipal" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un arbitre principal</option>
                    @foreach($referees as $referee)
                        <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assistant 1 *</label>
                <select id="assistant1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un assistant</option>
                    @foreach($referees as $referee)
                        <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assistant 2 *</label>
                <select id="assistant2" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un assistant</option>
                    @foreach($referees as $referee)
                        <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">4ème Arbitre (optionnel)</label>
                <select id="fourthOfficial" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner un 4ème arbitre</option>
                    @foreach($referees as $referee)
                        <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </button>
            <button onclick="confirmerDesignation(${matchId})" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Confirmer
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Fonction pour confirmer la désignation
function confirmerDesignation(matchId) {
    const arbitrePrincipal = document.getElementById('arbitrePrincipal').value;
    const assistant1 = document.getElementById('assistant1').value;
    const assistant2 = document.getElementById('assistant2').value;
    const fourthOfficial = document.getElementById('fourthOfficial').value;
    
    if (!arbitrePrincipal || !assistant1 || !assistant2) {
        showNotification('Veuillez sélectionner au moins l\'arbitre principal et les deux assistants', 'error');
        return;
    }
    
    // Simuler la sauvegarde (pour l'instant)
    showNotification('Désignation en cours...', 'info');
    
    setTimeout(() => {
        closeModal();
        showNotification('Arbitres désignés avec succès !', 'success');
        // Mettre à jour l'interface
        setTimeout(() => {
            location.reload();
        }, 1000);
    }, 1500);
}

// Fonction pour fermer le modal
function closeModal() {
    document.getElementById('designationModal').classList.add('hidden');
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
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    // Construire l'URL avec les filtres
    let url = '{{ route("competitions.association.designation-arbitres") }}?';
    
    if (competitionFilter) {
        url += `competition_id=${competitionFilter}&`;
    }
    
    if (dateFrom) {
        url += `date_from=${dateFrom}&`;
    }
    
    if (dateTo) {
        url += `date_to=${dateTo}&`;
    }
    
    // Rediriger vers la nouvelle URL
    window.location.href = url;
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

// Fermer le modal en cliquant à l'extérieur
document.getElementById('designationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection

