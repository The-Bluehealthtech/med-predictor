<!-- Card Association - Module Compétitions -->
<div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
    <!-- En-tête de la card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Côté Association/Ligue</h3>
                    <p class="text-green-100 text-sm">Supervision et gestion des compétitions</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-white text-sm">Statut</div>
                <div class="text-green-100 font-semibold">Actif</div>
            </div>
        </div>
    </div>

    <!-- Contenu de la card -->
    <div class="p-6">
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $competitions_count ?? 5 }}</div>
                <div class="text-sm text-gray-600">Compétitions</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $clubs_count ?? 24 }}</div>
                <div class="text-sm text-gray-600">Clubs</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $matchs_count ?? 156 }}</div>
                <div class="text-sm text-gray-600">Matchs</div>
            </div>
        </div>

        <!-- Actions principales -->
        <div class="space-y-3">
            <a href="{{ route('competitions.association.supervision') }}" 
               class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                <div class="bg-green-100 group-hover:bg-green-200 rounded-full p-2 mr-3">
                    <i class="fas fa-eye text-green-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Compétitions Supervisées</div>
                    <div class="text-sm text-gray-600">Gestion des compétitions</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
            </a>

            <a href="{{ route('competitions.association.engagements-clubs') }}" 
               class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                <div class="bg-blue-100 group-hover:bg-blue-200 rounded-full p-2 mr-3">
                    <i class="fas fa-clipboard-check text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Engagements des Clubs</div>
                    <div class="text-sm text-gray-600">Validation des inscriptions</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
            </a>

            <a href="{{ route('competitions.association.calendrier-global') }}" 
               class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                <div class="bg-purple-100 group-hover:bg-purple-200 rounded-full p-2 mr-3">
                    <i class="fas fa-calendar text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Calendrier Global</div>
                    <div class="text-sm text-gray-600">Planning centralisé</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
            </a>

            <a href="{{ route('competitions.association.resultats-classements') }}" 
               class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors group">
                <div class="bg-yellow-100 group-hover:bg-yellow-200 rounded-full p-2 mr-3">
                    <i class="fas fa-trophy text-yellow-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Résultats & Classements</div>
                    <div class="text-sm text-gray-600">Compilation automatique</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-yellow-600"></i>
            </a>

            <a href="{{ route('competitions.association.discipline-sanctions') }}" 
               class="flex items-center p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors group">
                <div class="bg-red-100 group-hover:bg-red-200 rounded-full p-2 mr-3">
                    <i class="fas fa-balance-scale text-red-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Discipline & Sanctions</div>
                    <div class="text-sm text-gray-600">Validation des sanctions</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-600"></i>
            </a>

            <a href="{{ route('competitions.association.rapports-statistiques') }}" 
               class="flex items-center p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                <div class="bg-indigo-100 group-hover:bg-indigo-200 rounded-full p-2 mr-3">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Rapports & Statistiques</div>
                    <div class="text-sm text-gray-600">Export PDF/Excel</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
            </a>
        </div>

        <!-- Tableau de bord rapide -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Tableau de Bord Rapide</h4>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600">Feuilles en attente</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $feuilles_attente ?? 8 }}</div>
                        </div>
                        <i class="fas fa-file-alt text-orange-500"></i>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600">Matchs aujourd'hui</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $matchs_aujourdhui ?? 3 }}</div>
                        </div>
                        <i class="fas fa-calendar-day text-blue-500"></i>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600">Sanctions récentes</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $sanctions_recentes ?? 2 }}</div>
                        </div>
                        <i class="fas fa-gavel text-red-500"></i>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600">Rapports générés</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $rapports_generes ?? 12 }}</div>
                        </div>
                        <i class="fas fa-chart-line text-green-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes importantes -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <span class="text-sm font-semibold text-gray-900">Alertes Importantes</span>
                </div>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                    {{ $alertes_count ?? 2 }} critiques
                </span>
            </div>
            
            <!-- Liste des alertes -->
            <div class="space-y-2">
                <div class="text-xs text-gray-600 flex items-center">
                    <i class="fas fa-clock text-red-500 mr-2"></i>
                    Match reporté - FC Ville vs AS Sport
                </div>
                <div class="text-xs text-gray-600 flex items-center">
                    <i class="fas fa-file-alt text-orange-500 mr-2"></i>
                    Feuille de match en retard - Club Athletic
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de la card -->
    <div class="bg-gray-50 px-6 py-3 rounded-b-lg">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <i class="fas fa-sync-alt mr-1"></i>
                Dernière sync: {{ now()->format('H:i') }}
            </div>
            <div class="flex space-x-2">
                <button onclick="genererRapport()" 
                        class="text-green-600 hover:text-green-800 text-sm">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Rapport
                </button>
                <button onclick="exporterDonnees()" 
                        class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-download mr-1"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function genererRapport() {
    if (window.CompetitionsApp) {
        window.CompetitionsApp.exportRapport('classement', 'pdf');
    } else {
        alert('Génération du rapport en cours...');
    }
}

function exporterDonnees() {
    if (window.CompetitionsApp) {
        window.CompetitionsApp.exportRapport('statistiques', 'excel');
    } else {
        alert('Export des données en cours...');
    }
}
</script>
@endpush






