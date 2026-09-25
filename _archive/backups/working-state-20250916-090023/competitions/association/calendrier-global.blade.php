@extends('layouts.app')

@section('title', 'Calendrier Global - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                Calendrier Global
            </h1>
            <p class="text-gray-600 mt-2">Planning centralisé de toutes les compétitions</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
            </a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-download mr-2"></i>Exporter
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les compétitions</option>
                    <option value="championnat-u19">Championnat Régional U19</option>
                    <option value="coupe-regionale">Coupe Régionale</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="semaine">Cette semaine</option>
                    <option value="mois">Ce mois</option>
                    <option value="trimestre">Ce trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="programme">Programmé</option>
                    <option value="reporte">Reporté</option>
                    <option value="termine">Terminé</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Matchs Programmés</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($matchs) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Matchs Terminés</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Matchs Reportés</p>
                    <p class="text-2xl font-bold text-gray-900">1</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Compétitions Actives</p>
                    <p class="text-2xl font-bold text-gray-900">2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier des Matchs -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Calendrier des Matchs</h2>
        </div>
        
        @if(count($matchs) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($matchs as $match)
                            <tr class="hover:bg-gray-50" data-match-id="{{ $match['id'] }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($match['date'])->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $match['heure'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $match['competition'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $match['domicile'] }}</div>
                                    <div class="text-sm text-gray-500">vs {{ $match['exterieur'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $match['lieu'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $match['arbitre_principal'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'Programmé' => 'bg-blue-100 text-blue-800',
                                            'Reporté - Météo' => 'bg-yellow-100 text-yellow-800',
                                            'Terminé' => 'bg-green-100 text-green-800',
                                            'Annulé' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusColor = $statusColors[$match['statut']] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $match['statut'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewMatch({{ $match['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="Voir détails">👁️ Voir</button>
                                        @if($match['reprogrammable'])
                                            <button onclick="rescheduleMatch({{ $match['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="Reprogrammer">📅 Reprogrammer</button>
                                        @endif
                                        <button onclick="editMatch({{ $match['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="Modifier">✏️ Modifier</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun match programmé</h3>
                <p class="text-gray-500 mb-6">Aucun match n'est programmé pour la période sélectionnée.</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Programmer un Match
                </button>
            </div>
        @endif
    </div>

    <!-- Légende -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Légende des Statuts</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-3">Programmé</span>
                <span class="text-sm text-gray-600">Match confirmé</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-3">Reporté</span>
                <span class="text-sm text-gray-600">Match décalé</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-3">Terminé</span>
                <span class="text-sm text-gray-600">Match joué</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mr-3">Annulé</span>
                <span class="text-sm text-gray-600">Match annulé</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'un match -->
<div id="viewMatchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    👁️ Détails du Match
                </h3>
                <button onclick="closeViewMatchModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <div id="matchDetails">
                <!-- Les détails seront injectés ici -->
            </div>
        </div>
    </div>
</div>

<!-- Modal pour reprogrammer un match -->
<div id="rescheduleMatchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    📅 Reprogrammer le Match
                </h3>
                <button onclick="closeRescheduleMatchModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="rescheduleMatchForm">
                <input type="hidden" id="rescheduleMatchId" name="match_id">
                
                <div class="mb-4">
                    <label for="rescheduleDate" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouvelle Date
                    </label>
                    <input type="date" id="rescheduleDate" name="date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="rescheduleTime" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouvelle Heure
                    </label>
                    <input type="time" id="rescheduleTime" name="time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="rescheduleVenue" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau Lieu
                    </label>
                    <input type="text" id="rescheduleVenue" name="venue" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Stade...">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRescheduleMatchModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveReschedule()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 Reprogrammer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier un match -->
<div id="editMatchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ✏️ Modifier le Match
                </h3>
                <button onclick="closeEditMatchModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editMatchForm">
                <input type="hidden" id="editMatchId" name="match_id">
                
                <div class="mb-4">
                    <label for="editHomeScore" class="block text-sm font-medium text-gray-700 mb-2">
                        Score Domicile
                    </label>
                    <input type="number" id="editHomeScore" name="home_score" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editAwayScore" class="block text-sm font-medium text-gray-700 mb-2">
                        Score Extérieur
                    </label>
                    <input type="number" id="editAwayScore" name="away_score" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut
                    </label>
                    <select id="editStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="scheduled">Programmé</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="postponed">Reporté</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditMatchModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveMatchChanges()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions pour les boutons d'action
function viewMatch(matchId) {
    console.log('Voir match:', matchId);
    
    // Récupérer les données du match depuis la ligne du tableau
    const row = document.querySelector(`tr[data-match-id="${matchId}"]`);
    if (row) {
        const competition = row.querySelector('td:nth-child(2)').textContent.trim();
        const match = row.querySelector('td:nth-child(3)').textContent.trim();
        const venue = row.querySelector('td:nth-child(4)').textContent.trim();
        const date = row.querySelector('td:nth-child(1)').textContent.trim();
        const status = row.querySelector('td:nth-child(5) span').textContent.trim();
        
        // Afficher les détails dans le modal
        document.getElementById('matchDetails').innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Compétition</label>
                    <p class="text-lg">${competition}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Match</label>
                    <p class="text-lg">${match}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lieu</label>
                    <p class="text-lg">${venue}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <p class="text-lg">${date}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                    <p class="text-lg">${status}</p>
                </div>
            </div>
        `;
    }
    
    document.getElementById('viewMatchModal').classList.remove('hidden');
}

function closeViewMatchModal() {
    document.getElementById('viewMatchModal').classList.add('hidden');
}

function rescheduleMatch(matchId) {
    console.log('Reprogrammer match:', matchId);
    document.getElementById('rescheduleMatchId').value = matchId;
    
    // Pré-remplir avec la date et heure actuelles
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('rescheduleDate').value = today;
    document.getElementById('rescheduleTime').value = '15:00';
    
    document.getElementById('rescheduleMatchModal').classList.remove('hidden');
}

function closeRescheduleMatchModal() {
    document.getElementById('rescheduleMatchModal').classList.add('hidden');
}

function editMatch(matchId) {
    console.log('Modifier match:', matchId);
    document.getElementById('editMatchId').value = matchId;
    
    // Pré-remplir avec les données actuelles
    document.getElementById('editHomeScore').value = '0';
    document.getElementById('editAwayScore').value = '0';
    document.getElementById('editStatus').value = 'scheduled';
    
    document.getElementById('editMatchModal').classList.remove('hidden');
}

function closeEditMatchModal() {
    document.getElementById('editMatchModal').classList.add('hidden');
}

function saveReschedule() {
    const matchId = document.getElementById('rescheduleMatchId').value;
    const date = document.getElementById('rescheduleDate').value;
    const time = document.getElementById('rescheduleTime').value;
    const venue = document.getElementById('rescheduleVenue').value;
    
    if (!date || !time) {
        alert('Veuillez remplir la date et l\'heure');
        return;
    }
    
    console.log('Reprogrammation:', { matchId, date, time, venue });
    
    // Simuler la reprogrammation
    alert(`Match ${matchId} reprogrammé pour le ${date} à ${time}`);
    closeRescheduleMatchModal();
    
    // Recharger la page pour mettre à jour les données
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function saveMatchChanges() {
    const matchId = document.getElementById('editMatchId').value;
    const homeScore = document.getElementById('editHomeScore').value;
    const awayScore = document.getElementById('editAwayScore').value;
    const status = document.getElementById('editStatus').value;
    
    console.log('Modifications:', { matchId, homeScore, awayScore, status });
    
    // Simuler la sauvegarde
    alert(`Modifications sauvegardées pour le match ${matchId}`);
    closeEditMatchModal();
    
    // Recharger la page pour mettre à jour les données
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endsection

