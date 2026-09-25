@extends('layouts.app')

@section('title', 'Fixtures - Calendrier des Matchs')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Calendrier des Matchs</h1>
                    <p class="mt-2 text-gray-600">Tous les matchs de la saison 2024-2025</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportFixtures()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i>
                        Exporter
                    </button>
                    <button onclick="refreshFixtures()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Journée</label>
                    <select id="journeeFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les journées</option>
                        @for($i = 1; $i <= 30; $i++)
                            <option value="{{ $i }}">Journée {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="statutFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="Terminé">Terminé</option>
                        <option value="À venir">À venir</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select id="periodeFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toute la saison</option>
                        <option value="septembre">Septembre 2024</option>
                        <option value="octobre">Octobre 2024</option>
                        <option value="novembre">Novembre 2024</option>
                        <option value="decembre">Décembre 2024</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-2"></i>
                        Appliquer
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Matchs Joués</h3>
                        <p class="text-sm text-gray-500" id="matchsJoues">{{ count(collect($fixtures)->flatten(1)->where('statut', 'Terminé')) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Prochain Match</h3>
                        <p class="text-sm text-gray-500" id="prochainMatch">{{ now()->addDays(7)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-trophy text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Victoires</h3>
                        <p class="text-sm text-gray-500" id="victoires">{{ rand(8, 15) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Points</h3>
                        <p class="text-sm text-gray-500" id="points">{{ rand(25, 45) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fixtures par compétition -->
        @foreach($fixtures as $competitionId => $competitionFixtures)
            @foreach($competitionFixtures as $journee)
                <div class="bg-white rounded-lg shadow mb-6 journee-container" data-journee="{{ $journee['journee'] }}" data-competition="{{ $competitionId }}">
                    <!-- En-tête de la journée -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $journee['competition_name'] }} - Journée {{ $journee['journee'] }}</h2>
                                <p class="text-sm text-gray-600">{{ $journee['date']->format('d/m/Y') }}</p>
                            </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Matchs</div>
                                <div class="text-lg font-semibold text-gray-900">{{ count($journee['matchs']) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Statut</div>
                                <div class="text-sm text-gray-900">
                                    @if($journee['date'] < now())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Terminé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            À venir
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matchs de la journée -->
                <div class="divide-y divide-gray-200">
                    @foreach($journee['matchs'] as $match)
                        <div class="p-6 hover:bg-gray-50 transition-colors match-row" data-statut="{{ $match['statut'] }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <!-- Équipe domicile -->
                                    <div class="flex items-center space-x-3">
                                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-sm">{{ substr($match['domicile']->name ?? $match['domicile']->short_name ?? 'CLUB', 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $match['domicile']->name ?? 'Club Domicile' }}</div>
                                            <div class="text-xs text-gray-500">{{ $match['domicile']->short_name ?? 'CD' }}</div>
                                        </div>
                                    </div>

                                    <!-- Score -->
                                    <div class="text-center">
                                        @if($match['statut'] === 'Terminé')
                                            <div class="text-2xl font-bold text-gray-900">
                                                {{ $match['buts_domicile'] }} - {{ $match['buts_exterieur'] }}
                                            </div>
                                        @else
                                            <div class="text-lg font-medium text-gray-500">VS</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">{{ $match['date']->format('d/m/Y') }} - {{ $match['heure'] }}</div>
                                    </div>

                                    <!-- Équipe extérieure -->
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 text-right">{{ $match['exterieur']->name ?? 'Club Extérieur' }}</div>
                                            <div class="text-xs text-gray-500 text-right">{{ $match['exterieur']->short_name ?? 'CE' }}</div>
                                        </div>
                                        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-600 font-bold text-sm">{{ substr($match['exterieur']->name ?? $match['exterieur']->short_name ?? 'CLUB', 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations du match -->
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Stade</div>
                                        <div class="text-sm font-medium text-gray-900">{{ $match['stade'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Arbitre</div>
                                        <div class="text-sm font-medium text-gray-900">{{ $match['arbitre_principal'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Statut</div>
                                        <div class="text-sm">
                                            @if($match['statut'] === 'Terminé')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Terminé
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    À venir
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('competitions.club.feuille-match', $match['id']) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-eye mr-2"></i>
                                            Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
// Fonction pour exporter les fixtures
function exportFixtures() {
    let csvContent = 'Fixtures - Saison 2024-2025\n';
    csvContent += 'Journée,Date,Équipe Domicile,Équipe Extérieure,Score,Stade,Arbitre,Statut\n';
    
    const journees = document.querySelectorAll('.journee-container');
    journees.forEach(journee => {
        const journeeNum = journee.dataset.journee;
        const matchs = journee.querySelectorAll('.match-row');
        
        matchs.forEach(match => {
            const domicile = match.querySelector('.text-sm.font-medium').textContent.trim();
            const exterieur = match.querySelectorAll('.text-sm.font-medium')[1].textContent.trim();
            const score = match.querySelector('.text-2xl.font-bold')?.textContent.trim() || 'VS';
            const stade = match.querySelectorAll('.text-sm.font-medium')[2]?.textContent.trim() || '';
            const arbitre = match.querySelectorAll('.text-sm.font-medium')[3]?.textContent.trim() || '';
            const statut = match.querySelector('.inline-flex').textContent.trim();
            const date = match.querySelector('.text-xs.text-gray-500').textContent.trim();
            
            csvContent += `${journeeNum},${date},${domicile},${exterieur},${score},${stade},${arbitre},${statut}\n`;
        });
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `fixtures_saison_2024_2025_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Fixtures exportées avec succès!', 'success');
}

// Fonction pour actualiser les fixtures
function refreshFixtures() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualisation...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Fonction pour appliquer les filtres
function applyFilters() {
    const journeeFilter = document.getElementById('journeeFilter').value;
    const statutFilter = document.getElementById('statutFilter').value;
    const periodeFilter = document.getElementById('periodeFilter').value;
    
    const journees = document.querySelectorAll('.journee-container');
    journees.forEach(journee => {
        let show = true;
        
        if (journeeFilter && journee.dataset.journee !== journeeFilter) {
            show = false;
        }
        
        if (statutFilter) {
            const matchs = journee.querySelectorAll('.match-row');
            const hasMatchingStatut = Array.from(matchs).some(match => 
                match.dataset.statut === statutFilter
            );
            if (!hasMatchingStatut) {
                show = false;
            }
        }
        
        journee.style.display = show ? 'block' : 'none';
    });
    
    showNotification('Filtres appliqués!', 'success');
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
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
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
