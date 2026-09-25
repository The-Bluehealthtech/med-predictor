@extends('layouts.app')

@section('title', 'Discipline & Notifications - Club')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Discipline & Notifications</h1>
            <p class="text-gray-600 mt-2">Suivi des cartons, suspensions et amendes</p>
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
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cartons Jaunes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('type', 'Carton Jaune')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-ban text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cartons Rouges</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('type', 'Carton Rouge')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Suspensions Actives</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('statut', 'like', '%Suspendu%')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
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
            <h2 class="text-lg font-semibold text-gray-900">Historique des Sanctions</h2>
        </div>
        
        @if($sanctions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amende</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sanctions as $sanction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-600">{{ substr($sanction['joueur'], 0, 2) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $sanction['joueur'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['match'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($sanction['date'])->format('d/m/Y') }}
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
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $sanction['type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $sanction['motif'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(str_contains($sanction['statut'], 'Suspendu'))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ⏰ {{ $sanction['statut'] }}
                                        </span>
                                    @elseif($sanction['statut'] === 'Validé')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ {{ $sanction['statut'] }}
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
                                        <button onclick="viewSanction({{ $sanction['id'] }})" class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($sanction['amende'] > 0)
                                            <button onclick="payAmende({{ $sanction['id'] }})" class="text-green-600 hover:text-green-900" title="Payer amende">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                        @endif
                                        <button onclick="downloadSanction({{ $sanction['id'] }})" class="text-gray-600 hover:text-gray-900" title="Télécharger">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune sanction</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune sanction n'est enregistrée pour votre club.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Notifications importantes -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Notifications Importantes</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-yellow-800">Suspension active</h4>
                        <p class="text-sm text-yellow-700 mt-1">Pierre Martin est suspendu pour 3 matchs suite à un carton rouge.</p>
                        <p class="text-xs text-yellow-600 mt-1">Prochaine éligibilité : 15/09/2024</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-euro-sign text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-red-800">Amende en attente</h4>
                        <p class="text-sm text-red-700 mt-1">Une amende de 150€ est due suite au carton rouge de Pierre Martin.</p>
                        <button class="text-xs text-red-600 hover:text-red-800 underline mt-1">Payer maintenant</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide de discipline -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>Guide de discipline
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Types de sanctions :</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• <span class="font-semibold">Carton Jaune</span> : Avertissement</li>
                    <li>• <span class="font-semibold">Carton Rouge</span> : Exclusion + suspension</li>
                    <li>• <span class="font-semibold">Amende</span> : Sanction financière</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Procédures :</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Vérifier les suspensions avant chaque match</li>
                    <li>• Payer les amendes dans les délais</li>
                    <li>• Contester si nécessaire</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour voir les détails d'une sanction
function viewSanction(sanctionId) {
    alert('Affichage des détails de la sanction #' + sanctionId);
    // Ici vous pourriez ouvrir un modal ou rediriger vers une page
}

// Fonction pour payer une amende
function payAmende(sanctionId) {
    if (confirm('Voulez-vous procéder au paiement de cette amende ?')) {
        alert('Redirection vers le système de paiement pour la sanction #' + sanctionId);
        // Ici vous pourriez rediriger vers un système de paiement
    }
}

// Fonction pour télécharger une sanction
function downloadSanction(sanctionId) {
    alert('Téléchargement de la sanction #' + sanctionId);
    // Ici vous pourriez déclencher le téléchargement d'un PDF
}
</script>
@endsection
