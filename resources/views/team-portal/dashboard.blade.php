@extends('layouts.app')

@section('title', 'Team Portal - Dashboard Technique')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚽</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Team Portal
                                </h1>
                                <p class="text-sm text-gray-600">Dashboard technique pour staffs d'équipe</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">⚽ Team Portal Dashboard</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Outil professionnel de gestion et d'analyse des équipes pour les staffs techniques
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Données temps réel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Analytics avancées
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">👥</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Équipes Actives</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $teams['active_teams'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Total: {{ $teams['total_teams'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🏃</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Joueurs Actifs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $clubStats['active_players'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Total: {{ $clubStats['total_players'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🏆</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Taux de Victoire</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $performanceMetrics['win_rate'] }}%</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Cette saison</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📊</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Note Moyenne</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($performanceMetrics['average_rating'], 1) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Performance</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Players by Position -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition par Position</h3>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-sm">📊</span>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="positionChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Performance Trends -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tendances de Performance</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">📈</span>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Training & Fitness Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Training Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Entraînement</h3>
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-orange-600 text-sm">🏃</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Sessions/semaine</span>
                        <span class="font-semibold text-gray-900">{{ $trainingData['training_sessions_week'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Taux de présence</span>
                        <span class="font-semibold text-green-600">{{ $trainingData['attendance_rate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Intensité moyenne</span>
                        <span class="font-semibold text-blue-600">{{ $trainingData['average_intensity'] }}/10</span>
                    </div>
                </div>
            </div>

            <!-- Fitness Status -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">État Physique</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">💪</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Joueurs en forme</span>
                        <span class="font-semibold text-green-600">{{ $clubStats['fit_players'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Joueurs blessés</span>
                        <span class="font-semibold text-red-600">{{ $clubStats['injured_players'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Niveau de forme</span>
                        <span class="font-semibold text-blue-600">{{ $trainingData['fitness_level'] }}/10</span>
                    </div>
                </div>
            </div>

            <!-- Match Analysis -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Analyse Matchs</h3>
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 text-sm">⚽</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Matchs ce mois</span>
                        <span class="font-semibold text-gray-900">{{ $performanceMetrics['matches_this_month'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Buts marqués</span>
                        <span class="font-semibold text-green-600">{{ $performanceMetrics['goals_scored'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Buts encaissés</span>
                        <span class="font-semibold text-red-600">{{ $performanceMetrics['goals_conceded'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers & Development -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Performers -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Top Performers</h3>
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <span class="text-yellow-600 text-sm">⭐</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($playerDevelopment['top_performers'] as $index => $player)
                        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-bold">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $player->position ?? 'Position N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900">{{ $player->overall_rating ?? 'N/A' }}</span>
                                <p class="text-xs text-gray-400">Note</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Young Talents -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Jeunes Talents</h3>
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-sm">🌟</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($playerDevelopment['young_talents'] as $index => $player)
                        <div class="flex items-center justify-between py-3 px-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-bold">{{ $player->age ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $player->position ?? 'Position N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-blue-600">{{ $player->potential_rating ?? 'N/A' }}</span>
                                <p class="text-xs text-gray-400">Potentiel</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Teams -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Équipes Récentes</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">⚽</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($teams['recent_teams'] as $team)
                    <div class="flex items-center justify-between py-4 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">⚽</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $team->name ?? 'Équipe sans nom' }}</p>
                                <p class="text-xs text-gray-500">{{ $team->category ?? 'Catégorie N/A' }} - {{ $team->club->name ?? 'Club N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ $team->status ?? 'N/A' }}</span>
                            <p class="text-xs text-gray-400">Statut</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Position Chart
const positionCtx = document.getElementById('positionChart').getContext('2d');
const positionChart = new Chart(positionCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($clubStats['players_by_position'] as $position)
            '{{ $position->position }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($clubStats['players_by_position'] as $position)
                {{ $position->count }},
                @endforeach
            ],
            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6'],
        datasets: [{
            label: 'Performance Moyenne',
            data: [7.2, 7.5, 7.8, 7.6, 7.9, 8.1],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Forme Physique',
            data: [8.1, 8.3, 8.0, 8.4, 8.2, 8.5],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>
@endsection
