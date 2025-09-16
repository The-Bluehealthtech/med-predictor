<!-- Card Club - Module Compétitions -->
<div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
    <!-- En-tête de la card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Côté Club</h3>
                    <p class="text-blue-100 text-sm">Gestion des compétitions pour les clubs</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-white text-sm">Statut</div>
                <div class="text-blue-100 font-semibold">Actif</div>
            </div>
        </div>
    </div>

    <!-- Contenu de la card -->
    <div class="p-6">
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $engagements_count ?? 2 }}</div>
                <div class="text-sm text-gray-600">Compétitions</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $effectif_eligible ?? 15 }}</div>
                <div class="text-sm text-gray-600">Joueurs Éligibles</div>
            </div>
        </div>

        <!-- Actions principales -->
        <div class="space-y-3">
            <a href="{{ route('competitions.club.engagements') }}" 
               class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                <div class="bg-blue-100 group-hover:bg-blue-200 rounded-full p-2 mr-3">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Mes Engagements</div>
                    <div class="text-sm text-gray-600">Compétitions inscrites</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
            </a>

            <a href="{{ route('competitions.club.effectif') }}" 
               class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                <div class="bg-green-100 group-hover:bg-green-200 rounded-full p-2 mr-3">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Effectif Éligible</div>
                    <div class="text-sm text-gray-600">Vérifications automatiques</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
            </a>

            <a href="{{ route('competitions.club.calendrier') }}" 
               class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                <div class="bg-purple-100 group-hover:bg-purple-200 rounded-full p-2 mr-3">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Calendrier & Matchs</div>
                    <div class="text-sm text-gray-600">Planning des rencontres</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
            </a>

            <a href="{{ route('competitions.club.feuilles-match') }}" 
               class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors group">
                <div class="bg-orange-100 group-hover:bg-orange-200 rounded-full p-2 mr-3">
                    <i class="fas fa-file-alt text-orange-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Feuilles de Match</div>
                    <div class="text-sm text-gray-600">Préparation et soumission</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600"></i>
            </a>

            <a href="{{ route('competitions.club.discipline') }}" 
               class="flex items-center p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors group">
                <div class="bg-red-100 group-hover:bg-red-200 rounded-full p-2 mr-3">
                    <i class="fas fa-gavel text-red-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Discipline & Notifications</div>
                    <div class="text-sm text-gray-600">Suivi sanctions et alertes</div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-600"></i>
            </a>
        </div>

        <!-- Alertes et notifications -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-bell text-yellow-500 mr-2"></i>
                    <span class="text-sm text-gray-600">Notifications</span>
                </div>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                    {{ $notifications_count ?? 3 }} nouvelles
                </span>
            </div>
            
            <!-- Liste des notifications récentes -->
            <div class="mt-3 space-y-2">
                <div class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    PCMA expiré pour 2 joueurs
                </div>
                <div class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-calendar text-blue-500 mr-2"></i>
                    Match demain à 15h00
                </div>
                <div class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-file-alt text-orange-500 mr-2"></i>
                    Feuille de match à préparer
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
                <button onclick="verifierEffectif()" 
                        class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Vérifier
                </button>
                <button onclick="exporterEffectif()" 
                        class="text-green-600 hover:text-green-800 text-sm">
                    <i class="fas fa-download mr-1"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifierEffectif() {
    if (window.CompetitionsApp) {
        window.CompetitionsApp.verifierEffectif();
    } else {
        alert('Vérification de l\'effectif en cours...');
    }
}

function exporterEffectif() {
    if (window.CompetitionsApp) {
        window.CompetitionsApp.exportRapport('effectif', 'excel');
    } else {
        alert('Export de l\'effectif en cours...');
    }
}
</script>
@endpush






