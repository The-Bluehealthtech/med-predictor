@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Arbitres</h1>
            <p class="mt-2 text-gray-600">Liste des arbitres et gestion des assignations</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'referee')->count() }}</div>
                            <div class="text-sm text-gray-500">Total Arbitres</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'referee')->where('status', 'active')->count() }}</div>
                            <div class="text-sm text-gray-500">Arbitres Actifs</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\GameMatch::where('status', 'scheduled')->count() }}</div>
                            <div class="text-sm text-gray-500">Matchs Programmes</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\GameMatch::where('status', 'completed')->count() }}</div>
                            <div class="text-sm text-gray-500">Matchs Termines</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('test.referee-assignments') }}" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium text-blue-900">Désignation des Arbitres</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('competitions.association.designation-arbitres') }}" class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium text-green-900">Gestion des Compétitions</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('referee.dashboard') }}" class="block p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            </svg>
                            <span class="font-medium text-orange-900">Portail Arbitre</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Referees List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Liste des Arbitres</h2>
                    <div class="flex space-x-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Ajouter Arbitre
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs Assignés</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach(\App\Models\User::where('role', 'referee')->orderBy('name')->get() as $referee)
                            <tr class="hover:bg-gray-50" data-referee-id="{{ $referee->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-orange-600">{{ substr($referee->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $referee->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $referee->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $referee->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $referee->status ?? 'Actif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \App\Models\GameMatch::whereHas('officials', function ($query) use ($referee) {
                                        $query->where('user_id', $referee->id);
                                    })->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewReferee({{ $referee->id }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="Voir détails">👁️ Voir</button>
                                        <button onclick="assignReferee({{ $referee->id }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="Assigner à un match">📋 Assigner</button>
                                        <button onclick="editReferee({{ $referee->id }})" class="text-orange-600 hover:text-orange-900 px-2 py-1 rounded" title="Modifier">✏️ Modifier</button>
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
</div>

<!-- Modal pour voir les détails d'un arbitre -->
<div id="viewRefereeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    👁️ Détails de l'Arbitre
                </h3>
                <button onclick="closeViewRefereeModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <div id="refereeDetails">
                <!-- Les détails seront injectés ici -->
            </div>
        </div>
    </div>
</div>

<!-- Modal pour assigner un arbitre -->
<div id="assignRefereeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    📋 Assigner l'Arbitre
                </h3>
                <button onclick="closeAssignRefereeModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="assignRefereeForm">
                <input type="hidden" id="assignRefereeId" name="referee_id">
                
                <div class="mb-4">
                    <label for="assignMatch" class="block text-sm font-medium text-gray-700 mb-2">
                        Match à assigner
                    </label>
                    <select id="assignMatch" name="match_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un match...</option>
                        <!-- Les matchs seront injectés ici -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="assignRole" class="block text-sm font-medium text-gray-700 mb-2">
                        Rôle
                    </label>
                    <select id="assignRole" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="main_referee">Arbitre Principal</option>
                        <option value="assistant_referee_1">Assistant Arbitre 1</option>
                        <option value="assistant_referee_2">Assistant Arbitre 2</option>
                        <option value="fourth_official">4ème Arbitre</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignRefereeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveRefereeAssignment()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 Assigner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier un arbitre -->
<div id="editRefereeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ✏️ Modifier l'Arbitre
                </h3>
                <button onclick="closeEditRefereeModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editRefereeForm">
                <input type="hidden" id="editRefereeId" name="referee_id">
                
                <div class="mb-4">
                    <label for="editRefereeName" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom
                    </label>
                    <input type="text" id="editRefereeName" name="name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editRefereeEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="editRefereeEmail" name="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editRefereeStatus" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut
                    </label>
                    <select id="editRefereeStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                        <option value="suspended">Suspendu</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditRefereeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveRefereeChanges()" 
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
function viewReferee(refereeId) {
    console.log('Voir arbitre:', refereeId);
    
    // Récupérer les données de l'arbitre depuis la ligne du tableau
    const row = document.querySelector(`tr[data-referee-id="${refereeId}"]`);
    if (row) {
        const name = row.querySelector('td:nth-child(1) .text-sm.font-medium').textContent;
        const email = row.querySelector('td:nth-child(2)').textContent;
        const status = row.querySelector('td:nth-child(3) span').textContent;
        const matchesAssigned = row.querySelector('td:nth-child(4)').textContent;
        
        // Afficher les détails dans le modal
        document.getElementById('refereeDetails').innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <p class="text-lg">${name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-lg">${email}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                    <p class="text-lg">${status}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Matchs Assignés</label>
                    <p class="text-lg">${matchesAssigned}</p>
                </div>
            </div>
        `;
    }
    
    document.getElementById('viewRefereeModal').classList.remove('hidden');
}

function closeViewRefereeModal() {
    document.getElementById('viewRefereeModal').classList.add('hidden');
}

function assignReferee(refereeId) {
    console.log('Assigner arbitre:', refereeId);
    document.getElementById('assignRefereeId').value = refereeId;
    
    // Charger les matchs disponibles
    loadAvailableMatches();
    
    document.getElementById('assignRefereeModal').classList.remove('hidden');
}

function closeAssignRefereeModal() {
    document.getElementById('assignRefereeModal').classList.add('hidden');
}

function editReferee(refereeId) {
    console.log('Modifier arbitre:', refereeId);
    
    // Récupérer les données de l'arbitre depuis la ligne du tableau
    const row = document.querySelector(`tr[data-referee-id="${refereeId}"]`);
    if (row) {
        const name = row.querySelector('td:nth-child(1) .text-sm.font-medium').textContent;
        const email = row.querySelector('td:nth-child(2)').textContent;
        const status = row.querySelector('td:nth-child(3) span').textContent;
        
        // Pré-remplir le formulaire
        document.getElementById('editRefereeId').value = refereeId;
        document.getElementById('editRefereeName').value = name;
        document.getElementById('editRefereeEmail').value = email;
        document.getElementById('editRefereeStatus').value = status.toLowerCase();
    }
    
    document.getElementById('editRefereeModal').classList.remove('hidden');
}

function closeEditRefereeModal() {
    document.getElementById('editRefereeModal').classList.add('hidden');
}

function loadAvailableMatches() {
    // Simuler le chargement des matchs disponibles
    const matchSelect = document.getElementById('assignMatch');
    matchSelect.innerHTML = '<option value="">Sélectionner un match...</option>';
    
    // Ajouter quelques matchs fictifs pour la démonstration
    const matches = [
        { id: 1, name: 'EST vs CSS - Journée 1' },
        { id: 2, name: 'ESS vs USM - Journée 1' },
        { id: 3, name: 'ASG vs CAB - Journée 2' },
        { id: 4, name: 'UST vs JSK - Journée 2' }
    ];
    
    matches.forEach(match => {
        const option = document.createElement('option');
        option.value = match.id;
        option.textContent = match.name;
        matchSelect.appendChild(option);
    });
}

function saveRefereeAssignment() {
    const refereeId = document.getElementById('assignRefereeId').value;
    const matchId = document.getElementById('assignMatch').value;
    const role = document.getElementById('assignRole').value;
    
    if (!matchId) {
        alert('Veuillez sélectionner un match');
        return;
    }
    
    console.log('Assignation:', { refereeId, matchId, role });
    
    // Simuler l'assignation
    alert(`Arbitre ${refereeId} assigné au match ${matchId} en tant que ${role}`);
    closeAssignRefereeModal();
    
    // Recharger la page pour mettre à jour les données
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function saveRefereeChanges() {
    const refereeId = document.getElementById('editRefereeId').value;
    const name = document.getElementById('editRefereeName').value;
    const email = document.getElementById('editRefereeEmail').value;
    const status = document.getElementById('editRefereeStatus').value;
    
    console.log('Modifications:', { refereeId, name, email, status });
    
    // Simuler la sauvegarde
    alert(`Modifications sauvegardées pour l'arbitre ${refereeId}`);
    closeEditRefereeModal();
    
    // Recharger la page pour mettre à jour les données
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endsection
