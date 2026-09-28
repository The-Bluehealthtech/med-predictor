@extends('layouts.app')

@section('title', __('competitions.match_sheets_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('competitions.match_sheets_page.heading') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.match_sheets_page.subtitle') }}</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('competitions.club.calendrier') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('competitions.match_sheets_page.back_to_calendar') }}
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.match_sheets_page.stat_total') }}</p>
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.match_sheets_page.stat_submitted') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut_code', 'submitted')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.match_sheets_page.stat_to_prepare') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut_code', 'draft')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.match_sheets_page.stat_late') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $feuilles->where('statut_code', 'late')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des feuilles de match -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.match_sheets_page.heading') }}</h2>
        </div>
        
        @if($feuilles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_match') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_available_squad') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_selected_squad') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.match_sheets_page.col_actions') }}</th>
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
                                    @if($feuille['statut_code'] === 'submitted')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ {{ __('competitions.match_sheets_page.status_submitted_badge') }}
                                        </span>
                                    @elseif($feuille['statut_code'] === 'draft')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            📝 {{ __('competitions.match_sheets_page.status_to_prepare_badge') }}
                                        </span>
                                    @elseif($feuille['statut_code'] === 'late')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            ⚠️ {{ __('competitions.match_sheets_page.status_late_badge') }}
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
                                        <span class="text-gray-500 ml-1">{{ __('competitions.match_sheets_page.players_suffix') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="font-semibold">{{ $feuille['effectif_selectionne'] }}</span>
                                        <span class="text-gray-500 ml-1">{{ __('competitions.match_sheets_page.players_suffix') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($feuille['statut_code'] === 'draft')
                                            <button onclick="openPreparationModal({{ $feuille['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 border rounded" title="{{ __('competitions.match_sheets_page.prepare_match_sheet_title') }}">
                                                ✏️ Préparer
                                            </button>
                                            <button onclick="openEffectifModal({{ $feuille['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 border rounded" title="{{ __('competitions.match_sheets_page.select_squad_title') }}">
                                                👥 Effectif
                                            </button>
                                        @elseif($feuille['statut_code'] === 'submitted')
                                            <button onclick="viewFeuille({{ $feuille['id'] }})" class="text-purple-600 hover:text-purple-900 px-2 py-1 border rounded" title="{{ __('competitions.match_sheets_page.view_submitted_sheet_title') }}">
                                                👁️ Voir
                                            </button>
                                            <button onclick="editFeuille({{ $feuille['id'] }})" class="text-orange-600 hover:text-orange-900 px-2 py-1 border rounded" title="{{ __('competitions.match_sheets_page.edit_title') }}">
                                                ✏️ Modifier
                                            </button>
                                        @else
                                            <button onclick="viewFeuille({{ $feuille['id'] }})" class="text-purple-600 hover:text-purple-900 px-2 py-1 border rounded" title="Voir la feuille">
                                                👁️ Voir
                                            </button>
                                            <button onclick="editFeuille({{ $feuille['id'] }})" class="text-orange-600 hover:text-orange-900 px-2 py-1 border rounded" title="Modifier la feuille">
                                                ✏️ Modifier
                                            </button>
                                        @endif
                                        <button onclick="downloadPDF({{ $feuille['id'] }})" class="text-gray-600 hover:text-gray-900 px-2 py-1 border rounded" title="{{ __('competitions.match_sheets_page.download_pdf_title') }}">
                                            ⬇️ PDF
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('competitions.match_sheets_page.no_sheet') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('competitions.match_sheets_page.no_sheet_desc') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Prochaines feuilles à préparer -->
    @if($feuilles->where('statut_code', 'draft')->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.match_sheets_page.upcoming_sheets_to_prepare') }}</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($feuilles->where('statut_code', 'draft')->take(3) as $feuille)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-gray-600">{{ __('competitions.match_sheets_page.match_label') }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($feuille['date'])->format('d/m') }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $feuille['match'] }}</h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div><i class="fas fa-users mr-2"></i>{{ $feuille['effectif_disponible'] }} {{ __('competitions.match_sheets_page.available_players_suffix') }}</div>
                                <div><i class="fas fa-user-check mr-2"></i>{{ $feuille['effectif_selectionne'] }} {{ __('competitions.match_sheets_page.selected_players_suffix') }}</div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <button onclick="openPreparationModal({{ $feuille['id'] }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>{{ __('competitions.match_sheets_page.prepare') }}
                                </button>
                                <button onclick="openEffectifModal({{ $feuille['id'] }})" class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                    <i class="fas fa-users mr-1"></i>{{ __('competitions.match_sheets_page.squad') }}
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
            <i class="fas fa-info-circle mr-2"></i>{{ __('competitions.match_sheets_page.usage_guide') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">{{ __('competitions.match_sheets_page.preparation_guide_heading') }}</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• {{ __('competitions.match_sheets_page.preparation_step_1') }}</li>
                    <li>• {{ __('competitions.match_sheets_page.preparation_step_2') }}</li>
                    <li>• {{ __('competitions.match_sheets_page.preparation_step_3') }}</li>
                    <li>• {{ __('competitions.match_sheets_page.preparation_step_4') }}</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">{{ __('competitions.match_sheets_page.status_guide_heading') }}</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• <span class="font-semibold">{{ __('competitions.match_sheets_page.status_to_prepare_badge') }}</span> : {{ __('competitions.match_sheets_page.status_guide_to_prepare') }}</li>
                    <li>• <span class="font-semibold">{{ __('competitions.match_sheets_page.status_submitted_badge') }}</span> : {{ __('competitions.match_sheets_page.status_guide_submitted') }}</li>
                    <li>• <span class="font-semibold">{{ __('competitions.match_sheets_page.status_late_badge') }}</span> : {{ __('competitions.match_sheets_page.status_guide_late') }}</li>
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
                <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.match_sheets_page.prepare_modal_title') }}</h3>
                <button onclick="closePreparationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="preparationContent">
                <!-- Contenu dynamique -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closePreparationModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    {{ __('competitions.match_sheets_page.cancel') }}
                </button>
                <button onclick="savePreparation()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    {{ __('competitions.match_sheets_page.save') }}
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
                <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.match_sheets_page.select_squad_modal_title') }}</h3>
                <button onclick="closeEffectifModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="effectifContent">
                <!-- Contenu dynamique -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeEffectifModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    {{ __('competitions.match_sheets_page.cancel') }}
                </button>
                <button onclick="saveEffectif()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    {{ __('competitions.match_sheets_page.validate_squad') }}
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

    // NOTE (audit factice -> reel, 2026-09) : ce modal affichait des
    // listes de joueurs entierement fictives ("Jean Dupont", "Pierre
    // Martin", "Ahmed Ben Ali") sans lien avec l'effectif reel du club,
    // et un bouton "Sauvegarder" qui affichait un succes sans rien
    // enregistrer. Remplace par un etat honnete : aucune fonctionnalite
    // de preparation detaillee de la feuille n'est encore connectee a une
    // route reelle.
    content.innerHTML = `
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-blue-800">${@json(__('competitions.match_sheets_page.js_sheet_number'))} #${feuilleId}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-yellow-800 text-sm">${@json(__('competitions.match_sheets_page.js_preparation_unavailable'))}</p>
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

    // NOTE (audit factice -> reel, 2026-09) : ce modal affichait une liste
    // de joueurs entierement fictive (memes noms que le modal de
    // preparation) avec des cases pre-cochees et un bouton "Valider
    // l'effectif" qui affichait un succes sans rien enregistrer. Remplace
    // par un etat honnete : la selection detaillee de l'effectif n'est
    // pas encore connectee a une route reelle.
    content.innerHTML = `
        <div class="space-y-4">
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-yellow-800 text-sm">${@json(__('competitions.match_sheets_page.js_roster_selection_unavailable'))}</p>
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
    alert(@json(__('competitions.match_sheets_page.js_save_unavailable')));
    closePreparationModal();
}

// Fonction pour sauvegarder l'effectif
function saveEffectif() {
    alert(@json(__('competitions.match_sheets_page.js_save_unavailable')));
    closeEffectifModal();
}

// Fonction pour voir une feuille soumise
function viewFeuille(feuilleId) {
    window.location.href = '{{ url('/competitions/club/feuille-match') }}/' + feuilleId;
}

// Fonction pour modifier une feuille : ouvrir la feuille réelle permet
// d'utiliser ses contrôles d'édition et de soumission.
function editFeuille(feuilleId) {
    window.location.href = '{{ url('/competitions/club/feuille-match') }}/' + feuilleId + '?edit=1';
}

// Téléchargement PDF via la feuille réelle (le contrôleur applique le rendu
// officiel et le navigateur peut l'imprimer/enregistrer en PDF).
function downloadPDF(feuilleId) {
    window.location.href = '{{ url('/competitions/club/feuille-match') }}/' + feuilleId + '?format=pdf';
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
