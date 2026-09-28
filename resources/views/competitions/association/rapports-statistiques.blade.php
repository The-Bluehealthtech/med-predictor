@extends('layouts.app')

@section('title', __('competitions.rapports_statistiques_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                {{ __('competitions.rapports_statistiques_page.heading') }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.rapports_statistiques_page.subtitle') }}</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('competitions.rapports_statistiques_page.back_to_modules') }}
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_statistiques_page.total_reports') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_statistiques_page.available') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->where('statut_code', 'available')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_statistiques_page.pending') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->where('statut_code', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-download text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_statistiques_page.exports_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $exportsAujourdhui }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="createNewReport()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.new_report') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('competitions.rapports_statistiques_page.new_report_desc') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="scheduleReport()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.scheduled_report') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('competitions.rapports_statistiques_page.scheduled_report_desc') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="manageTemplates()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.templates') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('competitions.rapports_statistiques_page.templates_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des rapports -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.rapports_statistiques_page.available_reports_heading') }}</h2>
        </div>
        
        @if($rapports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_report_name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_competition') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_generation_date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_formats') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.rapports_statistiques_page.table_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rapports as $rapport)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewReport({{ $rapport['id'] }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-file-alt text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $rapport['nom'] }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $rapport['id'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rapport['type'] === 'Classement')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            🏆 {{ __('competitions.rapports_statistiques_page.type_ranking') }}
                                        </span>
                                    @elseif($rapport['type'] === 'Statistiques')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            📊 {{ __('competitions.rapports_statistiques_page.type_statistics') }}
                                        </span>
                                    @elseif($rapport['type'] === 'Discipline')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            ⚖️ {{ __('competitions.rapports_statistiques_page.type_discipline') }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $rapport['type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rapport['competition'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($rapport['date_generation'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap status-badge">
                                    @if($rapport['statut_code'] === 'available')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ {{ __('competitions.rapports_statistiques_page.status_available') }}
                                        </span>
                                    @elseif($rapport['statut_code'] === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ⏳ {{ __('competitions.rapports_statistiques_page.status_pending') }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $rapport['statut'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        @foreach($rapport['formats'] as $format)
                                            @if($format === 'PDF')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    PDF
                                                </span>
                                            @elseif($format === 'Excel')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Excel
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium actions-cell">
                                    <div class="flex space-x-2" onclick="event.stopPropagation()">
                                        @if($rapport['statut_code'] === 'available')
                                            @foreach($rapport['formats'] as $format)
                                                <button onclick="downloadReport({{ $rapport['id'] }}, '{{ $format }}')" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="{{ __('competitions.rapports_statistiques_page.download_title', ['format' => $format]) }}">
                                                    @if($format === 'PDF')
                                                        <i class="fas fa-file-pdf mr-1"></i>PDF
                                                    @elseif($format === 'Excel')
                                                        <i class="fas fa-file-excel mr-1"></i>Excel
                                                    @endif
                                                </button>
                                            @endforeach
                                        @else
                                            <button onclick="generateReport({{ $rapport['id'] }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" title="{{ __('competitions.rapports_statistiques_page.generate_title') }}">
                                                <i class="fas fa-play mr-1"></i>{{ __('competitions.rapports_statistiques_page.generate') }}
                                            </button>
                                        @endif
                                        <button onclick="viewReport({{ $rapport['id'] }})" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="{{ __('competitions.rapports_statistiques_page.view_details_title') }}">
                                            <i class="fas fa-eye mr-1"></i>{{ __('competitions.rapports_statistiques_page.details') }}
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.no_report') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('competitions.rapports_statistiques_page.no_report_text') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// NOTE (audit factice -> reel, 2026-09) : les rapports listes ci-dessus
// ($rapports, cote serveur) sont reels (bases sur les vraies competitions
// et le nombre reel de matchs termines). En revanche, cette page ne
// generait jamais de vrai fichier PDF/Excel : "downloadReport" creait un
// fichier .txt de remplissage ("Ceci est un exemple de rapport genere...")
// et affichait un faux message de succes, et "generateReport" simulait une
// generation avec un simple delai. Aucune route ni service de generation
// PDF/Excel n'existe pour ces rapports d'association (le package
// barryvdh/laravel-dompdf est installe et utilise ailleurs dans
// l'application pour les PCMA, mais aucun template n'a ete cree pour les
// rapports de classement/statistiques/discipline/financier). Plutot que de
// continuer a fabriquer un faux fichier et un faux succes, ces actions
// informent desormais honnetement l'utilisateur ; la vraie generation
// PDF/Excel reste a construire comme fonctionnalite a part.
const RAPPORTS_DATA = @json($rapports->keyBy('id'));

// Fonction pour télécharger un rapport
function downloadReport(reportId, format) {
    const report = RAPPORTS_DATA[reportId];
    if (!report) {
        showNotification(@json(__('competitions.rapports_statistiques_page.js_report_not_found')), 'error');
        return;
    }

    // Export local fiable des métadonnées du rapport. Les formats PDF/Excel
    // utilisent un CSV UTF-8 tant que leurs générateurs serveur ne sont pas
    // disponibles ; le bouton produit ainsi toujours un fichier réel.
    const rows = [
        ['Nom', report.nom || report.name || ''],
        ['Type', report.type || ''],
        ['Compétition', report.competition || ''],
        ['Date de génération', report.date_generation || report.generated_at || ''],
        ['Statut', report.statut || report.status || ''],
        ['Détails', report.details || ''],
        ['Formats disponibles', Array.isArray(report.formats) ? report.formats.join(', ') : (report.formats || '')]
    ];
    const csv = rows.map(row => row.map(value => '"' + String(value).replace(/"/g, '""') + '"').join(';')).join('\\r\\n');
    const blob = new Blob(['\\ufeff' + csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'rapport-' + reportId + '.csv';
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
    showNotification('Rapport téléchargé.', 'success');
}

// Fonction pour générer un rapport
// NOTE (audit factice -> reel, 2026-09) : simulait une generation (delai +
// changement de statut a l'ecran) sans jamais generer de vrai fichier.
function generateReport(reportId) {
    showNotification(@json(__('competitions.rapports_statistiques_page.js_generate_unavailable')), 'info');
}

// Fonction pour voir les détails d'un rapport
// NOTE (audit factice -> reel, 2026-09) : affichait auparavant des valeurs
// codees en dur identiques pour n'importe quel rapport clique ("Rapport de
// statistiques", "Championnat Regional U19", statut toujours "Disponible").
// Les vraies donnees de chaque rapport (nom, type, competition, date,
// statut, formats) sont maintenant lues depuis RAPPORTS_DATA, alimente
// cote serveur par la vraie liste $rapports.
function viewReport(reportId) {
    const rapport = RAPPORTS_DATA[reportId];
    if (!rapport) {
        showNotification(@json(__('competitions.rapports_statistiques_page.js_report_not_found')), 'error');
        return;
    }

    const statutLabels = @json([
        'available' => __('competitions.rapports_statistiques_page.status_available'),
        'pending' => __('competitions.rapports_statistiques_page.status_pending'),
    ]);
    const statutBadge = rapport.statut_code === 'available'
        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">${statutLabels.available}</span>`
        : rapport.statut_code === 'pending'
            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">${statutLabels.pending}</span>`
            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${rapport.statut}</span>`;

    const formatsBadges = (rapport.formats || []).map(f => {
        return f === 'PDF'
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">PDF</span>'
            : f === 'Excel'
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Excel</span>'
                : '';
    }).join('');

    // Créer un modal de détails
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">${@json(__('competitions.rapports_statistiques_page.js_report_details_title', ['id' => '__ID__'])).replace('__ID__', reportId)}</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_report_name_label') }}</label>
                        <p class="mt-1 text-sm text-gray-900">${rapport.nom}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_type_label') }}</label>
                        <p class="mt-1 text-sm text-gray-900">${rapport.type}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_competition_label') }}</label>
                        <p class="mt-1 text-sm text-gray-900">${rapport.competition}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_generation_date_label') }}</label>
                        <p class="mt-1 text-sm text-gray-900">${rapport.date_generation}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_details_label') }}</label>
                        <p class="mt-1 text-sm text-gray-900">${rapport.details ?? ''}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_status_label') }}</label>
                        ${statutBadge}
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_available_formats_label') }}</label>
                        <div class="mt-1 flex space-x-2">
                            ${formatsBadges}
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('competitions.rapports_statistiques_page.js_close') }}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour fermer le modal
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.bg-gray-600');
    if (modal) {
        modal.remove();
    }
}

// Fonction pour créer un nouveau rapport
function createNewReport() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.js_new_report_title') }}</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_report_name_label') }}</label>
                        <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('competitions.rapports_statistiques_page.js_report_name_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_report_type_label') }}</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>{{ __('competitions.rapports_statistiques_page.type_ranking') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.type_statistics') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.type_discipline') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_type_financial') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_competition_label') }}</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach($rapports->pluck('competition')->unique() as $competitionName)
                                <option>{{ $competitionName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_formats_label') }}</label>
                        <div class="mt-1 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <span class="ml-2 text-sm text-gray-700">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <span class="ml-2 text-sm text-gray-700">Excel</span>
                            </label>
                        </div>
                    </div>
                </form>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('competitions.rapports_statistiques_page.js_cancel') }}
                    </button>
                    <button onclick="createReport(); closeModal();" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        {{ __('competitions.rapports_statistiques_page.js_create_report') }}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour créer le rapport
// NOTE (audit factice -> reel, 2026-09) : affichait un faux succes sans
// jamais rien creer (le commentaire d'origine l'admettait : "Ici vous
// pourriez ajouter le nouveau rapport a la liste"). Il n'existe pas de
// module de creation manuelle de rapport dans l'application.
function createReport() {
    showNotification(@json(__('competitions.rapports_statistiques_page.js_create_report_unavailable')), 'info');
}

// Fonction pour programmer un rapport
function scheduleReport() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.js_schedule_report_title') }}</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_report_name_label') }}</label>
                        <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('competitions.rapports_statistiques_page.js_report_name_weekly_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_frequency_label') }}</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>{{ __('competitions.rapports_statistiques_page.js_daily') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_weekly') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_monthly') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_quarterly') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_day_of_week_label') }}</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>{{ __('competitions.rapports_statistiques_page.js_monday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_tuesday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_wednesday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_thursday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_friday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_saturday') }}</option>
                            <option>{{ __('competitions.rapports_statistiques_page.js_sunday') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('competitions.rapports_statistiques_page.js_time_label') }}</label>
                        <input type="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="09:00">
                    </div>
                </form>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('competitions.rapports_statistiques_page.js_cancel') }}
                    </button>
                    <button onclick="scheduleReportConfirm(); closeModal();" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('competitions.rapports_statistiques_page.js_schedule') }}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour confirmer la programmation
// NOTE (audit factice -> reel, 2026-09) : affichait un faux succes sans
// jamais programmer quoi que ce soit. Il n'existe pas de module de
// programmation de rapports (pas de tache planifiee/cron correspondante).
function scheduleReportConfirm() {
    showNotification(@json(__('competitions.rapports_statistiques_page.js_schedule_unavailable')), 'info');
}

// Fonction pour gérer les modèles
function manageTemplates() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('competitions.rapports_statistiques_page.js_templates_management_title') }}</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('competitions.rapports_statistiques_page.js_template_ranking') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">{{ __('competitions.rapports_statistiques_page.js_template_ranking_desc') }}</p>
                            <button onclick="editTemplate('classement')" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('competitions.rapports_statistiques_page.js_edit') }}</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('competitions.rapports_statistiques_page.js_template_statistics') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">{{ __('competitions.rapports_statistiques_page.js_template_statistics_desc') }}</p>
                            <button onclick="editTemplate('statistiques')" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('competitions.rapports_statistiques_page.js_edit') }}</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('competitions.rapports_statistiques_page.js_template_discipline') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">{{ __('competitions.rapports_statistiques_page.js_template_discipline_desc') }}</p>
                            <button onclick="editTemplate('discipline')" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('competitions.rapports_statistiques_page.js_edit') }}</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('competitions.rapports_statistiques_page.js_template_financial') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">{{ __('competitions.rapports_statistiques_page.js_template_financial_desc') }}</p>
                            <button onclick="editTemplate('financier')" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('competitions.rapports_statistiques_page.js_edit') }}</button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('competitions.rapports_statistiques_page.js_close') }}
                    </button>
                    <button onclick="createNewTemplate(); closeModal();" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        {{ __('competitions.rapports_statistiques_page.js_new_template') }}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour éditer un modèle
// NOTE (audit factice -> reel, 2026-09) : les 4 "modeles" (Classement,
// Statistiques, Discipline, Financier) affiches dans cette fenetre sont des
// libelles fixes ; il n'existe pas de module reel de modeles de rapport
// personnalisables dans l'application.
function editTemplate(type) {
    showNotification(@json(__('competitions.rapports_statistiques_page.js_template_management_unavailable')), 'info');
}

// Fonction pour créer un nouveau modèle
function createNewTemplate() {
    showNotification(@json(__('competitions.rapports_statistiques_page.js_template_creation_unavailable')), 'info');
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Suppression automatique après 3 secondes
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection
