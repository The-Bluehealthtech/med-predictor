<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.4.15/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <style>
        .fifa-ultimate-card {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        
        .fifa-rating-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4);
            border: 3px solid #fff;
        }

        .fifa-nav-tab {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            color: #ffffff;
            /* Debug: rendre visible */
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .fifa-nav-tab.active {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-2px);
            color: #ffffff;
        }

        /* Couleurs de texte plus sombres pour une meilleure lisibilité */
        .text-dark-primary {
            color: #1f2937;
        }
        
        .text-dark-secondary {
            color: #374151;
        }
        
        .text-dark-accent {
            color: #4b5563;
        }
        
        .text-dark-muted {
            color: #6b7280;
        }

        .fifa-position-badge {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            border: 2px solid #fff;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <div id="fifa-app">
        <!-- Header avec authentification et navigation -->
        <div class="bg-white/10 backdrop-blur-md border-b border-white/20 p-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="text-2xl">⚽</div>
                    <div>
                        <h1 class="text-xl font-bold text-white">FIFA Ultimate Portal</h1>
                        <p class="text-white/80 text-sm">{{ session('user_role') === 'admin' ? 'Administration' : 'Joueur' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Navigation entre joueurs (visible uniquement pour les admins) -->
                    @if(session('user_role') === 'admin')
                        <div class="flex items-center space-x-2">
                            <label for="player-select" class="text-white/90 text-sm">Joueur:</label>
                            <select id="player-select" class="bg-white/20 border border-white/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                @foreach($allPlayers ?? [] as $p)
                                    <option value="{{ $p->id }}" {{ $p->id == $player->id ? 'selected' : '' }}>
                                        {{ $p->first_name }} {{ $p->last_name }} ({{ $p->club->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <button onclick="changePlayer()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                Changer
                            </button>
                        </div>
                    @endif
                    
                    <!-- Informations utilisateur -->
                    <div class="text-right">
                        <div class="text-white/90 text-sm">
                            @if(session('user_role') === 'admin')
                                Admin
                            @else
                                {{ session('player_name', 'Joueur') }}
                            @endif
                        </div>
                    </div>
                    
                    <!-- Bouton déconnexion -->
                    <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>

        <!-- Hero Zone FIFA Ultimate -->
        <div class="fifa-ultimate-card text-white p-6 m-4 relative">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute w-64 h-64 bg-white/5 rounded-full -top-32 -right-32"></div>
                <div class="absolute w-48 h-48 bg-white/3 rounded-full -bottom-24 -left-24"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row items-center justify-between space-y-6 lg:space-y-0">
                    <!-- Section gauche: Photo et infos joueur -->
                    <div class="flex items-center space-x-6">
                        <!-- Photo du joueur -->
                        <div class="relative flex-shrink-0">
                            <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-full overflow-hidden border-4 border-white/20 shadow-2xl bg-gradient-to-br from-gray-400 to-gray-600">
                                @if($portalData['images']['player_profile'] ?? null)
                                    <img src="{{ $portalData['images']['player_profile'] }}" 
                                         alt="{{ $player->first_name }} {{ $player->last_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl">
                                        {{ $player->first_name[0] }}{{ $player->last_name[0] }}
                                    </div>
                                @endif
                            </div>
                            <div class="fifa-position-badge absolute -bottom-2 -right-2 px-3 py-1 text-white font-bold text-sm">
                                {{ $player->position ?? 'N/A' }}
                            </div>
                        </div>
                        
                        <!-- Informations du joueur -->
                        <div class="text-center lg:text-left">
                            <h1 class="text-3xl lg:text-4xl font-bold mb-1">{{ $player->first_name }} {{ $player->last_name }}</h1>
                            <div class="text-lg text-yellow-400 font-semibold mb-3">⭐ FIFA Ultimate Legend</div>
                            
                            <div class="flex items-center justify-center lg:justify-start space-x-4 mb-4">
                                <!-- Logo du club -->
                                <div class="flex items-center space-x-2">
                                    @if($portalData['images']['club_logo'] ?? null)
                                        <img src="{{ $portalData['images']['club_logo'] }}" 
                                             alt="{{ $player->club->name ?? 'Club' }}" 
                                             class="w-8 h-8 object-contain">
                                    @else
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ $player->club->name[0] ?? 'C' }}
                                        </div>
                                    @endif
                                    <span class="text-lg font-medium">{{ $player->club->name ?? 'Club non défini' }}</span>
                                </div>
                                
                                <!-- Logo de la nation -->
                                <div class="w-px h-6 bg-white/30"></div>
                                <div class="flex items-center space-x-2">
                                    @if($portalData['images']['country_flag'] ?? null)
                                        <img src="{{ $portalData['images']['country_flag'] }}" 
                                             alt="{{ $player->nationality ?? 'Pays' }}" 
                                             class="w-6 h-4 object-cover rounded">
                                    @else
                                        <div class="w-6 h-4 bg-gray-500 rounded text-white text-xs flex items-center justify-center">
                                            {{ $player->nationality ? substr($player->nationality, 0, 2) : 'TF' }}
                                        </div>
                                    @endif
                                    <span class="text-sm">{{ $player->nationality ?? 'Nationalité non définie' }}</span>
                                </div>
                            </div>
                            
                            <!-- Informations détaillées -->
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 text-sm mb-4">
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-yellow-400">{{ $portalData['playerStats']['age'] ?? 'N/A' }}</div>
                                    <div class="text-xs opacity-70">Âge</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-blue-400">{{ $portalData['playerStats']['height'] ?? 'N/A' }}cm</div>
                                    <div class="text-xs opacity-70">Taille</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-green-400">{{ $portalData['playerStats']['weight'] ?? 'N/A' }}kg</div>
                                    <div class="text-xs opacity-70">Poids</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-purple-400">{{ $portalData['playerStats']['preferred_foot'] ?? 'N/A' }}</div>
                                    <div class="text-xs opacity-70">Pied</div>
                                </div>
                            </div>

                            <!-- Récompenses et statistiques de carrière -->
                            <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-3">
                                <span class="px-2 py-1 bg-yellow-500 text-black text-xs font-bold rounded-full">🏆 {{ $portalData['playerStats']['ballon_dor_count'] ?? 0 }}x Ballon d'Or</span>
                                <span class="px-2 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">⚽ {{ $portalData['playerStats']['total_goals'] ?? 0 }}+ Buts</span>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">🎯 {{ $portalData['playerStats']['total_assists'] ?? 0 }}+ Assists</span>
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs font-bold rounded-full">🏆 {{ $portalData['playerStats']['champions_league_count'] ?? 0 }}x Champions League</span>
                            </div>

                            <!-- Statistiques saison actuelle -->
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-green-400">{{ $portalData['playerStats']['season_goals'] ?? 0 }}</div>
                                    <div class="opacity-70">Buts saison</div>
                                </div>
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-blue-400">{{ $portalData['playerStats']['season_assists'] ?? 0 }}</div>
                                    <div class="opacity-70">Assists</div>
                                </div>
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-purple-400">{{ $portalData['seasonProgress']['matchesPlayed'] ?? 0 }}</div>
                                    <div class="opacity-70">Matchs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section droite: Stats FIFA et santé -->
                    <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6">
                        <!-- Ratings FIFA -->
                        <div class="text-center">
                            <div class="flex space-x-3 mb-2">
                                <div class="fifa-rating-badge text-black p-4">
                                    <div class="text-3xl font-bold">{{ $portalData['fifaStats']['overall_rating'] ?? 91 }}</div>
                                    <div class="text-xs">OVR</div>
                                </div>
                                <div class="fifa-rating-badge text-black p-3">
                                    <div class="text-2xl font-bold">{{ $portalData['fifaStats']['potential_rating'] ?? 80 }}</div>
                                    <div class="text-xs">POT</div>
                                </div>
                            </div>
                            <div class="text-xs text-white/70">FIFA Ultimate</div>
                        </div>
                        
                        <!-- Score de santé FIT -->
                        <div class="text-center">
                            <div class="relative w-20 h-20 mb-2">
                                <svg class="w-full h-full progress-circle" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.2)" stroke-width="4" fill="none"/>
                                    <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="4" fill="none"
                                            stroke-dasharray="251.33" stroke-dashoffset="{{ 251.33 - (($portalData['fifaStats']['fitness_score'] ?? 85) * 251.33 / 100) }}" stroke-linecap="round"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-green-400">{{ $portalData['fifaStats']['fitness_score'] ?? 85 }}</span>
                                </div>
                            </div>
                            <div class="text-xs font-medium">Score FIT</div>
                        </div>
                        
                        <!-- Infos importantes détaillées -->
                        <div class="space-y-2 text-sm">
                            <div class="p-3 bg-gradient-to-r from-green-500/20 to-green-600/20 rounded-lg border border-green-500/30">
                                <div class="text-xs text-green-300 font-medium">Risque de blessure</div>
                                <div class="font-bold text-green-400">{{ $portalData['heroMetrics']['injury_risk']['percentage'] ?? 15 }}% - {{ $portalData['heroMetrics']['injury_risk']['level'] ?? 'FAIBLE' }}</div>
                                <div class="w-full bg-green-500/50 rounded-full h-1 mt-1">
                                    <div class="bg-green-500 h-1 rounded-full" style="width: {{ $portalData['heroMetrics']['injury_risk']['percentage'] ?? 15 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-yellow-500/20 to-yellow-600/20 rounded-lg border border-yellow-500/30">
                                <div class="text-xs text-yellow-300 font-medium">Valeur marchande</div>
                                <div class="font-bold text-yellow-400">€{{ $portalData['heroMetrics']['market_value']['current'] ?? 180 }}M</div>
                                @if(($portalData['heroMetrics']['market_value']['change'] ?? 0) > 0)
                                    <div class="text-xs text-yellow-300">↗️ +€{{ $portalData['heroMetrics']['market_value']['change'] ?? 15 }}M ce mois</div>
                                @elseif(($portalData['heroMetrics']['market_value']['change'] ?? 0) < 0)
                                    <div class="text-xs text-red-300">↘️ €{{ $portalData['heroMetrics']['market_value']['change'] ?? 15 }}M ce mois</div>
                                @else
                                    <div class="text-xs text-gray-300">→ Stable</div>
                                @endif
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-blue-500/20 to-blue-600/20 rounded-lg border border-blue-500/30">
                                <div class="text-xs text-blue-300 font-medium">Disponibilité</div>
                                <div class="font-bold text-blue-400">✅ {{ $portalData['heroMetrics']['availability']['status'] ?? 'DISPONIBLE' }}</div>
                                <div class="text-xs text-blue-300">Prochain match: {{ $portalData['heroMetrics']['availability']['next_match'] ?? 'Dimanche' }}</div>
                            </div>

                            <!-- Indicateurs additionnels -->
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                <div class="text-center p-2 bg-white/5 rounded">
                                    <div class="font-bold text-orange-400">{{ $portalData['heroMetrics']['player_state']['form'] ?? 85 }}%</div>
                                    <div class="opacity-70">Forme</div>
                                </div>
                                <div class="text-center p-2 bg-white/5 rounded">
                                    <div class="font-bold text-cyan-400">{{ $portalData['heroMetrics']['player_state']['morale'] ?? 88 }}%</div>
                                    <div class="opacity-70">Moral</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barre de progression de la saison et indicateurs -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Progression saison -->
                    <div class="lg:col-span-2 p-4 bg-white/5 rounded-xl backdrop-blur-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium">Saison {{ $portalData['seasonProgress']['currentSeason'] ?? '2024-25' }}</span>
                            <span class="text-sm text-white/70">{{ $portalData['seasonProgress']['completion'] ?? 75 }}% complétée</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2 mb-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: {{ $portalData['seasonProgress']['completion'] ?? 75 }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-white/70">
                            <span>{{ $portalData['seasonProgress']['matchesPlayed'] ?? 0 }} matchs joués</span>
                            <span>{{ $portalData['seasonProgress']['matchesRemaining'] ?? 10 }} matchs restants</span>
                        </div>
                    </div>

                    <!-- Performances récentes -->
                    <div class="p-4 bg-white/5 rounded-xl backdrop-blur-sm">
                        <div class="text-sm font-medium mb-2">Performances récentes</div>
                        <div class="flex justify-between items-center text-xs">
                            <span>5 derniers matchs:</span>
                            <div class="flex space-x-1">
                                @foreach($portalData['recentPerformances'] ?? ['W', 'W', 'D', 'W', 'W'] as $perf)
                                    <div class="w-4 h-4 {{ $perf === 'W' ? 'bg-green-500' : ($perf === 'D' ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full flex items-center justify-center text-white text-xs">{{ $perf }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 mb-6">
            <div class="flex flex-wrap gap-4">
                <!-- Onglets Vue.js -->
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'fifa-nav-tab px-6 py-3 rounded-xl font-medium transition-all duration-300 relative',
                        activeTab === tab.id ? 'active' : ''
                    ]"
                >
                    <span class="tab-icon mr-2" v-text="tab.icon"></span>
                    <span v-text="tab.name"></span>
                    <span v-if="tab.notifications" class="notification-badge" v-text="tab.notifications"></span>
                </button>
            </div>
        </div>

                <!-- Contenu des Tabs -->
        <div class="px-6 pb-8">
            <!-- Tab Performance -->
            <div v-show="activeTab === 'performance'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">📊 Performance</h2>
                
                <!-- Indicateur de chargement -->
                <div v-if="loading.performance" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-dark-primary"></div>
                    <span class="ml-3 text-dark-primary">Chargement des données...</span>
                </div>
                
                <!-- Statistiques principales -->
                <div v-if="!loading.performance" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Buts marqués</h5>
                        <div class="text-3xl font-bold text-green-600">{{ $portalData['performanceStats']['current_month_goals'] ?? 0 }}</div>
                        <div class="text-sm text-dark-secondary">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Passes décisives</h5>
                        <div class="text-3xl font-bold text-blue-600">{{ $portalData['performanceStats']['current_month_assists'] ?? 0 }}</div>
                        <div class="text-sm text-dark-secondary">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Distance parcourue</h5>
                        <div class="text-3xl font-bold text-purple-600">{{ $portalData['performanceStats']['current_month_distance'] ?? 0 }} km</div>
                        <div class="text-sm text-dark-secondary">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Rating moyen</h5>
                        <div class="text-3xl font-bold text-orange-600">{{ $portalData['performanceStats']['average_rating'] ?? 0 }}</div>
                        <div class="text-sm text-dark-secondary">Basé sur {{ $portalData['performanceStats']['matches_played'] ?? 0 }} matchs</div>
                    </div>
                </div>

                <!-- Performances vs Grandes Équipes -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Performances vs Grandes Équipes</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full text-dark-primary">
                            <thead>
                                <tr class="border-b border-gray-300">
                                    <th class="text-left p-2 font-semibold">Adversaire</th>
                                    <th class="text-center p-2 font-semibold">Matchs</th>
                                    <th class="text-center p-2 font-semibold">Buts</th>
                                    <th class="text-center p-2 font-semibold">Assists</th>
                                    <th class="text-center p-2 font-semibold">Note Moy.</th>
                                    <th class="text-center p-2 font-semibold">Dernière perf.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="p-2">Real Madrid</td>
                                    <td class="text-center p-2">{{ rand(2, 5) }}</td>
                                    <td class="text-center p-2">{{ rand(3, 8) }}</td>
                                    <td class="text-center p-2">{{ rand(1, 4) }}</td>
                                    <td class="text-center p-2">{{ rand(70, 95) / 10 }}</td>
                                    <td class="text-center p-2">⭐ Excellent</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="p-2">Manchester City</td>
                                    <td class="text-center p-2">{{ rand(1, 4) }}</td>
                                    <td class="text-center p-2">{{ rand(1, 5) }}</td>
                                    <td class="text-center p-2">{{ rand(1, 3) }}</td>
                                    <td class="text-center p-2">{{ rand(65, 90) / 10 }}</td>
                                    <td class="text-center p-2">⭐ Très bon</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="p-2">Bayern Munich</td>
                                    <td class="text-center p-2">{{ rand(1, 3) }}</td>
                                    <td class="text-center p-2">{{ rand(0, 3) }}</td>
                                    <td class="text-center p-2">{{ rand(0, 2) }}</td>
                                    <td class="text-center p-2">{{ rand(60, 85) / 10 }}</td>
                                    <td class="text-center p-2">✓ Correct</td>
                                </tr>
                                <tr>
                                    <td class="p-2">Liverpool</td>
                                    <td class="text-center p-2">{{ rand(1, 3) }}</td>
                                    <td class="text-center p-2">{{ rand(0, 3) }}</td>
                                    <td class="text-center p-2">{{ rand(0, 2) }}</td>
                                    <td class="text-center p-2">{{ rand(60, 85) / 10 }}</td>
                                    <td class="text-center p-2">✓ Bon</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts et visualisations -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Radar des performances -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Radar des Performances</h5>
                        <div class="w-full h-64">
                            <canvas id="radarChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Ligne de progression -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Évolution Mensuelle</h5>
                        <div class="w-full h-64">
                            <canvas id="lineChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts supplémentaires -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Barres des buts -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Buts par Mois</h5>
                        <div class="w-full h-64">
                            <canvas id="barChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Doughnut des zones d'activité -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Zones d'Activité</h5>
                        <div class="w-full h-64">
                            <canvas id="doughnutChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Notifications -->
            <div v-show="activeTab === 'notifications'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">🔔 Notifications</h2>
                
                <!-- Notifications importantes -->
                <div class="space-y-4">
                    <!-- Notification Équipe Nationale -->
                    <div class="bg-white/90 rounded-lg p-4 shadow-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">🇫🇷</span>
                                <div>
                                    <h4 class="font-semibold text-dark-primary">Convocación {{ $player->nationality ?? 'France' }}</h4>
                                    <p class="text-dark-secondary text-sm">Convocado para partidos vs Brasil y Uruguay</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-dark-accent text-sm">{{ now()->addDays(rand(1, 30))->format('d/m/Y') }}</div>
                                <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">URGENT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Entraînement -->
                    <div class="bg-white/90 rounded-lg p-4 shadow-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">⚽</span>
                                <div>
                                    <h4 class="font-semibold text-dark-primary">Entrenamiento Selección</h4>
                                    <p class="text-dark-secondary text-sm">Sesión técnica mañana 9:00 AM</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-dark-accent text-sm">{{ now()->addDays(rand(1, 7))->format('d/m/Y') }}</div>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">CONVOQUÉ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Réseaux Sociaux -->
                    <div class="bg-white/90 rounded-lg p-4 shadow-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">📱</span>
                                <div>
                                    <h4 class="font-semibold text-dark-primary">Mensaje de Fan</h4>
                                    <p class="text-dark-secondary text-sm">Mensaje de apoyo de @fan_chelsea</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-dark-accent text-sm">{{ now()->subDays(rand(1, 5))->format('d/m/Y') }}</div>
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs rounded-full">POSITIVE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Twitter -->
                    <div class="bg-white/90 rounded-lg p-4 shadow-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">🐦</span>
                                <div>
                                    <h4 class="font-semibold text-dark-primary">Tendencia Twitter</h4>
                                    <p class="text-dark-secondary text-sm">#MoussaChelsea trending en France</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-dark-accent text-sm">{{ now()->subDays(rand(1, 3))->format('d/m/Y') }}</div>
                                <span class="px-2 py-1 bg-blue-400 text-white text-xs rounded-full">15.2K vues</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Santé & Bien-être -->
            <div v-show="activeTab === 'sante'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">❤️ Santé & Bien-être</h2>
                
                <!-- Métriques de santé principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Rythme Cardiaque</h5>
                        <div class="text-3xl font-bold text-red-600">{{ rand(60, 85) }} BPM</div>
                        <div class="text-sm text-dark-secondary">Au repos</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Niveau de Stress</h5>
                        <div class="text-3xl font-bold text-orange-600">{{ rand(20, 60) }}%</div>
                        <div class="text-sm text-dark-secondary">Élevé</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Qualité du Sommeil</h5>
                        <div class="text-3xl font-bold text-blue-600">{{ rand(70, 95) }}%</div>
                        <div class="text-sm text-dark-secondary">Très bonne</div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-2">Hydratation</h5>
                        <div class="text-3xl font-bold text-cyan-600">{{ rand(80, 100) }}%</div>
                        <div class="text-sm text-dark-secondary">Optimale</div>
                    </div>
                </div>

                <!-- Charts de santé -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Radar de santé -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Radar de Santé</h5>
                        <div class="w-full h-64">
                            <canvas id="healthRadarChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Ligne de progression santé -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Évolution Santé</h5>
                        <div class="w-full h-64">
                            <canvas id="healthLineChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Détails de santé -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Détails de Santé</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h6 class="font-medium text-dark-primary mb-2">Nutrition</h6>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Calories consommées</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(2000, 3500) }} kcal</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Protéines</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(150, 250) }}g</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Hydratation</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(2, 4) }}L</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h6 class="font-medium text-dark-primary mb-2">Récupération</h6>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Heures de sommeil</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(7, 9) }}h</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Qualité du sommeil</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(80, 95) }}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-secondary">Temps de récupération</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(12, 24) }}h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Médical -->
            <div v-show="activeTab === 'medical'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">🏥 Médical</h2>
                
                <!-- Alertes médicales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">🚨</span>
                            <div>
                                <h4 class="font-semibold text-red-800">Blessure Mineure</h4>
                                <p class="text-red-600 text-sm">Entorse légère cheville</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">⚠️</span>
                            <div>
                                <h4 class="font-semibold text-yellow-800">Contrôle Requis</h4>
                                <p class="text-yellow-600 text-sm">Bilan sanguin mensuel</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">✅</span>
                            <div>
                                <h4 class="font-semibold text-green-800">En Forme</h4>
                                <p class="text-green-600 text-sm">Aptitude confirmée</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts médicaux -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Évolution des blessures -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Évolution des Blessures</h5>
                        <div class="w-full h-64">
                            <canvas id="medicalLineChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Répartition des blessures -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Types de Blessures</h5>
                        <div class="w-full h-64">
                            <canvas id="medicalPieChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Historique médical détaillé -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Historique Médical</h5>
                    <div class="space-y-4">
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Consultation Cardiologue</h6>
                                    <p class="text-dark-secondary text-sm">Dr. Martinez - Centre médical principal</p>
                                    <p class="text-dark-accent text-sm">Bilan cardiaque complet - Résultats normaux</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(1, 15))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">TERMINÉ</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Rééducation Physique</h6>
                                    <p class="text-dark-secondary text-sm">Dr. Sophie Moreau - Centre de rééducation</p>
                                    <p class="text-dark-accent text-sm">Récupération musculaire post-blessure</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(5, 20))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">EN COURS</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Contrôle Dentaire</h6>
                                    <p class="text-dark-secondary text-sm">Dr. Dubois - Cabinet dentaire</p>
                                    <p class="text-dark-accent text-sm">Détartrage et contrôle général</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(10, 30))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">TERMINÉ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochains rendez-vous -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Prochains Rendez-vous</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="text-blue-500">📅</span>
                                <div>
                                    <h6 class="font-medium text-dark-primary">Contrôle de routine</h6>
                                    <p class="text-dark-secondary text-sm">Centre médical - {{ now()->addDays(rand(1, 7))->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">CONVOQUÉ</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="text-green-500">🏥</span>
                                <div>
                                    <h6 class="font-medium text-dark-primary">Bilan complet</h6>
                                    <p class="text-dark-secondary text-sm">Hôpital - {{ now()->addDays(rand(5, 14))->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">PLANIFIÉ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Devices -->
            <div v-show="activeTab === 'devices'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">📱 Devices</h2>
                
                <!-- Statut des devices -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg border border-green-200">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="text-2xl">⌚</span>
                            <div>
                                <h4 class="font-semibold text-dark-primary">Apple Watch</h4>
                                <p class="text-dark-secondary text-sm">Série 8 - 45mm</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Batterie</span>
                                <span class="font-semibold text-green-600">{{ rand(60, 95) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Connecté</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">ONLINE</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg border border-blue-200">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="text-2xl">📱</span>
                            <div>
                                <h4 class="font-semibold text-dark-primary">iPhone 15 Pro</h4>
                                <p class="text-dark-secondary text-sm">256GB - iOS 17.2</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Batterie</span>
                                <span class="font-semibold text-blue-600">{{ rand(40, 85) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Connecté</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">ONLINE</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg border border-purple-200">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="text-2xl">🎧</span>
                            <div>
                                <h4 class="font-semibold text-dark-primary">AirPods Pro</h4>
                                <p class="text-dark-secondary text-sm">2ème génération</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Batterie</span>
                                <span class="font-semibold text-purple-600">{{ rand(70, 100) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark-secondary text-sm">Connecté</span>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">ONLINE</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts des devices -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Utilisation des devices -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Utilisation des Devices</h5>
                        <div class="w-full h-64">
                            <canvas id="devicesBarChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Temps d'écran -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Temps d'Écran</h5>
                        <div class="w-full h-64">
                            <canvas id="devicesPieChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Données de monitoring -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Données de Monitoring</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h6 class="font-medium text-dark-primary mb-3">Métriques Physiques</h6>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Pas quotidiens</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(8000, 15000) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Calories brûlées</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(400, 800) }} kcal</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Distance parcourue</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(5, 12) }} km</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h6 class="font-medium text-dark-primary mb-3">Métriques Numériques</h6>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Notifications reçues</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(20, 50) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Apps utilisées</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(8, 15) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-dark-secondary">Temps d'écran</span>
                                    <span class="font-semibold text-dark-primary">{{ rand(3, 8) }}h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Dopage -->
            <div v-show="activeTab === 'dopage'" class="space-y-6">
                <h2 class="text-2xl font-bold text-dark-primary mb-6">🧪 Dopage</h2>
                
                <!-- Statut des contrôles -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">✅</span>
                            <div>
                                <h4 class="font-semibold text-green-800">Dernier Contrôle</h4>
                                <p class="text-green-600 text-sm">Négatif - {{ now()->subDays(rand(1, 30))->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">📋</span>
                            <div>
                                <h4 class="font-semibold text-blue-800">Prochain Contrôle</h4>
                                <p class="text-blue-600 text-sm">{{ now()->addDays(rand(5, 60))->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">⚠️</span>
                            <div>
                                <h4 class="font-semibold text-yellow-800">Risque</h4>
                                <p class="text-yellow-600 text-sm">Faible - {{ rand(5, 15) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts de dopage -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Chart Historique des contrôles -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Historique des Contrôles</h5>
                        <div class="w-full h-64">
                            <canvas id="dopingLineChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart Répartition des résultats -->
                    <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                        <h5 class="font-semibold text-dark-primary mb-4">Résultats des Tests</h5>
                        <div class="w-full h-64">
                            <canvas id="dopingDoughnutChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Historique détaillé des contrôles -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Historique des Contrôles</h5>
                    <div class="space-y-4">
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Contrôle Urinaire - Match</h6>
                                    <p class="text-dark-secondary text-sm">Stade de France - Contrôleur officiel</p>
                                    <p class="text-dark-accent text-sm">Test standard post-match - Résultat négatif</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(1, 15))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">NÉGATIF</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Contrôle Sanguin - Entraînement</h6>
                                    <p class="text-dark-secondary text-sm">Centre d'entraînement - Laboratoire agréé</p>
                                    <p class="text-dark-accent text-sm">Test complet - Résultat négatif</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(15, 45))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">NÉGATIF</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h6 class="font-medium text-dark-primary">Contrôle Surprise - Domicile</h6>
                                    <p class="text-dark-secondary text-sm">Résidence personnelle - Contrôleur agréé</p>
                                    <p class="text-dark-accent text-sm">Test urinaire et sanguin - Résultat négatif</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-dark-accent text-sm">{{ now()->subDays(rand(30, 90))->format('d/m/Y') }}</div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">NÉGATIF</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations sur les substances -->
                <div class="bg-white/90 rounded-lg p-6 shadow-lg">
                    <h5 class="font-semibold text-dark-primary mb-4">Substances Interdites</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h6 class="font-medium text-dark-primary mb-3">Substances Interdites en Compétition</h6>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Stimulants (amphétamines, cocaïne)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Stéroïdes anabolisants</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Hormones de croissance</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h6 class="font-medium text-dark-primary mb-3">Substances Interdites Hors Compétition</h6>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Stéroïdes anabolisants</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Hormones peptidiques</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                    <span class="text-dark-secondary text-sm">Agents de masquage</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Test ultra-simple de Vue.js
        console.log('🚀 Script commencé');
        console.log('🔍 Vue disponible:', typeof Vue);
        console.log('🔍 Élément #fifa-app:', document.getElementById('fifa-app'));
        
        // Test immédiat
        if (typeof Vue !== 'undefined') {
            console.log('✅ Vue.js est disponible immédiatement !');
        } else {
            console.error('❌ Vue.js n\'est PAS disponible !');
        }
        
        // Attendre que le DOM soit complètement chargé
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM chargé !');
            
            // Vérification que Vue.js est chargé
            if (typeof Vue === 'undefined') {
                console.error('❌ Vue.js n\'est pas chargé !');
                document.body.innerHTML = '<div class="p-8 text-center text-red-600"><h1>Erreur : Vue.js non chargé</h1><p>Vérifiez votre connexion internet</p></div>';
                return;
            }
            
            console.log('✅ Vue.js est chargé !');
            console.log('🔍 Élément #fifa-app trouvé:', document.getElementById('fifa-app'));

            const { createApp } = Vue;
            console.log('🔍 createApp disponible:', typeof createApp);

            const app = createApp({
                errorHandler(err, vm, info) {
                    console.error('Erreur Vue.js:', err);
                    console.error('Info:', info);
                },
                data() {
                    return {
                        activeTab: 'performance',
                        message: 'Vue.js fonctionne parfaitement ! 🎉',
                        navigationTabs: [
                            { id: 'performance', name: 'Performance', icon: '📊' },
                            { id: 'notifications', name: 'Notifications', icon: '🔔', notifications: 0 },
                            { id: 'sante', name: 'Santé & Bien-être', icon: '❤️' },
                            { id: 'medical', name: 'Médical', icon: '🏥', notifications: 0 },
                            { id: 'devices', name: 'Devices', icon: '📱', notifications: 0 },
                            { id: 'dopage', name: 'Dopage', icon: '🧪' }
                        ],
                        notifications: [],
                        performanceData: null,
                        healthData: null,
                        medicalData: null,
                        devicesData: null,
                        dopingData: null,
                        loading: {
                            performance: false,
                            health: false,
                            medical: false,
                            devices: false,
                            doping: false
                        }
                    }
                },
                mounted() {
                    console.log('🚀 Vue.js monté !');
                    console.log('📊 Onglets disponibles:', this.navigationTabs);
                    console.log('🎯 Onglet actif:', this.activeTab);
                    
                    this.$nextTick(() => {
                        this.loadAllData();
                    });
                },
                methods: {
                    async loadAllData() {
                        try {
                            await Promise.all([
                                this.loadPerformanceData(),
                                this.loadNotifications(),
                                this.loadHealthData(),
                                this.loadMedicalData(),
                                this.loadDevicesData(),
                                this.loadDopingData()
                            ]);
                            console.log('Toutes les données chargées avec succès');
                        } catch (error) {
                            console.error('Erreur lors du chargement des données:', error);
                        }
                    },

                    async loadPerformanceData() {
                        this.loading.performance = true;
                        try {
                            const response = await fetch('/api/portal/performance-data');
                            this.performanceData = await response.json();
                            this.initPerformanceCharts();
                        } catch (error) {
                            console.error('Erreur chargement performance:', error);
                        } finally {
                            this.loading.performance = false;
                        }
                    },

                    async loadNotifications() {
                        try {
                            const response = await fetch('/api/portal/notifications');
                            this.notifications = await response.json();
                            this.updateNotificationCounts();
                        } catch (error) {
                            console.error('Erreur chargement notifications:', error);
                        }
                    },

                    async loadHealthData() {
                        this.loading.health = true;
                        try {
                            const response = await fetch('/api/portal/health-data');
                            this.healthData = await response.json();
                            this.initHealthCharts();
                        } catch (error) {
                            console.error('Erreur chargement santé:', error);
                        } finally {
                            this.loading.health = false;
                        }
                    },

                    async loadMedicalData() {
                        this.loading.medical = true;
                        try {
                            const response = await fetch('/api/portal/medical-data');
                            this.medicalData = await response.json();
                            this.initMedicalCharts();
                        } catch (error) {
                            console.error('Erreur chargement médical:', error);
                        } finally {
                            this.loading.medical = false;
                        }
                    },

                    async loadDevicesData() {
                        this.loading.devices = true;
                        try {
                            const response = await fetch('/api/portal/devices-data');
                            this.devicesData = await response.json();
                            this.initDevicesCharts();
                        } catch (error) {
                            console.error('Erreur chargement devices:', error);
                        } finally {
                            this.loading.devices = false;
                        }
                    },

                    async loadDopingData() {
                        this.loading.doping = true;
                        try {
                            const response = await fetch('/api/portal/doping-data');
                            this.dopingData = await response.json();
                            this.initDopingCharts();
                        } catch (error) {
                            console.error('Erreur chargement dopage:', error);
                        } finally {
                            this.loading.doping = false;
                        }
                    },

                    updateNotificationCounts() {
                        this.navigationTabs.forEach(tab => {
                            if (tab.id === 'notifications') {
                                tab.notifications = this.notifications.filter(n => n.status === 'unread').length;
                            } else if (tab.id === 'medical') {
                                tab.notifications = this.medicalData?.alerts?.filter(a => a.type === 'warning').length || 0;
                            } else if (tab.id === 'devices') {
                                tab.notifications = this.devicesData?.devices?.filter(d => d.battery < 20).length || 0;
                            }
                        });
                    },

                    initPerformanceCharts() {
                        if (!this.performanceData) return;
                        console.log('Initialisation des charts de performance');
                        
                        // Chart Radar des performances
                        const radarCtx = document.getElementById('radarChart');
                        if (radarCtx) {
                            try {
                                new Chart(radarCtx, {
                                    type: 'radar',
                                    data: {
                                        labels: this.performanceData.radar.labels,
                                        datasets: [{
                                            label: 'Joueur',
                                            data: this.performanceData.radar.data,
                                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                            borderColor: 'rgba(59, 130, 246, 1)',
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            r: {
                                                beginAtZero: true,
                                                max: 100
                                            }
                                        }
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart radar:', error);
                            }
                        }

                        // Chart Ligne de progression
                        const lineCtx = document.getElementById('lineChart');
                        if (lineCtx) {
                            try {
                                new Chart(lineCtx, {
                                    type: 'line',
                                    data: {
                                        labels: this.performanceData.lineChart.labels,
                                        datasets: [{
                                            label: 'Rating',
                                            data: this.performanceData.lineChart.data,
                                            borderColor: 'rgba(34, 197, 94, 1)',
                                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart ligne:', error);
                            }
                        }

                        // Chart Barres des buts
                        const barCtx = document.getElementById('barChart');
                        if (barCtx) {
                            try {
                                new Chart(barCtx, {
                                    type: 'bar',
                                    data: {
                                        labels: this.performanceData.barChart.labels,
                                        datasets: [{
                                            label: 'Buts',
                                            data: this.performanceData.barChart.data,
                                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                            borderColor: 'rgba(239, 68, 68, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart barres:', error);
                            }
                        }

                        // Chart Doughnut des zones d'activité
                        const doughnutCtx = document.getElementById('doughnutChart');
                        if (doughnutCtx) {
                            try {
                                new Chart(doughnutCtx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: ['Ailier Droit', 'Ailier Gauche', 'Centre', 'Défense'],
                                        datasets: [{
                                            data: this.performanceData.doughnutChart.data,
                                            backgroundColor: [
                                                'rgba(59, 130, 246, 0.8)',
                                                'rgba(34, 197, 94, 0.8)',
                                                'rgba(251, 146, 60, 0.8)',
                                                'rgba(239, 68, 68, 0.8)'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart doughnut:', error);
                            }
                        }
                    },

                    initHealthCharts() {
                        if (!this.healthData) return;
                        console.log('Initialisation des charts de santé');
                        
                        // Chart Radar de santé
                        const healthRadarCtx = document.getElementById('healthRadarChart');
                        if (healthRadarCtx) {
                            try {
                                new Chart(healthRadarCtx, {
                                    type: 'radar',
                                    data: {
                                        labels: this.healthData.radar.labels,
                                        datasets: [{
                                            label: 'Santé',
                                            data: this.healthData.radar.data,
                                            backgroundColor: 'rgba(34, 197, 94, 0.2)',
                                            borderColor: 'rgba(34, 197, 94, 1)',
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            r: {
                                                beginAtZero: true,
                                                max: 100
                                            }
                                        }
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart santé radar:', error);
                            }
                        }

                        // Chart Ligne de stress
                        const healthLineCtx = document.getElementById('healthLineChart');
                        if (healthLineCtx) {
                            try {
                                new Chart(healthLineCtx, {
                                    type: 'line',
                                    data: {
                                        labels: this.healthData.lineChart.labels,
                                        datasets: [{
                                            label: 'Niveau de Stress',
                                            data: this.healthData.lineChart.data,
                                            borderColor: 'rgba(251, 146, 60, 1)',
                                            backgroundColor: 'rgba(251, 146, 60, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart santé ligne:', error);
                            }
                        }
                    },

                    initMedicalCharts() {
                        if (!this.medicalData) return;
                        console.log('Initialisation des charts médicaux');
                        
                        // Chart Évolution des blessures
                        const medicalLineCtx = document.getElementById('medicalLineChart');
                        if (medicalLineCtx) {
                            try {
                                new Chart(medicalLineCtx, {
                                    type: 'line',
                                    data: {
                                        labels: this.medicalData.charts.lineChart.labels,
                                        datasets: [{
                                            label: 'Blessures',
                                            data: this.medicalData.charts.lineChart.data,
                                            borderColor: 'rgba(239, 68, 68, 1)',
                                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart médical ligne:', error);
                            }
                        }

                        // Chart Types de blessures
                        const medicalPieCtx = document.getElementById('medicalPieChart');
                        if (medicalPieCtx) {
                            try {
                                new Chart(medicalPieCtx, {
                                    type: 'pie',
                                    data: {
                                        labels: this.medicalData.charts.pieChart.labels,
                                        datasets: [{
                                            data: this.medicalData.charts.pieChart.data,
                                            backgroundColor: [
                                                'rgba(59, 130, 246, 0.8)',
                                                'rgba(239, 68, 68, 0.8)',
                                                'rgba(251, 146, 60, 0.8)',
                                                'rgba(34, 197, 94, 0.8)'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart médical pie:', error);
                            }
                        }
                    },

                    initDevicesCharts() {
                        if (!this.devicesData) return;
                        console.log('Initialisation des charts devices');
                        
                        // Chart Utilisation des devices
                        const devicesBarCtx = document.getElementById('devicesBarChart');
                        if (devicesBarCtx) {
                            try {
                                new Chart(devicesBarCtx, {
                                    type: 'bar',
                                    data: {
                                        labels: this.devicesData.charts.barChart.labels,
                                        datasets: [{
                                            label: 'Utilisation (heures)',
                                            data: this.devicesData.charts.barChart.data,
                                            backgroundColor: [
                                                'rgba(34, 197, 94, 0.8)',
                                                'rgba(59, 130, 246, 0.8)',
                                                'rgba(168, 85, 247, 0.8)',
                                                'rgba(251, 146, 60, 0.8)'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart devices bar:', error);
                            }
                        }

                        // Chart Temps d'écran
                        const devicesPieCtx = document.getElementById('devicesPieChart');
                        if (devicesPieCtx) {
                            try {
                                new Chart(devicesPieCtx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: this.devicesData.charts.pieChart.labels,
                                        datasets: [{
                                            data: this.devicesData.charts.pieChart.data,
                                            backgroundColor: [
                                                'rgba(59, 130, 246, 0.8)',
                                                'rgba(34, 197, 94, 0.8)',
                                                'rgba(251, 146, 60, 0.8)',
                                                'rgba(168, 85, 247, 0.8)'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart devices pie:', error);
                            }
                        }
                    },

                    initDopingCharts() {
                        if (!this.dopingData) return;
                        console.log('Initialisation des charts dopage');
                        
                        // Chart Historique des contrôles
                        const dopingLineCtx = document.getElementById('dopingLineChart');
                        if (dopingLineCtx) {
                            try {
                                new Chart(dopingLineCtx, {
                                    type: 'line',
                                    data: {
                                        labels: this.dopingData.charts.lineChart.labels,
                                        datasets: [{
                                            label: 'Contrôles Effectués',
                                            data: this.dopingData.charts.lineChart.data,
                                            borderColor: 'rgba(59, 130, 246, 1)',
                                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart dopage ligne:', error);
                            }
                        }

                        // Chart Résultats des tests
                        const dopingDoughnutCtx = document.getElementById('dopingDoughnutChart');
                        if (dopingDoughnutCtx) {
                            try {
                                new Chart(dopingDoughnutCtx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: this.dopingData.charts.doughnutChart.labels,
                                        datasets: [{
                                            data: this.dopingData.charts.doughnutChart.data,
                                            backgroundColor: [
                                                'rgba(34, 197, 94, 0.8)',
                                                'rgba(251, 146, 60, 0.8)',
                                                'rgba(239, 68, 68, 0.8)'
                                            ]
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            } catch (error) {
                                console.error('Erreur chart dopage doughnut:', error);
                            }
                        }
                    }
                }
            });

            // Monter l'application avec gestion d'erreur
            try {
                console.log('🎯 Tentative de montage...');
                app.mount('#fifa-app');
                console.log('🎯 Application Vue.js montée !');
            } catch (error) {
                console.error('❌ Erreur lors du montage:', error);
                document.body.innerHTML = '<div class="p-8 text-center text-red-600"><h1>Erreur Vue.js</h1><p>' + error.message + '</p></div>';
            }
        });
        
        // Test final
        console.log('🔍 Script terminé');
    </script>

    <!-- Script pour la navigation entre joueurs (admin uniquement) -->
    <script>
        function changePlayer() {
            const select = document.getElementById('player-select');
            const playerId = select.value;
            
            if (playerId) {
                // Rediriger vers le portail du joueur sélectionné
                window.location.href = `/player-portal/${playerId}`;
            }
        }
    </script>
</body>
</html>
