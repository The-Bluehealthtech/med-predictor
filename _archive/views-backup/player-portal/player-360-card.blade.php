<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }} - Fiche Joueur 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .section-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .gradient-text {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .health-score-excellent { color: #10B981; }
        .health-score-good { color: #F59E0B; }
        .health-score-warning { color: #EF4444; }
        
        .tab-button {
            transition: all 0.3s ease;
            border-radius: 12px 12px 0 0;
        }
        .tab-button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }
        .tab-content {
            min-height: 400px;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 30px rgba(102, 126, 234, 0.7); }
        }
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- En-tête avec bouton retour -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('player-portal.dashboard') }}" class="flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au Dashboard
            </a>
            <div class="flex items-center space-x-4">
                <button @click="refreshData" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-sync-alt mr-2" :class="{'animate-spin': isLoading}"></i>
                    Actualiser
                </button>
                <div class="text-sm text-gray-500">
                    Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Carte principal du joueur -->
        <div class="player-card rounded-3xl p-8 mb-8 text-white relative overflow-hidden">
            <!-- Motif de fond -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white transform translate-x-32 -translate-y-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-white transform -translate-x-16 translate-y-16"></div>
            </div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-4 gap-8 items-center">
                <!-- Photo et infos principales -->
                <div class="lg:col-span-1 text-center">
                    <div class="relative inline-block">
                        <img src="{{ Auth::user()->player->player_picture ?? '/images/default-player.png' }}" 
                             alt="Photo de {{ Auth::user()->player->first_name }}"
                             class="w-32 h-32 rounded-full border-4 border-white shadow-2xl mx-auto pulse-glow">
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold mt-4 mb-2">
                        {{ Auth::user()->player->first_name }}<br>
                        {{ Auth::user()->player->last_name }}
                    </h1>
                    <p class="text-xl opacity-90">{{ Auth::user()->player->position ?? 'Position non définie' }}</p>
                    <div class="mt-4 flex justify-center items-center space-x-4">
                        @if(Auth::user()->player->nation_flag_url)
                            <img src="{{ Auth::user()->player->nation_flag_url }}" alt="Drapeau" class="w-8 h-6 rounded">
                        @endif
                        <span class="text-lg">{{ Auth::user()->player->nationality ?? 'Nationalité non définie' }}</span>
                    </div>
                </div>

                <!-- Statistiques principales -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="stat-card rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold">{{ Auth::user()->player->age ?? 'N/A' }}</div>
                            <div class="text-sm opacity-75">Âge</div>
                        </div>
                        <div class="stat-card rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold">{{ Auth::user()->player->height ?? 'N/A' }}</div>
                            <div class="text-sm opacity-75">Taille (cm)</div>
                        </div>
                        <div class="stat-card rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold">{{ Auth::user()->player->weight ?? 'N/A' }}</div>
                            <div class="text-sm opacity-75">Poids (kg)</div>
                        </div>
                        <div class="stat-card rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold">{{ Auth::user()->player->overall_rating ?? 'N/A' }}</div>
                            <div class="text-sm opacity-75">Note FIFA</div>
                        </div>
                    </div>

                    <!-- Valeur marchande et salaire -->
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="stat-card rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-bold">€{{ number_format(Auth::user()->player->value_eur ?? 0) }}</div>
                                    <div class="text-sm opacity-75">Valeur marchande</div>
                                </div>
                                <i class="fas fa-chart-line text-2xl opacity-75"></i>
                            </div>
                        </div>
                        <div class="stat-card rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-bold">€{{ number_format(Auth::user()->player->wage_eur ?? 0) }}</div>
                                    <div class="text-sm opacity-75">Salaire</div>
                                </div>
                                <i class="fas fa-euro-sign text-2xl opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score de santé FIT -->
                <div class="lg:col-span-1">
                    <div class="stat-card rounded-xl p-6 text-center">
                        <div class="text-sm opacity-75 mb-2">Score de Santé FIT</div>
                        <div class="relative">
                            <canvas ref="healthScoreChart" width="120" height="120"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-bold health-score-excellent">
                                        {{ Auth::user()->player->ghs_overall_score ?? 85 }}
                                    </div>
                                    <div class="text-xs opacity-75">/ 100</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-sm">
                            <span class="px-3 py-1 bg-green-500 rounded-full text-xs">EXCELLENT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets de navigation -->
        <div class="mb-6">
            <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-t-2xl overflow-hidden">
                <button v-for="tab in tabs" :key="tab.id" 
                        @click="activeTab = tab.id"
                        :class="['tab-button px-6 py-4 font-medium transition-all duration-300', activeTab === tab.id ? 'active' : 'text-gray-500 hover:text-gray-700']">
                    <i :class="tab.icon + ' mr-2'"></i>
                    {{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Contenu des onglets -->
        <div class="section-card rounded-2xl shadow-lg">
            <!-- Onglet Identité & Profil -->
            <div v-show="activeTab === 'identity'" class="tab-content p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Identité & Profil</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Informations personnelles -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                Informations Personnelles
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Nom complet</span>
                                    <span class="font-medium">{{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Date de naissance</span>
                                    <span class="font-medium">{{ Auth::user()->player->date_of_birth ? Auth::user()->player->date_of_birth->format('d/m/Y') : 'Non définie' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Nationalité</span>
                                    <span class="font-medium">{{ Auth::user()->player->nationality ?? 'Non définie' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">FIFA Connect ID</span>
                                    <span class="font-medium font-mono text-sm">{{ Auth::user()->player->fifa_connect_id ?? 'Non attribué' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations physiques -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-dumbbell mr-2 text-green-500"></i>
                                Caractéristiques Physiques
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Taille</span>
                                    <span class="font-medium">{{ Auth::user()->player->height ?? 'N/A' }} cm</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Poids</span>
                                    <span class="font-medium">{{ Auth::user()->player->weight ?? 'N/A' }} kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Pied préféré</span>
                                    <span class="font-medium">{{ Auth::user()->player->preferred_foot ?? 'Non défini' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Pied faible</span>
                                    <span class="font-medium">{{ Auth::user()->player->weak_foot ?? 'N/A' }}/5</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Club actuel et contrat -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-shield-alt mr-2 text-purple-500"></i>
                                Club Actuel
                            </h3>
                            @if(Auth::user()->player->club)
                            <div class="flex items-center space-x-4 mb-4">
                                @if(Auth::user()->player->club->logo_url)
                                    <img src="{{ Auth::user()->player->club->logo_url }}" alt="Logo club" class="w-16 h-16 object-contain">
                                @endif
                                <div>
                                    <div class="font-medium text-lg">{{ Auth::user()->player->club->name }}</div>
                                    <div class="text-gray-600">{{ Auth::user()->player->club->association->name ?? 'Fédération non définie' }}</div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Contrat jusqu'au</span>
                                    <span class="font-medium">{{ Auth::user()->player->contract_valid_until ? Auth::user()->player->contract_valid_until->format('d/m/Y') : 'Non défini' }}</span>
                                </div>
                                @if(Auth::user()->player->release_clause_eur)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Clause libératoire</span>
                                    <span class="font-medium">€{{ number_format(Auth::user()->player->release_clause_eur) }}</span>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-info-circle mb-2"></i>
                                <p>Aucun club associé</p>
                            </div>
                            @endif
                        </div>

                        <!-- Évaluations FIFA -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                Évaluations FIFA
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Note générale</span>
                                    <div class="flex items-center">
                                        <span class="font-bold text-2xl text-blue-600">{{ Auth::user()->player->overall_rating ?? 'N/A' }}</span>
                                        <span class="text-gray-500 ml-1">/100</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Potentiel</span>
                                    <div class="flex items-center">
                                        <span class="font-bold text-2xl text-green-600">{{ Auth::user()->player->potential_rating ?? 'N/A' }}</span>
                                        <span class="text-gray-500 ml-1">/100</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Gestes techniques</span>
                                    <span class="font-medium">{{ Auth::user()->player->skill_moves ?? 'N/A' }}/5</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Réputation internationale</span>
                                    <span class="font-medium">{{ Auth::user()->player->international_reputation ?? 'N/A' }}/5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Licences & Clubs -->
            <div v-show="activeTab === 'licenses'" class="tab-content p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Licences & Historique des Clubs</h2>
                <div class="space-y-6">
                    <!-- Licences actives -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-id-card mr-2 text-blue-500"></i>
                            Licences Actives
                        </h3>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-info-circle text-3xl mb-2"></i>
                            <p>Données de licences en cours de chargement...</p>
                            <p class="text-sm mt-2">Cette section sera mise à jour avec les données réelles des licences</p>
                        </div>
                    </div>

                    <!-- Historique des clubs -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-history mr-2 text-purple-500"></i>
                            Historique des Clubs
                        </h3>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clock text-3xl mb-2"></i>
                            <p>Historique des transferts en cours de chargement...</p>
                            <p class="text-sm mt-2">Cette section affichera l'historique complet des clubs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Performances -->
            <div v-show="activeTab === 'performance'" class="tab-content p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Performances Sportives</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Statistiques physiques -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-running mr-2 text-red-500"></i>
                            Performance Physique
                        </h3>
                        <div class="space-y-4">
                            @if(Auth::user()->player->performances->count() > 0)
                                @php $latestPerf = Auth::user()->player->performances->first() @endphp
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $latestPerf->endurance_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Endurance</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $latestPerf->strength_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Force</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ $latestPerf->speed_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Vitesse</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-orange-600">{{ $latestPerf->agility_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Agilité</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-chart-bar text-3xl mb-2"></i>
                                    <p>Aucune donnée de performance disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Statistiques techniques -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-cogs mr-2 text-blue-500"></i>
                            Performance Technique
                        </h3>
                        <div class="space-y-4">
                            @if(Auth::user()->player->performances->count() > 0)
                                @php $latestPerf = Auth::user()->player->performances->first() @endphp
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $latestPerf->technical_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Technique</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $latestPerf->tactical_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Tactique</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ $latestPerf->mental_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Mental</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-orange-600">{{ $latestPerf->social_score ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Social</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                                    <p>Aucune donnée technique disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Évolution des performances -->
                <div class="mt-6 bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Évolution des Performances
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas ref="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Onglet Médical -->
            <div v-show="activeTab === 'medical'" class="tab-content p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Dossier Médical & Bien-être</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Score de santé détaillé -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-red-500"></i>
                            Score de Santé FIT
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Physique</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-lg">{{ Auth::user()->player->ghs_physical_score ?? 85 }}</span>
                                    <div class="w-20 h-2 bg-gray-200 rounded-full ml-2">
                                        <div class="h-2 bg-green-500 rounded-full" style="width: {{ Auth::user()->player->ghs_physical_score ?? 85 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Mental</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-lg">{{ Auth::user()->player->ghs_mental_score ?? 78 }}</span>
                                    <div class="w-20 h-2 bg-gray-200 rounded-full ml-2">
                                        <div class="h-2 bg-blue-500 rounded-full" style="width: {{ Auth::user()->player->ghs_mental_score ?? 78 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Civique</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-lg">{{ Auth::user()->player->ghs_civic_score ?? 92 }}</span>
                                    <div class="w-20 h-2 bg-gray-200 rounded-full ml-2">
                                        <div class="h-2 bg-purple-500 rounded-full" style="width: {{ Auth::user()->player->ghs_civic_score ?? 92 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sommeil</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-lg">{{ Auth::user()->player->ghs_sleep_score ?? 81 }}</span>
                                    <div class="w-20 h-2 bg-gray-200 rounded-full ml-2">
                                        <div class="h-2 bg-indigo-500 rounded-full" style="width: {{ Auth::user()->player->ghs_sleep_score ?? 81 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Risque de blessure -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                            Risque de Blessure
                        </h3>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-green-600 mb-2">{{ Auth::user()->player->injury_risk_score ?? 15 }}%</div>
                            <div class="text-sm text-gray-600 mb-4">{{ Auth::user()->player->injury_risk_level ?? 'FAIBLE' }}</div>
                            <div class="w-full h-3 bg-gray-200 rounded-full">
                                <div class="h-3 bg-green-500 rounded-full" style="width: {{ 100 - (Auth::user()->player->injury_risk_score ?? 15) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ Auth::user()->player->injury_risk_reason ?? 'Excellent état de forme général' }}</p>
                        </div>
                    </div>

                    <!-- Disponibilité -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                            Disponibilité
                        </h3>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 mb-2">
                                {{ Auth::user()->player->match_availability ?? 'DISPONIBLE' }}
                            </div>
                            <div class="text-sm text-gray-600 mb-4">Pour le prochain match</div>
                            <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                <i class="fas fa-circle text-green-500 mr-2"></i>
                                Apte à jouer
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique médical récent -->
                <div class="mt-6 bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-blue-500"></i>
                        Historique Médical Récent
                    </h3>
                    @if(Auth::user()->player->healthRecords->count() > 0)
                        <div class="space-y-3">
                            @foreach(Auth::user()->player->healthRecords->take(3) as $record)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                <div>
                                    <div class="font-medium">{{ $record->visit_type ?? 'Consultation' }}</div>
                                    <div class="text-sm text-gray-600">{{ $record->record_date ? $record->record_date->format('d/m/Y') : 'Date non définie' }}</div>
                                </div>
                                @if($record->risk_score)
                                    <span class="px-2 py-1 text-xs rounded-full {{ $record->risk_score > 0.7 ? 'bg-red-100 text-red-800' : ($record->risk_score > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ number_format($record->risk_score * 100) }}% risque
                                    </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-stethoscope text-3xl mb-2"></i>
                            <p>Aucun dossier médical récent</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Onglet Prédictions -->
            <div v-show="activeTab === 'predictions'" class="tab-content p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Prédictions FIT & Analyses Prédictives</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Prédictions de performance -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-crystal-ball mr-2 text-purple-500"></i>
                            Prédictions de Performance
                        </h3>
                        @if(Auth::user()->player->medicalPredictions->count() > 0)
                            <div class="space-y-4">
                                @foreach(Auth::user()->player->medicalPredictions->take(3) as $prediction)
                                <div class="p-4 bg-white rounded-lg border-l-4 border-purple-500">
                                    <div class="font-medium">{{ ucfirst($prediction->prediction_type ?? 'Prédiction') }}</div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $prediction->predicted_condition ?? 'Condition prédite non définie' }}</div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-gray-500">Confiance: {{ number_format(($prediction->confidence_score ?? 0.8) * 100) }}%</span>
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                            {{ number_format(($prediction->risk_probability ?? 0.2) * 100) }}% risque
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-magic text-3xl mb-2"></i>
                                <p>Aucune prédiction disponible</p>
                                <p class="text-sm mt-2">Les prédictions seront générées avec plus de données</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recommandations IA -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-robot mr-2 text-blue-500"></i>
                            Suggestions IA
                        </h3>
                        <div class="space-y-3">
                            @php
                                $suggestions = json_decode(Auth::user()->player->ghs_ai_suggestions ?? '[]', true);
                                if (empty($suggestions)) {
                                    $suggestions = [
                                        'Maintenir un excellent niveau d\'hydratation',
                                        'Continuer les exercices de prévention',
                                        'Surveiller la charge d\'entraînement'
                                    ];
                                }
                            @endphp
                            @foreach($suggestions as $suggestion)
                            <div class="flex items-start p-3 bg-white rounded-lg">
                                <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                                <p class="text-sm">{{ $suggestion }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Graphique d'évolution prédictive -->
                <div class="mt-6 bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-area mr-2 text-green-500"></i>
                        Évolution Prédictive (6 mois)
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas ref="predictionChart"></canvas>
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
                    activeTab: 'identity',
                    isLoading: false,
                    tabs: [
                        { id: 'identity', name: 'Identité & Profil', icon: 'fas fa-user' },
                        { id: 'licenses', name: 'Licences & Clubs', icon: 'fas fa-id-card' },
                        { id: 'performance', name: 'Performances', icon: 'fas fa-chart-line' },
                        { id: 'medical', name: 'Médical & Bien-être', icon: 'fas fa-heartbeat' },
                        { id: 'predictions', name: 'Prédictions FIT', icon: 'fas fa-crystal-ball' }
                    ]
                }
            },
            mounted() {
                this.initializeCharts();
            },
            methods: {
                refreshData() {
                    this.isLoading = true;
                    // Simuler un appel API
                    setTimeout(() => {
                        this.isLoading = false;
                        // Ici on ferait un vrai appel API pour rafraîchir les données
                    }, 2000);
                },
                initializeCharts() {
                    // Chart.js pour le score de santé
                    const healthCtx = this.$refs.healthScoreChart?.getContext('2d');
                    if (healthCtx) {
                        new Chart(healthCtx, {
                            type: 'doughnut',
                            data: {
                                datasets: [{
                                    data: [{{ Auth::user()->player->ghs_overall_score ?? 85 }}, {{ 100 - (Auth::user()->player->ghs_overall_score ?? 85) }}],
                                    backgroundColor: ['#10B981', '#E5E7EB'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }

                    // Chart.js pour l'évolution des performances
                    const perfCtx = this.$refs.performanceChart?.getContext('2d');
                    if (perfCtx) {
                        new Chart(perfCtx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                                datasets: [
                                    {
                                        label: 'Performance Physique',
                                        data: [75, 78, 82, 85, 87, 89],
                                        borderColor: '#3B82F6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Performance Technique',
                                        data: [70, 72, 75, 78, 82, 85],
                                        borderColor: '#10B981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        tension: 0.4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'top' }
                                },
                                scales: {
                                    y: { beginAtZero: true, max: 100 }
                                }
                            }
                        });
                    }

                    // Chart.js pour les prédictions
                    const predCtx = this.$refs.predictionChart?.getContext('2d');
                    if (predCtx) {
                        new Chart(predCtx, {
                            type: 'line',
                            data: {
                                labels: ['Maintenant', '+1 mois', '+2 mois', '+3 mois', '+4 mois', '+5 mois', '+6 mois'],
                                datasets: [
                                    {
                                        label: 'Performance Prédite',
                                        data: [85, 87, 89, 91, 92, 93, 94],
                                        borderColor: '#8B5CF6',
                                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                        tension: 0.4,
                                        borderDash: [5, 5]
                                    },
                                    {
                                        label: 'Risque de Blessure',
                                        data: [15, 12, 10, 8, 8, 9, 10],
                                        borderColor: '#EF4444',
                                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                        tension: 0.4,
                                        borderDash: [5, 5]
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'top' }
                                },
                                scales: {
                                    y: { beginAtZero: true, max: 100 }
                                }
                            }
                        });
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>


















