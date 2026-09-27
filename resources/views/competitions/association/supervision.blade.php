@extends('layouts.app')

@section('title', __('competitions.supervision_page.page_title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-eye mr-3"></i>
                        {{ __('competitions.supervision_page.heading') }}
                    </h1>
                    <p class="text-blue-200">
                        {{ __('competitions.supervision_page.subtitle') }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.competitions.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('competitions.supervision_page.back') }}
                    </a>
                    <button onclick="createNewCompetition()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('competitions.supervision_page.new_competition') }}
                    </button>
                    <button onclick="exportData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        {{ __('competitions.supervision_page.export') }}
                    </button>
                    <button onclick="refreshData()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        {{ __('competitions.supervision_page.refresh') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques Globales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">{{ __('competitions.supervision_page.total_competitions') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->count() }}</p>
                    </div>
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <i class="fas fa-trophy text-blue-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">{{ __('competitions.supervision_page.active_competitions') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $activeCompetitionsCount }}</p>
                    </div>
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <i class="fas fa-play text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">{{ __('competitions.supervision_page.total_clubs') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->sum('nb_clubs') }}</p>
                    </div>
                    <div class="bg-purple-500/20 p-3 rounded-lg">
                        <i class="fas fa-users text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">{{ __('competitions.supervision_page.matches_played') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $competitions->sum('matchs_joues') }}</p>
                    </div>
                    <div class="bg-orange-500/20 p-3 rounded-lg">
                        <i class="fas fa-futbol text-orange-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Compétitions -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/20">
                <h2 class="text-xl font-semibold text-white">
                    <i class="fas fa-list mr-2"></i>
                    {{ __('competitions.supervision_page.supervised_competitions') }}
                </h2>
                <p class="text-blue-200 text-sm mt-1">
                    {{ $tunisianAssociation->name ?? __('competitions.supervision_page.association_fallback') }}
                </p>
            </div>

            @if($competitions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_competition') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_season') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_status') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_clubs') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_matches') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">{{ __('competitions.supervision_page.table_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($competitions as $competition)
                                <tr class="hover:bg-white/5 transition-colors"
                                    data-competition-id="{{ $competition['id'] }}"
                                    data-nom="{{ $competition['nom'] }}"
                                    data-saison="{{ $competition['saison'] }}"
                                    data-statut-label="{{ $competition['statut_label'] }}"
                                    data-nb-clubs="{{ $competition['nb_clubs'] }}"
                                    data-matchs-joues="{{ $competition['matchs_joues'] }}"
                                    data-nb-matchs="{{ $competition['nb_matchs'] }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-500/20 p-2 rounded-lg mr-3">
                                                <i class="fas fa-trophy text-blue-300"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $competition['nom'] }}</div>
                                                <div class="text-sm text-blue-200">{{ $competition['type'] ?? __('competitions.supervision_page.championship_fallback') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $competition['saison'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'published' => 'bg-green-500/20 text-green-300',
                                                'submitted' => 'bg-blue-500/20 text-blue-300',
                                                'validated' => 'bg-blue-500/20 text-blue-300',
                                                'draft' => 'bg-gray-500/20 text-gray-300',
                                                'cancelled' => 'bg-red-500/20 text-red-300',
                                            ];
                                            $statusColor = $statusColors[$competition['statut']] ?? 'bg-gray-500/20 text-gray-300';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                            {{ $competition['statut_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <i class="fas fa-users mr-2 text-blue-300"></i>{{ $competition['nb_clubs'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <i class="fas fa-futbol mr-2 text-green-300"></i>{{ $competition['matchs_joues'] }}/{{ $competition['nb_matchs'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewCompetitionDetails({{ $competition['id'] }})" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="{{ __('competitions.supervision_page.view_details_title') }}">
                                                <i class="fas fa-eye mr-1"></i>{{ __('competitions.supervision_page.details') }}
                                            </button>
                                            <button onclick="manageCompetition({{ $competition['id'] }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="{{ __('competitions.supervision_page.manage_title') }}">
                                                <i class="fas fa-cog mr-1"></i>{{ __('competitions.supervision_page.manage') }}
                                            </button>
                                            <button onclick="viewReports({{ $competition['id'] }})" 
                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="{{ __('competitions.supervision_page.reports_title') }}">
                                                <i class="fas fa-chart-bar mr-1"></i>{{ __('competitions.supervision_page.reports') }}
                                            </button>
                                            <button onclick="viewFixtures({{ $competition['id'] }})" 
                                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs transition-colors" 
                                                    title="{{ __('competitions.supervision_page.view_matches_title') }}">
                                                <i class="fas fa-calendar mr-1"></i>{{ __('competitions.supervision_page.matches') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-500/20 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-trophy text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">{{ __('competitions.supervision_page.no_competition_found') }}</h3>
                    <p class="text-blue-200 mb-6">{{ __('competitions.supervision_page.no_competition_found_text') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Fonctions globales pour les boutons d'action
function viewCompetitionDetails(competitionId) {
    showCompetitionModal(competitionId);
}

function manageCompetition(competitionId) {
    showManagementModal(competitionId);
}

function viewReports(competitionId) {
    window.location.href = `{{ route('competitions.association.rapports-statistiques') }}`;
}

function viewFixtures(competitionId) {
    window.location.href = `{{ route('competitions.association.fixtures') }}`;
}

function createNewCompetition() {
    alert(@json(__('competitions.supervision_page.js_not_implemented')));
}

// NOTE (audit factice -> reel, 2026-09) : cette fonction telechargeait un
// CSV entierement invente ("Ligue 1 Tunisienne", "Coupe de Tunisie" avec des
// chiffres fixes) quelles que soient les vraies competitions de
// l'association connectee, puis affichait un faux succes. Le CSV est
// desormais construit a partir des vraies lignes du tableau (donnees reelles
// issues de CompetitionController::associationSupervision()).
function exportData() {
    const rows = document.querySelectorAll('table tbody tr');
    if (!rows.length) {
        showNotification(@json(__('competitions.supervision_page.js_nothing_to_export')), 'error');
        return;
    }

    let csvContent = "Competition,Season,Status,Clubs,Matches\\n";
    let rowCount = 0;

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length < 5) return;

        const competition = cells[0].textContent.trim().replace(/\s+/g, ' ');
        const saison = cells[1].textContent.trim().replace(/\s+/g, ' ');
        const statut = cells[2].textContent.trim().replace(/\s+/g, ' ');
        const clubs = cells[3].textContent.trim().replace(/\s+/g, ' ');
        const matchs = cells[4].textContent.trim().replace(/\s+/g, ' ');

        csvContent += `"${competition}","${saison}","${statut}","${clubs}","${matchs}"\n`;
        rowCount++;
    });

    if (rowCount === 0) {
        showNotification(@json(__('competitions.supervision_page.js_nothing_to_export')), 'error');
        return;
    }

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'competitions_supervision.csv';
    a.click();
    window.URL.revokeObjectURL(url);

    showNotification(@json(__('competitions.supervision_page.js_export_success')), 'success');
}

// NOTE (audit factice -> reel, 2026-09) : affichait un faux message
// "Donnees actualisees !" apres un simple delai, sans jamais recharger les
// vraies donnees (le bouton retrouvait juste son etat initial). Recharge
// desormais reellement la page pour obtenir les donnees a jour.
function refreshData() {
    const button = event.target;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + @json(__('competitions.supervision_page.refreshing'));
    button.disabled = true;
    location.reload();
}

// Fonctions pour les modals
function showCompetitionModal(competitionId) {
    // NOTE (audit factice -> reel, 2026-09) : ce modal affichait une
    // competition entierement inventee ("Ligue 1 Tunisienne", statut
    // "Active", 20 clubs, 150/380 matchs) quel que soit le competitionId
    // reellement clique. Utilise desormais les vraies donnees deja
    // rendues dans la ligne du tableau (data-* sur la <tr>).
    const row = document.querySelector(`tr[data-competition-id="${competitionId}"]`);
    const data = row ? row.dataset : {};
    const labels = {
        title: 'Competition details',
        id_label: 'ID',
        status_label: 'Status',
        name_label: 'Name',
        season_label: 'Season',
        clubs_count_label: 'Clubs',
        matches_played_label: 'Matches played',
        matches_total_label: 'Total matches',
        available_actions: 'Available actions',
        view_matches: 'View matches',
        reports: 'Reports',
        close: 'Close'
    };

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">${labels.title}</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${labels.id_label}</label>
                        <p class="text-lg font-semibold text-blue-600">${competitionId}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${labels.status_label}</label>
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">${data.statutLabel || ''}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${labels.name_label}</label>
                    <p class="text-lg">${data.nom || ''}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">${labels.season_label}</label>
                    <p class="text-lg">${data.saison || ''}</p>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${labels.clubs_count_label}</label>
                        <p class="text-2xl font-bold text-blue-600">${data.nbClubs || '0'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${labels.matches_played_label}</label>
                        <p class="text-2xl font-bold text-green-600">${data.matchsJoues || '0'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">${labels.matches_total_label}</label>
                        <p class="text-2xl font-bold text-purple-600">${data.nbMatchs || '0'}</p>
                    </div>
                </div>
                <div class="pt-4 border-t">
                    <h4 class="font-semibold text-gray-800 mb-2">${labels.available_actions}</h4>
                    <div class="flex space-x-2">
                        <button onclick="viewFixtures(${competitionId})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-calendar mr-1"></i>${labels.view_matches}
                        </button>
                        <button onclick="viewReports(${competitionId})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-chart-bar mr-1"></i>${labels.reports}
                        </button>
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                            ${labels.close}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function showManagementModal(competitionId) {
    // NOTE (audit factice -> reel, 2026-09) : l'en-tete affichait "Ligue 1
    // Tunisienne - Saison 2024-2025" en dur, et les 6 boutons d'action
    // affichaient tous un faux succes ("Planification des matchs...")
    // sans jamais rien faire reellement. En-tete corrige avec les
    // vraies donnees de la ligne ; boutons remplaces par des messages
    // honnetes de fonctionnalite non disponible depuis cette fenetre
    // (Designation des Arbitres, Discipline & Sanctions et Rapports
    // existent deja comme pages dediees, liees depuis le tableau).
    const row = document.querySelector(`tr[data-competition-id="${competitionId}"]`);
    const data = row ? row.dataset : {};
    const labels = {
        title: 'Competition management',
        id_prefix: 'ID: ',
        matches_management: 'Matches management',
        schedule_matches: 'Schedule matches',
        edit_calendar: 'Edit calendar',
        manage_referees: 'Manage referees',
        clubs_management: 'Clubs management',
        club_entries: 'Club entries',
        discipline_sanctions: 'Discipline and sanctions',
        generate_reports: 'Generate reports',
        close: 'Close',
        schedule_matches_unavailable: 'Scheduling unavailable',
        edit_calendar_unavailable: 'Calendar editing unavailable',
        manage_referees_unavailable: 'Referee management unavailable',
        club_entries_unavailable: 'Club entries unavailable',
        discipline_unavailable: 'Discipline unavailable',
        reports_unavailable: 'Reports unavailable'
    };

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">${labels.title}</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">${labels.id_prefix}: ${competitionId}</h4>
                    <p class="text-blue-700">${data.nom || ''} - ${data.saison || ''}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">${labels.matches_management}</h4>
                        <button onclick="showNotification(${JSON.stringify(labels.schedule_matches_unavailable)}, 'error')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-calendar-plus mr-2"></i>${labels.schedule_matches}
                        </button>
                        <button onclick="showNotification(${JSON.stringify(labels.edit_calendar_unavailable)}, 'error')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-edit mr-2"></i>${labels.edit_calendar}
                        </button>
                        <button onclick="showNotification(${JSON.stringify(labels.manage_referees_unavailable)}, 'error')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-whistle mr-2"></i>${labels.manage_referees}
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">${labels.clubs_management}</h4>
                        <button onclick="showNotification(${JSON.stringify(labels.club_entries_unavailable)}, 'error')" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-handshake mr-2"></i>${labels.club_entries}
                        </button>
                        <button onclick="showNotification(${JSON.stringify(labels.discipline_unavailable)}, 'error')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-gavel mr-2"></i>${labels.discipline_sanctions}
                        </button>
                        <button onclick="showNotification(${JSON.stringify(labels.reports_unavailable)}, 'error')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg text-left">
                            <i class="fas fa-file-alt mr-2"></i>${labels.generate_reports}
                        </button>
                    </div>
                </div>
                
                <div class="pt-4 border-t">
                    <div class="flex justify-end space-x-2">
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                            ${labels.close}
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
});
</script>
@endsection
