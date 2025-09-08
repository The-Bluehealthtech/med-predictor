@extends('layouts.app')

@section('title', 'Rapports Avancés - FIT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
            Rapports Avancés
        </h1>
        <p class="text-gray-600">Génération de rapports détaillés avec filtres avancés et exports multiples</p>
    </div>

    <!-- Statistiques Générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-futbol text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Matchs</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Joueurs</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Arbitres</p>
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
                    <p class="text-sm font-medium text-gray-600">Compétitions</p>
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
                    Rapport de Matchs
                </h3>
                <p class="text-sm text-gray-600 mt-1">Analyse détaillée des matchs avec statistiques</p>
            </div>
            <div class="p-6">
                <form id="match-report-form" method="POST" action="{{ route('competitions.association.generate-match-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="matches">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                            <input type="date" name="date_from" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                            <input type="date" name="date_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                            <select name="competition_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Toutes les compétitions</option>
                                @foreach($competitions as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les clubs</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport de Performance des Joueurs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-running text-green-500 mr-2"></i>
                    Performance des Joueurs
                </h3>
                <p class="text-sm text-gray-600 mt-1">Statistiques détaillées des performances</p>
            </div>
            <div class="p-6">
                <form id="player-report-form" method="POST" action="{{ route('competitions.association.generate-player-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="players">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">Mois en cours</option>
                                <option value="current_season">Saison en cours</option>
                                <option value="last_3_months">3 derniers mois</option>
                                <option value="custom">Période personnalisée</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les clubs</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport des Arbitres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-whistle text-orange-500 mr-2"></i>
                    Rapport des Arbitres
                </h3>
                <p class="text-sm text-gray-600 mt-1">Activité et statistiques des arbitres</p>
            </div>
            <div class="p-6">
                <form id="referee-report-form" method="POST" action="{{ route('competitions.association.generate-referee-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="referees">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">Mois en cours</option>
                                <option value="current_season">Saison en cours</option>
                                <option value="last_6_months">6 derniers mois</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arbitre</label>
                            <select name="referee_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les arbitres</option>
                                @foreach($referees as $referee)
                                    <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Créateur de Rapports Personnalisés -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tools text-purple-500 mr-2"></i>
                    Créateur de Rapports
                </h3>
                <p class="text-sm text-gray-600 mt-1">Créez vos propres rapports personnalisés</p>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-cogs text-4xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Outil de Création</h4>
                    <p class="text-sm text-gray-600 mb-4">Sélectionnez les champs, filtres et format de votre choix</p>
                    <a href="{{ route('competitions.association.report-builder') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Créer un Rapport
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
                Rapports Récents
            </h3>
        </div>
        <div class="p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-file-alt text-3xl mb-2"></i>
                <p>Aucun rapport généré récemment</p>
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
@section('title', 'Rapports Avancés - FIT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
            Rapports Avancés
        </h1>
        <p class="text-gray-600">Génération de rapports détaillés avec filtres avancés et exports multiples</p>
    </div>

    <!-- Statistiques Générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-futbol text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Matchs</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Joueurs</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Arbitres</p>
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
                    <p class="text-sm font-medium text-gray-600">Compétitions</p>
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
                    Rapport de Matchs
                </h3>
                <p class="text-sm text-gray-600 mt-1">Analyse détaillée des matchs avec statistiques</p>
            </div>
            <div class="p-6">
                <form id="match-report-form" method="POST" action="{{ route('competitions.association.generate-match-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="matches">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                            <input type="date" name="date_from" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                            <input type="date" name="date_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                            <select name="competition_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Toutes les compétitions</option>
                                @foreach($competitions as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les clubs</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport de Performance des Joueurs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-running text-green-500 mr-2"></i>
                    Performance des Joueurs
                </h3>
                <p class="text-sm text-gray-600 mt-1">Statistiques détaillées des performances</p>
            </div>
            <div class="p-6">
                <form id="player-report-form" method="POST" action="{{ route('competitions.association.generate-player-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="players">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">Mois en cours</option>
                                <option value="current_season">Saison en cours</option>
                                <option value="last_3_months">3 derniers mois</option>
                                <option value="custom">Période personnalisée</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                            <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les clubs</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport des Arbitres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-whistle text-orange-500 mr-2"></i>
                    Rapport des Arbitres
                </h3>
                <p class="text-sm text-gray-600 mt-1">Activité et statistiques des arbitres</p>
            </div>
            <div class="p-6">
                <form id="referee-report-form" method="POST" action="{{ route('competitions.association.generate-referee-report') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="referees">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="current_month">Mois en cours</option>
                                <option value="current_season">Saison en cours</option>
                                <option value="last_6_months">6 derniers mois</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arbitre</label>
                            <select name="referee_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les arbitres</option>
                                @foreach($referees as $referee)
                                    <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="web" class="mr-2" checked>
                                <span class="text-sm">Vue Web</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>Générer le Rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Créateur de Rapports Personnalisés -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tools text-purple-500 mr-2"></i>
                    Créateur de Rapports
                </h3>
                <p class="text-sm text-gray-600 mt-1">Créez vos propres rapports personnalisés</p>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-cogs text-4xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Outil de Création</h4>
                    <p class="text-sm text-gray-600 mb-4">Sélectionnez les champs, filtres et format de votre choix</p>
                    <a href="{{ route('competitions.association.report-builder') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Créer un Rapport
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
                Rapports Récents
            </h3>
        </div>
        <div class="p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-file-alt text-3xl mb-2"></i>
                <p>Aucun rapport généré récemment</p>
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

