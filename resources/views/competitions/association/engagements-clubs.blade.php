@extends('layouts.app')

@section('title', __('competitions.engagements_clubs_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                {{ __('competitions.engagements_clubs_page.page_title') }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.engagements_clubs_page.subtitle') }}</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('competitions.engagements_clubs_page.back_button') }}
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-blue-600 text-2xl">🏢</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-600">{{ __('competitions.engagements_clubs_page.stat_total_clubs') }}</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $clubs->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-green-600 text-2xl">✅</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-600">{{ __('competitions.engagements_clubs_page.stat_engaged') }}</p>
                    <p class="text-2xl font-bold text-green-900">{{ $clubs->where('statut_engagement_code', 'engaged')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-yellow-600 text-2xl">⏳</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-600">{{ __('competitions.engagements_clubs_page.stat_pending') }}</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $clubs->where('statut_engagement_code', 'not_engaged')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-purple-600 text-2xl">⚽</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-600">{{ __('competitions.engagements_clubs_page.stat_total_matches') }}</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $clubs->sum('total_matches') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions globales -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex flex-wrap gap-4">
            <button onclick="exportEngagements()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                📥 {{ __('competitions.engagements_clubs_page.export_all_button') }}
            </button>
            <button onclick="validateAllEngagements()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                ✅ {{ __('competitions.engagements_clubs_page.validate_all_button') }}
            </button>
            <button onclick="refreshData()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                🔄 {{ __('competitions.engagements_clubs_page.refresh_button') }}
            </button>
            <button onclick="addNewEngagement()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                ➕ {{ __('competitions.engagements_clubs_page.add_button') }}
            </button>
        </div>
    </div>

    <!-- Liste des clubs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.engagements_clubs_page.section_title') }}</h2>
        </div>
        
        @if($clubs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_club') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_competition') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_matches') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_last_activity') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.engagements_clubs_page.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clubs as $club)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-lg">{{ substr($club['nom'], 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $club['nom'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $club['teams_count'] }} {{ __('competitions.engagements_clubs_page.team_unit') }} • {{ $club['competitions_count'] }} {{ __('competitions.engagements_clubs_page.competition_unit') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $club['competition'] }}
                                @if($club['competitions_count'] > 1)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        +{{ $club['competitions_count'] - 1 }} {{ __('competitions.engagements_clubs_page.other_competitions_label') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($club['statut_engagement_code'] === 'engaged')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ __('competitions.engagements_clubs_page.status_engaged_badge') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ __('competitions.engagements_clubs_page.status_pending_badge') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="text-green-600 font-medium">{{ $club['feuilles_validees'] }}</span>
                                    <span class="text-gray-400 mx-1">/</span>
                                    <span class="text-gray-600">{{ $club['feuilles_soumises'] }}</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ __('competitions.engagements_clubs_page.matches_ratio_label') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $club['derniere_activite'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewClubDetails({{ $club['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="{{ __('competitions.engagements_clubs_page.view_button_title') }}">👁️ {{ __('competitions.engagements_clubs_page.view_button') }}</button>
                                    <button onclick="validateEngagement({{ $club['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="{{ __('competitions.engagements_clubs_page.validate_button_title') }}">✅ {{ __('competitions.engagements_clubs_page.validate_button') }}</button>
                                    <button onclick="editEngagement({{ $club['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="{{ __('competitions.engagements_clubs_page.edit_button_title') }}">✏️ {{ __('competitions.engagements_clubs_page.edit_button') }}</button>
                                    <button onclick="exportClubData({{ $club['id'] }})" class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded" title="{{ __('competitions.engagements_clubs_page.export_button_title') }}">📥 {{ __('competitions.engagements_clubs_page.export_button') }}</button>
                                    <button onclick="suspendEngagement({{ $club['id'] }})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded" title="{{ __('competitions.engagements_clubs_page.suspend_button_title') }}">🚫 {{ __('competitions.engagements_clubs_page.suspend_button') }}</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('competitions.engagements_clubs_page.empty_state_title') }}</h3>
                <p class="text-gray-500">{{ __('competitions.engagements_clubs_page.empty_state_text') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
// Actions JavaScript pour les boutons
function exportEngagements() {
    // Créer un formulaire pour l'export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("competitions.association.export-engagements") }}';
    form.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function validateAllEngagements() {
    if (confirm(@json(__('competitions.engagements_clubs_page.js_validate_all_confirm')))) {
        fetch('{{ route("competitions.association.validate-all-engagements") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(@json(__('competitions.engagements_clubs_page.js_error_prefix')) + data.error);
            }
        })
        .catch(error => {
            alert(@json(__('competitions.engagements_clubs_page.js_validate_all_network_error_prefix')) + error);
        });
    }
}

function refreshData() {
    location.reload();
}

function addNewEngagement() {
    alert(@json(__('competitions.engagements_clubs_page.js_add_engagement_unavailable')));
    // Ici on pourrait rediriger vers un formulaire d'ajout
}

function viewClubDetails(clubId) {
    // NOTE (audit factice -> reel, 2026-09) : ce bouton appelait une route
    // de test ("test-club-details") supprimee depuis, ce qui provoquait
    // systematiquement une erreur 404. Utilise maintenant la vraie route
    // authentifiee competitions.association.club-details, deja backee par
    // une methode de controleur reelle (CompetitionController::clubDetails).
    const urlTemplate = @json(route('competitions.association.club-details', ['clubId' => '__CLUB_ID__']));
    fetch(urlTemplate.replace('__CLUB_ID__', clubId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les détails dans une modal ou une nouvelle page
                const club = data.club;
                const clubLabel = @json(__('competitions.engagements_clubs_page.js_view_details_club_label'));
                const teamsLabel = @json(__('competitions.engagements_clubs_page.js_view_details_teams_label'));
                const competitionsLabel = @json(__('competitions.engagements_clubs_page.js_view_details_competitions_label'));
                alert(`${clubLabel} ${club.name}\n${teamsLabel} ${club.teams.length}\n${competitionsLabel} ${club.teams.flatMap(t => t.competitions).length}`);
            } else {
                alert(@json(__('competitions.engagements_clubs_page.js_error_prefix')) + data.error);
            }
        })
        .catch(error => {
            alert(@json(__('competitions.engagements_clubs_page.js_view_details_error_prefix')) + error);
        });
}

function validateEngagement(clubId) {
    if (!confirm(@json(__('competitions.engagements_clubs_page.js_validate_engagement_confirm')))) return;
    fetch(`{{ url('/competitions/association/validate-engagement') }}/${clubId}`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'}
    }).then(r => r.json()).then(data => {
        alert(data.message || data.error);
        if (data.success) window.location.reload();
    }).catch(() => alert(@json(__('competitions.engagements_clubs_page.js_validate_engagement_error'))));
}

function editEngagement(clubId) {
    alert(@json(__('competitions.engagements_clubs_page.js_edit_engagement_unavailable')));
    // Ici on pourrait ouvrir un formulaire de modification
}

function exportClubData(clubId) {
    // NOTE (audit factice -> reel, 2026-09) : meme constat que viewClubDetails()
    // ci-dessus, ce bouton appelait une route de test supprimee
    // ("test-export-club-data"). Utilise maintenant la vraie route
    // authentifiee competitions.association.export-club-data.
    const urlTemplate = @json(route('competitions.association.export-club-data', ['clubId' => '__CLUB_ID__']));
    fetch(urlTemplate.replace('__CLUB_ID__', clubId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `club_${clubId}_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        alert(@json(__('competitions.engagements_clubs_page.js_export_club_data_error_prefix')) + error);
    });
}

function suspendEngagement(clubId) {
    // NOTE (audit factice -> reel, 2026-09) : meme constat que
    // validateEngagement() ci-dessus (route de test supprimee, et methode de
    // controleur reelle sans logique de suspension effective) : message
    // honnete plutot qu'un faux succes.
    if (confirm(@json(__('competitions.engagements_clubs_page.js_suspend_engagement_confirm')))) {
        alert(@json(__('competitions.engagements_clubs_page.js_suspend_engagement_unavailable')));
    }
}
</script>
@endsection
