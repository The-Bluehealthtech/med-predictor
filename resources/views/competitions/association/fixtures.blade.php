@extends('layouts.app')

@section('title', __('competitions.association_fixtures_page.page_title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        📅 {{ __('competitions.association_fixtures_page.page_title') }}
                    </h1>
                    <p class="text-blue-200">
                        {{ __('competitions.association_fixtures_page.subtitle') }}
                    </p>
                    @if($availableAssociations->isNotEmpty())
                        <form method="GET" class="mt-3 flex items-center gap-2">
                            <label for="association_id" class="text-sm text-blue-100">Association</label>
                            <select id="association_id" name="association_id" onchange="this.form.submit()" class="rounded px-3 py-2 text-gray-900">
                                @foreach($availableAssociations as $availableAssociation)
                                    <option value="{{ $availableAssociation->id }}" @selected($association->id == $availableAssociation->id)>{{ $availableAssociation->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('competitions.association.supervision') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ← {{ __('competitions.association_fixtures_page.back_button') }}
                    </a>
                    <button onclick="exportFixtures()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        📥 {{ __('competitions.association_fixtures_page.export_button') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-6 border border-white/20">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="searchInput"
                           placeholder="{{ __('competitions.association_fixtures_page.search_placeholder') }}"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select id="competitionFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('competitions.association_fixtures_page.all_competitions_option') }}</option>
                        @foreach($competitions as $competition)
                            <option value="{{ $competition->name }}">{{ $competition->name }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('competitions.fixtures_page.all_statuses') }}</option>
                        <option value="{{ __('competitions.fixtures_page.status_completed') }}">{{ __('competitions.fixtures_page.status_completed') }}</option>
                        <option value="{{ __('competitions.fixtures_page.status_upcoming') }}">{{ __('competitions.fixtures_page.status_upcoming') }}</option>
                    </select>
                    <select id="journeeFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('competitions.fixtures_page.all_matchdays') }}</option>
                        @for($i = 1; $i <= 30; $i++)
                            <option value="{{ $i }}">{{ __('competitions.fixtures_page.matchday_label') }} {{ $i }}</option>
                        @endfor
                    </select>
                    <button onclick="clearFilters()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ❌ {{ __('competitions.association_fixtures_page.clear_filters_button') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des Fixtures -->
        @if(isset($paginatedFixtures) && $paginatedFixtures->count() > 0)
            @foreach($paginatedFixtures as $journee)
                <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden mb-6" data-competition="{{ $journee['competition'] }}">
                    <div class="p-6 border-b border-white/20 bg-gradient-to-r from-blue-600/20 to-purple-600/20">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                📅 {{ __('competitions.fixtures_page.matchday_label') }} {{ $journee['journee'] }} - {{ $journee['competition'] }}
                            </h3>
                            <div class="text-sm text-gray-300">
                                🕐 {{ $journee['date']->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_time') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_match') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_stadium') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_result') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_referee') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.association_fixtures_page.col_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($journee['matchs'] as $match)
                                    <tr class="hover:bg-white/5 transition-colors cursor-pointer" onclick="viewMatchDetails({{ $match['id'] }})">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-clock mr-2 text-blue-300"></i>
                                                {{ $match['heure'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm font-medium text-white">
                                                    {{ $match['domicile'] }}
                                                </div>
                                                <div class="text-blue-300">vs</div>
                                                <div class="text-sm font-medium text-white">
                                                    {{ $match['exterieur'] }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <i class="fas fa-map-marker-alt mr-2 text-orange-300"></i>
                                            {{ $match['stade'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($match['statut_code'] === 'completed')
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-lg font-bold text-white">{{ $match['buts_domicile'] }}</span>
                                                    <span class="text-blue-300">-</span>
                                                    <span class="text-lg font-bold text-white">{{ $match['buts_exterieur'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <i class="fas fa-whistle mr-2 text-purple-300"></i>
                                            {{ $match['arbitre_principal'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                                        <div class="flex space-x-2">
                                                <button onclick="event.stopPropagation(); viewMatchDetails({{ $match['id'] }})" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="{{ __('competitions.association_fixtures_page.details_button_title') }}">
                                                    👁️ {{ __('competitions.association_fixtures_page.details_button') }}
                                                </button>
                                                <button onclick="event.stopPropagation(); viewMatchSheet({{ $match['id'] }})" 
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="{{ __('competitions.association_fixtures_page.match_sheet_button_title') }}">
                                                    📋 {{ __('competitions.association_fixtures_page.match_sheet_button') }}
                                                </button>
                                                <button onclick="event.stopPropagation(); editMatch({{ $match['id'] }})" 
                                                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="{{ __('competitions.association_fixtures_page.edit_button_title') }}">
                                                    ✏️ {{ __('competitions.association_fixtures_page.edit_button') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-300">
                        {{ __('competitions.association_fixtures_page.pagination_showing', ['first' => $paginatedFixtures->firstItem(), 'last' => $paginatedFixtures->lastItem(), 'total' => $paginatedFixtures->total()]) }}
                    </div>
                    <div class="flex space-x-2">
                        {{ $paginatedFixtures->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-12 text-center">
                <div class="bg-gray-500/20 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-calendar text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">{{ __('competitions.association_fixtures_page.empty_state_title') }}</h3>
                <p class="text-blue-200 mb-6">
                    {{ __('competitions.association_fixtures_page.empty_state_text') }}
                </p>
            </div>
        @endif
    </div>
</div>

<script>
const FIXTURES_STATUS_COMPLETED_LABEL = @json(__('competitions.fixtures_page.status_completed'));
const FIXTURES_STATUS_UPCOMING_LABEL = @json(__('competitions.fixtures_page.status_upcoming'));
const FIXTURES_HOME_TEAM_FALLBACK = @json(__('competitions.fixtures_page.home_club_fallback'));
const FIXTURES_AWAY_TEAM_FALLBACK = @json(__('competitions.fixtures_page.away_club_fallback'));
const FIXTURES_UNKNOWN_DATE_FALLBACK = @json(__('competitions.association_fixtures_page.fallback_unknown_date'));
const FIXTURES_UNKNOWN_TIME_FALLBACK = @json(__('competitions.association_fixtures_page.fallback_unknown_time'));
const FIXTURES_UNKNOWN_STADIUM_FALLBACK = @json(__('competitions.association_fixtures_page.fallback_unknown_stadium'));
const FIXTURES_UNKNOWN_REFEREE_FALLBACK = @json(__('competitions.association_fixtures_page.fallback_unknown_referee'));
const FIXTURES_MATCHDAY_LABEL = @json(__('competitions.fixtures_page.matchday_label'));

// Fonctions pour les boutons d\'action
function viewMatchDetails(matchId) {
    console.log('viewMatchDetails called with ID:', matchId);
    showMatchModal(matchId, 'details');
}

function viewMatchSheet(matchId) {
    console.log('viewMatchSheet called with ID:', matchId);
    // Rediriger vers la vraie feuille de match
    // NOTE (audit factice -> reel, 2026-09) : 'test.feuille-match' n'est
    // le nom d'aucune route enregistree (voir routes/web.php) - route()
    // levait donc une RouteNotFoundException au rendu de CETTE PAGE, a
    // chaque chargement (pas seulement au clic sur le bouton), quel que
    // soit le contenu de $paginatedFixtures. Remplace par la vraie route
    // nommee cote association.
    const url = `{{ route('competitions.association.feuille-match', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', matchId);
    console.log('Redirecting to:', url);
    window.location.href = url;
}

function editMatch(matchId) {
    console.log('editMatch called with ID:', matchId);
    showMatchModal(matchId, 'edit');
}

// NOTE (audit factice -> reel, 2026-09) : cette fonction telechargeait un
// CSV entierement invente ("Club A", "Club B", "Arbitre 1"...) quel que
// soit le contenu reel de la page, puis affichait un faux succes. Le CSV
// est desormais construit a partir des vraies lignes affichees a l'ecran
// (memes tableaux que ceux rendus par le serveur depuis les fixtures reelles).
function exportFixtures() {
    const tables = document.querySelectorAll('table');
    if (!tables.length) {
        showNotification(@json(__('competitions.association_fixtures_page.js_export_no_matches')), 'error');
        return;
    }

    let csvContent = [
        @json(__('competitions.association_fixtures_page.csv_col_match')),
        @json(__('competitions.association_fixtures_page.csv_col_time')),
        @json(__('competitions.association_fixtures_page.csv_col_stadium')),
        @json(__('competitions.association_fixtures_page.csv_col_result')),
        @json(__('competitions.association_fixtures_page.csv_col_referee'))
    ].join(',') + '\n';
    let rowCount = 0;

    tables.forEach(table => {
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 5) return;

            const heure = cells[0].textContent.trim().replace(/\s+/g, ' ');
            const match = cells[1].textContent.trim().replace(/\s+/g, ' ');
            const stade = cells[2].textContent.trim().replace(/\s+/g, ' ');
            const resultat = cells[3].textContent.trim().replace(/\s+/g, ' ');
            const arbitre = cells[4].textContent.trim().replace(/\s+/g, ' ');

            csvContent += `"${match}","${heure}","${stade}","${resultat}","${arbitre}"\n`;
            rowCount++;
        });
    });

    if (rowCount === 0) {
        showNotification(@json(__('competitions.association_fixtures_page.js_export_no_matches')), 'error');
        return;
    }

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'fixtures_competitions.csv';
    a.click();
    window.URL.revokeObjectURL(url);

    showNotification(@json(__('competitions.association_fixtures_page.js_export_success')), 'success');
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('competitionFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('journeeFilter').value = '';
    filterTable();
    showNotification(@json(__('competitions.association_fixtures_page.js_filters_cleared')), 'success');
}

// Fonction de recherche et filtrage
function filterTable() {
    console.log('filterTable called'); // Debug log
    
    const searchInput = document.getElementById('searchInput');
    const competitionFilter = document.getElementById('competitionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const journeeFilter = document.getElementById('journeeFilter');
    
    if (!searchInput || !competitionFilter || !statusFilter || !journeeFilter) {
        console.error('Search elements not found');
        return;
    }
    
    const searchTerm = searchInput.value.toLowerCase();
    const competitionValue = competitionFilter.value;
    const statusValue = statusFilter.value;
    const journeeValue = journeeFilter.value;
    
    console.log('Search term:', searchTerm, 'Competition:', competitionValue, 'Status:', statusValue);
    
    // Chercher dans tous les tableaux de toutes les journées
    const allRows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    let totalCount = allRows.length;
    
    console.log('Total rows found:', totalCount);
    
    allRows.forEach(row => {
        const matchText = row.textContent.toLowerCase();
        const statusCell = row.querySelector('td:nth-child(4)'); // Le statut est maintenant en 4ème position
        const status = statusCell ? statusCell.textContent.trim() : '';
        
        // Recherche dans le texte de la ligne
        const matchesSearch = !searchTerm || matchText.includes(searchTerm.toLowerCase());
        
        // Trouver la carte de journée parente (utilisee pour le filtre competition ET journée)
        const journeeCard = row.closest('.bg-white\\/10');

        // Filtre par compétition - NOTE (audit factice -> reel, 2026-09) : ce filtre
        // acceptait auparavant systematiquement tous les matchs ("ils sont tous du
        // meme championnat"), ce qui n'est vrai que si l'association ne gere qu'une
        // seule competition. Chaque carte de journée porte maintenant le vrai nom de
        // sa competition (data-competition), issu de
        // CompetitionController::getFixturesFromDatabase.
        let matchesCompetition = true;
        if (competitionValue) {
            matchesCompetition = journeeCard ? journeeCard.dataset.competition === competitionValue : false;
        }
        
        // Filtre par statut - vérifier si le statut correspond
        const matchesStatus = !statusValue || status.toLowerCase().includes(statusValue.toLowerCase());
        
        // Filtre par journée - vérifier si la ligne appartient à la journée sélectionnée
        let matchesJournee = true;
        if (journeeValue) {
            if (journeeCard) {
                const journeeTitle = journeeCard.querySelector('h3');
                if (journeeTitle) {
                    const journeeText = journeeTitle.textContent;
                    matchesJournee = journeeText.includes(`${FIXTURES_MATCHDAY_LABEL} ${journeeValue}`);
                }
            }
        }
        
        console.log(`Match: ${matchText.substring(0, 50)}... | Status: ${status} | Matches status: ${matchesStatus} | Matches journee: ${matchesJournee}`);
        
        if (matchesSearch && matchesCompetition && matchesStatus && matchesJournee) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Afficher le nombre de résultats
    showNotification(@json(__('competitions.association_fixtures_page.js_filter_results')).replace(':count', visibleCount).replace(':total', totalCount), 'success');
}

// Fonction pour afficher une modal de match
function showMatchModal(matchId, type) {
    // Trouver la ligne du match dans le tableau pour récupérer les vraies données
    const matchRow = document.querySelector(`tr[onclick*="${matchId}"]`);
    
    let matchData = {
        id: matchId,
        domicile: FIXTURES_HOME_TEAM_FALLBACK,
        exterieur: FIXTURES_AWAY_TEAM_FALLBACK,
        date: FIXTURES_UNKNOWN_DATE_FALLBACK,
        heure: FIXTURES_UNKNOWN_TIME_FALLBACK,
        stade: FIXTURES_UNKNOWN_STADIUM_FALLBACK,
        statut: FIXTURES_STATUS_UPCOMING_LABEL,
        arbitre: FIXTURES_UNKNOWN_REFEREE_FALLBACK,
        score: '-'
    };
    
    if (matchRow) {
        const cells = matchRow.querySelectorAll('td');
        if (cells.length >= 6) {
            // Extraire les données de la ligne
            const matchCell = cells[1]; // Cellule du match
            const dateCell = cells[0]; // Cellule de l'heure
            const stadeCell = cells[2]; // Cellule du stade
            const resultatCell = cells[3]; // Cellule du résultat/score
            const arbitreCell = cells[4]; // Cellule de l'arbitre
            
            if (matchCell) {
                const matchText = matchCell.textContent.trim();
                const teams = matchText.split('vs');
                if (teams.length === 2) {
                    matchData.domicile = teams[0].trim();
                    matchData.exterieur = teams[1].trim();
                }
            }
            
            if (dateCell) {
                matchData.heure = dateCell.textContent.trim();
            }
            
            if (stadeCell) {
                matchData.stade = stadeCell.textContent.trim();
            }
            
            if (resultatCell) {
                const resultatText = resultatCell.textContent.trim();
                if (resultatText !== '-') {
                    // Nettoyer le score en supprimant les espaces et caractères invisibles
                    matchData.score = resultatText.replace(/\s+/g, ' ').trim();
                    matchData.statut = FIXTURES_STATUS_COMPLETED_LABEL;
                } else {
                    matchData.statut = FIXTURES_STATUS_UPCOMING_LABEL;
                }
            }
            
            if (arbitreCell) {
                matchData.arbitre = arbitreCell.textContent.trim();
            }
        }
    }
    
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const title = type === 'edit' ? @json(__('competitions.association_fixtures_page.modal_edit_title')) : @json(__('competitions.association_fixtures_page.modal_details_title'));
    const actionButton = type === 'edit' ? 
        '<button onclick="saveMatch(' + matchId + ')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">' + @json(__('competitions.association_fixtures_page.modal_save_button')) + '</button>' :
        '<button onclick="viewMatchSheet(' + matchId + ')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">' + @json(__('competitions.association_fixtures_page.modal_view_sheet_button')) + '</button>';
    
    const statutClass = matchData.statut.includes(FIXTURES_STATUS_COMPLETED_LABEL) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">${title}</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_id_label'))}</label>
                        <p class="text-lg font-semibold text-blue-600">${matchData.id}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.fixtures_page.status_label'))}</label>
                        <span class="inline-block px-3 py-1 rounded-full text-sm ${statutClass}">${matchData.statut}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_match_label'))}</label>
                    <p class="text-lg font-semibold">${matchData.domicile} vs ${matchData.exterieur}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_time_label'))}</label>
                    ${type === 'edit' ? '<input id="edit-match-time" type="text" class="border rounded px-2 py-1 w-full" value="' + (matchData.heure || '') + '">' : '<p class="text-lg">' + matchData.heure + '</p>'}
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_stadium_label'))}</label>
                    ${type === 'edit' ? '<input id="edit-match-venue" class="border rounded px-2 py-1 w-full" value="' + (matchData.stade || '') + '">' : '<p class="text-lg">' + matchData.stade + '</p>'}
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_referee_label'))}</label>
                    <p class="text-lg">${matchData.arbitre}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${@json(__('competitions.association_fixtures_page.modal_score_label'))}</label>
                    ${type === 'edit' ? '<input id="edit-match-score" class="border rounded px-2 py-1 w-32" value="' + (matchData.score || '') + '">' : '<p class="text-2xl font-bold text-blue-600">' + matchData.score + '</p>'}
                </div>
                <div class="pt-4 border-t">
                    <div class="flex justify-end space-x-2">
                        ${actionButton}
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            {{ __('competitions.association_fixtures_page.modal_close_button') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal) {
        modal.remove();
    }
}

// NOTE (audit factice -> reel, 2026-09) : le modal "Modifier le Match" (type
// edit) affiche exactement les memes champs en lecture seule que le modal
// "Details du Match" (aucun champ de formulaire reel n'est presente) ; le
// bouton Sauvegarder affichait donc un faux succes sans qu'il y ait quoi que
// ce soit a enregistrer. Aucune edition reelle des matchs n'est disponible
// depuis cette page pour le moment.
function saveMatch(matchId) {
    const modal = document.querySelector('.fixed.inset-0:last-of-type');
    const venue = modal?.querySelector('#edit-match-venue')?.value || '';
    const score = modal?.querySelector('#edit-match-score')?.value || '';
    const scores = score.match(/(\\d+)\\s*-\\s*(\\d+)/);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch('{{ url('/competitions/association/match') }}/' + matchId + '/update', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
        body: JSON.stringify({
            venue,
            home_score: scores ? Number(scores[1]) : null,
            away_score: scores ? Number(scores[2]) : null
        })
    }).then(r => r.json()).then(data => {
        showNotification(data.message || data.error, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 600);
    }).catch(() => showNotification('Erreur lors de la modification', 'error'));
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des effets de survol aux boutons
    const buttons = document.querySelectorAll('button[onclick]');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Ajouter les événements de recherche
    const searchInput = document.getElementById('searchInput');
    const competitionFilter = document.getElementById('competitionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const journeeFilter = document.getElementById('journeeFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (competitionFilter) {
        competitionFilter.addEventListener('change', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    if (journeeFilter) {
        journeeFilter.addEventListener('change', filterTable);
    }
});
</script>
@endsection
