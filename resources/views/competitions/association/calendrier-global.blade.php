@extends('layouts.app')

@section('title', __('competitions.calendrier_global_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                {{ __('competitions.calendrier_global_page.heading') }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.calendrier_global_page.subtitle') }}</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('competitions.calendrier_global_page.back_to_modules') }}
            </a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-download mr-2"></i>{{ __('competitions.calendrier_global_page.export') }}
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('competitions.calendrier_global_page.filters_heading') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.calendrier_global_page.competition_label') }}</label>
                <select id="calendarCompetitionFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('competitions.calendrier_global_page.all_competitions') }}</option>
                    @foreach($competitionsList as $competitionName)
                        <option value="{{ $competitionName }}">{{ $competitionName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.calendrier_global_page.period_label') }}</label>
                <select id="calendarPeriodFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les périodes</option>
                    <option value="saison">Cette Saison</option>
                    <option value="semaine">{{ __('competitions.calendrier_global_page.this_week') }}</option>
                    <option value="mois">{{ __('competitions.calendrier_global_page.this_month') }}</option>
                    <option value="trimestre">{{ __('competitions.calendrier_global_page.this_quarter') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.calendrier_global_page.status_label') }}</label>
                <select id="calendarStatusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('competitions.calendrier_global_page.all_statuses') }}</option>
                    <option value="scheduled">{{ __('competitions.match_status_label.scheduled') }}</option>
                    <option value="postponed">{{ __('competitions.match_status_label.postponed') }}</option>
                    <option value="completed">{{ __('competitions.match_status_label.completed') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="calendarApplyFilters" type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>{{ __('competitions.calendrier_global_page.filter') }}
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.calendrier_global_page.scheduled_matches') }}</p>
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
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.calendrier_global_page.completed_matches') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $matchsTerminesCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.calendrier_global_page.postponed_matches') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $matchsReportesCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.calendrier_global_page.active_competitions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $competitionsActivesCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier des Matchs -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.calendrier_global_page.matches_calendar') }}</h2>
        </div>
        
        @if(count($matchs) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_date_time') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_competition') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_teams') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_venue') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_referee') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.calendrier_global_page.table_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($matchs as $match)
                            <tr class="hover:bg-gray-50" data-match-id="{{ $match['id'] }}" data-competition="{{ $match['competition'] }}" data-status="{{ $match['statut_code'] }}" data-date="{{ $match['date'] }}">
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
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'postponed' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'suspended' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statusColor = $statusColors[$match['statut_code']] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $match['statut'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewMatch({{ $match['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="{{ __('competitions.calendrier_global_page.view_details_title') }}">👁️ {{ __('competitions.calendrier_global_page.view') }}</button>
                                        @if($match['reprogrammable'])
                                            <button onclick="rescheduleMatch({{ $match['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="{{ __('competitions.calendrier_global_page.reschedule_title') }}">📅 {{ __('competitions.calendrier_global_page.reschedule') }}</button>
                                        @endif
                                        <button onclick="editMatch({{ $match['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="{{ __('competitions.calendrier_global_page.edit_title') }}">✏️ {{ __('competitions.calendrier_global_page.edit') }}</button>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('competitions.calendrier_global_page.no_match_scheduled') }}</h3>
                <p class="text-gray-500 mb-6">{{ __('competitions.calendrier_global_page.no_match_scheduled_text') }}</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>{{ __('competitions.calendrier_global_page.schedule_a_match') }}
                </button>
            </div>
        @endif
    </div>

    <!-- Légende -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('competitions.calendrier_global_page.status_legend_heading') }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-3">{{ __('competitions.match_status_label.scheduled') }}</span>
                <span class="text-sm text-gray-600">{{ __('competitions.calendrier_global_page.legend_scheduled') }}</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-3">{{ __('competitions.match_status_label.postponed') }}</span>
                <span class="text-sm text-gray-600">{{ __('competitions.calendrier_global_page.legend_postponed') }}</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-3">{{ __('competitions.match_status_label.completed') }}</span>
                <span class="text-sm text-gray-600">{{ __('competitions.calendrier_global_page.legend_completed') }}</span>
            </div>
            <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mr-3">{{ __('competitions.match_status_label.cancelled') }}</span>
                <span class="text-sm text-gray-600">{{ __('competitions.calendrier_global_page.legend_cancelled') }}</span>
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
                    👁️ {{ __('competitions.calendrier_global_page.match_details') }}
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
                    📅 {{ __('competitions.calendrier_global_page.reschedule_match') }}
                </h3>
                <button onclick="closeRescheduleMatchModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="rescheduleMatchForm">
                <input type="hidden" id="rescheduleMatchId" name="match_id">
                
                <div class="mb-4">
                    <label for="rescheduleDate" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.new_date') }}
                    </label>
                    <input type="date" id="rescheduleDate" name="date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="rescheduleTime" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.new_time') }}
                    </label>
                    <input type="time" id="rescheduleTime" name="time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="rescheduleVenue" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.new_venue') }}
                    </label>
                    <input type="text" id="rescheduleVenue" name="venue" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="{{ __('competitions.calendrier_global_page.venue_placeholder') }}">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRescheduleMatchModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        {{ __('competitions.calendrier_global_page.cancel') }}
                    </button>
                    <button type="button" onclick="saveReschedule()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 {{ __('competitions.calendrier_global_page.reschedule') }}
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
                    ✏️ {{ __('competitions.calendrier_global_page.edit_match') }}
                </h3>
                <button onclick="closeEditMatchModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editMatchForm">
                <input type="hidden" id="editMatchId" name="match_id">
                
                <div class="mb-4">
                    <label for="editHomeScore" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.home_score') }}
                    </label>
                    <input type="number" id="editHomeScore" name="home_score" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editAwayScore" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.away_score') }}
                    </label>
                    <input type="number" id="editAwayScore" name="away_score" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.calendrier_global_page.status_label') }}
                    </label>
                    <select id="editStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="scheduled">{{ __('competitions.match_status_label.scheduled') }}</option>
                        <option value="in_progress">{{ __('competitions.match_status_label.in_progress') }}</option>
                        <option value="completed">{{ __('competitions.match_status_label.completed') }}</option>
                        <option value="postponed">{{ __('competitions.match_status_label.postponed') }}</option>
                        <option value="cancelled">{{ __('competitions.match_status_label.cancelled') }}</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditMatchModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        {{ __('competitions.calendrier_global_page.cancel') }}
                    </button>
                    <button type="button" onclick="saveMatchChanges()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 {{ __('competitions.calendrier_global_page.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyCalendarFilters() {
    const competition = document.getElementById('calendarCompetitionFilter').value;
    const status = document.getElementById('calendarStatusFilter').value;
    const period = document.getElementById('calendarPeriodFilter').value;
    const now = new Date();
    const limits = { semaine: 7, mois: 31, trimestre: 92 };
    const seasonStart = new Date(now.getFullYear(), 8, 1);
    if (now < seasonStart) seasonStart.setFullYear(seasonStart.getFullYear() - 1);
    const seasonEnd = new Date(seasonStart.getFullYear() + 1, 7, 31, 23, 59, 59);
    document.querySelectorAll('tbody tr[data-match-id]').forEach(row => {
        const date = row.dataset.date ? new Date(row.dataset.date + 'T00:00:00') : null;
        const periodOk = !period || !date
            || (period === 'saison' ? (date >= seasonStart && date <= seasonEnd)
                : ((date - now) / 86400000 >= -1 && (date - now) / 86400000 <= limits[period]));
        const ok = (!competition || row.dataset.competition === competition)
            && (!status || row.dataset.status === status)
            && periodOk;
        row.hidden = !ok;
    });
}
document.getElementById('calendarApplyFilters')?.addEventListener('click', applyCalendarFilters);
['calendarCompetitionFilter','calendarPeriodFilter','calendarStatusFilter'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', applyCalendarFilters);
});

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
                    <label class="block text-sm font-medium text-gray-700">{{ __('competitions.calendrier_global_page.competition_label') }}</label>
                    <p class="text-lg">${competition}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('competitions.calendrier_global_page.match_label') }}</label>
                    <p class="text-lg">${match}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('competitions.calendrier_global_page.table_venue') }}</label>
                    <p class="text-lg">${venue}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('competitions.calendrier_global_page.date_label') }}</label>
                    <p class="text-lg">${date}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('competitions.calendrier_global_page.table_status') }}</label>
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
        alert(@json(__('competitions.calendrier_global_page.js_missing_date_time')));
        return;
    }
    
    console.log('Reprogrammation:', { matchId, date, time, venue });
    
    // NOTE (audit factice -> reel, 2026-09) : aucune route/backend n'existe
    // pour reprogrammer un match depuis cette page (aucun appel reseau
    // n'etait effectue, seul un message de succes factice etait affiche
    // puis la page etait rechargee sans aucun changement reel).
    alert(@json(__('competitions.calendrier_global_page.js_reschedule_unavailable')));
    closeRescheduleMatchModal();
}

function saveMatchChanges() {
    const matchId = document.getElementById('editMatchId').value;
    const homeScore = document.getElementById('editHomeScore').value;
    const awayScore = document.getElementById('editAwayScore').value;
    const status = document.getElementById('editStatus').value;
    
    console.log('Modifications:', { matchId, homeScore, awayScore, status });
    
    // NOTE (audit factice -> reel, 2026-09) : aucune route/backend n'existe
    // pour enregistrer ces modifications depuis cette page (aucun appel
    // reseau n'etait effectue, seul un message de succes factice etait
    // affiche puis la page etait rechargee sans aucun changement reel).
    alert(@json(__('competitions.calendrier_global_page.js_edit_unavailable')));
    closeEditMatchModal();
}
</script>
@endsection

