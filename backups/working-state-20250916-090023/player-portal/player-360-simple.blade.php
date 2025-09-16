<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name ?? 'Joueur' }} {{ $player->last_name ?? '' }} - Fiche Joueur 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .player-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .stat-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 30px rgba(102, 126, 234, 0.7); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('player-portal.dashboard') }}" class="flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au Dashboard
            </a>
            <div class="text-sm text-gray-500">
                Fiche Joueur 360° - {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        @if($player)
        <!-- Hero Zone - FIFA Ultimate Team Card -->
        <div class="player-card rounded-3xl p-8 mb-8 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white transform translate-x-32 -translate-y-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-white transform -translate-x-16 translate-y-16"></div>
            </div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Section Photo & Rating (4 colonnes) -->
                <div class="lg:col-span-4">
                    <div class="flex flex-col items-center lg:items-start">
                        <!-- Photo du joueur -->
                        <div class="relative mb-6">
                            <img src="/images/ronaldo.jpg" 
                                 alt="Photo de Cristiano Ronaldo"
                                 class="w-48 h-60 object-cover rounded-2xl border-4 border-white/30 shadow-2xl pulse-glow"
                                 onerror="this.src='/images/default_player.svg'"
                                 loading="lazy">
                            
                            <!-- Rating FIFA en overlay -->
                            <div class="absolute -top-4 -right-4 bg-gradient-to-r from-yellow-400 to-orange-500 w-20 h-20 flex items-center justify-center rounded-2xl shadow-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-black text-black">{{ $player->overall_rating ?? 75 }}</div>
                                    <div class="text-xs font-bold text-black">OVR</div>
                                </div>
                            </div>

                            <!-- Position en overlay -->
                            <div class="absolute -bottom-4 -left-4 bg-white/90 text-gray-800 px-4 py-2 rounded-xl font-bold shadow-lg">
                                {{ $player->position ?? 'CAM' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Identité & Infos (5 colonnes) -->
                <div class="lg:col-span-5">
                    <div class="space-y-6">
                        <!-- Nom et club -->
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-black mb-2 leading-none">
                                {{ strtoupper($player->last_name ?? 'PLAYER') }}
                            </h1>
                            <h2 class="text-2xl lg:text-3xl font-bold text-white/90 mb-4">
                                {{ $player->first_name ?? 'First' }}
                            </h2>
                            
                            <!-- Club et nationalité -->
                            <div class="flex items-center space-x-4 mb-6">
                                @if($player->club ?? false)
                                    <div class="flex items-center space-x-2">
                                                                            <img src="/images/chelsea_logo.png" 
                                         alt="Logo Chelsea FC" 
                                         class="w-10 h-10 object-contain rounded shadow-md"
                                         onerror="this.src='/images/default_club.svg'"
                                         loading="lazy">
                                        <span class="font-semibold">{{ $player->club->name ?? 'Free Agent' }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center space-x-2">
                                    <img src="/images/portugal_flag.png" 
                                         alt="Drapeau Portugal" 
                                         class="w-8 h-6 rounded shadow-sm object-cover"
                                         onerror="this.src='/images/default_flag.svg'"
                                         loading="lazy">
                                    <span class="font-semibold">{{ $player->nationality ?? 'International' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques FIFA -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $player->age ?? 25 }}</div>
                                <div class="text-sm opacity-75">ÂGE</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $player->height ?? 180 }}</div>
                                <div class="text-sm opacity-75">TAILLE</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $player->weak_foot ?? 3 }}<span class="text-lg">★</span></div>
                                <div class="text-sm opacity-75">PIED FAIBLE</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Score de Santé FIT (3 colonnes) -->
                <div class="lg:col-span-3">
                    <div class="text-center space-y-6">
                        <!-- Score de santé FIT -->
                        <div class="bg-white/20 rounded-2xl p-6 backdrop-blur-sm">
                            <div class="text-sm font-bold opacity-75 mb-2">SCORE DE SANTÉ FIT</div>
                            <div class="text-6xl font-black mb-2 text-green-400">
                                {{ $player->ghs_overall_score ?? 85 }}
                            </div>
                            <div class="text-sm opacity-75 mb-4">/ 100</div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>💚 Santé: {{ $player->ghs_physical_score ?? 85 }}</div>
                                <div>🧠 Mental: {{ $player->ghs_mental_score ?? 78 }}</div>
                                <div>😴 Sommeil: {{ $player->ghs_sleep_score ?? 81 }}</div>
                                <div>🤝 Social: {{ $player->ghs_civic_score ?? 92 }}</div>
                            </div>
                        </div>

                        <!-- Risque de blessure -->
                        <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                            <div class="text-xs font-bold opacity-75 mb-1">RISQUE BLESSURE</div>
                            <div class="text-2xl font-black text-green-400">{{ $player->injury_risk_score ?? 15 }}%</div>
                            <div class="text-xs font-semibold text-green-400">{{ $player->injury_risk_level ?? 'FAIBLE' }}</div>
                        </div>

                        <!-- Valeur marchande -->
                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-4 text-black">
                            <div class="text-xs font-bold mb-1">VALEUR ESTIMÉE</div>
                            <div class="text-lg font-black">€{{ number_format(($player->value_eur ?? 1500000) / 1000000, 1) }}M</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-8" id="fifa-nav">
            <div class="flex flex-wrap justify-center space-x-2 p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <button class="px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 bg-white/90 border border-blue-900/40" onclick="showTab('performance')">
                    <i class="fas fa-chart-line mr-2"></i>
                    Performance
                </button>
                <button class="px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 hover:bg-white/80 bg-white/60" onclick="showTab('health')">
                    <i class="fas fa-heartbeat mr-2"></i>
                    Santé & Bien-être
                </button>
                <button class="px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 hover:bg-white/80 bg-white/60" onclick="showTab('history')">
                    <i class="fas fa-history mr-2"></i>
                    Historique
                </button>
                <button class="px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 hover:bg-white/80 bg-white/60" onclick="showTab('market')">
                    <i class="fas fa-trending-up mr-2"></i>
                    Marché & Tendances
                </button>
            </div>
        </div>

        <!-- Contenu des Onglets -->
        <div class="space-y-8">
            <!-- Onglet Performance -->
            <div data-tab="performance" class="space-y-6">
                <h2 class="text-3xl font-bold text-blue-900 mb-6">Performance & Évolution</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Graphique d'évolution des performances -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Évolution des Performances</h3>
                        <div class="h-64 w-full">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Comparaison par rapport à la moyenne -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Comparaison vs Moyenne</h3>
                        <div class="h-64 w-full">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Santé & Bien-être -->
            <div data-tab="health" class="space-y-6" style="display: none;">
                <h2 class="text-3xl font-bold text-blue-900 mb-6">Santé & Bien-être</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Score de santé dans le temps -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Évolution du Score FIT</h3>
                        <div class="h-64 w-full">
                            <canvas id="healthScoreChart"></canvas>
                        </div>
                    </div>

                    <!-- Répartition des scores de santé -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Répartition Santé</h3>
                        <div class="h-64 w-full">
                            <canvas id="healthBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Historique -->
            <div data-tab="history" class="space-y-6" style="display: none;">
                <h2 class="text-3xl font-bold text-blue-900 mb-6">Historique</h2>
                
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Timeline de Carrière</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-blue-600">2024</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold">{{ $player->club->name ?? 'Club Actuel' }}</div>
                                <div class="text-sm text-gray-600">Saison 2023-24</div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="font-bold text-green-600">8</div>
                                    <div class="text-xs text-gray-500">Buts</div>
                                </div>
                                <div>
                                    <div class="font-bold text-blue-600">12</div>
                                    <div class="text-xs text-gray-500">Passes</div>
                                </div>
                                <div>
                                    <div class="font-bold text-purple-600">25</div>
                                    <div class="text-xs text-gray-500">Matchs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Marché & Tendances -->
            <div data-tab="market" class="space-y-6" style="display: none;">
                <h2 class="text-3xl font-bold text-blue-900 mb-6">Marché & Tendances</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Évolution de la valeur marchande -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Évolution Valeur Marchande</h3>
                        <div class="h-64 w-full">
                            <canvas id="marketValueChart"></canvas>
                        </div>
                    </div>

                    <!-- Prédictions futures -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Prédictions Futures</h3>
                        <div class="h-64 w-full">
                            <canvas id="predictionsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections d'informations d'origine -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <!-- Informations générales -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Informations Générales</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">FIFA Connect ID</span>
                        <span class="font-medium font-mono text-sm">{{ $player->fifa_connect_id ?? 'Non attribué' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Date de naissance</span>
                        <span class="font-medium">{{ $player->date_of_birth ? $player->date_of_birth->format('d/m/Y') : 'Non définie' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pied préféré</span>
                        <span class="font-medium">{{ $player->preferred_foot ?? 'Non défini' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Valeur marchande</span>
                        <span class="font-medium">€{{ number_format($player->value_eur ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Club actuel -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Club Actuel</h2>
                @if($player->club ?? false)
                <div class="flex items-center space-x-4 mb-4">
                    @if($player->club ?? false)
                        <img src="/images/chelsea_logo.png" 
                             alt="Logo Chelsea FC" 
                             class="w-16 h-16 object-contain"
                             onerror="this.src='/images/default_club.svg'"
                             loading="lazy">
                    @endif
                    <div>
                        <div class="font-medium text-lg">{{ $player->club->name ?? 'Nom du club' }}</div>
                        <div class="text-gray-600">{{ $player->club->association->name ?? 'Fédération' }}</div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Contrat jusqu'au</span>
                        <span class="font-medium">{{ $player->contract_valid_until ? $player->contract_valid_until->format('d/m/Y') : 'Non défini' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Salaire</span>
                        <span class="font-medium">€{{ number_format($player->wage_eur ?? 0) }}</span>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-3xl mb-2"></i>
                    <p>Aucun club associé</p>
                </div>
                @endif
            </div>

            <!-- Données de santé -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Données de Santé</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Dossiers médicaux</span>
                        <span class="font-bold text-blue-600">{{ $player->healthRecords->count() ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Évaluations PCMA</span>
                        <span class="font-bold text-green-600">{{ $player->pcmas->count() ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Prédictions médicales</span>
                        <span class="font-bold text-purple-600">{{ $player->medicalPredictions->count() ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Risque de blessure</span>
                        <span class="font-bold text-green-600">{{ $player->injury_risk_score ?? 15 }}% (FAIBLE)</span>
                    </div>
                </div>
            </div>

            <!-- Performances -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Performances</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Évaluations de performance</span>
                        <span class="font-bold text-orange-600">{{ $player->performances->count() ?? 0 }}</span>
                    </div>
                    @if($player->performances->count() > 0)
                        @php $latestPerf = $player->performances->first() @endphp
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-xl font-bold text-blue-600">{{ $latestPerf->endurance_score ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">Endurance</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-green-600">{{ $latestPerf->technical_score ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">Technique</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-gray-500">
                            <p>Aucune donnée de performance disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @else
        <!-- Message d'erreur si pas de joueur -->
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                <div>
                    <strong>Erreur d'accès :</strong> Aucun joueur associé à votre compte.
                    <p class="text-sm mt-1">Contactez l'administrateur pour résoudre ce problème.</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        let activeTab = 'performance';
        let charts = {};

        function showTab(tabId) {
            // Masquer tous les onglets
            document.querySelectorAll('[data-tab]').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Afficher l'onglet sélectionné
            const targetTab = document.querySelector(`[data-tab="${tabId}"]`);
            if (targetTab) {
                targetTab.style.display = 'block';
            }
            
            // Mettre à jour l'état actif des boutons
            document.querySelectorAll('button[onclick^="showTab"]').forEach(btn => {
                btn.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 hover:bg-white/80 bg-white/60';
            });
            
            event.target.className = 'px-6 py-3 rounded-xl font-medium transition-all duration-300 text-blue-900 bg-white/90 border border-blue-900/40';
            
            activeTab = tabId;
        }

        // Initialiser les charts quand le DOM est prêt
        document.addEventListener('DOMContentLoaded', function() {
            console.log('FIFA Ultimate Dashboard chargé avec Chart.js');
            
            // Créer les charts avec des données dynamiques
            createPerformanceChart();
            createComparisonChart();
            createHealthScoreChart();
            createHealthBreakdownChart();
            createMarketValueChart();
            createPredictionsChart();
            
            // Afficher le premier onglet par défaut
            showTab('performance');
        });

        function createPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;

            charts.performance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Performance Global',
                        data: [75, 78, 76, 82, 85, 88],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Forme Physique',
                        data: [80, 82, 79, 85, 87, 90],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'top',
                                    labels: { boxWidth: 12, font: { size: 11 } }
                                }
                            },
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    max: 100,
                                    ticks: { font: { size: 10 } }
                                },
                                x: {
                                    ticks: { font: { size: 10 } }
                                }
                            }
                        }
            });
        }

        function createComparisonChart() {
            const ctx = document.getElementById('comparisonChart');
            if (!ctx) return;

            charts.comparison = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Vitesse', 'Tir', 'Passe', 'Dribble', 'Défense', 'Physique'],
                    datasets: [{
                        label: 'Joueur',
                        data: [85, 78, 82, 75, 65, 80],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.2)',
                        pointBackgroundColor: '#8b5cf6'
                    }, {
                        label: 'Moyenne Position',
                        data: [75, 70, 75, 70, 60, 75],
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.1)',
                        pointBackgroundColor: '#6b7280'
                    }]
                },
                                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: { boxWidth: 12, font: { size: 11 } }
                                }
                            },
                            scales: { 
                                r: { 
                                    beginAtZero: true, 
                                    max: 100,
                                    ticks: { font: { size: 9 } }
                                } 
                            }
                        }
            });
        }

        function createHealthScoreChart() {
            const ctx = document.getElementById('healthScoreChart');
            if (!ctx) return;

            charts.healthScore = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Score FIT Global',
                        data: [82, 85, 83, 87, 89, {{ $player->ghs_overall_score ?? 85 }}],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }

        function createHealthBreakdownChart() {
            const ctx = document.getElementById('healthBreakdownChart');
            if (!ctx) return;

            charts.healthBreakdown = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Santé Physique', 'Santé Mentale', 'Sommeil', 'Social'],
                    datasets: [{
                        data: [{{ $player->ghs_physical_score ?? 85 }}, {{ $player->ghs_mental_score ?? 78 }}, {{ $player->ghs_sleep_score ?? 81 }}, {{ $player->ghs_civic_score ?? 92 }}],
                        backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function createMarketValueChart() {
            const ctx = document.getElementById('marketValueChart');
            if (!ctx) return;

            charts.marketValue = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Valeur (millions €)',
                        data: [1.2, 1.35, 1.28, 1.45, 1.5, {{ ($player->value_eur ?? 1500000) / 1000000 }}],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function createPredictionsChart() {
            const ctx = document.getElementById('predictionsChart');
            if (!ctx) return;

            charts.predictions = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Court terme', 'Moyen terme', 'Long terme'],
                    datasets: [{
                        label: 'Performance Prédite',
                        data: [88, 92, 85],
                        backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6']
                    }, {
                        label: 'Risque Blessure (%)',
                        data: [{{ $player->injury_risk_score ?? 15 }}, 25, 35],
                        backgroundColor: ['#f59e0b', '#ef4444', '#dc2626']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }
    </script>
</body>
</html>
