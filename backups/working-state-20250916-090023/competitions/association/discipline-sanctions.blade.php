@extends('layouts.app')

@section('title', 'Discipline & Sanctions - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-gavel text-red-600 mr-3"></i>
                Discipline & Sanctions
            </h1>
            <p class="text-gray-600 mt-2">Validation et gestion des sanctions disciplinaires</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportSanctions()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                📥 Exporter
            </button>
            <button onclick="addNewSanction()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                ➕ Nouvelle Sanction
            </button>
            <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                ← Retour aux Modules
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Sanctions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('statut', 'En attente')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Validées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('statut', 'Validé')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-euro-sign text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Amendes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->sum('amende') }} TND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sanctions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Sanctions à Valider</h2>
        </div>
        
        @if($sanctions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amende (TND)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suspension</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sanctions as $sanction)
                            <tr class="hover:bg-gray-50" data-sanction-id="{{ $sanction['id'] }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['joueur'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['club'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['match'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sanction['type'] === 'Carton Jaune')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            🟡 Carton Jaune
                                        </span>
                                    @elseif($sanction['type'] === 'Carton Rouge')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            🔴 Carton Rouge
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sanction['statut'] === 'Validé')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Validé
                                        </span>
                                    @elseif($sanction['statut'] === 'En attente')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ⏳ En attente
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $sanction['statut'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($sanction['amende'] > 0)
                                        <span class="text-red-600 font-semibold">{{ $sanction['amende'] }} TND</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-90
                                    @if($sanction['suspension'] > 0)
                                        <span class="text-orange-600 font-semibold">{{ $sanction['suspension'] }} jours</iment                               @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($sanction['statut'] === 'En attente')
                                                                                    <button onclick="validateSanction({{ $sanction['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="Valider">
                                            ✅ Valider
                                        </button>
                                        <button onclick="rejectSanction({{ $sanction['id'] }})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded" title="Rejeter">
                                            ❌ Rejeter
                                        </button>
                                        @endif
                                        <button onclick="editSanction({{ $sanction['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="Modifier">
                                            ✏️ Modifier
                                        </button>
                                        <button onclick="viewSanction({{ $sanction['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="Voir détails">
                                            👁️ Voir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <div class="text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune sanction</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune sanction n'est en attente de validation.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal d'édition des sanctions -->
<div id="editSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ✏️ Modifier la Sanction
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editSanctionForm">
                <input type="hidden" id="editSanctionId" name="sanction_id">
                
                <div class="mb-4">
                    <label for="editAmende" class="block text-sm font-medium text-gray-700 mb-2">
                        Amende (TND)
                    </label>
                    <input type="number" id="editAmende" name="amende" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                
                <div class="mb-4">
                    <label for="editSuspension" class="block text-sm font-medium text-gray-700 mb-2">
                        Suspension (jours)
                    </label>
                    <input type="number" id="editSuspension" name="suspension" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="editMotif" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif
                    </label>
                    <textarea id="editMotif" name="motif" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Détails du motif..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveSanctionChanges()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'ajout de nouvelle sanction -->
<div id="addSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ➕ Nouvelle Sanction
                </h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="addSanctionForm">
                <div class="mb-4">
                    <label for="addJoueur" class="block text-sm font-medium text-gray-700 mb-2">
                        Joueur
                    </label>
                    <select id="addJoueur" name="joueur" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un joueur</option>
                        <option value="Ahmed Ben Salah">Ahmed Ben Salah</option>
                        <option value="Youssef Msakni">Youssef Msakni</option>
                        <option value="Hamza Lahmar">Hamza Lahmar</option>
                        <option value="Aymen Mathlouthi">Aymen Mathlouthi</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="addType" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de Sanction
                    </label>
                    <select id="addType" name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Carton Jaune">🟡 Carton Jaune</option>
                        <option value="Carton Rouge">🔴 Carton Rouge</option>
                        <option value="Incident Disciplinaire">⚠️ Incident Disciplinaire</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="addAmende" class="block text-sm font-medium text-gray-700 mb-2">
                        Amende (TND)
                    </label>
                    <input type="number" id="addAmende" name="amende" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                
                <div class="mb-4">
                    <label for="addSuspension" class="block text-sm font-medium text-gray-700 mb-2">
                        Suspension (jours)
                    </label>
                    <input type="number" id="addSuspension" name="suspension" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="addMotif" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif
                    </label>
                    <textarea id="addMotif" name="motif" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Détails du motif..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveNewSanction()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        ➕ Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'édition des sanctions -->
<div id="editSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ✏️ Modifier la Sanction
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editSanctionForm">
                <input type="hidden" id="editSanctionId" name="sanction_id">
                
                <div class="mb-4">
                    <label for="editAmende" class="block text-sm font-medium text-gray-700 mb-2">
                        Amende (TND)
                    </label>
                    <input type="number" id="editAmende" name="amende" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                
                <div class="mb-4">
                    <label for="editSuspension" class="block text-sm font-medium text-gray-700 mb-2">
                        Suspension (jours)
                    </label>
                    <input type="number" id="editSuspension" name="suspension" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="editMotif" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif
                    </label>
                    <textarea id="editMotif" name="motif" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Détails du motif..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="button" onclick="saveSanctionChanges()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de détails des sanctions -->
<div id="viewSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    👁️ Détails de la Sanction
                </h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <input type="hidden" id="viewSanctionId" name="sanction_id">
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Joueur:</span>
                    <span id="viewJoueur" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Club:</span>
                    <span id="viewClub" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Match:</span>
                    <span id="viewMatch" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Type:</span>
                    <span id="viewType" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Statut:</span>
                    <span id="viewStatut" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Amende:</span>
                    <span id="viewAmende" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Suspension:</span>
                    <span id="viewSuspension" class="text-sm text-gray-900"></span>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeViewModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour valider une sanction
function validateSanction(sanctionId) {
    if (confirm('Voulez-vous valider cette sanction ?')) {
        alert('Sanction #' + sanctionId + ' validée avec succès !');
        // Ici vous pourriez faire un appel AJAX pour valider
    }
}

// Fonction pour rejeter une sanction
function rejectSanction(sanctionId) {
    if (confirm('Voulez-vous rejeter cette sanction ?')) {
        alert('Sanction #' + sanctionId + ' rejetée !');
        // Ici vous pourriez faire un appel AJAX pour rejeter
    }
}

// Fonction pour voir les détails d'une sanction
function viewSanction(sanctionId) {
    // Ouvrir le modal de détails
    document.getElementById('viewSanctionModal').classList.remove('hidden');
    document.getElementById('viewSanctionId').value = sanctionId;
    
    // Récupérer les données de la ligne du tableau
    const row = document.querySelector(`tr[data-sanction-id="${sanctionId}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        const joueur = cells[0].textContent.trim();
        const club = cells[1].textContent.trim();
        const match = cells[2].textContent.trim();
        const type = cells[3].textContent.trim();
        const statut = cells[4].textContent.trim();
        const amende = cells[5].textContent.trim();
        const suspension = cells[6].textContent.trim();
        
        // Remplir le modal avec les données
        document.getElementById('viewJoueur').textContent = joueur;
        document.getElementById('viewClub').textContent = club;
        document.getElementById('viewMatch').textContent = match;
        document.getElementById('viewType').textContent = type;
        document.getElementById('viewStatut').textContent = statut;
        document.getElementById('viewAmende').textContent = amende;
        document.getElementById('viewSuspension').textContent = suspension;
    }
}

// Fonction pour fermer le modal de détails
function closeViewModal() {
    document.getElementById('viewSanctionModal').classList.add('hidden');
}

// Fonction pour éditer une sanction
function editSanction(sanctionId) {
    // Ouvrir le modal d'édition
    document.getElementById('editSanctionModal').classList.remove('hidden');
    document.getElementById('editSanctionId').value = sanctionId;
    
    // Récupérer les données de la ligne du tableau
    const row = document.querySelector(`tr[data-sanction-id="${sanctionId}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        const amende = cells[5].textContent.trim().replace(' TND', '').replace('-', '0');
        const suspension = cells[6].textContent.trim().replace(' jours', '').replace('-', '0');
        
        // Remplir le modal avec les données
        document.getElementById('editAmende').value = amende;
        document.getElementById('editSuspension').value = suspension;
    }
}

// Fonction pour fermer le modal d'édition
function closeEditModal() {
    document.getElementById('editSanctionModal').classList.add('hidden');
}

// Fonction pour sauvegarder les modifications
function saveSanctionChanges() {
    const sanctionId = document.getElementById('editSanctionId').value;
    const amende = document.getElementById('editAmende').value;
    const suspension = document.getElementById('editSuspension').value;
    const motif = document.getElementById('editMotif').value;
    
    // Validation
    if (amende < 0 || suspension < 0) {
        alert('Les valeurs ne peuvent pas être négatives');
        return;
    }
    
    // Simulation de sauvegarde
    console.log('Sauvegarde de la sanction #' + sanctionId, {
        amende: amende + ' TND',
        suspension: suspension + ' jours',
        motif: motif
    });
    
    alert('Sanction modifiée avec succès !');
    closeEditModal();
    
    // Recharger la page pour voir les changements
    location.reload();
}

// Fonction pour exporter les sanctions
function exportSanctions() {
    console.log('Export des sanctions...');
    
    // Créer un fichier CSV avec les données
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    
    let csv = 'Joueur,Club,Match,Type,Statut,Amende,Suspension\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const joueur = cells[0].textContent.trim();
        const club = cells[1].textContent.trim();
        const match = cells[2].textContent.trim();
        const type = cells[3].textContent.trim();
        const statut = cells[4].textContent.trim();
        const amende = cells[5].textContent.trim();
        const suspension = cells[6].textContent.trim();
        
        csv += `"${joueur}","${club}","${match}","${type}","${statut}","${amende}","${suspension}"\n`;
    });
    
    // Télécharger le fichier
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sanctions_disciplinaires.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    alert('Export des sanctions terminé !');
}

// Fonction pour ajouter une nouvelle sanction
function addNewSanction() {
    // Ouvrir le modal d'ajout
    document.getElementById('addSanctionModal').classList.remove('hidden');
}
</script>
@endsection
