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
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->sum('amende') }}€</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amende</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sanctions as $sanction)
                            <tr class="hover:bg-gray-50">
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
                                        <span class="text-red-600 font-semibold">{{ $sanction['amende'] }}€</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($sanction['statut'] === 'En attente')
                                            <button onclick="validateSanction({{ $sanction['id'] }})" class="text-green-600 hover:text-green-900" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectSanction({{ $sanction['id'] }})" class="text-red-600 hover:text-red-900" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <button onclick="viewSanction({{ $sanction['id'] }})" class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                            <i class="fas fa-eye"></i>
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
    alert('Affichage des détails de la sanction #' + sanctionId);
    // Ici vous pourriez ouvrir un modal ou rediriger vers une page
}
</script>
@endsection
