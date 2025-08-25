<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Auth::user()->player->first_name ?? 'Joueur' }} {{ Auth::user()->player->last_name ?? '' }} - Fiche Joueur 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* FIFA Card Styles */
        .fifa-card {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 50%, #5c6bc0 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            transition: all 0.3s ease;
        }
        
        .fifa-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 35px 60px rgba(0,0,0,0.5);
        }

        .fifa-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(255,255,255,0.1) 0%, 
                rgba(255,255,255,0.05) 50%, 
                rgba(255,255,255,0.1) 100%);
            z-index: 1;
        }

        .fifa-card-content {
            position: relative;
            z-index: 2;
        }

        .fifa-rating {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .fifa-stat-bar {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            height: 8px;
        }

        .fifa-stat-fill {
            background: linear-gradient(90deg, #4caf50 0%, #8bc34a 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        .health-score-arc {
            stroke-dasharray: 251.2; /* 2 * π * 40 */
            stroke-dashoffset: 251.2;
            transition: stroke-dashoffset 2s ease-in-out;
        }

        .player-photo {
            filter: drop-shadow(0 10px 25px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }

        .fifa-card:hover .player-photo {
            transform: scale(1.05);
        }

        .club-logo {
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .stat-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .section-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .section-card:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        .gradient-text {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tab-button {
            transition: all 0.3s ease;
            border-radius: 12px 12px 0 0;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }

        .medical-alert {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .floating-element {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 via-blue-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Navigation -->
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
                    {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        @if(Auth::user() && Auth::user()->player)
        <!-- Hero Zone - FIFA Ultimate Team Card -->
        <div class="mb-8">
            <div class="fifa-card p-8 text-white">
                <div class="fifa-card-content">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <!-- Section Photo & Rating (4 colonnes) -->
                        <div class="lg:col-span-4">
                            <div class="flex flex-col items-center lg:items-start">
                                <!-- Photo du joueur -->
                                <div class="relative mb-6">
                                    <img src="{{ Auth::user()->player->player_picture ?? 'https://via.placeholder.com/200x250/4285f4/ffffff?text=' . substr(Auth::user()->player->first_name ?? 'J', 0, 1) . substr(Auth::user()->player->last_name ?? 'P', 0, 1) }}" 
                                         alt="Photo de {{ Auth::user()->player->first_name }}"
                                         class="player-photo w-48 h-60 object-cover rounded-2xl border-4 border-white/30">
                                    
                                    <!-- Rating FIFA en overlay -->
                                    <div class="absolute -top-4 -right-4 fifa-rating w-20 h-20 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-2xl font-black text-black">{{ Auth::user()->player->overall_rating ?? 75 }}</div>
                                            <div class="text-xs font-bold text-black">OVR</div>
                                        </div>
                                    </div>

                                    <!-- Position en overlay -->
                                    <div class="absolute -bottom-4 -left-4 bg-white/90 text-gray-800 px-4 py-2 rounded-xl font-bold">
                                        {{ Auth::user()->player->position ?? 'CAM' }}
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
                                        {{ strtoupper(Auth::user()->player->last_name ?? 'PLAYER') }}
                                    </h1>
                                    <h2 class="text-2xl lg:text-3xl font-bold text-white/90 mb-4">
                                        {{ Auth::user()->player->first_name ?? 'First' }}
                                    </h2>
                                    
                                    <!-- Club et nationalité -->
                                    <div class="flex items-center space-x-4 mb-6">
                                        @if(Auth::user()->player->club ?? false)
                                            <div class="flex items-center space-x-2">
                                                <img src="{{ Auth::user()->player->club->logo_url ?? 'https://via.placeholder.com/40x40/ffffff/4285f4?text=FC' }}" 
                                                     alt="Logo club" class="club-logo w-10 h-10 object-contain">
                                                <span class="font-semibold">{{ Auth::user()->player->club->name ?? 'Free Agent' }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center space-x-2">
                                            <img src="{{ Auth::user()->player->nation_flag_url ?? 'https://via.placeholder.com/32x24/ffffff/4285f4?text=FR' }}" 
                                                 alt="Drapeau" class="w-8 h-6 rounded">
                                            <span class="font-semibold">{{ Auth::user()->player->nationality ?? 'International' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistiques FIFA -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold">{{ Auth::user()->player->age ?? 25 }}</div>
                                        <div class="text-sm opacity-75">ÂGE</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold">{{ Auth::user()->player->height ?? 180 }}</div>
                                        <div class="text-sm opacity-75">TAILLE</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold">{{ Auth::user()->player->weak_foot ?? 3 }}<span class="text-lg">★</span></div>
                                        <div class="text-sm opacity-75">PIED FAIBLE</div>
                                    </div>
                                </div>

                                <!-- Barres de statistiques FIFA -->
                                <div class="space-y-3">
                                    @php
                                        $latestPerf = Auth::user()->player->performances->first();
                                        $stats = [
                                            ['name' => 'VIT', 'value' => $latestPerf ? $latestPerf->speed_score ?? 85 : 85],
                                            ['name' => 'TIR', 'value' => $latestPerf ? $latestPerf->technical_score ?? 78 : 78],
                                            ['name' => 'PAS', 'value' => $latestPerf ? $latestPerf->tactical_score ?? 82 : 82],
                                            ['name' => 'DRI', 'value' => Auth::user()->player->skill_moves ? Auth::user()->player->skill_moves * 20 : 75],
                                            ['name' => 'DEF', 'value' => 65],
                                            ['name' => 'PHY', 'value' => $latestPerf ? $latestPerf->strength_score ?? 80 : 80]
                                        ];
                                    @endphp
                                    
                                    @foreach($stats as $stat)
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-sm w-8">{{ $stat['name'] }}</span>
                                        <div class="flex-1 mx-3">
                                            <div class="fifa-stat-bar">
                                                <div class="fifa-stat-fill" style="width: {{ $stat['value'] }}%"></div>
                                            </div>
                                        </div>
                                        <span class="font-bold text-sm w-8 text-right">{{ $stat['value'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Section Score de Santé FIT (3 colonnes) -->
                        <div class="lg:col-span-3">
                            <div class="text-center space-y-6">
                                <!-- Score de santé FIT avec arc -->
                                <div class="relative">
                                    <svg class="w-32 h-32 mx-auto" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="8"/>
                                        <circle cx="50" cy="50" r="40" fill="none" stroke="#4ade80" stroke-width="8" 
                                                class="health-score-arc" 
                                                style="stroke-dashoffset: {{ 251.2 - (251.2 * (Auth::user()->player->ghs_overall_score ?? 85) / 100) }}; transform: rotate(-90deg); transform-origin: 50% 50%;" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-3xl font-black">{{ Auth::user()->player->ghs_overall_score ?? 85 }}</div>
                                            <div class="text-xs font-bold opacity-75">FIT</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Indicateurs de santé -->
                                <div class="space-y-2">
                                    <div class="text-sm font-semibold">
                                        💚 Santé: {{ Auth::user()->player->ghs_physical_score ?? 85 }}/100
                                    </div>
                                    <div class="text-sm font-semibold">
                                        🧠 Mental: {{ Auth::user()->player->ghs_mental_score ?? 78 }}/100
                                    </div>
                                    <div class="text-sm font-semibold">
                                        😴 Sommeil: {{ Auth::user()->player->ghs_sleep_score ?? 81 }}/100
                                    </div>
                                </div>

                                <!-- Risque de blessure -->
                                <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                                    <div class="text-xs font-bold opacity-75 mb-1">RISQUE BLESSURE</div>
                                    <div class="text-2xl font-black text-green-400">{{ Auth::user()->player->injury_risk_score ?? 15 }}%</div>
                                    <div class="text-xs font-semibold text-green-400">{{ Auth::user()->player->injury_risk_level ?? 'FAIBLE' }}</div>
                                </div>

                                <!-- Valeur marchande -->
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-4 text-black">
                                    <div class="text-xs font-bold mb-1">VALEUR ESTIMÉE</div>
                                    <div class="text-lg font-black">€{{ number_format((Auth::user()->player->value_eur ?? 1500000) / 1000000, 1) }}M</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets de navigation -->
        <div class="mb-6">
            <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-t-2xl overflow-hidden shadow-lg">
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
            <!-- Onglet Données Médicales -->
            <div v-show="activeTab === 'medical'" class="p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Données Médicales Complètes</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Dossiers médicaux récents -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-file-medical mr-2 text-red-500"></i>
                            Dossiers Médicaux
                        </h3>
                        @if(Auth::user()->player->healthRecords->count() > 0)
                            <div class="space-y-3">
                                @foreach(Auth::user()->player->healthRecords->take(5) as $record)
                                <div class="p-3 bg-white rounded-lg border-l-4 border-red-500">
                                    <div class="font-medium text-sm">{{ $record->visit_type ?? 'Consultation' }}</div>
                                    <div class="text-xs text-gray-600">{{ $record->record_date ? $record->record_date->format('d/m/Y') : 'Date non définie' }}</div>
                                    @if($record->diagnosis)
                                        <div class="text-xs text-gray-700 mt-1">{{ Str::limit($record->diagnosis, 50) }}</div>
                                    @endif
                                    @if($record->risk_score)
                                        <div class="mt-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $record->risk_score > 0.7 ? 'bg-red-100 text-red-800' : ($record->risk_score > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ number_format($record->risk_score * 100) }}% risque
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-stethoscope text-3xl mb-2"></i>
                                <p>Aucun dossier médical</p>
                            </div>
                        @endif
                    </div>

                    <!-- PCMA -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-blue-500"></i>
                            Évaluations PCMA
                        </h3>
                        @if(Auth::user()->player->pcmas->count() > 0)
                            <div class="space-y-3">
                                @foreach(Auth::user()->player->pcmas->take(3) as $pcma)
                                <div class="p-3 bg-white rounded-lg border-l-4 border-blue-500">
                                    <div class="font-medium text-sm">PCMA {{ $pcma->type ?? 'Standard' }}</div>
                                    <div class="text-xs text-gray-600">{{ $pcma->assessment_date ? $pcma->assessment_date->format('d/m/Y') : 'Date non définie' }}</div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($pcma->status ?? 'En cours') }}
                                        </span>
                                        @if($pcma->fifa_compliant)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">FIFA ✓</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-clipboard-check text-3xl mb-2"></i>
                                <p>Aucune évaluation PCMA</p>
                            </div>
                        @endif
                    </div>

                    <!-- Prédictions médicales -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-crystal-ball mr-2 text-purple-500"></i>
                            Prédictions IA
                        </h3>
                        @if(Auth::user()->player->medicalPredictions->count() > 0)
                            <div class="space-y-3">
                                @foreach(Auth::user()->player->medicalPredictions->take(3) as $prediction)
                                <div class="p-3 bg-white rounded-lg border-l-4 border-purple-500">
                                    <div class="font-medium text-sm">{{ ucfirst($prediction->prediction_type ?? 'Prédiction') }}</div>
                                    <div class="text-xs text-gray-600">{{ $prediction->prediction_date ? $prediction->prediction_date->format('d/m/Y') : 'Date non définie' }}</div>
                                    @if($prediction->predicted_condition)
                                        <div class="text-xs text-gray-700 mt-1">{{ Str::limit($prediction->predicted_condition, 40) }}</div>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                            {{ number_format(($prediction->confidence_score ?? 0.8) * 100) }}% confiance
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full {{ ($prediction->risk_probability ?? 0.2) > 0.5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ number_format(($prediction->risk_probability ?? 0.2) * 100) }}% risque
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-magic text-3xl mb-2"></i>
                                <p>Aucune prédiction</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Onglet Statistiques & Saisons -->
            <div v-show="activeTab === 'stats'" class="p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Statistiques & Résultats par Saison</h2>
                <div class="space-y-6">
                    <!-- Performances actuelles -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-chart-line mr-2 text-green-500"></i>
                            Performances Actuelles
                        </h3>
                        @if(Auth::user()->player->performances->count() > 0)
                            @php $latestPerf = Auth::user()->player->performances->first() @endphp
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $latestPerf->endurance_score ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Endurance</div>
                                </div>
                                <div class="text-center p-4 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $latestPerf->strength_score ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Force</div>
                                </div>
                                <div class="text-center p-4 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $latestPerf->speed_score ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Vitesse</div>
                                </div>
                                <div class="text-center p-4 bg-white rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600">{{ $latestPerf->technical_score ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-600">Technique</div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-chart-bar text-3xl mb-2"></i>
                                <p>Aucune donnée de performance disponible</p>
                            </div>
                        @endif
                    </div>

                    <!-- Évolution par saison (placeholder) -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Évolution par Saison
                        </h3>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-trophy text-3xl mb-2"></i>
                            <p>Données saisonnières en cours d'intégration</p>
                            <p class="text-sm mt-2">Cette section affichera l'historique complet des saisons</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Licences & Transferts -->
            <div v-show="activeTab === 'licenses'" class="p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Licences & Transferts</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Licences -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-id-card mr-2 text-blue-500"></i>
                            État des Licences
                        </h3>
                        <div class="space-y-3">
                            <!-- Licence actuelle (simulée) -->
                            <div class="p-4 bg-white rounded-lg border-l-4 border-green-500">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium">Licence Professionnelle</div>
                                        <div class="text-sm text-gray-600">{{ Auth::user()->player->club->name ?? 'Club actuel' }}</div>
                                        <div class="text-xs text-gray-500">Valide jusqu'au {{ Auth::user()->player->contract_valid_until ? Auth::user()->player->contract_valid_until->format('d/m/Y') : '30/06/2025' }}</div>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">ACTIVE</span>
                                </div>
                            </div>
                            
                            <!-- Licence internationale (simulée) -->
                            <div class="p-4 bg-white rounded-lg border-l-4 border-blue-500">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium">Clearance Internationale</div>
                                        <div class="text-sm text-gray-600">FIFA Connect</div>
                                        <div class="text-xs text-gray-500">ID: {{ Auth::user()->player->fifa_connect_id ?? 'FC-' . Auth::user()->player->id }}</div>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">VALIDE</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des transferts -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-exchange-alt mr-2 text-purple-500"></i>
                            Historique des Transferts
                        </h3>
                        <div class="space-y-3">
                            <!-- Transfert actuel (simulé) -->
                            @if(Auth::user()->player->club)
                            <div class="p-4 bg-white rounded-lg border-l-4 border-green-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        @if(Auth::user()->player->club->logo_url)
                                            <img src="{{ Auth::user()->player->club->logo_url }}" alt="Logo" class="w-8 h-8 object-contain">
                                        @endif
                                        <div>
                                            <div class="font-medium">{{ Auth::user()->player->club->name }}</div>
                                            <div class="text-sm text-gray-600">Contrat actuel</div>
                                            <div class="text-xs text-gray-500">Depuis 2023</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">ACTIF</span>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Transferts précédents (simulés) -->
                            <div class="p-4 bg-white rounded-lg border-l-4 border-gray-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-history text-gray-500 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">Club Précédent</div>
                                            <div class="text-sm text-gray-600">2021 - 2023</div>
                                            <div class="text-xs text-gray-500">Transfert libre</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">HISTORIQUE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Passeport Joueur -->
            <div v-show="activeTab === 'passport'" class="p-6">
                <h2 class="text-2xl font-bold gradient-text mb-6">Passeport Joueur FIFA</h2>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Informations du passeport -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-passport mr-2 text-blue-500"></i>
                                Informations Officielles
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">FIFA Connect ID</span>
                                    <span class="font-mono font-medium">{{ Auth::user()->player->fifa_connect_id ?? 'FC-' . str_pad(Auth::user()->player->id, 8, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">Statut Joueur</span>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">PROFESSIONNEL</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">Éligibilité Internationale</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ Auth::user()->player->nationality ?? 'FRANCE' }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">Dernière Mise à Jour</span>
                                    <span class="font-medium">{{ Auth::user()->player->updated_at ? Auth::user()->player->updated_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sélections nationales -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-flag mr-2 text-green-500"></i>
                                Sélections Nationales
                            </h3>
                            <div class="space-y-3">
                                <!-- Sélection senior (simulée) -->
                                <div class="p-4 bg-white rounded-lg border-l-4 border-blue-500">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ Auth::user()->player->nation_flag_url ?? 'https://via.placeholder.com/24x18/4285f4/ffffff?text=FR' }}" 
                                                 alt="Drapeau" class="w-6 h-4 rounded">
                                            <div>
                                                <div class="font-medium">Équipe Nationale A</div>
                                                <div class="text-sm text-gray-600">{{ Auth::user()->player->nationality ?? 'France' }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold">{{ Auth::user()->player->international_reputation ? Auth::user()->player->international_reputation * 5 : 12 }}</div>
                                            <div class="text-xs text-gray-600">sélections</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sélection jeunes (simulée) -->
                                <div class="p-4 bg-white rounded-lg border-l-4 border-green-500">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ Auth::user()->player->nation_flag_url ?? 'https://via.placeholder.com/24x18/4285f4/ffffff?text=FR' }}" 
                                                 alt="Drapeau" class="w-6 h-4 rounded">
                                            <div>
                                                <div class="font-medium">Équipe U21</div>
                                                <div class="text-sm text-gray-600">{{ Auth::user()->player->nationality ?? 'France' }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold">{{ Auth::user()->player->age < 23 ? 8 : 15 }}</div>
                                            <div class="text-xs text-gray-600">sélections</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Message d'erreur -->
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
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    activeTab: 'medical',
                    isLoading: false,
                    tabs: [
                        { id: 'medical', name: 'Données Médicales', icon: 'fas fa-heartbeat' },
                        { id: 'stats', name: 'Statistiques & Saisons', icon: 'fas fa-chart-line' },
                        { id: 'licenses', name: 'Licences & Transferts', icon: 'fas fa-id-card' },
                        { id: 'passport', name: 'Passeport Joueur', icon: 'fas fa-passport' }
                    ]
                }
            },
            mounted() {
                this.animateStats();
            },
            methods: {
                refreshData() {
                    this.isLoading = true;
                    // Simuler un appel API
                    setTimeout(() => {
                        this.isLoading = false;
                        location.reload();
                    }, 2000);
                },
                animateStats() {
                    // Animer les barres de statistiques FIFA
                    setTimeout(() => {
                        const statBars = document.querySelectorAll('.fifa-stat-fill');
                        statBars.forEach(bar => {
                            const width = bar.style.width;
                            bar.style.width = '0%';
                            setTimeout(() => {
                                bar.style.width = width;
                            }, 100);
                        });
                    }, 500);
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
