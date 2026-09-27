@extends('layouts.app')

@section('title', __('competitions.squad_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-check text-green-600 mr-3"></i>
                    {{ __('competitions.squad_page.heading') }}
                </h1>
                <p class="text-gray-600">{{ __('competitions.squad_page.subtitle') }}</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="verifierEffectif()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    {{ __('competitions.squad_page.check_now') }}
                </button>
                <button onclick="exporterEffectif()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    {{ __('competitions.squad_page.export') }}
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.squad_page.stat_eligible') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('statut_code', 'eligible')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.squad_page.stat_ineligible') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('statut_code', '!=', 'eligible')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.squad_page.stat_pcma_expired') }}</p>
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.squad_page.stat_suspended') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $effectif->where('suspension', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">{{ __('competitions.squad_page.filter_by_status') }}</label>
                <select id="filter-statut" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                    <option value="">{{ __('competitions.squad_page.all') }}</option>
                    <option value="eligible">{{ __('competitions.squad_page.eligible_option') }}</option>
                    <option value="ineligible">{{ __('competitions.squad_page.ineligible_option') }}</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">{{ __('competitions.squad_page.checks_label') }}</label>
                <select id="filter-verification" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                    <option value="">{{ __('competitions.squad_page.all_fem') }}</option>
                    <option value="licence">{{ __('competitions.squad_page.license_option') }}</option>
                    <option value="pcma">{{ __('competitions.squad_page.pcma_option') }}</option>
                    <option value="suspension">{{ __('competitions.squad_page.suspension_option') }}</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <input type="text" id="search-player" placeholder="{{ __('competitions.squad_page.search_player_placeholder') }}"
                       class="border border-gray-300 rounded-md px-3 py-1 text-sm">
            </div>
        </div>
    </div>

    <!-- Liste de l'Effectif -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('competitions.squad_page.club_squad') }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_player') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_fifa_license') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_checks') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_fifa_connect') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_last_check') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.squad_page.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($effectif as $joueur)
                    <tr class="hover:bg-gray-50" data-statut="{{ $joueur['eligible'] ? 'eligible' : 'ineligible' }}" data-licence="{{ $joueur['licence_valide'] }}" data-pcma="{{ $joueur['pcma_a_jour'] }}" data-suspension="{{ $joueur['suspension'] }}" data-nom="{{ strtolower($joueur['nom'] . ' ' . $joueur['prenom']) }}">
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
                                    {{ __('competitions.squad_page.license_badge') }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $joueur['pcma_a_jour'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $joueur['pcma_a_jour'] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ __('competitions.squad_page.pcma_badge') }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ !$joueur['suspension'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ !$joueur['suspension'] ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ __('competitions.squad_page.suspension_badge') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                @if(isset($joueur['age']))
                                <div class="text-gray-600 mb-1">
                                    <span class="font-medium">{{ __('competitions.squad_page.age_label') }}</span> {{ $joueur['age'] }} {{ __('competitions.squad_page.age_suffix') }}
                                </div>
                                @endif
                                @if(isset($joueur['position']))
                                <div class="text-gray-600 mb-1">
                                    <span class="font-medium">{{ __('competitions.squad_page.position_label') }}</span> {{ $joueur['position'] }}
                                </div>
                                @endif
                                @if(isset($joueur['nationalite']))
                                <div class="text-gray-600">
                                    <span class="font-medium">{{ __('competitions.squad_page.nationality_label') }}</span> {{ $joueur['nationalite'] }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $joueur['eligible'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                                @if(!$joueur['eligible'])
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
        @if($effectif->where('statut_code', '!=', 'eligible')->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ __('competitions.squad_page.alert_heading') }}</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $effectif->where('statut_code', '!=', 'eligible')->count() }} {{ __('competitions.squad_page.alert_count_suffix') }}</p>
                        <p class="mt-1">{{ __('competitions.squad_page.alert_advice') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// NOTE (audit factice -> reel, 2026-09) : ces 5 fonctions n'appelaient
// aucune route/API reelle (aucun fetch) ; elles affichaient seulement un
// message "en cours..." laissant croire qu'une action se produisait. Un
// homonyme CompetitionController::verifierEffectif() existait cote
// backend, mais restait non relie a aucune route et se limitait a un
// succes fixe sans logique reelle ; il a ete supprime comme code mort.
function verifierEffectif() {
    alert(@json(__('competitions.squad_js.check_all_unavailable')));
}

function exporterEffectif() {
    alert(@json(__('competitions.squad_js.export_unavailable')));
}

function verifierJoueur(joueurId) {
    alert(@json(__('competitions.squad_js.check_player_unavailable', ['id' => '__ID__'])).replace('__ID__', joueurId));
}

function voirDetails(joueurId) {
    alert(@json(__('competitions.squad_js.view_details_unavailable', ['id' => '__ID__'])).replace('__ID__', joueurId));
}

function corrigerProbleme(joueurId) {
    alert(@json(__('competitions.squad_js.fix_issue_unavailable', ['id' => '__ID__'])).replace('__ID__', joueurId));
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
