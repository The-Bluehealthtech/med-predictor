<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système de Performances</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">🎯 Test du Système de Performances</h1>
        
        <!-- Système de Performances Enrichi -->
        <div id="performance-dashboard">
            <!-- Navigation des onglets de performance -->
            <div class="performance-tabs mb-6">
                <div class="flex space-x-1 bg-gray-800 rounded-lg p-1">
                    <button 
                        onclick="changePerformanceTab('overview')"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-blue-600 text-white shadow-lg"
                    >
                        <i class="fas fa-chart-line mr-2"></i>Vue d'ensemble
                    </button>
                    <button 
                        onclick="changePerformanceTab('advanced')"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                    >
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques avancées
                    </button>
                    <button 
                        onclick="changePerformanceTab('match')"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                    >
                        <i class="fas fa-futbol mr-2"></i>Statistiques de match
                    </button>
                    <button 
                        onclick="changePerformanceTab('comparison')"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                    >
                        <i class="fas fa-balance-scale mr-2"></i>Analyse comparative
                    </button>
                    <button 
                        onclick="changePerformanceTab('trends')"
                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                    >
                        <i class="fas fa-trending-up mr-2"></i>Tendances
                    </button>
                </div>
            </div>

            <!-- Contenu des onglets de performance -->
            <div class="tab-content">
                <!-- Vue d'ensemble -->
                <div id="overview-tab" class="space-y-6">
                    <!-- En-tête des Performances -->
                    <div class="performance-header bg-gradient-to-r from-blue-900 to-purple-900 rounded-xl p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white mb-2">
                                    <i class="fas fa-chart-line mr-3"></i>Centre de Performances
                                </h2>
                                <p class="text-blue-200">Analyse complète des performances et statistiques</p>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-bold text-yellow-400">88</div>
                                <div class="text-sm text-blue-200">Rating Global</div>
                            </div>
                        </div>
                    </div>

                    <!-- Grille principale des performances -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Statistiques Offensives -->
                        <div class="bg-gray-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-red-400">
                                <i class="fas fa-bullseye mr-2"></i>Statistiques Offensives
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Buts marqués</span>
                                    <span class="text-2xl font-bold text-red-400">24</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Passes décisives</span>
                                    <span class="text-2xl font-bold text-blue-400">18</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Tirs cadrés</span>
                                    <span class="text-2xl font-bold text-yellow-400">67</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Précision des tirs</span>
                                    <span class="text-2xl font-bold text-green-400">78%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques Défensives -->
                        <div class="bg-gray-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-blue-400">
                                <i class="fas fa-shield-alt mr-2"></i>Statistiques Défensives
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Tacles réussis</span>
                                    <span class="text-2xl font-bold text-blue-400">45</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Interceptions</span>
                                    <span class="text-2xl font-bold text-green-400">32</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Dégagements</span>
                                    <span class="text-2xl font-bold text-yellow-400">28</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Duels gagnés</span>
                                    <span class="text-2xl font-bold text-purple-400">156</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques Physiques -->
                        <div class="bg-gray-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-green-400">
                                <i class="fas fa-running mr-2"></i>Statistiques Physiques
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Distance parcourue</span>
                                    <span class="text-2xl font-bold text-green-400">12.8km</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Vitesse max</span>
                                    <span class="text-2xl font-bold text-yellow-400">34.2km/h</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Sprint</span>
                                    <span class="text-2xl font-bold text-red-400">89</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Endurance</span>
                                    <span class="text-2xl font-bold text-blue-400">92%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques de performance -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Évolution des performances -->
                        <div class="bg-gray-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-blue-300">
                                <i class="fas fa-chart-line mr-2"></i>Évolution des Performances
                            </h3>
                            <div class="h-64">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>

                        <!-- Radar des compétences -->
                        <div class="bg-gray-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-green-300">
                                <i class="fas fa-radar-chart mr-2"></i>Radar des Compétences
                            </h3>
                            <div class="h-64">
                                <canvas id="skillsRadar"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques avancées -->
                <div id="advanced-tab" class="space-y-6" style="display: none;">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4 text-blue-300">
                            <i class="fas fa-chart-bar mr-2"></i>Statistiques Avancées
                        </h3>
                        <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                    </div>
                </div>

                <!-- Statistiques de match -->
                <div id="match-tab" class="space-y-6" style="display: none;">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4 text-green-300">
                            <i class="fas fa-futbol mr-2"></i>Statistiques de Match
                        </h3>
                        <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                    </div>
                </div>

                <!-- Analyse comparative -->
                <div id="comparison-tab" class="space-y-6" style="display: none;">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4 text-purple-300">
                            <i class="fas fa-balance-scale mr-2"></i>Analyse Comparative
                        </h3>
                        <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                    </div>
                </div>

                <!-- Tendances -->
                <div id="trends-tab" class="space-y-6" style="display: none;">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4 text-orange-300">
                            <i class="fas fa-trending-up mr-2"></i>Analyse des Tendances
                        </h3>
                        <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gestion des onglets de performance
        function changePerformanceTab(tabId) {
            console.log('Changement vers l\'onglet:', tabId);
            
            // Masquer tous les contenus
            const tabContents = document.querySelectorAll('#performance-dashboard .tab-content > div');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Afficher le contenu de l'onglet sélectionné
            const selectedContent = document.getElementById(tabId + '-tab');
            if (selectedContent) {
                selectedContent.style.display = 'block';
            }
            
            // Mettre à jour les boutons actifs
            const tabButtons = document.querySelectorAll('#performance-dashboard .performance-tabs button');
            tabButtons.forEach(button => {
                button.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
                button.classList.add('text-gray-400', 'hover:text-gray-200', 'hover:bg-gray-700');
            });
            
            const activeButton = document.querySelector(`#performance-dashboard .performance-tabs button[onclick="changePerformanceTab('${tabId}')"]`);
            if (activeButton) {
                activeButton.classList.remove('text-gray-400', 'hover:text-gray-200', 'hover:bg-gray-700');
                activeButton.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            }
        }

        // Initialisation des graphiques
        function initCharts() {
            const performanceCtx = document.getElementById('performanceChart');
            if (performanceCtx) {
                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6', 'J7', 'J8'],
                        datasets: [{
                            label: 'Rating Match',
                            data: [8.2, 7.8, 9.1, 8.5, 7.9, 8.8, 9.2, 8.7],
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Buts + Passes',
                            data: [2, 1, 3, 2, 1, 2, 3, 2],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#D1D5DB' } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#374151' }, ticks: { color: '#D1D5DB' } },
                            x: { grid: { color: '#374151' }, ticks: { color: '#D1D5DB' } }
                        }
                    }
                });
            }

            const skillsCtx = document.getElementById('skillsRadar');
            if (skillsCtx) {
                new Chart(skillsCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Vitesse', 'Technique', 'Physique', 'Mental', 'Tactique', 'Finition'],
                        datasets: [{
                            label: 'Compétences actuelles',
                            data: [88, 92, 85, 90, 87, 94],
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 244, 0.2)',
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { labels: { color: '#D1D5DB' } } },
                        scales: {
                            r: {
                                beginAtZero: true, max: 100, grid: { color: '#374151' },
                                ticks: { color: '#D1D5DB', stepSize: 20 },
                                pointLabels: { color: '#D1D5DB' }
                            }
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Système de performances chargé avec succès');
            setTimeout(initCharts, 100);
        });
    </script>
</body>
</html>
