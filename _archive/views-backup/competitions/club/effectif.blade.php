@extends('layouts.app')

@section('title', 'Effectif Éligible - Compétitions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-check text-green-600 mr-3"></i>
                    Effectif Éligible
                </h1>
                <p class="text-gray-600">Joueurs autorisés avec vérifications automatiques</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="verifierEffectif()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Vérifier Maintenant
                </button>
                <button onclick="exporterEffectif()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Éligibles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('statut', 'Éligible')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Inéligibles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('statut', '!=', 'Éligible')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">PCMA Expirés</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('pcma_a_jour', false)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-ban text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Suspendus</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('suspension', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Filtrer par statut:</label>
                <select id="filter-statut" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                    <option value="">Tous</option>
                    <option value="Éligible">Éligibles</option>
                    <option value="Inéligible">Inéligibles</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Vérifications:</label>
                <select id="filter-verification" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                    <option value="">Toutes</option>
                    <option value="licence">Licence</option>
                    <option value="pcma">PCMA</option>
                    <option value="suspension">Suspension</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <input type="text" id="search-player" placeholder="Rechercher un joueur..." 
                       class="border border-gray-300 rounded-md px-3 py-1 text-sm">
            </div>
        </div>
    </div>

    <!-- Liste de l'Effectif -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Effectif du Club</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licence FIFA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vérifications</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIFA Connect</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Vérification</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($effectif as $joueur)
                    <tr class="hover:bg-gray-50" data-statut="{{ $joueur['statut'] }}" data-licence="{{ $joueur['licence_valide'] }}" data-pcma="{{ $joueur['pcma_a_jour'] }}" data-suspension="{{ $joueur['suspension'] }}" data-nom="{{ strtolower($joueur['nom'] . ' ' . $joueur['prenom']) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $joueur['prenom'] }} {{ $joueur['nom'] }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $joueur['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-mono text-xs bg-gray-100 px-2 py-1 rounded mb-1">
                                    {{ $joueur['licence'] }}
                                </div>
                                @if(isset($joueur['type_licence']))
                                <div class="text-xs text-blue-600 font-medium">
                                    {{ $joueur['type_licence'] }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $joueur['licence_valide'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $joueur['licence_valide'] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    Licence
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $joueur['pcma_a_jour'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $joueur['pcma_a_jour'] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    PCMA
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ !$joueur['suspension'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ !$joueur['suspension'] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    Suspension
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                @if(isset($joueur['age']))
                                <div class="text-gray-600 mb-1">
                                    <span class="font-medium">Âge:</span> {{ $joueur['age'] }} ans
                                </div>
                                @endif
                                @if(isset($joueur['position']))
                                <div class="text-gray-600 mb-1">
                                    <span class="font-medium">Position:</span> {{ $joueur['position'] }}
                                </div>
                                @endif
                                @if(isset($joueur['nationalite']))
                                <div class="text-gray-600">
                                    <span class="font-medium">Nationalité:</span> {{ $joueur['nationalite'] }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $joueur['statut'] === 'Éligible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $joueur['statut'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $joueur['derniere_verification'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="verifierJoueur({{ $joueur['id'] }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button onclick="voirDetails({{ $joueur['id'] }})" 
                                        class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($joueur['statut'] !== 'Éligible')
                                <button onclick="corrigerProbleme({{ $joueur['id'] }})" 
                                        class="text-orange-600 hover:text-orange-900">
                                    <i class="fas fa-wrench"></i>
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

    <!-- Alertes -->
    <div class="mt-8">
        @if($effectif->where('statut', '!=', 'Éligible')->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Attention - Joueurs Inéligibles</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $effectif->where('statut', '!=', 'Éligible')->count() }} joueur(s) ne sont pas éligibles pour les compétitions.</p>
                        <p class="mt-1">Vérifiez les licences, PCMA et suspensions avant les prochains matchs.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function verifierEffectif() {
    // Simulation de la vérification
    alert('Vérification de l\'effectif en cours...');
}

function exporterEffectif() {
    // Simulation de l'export
    alert('Export de l\'effectif en cours...');
}

function verifierJoueur(joueurId) {
    // Simulation de la vérification d'un joueur
    alert('Vérification du joueur ' + joueurId + ' en cours...');
}

function voirDetails(joueurId) {
    // Simulation de l'affichage des détails
    alert('Affichage des détails du joueur ' + joueurId);
}

function corrigerProbleme(joueurId) {
    // Simulation de la correction
    alert('Correction du problème pour le joueur ' + joueurId);
}

// Filtres
document.getElementById('filter-statut').addEventListener('change', function() {
    const statut = this.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!statut || row.dataset.statut === statut) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('search-player').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!search || row.dataset.nom.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/competitions.css') }}">
@endpush
@endsection
