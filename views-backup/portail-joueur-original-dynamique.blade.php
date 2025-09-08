<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
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
        }

        .fifa-nav-tab.active {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-2px);
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
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'fifa-nav-tab px-6 py-3 rounded-xl font-medium transition-all duration-300 relative',
                        activeTab === tab.id ? 'active' : ''
                    ]"
                >
                    <span class="tab-icon mr-2">{{ tab.icon }}</span>
                    {{ tab.name }}
                    <span v-if="tab.notifications" class="notification-badge">{{ tab.notifications }}</span>
                </button>
            </div>
        </div>

        <!-- Contenu des Tabs -->
        <div class="px-6 pb-8">
            <!-- Tab Performance -->
            <div v-show="activeTab === 'performance'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">📊 Performance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white/10 rounded-lg p-6">
                        <h5 class="font-semibold text-white mb-2">Buts marqués</h5>
                        <div class="text-3xl font-bold text-green-600">{{ $portalData['performanceStats']['current_month_goals'] ?? 0 }}</div>
                        <div class="text-sm text-white">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/10 rounded-lg p-6">
                        <h5 class="font-semibold text-white mb-2">Passes décisives</h5>
                        <div class="text-3xl font-bold text-blue-600">{{ $portalData['performanceStats']['current_month_assists'] ?? 0 }}</div>
                        <div class="text-sm text-white">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/10 rounded-lg p-6">
                        <h5 class="font-semibold text-white mb-2">Distance parcourue</h5>
                        <div class="text-3xl font-bold text-purple-600">{{ $portalData['performanceStats']['current_month_distance'] ?? 0 }} km</div>
                        <div class="text-sm text-white">Ce mois</div>
                    </div>
                    
                    <div class="bg-white/10 rounded-lg p-6">
                        <h5 class="font-semibold text-white mb-2">Rating moyen</h5>
                        <div class="text-3xl font-bold text-orange-600">{{ $portalData['performanceStats']['average_rating'] ?? 0 }}</div>
                        <div class="text-sm text-white">Basé sur {{ $portalData['performanceStats']['matches_played'] ?? 0 }} matchs</div>
                    </div>
                </div>
            </div>

            <!-- Tab Notifications -->
            <div v-show="activeTab === 'notifications'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">🔔 Notifications</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($portalData['nationalTeamCallups'] ?? [] as $callup)
                    <div class="bg-white/10 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $callup['title'] }}</h3>
                        <p class="text-gray-300 text-sm mb-2">{{ $callup['message'] }}</p>
                        <div class="text-xs text-gray-400">{{ $callup['date'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Santé & Bien-être -->
            <div v-show="activeTab === 'sante'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">❤️ Santé & Bien-être</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($portalData['healthRecords'] ?? [] as $health)
                    <div class="bg-white/10 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $health['title'] }}</h3>
                        <p class="text-gray-300 text-sm mb-2">{{ $health['description'] }}</p>
                        <div class="text-xs text-gray-400">{{ $health['date'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Médical -->
            <div v-show="activeTab === 'medical'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">🏥 Médical</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($portalData['medicalRecords'] ?? [] as $medical)
                    <div class="bg-white/10 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $medical['title'] }}</h3>
                        <p class="text-gray-300 text-sm mb-2">{{ $medical['description'] }}</p>
                        <div class="text-xs text-gray-400">{{ $medical['record_date'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Devices -->
            <div v-show="activeTab === 'devices'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">📱 Devices</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($portalData['deviceMonitoring'] ?? [] as $device)
                    <div class="bg-white/10 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $device['device_name'] }}</h3>
                        <p class="text-gray-300 text-sm mb-2">{{ $device['device_type'] }}</p>
                        <div class="text-xs text-gray-400">Status: {{ $device['status'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab Dopage -->
            <div v-show="activeTab === 'dopage'" class="space-y-6">
                <h2 class="text-2xl font-bold text-white mb-6">🧪 Dopage</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($portalData['dopingControls'] ?? [] as $control)
                    <div class="bg-white/10 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $control['control_type'] }}</h3>
                        <p class="text-gray-300 text-sm mb-2">{{ $control['location'] }}</p>
                        <div class="text-xs text-gray-400">{{ $control['control_date'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    activeTab: 'performance',
                    navigationTabs: [
                        { id: 'performance', name: 'Performance', icon: '📊' },
                        { id: 'notifications', name: 'Notifications', icon: '🔔', notifications: 12 },
                        { id: 'sante', name: 'Santé & Bien-être', icon: '❤️' },
                        { id: 'medical', name: 'Médical', icon: '🏥', notifications: 4 },
                        { id: 'devices', name: 'Devices', icon: '📱', notifications: 3 },
                        { id: 'dopage', name: 'Dopage', icon: '🧪' }
                    ]
                }
            }
        }).mount('#fifa-app');
    </script>
</body>
</html>
