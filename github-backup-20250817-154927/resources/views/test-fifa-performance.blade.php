<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système FIFA Connect</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">⚽ Test Système FIFA Connect</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulaire de test -->
            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">🎯 Tester un Joueur</h2>
                <form id="playerForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">ID du Joueur</label>
                        <input type="number" id="playerId" value="1" class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md font-medium">
                        Récupérer les Performances FIFA
                    </button>
                </form>
                
                <div id="loading" class="hidden mt-4 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-2 text-gray-400">Chargement des données FIFA...</p>
                </div>
            </div>
            
            <!-- Résultats -->
            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4">📊 Données FIFA Récupérées</h2>
                <div id="results" class="space-y-4">
                    <p class="text-gray-400">Entrez un ID de joueur et cliquez sur "Récupérer"</p>
                </div>
            </div>
        </div>
        
        <!-- Graphiques de performance -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">📈 Ratings FIFA</h3>
                <div class="h-64">
                    <canvas id="ratingsChart"></canvas>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">🎯 Statistiques de Match</h3>
                <div class="h-64">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let ratingsChart = null;
        let statsChart = null;
        
        document.getElementById('playerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const playerId = document.getElementById('playerId').value;
            const loading = document.getElementById('loading');
            const results = document.getElementById('results');
            
            loading.classList.remove('hidden');
            results.innerHTML = '<p class="text-gray-400">Chargement...</p>';
            
            try {
                const response = await fetch(`/api/player-performance/${playerId}`);
                const data = await response.json();
                
                displayResults(data);
                updateCharts(data);
                
            } catch (error) {
                results.innerHTML = `<p class="text-red-400">Erreur: ${error.message}</p>`;
            } finally {
                loading.classList.add('hidden');
            }
        });
        
        function displayResults(data) {
            const results = document.getElementById('results');
            
            const stats = data.stats;
            const player = data.player;
            
            results.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-900 rounded-lg p-4">
                        <h3 class="font-bold text-lg mb-2">${player.first_name} ${player.last_name}</h3>
                        <p class="text-blue-200">Position: ${player.position || 'Non définie'}</p>
                        <p class="text-blue-200">Club: ${player.club?.name || 'Non défini'}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-900 rounded-lg p-3 text-center">
                            <div class="text-2xl font-bold text-green-400">${stats.overall_rating}</div>
                            <div class="text-sm text-green-200">Rating Global</div>
                        </div>
                        <div class="bg-purple-900 rounded-lg p-3 text-center">
                            <div class="text-2xl font-bold text-purple-400">${stats.potential_rating}</div>
                            <div class="text-sm text-purple-200">Potentiel</div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Forme:</span>
                            <span class="font-bold">${stats.form_percentage}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Fitness:</span>
                            <span class="font-bold">${stats.fitness_score}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Morale:</span>
                            <span class="font-bold">${stats.morale_percentage}%</span>
                        </div>
                    </div>
                    
                    <div class="bg-gray-700 rounded-lg p-3">
                        <h4 class="font-bold mb-2">Statistiques de Match</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>Matchs: ${stats.matches_played}</div>
                            <div>Buts: ${stats.goals_scored}</div>
                            <div>Passes: ${stats.assists}</div>
                            <div>Précision tirs: ${stats.shot_accuracy}%</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function updateCharts(data) {
            const stats = data.stats;
            
            // Graphique des ratings
            const ratingsCtx = document.getElementById('ratingsChart');
            if (ratingsChart) ratingsChart.destroy();
            
            ratingsChart = new Chart(ratingsCtx, {
                type: 'radar',
                data: {
                    labels: ['Attaque', 'Défense', 'Physique', 'Technique', 'Mental'],
                    datasets: [{
                        label: 'Ratings FIFA',
                        data: [
                            stats.attacking_rating,
                            stats.defending_rating,
                            stats.physical_rating,
                            stats.technical_rating,
                            stats.mental_rating
                        ],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true, max: 100,
                            ticks: { stepSize: 20, color: '#D1D5DB' },
                            pointLabels: { color: '#D1D5DB' },
                            grid: { color: '#374151' }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: '#D1D5DB' } }
                    }
                }
            });
            
            // Graphique des statistiques
            const statsCtx = document.getElementById('statsChart');
            if (statsChart) statsChart.destroy();
            
            statsChart = new Chart(statsCtx, {
                type: 'bar',
                data: {
                    labels: ['Buts', 'Passes', 'Tirs Cadrés', 'Passes Réussies'],
                    datasets: [{
                        label: 'Performance',
                        data: [
                            stats.goals_scored,
                            stats.assists,
                            stats.shots_on_target,
                            stats.passes_completed
                        ],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#374151' }, 
                            ticks: { color: '#D1D5DB' } 
                        },
                        x: { 
                            grid: { color: '#374151' }, 
                            ticks: { color: '#D1D5DB' } 
                        }
                    },
                    plugins: {
                        legend: { labels: { color: '#D1D5DB' } }
                    }
                }
            });
        }
    </script>
</body>
</html>
