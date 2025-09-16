@extends('layouts.app')

@section('title', 'Feuilles de Match - Club')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Feuilles de Match</h1>
            <p class="text-gray-600 mt-2">Préparation et soumission des feuilles de match</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('competitions.club.calendrier') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour au Calendrier
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Feuilles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Soumises</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut', 'Soumise')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">À Préparer</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut', 'À préparer')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Retard</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut', 'En retard')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des feuilles de match -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Feuilles de Match</h2>
        </div>
        
        @if($feuilles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effectif Disponible</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effectif Sélectionné</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($feuilles as $feuille)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $feuille['match'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($feuille['date'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($feuille['statut'] === 'Soumise')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Soumise
                                        </span>
                                    @elseif($feuille['statut'] === 'À préparer')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            📝 À préparer
                                        </span>
                                    @elseif($feuille['statut'] === 'En retard')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            ⚠️ En retard
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $feuille['statut'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="font-semibold">{{ $feuille['effectif_disponible'] }}</span>
                                        <span class="text-gray-500 ml-1">joueurs</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="font-semibold">{{ $feuille['effectif_selectionne'] }}</span>
                                        <span class="text-gray-500 ml-1">joueurs</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($feuille['statut'] === 'À préparer')
                                            <button onclick="openPreparationModal({{ $feuille['id'] }})" class="text-blue-600 hover:text-blue-900" title="Préparer feuille de match">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="openEffectifModal({{ $feuille['id'] }})" class="text-green-600 hover:text-green-900" title="Sélectionner effectif">
                                                <i class="fas fa-users"></i>
                                            </button>
                                        @elseif($feuille['statut'] === 'Soumise')
                                            <button onclick="viewFeuille({{ $feuille['id'] }})" class="text-purple-600 hover:text-purple-900" title="Voir feuille soumise">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editFeuille({{ $feuille['id'] }})" class="text-orange-600 hover:text-orange-900" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button onclick="downloadPDF({{ $feuille['id'] }})" class="text-gray-600 hover:text-gray-900" title="Télécharger PDF">
                                            <i class="fas fa-download"></i>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune feuille de match</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune feuille de match n'est actuellement disponible.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Prochaines feuilles à préparer -->
    @if($feuilles->where('statut', 'À préparer')->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Prochaines Feuilles à Préparer</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($feuilles->where('statut', 'À préparer')->take(3) as $feuille)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-gray-600">Match</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($feuille['date'])->format('d/m') }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $feuille['match'] }}</h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div><i class="fas fa-users mr-2"></i>{{ $feuille['effectif_disponible'] }} joueurs disponibles</div>
                                <div><i class="fas fa-user-check mr-2"></i>{{ $feuille['effectif_selectionne'] }} joueurs sélectionnés</div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <button onclick="openPreparationModal({{ $feuille['id'] }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Préparer
                                </button>
                                <button onclick="openEffectifModal({{ $feuille['id'] }})" class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                    <i class="fas fa-users mr-1"></i>Effectif
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Guide d'utilisation -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>Guide d'utilisation
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Préparation de la feuille de match :</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Sélectionnez les joueurs éligibles</li>
                    <li>• Vérifiez les licences et PCMA</li>
                    <li>• Définissez la composition d'équipe</li>
                    <li>• Soumettez avant la date limite</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Statuts des feuilles :</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• <span class="font-semibold">À préparer</span> : Feuille à compléter</li>
                    <li>• <span class="font-semibold">Soumise</span> : Feuille validée et envoyée</li>
                    <li>• <span class="font-semibold">En retard</span> : Délai dépassé</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de préparation de feuille de match -->
<div id="preparationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Préparer la feuille de match</h3>
                <button onclick="closePreparationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="preparationContent">
                <!-- Contenu dynamique -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closePreparationModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Annuler
                </button>
                <button onclick="savePreparation()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection d'effectif -->
<div id="effectifModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Sélectionner l'effectif</h3>
                <button onclick="closeEffectifModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="effectifContent">
                <!-- Contenu dynamique -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeEffectifModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Annuler
                </button>
                <button onclick="saveEffectif()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Valider l'effectif
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentFeuilleId = null;

// Fonction pour ouvrir le modal de préparation
function openPreparationModal(feuilleId) {
    currentFeuilleId = feuilleId;
    const modal = document.getElementById('preparationModal');
    const content = document.getElementById('preparationContent');
    
    // Contenu du modal de préparation
    content.innerHTML = `
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">Informations du match</h4>
                <p class="text-blue-800">Feuille de match #${feuilleId}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capitaine</label>
                    <select class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option>Sélectionner le capitaine</option>
                        <option>Jean Dupont</option>
                        <option>Pierre Martin</option>
                        <option>Ahmed Ben Ali</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vice-capitaine</label>
                    <select class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option>Sélectionner le vice-capitaine</option>
                        <option>Jean Dupont</option>
                        <option>Pierre Martin</option>
                        <option>Ahmed Ben Ali</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarques</label>
                <textarea class="w-full border border-gray-300 rounded-md px-3 py-2" rows="3" placeholder="Ajouter des remarques..."></textarea>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h4 class="font-semibold text-yellow-900 mb-2">⚠️ Vérifications importantes</h4>
                <ul class="text-yellow-800 text-sm space-y-1">
                    <li>• Vérifier les licences des joueurs</li>
                    <li>• Contrôler les PCMA (Passages de Compétition)</li>
                    <li>• S'assurer de la disponibilité des joueurs</li>
                </ul>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Fonction pour ouvrir le modal d'effectif
function openEffectifModal(feuilleId) {
    currentFeuilleId = feuilleId;
    const modal = document.getElementById('effectifModal');
    const content = document.getElementById('effectifContent');
    
    // Contenu du modal d'effectif
    content.innerHTML = `
        <div class="space-y-4">
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-green-900 mb-2">Sélection de l'effectif</h4>
                <p class="text-green-800">Choisissez les joueurs pour cette feuille de match</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="font-medium text-gray-900 mb-3">Joueurs disponibles (18)</h5>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" class="rounded" checked>
                            <span class="text-sm">Jean Dupont - Gardien</span>
                        </label>
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" class="rounded" checked>
                            <span class="text-sm">Pierre Martin - Défenseur</span>
                        </label>
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" class="rounded" checked>
                            <span class="text-sm">Ahmed Ben Ali - Milieu</span>
                        </label>
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" class="rounded">
                            <span class="text-sm">Mohamed Salah - Attaquant</span>
                        </label>
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" class="rounded">
                            <span class="text-sm">Karim Benzema - Attaquant</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <h5 class="font-medium text-gray-900 mb-3">Joueurs sélectionnés (3)</h5>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="text-sm">Jean Dupont - Gardien</span>
                            <button class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="text-sm">Pierre Martin - Défenseur</span>
                            <button class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="text-sm">Ahmed Ben Ali - Milieu</span>
                            <button class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2">📋 Règles de sélection</h4>
                <ul class="text-blue-800 text-sm space-y-1">
                    <li>• Minimum 11 joueurs, maximum 18 joueurs</li>
                    <li>• Au moins 1 gardien de but</li>
                    <li>• Vérifier les suspensions et blessures</li>
                </ul>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Fonction pour fermer le modal de préparation
function closePreparationModal() {
    document.getElementById('preparationModal').classList.add('hidden');
}

// Fonction pour fermer le modal d'effectif
function closeEffectifModal() {
    document.getElementById('effectifModal').classList.add('hidden');
}

// Fonction pour sauvegarder la préparation
function savePreparation() {
    alert('Préparation sauvegardée avec succès !');
    closePreparationModal();
    // Ici vous pourriez faire un appel AJAX pour sauvegarder
}

// Fonction pour sauvegarder l'effectif
function saveEffectif() {
    alert('Effectif validé avec succès !');
    closeEffectifModal();
    // Ici vous pourriez faire un appel AJAX pour sauvegarder
}

// Fonction pour voir une feuille soumise
function viewFeuille(feuilleId) {
    alert('Affichage de la feuille de match #' + feuilleId);
    // Ici vous pourriez ouvrir un modal ou rediriger vers une page
}

// Fonction pour modifier une feuille
function editFeuille(feuilleId) {
    alert('Modification de la feuille de match #' + feuilleId);
    // Ici vous pourriez ouvrir le modal de préparation
}

// Fonction pour télécharger le PDF
function downloadPDF(feuilleId) {
    alert('Téléchargement du PDF de la feuille #' + feuilleId);
    // Ici vous pourriez déclencher le téléchargement
}

// Fermer les modals en cliquant à l'extérieur
window.onclick = function(event) {
    const preparationModal = document.getElementById('preparationModal');
    const effectifModal = document.getElementById('effectifModal');
    
    if (event.target === preparationModal) {
        closePreparationModal();
    }
    if (event.target === effectifModal) {
        closeEffectifModal();
    }
}
</script>
@endsection
