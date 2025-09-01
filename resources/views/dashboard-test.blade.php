<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FIT</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">🏆 Dashboard FIT</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('modules.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            📋 Modules
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenu principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Hero Section -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Dashboard FIT</h2>
            <p class="text-xl text-gray-600">Plateforme complète de gestion du football avec modules médicaux, compétitions et analyses</p>
            </div>

            <!-- KPI Rapides -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Joueurs</p>
                            <p class="text-2xl font-semibold text-gray-900">25</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">🏟️</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Clubs</p>
                            <p class="text-2xl font-semibold text-gray-900">8</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Performance</p>
                            <p class="text-2xl font-semibold text-gray-900">87%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Budget</p>
                            <p class="text-2xl font-semibold text-gray-900">€120M</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Évolution des Effectifs</h3>
                    <div class="h-64">
                        <canvas id="playersChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🥧 Répartition par Position</h3>
                    <div class="h-64">
                        <canvas id="positionsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tableau de bord détaillé -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Effectif & Santé -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏥 Effectif & Santé</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-green-800">Joueurs aptes</span>
                            <span class="text-lg font-bold text-green-600">22</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <span class="text-sm font-medium text-orange-800">En récupération</span>
                            <span class="text-lg font-bold text-orange-600">2</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-medium text-red-800">Blessés</span>
                            <span class="text-lg font-bold text-red-600">1</span>
                        </div>
                    </div>
                </div>

                <!-- Performance Sportive -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">⚽ Performance Sportive</h3>
                    <div class="space-y-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">2.1</div>
                            <div class="text-sm text-blue-600">Points par match</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">+12</div>
                            <div class="text-sm text-green-600">Différence de buts</div>
                        </div>
                    </div>
                </div>

                <!-- Finance & Valeur -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Finance & Valeur</h3>
                    <div class="space-y-4">
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">85%</div>
                            <div class="text-sm text-purple-600">Exécution budget</div>
                        </div>
                        <div class="text-center p-3 bg-indigo-50 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">€45M</div>
                            <div class="text-sm text-indigo-600">Valeur marchande</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue d'Ensemble - KPI Rapides -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Vue d'Ensemble - KPI Rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Santé Globale du Club -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-white text-2xl font-bold">87%</span>
                            </div>
                            <div class="text-sm text-green-700 font-medium">SOLIDE</div>
                        </div>
                    </div>
                    
                    <!-- Profondeur de banc -->
                    <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-2xl font-bold text-blue-600">156</div>
                        <div class="text-sm text-gray-600">Profondeur de banc</div>
                    </div>
                    
                    <!-- Taux de blessures -->
                    <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="text-2xl font-bold text-orange-600">8.5%</div>
                        <div class="text-sm text-gray-600">Taux de blessures</div>
                    </div>
                    
                    <!-- Points par match -->
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-2xl font-bold text-green-600">2.1</div>
                        <div class="text-sm text-gray-600">Points par match</div>
                    </div>
                    
                    <!-- Valeur globale -->
                    <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="text-2xl font-bold text-purple-600">€45M</div>
                        <div class="text-sm text-gray-600">Valeur globale</div>
                    </div>
                </div>
            </div>

            <!-- Bloc Effectif & Santé -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Heatmap effectif par poste -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔥 Heatmap Effectif par Poste</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Attaquant</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-orange-500 rounded"></div>
                                <div class="w-3 h-3 bg-gray-300 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Milieu</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Défenseur</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-red-500 rounded"></div>
                                <div class="w-3 h-3 bg-gray-300 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Gardien</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-orange-500 rounded"></div>
                                <div class="w-3 h-3 bg-gray-300 rounded"></div>
                                <div class="w-3 h-3 bg-gray-300 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphique évolution blessures -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Évolution Blessures</h3>
                    <div class="h-32 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-4 bg-red-400 rounded-t" style="height: 60%"></div>
                        <div class="w-4 bg-orange-400 rounded-t" style="height: 40%"></div>
                        <div class="w-4 bg-yellow-400 rounded-t" style="height: 30%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 20%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 15%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 10%"></div>
                    </div>
                    <div class="text-xs text-gray-600 mt-2 text-center">Évolution sur 6 mois</div>
                </div>

                <!-- Joueurs à Risque -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Joueurs à Risque</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                            <span class="text-sm text-red-700">Mohamed Ben Ali</span>
                            <span class="text-xs text-red-600">Risque élevé</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-orange-50 rounded">
                            <span class="text-sm text-orange-700">Ahmed Khelifi</span>
                            <span class="text-xs text-orange-600">Attention</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                            <span class="text-sm text-yellow-700">Karim Mansouri</span>
                            <span class="text-xs text-yellow-600">Surveillance</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloc Performance Sportive -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Classement -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Classement</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                            <span class="text-sm font-medium">1. Équipe Senior</span>
                            <span class="text-sm text-blue-600">45 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm font-medium">2. Équipe U19</span>
                            <span class="text-sm text-gray-600">38 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm font-medium">3. Équipe Féminine</span>
                            <span class="text-sm text-gray-600">42 pts</span>
                        </div>
                    </div>
                </div>

                <!-- Courbe points par match -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Courbe Points par Match</h3>
                    <div class="h-32 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-4 bg-blue-400 rounded-t" style="height: 70%"></div>
                        <div class="w-4 bg-blue-400 rounded-t" style="height: 85%"></div>
                        <div class="w-4 bg-yellow-400 rounded-t" style="height: 60%"></div>
                        <div class="w-4 bg-blue-400 rounded-t" style="height: 90%"></div>
                        <div class="w-4 bg-blue-400 rounded-t" style="height: 75%"></div>
                    </div>
                    <div class="text-xs text-gray-600 mt-2 text-center">Évolution sur 5 matchs</div>
                </div>

                <!-- Statistiques Avancées -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Statistiques Avancées</h3>
                    <div class="space-y-3">
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <div class="font-semibold text-gray-900">+12</div>
                            <div class="text-sm text-gray-600">Différence de buts</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <div class="font-semibold text-gray-900">0.8</div>
                            <div class="text-sm text-gray-600">Ratio domicile</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloc Finance & Valeur -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Budget vs Réalisé -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Budget vs Réalisé</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Budget prévu</span>
                            <span class="text-sm font-semibold text-gray-900">€120M</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Dépenses réelles</span>
                            <span class="text-sm font-semibold text-green-600">€102M</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                        <div class="text-xs text-gray-600 text-center">Exécution: 85%</div>
                    </div>
                </div>

                <!-- Valeur Marchande -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💎 Valeur Marchande</h3>
                    <div class="space-y-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">€45M</div>
                            <div class="text-sm text-gray-600">Valeur actuelle</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-semibold text-green-600">+12.5%</div>
                            <div class="text-sm text-gray-600">Croissance annuelle</div>
                        </div>
                    </div>
                </div>

                <!-- Projection valorisation -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Projection Valorisation</h3>
                    <div class="h-32 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-4 bg-indigo-400 rounded-t" style="height: 60%"></div>
                        <div class="w-4 bg-indigo-400 rounded-t" style="height: 70%"></div>
                        <div class="w-4 bg-indigo-400 rounded-t" style="height: 80%"></div>
                        <div class="w-4 bg-indigo-400 rounded-t" style="height: 85%"></div>
                        <div class="w-4 bg-indigo-400 rounded-t" style="height: 90%"></div>
                    </div>
                    <div class="text-xs text-gray-600 mt-2 text-center">Projection 5 ans</div>
                </div>
            </div>

            <!-- Prospective & Alertes -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Pipeline Jeunes Talents -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🌟 Pipeline Jeunes Talents</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                            <span class="text-sm">U15 → U17</span>
                            <span class="text-sm font-semibold text-blue-600">8 talents</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                            <span class="text-sm">U17 → U19</span>
                            <span class="text-sm font-semibold text-green-600">5 talents</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-purple-50 rounded">
                            <span class="text-sm">U19 → Senior</span>
                            <span class="text-sm font-semibold text-purple-600">3 talents</span>
                        </div>
                    </div>
                </div>

                <!-- Prévisions Classement -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔮 Prévisions Classement</h3>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-lg font-bold text-green-700">2ème Place</div>
                        <div class="text-sm text-gray-600">Prévision finale 2024</div>
                    </div>
                    <div class="h-20 bg-gray-50 rounded flex items-end justify-around p-2 mt-3">
                        <div class="w-4 bg-green-400 rounded-t" style="height: 65%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 70%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 75%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 80%"></div>
                    </div>
                </div>

                <!-- Alertes Intelligentes -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Alertes Intelligentes</h3>
                    <div class="space-y-3">
                        <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded">
                            <div class="text-sm font-medium text-red-800">Risque de blessure accru</div>
                            <div class="text-xs text-red-600">1 joueur clé surmené</div>
                        </div>
                        <div class="p-3 bg-orange-50 border-l-4 border-orange-500 rounded">
                            <div class="text-sm font-medium text-orange-800">Profondeur insuffisante</div>
                            <div class="text-xs text-orange-600">Poste attaquant critique</div>
                        </div>
                        <div class="p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                            <div class="text-sm font-medium text-yellow-800">Budget sous-exécuté</div>
                            <div class="text-xs text-yellow-600">Exécution à 85%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique 1: Évolution des effectifs
        const playersCtx = document.getElementById('playersChart').getContext('2d');
        new Chart(playersCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Effectifs',
                    data: [45, 52, 58, 61, 67, 73],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#374151' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#374151' },
                        grid: { color: 'rgba(55,65,81,0.1)' }
                    },
                    x: {
                        ticks: { color: '#374151' },
                        grid: { color: 'rgba(55,65,81,0.1)' }
                    }
                }
            }
        });

        // Graphique 2: Répartition par position
        const positionsCtx = document.getElementById('positionsChart').getContext('2d');
        new Chart(positionsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Attaquant', 'Milieu', 'Défenseur', 'Gardien'],
                datasets: [{
                    data: [25, 30, 25, 20],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#374151' }
                    }
                }
            }
        });

        console.log('🏆 Dashboard FIT chargé avec succès');
    </script>
</body>
</html>
