<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Auth::user()->player->first_name ?? 'Joueur' }} {{ Auth::user()->player->last_name ?? '' }} - FIFA Ultimate Dashboard</title>
    
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
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fifa-nav-tab.active {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.4);
            transform: translateY(-2px);
        }

        .fifa-nav-tab:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .fifa-position-badge {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .fifa-stat-bar {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            overflow: hidden;
            height: 10px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }

        .fifa-stat-fill {
            background: linear-gradient(90deg, #4caf50 0%, #8bc34a 50%, #cddc39 100%);
            height: 100%;
            border-radius: 12px;
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
            position: relative;
        }

        .fifa-stat-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
            border-radius: 12px 12px 0 0;
        }

        .chart-container {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .player-photo {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 4px solid rgba(255,255,255,0.3);
        }

        .fifa-ultimate-card:hover .player-photo {
            transform: scale(1.05);
            border-color: rgba(255,255,255,0.5);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .fifa-ultimate-card {
                margin: 10px;
                border-radius: 16px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 min-h-screen">
    <div id="fifa-app" class="container mx-auto px-4 py-8">
        
        <!-- Navigation retour -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('player-portal.dashboard') }}" class="flex items-center text-white hover:text-blue-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au Dashboard
            </a>
            <button @click="refreshData()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all">
                <i class="fas fa-sync-alt mr-2"></i>
                    Actualiser
                </button>
                </div>

                <!-- Hero Zone avec informations complètes du joueur -->
        <div class="fifa-ultimate-card mb-8 p-8">
            <div class="fifa-card-content">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Info joueur gauche -->
                    <div class="lg:col-span-3">
                        <div class="text-center">
                            <div class="player-photo-container mb-4">
                                <img src="{{ Auth::user()->player->player_picture ?? '/images/defaults/player_placeholder.png' }}" 
                                     alt="Photo" 
                                     class="player-photo w-32 h-40 mx-auto rounded-2xl object-cover">
                            </div>
                            <h1 class="text-4xl lg:text-6xl font-black text-white gradient-text mb-2">
                                @{{ playerData.identity.first_name }} @{{ playerData.identity.last_name }}
                            </h1>
                            <div class="flex items-center justify-center space-x-4 mt-4">
                                <img src="{{ Auth::user()->player->club->logo_url ?? '/images/defaults/club_default.svg' }}" 
                                     alt="Club" class="club-logo w-8 h-8 rounded-full object-cover">
                                <img src="{{ Auth::user()->player->nation_flag_url ?? '/images/flags/france.svg' }}" 
                                     alt="Nationalité" class="nation-flag w-8 h-6 rounded object-cover">
                            </div>
            </div>
        </div>

                    <!-- Statistiques centrales -->
                    <div class="lg:col-span-6 text-center">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <!-- Note FIFA -->
                            <div class="fifa-rating-badge w-24 h-24 mx-auto flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-black text-black">93</div>
                                        <div class="text-xs font-bold text-black">OVR</div>
                                    </div>
                                </div>

                            <!-- Potentiel -->
                            <div class="fifa-rating-badge w-24 h-24 mx-auto flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-black text-black">82</div>
                                    <div class="text-xs font-bold text-black">POT</div>
                                </div>
                            </div>
                                </div>

                        <!-- Informations détaillées -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white/90 text-sm mb-4">
                            <div class="text-center">
                                <div class="text-white/70 text-xs uppercase tracking-wide">ÂGE</div>
                                <div class="text-xl font-bold">@{{ playerData.identity.age }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-white/70 text-xs uppercase tracking-wide">TAILLE</div>
                                <div class="text-xl font-bold">170cm</div>
                            </div>
                            <div class="text-center">
                                <div class="text-white/70 text-xs uppercase tracking-wide">POIDS</div>
                                <div class="text-xl font-bold">72kg</div>
                            </div>
                            <div class="text-center">
                                <div class="text-white/70 text-xs uppercase tracking-wide">PIED FORT</div>
                                <div class="text-xl font-bold">Gauche</div>
                                </div>
                            </div>

                        <!-- Position et détails -->
                        <div class="fifa-position-badge inline-block px-4 py-2 rounded-lg mb-4">
                            <span class="text-black font-bold">RW</span>
                                    </div>
                                </div>
                                
                    <!-- Infos supplémentaires droite -->
                    <div class="lg:col-span-3 text-white space-y-4">
                        <!-- Score de santé FIT -->
                        <div class="text-center mb-6">
                            <div class="text-sm text-white/70 mb-2">SCORE DE SANTÉ FIT</div>
                            <div class="relative w-24 h-24 mx-auto">
                                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" stroke="rgba(255,255,255,0.2)" stroke-width="6" fill="none"/>
                                    <circle cx="50" cy="50" r="45" stroke="#4ade80" stroke-width="6" fill="none" 
                                            stroke-dasharray="283" stroke-dashoffset="56.6" stroke-linecap="round"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-white">85</div>
                                        <div class="text-xs text-white/70">FIT</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Indicateurs de santé -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">💚 Santé</span>
                                <span class="text-white font-medium">85/100</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">🧠 Mental</span>
                                <span class="text-white font-medium">78/100</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">😴 Sommeil</span>
                                <span class="text-white font-medium">81/100</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">🤝 Social</span>
                                <span class="text-white font-medium">92/100</span>
                        </div>
                    </div>

                        <!-- Risque blessure -->
                        <div class="text-center mt-4">
                            <div class="text-sm text-white/70 mb-1">RISQUE BLESSURE</div>
                            <div class="text-2xl font-bold text-green-400">15%</div>
                            <div class="text-xs text-green-400">FAIBLE</div>
                        </div>

                        <!-- Valeur marchande -->
                        <div class="text-center mt-4">
                            <div class="text-sm text-white/70 mb-1">VALEUR ESTIMÉE</div>
                            <div class="text-2xl font-bold text-yellow-400">50M€</div>
                            <div class="text-xs text-green-400">
                                <i class="fas fa-arrow-up mr-1"></i>+11%
                                    </div>
                                    </div>

                        <!-- Disponibilité -->
                        <div class="text-center mt-4">
                            <div class="text-sm text-white/70 mb-2">DISPONIBILITÉ</div>
                            <div class="bg-green-400 text-black px-3 py-1 rounded-full text-xs font-bold">
                                DISPONIBLE
                                    </div>
                        </div>
                    </div>
                                    </div>
                                </div>
                            </div>

        <!-- Navigation par onglets - SOUS la hero zone -->
        <div class="mb-8">
            <div class="flex flex-wrap justify-center gap-3">
                <button v-for="tab in navigationTabs" 
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="['fifa-nav-tab px-6 py-3 rounded-xl text-white transition-all font-medium', 
                                activeTab === tab.id ? 'active' : '']">
                    <i :class="tab.icon + ' mr-2'"></i>
                    @{{ tab.name }}
                </button>
                                        </div>
                                    </div>

        <!-- Contenu des onglets -->
        <div v-show="activeTab === 'performance'" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i class="fas fa-chart-line mr-3"></i>
                Performance & Évolution
            </h2>
            
            <!-- Section Évolution des Performances -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="chart-container p-4">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Évolution des Performances</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
                <div class="chart-container p-4">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Comparaison vs Moyenne</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                                </div>
                            </div>

            <!-- Section Statistiques FIFA -->
            <div class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-gamepad mr-2 text-blue-600"></i>
                    Statistiques FIFA
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="stat in fifaStats" :key="stat.name" class="text-center">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <div class="text-sm font-medium text-blue-800 mb-2" v-text="stat.name"></div>
                            <div class="relative h-20 mb-2">
                                <div class="fifa-stat-bar">
                                    <div class="fifa-stat-fill" :style="{ width: stat.value + '%' }"></div>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-2xl font-bold text-blue-900" v-text="stat.value"></span>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Section Statistiques Détaillées -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ⚽ Performances Offensives -->
                <div class="chart-container p-4">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-futbol mr-2 text-green-600"></i>
                        ⚽ Performances Offensives
                        <span class="ml-auto text-sm text-gray-500">% vs saison dernière</span>
                    </h3>
                    <div class="space-y-3">
                        <div v-for="stat in offensiveStats" :key="stat.name" class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium" v-text="stat.name"></span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-bold" :class="stat.color" v-text="stat.value"></span>
                                <span class="text-sm" :class="stat.trend >= 0 ? 'text-green-500' : 'text-red-500'">
                                    <i :class="stat.trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                    @{{ Math.abs(stat.trend) }}%
                                </span>
                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                <!-- 🏃‍♂️ Performances Physiques -->
                <div class="chart-container p-4">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-running mr-2 text-orange-600"></i>
                        🏃‍♂️ Performances Physiques & Techniques
                        <span class="ml-auto text-sm text-gray-500">% vs saison dernière</span>
                    </h3>
                    <div class="space-y-3">
                        <div v-for="stat in physicalStats" :key="stat.name" class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium" v-text="stat.name"></span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-bold" :class="stat.color" v-text="stat.value"></span>
                                <span class="text-sm" :class="stat.trend >= 0 ? 'text-green-500' : 'text-red-500'">
                                    <i :class="stat.trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                    @{{ Math.abs(stat.trend) }}%
                                </span>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>

                <div v-show="activeTab === 'medical'" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i class="fas fa-stethoscope mr-3"></i>
                Dossier Médical Complet
            </h2>
            
            <!-- Actions Médicales -->
            <div class="flex flex-wrap justify-center gap-4 mb-6">
                <button onclick="openShareModalSimple()" class="bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg transition-colors flex items-center justify-center shadow-lg">
                    <i class="fas fa-share-alt mr-2"></i>
                    Partager le dossier
                </button>
                <button onclick="printMedicalRecord()" class="bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg transition-colors flex items-center justify-center shadow-lg">
                    <i class="fas fa-print mr-2"></i>
                    Imprimer
                </button>
                <button onclick="exportMedicalPDF()" class="bg-purple-500 hover:bg-purple-600 text-white py-3 px-6 rounded-lg transition-colors flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Exporter PDF
                </button>
            </div>

            <!-- Résumé Médical -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-stethoscope text-blue-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-blue-600">6</div>
                    <div class="text-sm text-gray-600">Consultations</div>
                </div>
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-clipboard-check text-green-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-green-600">3</div>
                    <div class="text-sm text-gray-600">PCMA</div>
                </div>
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-robot text-purple-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-purple-600">2</div>
                    <div class="text-sm text-gray-600">Prédictions IA</div>
                </div>
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-orange-600">1</div>
                    <div class="text-sm text-gray-600">Alertes</div>
                </div>
            </div>

            <!-- Détails du Dossier Médical -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Dossiers de Santé -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-folder-medical mr-2 text-blue-600"></i>
                        Dossiers de Santé
                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">6 dossiers</span>
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="record in medicalData.healthRecords" :key="record.id" 
                             class="p-3 rounded-lg border-l-4 border-blue-500 bg-blue-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-file-medical mr-2 text-blue-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="record.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="record.description"></p>
                                    <div class="text-xs text-gray-500">
                                        <span v-text="record.doctor"></span> • <span v-text="record.date"></span>
                                    </div>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full',
                                               record.status === 'completed' ? 'bg-green-100 text-green-800' :
                                               record.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-blue-100 text-blue-800']" 
                                      v-text="record.status === 'completed' ? 'Terminé' :
                                               record.status === 'pending' ? 'En attente' : 'En cours'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Évaluations PCMA -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-check mr-2 text-green-600"></i>
                        Évaluations PCMA
                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">3 évaluations</span>
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="pcma in medicalData.pcmas" :key="pcma.id" 
                             class="p-3 rounded-lg border-l-4 border-green-500 bg-green-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-clipboard-list mr-2 text-green-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="pcma.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="pcma.description"></p>
                                    <div class="text-xs text-gray-500">
                                        <span v-text="pcma.assessor"></span> • <span v-text="pcma.date"></span>
                                    </div>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full',
                                               pcma.fitness === 'fit' ? 'bg-green-100 text-green-800' :
                                               pcma.fitness === 'unfit' ? 'bg-red-100 text-red-800' :
                                               'bg-yellow-100 text-yellow-800']" 
                                      v-text="pcma.fitness === 'fit' ? 'APT' :
                                               pcma.fitness === 'unfit' ? 'INA' : 'LIMITÉ'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prédictions IA et Alertes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Prédictions IA -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-robot mr-2 text-purple-600"></i>
                        Prédictions IA
                        <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">2 prédictions</span>
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="prediction in medicalData.predictions" :key="prediction.id" 
                             class="p-3 rounded-lg border-l-4 border-purple-500 bg-purple-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-brain mr-2 text-purple-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="prediction.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="prediction.description"></p>
                                    <div class="text-xs text-gray-500">
                                        <span v-text="prediction.date"></span> • <span v-text="prediction.confidence"></span>% confiance
                                    </div>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full',
                                               prediction.risk === 'low' ? 'bg-green-100 text-green-800' :
                                               prediction.risk === 'high' ? 'bg-red-100 text-red-800' :
                                               'bg-yellow-100 text-yellow-800']" 
                                      v-text="prediction.risk === 'low' ? 'FAIBLE' :
                                               prediction.risk === 'high' ? 'ÉLEVÉ' : 'MOYEN'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertes Médicales -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>
                        Alertes Médicales
                        <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">1 alerte</span>
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="alert in medicalData.alerts" :key="alert.id" 
                             class="p-3 rounded-lg border-l-4 border-orange-500 bg-orange-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-bell mr-2 text-orange-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="alert.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="alert.message"></p>
                                    <div class="text-xs text-gray-500">
                                        <span v-text="alert.date"></span> • <span v-text="alert.priority"></span>
                                    </div>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full',
                                               alert.priority === 'high' ? 'bg-red-100 text-red-800' :
                                               alert.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-blue-100 text-blue-800']" 
                                      v-text="alert.priority === 'high' ? 'URGENT' :
                                               alert.priority === 'medium' ? 'IMPORTANT' : 'INFO'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historiques de Blessures et Maladies -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Historique des Blessures -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-band-aid mr-2 text-red-600"></i>
                        Historique des Blessures
                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">3 blessures</span>
                    </h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div v-for="injury in medicalData.injuries" :key="injury.id" 
                             :class="['p-3 rounded-lg border-l-4', 
                                      injury.status === 'recovered' ? 'border-green-500 bg-green-50' :
                                      injury.status === 'recovering' ? 'border-yellow-500 bg-yellow-50' :
                                      'border-red-500 bg-red-50']">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="injury.type"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="injury.description"></p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <span v-text="injury.location"></span> • <span v-text="injury.doctor"></span> • <span v-text="injury.date"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span :class="['px-2 py-1 text-xs rounded-full mb-1 block',
                                                   injury.status === 'recovered' ? 'bg-green-100 text-green-800' :
                                                   injury.status === 'recovering' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800']" 
                                          v-text="injury.status === 'recovered' ? 'GUÉRI' :
                                                   injury.status === 'recovering' ? 'EN COURS' : 'CRITIQUE'"></span>
                                    <span :class="['px-2 py-1 text-xs rounded-full',
                                                   injury.severity === 'mild' ? 'bg-blue-100 text-blue-800' :
                                                   injury.severity === 'moderate' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800']" 
                                          v-text="injury.severity === 'mild' ? 'LÉGÈRE' :
                                                   injury.severity === 'moderate' ? 'MODÉRÉE' : 'GRAVE'"></span>
                                </div>
                            </div>
                            
                            <!-- Barre de progression de récupération -->
                            <div class="mb-2">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Récupération</span>
                                    <span v-text="injury.recovery + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div :class="['h-2 rounded-full transition-all duration-1000',
                                                  injury.recovery === 100 ? 'bg-green-500' :
                                                  injury.recovery >= 75 ? 'bg-yellow-500' :
                                                  'bg-red-500']" 
                                         :style="{ width: injury.recovery + '%' }"></div>
                                </div>
                            </div>

                            <!-- Détails de la blessure -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                <div>
                                    <strong class="text-gray-700">Traitement:</strong>
                                    <p class="text-gray-600" v-text="injury.treatment"></p>
                                </div>
                                <div>
                                    <strong class="text-gray-700">Retour estimé:</strong>
                                    <p class="text-gray-600" v-text="injury.estimatedReturn"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Restrictions:</strong>
                                    <p class="text-gray-600" v-text="injury.restrictions"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Symptômes:</strong>
                                    <p class="text-gray-600" v-text="injury.symptoms"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des Maladies -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-virus mr-2 text-orange-600"></i>
                        Historique des Maladies
                        <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">2 maladies</span>
                    </h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div v-for="illness in medicalData.illnesses" :key="illness.id" 
                             :class="['p-3 rounded-lg border-l-4', 
                                      illness.status === 'recovered' ? 'border-green-500 bg-green-50' :
                                      illness.status === 'recovering' ? 'border-yellow-500 bg-yellow-50' :
                                      'border-red-500 bg-red-50']">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-stethoscope mr-2 text-orange-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="illness.type"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="illness.description"></p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <span v-text="illness.diagnosis"></span> • <span v-text="illness.doctor"></span> • <span v-text="illness.date"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span :class="['px-2 py-1 text-xs rounded-full mb-1 block',
                                                   illness.status === 'recovered' ? 'bg-green-100 text-green-800' :
                                                   illness.status === 'recovering' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800']" 
                                          v-text="illness.status === 'recovered' ? 'GUÉRI' :
                                                   illness.status === 'recovering' ? 'EN COURS' : 'CRITIQUE'"></span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">MALADIE</span>
                                </div>
                            </div>
                            
                            <!-- Barre de progression de récupération -->
                            <div class="mb-2">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Récupération</span>
                                    <span v-text="illness.recovery + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div :class="['h-2 rounded-full transition-all duration-1000',
                                                  illness.recovery === 100 ? 'bg-green-500' :
                                                  illness.recovery >= 75 ? 'bg-yellow-500' :
                                                  'bg-red-500']" 
                                         :style="{ width: illness.recovery + '%' }"></div>
                                </div>
                            </div>

                            <!-- Détails de la maladie -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                <div>
                                    <strong class="text-gray-700">Traitement:</strong>
                                    <p class="text-gray-600" v-text="illness.treatment"></p>
                                </div>
                                <div>
                                    <strong class="text-right">Retour estimé:</strong>
                                    <p class="text-gray-600" v-text="illness.estimatedReturn"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Restrictions:</strong>
                                    <p class="text-gray-600" v-text="illness.restrictions"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Symptômes:</strong>
                                    <p class="text-gray-600" v-text="illness.symptoms"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Complications:</strong>
                                    <p class="text-gray-600" v-text="illness.complications"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Notifications -->
        <div v-show="activeTab === 'notifications'" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i class="fas fa-bell mr-3"></i>
                Centre de Notifications
            </h2>

            <!-- Filtres de notifications -->
            <div class="flex flex-wrap justify-center gap-3 mb-6">
                <button v-for="filter in notificationFilters" 
                        :key="filter.id"
                        @click="activeNotificationFilter = filter.id"
                        :class="['fifa-nav-tab px-4 py-2 rounded-lg text-white transition-all text-sm', 
                                activeNotificationFilter === filter.id ? 'active' : '']">
                    <i :class="filter.icon + ' mr-2'"></i>
                    @{{ filter.name }}
                    <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs" v-text="filter.count"></span>
                </button>
                                </div>

            <!-- Section Équipe Nationale -->
            <div v-show="['all', 'national'].includes(activeNotificationFilter)" 
                 v-if="notificationData.nationalTeam.length > 0" class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-flag mr-2 text-blue-600"></i>
                    Équipe Nationale
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full" v-text="notificationData.nationalTeam.filter(n => n.urgent).length + ' urgent' + (notificationData.nationalTeam.filter(n => n.urgent).length > 1 ? 's' : '')"></span>
                </h3>
                <div class="space-y-3">
                    <div v-for="notification in notificationData.nationalTeam" :key="notification.id" 
                         :class="['rounded-lg p-4 border-l-4', notification.urgent ? 'bg-gradient-to-r from-red-50 to-red-100 border-red-500' : 'bg-gradient-to-r from-blue-50 to-blue-100 border-blue-500']">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i :class="notification.icon + ' mr-2 ' + (notification.urgent ? 'text-red-600' : 'text-blue-600')"></i>
                                    <h4 class="font-semibold" :class="notification.urgent ? 'text-red-900' : 'text-blue-900'" v-text="notification.title"></h4>
                                    <span v-if="notification.urgent" class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">URGENT</span>
                            </div>
                                <p class="text-gray-700 text-sm mb-2" v-text="notification.message"></p>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span v-text="notification.date"></span>
                                    <i class="fas fa-map-marker-alt ml-3 mr-1"></i>
                                    <span v-text="notification.location"></span>
                        </div>
                            </div>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                Voir détails
                            </button>
                    </div>
                </div>
            </div>
        </div>

            <!-- Section Convocations Entraînements -->
            <div v-show="['all', 'training'].includes(activeNotificationFilter)" 
                 v-if="notificationData.trainingSessions.length > 0" class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-running mr-2 text-green-600"></i>
                    Convocations Entraînements
                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full" v-text="notificationData.trainingSessions.filter(s => s.mandatory).length + ' obligatoires'"></span>
                </h3>
                <div class="space-y-3">
                    <div v-for="session in notificationData.trainingSessions" :key="session.id" 
                         class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i :class="session.icon + ' mr-2 text-green-600'"></i>
                                    <h4 class="font-semibold text-green-900" v-text="session.title"></h4>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full" 
                                          :class="session.mandatory ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white'"
                                          v-text="session.mandatory ? 'OBLIGATOIRE' : 'OPTIONNEL'"></span>
                                </div>
                                <p class="text-gray-700 text-sm mb-2" v-text="session.description"></p>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span v-text="session.date + ' - ' + session.time"></span>
                                    <i class="fas fa-map-marker-alt ml-3 mr-1"></i>
                                    <span v-text="session.location"></span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Confirmer
                                </button>
                                <button v-if="!session.mandatory" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Décliner
                </button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

            <!-- Section Convocations Matches -->
            <div v-show="['all', 'matches'].includes(activeNotificationFilter)" 
                 v-if="notificationData.matches.length > 0" class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-futbol mr-2 text-purple-600"></i>
                    Convocations Matches
                    <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full" v-text="notificationData.matches.filter(m => m.status === 'convoqué').length + ' confirmées'"></span>
                </h3>
                <div class="space-y-3">
                    <div v-for="match in notificationData.matches" :key="match.id" 
                         class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i :class="match.icon + ' mr-2 text-purple-600'"></i>
                                    <h4 class="font-semibold text-purple-900" v-text="match.title"></h4>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full"
                                          :class="match.status === 'convoqué' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white'"
                                          v-text="match.status.toUpperCase()"></span>
                                </div>
                                <div class="text-sm text-gray-700 mb-2">
                                    <strong v-text="match.competition"></strong> • <span v-text="match.opponent"></span>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span v-text="match.date + ' à ' + match.time"></span>
                                    <br>
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <span v-text="match.stadium"></span>
                                    <br>
                                    <i class="fas fa-clock mr-1"></i>
                                    <strong>Rassemblement:</strong> <span v-text="match.meetingTime"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>

            <!-- Section Rendez-vous Médicaux -->
            <div v-show="['all', 'medical'].includes(activeNotificationFilter)" 
                 v-if="notificationData.medicalAppointments.length > 0" class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-user-md mr-2 text-red-600"></i>
                    Rendez-vous Médicaux
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full" v-text="notificationData.medicalAppointments.filter(a => a.urgent).length + ' urgent' + (notificationData.medicalAppointments.filter(a => a.urgent).length > 1 ? 's' : '')"></span>
                </h3>
                <div class="space-y-3">
                    <div v-for="appointment in notificationData.medicalAppointments" :key="appointment.id" 
                         :class="['rounded-lg p-4 border-l-4', appointment.urgent ? 'bg-gradient-to-r from-red-50 to-red-100 border-red-500' : 'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-500']">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i :class="appointment.icon + ' mr-2 ' + (appointment.urgent ? 'text-red-600' : 'text-orange-600')"></i>
                                    <h4 class="font-semibold" :class="appointment.urgent ? 'text-red-900' : 'text-orange-900'" v-text="appointment.type"></h4>
                                    <span v-if="appointment.urgent" class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">URGENT</span>
                                </div>
                                <p class="text-gray-700 text-sm mb-2" v-text="appointment.purpose"></p>
                                <div class="text-xs text-gray-600">
                                    <strong>Praticien:</strong> <span v-text="appointment.doctor"></span><br>
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span v-text="appointment.date + ' à ' + appointment.time"></span><br>
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <span v-text="appointment.location"></span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Confirmer
                                </button>
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Reporter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>

            <!-- Section Alertes Réseaux Sociaux -->
            <div v-show="['all', 'social'].includes(activeNotificationFilter)" 
                 v-if="notificationData.socialAlerts.length > 0" class="chart-container p-4 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-share-alt mr-2 text-indigo-600"></i>
                    Alertes Réseaux Sociaux
                    <span class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full" v-text="notificationData.socialAlerts.filter(a => a.needsResponse).length + ' à traiter'"></span>
                </h3>
                <div class="space-y-3">
                    <div v-for="alert in notificationData.socialAlerts" :key="alert.id" 
                         :class="['rounded-lg p-4 border-l-4', 
                                  alert.sentiment === 'positive' ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-500' :
                                  alert.sentiment === 'negative' ? 'bg-gradient-to-r from-red-50 to-red-100 border-red-500' :
                                  'bg-gradient-to-r from-blue-50 to-blue-100 border-blue-500']">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i :class="'fab fa-' + alert.platform + ' mr-2 ' + 
                                               (alert.sentiment === 'positive' ? 'text-green-600' :
                                                alert.sentiment === 'negative' ? 'text-red-600' : 'text-blue-600')"></i>
                                    <h4 class="font-semibold" 
                                        :class="alert.sentiment === 'positive' ? 'text-green-900' :
                                                alert.sentiment === 'negative' ? 'text-red-900' : 'text-blue-900'" 
                                        v-text="alert.title"></h4>
                                    <span v-if="alert.needsResponse" class="ml-2 px-2 py-1 bg-orange-500 text-white text-xs rounded-full">À TRAITER</span>
                                </div>
                                <p class="text-gray-700 text-sm mb-2" v-text="alert.content"></p>
                                <div class="text-xs text-gray-600">
                                    <span v-text="alert.timestamp"></span> • 
                                    <span v-text="alert.views"></span> vues • 
                                    <span v-text="alert.engagement"></span> interactions
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Voir
                                </button>
                                <button v-if="alert.needsResponse" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Répondre
                                </button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Santé & Bien-être -->
            <div v-show="activeTab === 'health'" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i class="fas fa-heartbeat mr-3"></i>
                Santé & Bien-être
            </h2>
                
            <!-- Section Score FIT Global -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Score FIT Principal -->
                    <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                        Évolution du Score FIT
                    </h3>
                    <div class="flex items-center justify-center mb-4">
                        <div class="relative w-32 h-32">
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" stroke="rgba(0,0,0,0.1)" stroke-width="6" fill="none"/>
                                <circle cx="50" cy="50" r="45" stroke="#10b981" stroke-width="6" fill="none" 
                                        :stroke-dasharray="283" 
                                        :stroke-dashoffset="283 - (283 * healthData.globalScore / 100)" 
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-600" v-text="healthData.globalScore"></div>
                                    <div class="text-sm text-gray-600">FIT</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h-48">
                        <canvas id="healthScoreChart"></canvas>
                    </div>
                    </div>

                <!-- Répartition Santé -->
                    <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-chart-donut mr-2 text-blue-600"></i>
                        Répartition Santé
                    </h3>
                    <div class="h-64">
                        <canvas id="healthBreakdownChart"></canvas>
                    </div>
                </div>
                    </div>

            <!-- Indicateurs de Santé -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="chart-container p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-heart text-red-500 text-2xl mr-2"></i>
                                    <div>
                            <div class="text-xl font-bold text-gray-800" v-text="healthData.physicalScore"></div>
                            <div class="text-sm text-gray-600">Physique</div>
                                    </div>
                                </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full transition-all duration-1000" 
                             :style="{ width: healthData.physicalScore + '%' }"></div>
                                </div>
                            </div>

                <div class="chart-container p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-brain text-purple-500 text-2xl mr-2"></i>
                        <div>
                            <div class="text-xl font-bold text-gray-800" v-text="healthData.mentalScore"></div>
                            <div class="text-sm text-gray-600">Mental</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full transition-all duration-1000" 
                             :style="{ width: healthData.mentalScore + '%' }"></div>
                </div>
            </div>

                <div class="chart-container p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-moon text-indigo-500 text-2xl mr-2"></i>
                        <div>
                            <div class="text-xl font-bold text-gray-800" v-text="healthData.sleepScore"></div>
                            <div class="text-sm text-gray-600">Sommeil</div>
                                </div>
                                </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full transition-all duration-1000" 
                             :style="{ width: healthData.sleepScore + '%' }"></div>
                                    </div>
                                    </div>

                <div class="chart-container p-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-users text-green-500 text-2xl mr-2"></i>
                                    <div>
                            <div class="text-xl font-bold text-gray-800" v-text="healthData.socialScore"></div>
                            <div class="text-sm text-gray-600">Social</div>
                                    </div>
                                </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" 
                             :style="{ width: healthData.socialScore + '%' }"></div>
                            </div>
                        </div>
                    </div>

            <!-- Paramètres Vitaux -->
            <div class="chart-container p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-thermometer-half mr-2 text-orange-600"></i>
                    Paramètres Vitaux Actuels
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-heartbeat text-red-600 text-2xl mb-2"></i>
                        <div class="text-sm text-gray-600">Fréq. Cardiaque</div>
                        <div class="text-xl font-bold text-red-600" v-text="healthData.vitals.heartRate"></div>
                        <div class="text-xs text-gray-500">BPM</div>
                                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-tachometer-alt text-blue-600 text-2xl mb-2"></i>
                        <div class="text-sm text-gray-600">Tension</div>
                        <div class="text-xl font-bold text-blue-600" v-text="healthData.vitals.bloodPressure"></div>
                        <div class="text-xs text-gray-500">mmHg</div>
                                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-thermometer-half text-yellow-600 text-2xl mb-2"></i>
                        <div class="text-sm text-gray-600">Température</div>
                        <div class="text-xl font-bold text-yellow-600" v-text="healthData.vitals.temperature + '°'"></div>
                        <div class="text-xs text-gray-500">Celsius</div>
                                </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-weight text-purple-600 text-2xl mb-2"></i>
                        <div class="text-sm text-gray-600">Poids</div>
                        <div class="text-xl font-bold text-purple-600" v-text="healthData.vitals.weight"></div>
                        <div class="text-xs text-gray-500">kg</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <div class="text-sm text-gray-600">IMC</div>
                        <div class="text-xl font-bold text-green-600" v-text="healthData.vitals.bmi"></div>
                        <div class="text-xs text-gray-500">(Normal)</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <div class="text-sm text-gray-600">Masse Grasse</div>
                        <div class="text-xl font-bold text-indigo-600" v-text="healthData.vitals.bodyFat + '%'"></div>
                        <div class="text-xs text-gray-500">(Excellent)</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg">
                        <div class="text-sm text-gray-600">Hydratation</div>
                        <div class="text-xl font-bold text-cyan-600" v-text="healthData.vitals.hydration + '%'"></div>
                        <div class="text-xs text-gray-500">(Bon)</div>
                            </div>
                        </div>
                    </div>

            <!-- Historique Médical -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-gray-600"></i>
                        Historique Médical
                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                            Risque: @{{ healthData.injuryRisk }}% (@{{ healthData.injuryRiskLevel }})
                        </span>
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="record in healthData.medicalHistory" :key="record.id" 
                             :class="['p-3 rounded-lg border-l-4', 
                                      record.riskLevel === 'low' ? 'bg-green-50 border-green-500' :
                                      record.riskLevel === 'medium' ? 'bg-yellow-50 border-yellow-500' :
                                      'bg-red-50 border-red-500']">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i :class="'fas mr-2 ' + 
                                                   (record.type === 'consultation' ? 'fa-stethoscope text-blue-600' :
                                                    record.type === 'injury' ? 'fa-exclamation-triangle text-yellow-600' :
                                                    'fa-shield-alt text-green-600')"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="record.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="record.summary"></p>
                                    <div class="text-xs text-gray-500">
                                        <span v-text="record.doctor"></span> • <span v-text="record.date"></span>
                                </div>
                                </div>
                                <span :class="['px-2 py-1 text-xs rounded-full',
                                               record.status === 'completed' ? 'bg-green-100 text-green-800' :
                                               record.status === 'recovering' ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-blue-100 text-blue-800']" 
                                      v-text="record.status === 'completed' ? 'Terminé' :
                                               record.status === 'recovering' ? 'En cours' : 'Actif'"></span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Recommandations -->
                    <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                        Recommandations Personnalisées
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        <div v-for="rec in healthData.recommendations" :key="rec.id" 
                             :class="['p-3 rounded-lg border-l-4',
                                      rec.priority === 'high' ? 'bg-red-50 border-red-500' :
                                      rec.priority === 'medium' ? 'bg-yellow-50 border-yellow-500' :
                                      'bg-blue-50 border-blue-500']">
                            <div class="flex items-start">
                                <i :class="rec.icon + ' mr-3 mt-1 ' + 
                                           (rec.priority === 'high' ? 'text-red-600' :
                                            rec.priority === 'medium' ? 'text-yellow-600' :
                                            'text-blue-600')"></i>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-semibold text-gray-800" v-text="rec.title"></h4>
                                        <span :class="['px-2 py-1 text-xs rounded-full',
                                                       rec.priority === 'high' ? 'bg-red-100 text-red-800' :
                                                       rec.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                                       'bg-blue-100 text-blue-800']"
                                              v-text="rec.priority === 'high' ? 'URGENT' :
                                                       rec.priority === 'medium' ? 'IMPORTANT' : 'INFO'"></span>
                    </div>
                                    <p class="text-sm text-gray-600" v-text="rec.description"></p>
                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Dopage -->
        <div v-show="activeTab === 'doping'" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i class="fas fa-vial mr-3"></i>
                Contrôles Antidopage & ATU
            </h2>

            <!-- Statut des Contrôles -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-calendar-check text-green-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-green-600">28/02/2025</div>
                    <div class="text-sm text-gray-600">Dernier contrôle</div>
                </div>
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-blue-600">15/04/2025</div>
                    <div class="text-sm text-gray-600">Prochain contrôle</div>
                </div>
                <div class="chart-container p-4 text-center">
                    <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                    <div class="text-2xl font-bold text-green-600">3/3</div>
                    <div class="text-sm text-gray-600">Contrôles réussis</div>
                </div>
            </div>

            <!-- Historique des Contrôles -->
            <div class="chart-container p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    Historique des Contrôles
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">3 contrôles</span>
                </h3>
                <div class="space-y-3 max-h-48 overflow-y-auto">
                    <div v-for="control in dopingData.controlHistory" :key="control.id" 
                         class="p-3 rounded-lg border-l-4 border-green-500 bg-green-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-vial mr-2 text-green-600"></i>
                                    <h4 class="font-semibold text-gray-800" v-text="control.type"></h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-1" v-text="control.notes"></p>
                                <div class="text-xs text-gray-500 mb-2">
                                    <span v-text="control.location"></span> • <span v-text="control.date"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 mb-1 block">
                                    @{{ control.result.toUpperCase() }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    @{{ control.status === 'completed' ? 'TERMINÉ' : 'EN COURS' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-600">
                            <strong>Substances détectées:</strong> <span v-text="control.substances"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Déclarations ATU Actives -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-file-medical mr-2 text-purple-600"></i>
                        Déclarations ATU Actives
                        <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">3 déclarations</span>
                    </h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div v-for="atu in dopingData.atuDeclarations" :key="atu.id" 
                             :class="['p-3 rounded-lg border-l-4', 
                                      atu.riskLevel === 'none' ? 'border-green-500 bg-green-50' :
                                      atu.riskLevel === 'low' ? 'border-yellow-500 bg-yellow-50' :
                                      'border-red-500 bg-red-50']">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-pills mr-2 text-purple-600"></i>
                                        <h4 class="font-semibold text-gray-800" v-text="atu.substance"></h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1" v-text="atu.reason"></p>
                                    <div class="text-xs text-gray-500 mb-2">
                                        <span v-text="atu.doctor"></span> • <span v-text="atu.startDate + ' - ' + atu.endDate</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span :class="['px-2 py-1 text-xs rounded-full mb-1 block',
                                                   atu.riskLevel === 'none' ? 'bg-green-100 text-green-800' :
                                                   atu.riskLevel === 'low' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800']" 
                                          v-text="atu.riskLevel === 'none' ? 'AUCUN RISQUE' :
                                                   atu.riskLevel === 'low' ? 'RISQUE FAIBLE' : 'RISQUE ÉLEVÉ'"></span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        ACTIVE
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Détails de l'ATU -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                <div>
                                    <strong class="text-gray-700">Dosage:</strong>
                                    <p class="text-gray-600" v-text="atu.dosage"></p>
                                </div>
                                <div>
                                    <strong class="text-gray-700">Fréquence:</strong>
                                    <p class="text-gray-600" v-text="atu.frequency"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <strong class="text-gray-700">Notes:</strong>
                                    <p class="text-gray-600" v-text="atu.notes"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertes et Substances Interdites -->
                <div class="chart-container p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>
                        Alertes & Substances Interdites
                        <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">2 alertes</span>
                    </h3>
                    
                    <!-- Alertes de Risque -->
                    <div class="space-y-3 mb-4 max-h-32 overflow-y-auto">
                        <div v-for="alert in dopingData.riskAlerts" :key="alert.id" 
                             :class="['p-2 rounded-lg border-l-4', 
                                      alert.priority === 'high' ? 'border-red-500 bg-red-50' :
                                      alert.priority === 'medium' ? 'border-yellow-500 bg-yellow-50' :
                                      'border-blue-500 bg-blue-50']">
                            <div class="flex items-start">
                                <i :class="'fas mr-2 mt-1 ' + 
                                           (alert.priority === 'high' ? 'fa-exclamation-triangle text-red-600' :
                                            alert.priority === 'medium' ? 'fa-exclamation-circle text-yellow-600' :
                                            'fa-info-circle text-blue-600')"></i>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-sm" :class="alert.priority === 'high' ? 'text-red-900' : 
                                                                           alert.priority === 'medium' ? 'text-yellow-900' : 'text-blue-900'" 
                                         v-text="alert.title"></h4>
                                    <p class="text-xs text-gray-600" v-text="alert.message"></p>
                                    <div class="text-xs text-gray-500 mt-1" v-text="alert.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Substances Interdites -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2 text-sm">Substances Interdites Principales:</h4>
                        <div class="grid grid-cols-1 gap-1 text-xs">
                            <div v-for="substance in dopingData.prohibitedSubstances" :key="substance" 
                                 class="flex items-center p-1 rounded bg-red-50 border border-red-200">
                                <i class="fas fa-ban text-red-600 mr-2 text-xs"></i>
                                <span class="text-red-800" v-text="substance"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Importantes -->
            <div class="chart-container p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informations Importantes
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2">⚠️ Règles de Base</h4>
                        <ul class="text-blue-800 space-y-1 text-xs">
                            <li>• Toujours déclarer les médicaments prescrits</li>
                            <li>• Vérifier la composition des compléments</li>
                            <li>• Consulter avant prise de nouveaux produits</li>
                            <li>• Garder les ordonnances à jour</li>
                        </ul>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h4 class="font-semibold text-green-900 mb-2">✅ Bonnes Pratiques</h4>
                        <ul class="text-green-800 space-y-1 text-xs">
                            <li>• Contrôles réguliers programmés</li>
                            <li>• Déclarations ATU à jour</li>
                            <li>• Communication transparente</li>
                            <li>• Formation continue antidopage</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet par défaut pour les autres -->
        <div v-show="!['performance', 'medical', 'notifications', 'health', 'doping'].includes(activeTab)" class="space-y-6">
            <h2 class="text-3xl font-bold text-white gradient-text mb-6">
                <i :class="navigationTabs.find(tab => tab.id === activeTab)?.icon + ' mr-3'"></i>
                @{{ navigationTabs.find(tab => tab.id === activeTab)?.name }}
            </h2>
            <div class="chart-container p-4">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Contenu à venir</h3>
                <p class="text-gray-600">Cette section sera implémentée prochainement.</p>
                </div>
            </div>
    </div>

    <!-- Modal de Partage Médical -->
    <div id="medicalShareModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Partager le Dossier Médical</h3>
                    <button onclick="closeShareModalSimple()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                        <input type="email" id="shareEmail" placeholder="email@exemple.com" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (optionnel)</label>
                        <textarea id="shareMessage" rows="3" placeholder="Message personnalisé..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button onclick="shareSimple()" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Envoyer
                        </button>
                        <button onclick="closeShareModalSimple()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md transition-colors">
                            Annuler
                        </button>
                    </div>
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
                        { id: 'performance', name: 'Performance', icon: 'fas fa-chart-line' },
                        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' },
                        { id: 'health', name: 'Santé & Bien-être', icon: 'fas fa-heartbeat' },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-stethoscope' },
                        { id: 'doping', name: 'Dopage', icon: 'fas fa-vial' },
                        { id: 'device', name: 'Appareils', icon: 'fas fa-mobile-alt' },
                        { id: 'history', name: 'Historique', icon: 'fas fa-history' },
                        { id: 'market', name: 'Marché', icon: 'fas fa-trending-up' }
                    ],
                    playerData: {
                        identity: {
                            first_name: 'Lionel',
                            last_name: 'Messi',
                            age: 37
                        }
                    },
                    // Données complètes pour les performances
                    offensiveStats: [
                        { name: 'Buts', value: '15', trend: 12, color: 'text-green-600' },
                        { name: 'Assists', value: '8', trend: 6, color: 'text-blue-600' },
                        { name: 'Tirs cadrés', value: '67%', trend: 4, color: 'text-purple-600' },
                        { name: 'Passes décisives', value: '23', trend: 8, color: 'text-indigo-600' },
                        { name: 'Buts/Match', value: '0.8', trend: 15, color: 'text-green-700' },
                        { name: 'xG (Expected Goals)', value: '12.3', trend: -2, color: 'text-orange-600' }
                    ],
                    physicalStats: [
                        { name: 'Distance/Match', value: '11.4 km', trend: 3, color: 'text-orange-600' },
                        { name: 'Vitesse Max', value: '32.1 km/h', trend: 1, color: 'text-red-600' },
                        { name: 'Sprints', value: '47/match', trend: 5, color: 'text-yellow-600' },
                        { name: 'Passes réussies', value: '89.2%', trend: 7, color: 'text-blue-600' },
                        { name: 'Duels gagnés', value: '71%', trend: 4, color: 'text-purple-600' },
                        { name: 'Interceptions', value: '2.3/match', trend: 12, color: 'text-green-600' }
                    ],
                    fifaStats: [
                        { name: 'VIT', value: 85, animated_value: 0 },
                        { name: 'TIR', value: 78, animated_value: 0 },
                        { name: 'PAS', value: 82, animated_value: 0 },
                        { name: 'DRI', value: 75, animated_value: 0 },
                        { name: 'DEF', value: 65, animated_value: 0 },
                        { name: 'PHY', value: 80, animated_value: 0 }
                    ],
                    // Données complètes pour les notifications
                    activeNotificationFilter: 'all',
                    notificationFilters: [
                        { id: 'all', name: 'Toutes', icon: 'fas fa-list', count: 12 },
                        { id: 'national', name: 'Équipe Nationale', icon: 'fas fa-flag', count: 2 },
                        { id: 'training', name: 'Entraînements', icon: 'fas fa-running', count: 3 },
                        { id: 'matches', name: 'Matches', icon: 'fas fa-futbol', count: 2 },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md', count: 2 },
                        { id: 'social', name: 'Réseaux Sociaux', icon: 'fas fa-share-alt', count: 3 }
                    ],
                    notificationData: {
                        nationalTeam: [
                            {
                                id: 1,
                                title: 'Convocation Équipe de France',
                                message: 'Vous êtes convoqué pour les matches amicaux contre l\'Italie et l\'Espagne.',
                                date: 'Rassemblement: 18 Mars 2025',
                                location: 'Centre National de Clairefontaine',
                                icon: 'fas fa-flag',
                                urgent: true
                            },
                            {
                                id: 2,
                                title: 'Stage de Préparation',
                                message: 'Stage de préparation avant les qualifications Euro 2026.',
                                date: '25-30 Mars 2025',
                                location: 'Clairefontaine',
                                icon: 'fas fa-clipboard-list',
                                urgent: false
                            }
                        ],
                        trainingSessions: [
                            {
                                id: 1,
                                title: 'Entraînement Technique',
                                description: 'Session de travail technique et tactique avant le match de Ligue 1.',
                                date: 'Aujourd\'hui',
                                time: '10:00 - 12:00',
                                location: 'Centre d\'Entraînement',
                                icon: 'fas fa-futbol',
                                mandatory: true
                            },
                            {
                                id: 2,
                                title: 'Séance Physique',
                                description: 'Préparation physique et récupération active.',
                                date: 'Demain',
                                time: '09:00 - 10:30',
                                location: 'Salle de Fitness',
                                icon: 'fas fa-dumbbell',
                                mandatory: true
                            },
                            {
                                id: 3,
                                title: 'Entraînement Libre',
                                description: 'Session optionnelle de perfectionnement individuel.',
                                date: 'Vendredi',
                                time: '14:00 - 16:00',
                                location: 'Terrain Annexe',
                                icon: 'fas fa-running',
                                mandatory: false
                            }
                        ],
                        matches: [
                            {
                                id: 1,
                                title: 'PSG vs Olympique de Marseille',
                                opponent: 'Olympique de Marseille',
                                competition: 'Ligue 1',
                                date: 'Dimanche 15 Mars',
                                time: '21:00',
                                stadium: 'Parc des Princes',
                                meetingTime: '18:00 au stade',
                                status: 'convoqué',
                                icon: 'fas fa-futbol'
                            },
                            {
                                id: 2,
                                title: 'PSG vs Bayern Munich',
                                opponent: 'Bayern Munich',
                                competition: 'Champions League',
                                date: 'Mercredi 19 Mars',
                                time: '21:00',
                                stadium: 'Allianz Arena, Munich',
                                meetingTime: '17:00 à l\'hôtel',
                                status: 'attente',
                                icon: 'fas fa-trophy'
                            }
                        ],
                        medicalAppointments: [
                                {
                                    id: 1,
                                type: 'Examen Médical Complet',
                                doctor: 'Dr. Martin Dubois',
                                purpose: 'Bilan de santé trimestriel obligatoire',
                                date: 'Lundi 11 Mars',
                                time: '14:00',
                                location: 'Centre Médical du Club',
                                icon: 'fas fa-stethoscope',
                                urgent: false
                            },
                            {
                                id: 2,
                                type: 'Consultation Kinésithérapeute',
                                doctor: 'Sophie Moreau',
                                purpose: 'Suivi blessure genou droit',
                                date: 'Mercredi 13 Mars',
                                time: '16:30',
                                location: 'Cabinet de Kinésithérapie',
                                icon: 'fas fa-hand-holding-medical',
                                urgent: true
                            }
                        ],
                        socialAlerts: [
                            {
                                id: 1,
                                title: 'Mention sur Instagram',
                                content: '@psg vous a tagué dans une story : "Nos stars à l\'entraînement aujourd\'hui 🔥"',
                                platform: 'instagram',
                                sentiment: 'positive',
                                timestamp: 'Il y a 2h',
                                views: '45.2K',
                                engagement: '1.2K',
                                needsResponse: false
                            },
                            {
                                id: 2,
                                title: 'Article L\'Équipe',
                                content: 'L\'Équipe publie un article sur vos performances récentes en Champions League.',
                                platform: 'google',
                                sentiment: 'positive',
                                timestamp: 'Il y a 4h',
                                views: '152K',
                                engagement: '892',
                                needsResponse: false
                            },
                            {
                                id: 3,
                                title: 'Commentaire Facebook',
                                content: 'Discussion animée sur votre dernière performance. Certains commentaires négatifs.',
                                platform: 'facebook',
                                sentiment: 'negative',
                                timestamp: 'Il y a 6h',
                                views: '23.1K',
                                engagement: '456',
                                needsResponse: true
                            }
                        ]
                    },
                    // Données complètes pour la santé et bien-être
                    healthData: {
                        globalScore: 85,
                        physicalScore: 85,
                        mentalScore: 78,
                        sleepScore: 81,
                        socialScore: 92,
                        injuryRisk: 15,
                        injuryRiskLevel: 'FAIBLE',
                        vitals: {
                            heartRate: 65,
                            bloodPressure: '120/80',
                            temperature: 36.8,
                            weight: 72,
                            bmi: 24.9,
                            bodyFat: 8.5,
                            hydration: 92
                        },
                        recentMetrics: [
                            { date: '2025-03-10', fitScore: 85, sleep: 7.5, stress: 25, recovery: 82 },
                            { date: '2025-03-09', fitScore: 83, sleep: 6.8, stress: 30, recovery: 78 },
                            { date: '2025-03-08', fitScore: 87, sleep: 8.2, stress: 18, recovery: 89 },
                            { date: '2025-03-07', fitScore: 82, sleep: 7.1, stress: 35, recovery: 75 },
                            { date: '2025-03-06', fitScore: 86, sleep: 7.8, stress: 22, recovery: 84 },
                            { date: '2025-03-05', fitScore: 84, sleep: 7.0, stress: 28, recovery: 80 },
                            { date: '2025-03-04', fitScore: 88, sleep: 8.5, stress: 15, recovery: 91 }
                        ],
                        medicalHistory: [
                            {
                                id: 1,
                                type: 'consultation',
                                date: '2025-03-05',
                                title: 'Bilan de santé trimestriel',
                                doctor: 'Dr. Jean Martin',
                                status: 'completed',
                                riskLevel: 'low',
                                summary: 'État de santé excellent, aucune anomalie détectée'
                            },
                            {
                                id: 2,
                                type: 'injury',
                                date: '2025-02-15',
                                title: 'Élongation ischio-jambiers',
                                doctor: 'Dr. Sophie Moreau',
                                status: 'recovering',
                                riskLevel: 'medium',
                                summary: 'Récupération en cours, 2 semaines d\'arrêt recommandées'
                            },
                            {
                                id: 3,
                                type: 'prevention',
                                date: '2025-01-20',
                                title: 'Programme de prévention',
                                doctor: 'Équipe médicale',
                                status: 'active',
                                riskLevel: 'low',
                                summary: 'Programme d\'étirements et de renforcement musculaire'
                            }
                        ],
                        recommendations: [
                            {
                                id: 1,
                                category: 'nutrition',
                                title: 'Augmenter l\'hydratation',
                                description: 'Boire 500ml d\'eau supplémentaire avant l\'entraînement',
                                priority: 'medium',
                                icon: 'fas fa-tint'
                            },
                            {
                                id: 2,
                                category: 'sleep',
                                title: 'Optimiser le sommeil',
                                description: 'Se coucher 30 minutes plus tôt pour atteindre 8h de sommeil',
                                priority: 'high',
                                icon: 'fas fa-bed'
                            },
                            {
                                id: 3,
                                category: 'recovery',
                                title: 'Séances de récupération',
                                description: 'Ajouter 2 séances de yoga/étirements par semaine',
                                priority: 'medium',
                                icon: 'fas fa-spa'
                            },
                            {
                                id: 4,
                                category: 'mental',
                                title: 'Gestion du stress',
                                description: 'Pratique de méditation 10 minutes par jour',
                                priority: 'low',
                                icon: 'fas fa-brain'
                            }
                        ]
                    },
                    // Données complètes pour le dossier médical
                    medicalData: {
                        healthRecords: [
                            {
                                id: 1,
                                title: 'Consultation Cardiologique',
                                description: 'Bilan cardiaque complet avec ECG et échographie',
                                doctor: 'Dr. Jean Martin',
                                date: '05/03/2025',
                                status: 'completed'
                            },
                            {
                                id: 2,
                                title: 'Examen Orthopédique',
                                description: 'Évaluation genou droit après blessure',
                                doctor: 'Dr. Sophie Moreau',
                                date: '28/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 3,
                                title: 'Bilan Biologique',
                                description: 'Analyses sanguines et urinaires complètes',
                                doctor: 'Dr. Pierre Dubois',
                                date: '20/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 4,
                                title: 'Consultation Nutrition',
                                description: 'Plan alimentaire personnalisé',
                                doctor: 'Dr. Marie Laurent',
                                date: '15/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 5,
                                title: 'Examen Dentaire',
                                description: 'Contrôle et détartrage',
                                doctor: 'Dr. Paul Bernard',
                                date: '10/02/2025',
                                status: 'completed'
                            },
                            {
                                id: 6,
                                title: 'Consultation Kinésithérapie',
                                description: 'Rééducation ischio-jambiers',
                                doctor: 'Sophie Moreau',
                                date: '05/02/2025',
                                status: 'pending'
                            }
                        ],
                        pcmas: [
                            {
                                id: 1,
                                title: 'Évaluation PCMA Saison 2024-2025',
                                description: 'Bilan complet aptitude compétition',
                                assessor: 'Dr. Jean Martin',
                                date: '01/09/2024',
                                fitness: 'fit'
                            },
                            {
                                id: 2,
                                title: 'Contrôle PCMA Mi-saison',
                                description: 'Vérification maintien aptitude',
                                assessor: 'Dr. Sophie Moreau',
                                date: '15/01/2025',
                                fitness: 'fit'
                            },
                            {
                                id: 3,
                                title: 'Évaluation PCMA Post-blessure',
                                description: 'Contrôle après récupération',
                                assessor: 'Dr. Pierre Dubois',
                                date: '01/03/2025',
                                fitness: 'limited'
                            }
                        ],
                        predictions: [
                            {
                                id: 1,
                                title: 'Risque de Blessure',
                                description: 'Analyse prédictive basée sur charge d\'entraînement',
                                date: '08/03/2025',
                                confidence: 87,
                                risk: 'low'
                            },
                            {
                                id: 2,
                                title: 'Performance Prédictive',
                                description: 'Modèle IA pour optimiser la forme physique',
                                date: '05/03/2025',
                                confidence: 92,
                                risk: 'low'
                            }
                        ],
                        alerts: [
                            {
                                id: 1,
                                title: 'Surveillance Récupération',
                                message: 'Attention à la charge d\'entraînement post-blessure',
                                date: '08/03/2025',
                                priority: 'medium'
                            }
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
                                estimatedReturn: '25/03/2025',
                                restrictions: 'Pas de sprint, pas de frappe de balle forte',
                                symptoms: 'Douleur modérée, raideur matinale, limitation amplitude'
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
                                estimatedReturn: '25/01/2025',
                                restrictions: 'Aucune restriction actuelle',
                                symptoms: 'Aucun symptôme résiduel'
                            },
                            {
                                id: 3,
                                type: 'Contusion cuisse',
                                location: 'Cuisse droite',
                                severity: 'mild',
                                date: '28/12/2024',
                                recovery: 100,
                                status: 'recovered',
                                description: 'Contusion suite à un choc avec un adversaire',
                                treatment: 'Glace, anti-inflammatoires, étirements doux',
                                doctor: 'Dr. Pierre Dubois',
                                estimatedReturn: '05/01/2025',
                                restrictions: 'Aucune restriction actuelle',
                                symptoms: 'Aucun symptôme résiduel'
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
                                estimatedReturn: '30/01/2025',
                                restrictions: 'Aucune restriction actuelle',
                                symptoms: 'Aucun symptôme résiduel',
                                complications: 'Aucune'
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
                                estimatedReturn: '10/12/2024',
                                restrictions: 'Aucune restriction actuelle',
                                symptoms: 'Aucun symptôme résiduel',
                                complications: 'Aucune'
                            }
                        ]
                    },
                    // Données complètes pour le dopage
                    dopingData: {
                        lastControl: '2025-02-28',
                        nextControl: '2025-04-15',
                        controlHistory: [
                            {
                                id: 1,
                                type: 'Contrôle inopiné',
                                date: '28/02/2025',
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
                                location: 'Stade de France',
                                status: 'completed',
                                result: 'négatif',
                                substances: 'Aucune substance interdite détectée',
                                notes: 'Contrôle après match France vs Italie'
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
                                riskLevel: 'low',
                                notes: 'Déclaration approuvée par l\'AMA'
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
                                riskLevel: 'none',
                                notes: 'Supplément nutritionnel autorisé'
                            },
                            {
                                id: 3,
                                substance: 'Oméga-3',
                                reason: 'Récupération musculaire',
                                dosage: '1000mg par jour',
                                frequency: '1 fois par jour',
                                doctor: 'Dr. Pierre Dubois',
                                startDate: '01/01/2025',
                                endDate: '31/12/2025',
                                status: 'active',
                                riskLevel: 'none',
                                notes: 'Acides gras essentiels'
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
                            },
                            {
                                id: 2,
                                type: 'info',
                                title: 'Médicaments en vente libre',
                                message: 'Certains médicaments en vente libre peuvent contenir des substances interdites',
                                priority: 'low',
                                date: '05/03/2025'
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
                console.log('FIFA Ultimate Dashboard mounted successfully');
                console.log('Active Tab:', this.activeTab);
                
                // Initialiser les animations et graphiques
                this.initializeAnimations();
                this.$nextTick(() => {
                    this.initializeCharts();
                });
            },
            methods: {
                refreshData() {
                    alert('Actualisation simulée');
                },
                
                initializeAnimations() {
                    // Animer les barres de statistiques FIFA
                    setTimeout(() => {
                        this.fifaStats.forEach((stat, index) => {
                            setTimeout(() => {
                                stat.animated_value = stat.value;
                            }, index * 100);
                        });
                    }, 1000);
                },
                
                initializeCharts() {
                    this.createPerformanceChart();
                    this.createComparisonChart();
                    this.createHealthScoreChart();
                    this.createHealthBreakdownChart();
                },

                createPerformanceChart() {
                    const ctx = document.getElementById('performanceChart');
                    if (!ctx) return;

                    new Chart(ctx, {
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
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
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
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                },

                createHealthScoreChart() {
                    const ctx = document.getElementById('healthScoreChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.healthData.recentMetrics.map(m => {
                                const date = new Date(m.date);
                                return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
                            }),
                            datasets: [{
                                label: 'Score FIT',
                                data: this.healthData.recentMetrics.map(m => m.fitScore),
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
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                },

                createHealthBreakdownChart() {
                    const ctx = document.getElementById('healthBreakdownChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Santé Physique', 'Santé Mentale', 'Sommeil', 'Social'],
                            datasets: [{
                                data: [
                                    this.healthData.physicalScore,
                                    this.healthData.mentalScore,
                                    this.healthData.sleepScore,
                                    this.healthData.socialScore
                                ],
                                backgroundColor: ['#ef4444', '#8b5cf6', '#6366f1', '#10b981'],
                                borderWidth: 2,
                                borderColor: '#fff'
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
                }
            }
        }).mount('#fifa-app');

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
            
            // Simulation d'envoi
            alert(`Dossier médical envoyé à ${email} avec le message : "${message || 'Aucun message'}"`);
            closeShareModalSimple();
            
            // Réinitialiser les champs
            document.getElementById('shareEmail').value = '';
            document.getElementById('shareMessage').value = '';
        }

        function printMedicalRecord() {
            // Créer une vue d'impression
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Dossier Médical - @{{ playerData.identity.first_name }} @{{ playerData.identity.last_name }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                        .section { margin-bottom: 30px; }
                        .section h3 { color: #2563eb; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
                        .record { margin-bottom: 15px; padding: 10px; border-left: 4px solid #3b82f6; background: #f8fafc; }
                        .record h4 { margin: 0 0 5px 0; color: #1e40af; }
                        .record p { margin: 5px 0; color: #374151; }
                        .record .meta { font-size: 12px; color: #6b7280; }
                        .status { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
                        .status.completed { background: #dcfce7; color: #166534; }
                        .status.pending { background: #fef3c7; color: #92400e; }
                        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #6b7280; }
                        @media print { .no-print { display: none; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>DOSSIER MÉDICAL COMPLET</h1>
                        <h2>@{{ playerData.identity.first_name }} @{{ playerData.identity.last_name }}</h2>
                        <p>Date d'impression : ${new Date().toLocaleDateString('fr-FR')}</p>
                    </div>

                    <div class="section">
                        <h3>📋 Dossiers de Santé (6 dossiers)</h3>
                        <div class="record">
                            <h4>Consultation Cardiologique</h4>
                            <p>Bilan cardiaque complet avec ECG et échographie</p>
                            <div class="meta">Dr. Jean Martin • 05/03/2025</div>
                            <span class="status completed">Terminé</span>
                        </div>
                        <div class="record">
                            <h4>Examen Orthopédique</h4>
                            <p>Évaluation genou droit après blessure</p>
                            <div class="meta">Dr. Sophie Moreau • 28/02/2025</div>
                            <span class="status completed">Terminé</span>
                        </div>
                        <div class="record">
                            <h4>Bilan Biologique</h4>
                            <p>Analyses sanguines et urinaires complètes</p>
                            <div class="meta">Dr. Pierre Dubois • 20/02/2025</div>
                            <span class="status completed">Terminé</span>
                        </div>
                        <div class="record">
                            <h4>Consultation Nutrition</h4>
                            <p>Plan alimentaire personnalisé</p>
                            <div class="meta">Dr. Marie Laurent • 15/02/2025</div>
                            <span class="status completed">Terminé</span>
                        </div>
                        <div class="record">
                            <h4>Examen Dentaire</h4>
                            <p>Contrôle et détartrage</p>
                            <div class="meta">Dr. Paul Bernard • 10/02/2025</div>
                            <span class="status completed">Terminé</span>
                        </div>
                        <div class="record">
                            <h4>Consultation Kinésithérapie</h4>
                            <p>Rééducation ischio-jambiers</p>
                            <div class="meta">Sophie Moreau • 05/02/2025</div>
                            <span class="status pending">En attente</span>
                        </div>
                    </div>

                    <div class="section">
                        <h3>✅ Évaluations PCMA (3 évaluations)</h3>
                        <div class="record">
                            <h4>Évaluation PCMA Saison 2024-2025</h4>
                            <p>Bilan complet aptitude compétition</p>
                            <div class="meta">Dr. Jean Martin • 01/09/2024</div>
                            <span class="status completed">APT</span>
                        </div>
                        <div class="record">
                            <h4>Contrôle PCMA Mi-saison</h4>
                            <p>Vérification maintien aptitude</p>
                            <div class="meta">Dr. Sophie Moreau • 15/01/2025</div>
                            <span class="status completed">APT</span>
                        </div>
                        <div class="record">
                            <h4>Évaluation PCMA Post-blessure</h4>
                            <p>Contrôle après récupération</p>
                            <div class="meta">Dr. Pierre Dubois • 01/03/2025</div>
                            <span class="status pending">LIMITÉ</span>
                        </div>
                    </div>

                    <div class="section">
                        <h3>🤖 Prédictions IA (2 prédictions)</h3>
                        <div class="record">
                            <h4>Risque de Blessure</h4>
                            <p>Analyse prédictive basée sur charge d'entraînement</p>
                            <div class="meta">08/03/2025 • 87% confiance</div>
                            <span class="status completed">FAIBLE</span>
                        </div>
                        <div class="record">
                            <h4>Performance Prédictive</h4>
                            <p>Modèle IA pour optimiser la forme physique</p>
                            <div class="meta">05/03/2025 • 92% confiance</div>
                            <span class="status completed">FAIBLE</span>
                        </div>
                    </div>

                    <div class="section">
                        <h3>⚠️ Alertes Médicales (1 alerte)</h3>
                        <div class="record">
                            <h4>Surveillance Récupération</h4>
                            <p>Attention à la charge d'entraînement post-blessure</p>
                            <div class="meta">08/03/2025 • IMPORTANT</div>
                            <span class="status pending">IMPORTANT</span>
                        </div>
                    </div>

                    <div class="footer">
                        <p>Document généré automatiquement par le système FIT</p>
                        <p>Confidentiel - Usage médical uniquement</p>
                    </div>

                    <div class="no-print" style="margin-top: 30px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Imprimer
                        </button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                            Fermer
                        </button>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        function exportMedicalPDF() {
            // Simulation d'export PDF
            alert('Fonctionnalité d\'export PDF en cours de développement. Pour l\'instant, utilisez la fonction d\'impression et sauvegardez en PDF depuis votre navigateur.');
            
            // Alternative : ouvrir la vue d'impression
            setTimeout(() => {
                printMedicalRecord();
            }, 1000);
        }
    </script>
</body>
</html>
