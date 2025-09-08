<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* FIFA Ultimate Team Styles */
        .fifa-ultimate-card {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fifa-ultimate-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 35px 70px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.3);
        }

        .fifa-rating-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4), inset 0 2px 4px rgba(255,255,255,0.3);
            border: 3px solid #fff;
        }

        .fifa-nav-tab {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .fifa-nav-tab.active {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
        }

        .fifa-nav-tab:hover {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35);
        }

        .fifa-position-badge {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            border: 2px solid #fff;
        }

        .stat-bar {
            height: 8px;
            background: linear-gradient(85deg, #10b981 0%, #34d399 100%);
            border-radius: 4px;
            transition: width 1s ease-out;
        }

        .health-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .health-excellent { background: #10b981; }
        .health-good { background: #f59e0b; }
        .health-warning { background: #ef4444; }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.4);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .performance-card {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .performance-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .progress-circle {
            transform: rotate(-85deg);
        }

        .progress-circle circle {
            transition: stroke-dasharray 1s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <div id="fifa-app">
        <!-- Hero Zone FIFA Ultimate (Compacte) -->
        <div class="fifa-ultimate-card text-white p-6 m-4 relative">
            <!-- Particles d'arrière-plan simples -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute w-64 h-64 bg-white/5 rounded-full -top-32 -right-32"></div>
                <div class="absolute w-48 h-48 bg-white/3 rounded-full -bottom-24 -left-24"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row items-center justify-between space-y-6 lg:space-y-0">
                    <!-- Section gauche: Photo et infos joueur -->
                    <div class="flex items-center space-x-6">
                        <!-- Photo du joueur avec fallback -->
                        <div class="relative flex-shrink-0">
                            <div class="w-32 h-32 lg:w-36 lg:h-36 rounded-full overflow-hidden border-4 border-white/20 shadow-2xl bg-gradient-to-br from-gray-400 to-gray-600">
                                <!-- Image avec multiples fallbacks -->
                                <img src="https://cdn.futbin.com/content/fifa23/img/players/158023.png" 
                                     alt="{{ $player->first_name }} {{ $player->last_name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='https://www.ea.com/fifa/ultimate-team/web-app/content/24B23FDE-7835-41C2-87A2-F3DFDB2E82/2024/fut/items/images/mobile/portraits/158023.png'; if(this.src.includes('ea.com') && this.complete && this.naturalHeight == 0) { this.style.display='none'; this.nextElementSibling.style.display='flex'; }">
                                <!-- Fallback avec initiales -->
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl" style="display: none;">
                                    LM
                                </div>
                            </div>
                            <!-- Badge position -->
                            <div class="fifa-position-badge absolute -bottom-2 -right-2 px-3 py-1 text-white font-bold text-sm">
                                {{ $player->position }}
                            </div>
                        </div>
                        
                        <!-- Informations du joueur -->
                        <div class="text-center lg:text-left">
                            <h1 class="text-3xl lg:text-4xl font-bold mb-1">{{ $player->first_name }} {{ $player->last_name }}</h1>
                            <div class="text-lg text-yellow-400 font-semibold mb-3">⭐ FIFA Ultimate Legend</div>
                            
                            <div class="flex items-center justify-center lg:justify-start space-x-4 mb-4">
                                <!-- Logo du club avec fallback -->
                                <div class="flex items-center space-x-2">
                                    <img src="{{ $player->club->logo_url ?? $player->club->logo_path ?? "/images/clubs/default_club.svg" }}" 
                                         alt="Chelsea FC" 
                                         class="w-8 h-8 object-contain"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMzYjgyZjYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIj4KPHN2ZyB2aWV3Qm{{ $player->overall_rating ?? "N/A" }}PSIwIDAgMjQgMjQiPgo8cGF0aCBkPSJNMTIgMkMxMy4xIDIgMTQgMi{{ $player->performances->count() }}IDE0IDRIMFY2SDE2VjEwSDEwVjE0SDE2VjE2SDEwVjIwQzE0IDIxLjEgMTMuMSAyMiAxMiAyMkMxMC45IDIyIDEwIDIxLjEgMTAgMjBWMjBIMFYxOEgxMFYxNEg4VjEwSDEwVjRDMTAgMi45IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo='; if(this.complete && this.naturalHeight == 0) { this.style.display='none'; this.nextElementSibling.style.display='inline-block'; }">
                                    <!-- Fallback club icon -->
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold" style="display: none;">
                                        C
                                    </div>
                                    <span class="text-lg font-medium">Chelsea FC</span>
                                </div>
                                
                                <!-- Logo de la nation avec fallback -->
                                <div class="w-px h-6 bg-white/30"></div>
                                <div class="flex items-center space-x-2">
                                    <img src="https://flagcdn.com/w40/ar.png" 
                                         alt="The Football Association" 
                                         class="w-6 h-4 object-cover rounded"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAyNCAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjE2IiBmaWxsPSIjNGY0NmU1Ii8+Cjx0ZXh0IHg9IjEyIiB5PSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0id2hpdGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiI+wqA8L3RleHQ+Cjwvc3ZnPgo='; if(this.complete && this.naturalHeight == 0) { this.style.display='none'; this.nextElementSibling.style.display='inline-block'; }">
                                    <!-- Fallback nation -->
                                    <div class="w-6 h-4 bg-gray-500 rounded text-white text-xs flex items-center justify-center" style="display: none;">
                                        TF
                                    </div>
                                    <span class="text-sm">The Football Association</span>
                                </div>
                            </div>
                            
                            <!-- Informations détaillées compactes -->
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 text-sm mb-4">
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-yellow-400">37</div>
                                    <div class="text-xs opacity-70">Âge</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-blue-400">{{ $player->height ?? "N/A" }}cm</div>
                                    <div class="text-xs opacity-70">Taille</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-green-400">{{ $player->weight ?? "N/A" }}kg</div>
                                    <div class="text-xs opacity-70">Poids</div>
                                </div>
                                <div class="text-center p-2 bg-white/10 rounded-lg">
                                    <div class="font-semibold text-purple-400">{{ $player->preferred_foot ?? "N/A" }}</div>
                                    <div class="text-xs opacity-70">Pied</div>
                                </div>
                            </div>

                            <!-- Récompenses et statistiques de carrière -->
                            <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-3">
                                <span class="px-2 py-1 bg-yellow-500 text-black text-xs font-bold rounded-full">🏆 8x Ballon d'Or</span>
                                <span class="px-2 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">⚽ 800+ Buts</span>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">🎯 300+ Assists</span>
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs font-bold rounded-full">🏆 Champions League</span>
                            </div>

                            <!-- Statistiques saison actuelle -->
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-green-400">12</div>
                                    <div class="opacity-70">Buts saison</div>
                                </div>
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-blue-400">8</div>
                                    <div class="opacity-70">Assists</div>
                                </div>
                                <div class="p-2 bg-white/5 rounded">
                                    <div class="font-bold text-purple-400">{{ $player->performances->sum("goals") ?? 0 }}</div>
                                    <div class="opacity-70">Matchs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section droite: Stats FIFA et santé -->
                    <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6">
                        <!-- Ratings FIFA compacts -->
                        <div class="text-center">
                            <div class="flex space-x-3 mb-2">
                                <div class="fifa-rating-badge text-black p-4">
                                    <div class="text-3xl font-bold">93</div>
                                    <div class="text-xs">OVR</div>
                                </div>
                                <div class="fifa-rating-badge text-black p-3">
                                    <div class="text-2xl font-bold">82</div>
                                    <div class="text-xs">POT</div>
                                </div>
                            </div>
                            <div class="text-xs text-white/85">FIFA Ultimate</div>
                        </div>
                        
                        <!-- Score de santé FIT compact -->
                        <div class="text-center">
                            <div class="relative w-20 h-20 mb-2">
                                <svg class="w-full h-full progress-circle" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.2)" stroke-width="8" fill="none"/>
                                    <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="8" fill="none"
                                            stroke-dasharray="251.33" stroke-dashoffset="37.7" stroke-linecap="round"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-green-400">85</span>
                                </div>
                            </div>
                            <div class="text-xs font-medium">Score FIT</div>
                        </div>
                        
                        <!-- Infos importantes détaillées -->
                        <div class="space-y-2 text-sm">
                            <div class="p-3 bg-gradient-to-r from-green-500/20 to-green-600/20 rounded-lg border border-green-500/30">
                                <div class="text-xs text-green-300 font-medium">Risque de blessure</div>
                                <div class="font-bold text-green-400">15% - FAIBLE</div>
                                <div class="w-full bg-green-850/50 rounded-full h-1 mt-1">
                                    <div class="bg-green-500 h-1 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-yellow-500/20 to-yellow-600/20 rounded-lg border border-yellow-500/30">
                                <div class="text-xs text-yellow-300 font-medium">Valeur marchande</div>
                                <div class="font-bold text-yellow-400">€180M</div>
                                <div class="text-xs text-yellow-300">↗️ +€15M ce mois</div>
                            </div>
                            
                            <div class="p-3 bg-gradient-to-r from-blue-500/20 to-blue-600/20 rounded-lg border border-blue-500/30">
                                <div class="text-xs text-blue-300 font-medium">Disponibilité</div>
                                <div class="font-bold text-blue-400">✅ DISPONIBLE</div>
                                <div class="text-xs text-blue-300">Prochain match: Dimanche</div>
                            </div>

                            <!-- Indicateurs additionnels -->
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                <div class="text-center p-2 bg-white/5 rounded">
                                    <div class="font-bold text-orange-400">85%</div>
                                    <div class="opacity-70">Forme</div>
                                </div>
                                <div class="text-center p-2 bg-white/5 rounded">
                                    <div class="font-bold text-cyan-400">88%</div>
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
                            <span class="text-sm font-medium">Saison 2024-25</span>
                            <span class="text-sm text-white/80">75% complétée</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2 mb-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-white/70">
                            <span>{{ $player->performances->sum("goals") ?? 0 }} matchs joués</span>
                            <span>10 matchs restants</span>
                        </div>
                    </div>

                    <!-- Performances récentes -->
                    <div class="p-4 bg-white/5 rounded-xl backdrop-blur-sm">
                        <div class="text-sm font-medium mb-2">Performances récentes</div>
                        <div class="flex justify-between items-center text-xs">
                            <span>5 derniers matchs:</span>
                            <div class="flex space-x-1">
                                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs">W</div>
                                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs">W</div>
                                <div class="w-4 h-4 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xs">D</div>
                                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs">W</div>
                                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs">W</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 mb-6">
            <div class="flex flex-wrap gap-4">
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['fifa-nav-tab relative px-6 py-3 rounded-lg text-white font-medium transition-all', activeTab === tab.id ? 'active' : '']"
                >
                    <i :class="tab.icon" class="mr-2"></i>
                    <span v-text="tab.name"></span>
                    <span v-if="tab.count" class="notification-badge" v-text="tab.count"></span>
                </button>
            </div>
        </div>

        <!-- Contenu des onglets -->
        <div class="px-6 pb-6">
            <!-- Onglet Performance -->
            <div v-show="activeTab === 'performance'" class="space-y-6">
                <!-- Résumé de la saison -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="performance-card p-6 text-center">
                        <div class="text-3xl font-bold text-green-600" v-text="seasonSummary.goals.total"></div>
                        <div class="text-sm text-gray-600">Buts marqués</div>
                        <div class="text-xs text-green-600 mt-1" v-text="'↗️ ' + seasonSummary.goals.trend + ' vs mois dernier'"></div>
                    </div>
                    <div class="performance-card p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600" v-text="seasonSummary.assists.total"></div>
                        <div class="text-sm text-gray-600">Passes décisives</div>
                        <div class="text-xs text-blue-600 mt-1" v-text="'↗️ +' + seasonSummary.assists.trend + ' vs mois dernier'"></div>
                    </div>
                    <div class="performance-card p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600" v-text="seasonSummary.matches.distance"></div>
                        <div class="text-sm text-gray-600">Distance parcourue</div>
                        <div class="text-xs text-gray-600 mt-1" v-text="'Moyenne/match: ' + (parseFloat(seasonSummary.matches.distance.replace(' km', '')) / seasonSummary.matches.total).toFixed(1) + 'km'"></div>
                    </div>
                    <div class="performance-card p-6 text-center">
                        <div class="text-3xl font-bold text-orange-600" v-text="seasonSummary.matches.rating"></div>
                        <div class="text-sm text-gray-600">Rating moyen</div>
                        <div class="text-xs text-orange-600 mt-1" v-text="'Basé sur ' + seasonSummary.matches.total + ' matchs'"></div>
                    </div>
                </div>

                <!-- Évolution des performances -->
                <div class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                        Évolution des Performances - Saison 2024/25
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="chart-container">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <div class="chart-container">
                                <canvas id="comparisonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques détaillées par catégorie -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Performances Offensives -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-futbol text-green-600 mr-2"></i>
                            Attaque
                        </h4>
                        <div class="space-y-4">
                            <div v-for="stat in offensiveStats" :key="stat.name" class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700" v-text="stat.name"></span>
                                    <span class="font-bold text-gray-800" v-text="stat.display"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="stat-bar h-2 rounded-full bg-green-500" :style="{width: stat.percentage + '%'}"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span v-text="'Moy. équipe: ' + stat.teamAvg"></span>
                                    <span v-text="'Moy. ligue: ' + stat.leagueAvg"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performances Physiques -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-dumbbell text-purple-600 mr-2"></i>
                            Physique
                        </h4>
                        <div class="space-y-4">
                            <div v-for="stat in physicalStats" :key="stat.name" class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700" v-text="stat.name"></span>
                                    <span class="font-bold text-gray-800" v-text="stat.display"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="stat-bar h-2 rounded-full bg-purple-500" :style="{width: stat.percentage + '%'}"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span v-text="'Moy. équipe: ' + stat.teamAvg"></span>
                                    <span v-text="'Moy. ligue: ' + stat.leagueAvg"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performances Techniques -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-cogs text-blue-600 mr-2"></i>
                            Technique
                        </h4>
                        <div class="space-y-4">
                            <div v-for="stat in technicalStats" :key="stat.name" class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700" v-text="stat.name"></span>
                                    <span class="font-bold text-gray-800" v-text="stat.display"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="stat-bar h-2 rounded-full bg-blue-500" :style="{width: stat.percentage + '%'}"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span v-text="'Moy. équipe: ' + stat.teamAvg"></span>
                                    <span v-text="'Moy. ligue: ' + stat.leagueAvg"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Records personnels et distinctions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Records de carrière -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                            Records Personnels
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-800">Plus de buts en un match</div>
                                    <div class="text-sm text-gray-600">vs Real Madrid - 16/03/2024</div>
                                </div>
                                <div class="text-2xl font-bold text-yellow-600">4</div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-800">Plus de passes décisives</div>
                                    <div class="text-sm text-gray-600">vs Monaco - 22/04/2024</div>
                                </div>
                                <div class="text-2xl font-bold text-blue-600">3</div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-800">Plus longue distance</div>
                                    <div class="text-sm text-gray-600">vs Liverpool - 08/05/2024</div>
                                </div>
                                <div class="text-2xl font-bold text-green-600">12.8km</div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-800">Vitesse de pointe</div>
                                    <div class="text-sm text-gray-600">vs Bayern - 15/04/2024</div>
                                </div>
                                <div class="text-2xl font-bold text-purple-600">34.2km/h</div>
                            </div>
                        </div>
                    </div>

                    <!-- Heatmap et positions -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-map-marked-alt text-red-600 mr-2"></i>
                            Zones d'Activité
                        </h4>
                        <div class="mb-4">
                            <div class="text-center text-sm text-gray-600 mb-2">Heatmap - 5 derniers matchs</div>
                            <div class="relative bg-green-100 rounded-lg p-4" style="height: 200px;">
                                <!-- Terrain de football simplifié -->
                                <div class="absolute inset-2 border-2 border-white rounded">
                                    <!-- Zone centrale chaude -->
                                    <div class="absolute top-1/4 right-1/4 w-1/3 h-1/2 bg-red-300 rounded-full opacity-80"></div>
                                    <!-- Zone droite -->
                                    <div class="absolute top-1/3 right-1/6 w-1/4 h-1/3 bg-orange-300 rounded-full opacity-60"></div>
                                    <!-- Zone d'attaque -->
                                    <div class="absolute top-1/2 right-1/12 w-1/5 h-1/4 bg-yellow-300 rounded-full opacity-70"></div>
                                </div>
                                <div class="absolute bottom-1 right-1 text-xs text-gray-500">Zone préférée: Ailier droit</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="text-center p-2 bg-red-50 rounded">
                                <div class="font-bold text-red-600">68%</div>
                                <div class="text-gray-600">Côté droit</div>
                            </div>
                            <div class="text-center p-2 bg-orange-50 rounded">
                                <div class="font-bold text-orange-600">{{ $player->performances->count() }}%</div>
                                <div class="text-gray-600">Surface adverse</div>
                            </div>
                            <div class="text-center p-2 bg-blue-50 rounded">
                                <div class="font-bold text-blue-600">156</div>
                                <div class="text-gray-600">Touches/match</div>
                            </div>
                            <div class="text-center p-2 bg-green-50 rounded">
                                <div class="font-bold text-green-600">23%</div>
                                <div class="text-gray-600">Dribbles réussis</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performances par adversaire -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>
                        Performances vs Grandes Équipes
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Adversaire</th>
                                    <th class="text-center py-2">Matchs</th>
                                    <th class="text-center py-2">Buts</th>
                                    <th class="text-center py-2">Assists</th>
                                    <th class="text-center py-2">Note Moy.</th>
                                    <th class="text-center py-2">Dernière perf.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 font-medium">Real Madrid</td>
                                    <td class="text-center">3</td>
                                    <td class="text-center text-green-600 font-bold">5</td>
                                    <td class="text-center text-blue-600 font-bold">2</td>
                                    <td class="text-center">8.7</td>
                                    <td class="text-center text-green-600">⭐ Excellent</td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 font-medium">Manchester City</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center text-green-600 font-bold">2</td>
                                    <td class="text-center text-blue-600 font-bold">3</td>
                                    <td class="text-center">8.2</td>
                                    <td class="text-center text-green-600">⭐ Très bon</td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 font-medium">Bayern Munich</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center text-green-600 font-bold">1</td>
                                    <td class="text-center text-blue-600 font-bold">1</td>
                                    <td class="text-center">7.5</td>
                                    <td class="text-center text-yellow-600">✓ Correct</td>
                                </tr>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 font-medium">Liverpool</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center text-green-600 font-bold">1</td>
                                    <td class="text-center text-blue-600 font-bold">0</td>
                                    <td class="text-center">7.8</td>
                                    <td class="text-center text-green-600">✓ Bon</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Notifications (contenu complet comme avant) -->
            <div v-show="activeTab === 'notifications'" class="space-y-6">
                <!-- Filtres de notifications -->
                <div class="performance-card p-4">
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="filter in notificationFilters" 
                            :key="filter.id"
                            @click="activeNotificationFilter = filter.id"
                            :class="['px-4 py-2 rounded-lg font-medium transition-all', activeNotificationFilter === filter.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                        >
                            <i :class="filter.icon" class="mr-2"></i>
                            <span v-text="filter.name"></span>
                            <span v-if="filter.count" class="ml-2 px-2 py-1 bg-white/20 rounded-full text-xs" v-text="filter.count"></span>
                        </button>
                    </div>
                </div>

                <!-- Notifications Équipe Nationale -->
                <div v-show="activeNotificationFilter === 'all' || activeNotificationFilter === 'national'" class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-flag text-blue-600 mr-3"></i>
                        Équipe Nationale
                    </h3>
                    <div class="space-y-4">
                        <div v-for="notification in notificationData.nationalTeam" :key="notification.id" 
                             class="flex items-start p-4 rounded-lg transition-all hover:shadow-md"
                             :class="notification.urgent ? 'bg-red-50 border-l-4 border-red-500' : 'bg-blue-50 border-l-4 border-blue-500'">
                            <div class="flex-shrink-0 mr-4">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white', notification.urgent ? 'bg-red-500' : 'bg-blue-500']">
                                    <i :class="notification.icon"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800" v-text="notification.title"></h4>
                                <p class="text-gray-600 mb-2" v-text="notification.message"></p>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="font-medium text-blue-600" v-text="notification.date"></span>
                                    <span class="text-gray-500" v-text="notification.location"></span>
                                </div>
                            </div>
                            <div v-if="notification.urgent" class="flex-shrink-0">
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full font-bold">URGENT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sessions d'Entraînement -->
                <div v-show="activeNotificationFilter === 'all' || activeNotificationFilter === 'training'" class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-dumbbell text-green-600 mr-3"></i>
                        Sessions d'Entraînement
                    </h3>
                    <div class="space-y-4">
                        <div v-for="session in notificationData.trainingSessions" :key="session.id" 
                             class="flex items-start p-4 bg-green-50 border-l-4 border-green-500 rounded-lg hover:shadow-md transition-all">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                                    <i :class="session.icon"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800" v-text="session.title"></h4>
                                <p class="text-gray-600 mb-2" v-text="session.description"></p>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="font-medium text-green-600" v-text="session.date + ' • ' + session.time"></span>
                                    <span class="text-gray-500" v-text="session.location"></span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span :class="['px-2 py-1 text-xs rounded-full font-bold', session.mandatory ? 'bg-orange-500 text-white' : 'bg-gray-500 text-white']" 
                                      v-text="session.mandatory ? 'OBLIGATOIRE' : 'OPTIONNEL'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matchs -->
                <!-- Matchs à Venir -->
                <div v-show="activeNotificationFilter === 'all' || activeNotificationFilter === 'matches'" class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-futbol text-orange-600 mr-3"></i>
                        Matchs à Venir
                    </h3>
                    <div class="space-y-4">
                        <div v-for="match in notificationData.matches" :key="match.id" 
                             class="flex items-start p-4 bg-orange-50 border-l-4 border-orange-500 rounded-lg hover:shadow-md transition-all">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white">
                                    <i :class="match.icon"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800" v-text="match.title"></h4>
                                <p class="text-gray-600 mb-2" v-text="match.competition + ' • ' + match.stadium"></p>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="font-medium text-orange-600" v-text="match.date + ' • ' + match.time"></span>
                                    <span class="text-gray-500" v-text="'RDV: ' + match.meetingTime"></span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span :class="['px-2 py-1 text-xs rounded-full font-bold', 
                                              match.status === 'convoqué' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white']" 
                                      v-text="match.status.toUpperCase()"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rendez-vous Médicaux -->
                <div v-show="activeNotificationFilter === 'all' || activeNotificationFilter === 'medical'" class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-md text-red-600 mr-3"></i>
                        Rendez-vous Médicaux
                    </h3>
                    <div class="space-y-4">
                        <div v-for="appointment in notificationData.medicalAppointments" :key="appointment.id" 
                             class="flex items-start p-4 rounded-lg hover:shadow-md transition-all"
                             :class="appointment.urgent ? 'bg-red-50 border-l-4 border-red-500' : 'bg-blue-50 border-l-4 border-blue-500'">
                            <div class="flex-shrink-0 mr-4">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white', appointment.urgent ? 'bg-red-500' : 'bg-blue-500']">
                                    <i :class="appointment.icon"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800" v-text="appointment.type"></h4>
                                <p class="text-gray-600 mb-2" v-text="appointment.purpose"></p>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="font-medium" :class="appointment.urgent ? 'text-red-600' : 'text-blue-600'" v-text="appointment.date + ' • ' + appointment.time"></span>
                                    <span class="text-gray-500" v-text="appointment.location"></span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1" v-text="'Dr. ' + appointment.doctor"></div>
                            </div>
                            <div v-if="appointment.urgent" class="flex-shrink-0">
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full font-bold">URGENT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertes Réseaux Sociaux -->
                <div v-show="activeNotificationFilter === 'all' || activeNotificationFilter === 'social'" class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-share-alt text-purple-600 mr-3"></i>
                        Alertes Réseaux Sociaux
                    </h3>
                    <div class="space-y-4">
                        <div v-for="alert in notificationData.socialAlerts" :key="alert.id" 
                             class="flex items-start p-4 border rounded-lg hover:shadow-md transition-all"
                             :class="alert.sentiment === 'positive' ? 'border-green-200 bg-green-50' : 
                                    alert.sentiment === 'negative' ? 'border-red-200 bg-red-50' : 
                                    'border-gray-200 bg-gray-50'">
                            <div class="flex-shrink-0 mr-4">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white',
                                             alert.platform === 'instagram' ? 'bg-pink-500' :
                                             alert.platform === 'facebook' ? 'bg-blue-600' :
                                             alert.platform === 'google' ? 'bg-red-500' : 'bg-gray-500']">
                                    <i :class="alert.platform === 'instagram' ? 'fab fa-instagram' :
                                              alert.platform === 'facebook' ? 'fab fa-facebook-f' :
                                              alert.platform === 'google' ? 'fas fa-search' : 'fas fa-globe'"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800" v-text="alert.title"></h4>
                                <p class="text-gray-600 mb-2" v-text="alert.content"></p>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="text-gray-500" v-text="alert.timestamp"></span>
                                    <span class="text-gray-500" v-text="alert.views + ' vues'"></span>
                                    <span class="text-gray-500" v-text="alert.engagement + ' interactions'"></span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex flex-col items-end space-y-2">
                                <span :class="['px-2 py-1 text-xs rounded-full font-bold',
                                              alert.sentiment === 'positive' ? 'bg-green-100 text-green-800' :
                                              alert.sentiment === 'negative' ? 'bg-red-100 text-red-800' :
                                              'bg-gray-100 text-gray-800']" 
                                      v-text="alert.sentiment.toUpperCase()"></span>
                                <span v-if="alert.needsResponse" class="px-2 py-1 bg-orange-500 text-white text-xs rounded-full font-bold">ACTION REQUISE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Santé & Bien-être -->
            <div v-show="activeTab === 'health'" class="space-y-6">
                <!-- Tableau de bord santé global -->
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                    <div class="performance-card p-4 text-center">
                        <div class="text-3xl font-bold text-green-600" v-text="healthData.globalScore"></div>
                        <div class="text-sm text-gray-600">Score FIT Global</div>
                        <div class="text-xs text-green-600 mt-1">↗️ +2 points</div>
                    </div>
                    <div class="performance-card p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600" v-text="healthData.physicalScore + '%'"></div>
                        <div class="text-sm text-gray-600">Condition Physique</div>
                        <div class="text-xs text-blue-600 mt-1">↗️ +3%</div>
                    </div>
                    <div class="performance-card p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600" v-text="healthData.sleepScore + '%'"></div>
                        <div class="text-sm text-gray-600">Qualité Sommeil</div>
                        <div class="text-xs text-purple-600 mt-1">↗️ +5%</div>
                    </div>
                    <div class="performance-card p-4 text-center">
                        <div class="text-3xl font-bold text-orange-600" v-text="healthData.injuryRisk + '%'"></div>
                        <div class="text-sm text-gray-600">Risque Blessure</div>
                        <div class="text-xs text-green-600 mt-1">↘️ -2%</div>
                    </div>
                </div>

                <!-- Monitoring temps réel -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Paramètres Vitaux Temps Réel -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                            Monitoring Temps Réel
                        </h4>
                        <div class="space-y-4">
                            <div v-for="vital in healthData.vitals" :key="vital.name" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i :class="vital.icon + ' text-xl'" :style="{color: vital.color}"></i>
                                    <div>
                                        <div class="font-medium text-gray-800" v-text="vital.name"></div>
                                        <div class="text-sm" :class="vital.status === 'normal' ? 'text-green-600' : vital.status === 'warning' ? 'text-yellow-600' : 'text-red-600'" 
                                             v-text="vital.status === 'normal' ? 'Normal' : vital.status === 'warning' ? 'Attention' : 'Critique'"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-800" v-text="vital.value"></div>
                                    <div class="text-xs text-gray-500" v-text="vital.unit"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hydratation et Nutrition -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tint text-blue-600 mr-2"></i>
                            Hydratation & Nutrition
                        </h4>
                        <div class="space-y-4">
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-800">Hydratation Quotidienne</span>
                                    <span class="text-blue-600 font-bold">2.8L / 3.5L</span>
                                </div>
                                <div class="w-full bg-blue-200 rounded-full h-3">
                                    <div class="bg-blue-500 h-3 rounded-full" style="width: 80%"></div>
                                </div>
                                <div class="text-xs text-blue-600 mt-1">80% de l'objectif</div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                    <span class="text-sm">🥗 Protéines</span>
                                    <span class="font-bold text-green-600">82g / 85g</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                                    <span class="text-sm">🍞 Glucides</span>
                                    <span class="font-bold text-yellow-600">2{{ $player->performances->count() }}g / {{ $player->performances->sum("goals") ?? 0 }}0g</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                                    <span class="text-sm">🥑 Lipides</span>
                                    <span class="font-bold text-orange-600">62g / 70g</span>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-purple-50 rounded">
                                    <span class="text-sm">🔥 Calories</span>
                                    <span class="font-bold text-purple-600">{{ $player->performances->sum("goals") ?? 0 }}40 / 3200</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sommeil Détaillé -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-moon text-indigo-600 mr-2"></i>
                            Analyse du Sommeil
                        </h4>
                        <div class="space-y-4">
                            <div class="text-center p-3 bg-indigo-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600">8h 15min</div>
                                <div class="text-sm text-gray-600">Nuit dernière</div>
                                <div class="text-xs text-indigo-600 mt-1">✓ Objectif atteint</div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Sommeil profond</span>
                                    <span class="font-bold text-indigo-600">2h 30min (30%)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Sommeil REM</span>
                                    <span class="font-bold text-purple-600">1h {{ $player->performances->count() }}min (21%)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Sommeil léger</span>
                                    <span class="font-bold text-blue-600">3h 50min (47%)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Réveils</span>
                                    <span class="font-bold text-gray-600">2 fois</span>
                                </div>
                            </div>
                            <div class="p-2 bg-green-50 rounded text-center">
                                <div class="text-sm font-medium text-green-800">Score Récupération</div>
                                <div class="text-lg font-bold text-green-600">87/100</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stress et Bien-être Mental -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-brain text-purple-600 mr-2"></i>
                            Santé Mentale & Stress
                        </h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-xl font-bold text-purple-600">Faible</div>
                                    <div class="text-sm text-gray-600">Niveau Stress</div>
                                    <div class="text-xs text-green-600 mt-1">↘️ -15%</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-xl font-bold text-green-600">Excellent</div>
                                    <div class="text-sm text-gray-600">Humeur</div>
                                    <div class="text-xs text-green-600 mt-1">↗️ +8%</div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-800">🧘 Méditation Quotidienne</div>
                                        <div class="text-sm text-gray-600">Séance de 15 min</div>
                                    </div>
                                    <div class="text-blue-600 font-bold">✓</div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-800">📚 Session Psychologue</div>
                                        <div class="text-sm text-gray-600">Prochaine: Jeudi 14h</div>
                                    </div>
                                    <div class="text-yellow-600 font-bold">📅</div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-800">⚖️ Équilibre Vie/Sport</div>
                                        <div class="text-sm text-gray-600">Score cette semaine</div>
                                    </div>
                                    <div class="text-green-600 font-bold">8.5/10</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-dumbbell text-orange-600 mr-2"></i>
                            Condition Physique Avancée
                        </h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="text-center p-3 bg-red-50 rounded-lg">
                                    <div class="text-lg font-bold text-red-600">52 bpm</div>
                                    <div class="text-sm text-gray-600">FC Repos</div>
                                    <div class="text-xs text-green-600">↘️ -2 bpm</div>
                                </div>
                                <div class="text-center p-3 bg-orange-50 rounded-lg">
                                    <div class="text-lg font-bold text-orange-600">185 bpm</div>
                                    <div class="text-sm text-gray-600">FC Max</div>
                                    <div class="text-xs text-gray-600">Stable</div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">VO2 Max</span>
                                    <span class="font-bold text-green-600">68.5 ml/kg/min</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 88%"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Composition Corporelle</span>
                                    <span class="font-bold text-blue-600">8.2% MG</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 78%"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Masse Musculaire</span>
                                    <span class="font-bold text-purple-600">65.8 kg</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">Flexibilité</span>
                                    <span class="font-bold text-indigo-600">Excellente</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tendances et Évolution -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                        Évolution & Tendances (30 derniers jours)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="chart-container mb-4">
                                <canvas id="healthTrendsChart"></canvas>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="p-3 border-l-4 border-green-500 bg-green-50">
                                <div class="font-medium text-green-800">📈 Amélioration Notable</div>
                                <div class="text-sm text-green-700">Qualité du sommeil en hausse constante (+12%)</div>
                            </div>
                            <div class="p-3 border-l-4 border-blue-500 bg-blue-50">
                                <div class="font-medium text-blue-800">💪 Progression Physique</div>
                                <div class="text-sm text-blue-700">VO2 Max et endurance en amélioration</div>
                            </div>
                            <div class="p-3 border-l-4 border-yellow-500 bg-yellow-50">
                                <div class="font-medium text-yellow-800">⚠️ Attention Nutrition</div>
                                <div class="text-sm text-yellow-700">Apport calorique légèrement insuffisant</div>
                            </div>
                            <div class="p-3 border-l-4 border-purple-500 bg-purple-50">
                                <div class="font-medium text-purple-800">🧠 Bien-être Mental</div>
                                <div class="text-sm text-purple-700">Niveau de stress maîtrisé, continuer méditation</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facteurs SDOH (Social Determinants of Health) -->
                <div class="performance-card p-6 bg-white rounded-2xl shadow-lg">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users text-teal-600 mr-3"></i>
                        Santé & Bien-être – Facteurs SDOH
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Radar Chart SDOH -->
                        <div class="w-full">
                            <div class="chart-container mb-4" style="height: 400px; min-height: 400px;">
                                <canvas id="sdohRadarChart"></canvas>
                            </div>
                        </div>

                        <!-- SDOH Details -->
                        <div class="w-full space-y-4">
                            <p class="text-gray-700 mb-4">
                                Cet indicateur <strong>SDOH</strong> (Social Determinants of Health) donne une vision
                                à {{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}0° de l'état de bien-être global du joueur, en intégrant les
                                facteurs sociaux, environnementaux et comportementaux.
                            </p>
                            
                            <!-- SDOH Factors breakdown -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-teal-50 rounded-lg border-l-4 border-teal-500">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-home text-teal-600"></i>
                                        <span class="font-medium text-gray-800">Environnement de vie</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-teal-600">85/100</div>
                                        <div class="text-xs text-gray-600">Qualité logement, stabilité</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-heart text-green-600"></i>
                                        <span class="font-medium text-gray-800">Soutien social</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-green-600">85/100</div>
                                        <div class="text-xs text-gray-600">Famille, amis, entourage</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-md text-blue-600"></i>
                                        <span class="font-medium text-gray-800">Accès aux soins</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-blue-600">75/100</div>
                                        <div class="text-xs text-gray-600">Rapidité, disponibilité</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                                        <span class="font-medium text-gray-800">Situation financière</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-yellow-600">80/100</div>
                                        <div class="text-xs text-gray-600">Sécurité économique</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-brain text-purple-600"></i>
                                        <span class="font-medium text-gray-800">Bien-être mental</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-purple-600">78/100</div>
                                        <div class="text-xs text-gray-600">Stress, motivation</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Overall SDOH Score -->
                            <div class="mt-6 p-4 bg-gradient-to-r from-teal-50 to-green-50 rounded-lg border border-teal-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-gray-800">Score SDOH Global</div>
                                        <div class="text-sm text-gray-600">Déterminants sociaux de santé</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-teal-600">83.6/100</div>
                                        <div class="text-xs text-teal-700">↗️ +2.4 ce mois</div>
                                    </div>
                                </div>
                                <div class="w-full bg-teal-200 rounded-full h-3 mt-3">
                                    <div class="bg-gradient-to-r from-teal-500 to-green-500 h-3 rounded-full" style="width: 83.6%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommandations Personnalisées IA -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-robot text-cyan-600 mr-2"></i>
                        Recommandations IA Personnalisées
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="recommendation in healthData.recommendations" :key="recommendation.id" 
                             class="p-4 rounded-lg border-l-4" :style="{'border-left-color': recommendation.color, 'background-color': recommendation.color + '15'}">
                            <div class="flex items-start space-x-3">
                                <i :class="recommendation.icon + ' text-xl mt-1'" :style="{color: recommendation.color}"></i>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800" v-text="recommendation.title"></div>
                                    <div class="text-sm text-gray-600 mt-1" v-text="recommendation.description"></div>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs px-2 py-1 rounded-full text-white font-bold" :style="{'background-color': recommendation.color}" 
                                              v-text="recommendation.priority"></span>
                                        <span class="text-xs text-gray-500" v-text="recommendation.impact"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Objectifs et Défis -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-target text-red-600 mr-2"></i>
                        Objectifs & Défis de la Semaine
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-800">💧 Hydratation</span>
                                <span class="text-blue-600 font-bold">5/7</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2 mb-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 71%"></div>
                            </div>
                            <div class="text-xs text-blue-700">3.5L par jour - 71% complété</div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-green-800">🏃‍♂️ Cardio</span>
                                <span class="text-green-600 font-bold">4/5</span>
                            </div>
                            <div class="w-full bg-green-200 rounded-full h-2 mb-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 80%"></div>
                            </div>
                            <div class="text-xs text-green-700">5 sessions - 80% complété</div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-purple-800">🧘 Méditation</span>
                                <span class="text-purple-600 font-bold">6/7</span>
                            </div>
                            <div class="w-full bg-purple-200 rounded-full h-2 mb-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 86%"></div>
                            </div>
                            <div class="text-xs text-purple-700">15min quotidien - 86% complété</div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-orange-800">😴 Sommeil</span>
                                <span class="text-orange-600 font-bold">6/7</span>
                            </div>
                            <div class="w-full bg-orange-200 rounded-full h-2 mb-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 86%"></div>
                            </div>
                            <div class="text-xs text-orange-700">8h minimum - 86% complété</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Médical (simplifié) -->
            <div v-show="activeTab === 'medical'" class="space-y-6">
                <div class="performance-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-md text-blue-600 mr-3"></i>
                            Dossier Médical Complet
                        </h3>
                        <div class="flex space-x-3">
                            <button onclick="openShareModalSimple()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                                <i class="fas fa-share mr-2"></i>Partager
                            </button>
                            <button onclick="printMedicalRecord()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all">
                                <i class="fas fa-print mr-2"></i>Imprimer
                            </button>
                            <button onclick="exportMedicalPDF()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                                <i class="fas fa-file-pdf mr-2"></i>Export PDF
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600" v-text="medicalData.healthRecords.length"></div>
                            <div class="text-sm text-gray-600">Dossiers de Santé</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600" v-text="medicalData.pcmas.length"></div>
                            <div class="text-sm text-gray-600">Évaluations PCMA</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600" v-text="medicalData.predictions.length"></div>
                            <div class="text-sm text-gray-600">Prédictions IA</div>
                        </div>
                    </div>

                    <!-- Dossiers récents -->
                    <div class="space-y-3 mb-6">
                        <h4 class="font-bold text-gray-800">📋 Dossiers Récents</h4>
                        <div v-for="record in medicalData.healthRecords.slice(0, 3)" :key="record.id" 
                             class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium" v-text="record.title"></div>
                                <div class="text-sm text-gray-600" v-text="record.doctor + ' • ' + record.date"></div>
                            </div>
                            <span :class="['px-3 py-1 rounded-full text-xs font-bold', record.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800']" 
                                  v-text="record.status === 'completed' ? 'Terminé' : 'En attente'"></span>
                        </div>
                    </div>

                    <!-- Historique des Blessures -->
                    <div class="space-y-3 mb-6">
                        <h4 class="font-bold text-gray-800">🩹 Historique des Blessures</h4>
                        <div v-for="injury in medicalData.injuries" :key="injury.id" 
                             class="p-4 border rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium" v-text="injury.type"></div>
                                    <div class="text-sm text-gray-600" v-text="injury.location + ' • ' + injury.date"></div>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs font-bold', 
                                              injury.status === 'recovered' ? 'bg-green-100 text-green-800' : 
                                              injury.status === 'recovering' ? 'bg-orange-100 text-orange-800' : 
                                              'bg-red-100 text-red-800']" 
                                      v-text="injury.status === 'recovered' ? 'GUÉRI' : 
                                             injury.status === 'recovering' ? 'EN RÉCUPÉRATION' : 
                                             'ACTIF'"></span>
                            </div>
                            <div class="text-sm text-gray-700 mb-2" v-text="injury.description"></div>
                            <div v-if="injury.status === 'recovering'" class="mb-2">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Récupération</span>
                                    <span v-text="injury.recovery + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-500" 
                                         :style="{width: injury.recovery + '%'}"></div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">
                                <span>Traitement: </span><span v-text="injury.treatment"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des Maladies -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-gray-800">🦠 Historique des Maladies</h4>
                        <div v-for="illness in medicalData.illnesses" :key="illness.id" 
                             class="p-4 border rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium" v-text="illness.type"></div>
                                    <div class="text-sm text-gray-600" v-text="illness.diagnosis + ' • ' + illness.date"></div>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs font-bold', 
                                              illness.status === 'recovered' ? 'bg-green-100 text-green-800' : 
                                              'bg-orange-100 text-orange-800']" 
                                      v-text="illness.status === 'recovered' ? 'GUÉRI' : 'EN COURS'"></span>
                            </div>
                            <div class="text-sm text-gray-700 mb-2" v-text="illness.description"></div>
                            <div class="text-xs text-gray-500">
                                <span>Traitement: </span><span v-text="illness.treatment"></span><br>
                                <span>Médecin: </span><span v-text="illness.doctor"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des Blessures -->
                    <div class="performance-card p-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bandage text-red-600 mr-2"></i>
                            Historique des Blessures
                        </h4>
                        <div class="space-y-4">
                            <div v-for="injury in medicalData.injuries" :key="injury.id" class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="font-bold text-gray-800" v-text="injury.type"></h5>
                                        <p class="text-sm text-gray-600" v-text="injury.location + ' • ' + injury.date"></p>
                                    </div>
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', 
                                                  injury.status === 'recovered' ? 'bg-green-100 text-green-800' : 
                                                  injury.status === 'healing' ? 'bg-yellow-100 text-yellow-800' : 
                                                  'bg-red-100 text-red-800']" 
                                          v-text="injury.status === 'recovered' ? 'Guéri' : 
                                                  injury.status === 'healing' ? 'En guérison' : 'Actif'"></span>
                                </div>
                                <p class="text-sm text-gray-700 mb-3" v-text="injury.description"></p>
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Récupération</span>
                                        <span v-text="injury.recovery_progress + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div :class="['h-2 rounded-full', 
                                                     injury.recovery_progress >= 85 ? 'bg-green-500' : 
                                                     injury.recovery_progress >= 50 ? 'bg-yellow-500' : 'bg-red-500']" 
                                             :style="{width: injury.recovery_progress + '%'}"></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span>Médecin: </span><span v-text="injury.doctor"></span> • 
                                    <span>Durée estimée: </span><span v-text="injury.estimated_duration"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des Maladies -->
                    <div class="performance-card p-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-stethoscope text-purple-600 mr-2"></i>
                            Historique des Maladies
                        </h4>
                        <div class="space-y-4">
                            <div v-for="illness in medicalData.illnesses" :key="illness.id" class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="font-bold text-gray-800" v-text="illness.name"></h5>
                                        <p class="text-sm text-gray-600" v-text="illness.date_diagnosed + ' • Dr. ' + illness.doctor"></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-bold',
                                                      illness.severity === 'mild' ? 'bg-green-100 text-green-800' :
                                                      illness.severity === 'moderate' ? 'bg-yellow-100 text-yellow-800' :
                                                      'bg-red-100 text-red-800']"
                                              v-text="illness.severity === 'mild' ? 'Léger' :
                                                      illness.severity === 'moderate' ? 'Modéré' : 'Sévère'"></span>
                                        <span :class="['px-2 py-1 rounded-full text-xs font-bold',
                                                      illness.status === 'cured' ? 'bg-green-100 text-green-800' :
                                                      illness.status === 'chronic' ? 'bg-blue-100 text-blue-800' :
                                                      illness.status === 'treating' ? 'bg-yellow-100 text-yellow-800' :
                                                      'bg-red-100 text-red-800']"
                                              v-text="illness.status === 'cured' ? 'Guéri' :
                                                      illness.status === 'chronic' ? 'Chronique' :
                                                      illness.status === 'treating' ? 'En traitement' : 'Actif'"></span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 mb-3" v-text="illness.description"></p>
                                <div v-if="illness.current_treatment" class="bg-blue-50 p-3 rounded-lg mb-2">
                                    <div class="text-sm font-medium text-blue-800">Traitement actuel:</div>
                                    <div class="text-sm text-blue-700" v-text="illness.current_treatment"></div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span>Dernière consultation: </span><span v-text="illness.last_checkup"></span>
                                    <span v-if="illness.next_appointment"> • Prochain RDV: </span><span v-if="illness.next_appointment" v-text="illness.next_appointment"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Devices -->
            <div v-show="activeTab === 'devices'" class="space-y-6">
                <div class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-mobile-alt text-blue-600 mr-3"></i>
                        Appareils Connectés
                    </h3>
                    
                    <!-- Résumé des devices -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600" v-text="deviceData.connectedDevices"></div>
                            <div class="text-sm text-gray-600">Appareils connectés</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600" v-text="deviceData.dailyDataPoints"></div>
                            <div class="text-sm text-gray-600">Points de données/jour</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600" v-text="deviceData.lastSync"></div>
                            <div class="text-sm text-gray-600">Dernière sync</div>
                        </div>
                    </div>

                    <!-- Liste des devices -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-gray-800">📱 Appareils Disponibles</h4>
                        <div v-for="device in deviceData.devices" :key="device.id" 
                             class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md transition-all">
                            <div class="flex items-center space-x-4">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white', device.connected ? 'bg-green-500' : 'bg-gray-400']">
                                    <i :class="device.icon"></i>
                                </div>
                                <div>
                                    <div class="font-medium" v-text="device.name"></div>
                                    <div class="text-sm text-gray-600" v-text="device.type + ' • ' + device.brand"></div>
                                    <div class="text-xs text-gray-500" v-text="'Dernières données: ' + device.lastData"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div :class="['px-3 py-1 rounded-full text-xs font-bold', device.connected ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']" 
                                     v-text="device.connected ? 'CONNECTÉ' : 'DÉCONNECTÉ'"></div>
                                <div v-if="device.batteryLevel" class="text-xs text-gray-500 mt-1" v-text="'Batterie: ' + device.batteryLevel + '%'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Données récentes -->
                    <div class="mt-6">
                        <h4 class="font-bold text-gray-800 mb-3">📊 Données Récentes (24h)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-red-50 rounded-lg">
                                <div class="text-lg font-bold text-red-600" v-text="deviceData.recentData.heartRate"></div>
                                <div class="text-xs text-gray-600">FC Moyenne (bpm)</div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-lg font-bold text-blue-600" v-text="deviceData.recentData.steps"></div>
                                <div class="text-xs text-gray-600">Pas</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600" v-text="deviceData.recentData.sleep"></div>
                                <div class="text-xs text-gray-600">Sommeil (h)</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-lg font-bold text-purple-600" v-text="deviceData.recentData.calories"></div>
                                <div class="text-xs text-gray-600">Calories</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Dopage (complet) -->
            <div v-show="activeTab === 'doping'" class="space-y-6">
                <!-- Statut des contrôles -->
                <div class="performance-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-vial text-purple-600 mr-3"></i>
                        Statut des Contrôles Antidopage
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center p-6 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600" v-text="dopingData.lastControl"></div>
                            <div class="text-sm text-gray-600">Dernier contrôle</div>
                            <div class="text-xs text-green-600 mt-2">✅ NÉGATIF</div>
                        </div>
                        <div class="text-center p-6 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600" v-text="dopingData.nextControl"></div>
                            <div class="text-sm text-gray-600">Prochain contrôle</div>
                            <div class="text-xs text-blue-600 mt-2">📅 PROGRAMMÉ</div>
                        </div>
                    </div>
                </div>

                <!-- Historique des contrôles -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">📋 Historique des Contrôles</h4>
                    <div class="space-y-3">
                        <div v-for="control in dopingData.controlHistory" :key="control.id" 
                             class="p-4 border rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium" v-text="control.type"></div>
                                    <div class="text-sm text-gray-600" v-text="control.date + ' • ' + control.location"></div>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs font-bold', 
                                              control.result === 'négatif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']" 
                                      v-text="control.result.toUpperCase()"></span>
                            </div>
                            <div class="text-sm text-gray-700 mb-2" v-text="control.substances"></div>
                            <div class="text-xs text-gray-500" v-text="control.notes"></div>
                        </div>
                    </div>
                </div>

                <!-- Déclarations ATU -->
                <div class="performance-card p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">💊 Déclarations ATU Actives</h4>
                    <div class="space-y-3">
                        <div v-for="atu in dopingData.atuDeclarations" :key="atu.id" 
                             class="p-4 border rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-medium" v-text="atu.substance"></div>
                                    <div class="text-sm text-gray-600" v-text="atu.reason + ' • ' + atu.doctor"></div>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs font-bold', 
                                              atu.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']" 
                                      v-text="atu.status.toUpperCase()"></span>
                            </div>
                            <div class="text-sm text-gray-700 mb-2">
                                <span class="font-medium">Dosage:</span> <span v-text="atu.dosage"></span><br>
                                <span class="font-medium">Fréquence:</span> <span v-text="atu.frequency"></span><br>
                                <span class="font-medium">Période:</span> <span v-text="atu.startDate + ' - ' + atu.endDate"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertes et substances interdites -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Alertes de risque -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">⚠️ Alertes de Risque</h4>
                        <div class="space-y-3">
                            <div v-for="alert in dopingData.riskAlerts" :key="alert.id" 
                                 class="p-3 bg-orange-50 border-l-4 border-orange-500 rounded">
                                <div class="font-medium text-orange-800" v-text="alert.title"></div>
                                <div class="text-sm text-orange-700" v-text="alert.message"></div>
                                <div class="text-xs text-orange-600 mt-1" v-text="alert.date"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Substances interdites -->
                    <div class="performance-card p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">🚫 Substances Interdites</h4>
                        <div class="space-y-2">
                            <div v-for="substance in dopingData.prohibitedSubstances" :key="substance" 
                                 class="p-2 bg-red-50 border border-red-200 rounded text-sm text-red-800">
                                <span v-text="substance"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de partage médical (simplifié) -->
        <div id="medicalShareModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold mb-4">Partager le dossier médical</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email du destinataire</label>
                        <input type="email" id="shareEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="medecin@exemple.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                        <textarea id="shareMessage" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Message personnalisé..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeShareModalSimple()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Annuler</button>
                    <button onclick="shareSimple()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Partager</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('🚀 FIFA Original - Script chargé');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📱 DOM chargé, initialisation Vue.js...');
            
            try {
                if (typeof Vue === 'undefined') {
                    throw new Error('❌ Vue.js n\'est pas chargé');
                }
                
                console.log('✅ Vue.js détecté, version:', Vue.version);
                const createApp = Vue.createApp;
                
                console.log('🔧 Création de l\'application Vue...');
                const app = createApp({
            data() {
                return {
                    activeTab: 'performance',
                    activeNotificationFilter: 'all',
                    navigationTabs: [
                        { id: 'performance', name: 'Performance', icon: 'fas fa-chart-line', count: null },
                        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell', count: 12 },
                        { id: 'health', name: 'Santé & Bien-être', icon: 'fas fa-heart', count: null },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md', count: 4 },
                        { id: 'devices', name: 'Devices', icon: 'fas fa-mobile-alt', count: 3 },
                        { id: 'doping', name: 'Dopage', icon: 'fas fa-vial', count: null }
                    ],
                    notificationFilters: [
                        { id: 'all', name: 'Toutes', icon: 'fas fa-bell', count: 12 },
                        { id: 'national', name: 'Équipe Nationale', icon: 'fas fa-flag', count: 2 },
                        { id: 'training', name: 'Entraînements', icon: 'fas fa-dumbbell', count: 3 },
                        { id: 'matches', name: 'Matchs', icon: 'fas fa-futbol', count: 2 },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md', count: 2 },
                        { id: 'social', name: 'Réseaux Sociaux', icon: 'fas fa-share-alt', count: 3 }
                    ],
                    offensiveStats: [
                        { name: 'Buts marqués', display: '12', percentage: 85, teamAvg: '8.5', leagueAvg: '7.2' },
                        { name: 'Tirs cadrés', display: '{{ $player->performances->sum("shots_on_target") ?? 0 }}', percentage: 78, teamAvg: '35.2', leagueAvg: '32.1' },
                        { name: 'Tirs totaux', display: '{{ $player->performances->sum("shots") ?? 0 }}', percentage: {{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}, teamAvg: '58.4', leagueAvg: '55.7' },
                        { name: 'Précision tirs', display: '78%', percentage: 78, teamAvg: '65%', leagueAvg: '62%' },
                        { name: 'Passes décisives', display: '8', percentage: 82, teamAvg: '6.8', leagueAvg: '5.9' },
                        { name: 'Passes clés', display: '{{ $player->performances->sum("goals") ?? 0 }}', percentage: 78, teamAvg: '22.1', leagueAvg: '19.8' },
                        { name: 'Centres réussis', display: '18', percentage: 75, teamAvg: '15.3', leagueAvg: '13.7' },
                        { name: 'Dribbles réussis', display: '{{ $player->performances->sum("direction_changes") ?? 0 }}', percentage: 82, teamAvg: '{{ $player->performances->count() }}.2', leagueAvg: '38.9' }
                    ],
                    physicalStats: [
                        { name: 'Distance parcourue', display: '2{{ $player->performances->count() }} km', percentage: 78, teamAvg: '198 km', leagueAvg: '185 km' },
                        { name: 'Vitesse maximale', display: '{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}.2 km/h', percentage: 85, teamAvg: '32.1 km/h', leagueAvg: '30.8 km/h' },
                        { name: 'Vitesse moyenne', display: '12.3 km/h', percentage: 82, teamAvg: '11.2 km/h', leagueAvg: '10.9 km/h' },
                        { name: 'Sprints', display: '156', percentage: 82, teamAvg: '1{{ $player->performances->sum("goals") ?? 0 }}', leagueAvg: '115' },
                        { name: 'Accélérations', display: '{{ $player->performances->sum("accelerations") ?? 0 }}', percentage: 87, teamAvg: '198', leagueAvg: '185' },
                        { name: 'Décélérations', display: '198', percentage: 85, teamAvg: '1{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}', leagueAvg: '165' },
                        { name: 'Changements direction', display: '{{ $player->performances->sum("direction_changes") ?? 0 }}', percentage: 78, teamAvg: '{{ $player->performances->sum("direction_changes") ?? 0 }}', leagueAvg: '{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}' },
                        { name: 'Sautes', display: '12', percentage: 70, teamAvg: '15.2', leagueAvg: '14.8' }
                    ],
                    technicalStats: [
                        { name: 'Précision passes', display: '88%', percentage: 82, teamAvg: '85%', leagueAvg: '82%' },
                        { name: 'Passes longues', display: '{{ $player->performances->count() }}', percentage: 78, teamAvg: '38', leagueAvg: '35' },
                        { name: 'Centres réussis', display: '18', percentage: 75, teamAvg: '15.3', leagueAvg: '13.7' },
                        { name: 'Tacles réussis', display: '23', percentage: 82, teamAvg: '{{ $player->performances->sum("goals") ?? 0 }}.5', leagueAvg: '26.8' },
                        { name: 'Interceptions', display: '31', percentage: 85, teamAvg: '26.8', leagueAvg: '24.5' },
                        { name: 'Dégagements', display: '12', percentage: 78, teamAvg: '15.2', leagueAvg: '14.1' },
                        { name: 'Fautes commises', display: '8', percentage: 82, teamAvg: '12.4', leagueAvg: '11.8' },
                        { name: 'Cartons jaunes', display: '2', percentage: 85, teamAvg: '4.8', leagueAvg: '4.2' }
                    ],
                    seasonSummary: {
                        'goals': {'total': 12, 'trend': '+3', 'avg': 0.43},
                        'assists': {'total': 8, 'trend': '+2', 'avg': 0.29},
                        'matches': {'total': {{ $player->performances->sum("goals") ?? 0 }}, 'rating': 8.7, 'distance': '2{{ $player->performances->count() }} km'}
                    },
                    performanceEvolution: {
                        'labels': ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                        'ratings': [8.2, 8.4, 8.6, 8.7, 8.8, 8.7],
                        'goals': [2, 3, 2, 1, 2, 2],
                        'assists': [1, 2, 1, 1, 2, 1]
                    },
                    notificationData: {
                        'nationalTeam': [
                            { id: 1, title: 'Convocación Selección {{ $player->nationality }}', message: 'Convocado para partidos vs Brasil y Uruguay', date: '15/03/2025', priority: 'high', type: 'national' },
                            { id: 2, title: 'Entrenamiento Selección', message: 'Sesión técnica mañana 9:00 AM', date: '14/03/2025', priority: 'medium', type: 'national' }
                        ],
                        'trainingSessions': [
                            { id: 3, title: 'Entrenamiento Técnico', message: 'Sesión de pases y finalización', date: '13/03/2025', priority: 'medium', type: 'training' },
                            { id: 4, title: 'Entrenamiento Físico', message: 'Circuitos de velocidad y resistencia', date: '12/03/2025', priority: 'medium', type: 'training' },
                            { id: 5, title: 'Recuperación Activa', message: 'Sesión de estiramientos y yoga', date: '11/03/2025', priority: 'low', type: 'training' }
                        ],
                        'matches': [
                            { id: 6, title: 'Próximo Partido', message: '{{ $player->club->name ?? "Club" }} vs {{ $player->club->association->name ?? "Adversaire" }} - Domingo 16/03', date: '10/03/2025', priority: 'high', type: 'matches', status: 'convoqué', icon: 'fas fa-futbol', competition: '{{ $player->club->association->name ?? "Compétition" }}', stadium: '{{ $player->club->stadium_name ?? "Stade" }}', time: '15:00', meetingTime: '14:00' },
                            { id: 7, title: 'Análisis Rival', message: 'Revisión táctica Manchester City', date: '09/03/2025', priority: 'medium', type: 'matches', status: 'convoqué', icon: 'fas fa-chart-line', competition: 'Analyse Tactique', stadium: 'Centre d\'entraînement', time: '10:00', meetingTime: '09:30' }
                        ],
                        'medicalAppointments': [
                            { id: 8, title: 'Control Médico', message: 'Revisión rutinaria con Dr. Martínez', date: '08/03/2025', priority: 'medium', type: 'medical', urgent: false, icon: 'fas fa-user-md', type: 'Consultation', purpose: 'Revisión rutinaria', time: '10:00', location: 'Centre médical', doctor: 'Dr. Martínez' },
                            { id: 9, title: 'Fisioterapia', message: 'Sesión de recuperación muscular', date: '07/03/2025', priority: 'medium', type: 'medical', urgent: false, icon: 'fas fa-stretching', type: 'Rééducation', purpose: 'Récupération musculaire', time: '14:00', location: 'Centre de rééducation', doctor: 'Sophie Moreau' }
                        ],
                        'socialMedia': [
                            { id: 10, title: 'Mensaje de Fan', message: 'Mensaje de apoyo de @fan_chelsea', date: '06/03/2025', priority: 'low', type: 'social' },
                            { id: 11, title: 'Tendencia Twitter', message: '#{{ $player->first_name }}Chelsea trending en {{ $player->nationality }}', date: '05/03/2025', priority: 'low', type: 'social' },
                            { id: 12, title: 'Instagram Live', message: 'Solicitud de entrevista en vivo', date: '04/03/2025', priority: 'low', type: 'social' }
                        ],
                        'socialAlerts': [
                            { id: 1, title: 'Mensaje de Fan', content: 'Mensaje de apoyo de @fan_chelsea', timestamp: '06/03/2025', views: '2.5K', engagement: '156', sentiment: 'positive', platform: 'instagram', needsResponse: false },
                            { id: 2, title: 'Tendencia Twitter', content: '#{{ $player->first_name }}Chelsea trending en {{ $player->nationality }}', timestamp: '05/03/2025', views: '15.2K', engagement: '888', sentiment: 'positive', platform: 'google', needsResponse: true },
                            { id: 3, title: 'Instagram Live', content: 'Solicitud de entrevista en vivo', timestamp: '04/03/2025', views: '8.7K', engagement: '{{ $player->performances->sum("shots_on_target") ?? 0 }}3', sentiment: 'neutral', platform: 'instagram', needsResponse: true }
                        ]
                    },
                    healthData: {
                        'globalScore': 87,
                        'physicalScore': 82,
                        'mentalScore': 85,
                        'sleepScore': 86,
                        'socialScore': 78,
                        'injuryRisk': 15,
                        vitals: [
                            { name: 'Fréquence Cardiaque', value: '{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}', unit: 'bpm', icon: 'fas fa-heartbeat', color: '#ef4444', status: 'normal' },
                            { name: 'Tension Artérielle', value: '125/80', unit: 'mmHg', icon: 'fas fa-thermometer-half', color: '#3b82f6', status: 'normal' },
                            { name: 'Température', value: '{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}.8°', unit: 'C', icon: 'fas fa-temperature-low', color: '#f59e0b', status: 'normal' },
                            { name: 'Saturation O2', value: '98%', unit: 'SpO2', icon: 'fas fa-lungs', color: '#10b981', status: 'normal' },
                            { name: 'Hydratation', value: '82%', unit: 'niveau', icon: 'fas fa-tint', color: '#06b6d4', status: 'normal' },
                            { name: 'Stress Cortisol', value: '12.5', unit: 'µg/dL', icon: 'fas fa-brain', color: '#8b5cf6', status: 'normal' }
                        ],
                        recentMetrics: [
                            { id: 1, type: 'Poids Corporel', value: '{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}.1 kg', date: '10/03/2025', icon: 'fas fa-weight', color: '#3b82f6', trend: 'stable', change: '+0.1kg' },
                            { id: 2, type: 'Masse Grasse', value: '8.2%', date: '10/03/2025', icon: 'fas fa-chart-pie', color: '#10b981', trend: 'down', change: '-0.3%' },
                            { id: 3, type: 'Masse Musculaire', value: '65.8 kg', date: '10/03/2025', icon: 'fas fa-dumbbell', color: '#f59e0b', trend: 'up', change: '+0.5kg' },
                            { id: 4, type: 'VO2 Max', value: '68.5 ml/kg/min', date: '08/03/2025', icon: 'fas fa-lungs', color: '#ef4444', trend: 'up', change: '+1.2' },
                            { id: 5, type: 'VFC (HRV)', value: '52ms', date: '10/03/2025', icon: 'fas fa-heartbeat', color: '#8b5cf6', trend: 'up', change: '+3ms' }
                        ],
                        recommendations: [
                            { 
                                id: 1, 
                                title: 'Optimiser la Récupération', 
                                description: 'Augmenter la durée de sommeil profond avec une routine de 30min avant coucher', 
                                priority: 'HAUTE', 
                                color: '#ef4444', 
                                icon: 'fas fa-bed',
                                impact: 'Performance +8%'
                            },
                            { 
                                id: 2, 
                                title: 'Nutrition Périodisée', 
                                description: 'Adapter l\'apport calorique selon l\'intensité d\'entraînement (±200 cal)', 
                                priority: 'MOYENNE', 
                                color: '#f59e0b', 
                                icon: 'fas fa-utensils',
                                impact: 'Énergie +12%'
                            },
                            { 
                                id: 3, 
                                title: 'Hydratation Intelligente', 
                                description: 'Surveillance électrolytes pendant entraînements >85min', 
                                priority: 'MOYENNE', 
                                color: '#06b6d4', 
                                icon: 'fas fa-tint',
                                impact: 'Endurance +5%'
                            },
                            { 
                                id: 4, 
                                title: 'Méditation Ciblée', 
                                description: 'Sessions pré-match de 10min pour optimiser concentration', 
                                priority: 'FAIBLE', 
                                color: '#8b5cf6', 
                                icon: 'fas fa-brain',
                                impact: 'Focus +15%'
                            },
                            { 
                                id: 5, 
                                title: 'Mobilité Préventive', 
                                description: 'Routine quotidienne 15min pour prévenir blessures musculaires', 
                                priority: 'HAUTE', 
                                color: '#10b981', 
                                icon: 'fas fa-stretching',
                                impact: 'Prévention -25%'
                            },
                            { 
                                id: 6, 
                                title: 'Analyse Postural', 
                                description: 'Évaluation biomécanique mensuelle avec corrections ciblées', 
                                priority: 'MOYENNE', 
                                color: '#3b82f6', 
                                icon: 'fas fa-user-md',
                                impact: 'Technique +10%'
                            }
                        ]
                    },
                    deviceData: {
                        connectedDevices: 3,
                        dailyDataPoints: 1440,
                        lastSync: '2 min',
                        devices: [
                            { id: 1, name: 'Polar H10', type: 'Capteur cardiaque', brand: 'Polar', icon: 'fas fa-heartbeat', connected: true, batteryLevel: 85, lastData: 'Il y a 2 min' },
                            { id: 2, name: 'WHOOP 4.0', type: 'Tracker fitness', brand: 'WHOOP', icon: 'fas fa-running', connected: true, batteryLevel: 62, lastData: 'Il y a 5 min' },
                            { id: 3, name: 'GPS Catapult', type: 'Tracker GPS', brand: 'Catapult', icon: 'fas fa-satellite', connected: true, batteryLevel: 88, lastData: 'Il y a 1 min' }
                        ],
                        recentData: {
                            heartRate: 68,
                            steps: 15{{ $player->performances->sum("shots_on_target") ?? 0 }}0,
                            sleep: '7h32',
                            calories: {{ $player->performances->sum("goals") ?? 0 }}47
                        }
                    },
                    dopingData: {
                        lastControl: '{{ $player->performances->sum("goals") ?? 0 }}/02/2025',
                        nextControl: '15/04/2025',
                        controlHistory: [
                            { id: 1, type: 'Contrôle inopiné', date: '{{ $player->performances->sum("goals") ?? 0 }}/02/2025', location: 'Centre d\'entraînement', status: 'completed', result: 'négatif', substances: 'Aucune substance interdite détectée', notes: 'Contrôle standard, aucun problème détecté' },
                            { id: 2, type: 'Contrôle post-match', date: '15/02/2025', location: 'Stade de {{ $player->club->country ?? "Pays" }}', status: 'completed', result: 'négatif', substances: 'Aucune substance interdite détectée', notes: 'Contrôle après match {{ $player->club->country ?? "Pays" }} vs Italie' }
                        ],
                        atuDeclarations: [
                            { id: 1, substance: 'Ventoline (Salbutamol)', reason: 'Asthme d\'effort', dosage: '100mcg par inhalation', frequency: 'Avant entraînement si nécessaire', doctor: 'Dr. Jean Martin', startDate: '01/09/2024', endDate: '31/08/2025', status: 'active', riskLevel: 'low' }
                        ]
                    },
                    sdohData: {
                        environment: 85,
                        socialSupport: 85,
                        healthcareAccess: 75,
                        financialStatus: 80,
                        mentalWellbeing: 78,
                        education: 88,
                        employment: 87,
                        community: 85
                    },
                    medicalData: {
                        healthRecords: [
                            {
                                id: 1,
                                title: 'Consultation Cardiologique',
                                doctor: 'Dr. Jean Martin',
                                date: '05/03/2025',
                                status: 'completed'
                            },
                            {
                                id: 2,
                                title: 'Examen Orthopédique',
                                doctor: 'Dr. Sophie Moreau',
                                date: '{{ $player->performances->sum("goals") ?? 0 }}/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 3,
                                title: 'Consultation Kinésithérapie',
                                doctor: 'Sophie Moreau',
                                date: '05/02/2025',
                                status: 'pending'
                            },
                            {
                                id: 4,
                                title: 'Bilan Biologique Complet',
                                doctor: 'Dr. Pierre Dubois',
                                date: '20/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 5,
                                title: 'Consultation Nutrition',
                                doctor: 'Dr. Marie Laurent',
                                date: '15/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 6,
                                title: 'Examen Dentaire',
                                doctor: 'Dr. Paul Bernard',
                                date: '10/02/2025',
                                status: 'completed'
                            }
                        ],
                        pcmas: [
                            { id: 1, title: 'Évaluation PCMA Saison 2024-2025', date: '01/09/2024', fitness: 'fit' },
                            { id: 2, title: 'Contrôle PCMA Mi-saison', date: '15/01/2025', fitness: 'fit' },
                            { id: 3, title: 'Évaluation PCMA Post-blessure', date: '01/03/2025', fitness: 'limited' }
                        ],
                        predictions: [
                            { id: 1, title: 'Risque de Blessure', confidence: 87, risk: 'low' },
                            { id: 2, title: 'Performance Prédictive', confidence: 88, risk: 'low' }
                        ],
                        injuries: [
                            {
                                id: 1,
                                type: 'Élongation ischio-jambiers',
                                location: 'Jambe droite',
                                severity: 'moderate',
                                date: '15/02/2025',
                                recovery: 75,
                                status: 'recovering',
                                description: 'Élongation grade 2 des ischio-jambiers lors d\'un sprint',
                                treatment: 'Kinésithérapie, étirements, renforcement progressif',
                                doctor: 'Dr. Sophie Moreau',
                                estimatedReturn: '25/03/2025'
                            },
                            {
                                id: 2,
                                type: 'Entorse cheville',
                                location: 'Cheville gauche',
                                severity: 'mild',
                                date: '10/01/2025',
                                recovery: 100,
                                status: 'recovered',
                                description: 'Entorse légère lors d\'un changement de direction',
                                treatment: 'Repos, glace, compression, élévation',
                                doctor: 'Dr. Jean Martin',
                                estimatedReturn: '25/01/2025'
                            },
                            {
                                id: 3,
                                type: 'Contusion cuisse',
                                location: 'Cuisse droite',
                                severity: 'mild',
                                date: '{{ $player->performances->sum("goals") ?? 0 }}/12/2024',
                                recovery: 100,
                                status: 'recovered',
                                description: 'Contusion suite à un choc avec un adversaire',
                                treatment: 'Glace, anti-inflammatoires, étirements doux',
                                doctor: 'Dr. Pierre Dubois',
                                estimatedReturn: '05/01/2025'
                            }
                        ],
                        illnesses: [
                            {
                                id: 1,
                                type: 'Infection respiratoire',
                                diagnosis: 'Bronchite aiguë',
                                date: '20/01/2025',
                                recovery: 100,
                                status: 'recovered',
                                description: 'Infection des voies respiratoires avec toux et fièvre',
                                treatment: 'Antibiotiques, repos, hydratation',
                                doctor: 'Dr. Marie Laurent',
                                estimatedReturn: '30/01/2025'
                            },
                            {
                                id: 2,
                                type: 'Gastro-entérite',
                                diagnosis: 'Intoxication alimentaire',
                                date: '05/12/2024',
                                recovery: 100,
                                status: 'recovered',
                                description: 'Troubles digestifs suite à un repas',
                                treatment: 'Réhydratation, régime alimentaire, repos',
                                doctor: 'Dr. Sophie Moreau',
                                estimatedReturn: '10/12/2024'
                            }
                        ]
                    },
                    deviceData: {
                        connectedDevices: 3,
                        dailyDataPoints: 1440,
                        lastSync: '2 min',
                        devices: [
                            {
                                id: 1,
                                name: 'Polar H10',
                                type: 'Capteur cardiaque',
                                brand: 'Polar',
                                icon: 'fas fa-heartbeat',
                                connected: true,
                                batteryLevel: 85,
                                lastData: 'Il y a 2 min'
                            },
                            {
                                id: 2,
                                name: 'WHOOP 4.0',
                                type: 'Tracker fitness',
                                brand: 'WHOOP',
                                icon: 'fas fa-running',
                                connected: true,
                                batteryLevel: 62,
                                lastData: 'Il y a 5 min'
                            },
                            {
                                id: 3,
                                name: 'GPS Catapult',
                                type: 'Tracker GPS',
                                brand: 'Catapult',
                                icon: 'fas fa-satellite',
                                connected: true,
                                batteryLevel: 88,
                                lastData: 'Il y a 1 min'
                            },
                            {
                                id: 4,
                                name: 'Balance InBody',
                                type: 'Composition corporelle',
                                brand: 'InBody',
                                icon: 'fas fa-weight',
                                connected: false,
                                batteryLevel: null,
                                lastData: 'Il y a 2h'
                            },
                            {
                                id: 5,
                                name: 'Oura Ring',
                                type: 'Tracker sommeil',
                                brand: 'Oura',
                                icon: 'fas fa-bed',
                                connected: true,
                                batteryLevel: {{ $player->performances->count() }},
                                lastData: 'Il y a 10 min'
                            }
                        ],
                        recentData: {
                            heartRate: 68,
                            steps: 15{{ $player->performances->sum("shots_on_target") ?? 0 }}0,
                            sleep: '7h32',
                            calories: {{ $player->performances->sum("goals") ?? 0 }}47
                        }
                    },
                    dopingData: {
                        lastControl: '{{ $player->performances->sum("goals") ?? 0 }}/02/2025',
                        nextControl: '15/04/2025',
                        controlHistory: [
                            {
                                id: 1,
                                type: 'Contrôle inopiné',
                                date: '{{ $player->performances->sum("goals") ?? 0 }}/02/2025',
                                location: 'Centre d\'entraînement',
                                status: 'completed',
                                result: 'négatif',
                                substances: 'Aucune substance interdite détectée',
                                notes: 'Contrôle standard, aucun problème détecté'
                            },
                            {
                                id: 2,
                                type: 'Contrôle post-match',
                                date: '15/02/2025',
                                location: 'Stade de {{ $player->club->country ?? "Pays" }}',
                                status: 'completed',
                                result: 'négatif',
                                substances: 'Aucune substance interdite détectée',
                                notes: 'Contrôle après match {{ $player->club->country ?? "Pays" }} vs Italie'
                            },
                            {
                                id: 3,
                                type: 'Contrôle ciblé',
                                date: '30/01/2025',
                                location: 'Domicile',
                                status: 'completed',
                                result: 'négatif',
                                substances: 'Aucune substance interdite détectée',
                                notes: 'Contrôle surprise à 6h du matin'
                            }
                        ],
                        atuDeclarations: [
                            {
                                id: 1,
                                substance: 'Ventoline (Salbutamol)',
                                reason: 'Asthme d\'effort',
                                dosage: '100mcg par inhalation',
                                frequency: 'Avant entraînement si nécessaire',
                                doctor: 'Dr. Jean Martin',
                                startDate: '01/09/2024',
                                endDate: '31/08/2025',
                                status: 'active',
                                riskLevel: 'low'
                            },
                            {
                                id: 2,
                                substance: 'Vitamine D3',
                                reason: 'Carence saisonnière',
                                dosage: '2000 UI par jour',
                                frequency: '1 fois par jour',
                                doctor: 'Dr. Sophie Moreau',
                                startDate: '01/12/2024',
                                endDate: '31/03/2025',
                                status: 'active',
                                riskLevel: 'none'
                            }
                        ],
                        riskAlerts: [
                            {
                                id: 1,
                                type: 'warning',
                                title: 'Attention aux compléments',
                                message: 'Vérifiez toujours la composition des compléments alimentaires',
                                priority: 'medium',
                                date: '08/03/2025'
                            }
                        ],
                        prohibitedSubstances: [
                            'Stéroïdes anabolisants',
                            'Hormones de croissance',
                            'EPO',
                            'Stimulants (amphétamines, cocaïne)',
                            'Diurétiques',
                            'Bêta-bloquants',
                            'Cannabinoïdes'
                        ]
                    }
                }
            },
            mounted() {
                console.log('FIFA Ultimate Dashboard optimized loaded successfully');
                this.initializeCharts();
            },
            methods: {
                initializeCharts() {
                    this.$nextTick(() => {
                        this.createPerformanceChart();
                        this.createComparisonChart();
                        this.createHealthTrendsChart();
                        this.createSDOHRadarChart();
                    });
                },
                createPerformanceChart() {
                    const ctx = document.getElementById('performanceChart');
                    if (!ctx) return;

                    // Utiliser les données dynamiques de l'évolution des performances
                    const labels = this.performanceEvolution.labels || ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
                    const ratings = this.performanceEvolution.ratings || [75, 78, {{ $player->performances->sum("direction_changes") ?? 0 }}, 82, 85, 78];
                    const goals = this.performanceEvolution.goals || [0, 0, 0, 0, 0, 0];
                    const assists = this.performanceEvolution.assists || [0, 0, 0, 0, 0, 0];

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Rating Performance',
                                data: ratings,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y'
                            }, {
                                label: 'Buts marqués',
                                data: goals,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: false,
                                yAxisID: 'y1'
                            }, {
                                label: 'Assists délivrés',
                                data: assists,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(2{{ $player->performances->count() }}, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: false,
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Évolution des Performances - Saison 2024/25',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    beginAtZero: true,
                                    max: 10,
                                    title: {
                                        display: true,
                                        text: 'Rating (0-10)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    beginAtZero: true,
                                    max: 5,
                                    title: {
                                        display: true,
                                        text: 'Buts/Assists'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                },
                createComparisonChart() {
                    const ctx = document.getElementById('comparisonChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: ['Vitesse', 'Endurance', 'Technique', 'Mental', 'Physique', 'Tactique'],
                            datasets: [{
                                label: 'Mes Stats',
                                data: [78, 85, 88, 78, 86, 82],
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointHoverBackgroundColor: '#ffffff',
                                pointHoverBorderColor: '#10b981'
                            }, {
                                label: 'Moyenne Équipe',
                                data: [{{ $player->performances->sum("direction_changes") ?? 0 }}, 78, 81, {{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}, 79, 77],
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                r: {
                                    angleLines: {
                                        display: true
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100
                                }
                            }
                        }
                    });
                },
                createHealthTrendsChart() {
                    const ctx = document.getElementById('healthTrendsChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6', 'Sem 7', 'Sem 8'],
                            datasets: [{
                                label: 'Score FIT Global',
                                data: [78, 80, 82, 81, 84, 85, 86, 85],
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'Qualité Sommeil',
                                data: [70, {{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}, 75, 78, 80, 82, 84, 82],
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 88, 246, 0.1)',
                                tension: 0.4,
                                fill: false
                            }, {
                                label: 'Niveau Stress',
                                data: [25, 23, 20, 18, 17, 15, 14, 15],
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: false
                            }, {
                                label: 'Condition Physique',
                                data: [82, 83, 84, 85, 86, 87, 78, 78],
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(2{{ $player->performances->count() }}, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Évolution sur 8 semaines'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Score (%)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Semaines'
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                            }
                        }
                    });
                },
                createSDOHRadarChart() {
                    const ctx = document.getElementById('sdohRadarChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: [
                                'Environnement de vie',
                                'Soutien social',
                                'Accès aux soins',
                                'Situation financière',
                                'Bien-être mental'
                            ],
                            datasets: [
                                {
                                    label: 'Score SDOH du joueur',
                                    data: [85, 85, 75, 80, 78],
                                    backgroundColor: 'rgba(20, 184, 166, 0.2)',
                                    borderColor: 'rgba(20, 184, 166, 1)',
                                    borderWidth: 2,
                                    pointBackgroundColor: 'rgba(20, 184, 166, 1)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: 'rgba(20, 184, 166, 1)',
                                    pointHoverBorderWidth: 3,
                                    pointRadius: 7,
                                    pointHoverRadius: 10
                                },
                                {
                                    label: 'Moyenne FIFA Athletes',
                                    data: [78, 82, 70, 75, 80],
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    borderColor: 'rgba(99, 102, 241, 0.8)',
                                    borderWidth: 1,
                                    pointBackgroundColor: 'rgba(99, 102, 241, 0.8)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 1,
                                    pointRadius: 5,
                                    borderDash: [5, 5]
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            aspectRatio: 1.2,
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Facteurs sociaux déterminants de la santé (SDOH)',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    color: '#374151'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.r + '/100';
                                        }
                                    }
                                }
                            },
                            scales: {
                                r: {
                                    angleLines: { 
                                        display: true,
                                        color: 'rgba(156, 163, 175, 0.3)'
                                    },
                                    grid: {
                                        color: 'rgba(156, 163, 175, 0.2)'
                                    },
                                    pointLabels: {
                                        font: {
                                            size: 14,
                                            weight: '600'
                                        },
                                        color: '#374151',
                                        padding: 15
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100,
                                    ticks: { 
                                        stepSize: 20,
                                        display: true,
                                        backdropColor: 'rgba(255, 255, 255, 0.8)',
                                        color: '#6B{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}80',
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                }
            }
                });
                
                console.log('🔧 Application Vue créée, montage en cours...');
                app.mount('#fifa-app');
                console.log('✅ Application Vue montée avec succès !');
                
            } catch (error) {
                console.error('❌ Erreur lors de l\'initialisation de Vue.js:', error);
                document.body.innerHTML += `
                    <div style="position: fixed; top: 20px; right: 20px; background: red; color: white; padding: 20px; border-radius: 8px; z-index: 9999;">
                        <h3>❌ Erreur Vue.js</h3>
                        <p>${error.message}</p>
                        <p>Vérifiez la console pour plus de détails</p>
                    </div>
                `;
            }
        });

        // Fonctions pour le dossier médical
        function openShareModalSimple() {
            document.getElementById('medicalShareModal').classList.remove('hidden');
        }

        function closeShareModalSimple() {
            document.getElementById('medicalShareModal').classList.add('hidden');
        }

        function shareSimple() {
            const email = document.getElementById('shareEmail').value;
            const message = document.getElementById('shareMessage').value;
            
            if (!email) {
                alert('Veuillez saisir une adresse email');
                return;
            }
            
            alert(`Dossier médical envoyé à ${email}`);
            closeShareModalSimple();
            
            document.getElementById('shareEmail').value = '';
            document.getElementById('shareMessage').value = '';
        }

        function printMedicalRecord() {
            alert('Fonction d\'impression activée');
        }

        function exportMedicalPDF() {
            alert('Export PDF en cours de développement');
        }
    </script>
</body>
</html>
