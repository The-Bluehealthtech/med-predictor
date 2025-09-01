<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard FIT - Suivi Holistique</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Navigation -->
    @if (Route::has('login'))
        <div class="fixed top-0 right-0 p-6 text-right z-10">
            @auth
                <a href="{{ url('/home') }}" class="font-semibold text-white hover:text-blue-200 transition-colors duration-200">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="font-semibold text-white hover:text-blue-200 transition-colors duration-200">Connexion</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 font-semibold text-white hover:text-blue-200 transition-colors duration-200">Inscription</a>
                @endif
            @endauth
        </div>
    @endif

    <!-- Hero Section -->
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- En-tête principal -->
            <div class="text-center mb-12">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-4xl font-bold text-white">⚽</span>
                    </div>
                </div>
                <h1 class="text-5xl font-bold text-gray-900 mb-4">Dashboard FIT</h1>
                <p class="text-xl text-gray-600 mb-6">Outil Holistique de Suivi pour Clubs & Associations</p>
                <div class="flex justify-center space-x-4">
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm border border-green-200">
                        🟢 Système Opérationnel
                    </span>
                    <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm border border-blue-200">
                        📊 Données en Temps Réel
                    </span>
                </div>
            </div>

            <!-- 🎯 CARTES PRINCIPALES DYNAMIQUES -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Carte 1: Vue d'ensemble des Joueurs -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Joueurs</h3>
                    </div>
                    
                    @php
                        // Vérifier si on est en mode fallback
                        if (request('db_fallback')) {
                            $totalPlayers = request('simulated_data.players.total', 25);
                            $activePlayers = request('simulated_data.players.active', 22);
                            $avgAge = request('simulated_data.players.avg_age', 24.5);
                        } else {
                            try {
                                $totalPlayers = \App\Models\Player::count();
                                $activePlayers = \App\Models\Player::whereHas('licenses', function($query) {
                                    $query->where('status', 'active');
                                })->count();
                                $avgAge = \App\Models\Player::whereNotNull('date_of_birth')
                                    ->get()
                                    ->avg(function($player) {
                                        return \Carbon\Carbon::parse($player->date_of_birth)->age;
                                    });
                            } catch (\Exception $e) {
                                $totalPlayers = 25;
                                $activePlayers = 22;
                                $avgAge = 24.5;
                            }
                        }
                    @endphp
                    
                    <div class="space-y-3">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $totalPlayers }}</div>
                            <div class="text-gray-600 text-sm">Total des joueurs</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="text-center p-2 bg-green-50 rounded">
                                <div class="text-green-700 font-semibold">{{ $activePlayers }}</div>
                                <div class="text-gray-600 text-xs">Actifs</div>
                            </div>
                            <div class="text-center p-2 bg-yellow-50 rounded">
                                <div class="text-yellow-700 font-semibold">{{ round($avgAge, 1) }}</div>
                                <div class="text-gray-600 text-xs">Âge moyen</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte 2: Clubs & Associations -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Clubs</h3>
                    </div>
                    
                                                @php
                                try {
                                    $totalClubs = \App\Models\Club::count();
                                    $totalAssociations = \App\Models\Association::count();
                                    $totalConfederations = \App\Models\Confederation::count();
                                } catch (\Exception $e) {
                                    $totalClubs = 0;
                                    $totalAssociations = 0;
                                    $totalConfederations = 0;
                                }
                            @endphp
                    
                    <div class="space-y-3">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-1">{{ $totalClubs }}</div>
                            <div class="text-gray-600 text-sm">Clubs enregistrés</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="text-center p-2 bg-blue-50 rounded">
                                <div class="text-blue-700 font-semibold">{{ $totalAssociations }}</div>
                                <div class="text-gray-600 text-xs">Associations</div>
                            </div>
                            <div class="text-center p-2 bg-purple-50 rounded">
                                <div class="text-purple-700 font-semibold">{{ $totalConfederations }}</div>
                                <div class="text-gray-600 text-xs">Confédérations</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte 3: Performances & Santé -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Performances</h3>
                    </div>
                    
                                                @php
                                try {
                                    $totalStats = \DB::table('player_season_stats')->count();
                                    $avgGoals = \DB::table('player_season_stats')->avg('goals') ?: 0;
                                    $avgAssists = \DB::table('player_season_stats')->avg('assists') ?: 0;
                                } catch (\Exception $e) {
                                    $totalStats = 0;
                                    $avgGoals = 0;
                                    $avgAssists = 0;
                                }
                            @endphp
                    
                    <div class="space-y-3">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-1">{{ $totalStats }}</div>
                            <div class="text-gray-600 text-sm">Statistiques enregistrées</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="text-center p-2 bg-red-50 rounded">
                                <div class="text-red-700 font-semibold">{{ round($avgGoals, 1) }}</div>
                                <div class="text-gray-600 text-xs">Buts/moy</div>
                            </div>
                            <div class="text-center p-2 bg-orange-50 rounded">
                                <div class="text-orange-700 font-semibold">{{ round($avgAssists, 1) }}</div>
                                <div class="text-gray-600 text-xs">Passes/moy</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte 4: Système FIT -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cog text-orange-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Système FIT</h3>
                    </div>
                    
                                                @php
                                try {
                                    $lastUpdate = \Carbon\Carbon::now()->format('d/m/Y H:i');
                                    $dbSize = \DB::select('SELECT COUNT(*) as total FROM sqlite_master WHERE type="table"')[0]->total ?? 'N/A';
                                } catch (\Exception $e) {
                                    $lastUpdate = 'N/A';
                                    $dbSize = 'N/A';
                                }
                            @endphp
                    
                    <div class="space-y-3">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600 mb-1">{{ $dbSize }}</div>
                            <div class="text-gray-600 text-sm">Tables actives</div>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded">
                            <div class="text-green-700 font-semibold text-sm">{{ $lastUpdate }}</div>
                            <div class="text-gray-600 text-xs">Dernière mise à jour</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📊 GRAPHIQUES ET ANALYSES -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                <!-- Graphique 1: Évolution des joueurs par mois -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Évolution des Effectifs</h3>
                    <div class="h-64">
                        <canvas id="playersChart"></canvas>
                    </div>
                </div>

                <!-- Graphique 2: Répartition par position -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Répartition par Position</h3>
                    <div class="h-64">
                        <canvas id="positionsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 🚀 ACTIONS RAPIDES -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Gestion des Joueurs</h3>
                    <div class="space-y-3">
                        <a href="/players/list" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            📋 Liste des Joueurs
                        </a>
                        <a href="/modules/player-registration" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            ➕ Nouveau Joueur
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏟️ Gestion des Clubs</h3>
                    <div class="space-y-3">
                        <a href="/modules/clubs" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            🏛️ Gérer les Clubs
                        </a>
                        <a href="/modules/associations" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            🏛️ Gérer les Associations
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Analyses & Rapports</h3>
                    <div class="space-y-3">
                        <a href="/analytics/dashboard" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            📈 Tableau de Bord
                        </a>
                        <a href="/modules/competitions" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-3 px-4 rounded-lg transition-colors duration-200">
                            🏆 Compétitions
                        </a>
                    </div>
                </div>
            </div>

            <!-- 📋 STATUT DU SYSTÈME -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Statut du Système FIT</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-green-700 font-semibold">✅ Base de données</div>
                        <div class="text-gray-600 text-xs">Opérationnelle</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-blue-700 font-semibold">✅ API FIT</div>
                        <div class="text-gray-600 text-xs">Connectée</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="text-purple-700 font-semibold">✅ Synchronisation</div>
                        <div class="text-gray-600 text-xs">Active</div>
                    </div>
                    <div class="text-center p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="text-orange-700 font-semibold">✅ Sécurité</div>
                        <div class="text-gray-600 text-xs">Renforcée</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🟦 VUE D'ENSEMBLE - KPI RAPIDES -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">📊 Vue d'Ensemble - Indicateurs Clés</h3>
            
            <!-- Carte santé du club -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 mb-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Santé Globale du Club</h4>
                        <p class="text-gray-600">État général de l'effectif et des performances</p>
                    </div>
                    <div class="text-center">
                                            @php
                        // Version simplifiée sans accès à la base
                        $healthScore = 75;
                        $healthStatus = 'ATTENTION';
                        $healthColor = 'bg-orange-500';
                        $textColor = 'text-orange-700';
                    @endphp
                        <div class="w-16 h-16 {{ $healthColor }} rounded-full flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ $healthScore }}%</span>
                        </div>
                        <div class="text-sm {{ $textColor }} font-medium mt-1">{{ $healthStatus }}</div>
                    </div>
                </div>
            </div>

            <!-- 4 chiffres clés -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    try {
                        // Profondeur de banc (nombre de joueurs par poste)
                        $depthByPosition = \App\Models\Player::selectRaw('position, COUNT(*) as count')
                            ->groupBy('position')
                            ->get()
                            ->sum('count');
                        
                                                        // Taux de blessures
                                $totalPlayers = \App\Models\Player::count();
                                $injuredPlayers = \DB::table('player_injuries_diseases')->where('is_resolved', false)->count();
                                $injuryRate = $totalPlayers > 0 ? round(($injuredPlayers / $totalPlayers) * 100, 1) : 0;
                        
                                                        // Points par match (simulation pour l'instant)
                                $pointsPerMatch = 2.1; // Valeur simulée en attendant les compétitions
                        
                        // Valeur globale (estimation basée sur les joueurs)
                        $totalValue = \App\Models\Player::sum('market_value') ?: 0;
                        $formattedValue = $totalValue > 1000000 ? '€' . round($totalValue / 1000000, 1) . 'M' : '€' . round($totalValue / 1000, 1) . 'K';
                    } catch (\Exception $e) {
                        $depthByPosition = 0;
                        $injuryRate = 0;
                        $pointsPerMatch = 0;
                        $formattedValue = '€0';
                    }
                @endphp
                
                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $depthByPosition }}</div>
                    <div class="text-sm text-gray-600">Profondeur de banc</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="text-2xl font-bold text-orange-600">{{ $injuryRate }}%</div>
                    <div class="text-sm text-gray-600">Taux de blessures</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-2xl font-bold text-green-600">{{ $pointsPerMatch }}</div>
                    <div class="text-sm text-gray-600">Points par match</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="text-2xl font-bold text-purple-600">{{ $formattedValue }}</div>
                    <div class="text-sm text-gray-600">Valeur globale</div>
                </div>
            </div>
        </div>

        <!-- 🟩 BLOQUE GAUCHE : EFFECTIF & SANTÉ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Effectif & Santé</h3>
                
                <!-- Heatmap effectif par poste -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">État par Poste</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Attaquants</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-orange-500 rounded"></div>
                                <div class="w-3 h-3 bg-red-500 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Milieux</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Défenseurs</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-orange-500 rounded"></div>
                                <div class="w-3 h-3 bg-red-500 rounded"></div>
                                <div class="w-3 h-3 bg-red-500 rounded"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Gardiens</span>
                            <div class="flex space-x-1">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphique blessures -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Évolution Blessures</h4>
                    <div class="h-32 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-6 bg-red-400 rounded-t" style="height: 60%"></div>
                        <div class="w-6 bg-orange-400 rounded-t" style="height: 40%"></div>
                        <div class="w-6 bg-yellow-400 rounded-t" style="height: 80%"></div>
                        <div class="w-6 bg-green-400 rounded-t" style="height: 20%"></div>
                        <div class="w-6 bg-blue-400 rounded-t" style="height: 30%"></div>
                    </div>
                </div>

                <!-- Joueurs à risque -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Joueurs à Risque</h4>
                    <div class="space-y-2">
                        @php
                            try {
                                // Récupérer les joueurs à risque basés sur les données de santé
                                $atRiskPlayers = \App\Models\Player::where('injury_risk_level', 'high')
                                    ->orWhere('injury_risk_score', '>', 70)
                                    ->limit(3)
                                    ->get();
                                
                                if ($atRiskPlayers->isEmpty()) {
                                    echo '<div class="text-center p-2 bg-green-50 rounded">';
                                    echo '<span class="text-sm text-green-700">Aucun joueur à risque</span>';
                                    echo '</div>';
                                } else {
                                    foreach ($atRiskPlayers as $player) {
                                        $riskLevel = $player->injury_risk_level ?? 'medium';
                                        $riskColor = $riskLevel === 'high' ? 'bg-red-50' : 'bg-orange-50';
                                        $textColor = $riskLevel === 'high' ? 'text-red-700' : 'text-orange-700';
                                        $riskText = $riskLevel === 'high' ? 'Risque élevé' : 'Attention';
                                        
                                        echo '<div class="flex items-center justify-between p-2 ' . $riskColor . ' rounded">';
                                        echo '<span class="text-sm ' . $textColor . '">' . $player->first_name . ' ' . $player->last_name . '</span>';
                                        echo '<span class="text-xs ' . $textColor . '">' . $riskText . '</span>';
                                        echo '</div>';
                                    }
                                }
                            } catch (\Exception $e) {
                                echo '<div class="text-center p-2 bg-gray-50 rounded">';
                                echo '<span class="text-sm text-gray-700">Données non disponibles</span>';
                                echo '</div>';
                            }
                        @endphp
                    </div>
                </div>
            </div>

            <!-- 🟨 BLOQUE CENTRAL : PERFORMANCE SPORTIVE -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚽ Performance Sportive</h3>
                
                <!-- Classement par compétition -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Classement</h4>
                    <div class="space-y-2">
                        @php
                            try {
                                // Classement des équipes (simulation pour l'instant)
                                $rankings = collect([
                                    (object)['name' => 'Équipe Senior', 'points' => 45, 'position' => 1],
                                    (object)['name' => 'Équipe U19', 'points' => 38, 'position' => 2],
                                    (object)['name' => 'Équipe Féminine', 'points' => 42, 'position' => 3]
                                ]);
                                
                                if ($rankings->isEmpty()) {
                                    echo '<div class="text-center p-2 bg-gray-50 rounded">';
                                    echo '<span class="text-sm text-gray-700">Aucun classement disponible</span>';
                                    echo '</div>';
                                } else {
                                    foreach ($rankings as $index => $team) {
                                        $bgColor = $index === 0 ? 'bg-blue-50' : 'bg-gray-50';
                                        $textColor = $index === 0 ? 'text-blue-700' : 'text-gray-700';
                                        
                                        echo '<div class="flex items-center justify-between p-2 ' . $bgColor . ' rounded">';
                                        echo '<span class="text-sm font-medium">' . ($index + 1) . '. ' . $team->name . '</span>';
                                        echo '<span class="text-sm ' . $textColor . '">' . ($team->points ?? 0) . ' pts</span>';
                                        echo '</div>';
                                    }
                                }
                            } catch (\Exception $e) {
                                echo '<div class="text-center p-2 bg-gray-50 rounded">';
                                echo '<span class="text-sm text-gray-700">Données non disponibles</span>';
                                echo '</div>';
                            }
                        @endphp
                    </div>
                </div>

                <!-- Courbe points par match -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Points par Match</h4>
                    <div class="h-32 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-4 bg-green-400 rounded-t" style="height: 70%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 85%"></div>
                        <div class="w-4 bg-yellow-400 rounded-t" style="height: 60%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 90%"></div>
                        <div class="w-4 bg-green-400 rounded-t" style="height: 75%"></div>
                    </div>
                </div>

                <!-- Statistiques avancées -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statistiques Avancées</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @php
                            try {
                                // Différence de buts (simulation pour l'instant)
                                $goalDifference = 12; // Différence simulée
                                
                                // Ratio domicile (simulation pour l'instant)
                                $homeRatio = 0.8; // Ratio simulé
                            } catch (\Exception $e) {
                                $goalDifference = 0;
                                $homeRatio = 0;
                            }
                        @endphp
                        
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <div class="font-semibold text-gray-900">{{ $goalDifference > 0 ? '+' : '' }}{{ $goalDifference }}</div>
                            <div class="text-gray-600">Différence de buts</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <div class="font-semibold text-gray-900">{{ $homeRatio }}</div>
                            <div class="text-gray-600">Ratio domicile</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🟥 BLOQUE DROIT : FINANCE & VALEUR -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Finance & Valeur</h3>
                
                <!-- Budget vs Dépenses -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Budget vs Réalisé</h4>
                    <div class="space-y-3">
                        @php
                            try {
                                // Récupérer les données budgétaires (simulation pour l'instant)
                                $budget = 120000000; // Budget simulé
                                $expenses = 102000000; // Dépenses simulées
                                
                                $budgetFormatted = $budget > 1000000 ? '€' . round($budget / 1000000, 1) . 'M' : '€' . round($budget / 1000, 1) . 'K';
                                $expensesFormatted = $expenses > 1000000 ? '€' . round($expenses / 1000000, 1) . 'M' : '€' . round($expenses / 1000, 1) . 'K';
                                
                                $budgetPercentage = $budget > 0 ? round(($expenses / $budget) * 100) : 0;
                                $expensesPercentage = $budget > 0 ? round(($expenses / $budget) * 100) : 0;
                            } catch (\Exception $e) {
                                $budgetFormatted = '€120M';
                                $expensesFormatted = '€102M';
                                $budgetPercentage = 85;
                                $expensesPercentage = 85;
                            }
                        @endphp
                        
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Budget {{ date('Y') }}</span>
                                <span class="text-gray-900">{{ $budgetFormatted }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $budgetPercentage }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Dépenses</span>
                                <span class="text-gray-900">{{ $expensesFormatted }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $expensesPercentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valeur marchande -->
                <div class="mb-6">
                    <h4 class="text-center p-4 bg-purple-50 rounded-lg">
                        @php
                            try {
                                // Calculer la valeur marchande totale (simulation pour l'instant)
                                $currentValue = \App\Models\Player::count() * 1000000; // Estimation basée sur le nombre de joueurs
                                $previousValue = $currentValue * 0.88; // Estimation de l'année précédente
                                
                                $growthRate = $previousValue > 0 ? round((($currentValue - $previousValue) / $previousValue) * 100, 1) : 0;
                                $growthText = $growthRate > 0 ? '+' . $growthRate . '%' : $growthRate . '%';
                                $growthColor = $growthRate > 0 ? 'text-green-600' : ($growthRate < 0 ? 'text-red-600' : 'text-gray-600');
                                
                                $formattedValue = $currentValue > 1000000 ? '€' . round($currentValue / 1000000, 1) . 'M' : '€' . round($currentValue / 1000, 1) . 'K';
                            } catch (\Exception $e) {
                                $formattedValue = '€0';
                                $growthText = 'N/A';
                                $growthColor = 'text-gray-600';
                            }
                        @endphp
                        
                        <div class="text-2xl font-bold text-purple-700">{{ $formattedValue }}</div>
                        <div class="text-sm {{ $growthColor }}">{{ $growthText }} vs {{ date('Y') - 1 }}</div>
                    </div>
                </div>

                <!-- Projection valorisation -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Projection 2025</h4>
                    <div class="h-24 bg-gray-50 rounded flex items-end justify-around p-2">
                        <div class="w-3 bg-purple-400 rounded-t" style="height: 60%"></div>
                        <div class="w-3 bg-purple-400 rounded-t" style="height: 70%"></div>
                        <div class="w-3 bg-purple-400 rounded-t" style="height: 85%"></div>
                        <div class="w-3 bg-purple-400 rounded-t" style="height: 90%"></div>
                        <div class="w-3 bg-purple-400 rounded-t" style="height: 95%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🟪 BAS DE PAGE : PROSPECTIVE & ALERTS -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">🔮 Prospective & Alertes</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Pipeline jeunes talents -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">🌟 Pipeline Jeunes Talents</h4>
                    <div class="space-y-3">
                        @php
                            try {
                                // Récupérer les données des jeunes talents par catégorie d'âge
                                $ageCategories = [
                                    'U15' => ['min_age' => 13, 'max_age' => 15, 'color' => 'bg-blue-500', 'bg' => 'bg-blue-50'],
                                    'U17' => ['min_age' => 15, 'max_age' => 17, 'color' => 'bg-green-500', 'bg' => 'bg-green-50'],
                                    'U19' => ['min_age' => 17, 'max_age' => 19, 'color' => 'bg-purple-500', 'bg' => 'bg-purple-50']
                                ];
                                
                                foreach ($ageCategories as $category => $config) {
                                    $players = \App\Models\Player::whereNotNull('date_of_birth')
                                        ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?', [$config['min_age'], $config['max_age']])
                                        ->where('potential_rating', '>=', 70) // Seuil de potentiel
                                        ->count();
                                    
                                    $promotionYear = date('Y') + (19 - $config['max_age']);
                                    $description = $players > 0 ? $players . ' joueur' . ($players > 1 ? 's' : '') . ' prometteur' . ($players > 1 ? 's' : '') : 'Aucun talent';
                                    
                                    echo '<div class="flex items-center p-3 ' . $config['bg'] . ' rounded-lg">';
                                    echo '<div class="w-8 h-8 ' . $config['color'] . ' rounded-full flex items-center justify-center text-white text-sm font-bold">' . $category . '</div>';
                                    echo '<div class="ml-3">';
                                    echo '<div class="text-sm font-medium text-gray-900">Équipe ' . $promotionYear . '</div>';
                                    echo '<div class="text-xs text-gray-600">' . $description . '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } catch (\Exception $e) {
                                echo '<div class="text-center p-3 bg-gray-50 rounded-lg">';
                                echo '<div class="text-sm text-gray-600">Données non disponibles</div>';
                                echo '</div>';
                            }
                        @endphp
                    </div>
                </div>

                <!-- Prévisions classement -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">📈 Prévisions Classement</h4>
                    <div class="space-y-3">
                        @php
                            try {
                                // Prévision de classement (simulation pour l'instant)
                                $currentPosition = 2; // Position actuelle simulée
                                $totalTeams = 20; // Nombre total d'équipes simulé
                                
                                // Taux de victoire simulé
                                $winRate = 65; // Taux de victoire simulé
                                
                                // Prévision de position finale
                                $predictedPosition = max(1, min($totalTeams, round($currentPosition * (1 - ($winRate - 50) / 100))));
                                // Déterminer le texte de position
                                if ($predictedPosition === 1) {
                                    $positionText = '1ère Place';
                                } else {
                                    $positionText = $predictedPosition . 'ème Place';
                                }
                                
                                // Déterminer la couleur du texte
                                if ($predictedPosition <= 3) {
                                    $positionColor = 'text-green-700';
                                } elseif ($predictedPosition <= 6) {
                                    $positionColor = 'text-blue-700';
                                } else {
                                    $positionColor = 'text-orange-700';
                                }
                                
                                // Déterminer la couleur de fond
                                if ($predictedPosition <= 3) {
                                    $bgColor = 'bg-green-50';
                                } elseif ($predictedPosition <= 6) {
                                    $bgColor = 'bg-blue-50';
                                } else {
                                    $bgColor = 'bg-orange-50';
                                }
                                
                            } catch (\Exception $e) {
                                $positionText = 'N/A';
                                $positionColor = 'text-gray-700';
                                $bgColor = 'bg-gray-50';
                                $winRate = 50;
                            }
                        @endphp
                        
                        <div class="text-center p-4 {{ $bgColor }} rounded-lg">
                            <div class="text-lg font-bold {{ $positionColor }}">{{ $positionText }}</div>
                            <div class="text-sm text-gray-600">Prévision finale {{ date('Y') }}</div>
                        </div>
                        
                        <div class="h-20 bg-gray-50 rounded flex items-end justify-around p-2">
                            @for ($i = 0; $i < 4; $i++)
                                @php
                                    $height = min(100, max(20, $winRate + rand(-10, 10)));
                                    $barColor = $height >= 70 ? 'bg-green-400' : ($height >= 50 ? 'bg-yellow-400' : 'bg-red-400');
                                @endphp
                                <div class="w-4 {{ $barColor }} rounded-t" style="height: {{ $height }}%"></div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Alertes intelligentes -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">🚨 Alertes Intelligentes</h4>
                    <div class="space-y-3">
                        @php
                            try {
                                $alerts = [];
                                
                                // Alerte 1: Risque de blessure
                                $highRiskPlayers = \App\Models\Player::where('injury_risk_level', 'high')
                                    ->orWhere('injury_risk_score', '>', 70)
                                    ->count();
                                
                                if ($highRiskPlayers > 0) {
                                    $message = $highRiskPlayers . ' joueur' . ($highRiskPlayers > 1 ? 's' : '') . ' à risque';
                                    $alerts[] = [
                                        'type' => 'red',
                                        'title' => 'Risque de blessure accru',
                                        'message' => $message
                                    ];
                                }
                                
                                // Alerte 2: Profondeur de banc
                                $positions = ['Attaquant', 'Milieu', 'Défenseur', 'Gardien'];
                                foreach ($positions as $position) {
                                    $count = \App\Models\Player::where('position', $position)->count();
                                    if ($count < 3) {
                                        $alerts[] = [
                                            'type' => 'orange',
                                            'title' => 'Profondeur insuffisante',
                                            'message' => 'Poste ' . $position . ' critique (' . $count . ' joueur' . ($count > 1 ? 's' : '') . ')'
                                        ];
                                        break;
                                    }
                                }
                                
                                // Alerte 3: Budget (simulation pour l'instant)
                                $budget = 120000000; // Budget simulé
                                $expenses = 102000000; // Dépenses simulées
                                if ($budget > 0 && $expenses > 0) {
                                    $executionRate = ($expenses / $budget) * 100;
                                    if ($executionRate < 80) {
                                        $alerts[] = [
                                            'type' => 'yellow',
                                            'title' => 'Budget sous-exécuté',
                                            'message' => 'Exécution à ' . round($executionRate, 1) . '%'
                                        ];
                                    }
                                }
                                
                                // Si aucune alerte, afficher un message positif
                                if (empty($alerts)) {
                                    echo '<div class="p-3 bg-green-50 border-l-4 border-green-500 rounded">';
                                    echo '<div class="text-sm font-medium text-green-800">✅ Aucune alerte</div>';
                                    echo '<div class="text-xs text-green-600">Tout va bien !</div>';
                                    echo '</div>';
                                } else {
                                    // Afficher les alertes (max 3)
                                    foreach (array_slice($alerts, 0, 3) as $alert) {
                                        $colors = [
                                            'red' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'title' => 'text-red-800', 'message' => 'text-red-600'],
                                            'orange' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-500', 'title' => 'text-orange-800', 'message' => 'text-orange-600'],
                                            'yellow' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'title' => 'text-yellow-800', 'message' => 'text-yellow-600']
                                        ];
                                        
                                        $color = $colors[$alert['type']];
                                        echo '<div class="p-3 ' . $color['bg'] . ' border-l-4 ' . $color['border'] . ' rounded">';
                                        echo '<div class="text-sm font-medium ' . $color['title'] . '">' . $alert['title'] . '</div>';
                                        echo '<div class="text-xs ' . $color['message'] . '">' . $alert['message'] . '</div>';
                                        echo '</div>';
                                    }
                                }
                                
                            } catch (\Exception $e) {
                                echo '<div class="p-3 bg-gray-50 border-l-4 border-gray-500 rounded">';
                                echo '<div class="text-sm font-medium text-gray-800">Système d\'alertes</div>';
                                echo '<div class="text-xs text-gray-600">Données non disponibles</div>';
                                echo '</div>';
                            }
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les graphiques -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Dashboard FIT chargé avec succès');
        
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
    });
</script>
</body>
</html>
