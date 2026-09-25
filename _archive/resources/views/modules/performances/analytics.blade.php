<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Analytics - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">📈 Performance Analytics</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Retour aux Modules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Performance Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-lg">⚡</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Performances</p>
                        <p class="text-xl font-bold text-gray-900">{{ \App\Models\PlayerPerformance::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-green-600 text-lg">🏃</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Distance Moy.</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format(\App\Models\PlayerPerformance::avg('distance_covered') ?? 0, 1) }} km</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-yellow-600 text-lg">⚽</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Buts</p>
                        <p class="text-xl font-bold text-gray-900">{{ \App\Models\PlayerPerformance::sum('goals_scored') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-purple-600 text-lg">🎯</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Précision</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format(\App\Models\PlayerPerformance::avg('pass_accuracy') ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <!-- Performance Trends -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tendances de Performance</h3>
                <div class="h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Top Performers</h3>
                <div class="h-64">
                    <canvas id="topPerformersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Physical Metrics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Métriques Physiques</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Vitesse Max</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format(\App\Models\PlayerPerformance::max('max_speed') ?? 0, 1) }} km/h</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Accélérations</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('sprints_count') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Temps de Jeu</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format(\App\Models\PlayerPerformance::avg('minutes_played') ?? 0, 0) }} min</span>
                    </div>
                </div>
            </div>

            <!-- Technical Metrics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Métriques Techniques</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Passes Réussies</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('passes_completed') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Tirs Cadrés</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('shots_on_target') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Passes Clés</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('key_passes') }}</span>
                    </div>
                </div>
            </div>

            <!-- Defensive Metrics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Métriques Défensives</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Tacles</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('tackles_won') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Interceptions</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('interceptions') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Duels Gagnés</span>
                        <span class="text-sm font-semibold text-gray-900">{{ \App\Models\PlayerPerformance::sum('duels_won') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Performances Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Performances Récentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buts</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(\App\Models\PlayerPerformance::with(['player'])->latest()->limit(10)->get() as $performance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $performance->player->first_name ?? 'N/A' }} {{ $performance->player->last_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                Match {{ $performance->id ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ number_format($performance->overall_rating ?? 0, 1) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($performance->distance_covered ?? 0, 1) }} km
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $performance->goals_scored ?? 0 }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                Aucune performance récente trouvée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<script>
// Performance Trends Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
        datasets: [{
            label: 'Note Moyenne',
            data: [7.2, 7.5, 7.8, 7.6],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Distance Moyenne (km)',
            data: [8.5, 9.2, 8.8, 9.1],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Top Performers Chart
const topPerformersCtx = document.getElementById('topPerformersChart').getContext('2d');
const topPerformersChart = new Chart(topPerformersCtx, {
    type: 'bar',
    data: {
        labels: ['Joueur 1', 'Joueur 2', 'Joueur 3', 'Joueur 4', 'Joueur 5'],
        datasets: [{
            label: 'Note Moyenne',
            data: [8.5, 8.2, 8.0, 7.9, 7.8],
            backgroundColor: '#3B82F6'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
