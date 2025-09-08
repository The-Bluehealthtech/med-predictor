@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📊</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    FIFA Analytics
                                </h1>
                                <p class="text-sm text-gray-600">Tableau de bord analytique</p>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">📊 FIFA Analytics Dashboard</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Tableau de bord analytique pour le suivi des données FIFA Connect
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Données en temps réel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
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
                            <p class="text-sm font-medium text-gray-500">Joueurs Totaux</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Player::count() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Total</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🏆</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Clubs Actifs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Club::count() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Actifs</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🌍</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Associations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Association::count() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">FIFA</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">⚽</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Matchs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\GameMatch::count() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Total</span>
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

            <!-- Players by Club -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Top 10 Clubs</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">🏆</span>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="clubChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Activité Récente</h3>
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 text-sm">📈</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-4 px-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">👤</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Nouveaux joueurs ajoutés</p>
                                <p class="text-xs text-gray-500">Cette semaine</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-green-600">+{{ \App\Models\Player::where('created_at', '>=', now()->subWeek())->count() }}</span>
                            <p class="text-xs text-gray-400">Nouveaux</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-4 px-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">🏆</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Clubs enregistrés</p>
                                <p class="text-xs text-gray-500">Total actif</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-blue-600">{{ \App\Models\Club::count() }}</span>
                            <p class="text-xs text-gray-400">Clubs</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-4 px-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">⚽</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Matchs programmés</p>
                                <p class="text-xs text-gray-500">Ce mois</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-purple-600">{{ \App\Models\GameMatch::where('match_date', '>=', now()->startOfMonth())->count() }}</span>
                            <p class="text-xs text-gray-400">Matchs</p>
                        </div>
                    </div>
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
        labels: ['Gardien', 'Défenseur', 'Milieu', 'Attaquant'],
        datasets: [{
            data: [
                {{ \App\Models\Player::where('position', 'Gardien')->count() }},
                {{ \App\Models\Player::where('position', 'Défenseur')->count() }},
                {{ \App\Models\Player::where('position', 'Milieu')->count() }},
                {{ \App\Models\Player::where('position', 'Attaquant')->count() }}
            ],
            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#EF4444'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Club Chart
const clubCtx = document.getElementById('clubChart').getContext('2d');
const clubChart = new Chart(clubCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(\App\Models\Club::withCount('players')->orderBy('players_count', 'desc')->limit(10)->pluck('name')) !!},
        datasets: [{
            label: 'Nombre de joueurs',
            data: {!! json_encode(\App\Models\Club::withCount('players')->orderBy('players_count', 'desc')->limit(10)->pluck('players_count')) !!},
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
@endsection
