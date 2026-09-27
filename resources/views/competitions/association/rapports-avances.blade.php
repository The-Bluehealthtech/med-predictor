@extends('layouts.app')

@section('title', __('competitions.rapports_avances_page.page_title'))

@section('content')
{{-- NOTE (audit factice -> reel, 2026-09) : cette vue n'est reliée à aucune route de l'application et aucune des actions ci-dessous (génération de rapports matchs/joueurs/arbitres, créateur de rapports) n'a de contrôleur réel derrière. Le texte a été traduit sur demande explicite, mais aucune route ni fonctionnalité n'a été ajoutée ou corrigée ici. --}}
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
            {{ __('competitions.rapports_avances_page.header_title') }}
        </h1>
        <p class="text-gray-600">{{ __('competitions.rapports_avances_page.header_subtitle') }}</p>
    </div>

    <!-- Statistiques Générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-futbol text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_avances_page.stat_total_matches') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_matches'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_avances_page.stat_total_players') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_players'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-whistle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_avances_page.stat_total_referees') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_referees'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.rapports_avances_page.stat_total_competitions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_competitions'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Types de Rapports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Rapport de Matchs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                    {{ __('competitions.rapports_avances_page.match_report_title') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('competitions.rapports_avances_page.match_report_desc') }}</p>
            </div>
            <div class="p-6">
                <form id="match-report-form" method="POST" action="{{ route('competitions.association.generate-match-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="matches">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.date_from_label') }}</label>
                            <input type="date" name="date_from" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.date_to_label') }}</label>
                            <input type="date" name="date_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.competition_label') }}</label>
                            <select name="competition_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('competitions.rapports_avances_page.all_competitions_option') }}</option>
                                @foreach($competitions as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.club_label') }}</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('competitions.rapports_avances_page.all_clubs_option') }}</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.export_format_label') }}</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_web_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_pdf_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_excel_option') }}</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>{{ __('competitions.rapports_avances_page.generate_report_button') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport de Performance des Joueurs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-running text-green-500 mr-2"></i>
                    {{ __('competitions.rapports_avances_page.player_report_title') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('competitions.rapports_avances_page.player_report_desc') }}</p>
            </div>
            <div class="p-6">
                <form id="player-report-form" method="POST" action="{{ route('competitions.association.generate-player-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="players">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.period_label') }}</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">{{ __('competitions.rapports_avances_page.period_current_month') }}</option>
                                <option value="current_season">{{ __('competitions.rapports_avances_page.period_current_season') }}</option>
                                <option value="last_3_months">{{ __('competitions.rapports_avances_page.period_last_3_months') }}</option>
                                <option value="custom">{{ __('competitions.rapports_avances_page.period_custom') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.club_label') }}</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('competitions.rapports_avances_page.all_clubs_option') }}</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.export_format_label') }}</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_web_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_pdf_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_excel_option') }}</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>{{ __('competitions.rapports_avances_page.generate_report_button') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport des Arbitres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-whistle text-orange-500 mr-2"></i>
                    {{ __('competitions.rapports_avances_page.referee_report_title') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('competitions.rapports_avances_page.referee_report_desc') }}</p>
            </div>
            <div class="p-6">
                <form id="referee-report-form" method="POST" action="{{ route('competitions.association.generate-referee-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="referees">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.period_label') }}</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">{{ __('competitions.rapports_avances_page.period_current_month') }}</option>
                                <option value="current_season">{{ __('competitions.rapports_avances_page.period_current_season') }}</option>
                                <option value="last_6_months">{{ __('competitions.rapports_avances_page.period_last_6_months') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.referee_label') }}</label>
                            <select name="referee_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('competitions.rapports_avances_page.all_referees_option') }}</option>
                                @foreach($referees as $referee)
                                    <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('competitions.rapports_avances_page.export_format_label') }}</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_web_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_pdf_option') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">{{ __('competitions.rapports_avances_page.format_excel_option') }}</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>{{ __('competitions.rapports_avances_page.generate_report_button') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Créateur de Rapports Personnalisés -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tools text-purple-500 mr-2"></i>
                    {{ __('competitions.rapports_avances_page.report_builder_title') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('competitions.rapports_avances_page.report_builder_desc') }}</p>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-cogs text-4xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('competitions.rapports_avances_page.report_builder_tool_title') }}</h4>
                    <p class="text-sm text-gray-600 mb-4">{{ __('competitions.rapports_avances_page.report_builder_tool_desc') }}</p>
                    <a href="{{ route('competitions.association.report-builder') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>{{ __('competitions.rapports_avances_page.create_report_button') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapports Récents -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history text-gray-500 mr-2"></i>
                {{ __('competitions.rapports_avances_page.recent_reports_title') }}
            </h3>
        </div>
        <div class="p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-file-alt text-3xl mb-2"></i>
                <p>{{ __('competitions.rapports_avances_page.no_recent_reports') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des formulaires de rapports
    const forms = document.querySelectorAll('form[id$="-form"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const format = formData.get('format');
            
            if (format === 'web') {
                // Afficher le rapport dans une nouvelle page
                const url = this.action;
                const method = this.method;
                
                // Créer un formulaire temporaire pour la soumission
                const tempForm = document.createElement('form');
                tempForm.method = method;
                tempForm.action = url;
                tempForm.target = '_blank';
                
                // Copier tous les champs
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    tempForm.appendChild(input);
                }
                
                document.body.appendChild(tempForm);
                tempForm.submit();
                document.body.removeChild(tempForm);
            } else {
                // Soumettre normalement pour PDF/Excel
                this.submit();
            }
        });
    });

    // Gestion du formulaire des joueurs
    document.getElementById('player-report-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const format = formData.get('format');
        
        if (format === 'web') {
            // Rediriger vers la route GET pour l'affichage web
            const params = new URLSearchParams();
            params.append('club_id', formData.get('club_id') || '');
            params.append('period', formData.get('period') || '');
            
            window.location.href = '{{ route("competitions.association.player-performance-report") }}?' + params.toString();
        } else {
            // Soumettre normalement pour PDF/Excel
            this.submit();
        }
    });
});
</script>
@endpush

