<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Ultimate Dashboard - Fonctionnel</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
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
    </style>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen">
        <!-- Hero Zone avec Photo et Logos -->
        <div class="bg-gradient-to-r from-blue-900 via-purple-900 to-indigo-900 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            
            <div class="relative z-10 p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col lg:flex-row items-center justify-between">
                        <!-- Informations du joueur -->
                        <div class="flex-1 text-center lg:text-left mb-8 lg:mb-0">
                            <div class="flex items-center justify-center lg:justify-start mb-6">
                                <!-- Photo du joueur -->
                                <div class="w-24 h-24 lg:w-32 lg:h-32 rounded-full border-4 border-white shadow-2xl mr-6 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=150&h=150&fit=crop&crop=face" 
                                         alt="Photo du joueur" 
                                         class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Informations -->
                                <div>
                                    <h1 class="text-3xl lg:text-5xl font-bold mb-2">Kylian Mbappé</h1>
                                    <p class="text-xl lg:text-2xl text-blue-200 mb-2">Attaquant - PSG & France</p>
                                    <div class="flex items-center space-x-4 text-sm">
                                        <span class="bg-blue-600 px-3 py-1 rounded-full">#7</span>
                                        <span class="bg-green-600 px-3 py-1 rounded-full">Actif</span>
                                        <span class="bg-purple-600 px-3 py-1 rounded-full">Capitaine</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statistiques rapides -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-2xl mx-auto lg:mx-0">
                                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 border border-white border-opacity-20">
                                    <p class="text-2xl font-bold">25</p>
                                    <p class="text-sm text-blue-200">Matchs</p>
                                </div>
                                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 border border-white border-opacity-20">
                                    <p class="text-2xl font-bold">15</p>
                                    <p class="text-sm text-blue-200">Buts</p>
                                </div>
                                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 border border-white border-opacity-20">
                                    <p class="text-2xl font-bold">12</p>
                                    <p class="text-sm text-blue-200">Passes</p>
                                </div>
                                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 border border-white border-opacity-20">
                                    <p class="text-2xl font-bold">8.2</p>
                                    <p class="text-sm text-blue-200">Note</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logos et badges -->
                        <div class="flex flex-col items-center space-y-4">
                            <!-- Logo PSG -->
                            <div class="bg-white rounded-full p-4 shadow-2xl">
                                <img src="https://upload.wikimedia.org/wikipedia/en/a/a7/Paris_Saint-Germain_F.C..svg" 
                                     alt="Logo PSG" 
                                     class="w-16 h-16">
                            </div>
                            
                            <!-- Logo France -->
                            <div class="bg-white rounded-full p-4 shadow-2xl">
                                <img src="https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg" 
                                     alt="Drapeau France" 
                                     class="w-16 h-16">
                            </div>
                            
                            <!-- Badge FIFA -->
                            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full p-4 shadow-2xl">
                                <i class="fas fa-trophy text-3xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 mb-6 mt-8">
            <div class="flex flex-wrap gap-4">
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['fifa-nav-tab relative px-6 py-3 rounded-lg text-white font-medium transition-all', activeTab === tab.id ? 'active' : '']"
                >
                    <i :class="tab.icon" class="mr-2"></i>
                    <span v-text="tab.name"></span>
                    <span v-if="tab.count" class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs" v-text="tab.count"></span>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="px-6">
            <!-- Onglet Performance -->
            <div v-show="activeTab === 'performance'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">⚽ Performance & Statistiques</h2>
                
                <!-- Résumé de saison avec métriques avancées -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Buts</h3>
                        <p class="text-3xl font-bold" v-text="performanceData.seasonSummary.goals.total"></p>
                        <p class="text-sm opacity-90">Tendance: <span v-text="performanceData.seasonSummary.goals.trend"></span></p>
                        <p class="text-xs opacity-75">Moyenne: <span v-text="performanceData.seasonSummary.goals.average"></span>/match</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Passes</h3>
                        <p class="text-3xl font-bold" v-text="performanceData.seasonSummary.assists.total"></p>
                        <p class="text-sm opacity-90">Tendance: <span v-text="performanceData.seasonSummary.assists.trend"></span></p>
                        <p class="text-xs opacity-75">Précision: <span v-text="performanceData.seasonSummary.assists.accuracy"></span>%</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Matchs</h3>
                        <p class="text-3xl font-bold" v-text="performanceData.seasonSummary.matches.total"></p>
                        <p class="text-sm opacity-90">Note: <span v-text="performanceData.seasonSummary.matches.rating"></span>/10</p>
                        <p class="text-xs opacity-75">Distance: <span v-text="performanceData.seasonSummary.matches.distance"></span></p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Minutes</h3>
                        <p class="text-3xl font-bold" v-text="performanceData.seasonSummary.minutes.total"></p>
                        <p class="text-sm opacity-90">Moyenne: <span v-text="performanceData.seasonSummary.minutes.average"></span>/match</p>
                        <p class="text-xs opacity-75">Disponibilité: <span v-text="performanceData.seasonSummary.minutes.availability"></span>%</p>
                    </div>
                </div>
                
                                <!-- Statistiques détaillées par catégorie -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Statistiques Offensives -->
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <h3 class="font-bold text-red-800 mb-3">🎯 Attaque</h3>
                        <div class="space-y-2">
                            <div v-for="stat in performanceData.offensiveStats" :key="stat.name" class="flex justify-between items-center">
                                <span class="text-sm text-red-700" v-text="stat.name"></span>
                                <span class="font-bold text-red-800" v-text="stat.value"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques Physiques -->
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <h3 class="font-bold text-blue-800 mb-3">💪 Physique</h3>
                        <div class="space-y-2">
                            <div v-for="stat in performanceData.physicalStats" :key="stat.name" class="flex justify-between items-center">
                                <span class="text-sm text-blue-700" v-text="stat.name"></span>
                                <span class="font-bold text-blue-800" v-text="stat.value"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques Techniques -->
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-800 mb-3">⚽ Technique</h3>
                        <div class="space-y-2">
                            <div v-for="stat in performanceData.technicalStats" :key="stat.name" class="flex justify-between items-center">
                                <span class="text-sm text-green-700" v-text="stat.name"></span>
                                <span class="font-bold text-green-800" v-text="stat.value"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques Défensives -->
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <h3 class="font-bold text-purple-800 mb-3">🛡️ Défense</h3>
                        <div class="space-y-2">
                            <div v-for="stat in performanceData.defensiveStats" :key="stat.name" class="flex justify-between items-center">
                                <span class="text-sm text-purple-700" v-text="stat.name"></span>
                                <span class="font-bold text-purple-800" v-text="stat.value"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Évolution des performances -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📈 Évolution des Performances</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="(evolution, month) in performanceData.performanceEvolution" :key="month" 
                                 class="bg-white p-3 rounded border">
                                <h4 class="font-bold text-gray-800 mb-2" v-text="month"></h4>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Note:</span>
                                        <span class="font-bold" v-text="evolution.rating + '/10'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Buts:</span>
                                        <span class="font-bold" v-text="evolution.goals"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Passes:</span>
                                        <span class="font-bold" v-text="evolution.assists"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Minutes:</span>
                                        <span class="font-bold" v-text="evolution.minutes"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Détails des matchs récents -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🏆 Matchs Récents</h3>
                    <div class="space-y-3">
                        <div v-for="match in performanceData.recentMatches" :key="match.id" 
                             class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="font-bold text-lg mr-3" v-text="match.homeTeam + ' vs ' + match.awayTeam"></span>
                                        <span :class="[
                                            'px-3 py-1 rounded-full text-xs font-bold',
                                            match.result === 'Victoire' ? 'bg-green-100 text-green-800' : 
                                            match.result === 'Nul' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                                        ]" v-text="match.result"></span>
                                    </div>
                                    <p class="text-gray-700 mb-2">Date: <span v-text="match.date"></span> | Score: <span v-text="match.score"></span></p>
                                    
                                    <!-- Performance du joueur dans ce match -->
                                    <div class="bg-white p-3 rounded border mt-3">
                                        <h5 class="font-bold text-gray-700 mb-2">📊 Performance du Joueur</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <span class="font-bold text-gray-600">Note:</span>
                                                <span class="font-bold" v-text="match.playerPerformance.rating + '/10'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Buts:</span>
                                                <span v-text="match.playerPerformance.goals"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Passes:</span>
                                                <span v-text="match.playerPerformance.assists"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Minutes:</span>
                                                <span v-text="match.playerPerformance.minutes + '\''"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Tirs:</span>
                                                <span v-text="match.playerPerformance.shots"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Précision:</span>
                                                <span v-text="match.playerPerformance.accuracy + '%'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Distance:</span>
                                                <span v-text="match.playerPerformance.distance + ' km'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Vitesse max:</span>
                                                <span v-text="match.playerPerformance.maxSpeed + ' km/h'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-chart-line mr-1"></i>Analyse
                                    </button>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-download mr-1"></i>Rapport
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Comparaison avec la saison précédente -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📊 Comparaison Saison Précédente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800 mb-2">Buts</h4>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600" v-text="performanceData.seasonComparison.goals.current"></p>
                                    <p class="text-sm text-blue-600">Cette saison</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-600" v-text="performanceData.seasonComparison.goals.previous"></p>
                                    <p class="text-sm text-gray-600">Saison précédente</p>
                                    <span :class="[
                                        'text-xs font-bold px-2 py-1 rounded',
                                        performanceData.seasonComparison.goals.change > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]" v-text="(performanceData.seasonComparison.goals.change > 0 ? '+' : '') + performanceData.seasonComparison.goals.change + '%'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800 mb-2">Passes</h4>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-green-600" v-text="performanceData.seasonComparison.assists.current"></p>
                                    <p class="text-sm text-green-600">Cette saison</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-600" v-text="performanceData.seasonComparison.assists.previous"></p>
                                    <p class="text-sm text-gray-600">Saison précédente</p>
                                    <span :class="[
                                        'text-xs font-bold px-2 py-1 rounded',
                                        performanceData.seasonComparison.assists.change > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]" v-text="(performanceData.seasonComparison.assists.change > 0 ? '+' : '') + performanceData.seasonComparison.assists.change + '%'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800 mb-2">Note Moyenne</h4>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-purple-600" v-text="performanceData.seasonComparison.rating.current + '/10'"></p>
                                    <p class="text-sm text-purple-600">Cette saison</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-600" v-text="performanceData.seasonComparison.rating.previous + '/10'"></p>
                                    <p class="text-sm text-gray-600">Saison précédente</p>
                                    <span :class="[
                                        'text-xs font-bold px-2 py-1 rounded',
                                        performanceData.seasonComparison.rating.change > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]" v-text="(performanceData.seasonComparison.rating.change > 0 ? '+' : '') + performanceData.seasonComparison.rating.change + '%'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800 mb-2">Minutes</h4>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-orange-600" v-text="performanceData.seasonComparison.minutes.current"></p>
                                    <p class="text-sm text-orange-600">Cette saison</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-600" v-text="performanceData.seasonComparison.minutes.previous"></p>
                                    <p class="text-sm text-gray-600">Saison précédente</p>
                                    <span :class="[
                                        'text-xs font-bold px-1 rounded',
                                        performanceData.seasonComparison.minutes.change > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]" v-text="(performanceData.seasonComparison.minutes.change > 0 ? '+' : '') + performanceData.seasonComparison.minutes.change + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">⚡ Actions Rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                            <i class="fas fa-chart-line mr-2"></i>Analyse Détaillée
                        </button>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                            <i class="fas fa-download mr-2"></i>Exporter Rapport
                        </button>
                        <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-target mr-2"></i>Définir Objectifs
                        </button>
                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-share-alt mr-2"></i>Partager Performance
                        </button>
                    </div>
                </div>
                
                <!-- Chat d'Assistance Performance -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-bold mb-3 text-blue-800">💬 Assistant Performance IA</h3>
                    <div class="bg-white rounded-lg p-3 mb-3 max-h-40 overflow-y-auto">
                        <div v-for="message in performanceChat.messages" :key="message.id" 
                             :class="['mb-2 p-2 rounded-lg', message.type === 'user' ? 'bg-blue-100 ml-8' : 'bg-gray-100 mr-8']">
                            <div class="flex items-start">
                                <span v-if="message.type === 'user'" class="text-blue-600 font-bold mr-2">Vous:</span>
                                <span v-else class="text-gray-600 font-bold mr-2">IA:</span>
                                <span v-text="message.text" class="text-sm"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <input v-model="performanceChat.newMessage" 
                               @keyup.enter="sendPerformanceMessage"
                               type="text" 
                               placeholder="Posez une question sur vos performances..." 
                               class="flex-1 px-3 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button @click="sendPerformanceMessage" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Notifications -->
            <div v-show="activeTab === 'notifications'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Notifications</h2>
                
                <!-- Filtres -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <button 
                        v-for="filter in ['Toutes', 'Urgentes', 'Entraînements', 'Matchs', 'Médical']" 
                        :key="filter"
                        @click="activeNotificationFilter = filter"
                        :class="['px-4 py-2 rounded-lg font-medium transition-all', 
                            activeNotificationFilter === filter 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                    >
                        <span v-text="filter"></span>
                    </button>
                </div>
                
                <!-- Affichage filtré -->
                <div v-if="activeNotificationFilter === 'Toutes' || activeNotificationFilter === 'Urgentes'">
                    <!-- Équipe nationale -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3 text-blue-800">🇫🇷 Équipe Nationale</h3>
                        <div v-for="notification in notificationData.nationalTeam" :key="notification.id" 
                             class="bg-blue-50 p-4 rounded-lg mb-3 border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-blue-900" v-text="notification.title"></h4>
                                    <p class="text-blue-700" v-text="notification.message"></p>
                                    <p class="text-sm text-blue-600 mt-1" v-text="notification.date"></p>
                                </div>
                                <span v-if="notification.type === 'urgent'" 
                                      class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">URGENT</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="activeNotificationFilter === 'Toutes' || activeNotificationFilter === 'Entraînements'">
                    <!-- Entraînements -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3 text-green-800">⚽ Entraînements</h3>
                        <div v-for="notification in notificationData.trainingSessions" :key="notification.id" 
                             class="bg-green-50 p-4 rounded-lg mb-3 border-l-4 border-green-500">
                            <h4 class="font-bold text-green-900" v-text="notification.title"></h4>
                            <p class="text-green-700" v-text="notification.message"></p>
                            <p class="text-sm text-green-600 mt-1" v-text="notification.date"></p>
                        </div>
                    </div>
                </div>
                
                <div v-if="activeNotificationFilter === 'Toutes' || activeNotificationFilter === 'Matchs'">
                    <!-- Matchs -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3 text-purple-800">🏆 Matchs</h3>
                        <div v-for="notification in notificationData.matches" :key="notification.id" 
                             class="bg-purple-50 p-4 rounded-lg mb-3 border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-900" v-text="notification.title"></h4>
                            <p class="text-purple-700" v-text="notification.message"></p>
                            <p class="text-sm text-purple-600 mt-1" v-text="notification.date"></p>
                        </div>
                    </div>
                </div>
                
                <div v-if="activeNotificationFilter === 'Toutes' || activeNotificationFilter === 'Médical'">
                    <!-- Rendez-vous médicaux -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3 text-orange-800">🏥 Rendez-vous médicaux</h3>
                        <div v-for="notification in notificationData.medicalAppointments" :key="notification.id" 
                             class="bg-orange-50 p-4 rounded-lg mb-3 border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-900" v-text="notification.title"></h4>
                            <p class="text-orange-700" v-text="notification.message"></p>
                            <p class="text-sm text-orange-600 mt-1" v-text="notification.date"></p>
                        </div>
                    </div>
                </div>
                
                <div v-if="activeNotificationFilter === 'Toutes'">
                    <!-- Réseaux sociaux -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3 text-indigo-800">📱 Réseaux sociaux</h3>
                        <div v-for="notification in notificationData.socialMedia" :key="notification.id" 
                             class="bg-indigo-50 p-4 rounded-lg mb-3 border-l-4 border-indigo-500">
                            <h4 class="font-bold text-indigo-900" v-text="notification.title"></h4>
                            <p class="text-indigo-700" v-text="notification.message"></p>
                            <p class="text-sm text-indigo-600 mt-1" v-text="notification.date"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Chat d'Assistance Notifications -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-bold mb-3 text-green-800">💬 Assistant Notifications IA</h3>
                    <div class="bg-white rounded-lg p-3 mb-3 max-h-40 overflow-y-auto">
                        <div v-for="message in notificationChat.messages" :key="message.id" 
                             :class="['mb-2 p-2 rounded-lg', message.type === 'user' ? 'bg-green-100 ml-8' : 'bg-gray-100 mr-8']">
                            <div class="flex items-start">
                                <span v-if="message.type === 'user'" class="text-green-600 font-bold mr-2">Vous:</span>
                                <span v-else class="text-gray-600 font-bold mr-2">IA:</span>
                                <span v-text="message.text" class="text-sm"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <input v-model="notificationChat.newMessage" 
                               @keyup.enter="sendNotificationMessage"
                               type="text" 
                               placeholder="Posez une question sur vos notifications..." 
                               class="flex-1 px-3 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button @click="sendNotificationMessage" 
                                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Santé & Bien-être -->
            <div v-show="activeTab === 'health'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Santé & Bien-être</h2>
                
                <!-- Scores globaux -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Score Global</h3>
                        <p class="text-3xl font-bold" v-text="healthData.globalScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Score Physique</h3>
                        <p class="text-3xl font-bold" v-text="healthData.physicalScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Score Mental</h3>
                        <p class="text-3xl font-bold" v-text="healthData.mentalScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                </div>
                
                <!-- Scores détaillés -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Sommeil</h3>
                        <p class="text-2xl font-bold" v-text="healthData.sleepScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Social</h3>
                        <p class="text-2xl font-bold" v-text="healthData.socialScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                    <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Récupération</h3>
                        <p class="text-2xl font-bold" v-text="healthData.recoveryScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                    <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Préparation</h3>
                        <p class="text-2xl font-bold" v-text="healthData.readinessScore"></p>
                        <p class="text-sm opacity-90">/ 100</p>
                    </div>
                </div>
                
                <!-- Indicateurs avancés -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📈 Indicateurs Avancés</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h4 class="font-bold text-yellow-800">Fatigue</h4>
                            <p class="text-xl font-bold text-yellow-600" v-text="healthData.fatigueIndex + '%'"></p>
                            <p class="text-sm text-yellow-600">Faible</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Hydratation</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.hydrationStatus"></p>
                            <p class="text-sm text-blue-600">Statut</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">IMC</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.bmi"></p>
                            <p class="text-sm text-green-600">Normal</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">Densité Osseuse</h4>
                            <p class="text-xl font-bold text-purple-600" v-text="healthData.bodyComposition.boneDensity + '%'"></p>
                            <p class="text-sm text-purple-600">Excellente</p>
                        </div>
                    </div>
                </div>
                
                <!-- Signes vitaux -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🫀 Signes Vitaux</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-800">Fréquence Cardiaque</h4>
                            <p class="text-xl font-bold text-red-600" v-text="healthData.vitals.heartRate.current + ' bpm'"></p>
                            <p class="text-sm text-red-600">Repos: <span v-text="healthData.vitals.heartRate.resting + ' bpm'"></span></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Tension Artérielle</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.vitals.bloodPressure.systolic + '/' + healthData.vitals.bloodPressure.diastolic"></p>
                            <p class="text-sm text-blue-600">mmHg</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">Saturation O₂</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.vitals.oxygenSaturation + '%'"></p>
                            <p class="text-sm text-green-600">Normale</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h4 class="font-bold text-yellow-800">Température</h4>
                            <p class="text-xl font-bold text-yellow-600" v-text="healthData.vitals.temperature + '°C'"></p>
                            <p class="text-sm text-yellow-600">Normale</p>
                        </div>
                    </div>
                </div>
                
                <!-- Métriques récentes -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📊 Métriques Récentes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sommeil</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stress</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Énergie</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hydratation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pas</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calories</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charge</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Récupération</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="metric in healthData.recentMetrics" :key="metric.date" class="border-t border-gray-200">
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.date"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.sleepHours + 'h'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.stressLevel + '/10'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.energyLevel + '/10'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.hydration + 'L'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.steps.toLocaleString()"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.calories + ' kcal'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.trainingLoad + '%'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900" v-text="metric.recovery + '/10'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Recommandations -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💡 Recommandations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(recommendation, index) in healthData.recommendations" :key="index" 
                             class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <p class="text-blue-800" v-text="recommendation"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Nutrition -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🍎 Nutrition</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800">Calories</h4>
                            <p class="text-xl font-bold text-orange-600" v-text="healthData.nutritionData.dailyCalories + ' kcal'"></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Protéines</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.nutritionData.protein + 'g'"></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">Glucides</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.nutritionData.carbs + 'g'"></p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h4 class="font-bold text-yellow-800">Lipides</h4>
                            <p class="text-xl font-bold text-yellow-600" v-text="healthData.nutritionData.fats + 'g'"></p>
                        </div>
                    </div>
                    
                    <!-- Suppléments -->
                    <div class="mt-4">
                        <h4 class="font-bold mb-2 text-gray-700">💊 Suppléments</h4>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="supplement in healthData.nutritionData.supplements" :key="supplement" 
                                  class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                                <span v-text="supplement"></span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Micronutriments -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <h5 class="font-bold text-green-800">Fibres</h5>
                            <p class="text-green-600" v-text="healthData.nutritionData.fiber + 'g'"></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h5 class="font-bold text-blue-800">Sodium</h5>
                            <p class="text-blue-600" v-text="healthData.nutritionData.sodium + 'mg'"></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <h5 class="font-bold text-purple-800">Potassium</h5>
                            <p class="text-purple-600" v-text="healthData.nutritionData.potassium + 'mg'"></p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <h5 class="font-bold text-yellow-800">Calcium</h5>
                            <p class="text-yellow-600" v-text="healthData.nutritionData.calcium + 'mg'"></p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <h5 class="font-bold text-red-800">Fer</h5>
                            <p class="text-red-600" v-text="healthData.nutritionData.iron + 'mg'"></p>
                        </div>
                    </div>
                    
                    <!-- Timing des repas -->
                    <div class="mt-4">
                        <h4 class="font-bold mb-2 text-gray-700">⏰ Timing des Repas</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-orange-50 p-3 rounded-lg">
                                <h5 class="font-bold text-orange-800">Pré-entraînement</h5>
                                <p class="text-orange-600" v-text="healthData.nutritionData.mealTiming.preWorkout"></p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <h5 class="font-bold text-green-800">Post-entraînement</h5>
                                <p class="text-green-600" v-text="healthData.nutritionData.mealTiming.postWorkout"></p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <h5 class="font-bold text-blue-800">Avant coucher</h5>
                                <p class="text-blue-600" v-text="healthData.nutritionData.mealTiming.bedtime"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Fitness -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💪 Fitness</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">VO₂ Max</h4>
                            <p class="text-xl font-bold text-purple-600" v-text="healthData.fitnessData.vo2Max + ' ml/kg/min'"></p>
                            <p class="text-sm text-purple-600">Excellent</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-800">Seuil Lactate</h4>
                            <p class="text-xl font-bold text-red-600" v-text="healthData.fitnessData.lactateThreshold + '%'"></p>
                            <p class="text-sm text-red-600">Très élevé</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">Masse Musculaire</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.fitnessData.muscleMass + '%'"></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">% Graisse</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.fitnessData.bodyFat + '%'"></p>
                            <p class="text-sm text-blue-600">Athlétique</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h4 class="font-bold text-yellow-800">Flexibilité</h4>
                            <p class="text-xl font-bold text-yellow-600" v-text="healthData.fitnessData.flexibility + '%'"></p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <h4 class="font-bold text-indigo-800">Équilibre</h4>
                            <p class="text-xl font-bold text-indigo-600" v-text="healthData.fitnessData.balance + '%'"></p>
                        </div>
                    </div>
                    
                    <!-- Métriques avancées -->
                    <div class="mt-4">
                        <h4 class="font-bold mb-3 text-gray-700">📊 Métriques Avancées</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-red-50 p-3 rounded-lg">
                                <h5 class="font-bold text-red-800">Temps Réaction</h5>
                                <p class="text-red-600" v-text="healthData.fitnessData.reactionTime + 's'"></p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <h5 class="font-bold text-blue-800">Saut Vertical</h5>
                                <p class="text-blue-600" v-text="healthData.fitnessData.verticalJump + 'cm'"></p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <h5 class="font-bold text-green-800">Développé Couché</h5>
                                <p class="text-green-600" v-text="healthData.fitnessData.benchPress + 'kg'"></p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <h5 class="font-bold text-purple-800">Squat</h5>
                                <p class="text-purple-600" v-text="healthData.fitnessData.squat + 'kg'"></p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <h5 class="font-bold text-yellow-800">Deadlift</h5>
                                <p class="text-yellow-600" v-text="healthData.fitnessData.deadlift + 'kg'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sommeil détaillé -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">😴 Analyse du Sommeil</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <h4 class="font-bold text-indigo-800">Sommeil Profond</h4>
                            <p class="text-xl font-bold text-indigo-600" v-text="healthData.sleepData.deepSleep + 'h'"></p>
                            <p class="text-sm text-indigo-600">Récupération</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">Sommeil REM</h4>
                            <p class="text-xl font-bold text-purple-600" v-text="healthData.sleepData.remSleep + 'h'"></p>
                            <p class="text-sm text-purple-600">Mémoire</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Efficacité</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.sleepData.sleepEfficiency + '%'"></p>
                            <p class="text-sm text-blue-600">Qualité</p>
                        </div>
                    </div>
                    
                    <!-- Détails sommeil -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <h5 class="font-bold text-green-800">Temps Total</h5>
                            <p class="text-green-600" v-text="healthData.sleepData.totalSleepTime + 'h'"></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <h5 class="font-bold text-purple-800">Cycles</h5>
                            <p class="text-purple-600" v-text="healthData.sleepData.sleepCycles"></p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <h5 class="font-bold text-orange-800">Latence REM</h5>
                            <p class="text-orange-600" v-text="healthData.sleepData.remLatency + 'min'"></p>
                        </div>
                        <div class="bg-indigo-50 p-3 rounded-lg">
                            <h5 class="font-bold text-indigo-800">Qualité</h5>
                            <p class="text-indigo-600" v-text="healthData.sleepData.sleepQuality"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Gestion du stress -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🧘 Gestion du Stress</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold mb-2 text-gray-700">Niveau actuel: <span class="text-blue-600" v-text="healthData.stressData.currentLevel + '/10'"></span></h4>
                            <p class="text-sm text-gray-600 mb-3">Tendance: <span v-text="healthData.stressData.trend"></span></p>
                            
                            <h5 class="font-bold mb-2 text-gray-700">Déclencheurs:</h5>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span v-for="trigger in healthData.stressData.triggers" :key="trigger" 
                                      class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
                                    <span v-text="trigger"></span>
                                </span>
                            </div>
                        </div>
                        <div>
                            <h5 class="font-bold mb-2 text-gray-700">Stratégies d'adaptation:</h5>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="strategy in healthData.stressData.copingStrategies" :key="strategy" 
                                      class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                    <span v-text="strategy"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détails stress -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h5 class="font-bold text-blue-800">Cortisol</h5>
                            <p class="text-blue-600" v-text="healthData.stressData.cortisolLevel"></p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <h5 class="font-bold text-red-800">Adrénaline</h5>
                            <p class="text-red-600" v-text="healthData.stressData.adrenalineLevel"></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <h5 class="font-bold text-green-800">Temps Récupération</h5>
                            <p class="text-green-600" v-text="healthData.stressData.recoveryTime"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Métriques de performance -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🏃 Métriques de Performance</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-800">Vitesse Sprint</h4>
                            <p class="text-xl font-bold text-red-600" v-text="healthData.performanceMetrics.sprintSpeed"></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Endurance</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.performanceMetrics.endurance"></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">Force</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.performanceMetrics.strength"></p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">Puissance</h4>
                            <p class="text-xl font-bold text-purple-600" v-text="healthData.performanceMetrics.power"></p>
                        </div>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <h4 class="font-bold text-yellow-800">Agilité</h4>
                            <p class="text-xl font-bold text-yellow-600" v-text="healthData.performanceMetrics.agility"></p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <h4 class="font-bold text-indigo-800">Coordination</h4>
                            <p class="text-xl font-bold text-indigo-600" v-text="healthData.performanceMetrics.coordination"></p>
                        </div>
                        <div class="bg-pink-50 p-4 rounded-lg border-l-4 border-pink-500">
                            <h4 class="font-bold text-pink-800">Focus Mental</h4>
                            <p class="text-xl font-bold text-pink-600" v-text="healthData.performanceMetrics.mentalFocus"></p>
                        </div>
                        <div class="bg-teal-50 p-4 rounded-lg border-l-4 border-teal-500">
                            <h4 class="font-bold text-teal-800">Prise de Décision</h4>
                            <p class="text-xl font-bold text-teal-600" v-text="healthData.performanceMetrics.decisionMaking"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Tendances de santé -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📊 Tendances de Santé</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Poids</h4>
                            <p class="text-xl font-bold text-blue-600" v-text="healthData.healthTrends.weight.current + ' kg'"></p>
                            <p class="text-sm text-blue-600">Tendance: <span v-text="healthData.healthTrends.weight.trend"></span></p>
                            <p class="text-xs text-blue-500">Objectif: <span v-text="healthData.healthTrends.weight.target + ' kg'"></span></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">% Graisse</h4>
                            <p class="text-xl font-bold text-green-600" v-text="healthData.healthTrends.bodyFat.current + '%'"></p>
                            <p class="text-sm text-green-600">Tendance: <span v-text="healthData.healthTrends.bodyFat.trend"></span></p>
                            <p class="text-xs text-green-500">Objectif: <span v-text="healthData.healthTrends.bodyFat.target + '%'"></span></p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">Masse Musculaire</h4>
                            <p class="text-xl font-bold text-purple-600" v-text="healthData.healthTrends.muscleMass.current + '%'"></p>
                            <p class="text-sm text-purple-600">Tendance: <span v-text="healthData.healthTrends.muscleMass.trend"></span></p>
                            <p class="text-xs text-purple-500">Objectif: <span v-text="healthData.healthTrends.muscleMass.target + '%'"></span></p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800">VO₂ Max</h4>
                            <p class="text-xl font-bold text-orange-600" v-text="healthData.healthTrends.vo2Max.current + ' ml/kg/min'"></p>
                            <p class="text-sm text-orange-600">Tendance: <span v-text="healthData.healthTrends.vo2Max.trend"></span></p>
                            <p class="text-xs text-orange-500">Objectif: <span v-text="healthData.healthTrends.vo2Max.target + ' ml/kg/min'"></span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Alertes -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🚨 Alertes & Notifications</h3>
                    <div class="space-y-3">
                        <div v-for="alert in healthData.alerts" :key="alert.type" 
                             :class="[
                                 'p-4 rounded-lg border-l-4',
                                 alert.type === 'warning' ? 'bg-yellow-50 border-yellow-500' : '',
                                 alert.type === 'info' ? 'bg-blue-50 border-blue-500' : '',
                                 alert.type === 'success' ? 'bg-green-50 border-green-500' : ''
                             ]">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p :class="[
                                        'font-medium',
                                        alert.type === 'warning' ? 'text-yellow-800' : '',
                                        alert.type === 'info' ? 'text-blue-800' : '',
                                        alert.type === 'success' ? 'text-green-800' : ''
                                    ]" v-text="alert.message"></p>
                                    <p class="text-sm text-gray-600 mt-1">Priorité: <span v-text="alert.priority"></span></p>
                                </div>
                                <div class="ml-4">
                                    <span v-if="alert.type === 'warning'" class="text-yellow-600 text-2xl">⚠️</span>
                                    <span v-else-if="alert.type === 'info'" class="text-blue-600 text-2xl">ℹ️</span>
                                    <span v-else-if="alert.type === 'success'" class="text-green-600 text-2xl">✅</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Risque de blessure -->
                <div class="bg-gradient-to-r from-orange-50 to-red-50 p-4 rounded-lg border-l-4 border-orange-500">
                    <h3 class="text-lg font-bold mb-2 text-orange-800">⚠️ Risque de Blessure</h3>
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-orange-700">Niveau de risque actuel: <span class="font-bold" v-text="healthData.injuryRisk + '%'"></span></p>
                            <p class="text-sm text-orange-600 mt-1">Surveillance recommandée - Éviter les efforts intenses</p>
                        </div>
                        <div class="ml-4">
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 font-bold text-lg" v-text="healthData.injuryRisk + '%'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Médical -->
            <div v-show="activeTab === 'medical'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Dossier Médical Complet</h2>
                
                <!-- Actions principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>Partager le dossier
                    </button>
                    <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Imprimer
                    </button>
                    <button class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                    </button>
                </div>
                
                <!-- Dossiers médicaux récents -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📋 Dossiers Médicaux Récents</h3>
                    <div class="space-y-3">
                        <div v-for="record in medicalData.recentHealthRecords" :key="record.id" 
                             class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-bold text-blue-800" v-text="record.type"></h4>
                                    <p class="text-blue-700" v-text="record.notes"></p>
                                    <p class="text-sm text-blue-600 mt-1">Médecin: <span v-text="record.doctor"></span> | Date: <span v-text="record.date"></span></p>
                                    
                                    <!-- Détails vitaux -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3">
                                        <div class="text-xs">
                                            <span class="font-bold text-blue-700">Tension:</span>
                                            <span v-text="record.bloodPressure"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-blue-700">FC:</span>
                                            <span v-text="record.heartRate + ' bpm'"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-blue-700">Temp:</span>
                                            <span v-text="record.temperature + '°C'"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-blue-700">IMC:</span>
                                            <span v-text="record.bmi"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs ml-4" v-text="record.status"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Évaluations PCMA -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🏥 Évaluations PCMA</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="pcma in medicalData.pcmas" :key="pcma.id" 
                             class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800" v-text="pcma.type"></h4>
                            <p class="text-2xl font-bold text-green-600" v-text="pcma.score + '/100'"></p>
                            <p class="text-sm text-green-600" v-text="pcma.category"></p>
                            <p class="text-sm text-green-700 mt-2" v-text="pcma.notes"></p>
                            
                            <!-- Composantes détaillées -->
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="text-xs">
                                    <span class="font-bold text-green-700">Physique:</span>
                                    <span v-text="pcma.components.physical + '/100'"></span>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-green-700">Mental:</span>
                                    <span v-text="pcma.components.mental + '/100'"></span>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-green-700">Social:</span>
                                    <span v-text="pcma.components.social + '/100'"></span>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-green-700">Technique:</span>
                                    <span v-text="pcma.components.technical + '/100'"></span>
                                </div>
                            </div>
                            
                            <p class="text-xs text-green-600 mt-2">Date: <span v-text="pcma.date"></span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Prédictions médicales -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🔮 Prédictions Médicales</h3>
                    <div class="space-y-3">
                        <div v-for="prediction in medicalData.medicalPredictions" :key="prediction.id" 
                             class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-purple-800" v-text="prediction.prediction"></h4>
                                    <p class="text-purple-700" v-text="prediction.recommendations"></p>
                                    <p class="text-sm text-purple-600 mt-1">Confiance: <span v-text="prediction.confidence + '%'"></span></p>
                                </div>
                                <div class="text-right">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-xs font-bold',
                                        prediction.riskLevel === 'Faible' ? 'bg-green-100 text-green-800' : '',
                                        prediction.riskLevel === 'Modéré' ? 'bg-yellow-100 text-yellow-800' : '',
                                        prediction.riskLevel === 'Élevé' ? 'bg-red-100 text-red-800' : ''
                                    ]" v-text="prediction.riskLevel"></span>
                                    <p class="text-xs text-purple-600 mt-1" v-text="prediction.date"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historique des blessures -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🩹 Historique des Blessures</h3>
                    <div class="space-y-3">
                        <div v-for="injury in medicalData.injuries" :key="injury.id" 
                             class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-red-800" v-text="injury.type"></h4>
                                    <p class="text-red-700" v-text="injury.notes"></p>
                                    <p class="text-sm text-red-600 mt-1">Date: <span v-text="injury.date"></span> | Récupération: <span v-text="injury.recoveryTime"></span></p>
                                </div>
                                <div class="text-right">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-xs font-bold',
                                        injury.severity === 'Légère' ? 'bg-green-100 text-green-800' : '',
                                        injury.severity === 'Modérée' ? 'bg-yellow-100 text-yellow-800' : '',
                                        injury.severity === 'Grave' ? 'bg-red-100 text-red-800' : ''
                                    ]" v-text="injury.severity"></span>
                                    <p class="text-sm text-red-600 mt-1" v-text="injury.status"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historique des maladies -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🤒 Historique des Maladies</h3>
                    <div class="space-y-3">
                        <div v-for="illness in medicalData.illnesses" :key="illness.id" 
                             class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800" v-text="illness.type"></h4>
                            <p class="text-orange-700">Symptômes: <span v-text="illness.symptoms"></span></p>
                            <p class="text-orange-700">Traitement: <span v-text="illness.treatment"></span></p>
                            <p class="text-sm text-orange-600 mt-1">Date: <span v-text="illness.date"></span> | <span v-text="illness.status"></span></p>
                            <p class="text-sm text-orange-600 mt-1" v-text="illness.notes"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Médicaments -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💊 Médicaments Actuels</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="medication in medicalData.medications" :key="medication.name" 
                             class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <h4 class="font-bold text-indigo-800" v-text="medication.name"></h4>
                            <p class="text-indigo-700">Dosage: <span v-text="medication.dosage"></span></p>
                            <p class="text-indigo-700">Fréquence: <span v-text="medication.frequency"></span></p>
                            <p class="text-indigo-700">Durée: <span v-text="medication.duration"></span></p>
                            <p class="text-sm text-indigo-600 mt-2" v-text="medication.notes"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Rendez-vous à venir -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📅 Rendez-vous à Venir</h3>
                    <div class="space-y-3">
                        <div v-for="appointment in medicalData.upcomingAppointments" :key="appointment.id" 
                             class="bg-teal-50 p-4 rounded-lg border-l-4 border-teal-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-teal-800" v-text="appointment.type"></h4>
                                    <p class="text-teal-700">Médecin: <span v-text="appointment.doctor"></span></p>
                                    <p class="text-teal-700">Lieu: <span v-text="appointment.location"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-teal-600" v-text="appointment.date"></p>
                                    <p class="text-sm text-teal-600" v-text="appointment.time"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recommandations médicales -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💡 Recommandations Médicales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(recommendation, index) in medicalData.recommendations" :key="index" 
                             class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <p class="text-blue-800" v-text="recommendation"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Alertes médicales -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🚨 Alertes Médicales</h3>
                    <div class="space-y-3">
                        <div v-for="alert in medicalData.alerts" :key="alert.id" 
                             :class="[
                                 'p-4 rounded-lg border-l-4',
                                 alert.type === 'warning' ? 'bg-yellow-50 border-yellow-500' : '',
                                 alert.type === 'info' ? 'bg-blue-50 border-blue-500' : '',
                                 alert.type === 'success' ? 'bg-green-50 border-green-500' : ''
                             ]">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p :class="[
                                        'font-medium',
                                        alert.type === 'warning' ? 'text-yellow-800' : '',
                                        alert.type === 'info' ? 'text-blue-800' : '',
                                        alert.type === 'success' ? 'text-green-800' : ''
                                    ]" v-text="alert.message"></p>
                                    <p class="text-sm text-gray-600 mt-1">Priorité: <span v-text="alert.priority"></span> | Date: <span v-text="alert.date"></span></p>
                                    <p class="text-sm text-gray-600">Catégorie: <span v-text="alert.category"></span> | Action: <span v-text="alert.action"></span></p>
                                </div>
                                <div class="ml-4">
                                    <span v-if="alert.type === 'warning'" class="text-yellow-600 text-2xl">⚠️</span>
                                    <span v-else-if="alert.type === 'info'" class="text-blue-600 text-2xl">ℹ️</span>
                                    <span v-else-if="alert.type === 'success'" class="text-green-600 text-2xl">✅</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tests médicaux -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🔬 Tests Médicaux</h3>
                    <div class="space-y-3">
                        <div v-for="test in medicalData.medicalTests" :key="test.id" 
                             class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-indigo-800" v-text="test.type"></h4>
                                    <p class="text-indigo-700">Résultats: <span v-text="test.results"></span></p>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                                        <div v-if="test.hemoglobin" class="text-xs">
                                            <span class="font-bold text-indigo-700">Hb:</span> <span v-text="test.hemoglobin + ' g/dL'"></span>
                                        </div>
                                        <div v-if="test.heartRate" class="text-xs">
                                            <span class="font-bold text-indigo-700">FC:</span> <span v-text="test.heartRate + ' bpm'"></span>
                                        </div>
                                        <div v-if="test.vo2Max" class="text-xs">
                                            <span class="font-bold text-indigo-700">VO₂:</span> <span v-text="test.vo2Max + ' ml/kg/min'"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs" v-text="test.status"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vaccinations -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💉 Vaccinations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="vaccine in medicalData.vaccinations" :key="vaccine.id" 
                             class="bg-teal-50 p-4 rounded-lg border-l-4 border-teal-500">
                            <h4 class="font-bold text-teal-800" v-text="vaccine.name"></h4>
                            <p class="text-teal-700">Date: <span v-text="vaccine.date"></span></p>
                            <p class="text-teal-700">Prochain: <span v-text="vaccine.nextDue"></span></p>
                            <p class="text-sm text-teal-600 mt-2" v-text="vaccine.notes"></p>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs mt-2 inline-block" v-text="vaccine.status"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Antécédents familiaux -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">👨‍👩‍👧‍👦 Antécédents Familiaux</h3>
                    <div class="space-y-3">
                        <div v-for="(history, index) in medicalData.familyHistory" :key="index" 
                             class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800" v-text="history.condition"></h4>
                            <p class="text-orange-700">Relation: <span v-text="history.relation"></span> (âge: <span v-text="history.age"></span>)</p>
                            <p class="text-sm text-orange-600 mt-1" v-text="history.notes"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Facteurs de mode de vie -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🌱 Mode de Vie</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <h5 class="font-bold text-green-800">Tabac</h5>
                            <p class="text-green-600" v-text="medicalData.lifestyleFactors.smoking"></p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h5 class="font-bold text-blue-800">Alcool</h5>
                            <p class="text-blue-600" v-text="medicalData.lifestyleFactors.alcohol"></p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <h5 class="font-bold text-purple-800">Sommeil</h5>
                            <p class="text-purple-600" v-text="medicalData.lifestyleFactors.sleep"></p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <h5 class="font-bold text-yellow-800">Stress</h5>
                            <p class="text-yellow-600" v-text="medicalData.lifestyleFactors.stress"></p>
                        </div>
                        <div class="bg-indigo-50 p-3 rounded-lg">
                            <h5 class="font-bold text-indigo-800">Alimentation</h5>
                            <p class="text-indigo-600" v-text="medicalData.lifestyleFactors.diet"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Contacts d'urgence -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🚨 Contacts d'Urgence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="contact in medicalData.emergencyContacts" :key="contact.name" 
                             class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-800" v-text="contact.name"></h4>
                            <p class="text-red-700">Rôle: <span v-text="contact.role"></span></p>
                            <p class="text-red-700">Tél: <span v-text="contact.phone"></span></p>
                            <p class="text-red-700">Email: <span v-text="contact.email"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Devices -->
            <div v-show="activeTab === 'devices'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">📱 Gestion des Devices</h2>
                
                <!-- Résumé des devices connectés -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Devices Actifs</h3>
                        <p class="text-3xl font-bold" v-text="devicesData.activeDevices"></p>
                        <p class="text-sm opacity-90">Connectés</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Données Synchro</h3>
                        <p class="text-3xl font-bold" v-text="devicesData.syncStatus"></p>
                        <p class="text-sm opacity-90">À jour</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Batterie Moyenne</h3>
                        <p class="text-3xl font-bold" v-text="devicesData.avgBattery + '%'"></p>
                        <p class="text-sm opacity-90">Niveau</p>
                    </div>
                </div>
                
                <!-- Liste des devices -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📱 Devices Connectés</h3>
                    <div class="space-y-3">
                        <div v-for="device in devicesData.connectedDevices" :key="device.id" 
                             class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <i :class="device.icon + ' text-2xl mr-3'" :style="{color: device.color}"></i>
                                        <div>
                                            <h4 class="font-bold text-gray-800" v-text="device.name"></h4>
                                            <p class="text-sm text-gray-600" v-text="device.model"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Statut du device -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Batterie:</span>
                                            <span :class="[
                                                'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                device.battery > 50 ? 'bg-green-100 text-green-800' : 
                                                device.battery > 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                                            ]" v-text="device.battery + '%'"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Connexion:</span>
                                            <span :class="[
                                                'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                device.connection === 'WiFi' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                                            ]" v-text="device.connection"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Dernière sync:</span>
                                            <span class="text-gray-600" v-text="device.lastSync"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Statut:</span>
                                            <span :class="[
                                                'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                device.status === 'Online' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]" v-text="device.status"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Données récentes -->
                                    <div class="bg-white p-3 rounded border">
                                        <h5 class="font-bold text-gray-700 mb-2">📊 Données Récentes</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <span class="font-bold text-gray-600">Pas:</span>
                                                <span v-text="device.recentData.steps.toLocaleString()"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Calories:</span>
                                                <span v-text="device.recentData.calories + ' kcal'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Distance:</span>
                                                <span v-text="device.recentData.distance + ' km'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Sommeil:</span>
                                                <span v-text="device.recentData.sleep + 'h'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-sync-alt mr-1"></i>Sync
                                    </button>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-cog mr-1"></i>Config
                                    </button>
                                    <button class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
                                        <i class="fas fa-unlink mr-1"></i>Déconnecter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Applications connectées -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🔗 Applications Connectées</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="app in devicesData.connectedApps" :key="app.name" 
                             class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <div class="flex items-center mb-3">
                                <i :class="app.icon + ' text-2xl mr-3'" :style="{color: app.color}"></i>
                                <div>
                                    <h4 class="font-bold text-indigo-800" v-text="app.name"></h4>
                                    <p class="text-sm text-indigo-600" v-text="app.type"></p>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dernière sync:</span>
                                    <span class="font-medium" v-text="app.lastSync"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Données:</span>
                                    <span class="font-medium" v-text="app.dataPoints + ' points'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Statut:</span>
                                    <span :class="[
                                        'px-2 py-1 rounded text-xs font-bold',
                                        app.status === 'Connected' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    ]" v-text="app.status"></span>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex space-x-2">
                                <button class="bg-indigo-500 text-white px-3 py-1 rounded text-xs hover:bg-indigo-600">
                                    <i class="fas fa-sync-alt mr-1"></i>Sync
                                </button>
                                <button class="bg-gray-500 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">
                                    <i class="fas fa-cog mr-1"></i>Paramètres
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques de synchronisation -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📈 Statistiques de Synchronisation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800">Syncs Réussies</h4>
                            <p class="text-2xl font-bold text-blue-600" v-text="devicesData.syncStats.successful"></p>
                            <p class="text-sm text-blue-600">Cette semaine</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800">Données Transférées</h4>
                            <p class="text-2xl font-bold text-green-600" v-text="devicesData.syncStats.dataTransferred + ' MB'"></p>
                            <p class="text-sm text-green-600">Total</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800">Temps Moyen</h4>
                            <p class="text-2xl font-bold text-purple-600" v-text="devicesData.syncStats.avgTime + 's'"></p>
                            <p class="text-sm text-purple-600">Par sync</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                            <h4 class="font-bold text-orange-800">Erreurs</h4>
                            <p class="text-2xl font-bold text-orange-600" v-text="devicesData.syncStats.errors"></p>
                            <p class="text-sm text-orange-600">Cette semaine</p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">⚡ Actions Rapides</h3>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                            <i class="fas fa-sync-alt mr-2"></i>Synchroniser Tous
                        </button>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                            <i class="fas fa-plus mr-2"></i>Ajouter Device
                        </button>
                        <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-download mr-2"></i>Exporter Données
                        </button>
                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-cog mr-2"></i>Paramètres
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Onglet Dopage -->
            <div v-show="activeTab === 'doping'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">🧪 Contrôle Anti-Dopage</h2>
                
                <!-- Statut global du contrôle anti-dopage -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Statut Global</h3>
                        <p class="text-3xl font-bold">Conforme</p>
                        <p class="text-sm opacity-90">Aucun risque</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Tests Effectués</h3>
                        <p class="text-3xl font-bold" v-text="dopingData.totalTests"></p>
                        <p class="text-sm opacity-90">Cette saison</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Dernier Test</h3>
                        <p class="text-2xl font-bold" v-text="dopingData.lastTestDate"></p>
                        <p class="text-sm opacity-90">Résultat négatif</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Prochain Test</h3>
                        <p class="text-2xl font-bold" v-text="dopingData.nextTestDate"></p>
                        <p class="text-sm opacity-90">Prévu</p>
                    </div>
                </div>
                
                <!-- Historique des tests -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📋 Historique des Tests Anti-Dopage</h3>
                    <div class="space-y-3">
                        <div v-for="test in dopingData.testHistory" :key="test.id" 
                             class="bg-gray-50 p-4 rounded-lg border-l-4 border-green-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span :class="[
                                            'px-3 py-1 rounded-full text-xs font-bold mr-3',
                                            test.result === 'Négatif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        ]" v-text="test.result"></span>
                                        <h4 class="font-bold text-gray-800" v-text="test.type"></h4>
                                    </div>
                                    <p class="text-gray-700 mb-2">Date: <span v-text="test.date"></span> | Heure: <span v-text="test.time"></span></p>
                                    <p class="text-gray-700 mb-2">Lieu: <span v-text="test.location"></span> | Contrôleur: <span v-text="test.controller"></span></p>
                                    
                                    <!-- Détails du test -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3">
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Type d'échantillon:</span>
                                            <span v-text="test.sampleType"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Numéro échantillon:</span>
                                            <span v-text="test.sampleNumber"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Statut:</span>
                                            <span v-text="test.status"></span>
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-bold text-gray-700">Laboratoire:</span>
                                            <span v-text="test.laboratory"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Résultats détaillés -->
                                    <div v-if="test.detailedResults" class="mt-3 bg-white p-3 rounded border">
                                        <h5 class="font-bold text-gray-700 mb-2">🔬 Résultats Détaillés</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                                            <div v-for="(result, substance) in test.detailedResults" :key="substance">
                                                <span class="font-bold text-gray-600" v-text="substance + ':'"></span>
                                                <span :class="[
                                                    'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                    result === 'Négatif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                ]" v-text="result"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-download mr-1"></i>Rapport
                                    </button>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-share-alt mr-1"></i>Partager
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Substances interdites et autorisées -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🚫 Substances Interdites & ✅ Autorisées</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Substances interdites -->
                        <div>
                            <h4 class="font-bold text-red-800 mb-3">🚫 Substances Interdites</h4>
                            <div class="space-y-2">
                                <div v-for="substance in dopingData.prohibitedSubstances" :key="substance.name" 
                                     class="bg-red-50 p-3 rounded-lg border-l-4 border-red-500">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-bold text-red-800" v-text="substance.name"></h5>
                                            <p class="text-sm text-red-700" v-text="substance.category"></p>
                                            <p class="text-xs text-red-600 mt-1" v-text="substance.description"></p>
                                        </div>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-bold" v-text="substance.status"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Substances autorisées -->
                        <div>
                            <h4 class="font-bold text-green-800 mb-3">✅ Substances Autorisées</h4>
                            <div class="space-y-2">
                                <div v-for="substance in dopingData.allowedSubstances" :key="substance.name" 
                                     class="bg-green-50 p-3 rounded-lg border-l-4 border-green-500">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-bold text-green-800" v-text="substance.name"></h5>
                                            <p class="text-sm text-green-700" v-text="substance.category"></p>
                                            <p class="text-xs text-green-600 mt-1" v-text="substance.description"></p>
                                        </div>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold" v-text="substance.status"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Médicaments et traitements -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">💊 Médicaments & Traitements</h3>
                    <div class="space-y-3">
                        <div v-for="medication in dopingData.medications" :key="medication.name" 
                             class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-bold text-blue-800" v-text="medication.name"></h4>
                                    <p class="text-blue-700">Prescription: <span v-text="medication.prescription"></span></p>
                                    <p class="text-blue-700">Statut: <span v-text="medication.status"></span></p>
                                    
                                    <!-- Informations anti-dopage -->
                                    <div class="mt-3 bg-white p-3 rounded border">
                                        <h5 class="font-bold text-gray-700 mb-2">🧪 Statut Anti-Dopage</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                                            <div>
                                                <span class="font-bold text-gray-600">Autorisation:</span>
                                                <span :class="[
                                                    'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                    medication.dopingStatus.authorized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                ]" v-text="medication.dopingStatus.authorized ? 'Oui' : 'Non'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">TUE requise:</span>
                                                <span :class="[
                                                    'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                    medication.dopingStatus.tueRequired ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
                                                ]" v-text="medication.dopingStatus.tueRequired ? 'Oui' : 'Non'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Statut TUE:</span>
                                                <span :class="[
                                                    'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                    medication.dopingStatus.tueStatus === 'Approuvée' ? 'bg-green-100 text-green-800' : 
                                                    medication.dopingStatus.tueStatus === 'En attente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                                                ]" v-text="medication.dopingStatus.tueStatus"></span>
                                            </div>
                                        </div>
                                        
                                        <div v-if="medication.dopingStatus.notes" class="mt-2 text-xs text-gray-600">
                                            <span class="font-bold">Notes:</span> <span v-text="medication.dopingStatus.notes"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-file-medical mr-1"></i>TUE
                                    </button>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-edit mr-1"></i>Modifier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Autorisations d'usage thérapeutique (TUE) -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">📋 Autorisations d'Usage Thérapeutique (TUE)</h3>
                    <div class="space-y-3">
                        <div v-for="tue in dopingData.tueApplications" :key="tue.id" 
                             class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-bold text-purple-800" v-text="tue.medication"></h4>
                                    <p class="text-purple-700">Diagnostic: <span v-text="tue.diagnosis"></span></p>
                                    <p class="text-purple-700">Médecin: <span v-text="tue.doctor"></span></p>
                                    
                                    <!-- Statut de la demande -->
                                    <div class="mt-3 bg-white p-3 rounded border">
                                        <h5 class="font-bold text-gray-700 mb-2">📝 Statut de la Demande</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <span class="font-bold text-gray-600">Date demande:</span>
                                                <span v-text="tue.applicationDate"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Statut:</span>
                                                <span :class="[
                                                    'ml-1 px-2 py-1 rounded text-xs font-bold',
                                                    tue.status === 'Approuvée' ? 'bg-green-100 text-green-800' : 
                                                    tue.status === 'En attente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                                                ]" v-text="tue.status"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Date réponse:</span>
                                                <span v-text="tue.responseDate || 'En attente'"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-600">Validité:</span>
                                                <span v-text="tue.validityPeriod || 'À déterminer'"></span>
                                            </div>
                                        </div>
                                        
                                        <div v-if="tue.notes" class="mt-2 text-xs text-gray-600">
                                            <span class="font-bold">Notes:</span> <span v-text="tue.notes"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-purple-500 text-white px-3 py-1 rounded text-xs hover:bg-purple-600">
                                        <i class="fas fa-eye mr-1"></i>Voir
                                    </button>
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-download mr-1"></i>Télécharger
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertes et notifications -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🚨 Alertes & Notifications</h3>
                    <div class="space-y-3">
                        <div v-for="alert in dopingData.alerts" :key="alert.id" 
                             :class="[
                                 'p-4 rounded-lg border-l-4',
                                 alert.type === 'warning' ? 'bg-yellow-50 border-yellow-500' : '',
                                 alert.type === 'info' ? 'bg-blue-50 border-blue-500' : '',
                                 alert.type === 'success' ? 'bg-green-50 border-green-500' : '',
                                 alert.type === 'danger' ? 'bg-red-50 border-red-500' : ''
                             ]">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p :class="[
                                        'font-medium',
                                        alert.type === 'warning' ? 'text-yellow-800' : '',
                                        alert.type === 'info' ? 'text-blue-800' : '',
                                        alert.type === 'success' ? 'text-green-800' : '',
                                        alert.type === 'danger' ? 'text-red-800' : ''
                                    ]" v-text="alert.message"></p>
                                    <p class="text-sm text-gray-600 mt-1">Priorité: <span v-text="alert.priority"></span> | Date: <span v-text="alert.date"></span></p>
                                </div>
                                <div class="ml-4">
                                    <span v-if="alert.type === 'warning'" class="text-yellow-600 text-2xl">⚠️</span>
                                    <span v-else-if="alert.type === 'info'" class="text-blue-600 text-2xl">ℹ️</span>
                                    <span v-else-if="alert.type === 'success'" class="text-green-600 text-2xl">✅</span>
                                    <span v-else-if="alert.type === 'danger'" class="text-red-600 text-2xl">🚨</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">⚡ Actions Rapides</h3>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                            <i class="fas fa-plus mr-2"></i>Nouvelle TUE
                        </button>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                            <i class="fas fa-file-medical mr-2"></i>Déclarer Médicament
                        </button>
                        <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-download mr-2"></i>Exporter Historique
                        </button>
                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-question-circle mr-2"></i>Aide & Support
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Onglet SDOH -->
            <div v-show="activeTab === 'sdoh'" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">🌍 Déterminants Sociaux de la Santé (SDOH)</h2>
                
                <!-- Vue d'ensemble des SDOH -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Score Global SDOH</h3>
                        <p class="text-3xl font-bold" v-text="sdohData.globalScore + '/100'"></p>
                        <p class="text-sm opacity-90">Excellent</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Facteurs de Risque</h3>
                        <p class="text-3xl font-bold" v-text="sdohData.riskFactors.length"></p>
                        <p class="text-sm opacity-90">Identifiés</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Interventions</h3>
                        <p class="text-3xl font-bold" v-text="sdohData.interventions.length"></p>
                        <p class="text-sm opacity-90">En cours</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                        <h3 class="font-bold mb-2">Dernière Évaluation</h3>
                        <p class="text-2xl font-bold" v-text="sdohData.lastAssessment"></p>
                        <p class="text-sm opacity-90">Mise à jour</p>
                    </div>
                </div>
                
                <!-- Catégories SDOH -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Conditions Économiques -->
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <h3 class="font-bold text-blue-800 mb-3">💰 Conditions Économiques</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Revenus familiaux</span>
                                <span class="font-bold text-blue-800" v-text="sdohData.economic.income"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Emploi des parents</span>
                                <span class="font-bold text-blue-800" v-text="sdohData.economic.parentalEmployment"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Sécurité alimentaire</span>
                                <span class="font-bold text-blue-800" v-text="sdohData.economic.foodSecurity"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Logement</span>
                                <span class="font-bold text-blue-800" v-text="sdohData.economic.housing"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Environnement Social -->
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <h3 class="font-bold text-green-800 mb-3">🏘️ Environnement Social</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Support familial</span>
                                <span class="font-bold text-green-800" v-text="sdohData.social.familySupport + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Réseau social</span>
                                <span class="font-bold text-green-800" v-text="sdohData.social.socialNetwork + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Communauté</span>
                                <span class="font-bold text-green-800" v-text="sdohData.social.community + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Relations</span>
                                <span class="font-bold text-green-800" v-text="sdohData.social.relationships + '/10'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accès aux Soins -->
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <h3 class="font-bold text-purple-800 mb-3">🏥 Accès aux Soins</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Couverture santé</span>
                                <span class="font-bold text-purple-800" v-text="sdohData.healthcare.coverage"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Proximité soins</span>
                                <span class="font-bold text-purple-800" v-text="sdohData.healthcare.proximity"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Qualité soins</span>
                                <span class="font-bold text-purple-800" v-text="sdohData.healthcare.quality + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Barrières</span>
                                <span class="font-bold text-purple-800" v-text="sdohData.healthcare.barriers"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Éducation & Littératie -->
                    <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                        <h3 class="font-bold text-orange-800 mb-3">📚 Éducation & Littératie</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-orange-700">Niveau éducation</span>
                                <span class="font-bold text-orange-800" v-text="sdohData.education.level"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-orange-700">Littératie santé</span>
                                <span class="font-bold text-orange-800" v-text="sdohData.education.healthLiteracy + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-orange-700">Accès information</span>
                                <span class="font-bold text-orange-800" v-text="sdohData.education.informationAccess + '/10'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-orange-700">Support scolaire</span>
                                <span class="font-bold text-orange-800" v-text="sdohData.education.schoolSupport + '/10'"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Facteurs de Risque Identifiés -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">⚠️ Facteurs de Risque Identifiés</h3>
                    <div class="space-y-3">
                        <div v-for="risk in sdohData.riskFactors" :key="risk.id" 
                             class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span :class="[
                                            'px-3 py-1 rounded-full text-xs font-bold mr-3',
                                            risk.severity === 'Élevé' ? 'bg-red-100 text-red-800' : 
                                            risk.severity === 'Modéré' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800'
                                        ]" v-text="risk.severity"></span>
                                        <h4 class="font-bold text-red-800" v-text="risk.factor"></h4>
                                    </div>
                                    <p class="text-red-700 mb-2" v-text="risk.description"></p>
                                    <p class="text-sm text-red-600">Impact: <span v-text="risk.impact"></span> | Catégorie: <span v-text="risk.category"></span></p>
                                    
                                    <!-- Interventions recommandées -->
                                    <div class="mt-3 bg-white p-3 rounded border">
                                        <h5 class="font-bold text-red-700 mb-2">💡 Interventions Recommandées</h5>
                                        <div class="space-y-2">
                                            <div v-for="intervention in risk.recommendedInterventions" :key="intervention" 
                                                 class="text-sm text-red-600">
                                                • <span v-text="intervention"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                        <i class="fas fa-eye mr-1"></i>Détails
                                    </button>
                                    <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-plus mr-1"></i>Intervention
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Interventions en Cours -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">🔄 Interventions en Cours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="intervention in sdohData.interventions" :key="intervention.id" 
                             class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h4 class="font-bold text-green-800 mb-2" v-text="intervention.name"></h4>
                            <p class="text-green-700 mb-3" v-text="intervention.description"></p>
                            
                            <!-- Progression -->
                            <div class="mb-3">
                                <div class="flex justify-between text-sm text-green-600 mb-1">
                                    <span>Progression</span>
                                    <span v-text="intervention.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-green-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" :style="{width: intervention.progress + '%'}"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Début: <span v-text="intervention.startDate"></span></span>
                                <span>Fin prévue: <span v-text="intervention.endDate"></span></span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="mt-3 flex space-x-2">
                                <button class="bg-green-500 text-white px-2 py-1 rounded text-xs hover:bg-green-600">
                                    <i class="fas fa-edit mr-1"></i>Modifier
                                </button>
                                <button class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">
                                    <i class="fas fa-chart-line mr-1"></i>Suivi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3 text-gray-800">⚡ Actions Rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Évaluation
                        </button>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all">
                            <i class="fas fa-chart-bar mr-2"></i>Rapport SDOH
                        </button>
                        <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-users mr-2"></i>Références
                        </button>
                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-question-circle mr-2"></i>Aide & Support
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Autres onglets -->
            <div v-show="activeTab !== 'performance' && activeTab !== 'notifications' && activeTab !== 'health' && activeTab !== 'medical' && activeTab !== 'devices' && activeTab !== 'doping' && activeTab !== 'sdoh'" class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4" v-text="'Onglet actif : ' + activeTab"></h2>
                <p class="text-gray-600">Contenu de l'onglet sélectionné</p>
            </div>
            
            <!-- Test Vue.js -->
            <div class="mt-4 p-4 bg-blue-100 rounded">
                <h3 class="font-bold text-blue-800">Test Vue.js</h3>
                <p>Message: <span v-text="message"></span></p>
                <p>Compteur: <span v-text="counter"></span></p>
                <button @click="increment" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Incrémenter</button>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    activeTab: 'performance',
                    message: 'Vue.js fonctionne dans Laravel!',
                    counter: 0,
                    activeNotificationFilter: 'Toutes',
                    navigationTabs: [
                        { id: 'performance', name: 'Performance', icon: 'fas fa-chart-line', count: null },
                        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell', count: 12 },
                        { id: 'health', name: 'Santé & Bien-être', icon: 'fas fa-heart', count: null },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md', count: 4 },
                        { id: 'devices', name: 'Devices', icon: 'fas fa-mobile-alt', count: 3 },
                        { id: 'doping', name: 'Dopage', icon: 'fas fa-exclamation-triangle', count: 2 },
                        { id: 'sdoh', name: 'SDOH', icon: 'fas fa-globe', count: 5 }
                    ],
                    
                    // Données de performance statiques pour l'instant
                    offensiveStats: [
                        { name: 'Buts', value: 15 },
                        { name: 'Passes', value: 12 },
                        { name: 'Tirs', value: 45 },
                        { name: 'Tirs cadrés', value: 28 }
                    ],
                    physicalStats: [
                        { name: 'Vitesse', value: '85 km/h' },
                        { name: 'Endurance', value: '92%' },
                        { name: 'Force', value: '78%' },
                        { name: 'Agilité', value: '88%' }
                    ],
                    
                    seasonSummary: {
                        goals: { total: 15, trend: '+3', avg: 0.6 },
                        assists: { total: 12, trend: '+2', avg: 0.5 },
                        matches: { total: 25, rating: 8.2, distance: '245 km' }
                    },
                    
                    performanceEvolution: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                        ratings: [7.5, 7.8, 8.1, 8.3, 8.5, 8.2],
                        goals: [2, 3, 2, 4, 3, 1],
                        assists: [1, 2, 3, 2, 3, 1]
                    },
                    
                    // Données de notifications statiques
                    notificationData: {
                        nationalTeam: [
                            { id: 1, title: 'Sélection équipe nationale', message: 'Convoqué pour le match France - Allemagne', date: '2025-08-15', type: 'urgent' },
                            { id: 2, title: 'Stage de préparation', message: 'Stage de 3 jours à Clairefontaine', date: '2025-08-20', type: 'info' }
                        ],
                        trainingSessions: [
                            { id: 3, title: 'Entraînement technique', message: 'Séance de tirs et passes', date: '2025-08-12', type: 'training' },
                            { id: 4, title: 'Récupération', message: 'Séance de récupération active', date: '2025-08-13', type: 'recovery' }
                        ],
                        matches: [
                            { id: 5, title: 'Prochain match', message: 'PSG vs OM - 20h00', date: '2025-08-18', type: 'match' },
                            { id: 6, title: 'Résumé match précédent', message: 'Victoire 3-1 contre Lyon', date: '2025-08-10', type: 'result' }
                        ],
                        medicalAppointments: [
                            { id: 7, title: 'Contrôle médical', message: 'Bilan de forme complet', date: '2025-08-16', type: 'medical' },
                            { id: 8, title: 'Suivi blessure', message: 'Contrôle genou droit', date: '2025-08-17', type: 'injury' }
                        ],
                        socialMedia: [
                            { id: 9, title: 'Mention Twitter', message: '@FIFAcom vous félicite', date: '2025-08-11', type: 'social' },
                            { id: 10, title: 'Article presse', message: 'Interview dans L\'Équipe', date: '2025-08-09', type: 'media' }
                        ]
                    },
                    
                    // Données de santé statiques complètes
                    healthData: {
                        globalScore: 85,
                        physicalScore: 88,
                        mentalScore: 78,
                        sleepScore: 82,
                        socialScore: 85,
                        injuryRisk: 15,
                        recoveryScore: 76,
                        readinessScore: 79,
                        fatigueIndex: 23,
                        hydrationStatus: 'optimal',
                        bmi: 22.4,
                        bodyComposition: {
                            muscleMass: 78,
                            bodyFat: 12,
                            boneDensity: 95,
                            visceralFat: 8,
                            waterPercentage: 62
                        },
                        vitals: {
                            heartRate: { current: 72, resting: 58, max: 185, variability: 45 },
                            bloodPressure: { systolic: 120, diastolic: 80, mean: 93, pulse: 72 },
                            oxygenSaturation: 98,
                            temperature: 36.8,
                            respiratoryRate: 16,
                            bloodGlucose: 5.2
                        },
                        recentMetrics: [
                            { date: '2025-08-10', sleepHours: 8.2, stressLevel: 3, energyLevel: 8, hydration: 2.3, steps: 12500, calories: 2850, trainingLoad: 75, recovery: 8 },
                            { date: '2025-08-09', sleepHours: 7.8, stressLevel: 4, energyLevel: 7, hydration: 2.1, steps: 11800, calories: 3100, trainingLoad: 85, recovery: 6 },
                            { date: '2025-08-08', sleepHours: 8.5, stressLevel: 2, energyLevel: 9, hydration: 2.5, steps: 13200, calories: 2950, trainingLoad: 65, recovery: 9 },
                            { date: '2025-08-07', sleepHours: 7.5, stressLevel: 5, energyLevel: 6, hydration: 1.8, steps: 9800, calories: 2700, trainingLoad: 90, recovery: 5 },
                            { date: '2025-08-06', sleepHours: 8.0, stressLevel: 3, energyLevel: 8, hydration: 2.2, steps: 11500, calories: 3000, trainingLoad: 70, recovery: 7 }
                        ],
                        recommendations: [
                            'Maintenir 8h de sommeil par nuit',
                            'Pratiquer la méditation 10min avant le coucher',
                            'Augmenter l\'hydratation (2.5L/jour)',
                            'Séances de récupération active 2x/semaine',
                            'Étirements post-entraînement obligatoires',
                            'Contrôle tension artérielle 2x/jour',
                            'Surveillance glycémie post-match',
                            'Test VO2Max mensuel',
                            'Bilan composition corporelle hebdomadaire'
                        ],
                        nutritionData: {
                            dailyCalories: 3200,
                            protein: 180,
                            carbs: 400,
                            fats: 90,
                            hydration: 2.5,
                            fiber: 35,
                            sodium: 2300,
                            potassium: 3500,
                            calcium: 1200,
                            iron: 18,
                            supplements: ['Vitamine D', 'Oméga-3', 'Magnésium', 'BCAA', 'Créatine', 'Beta-Alanine'],
                            mealTiming: {
                                preWorkout: '2h avant',
                                postWorkout: '30min après',
                                bedtime: '1h avant'
                            }
                        },
                        fitnessData: {
                            vo2Max: 58,
                            lactateThreshold: 85,
                            muscleMass: 78,
                            bodyFat: 12,
                            flexibility: 75,
                            balance: 82,
                            power: 88,
                            speed: 92,
                            agility: 79,
                            coordination: 85,
                            reactionTime: 0.18,
                            verticalJump: 65,
                            benchPress: 120,
                            squat: 160,
                            deadlift: 180
                        },
                        sleepData: {
                            deepSleep: 2.1,
                            remSleep: 1.8,
                            lightSleep: 4.3,
                            sleepEfficiency: 87,
                            sleepLatency: 12,
                            wakeUps: 1,
                            totalSleepTime: 8.2,
                            sleepCycles: 5,
                            remLatency: 90,
                            sleepQuality: 'excellent'
                        },
                        stressData: {
                            currentLevel: 3,
                            trend: 'stable',
                            triggers: ['Entraînement intensif', 'Matchs importants', 'Voyages'],
                            copingStrategies: ['Méditation', 'Respiration 4-7-8', 'Marche en nature'],
                            cortisolLevel: 'normal',
                            adrenalineLevel: 'elevated',
                            recoveryTime: '24h'
                        },
                        performanceMetrics: {
                            sprintSpeed: '32.5 km/h',
                            endurance: '85%',
                            strength: '88%',
                            power: '92%',
                            agility: '79%',
                            coordination: '85%',
                            mentalFocus: '82%',
                            decisionMaking: '88%'
                        },
                        healthTrends: {
                            weight: { current: 75, trend: 'stable', target: 74 },
                            bodyFat: { current: 12, trend: 'decreasing', target: 10 },
                            muscleMass: { current: 78, trend: 'increasing', target: 80 },
                            vo2Max: { current: 58, trend: 'increasing', target: 60 }
                        },
                        alerts: [
                            { type: 'warning', message: 'Hydratation légèrement insuffisante hier', priority: 'medium' },
                            { type: 'info', message: 'Récupération optimale après match', priority: 'low' },
                            { type: 'success', message: 'Score de sommeil excellent cette semaine', priority: 'low' }
                        ]
                    },
                    
                    // Données médicales statiques complètes
                    medicalData: {
                        recentHealthRecords: [
                            { id: 1, date: '2025-08-10', type: 'Bilan complet', status: 'completed', doctor: 'Dr. Martin', notes: 'Excellente forme générale, tous paramètres normaux', bloodPressure: '120/80', heartRate: 72, temperature: 36.8, weight: 75, height: 180, bmi: 23.1 },
                            { id: 2, date: '2025-08-05', type: 'Contrôle post-match', status: 'completed', doctor: 'Dr. Dubois', notes: 'Récupération normale, surveillance genou droit', bloodPressure: '125/82', heartRate: 78, temperature: 37.1, weight: 75.2, height: 180, bmi: 23.2 },
                            { id: 3, date: '2025-07-28', type: 'Bilan pré-saison', status: 'completed', doctor: 'Dr. Martin', notes: 'Prêt pour la compétition, recommandations nutrition', bloodPressure: '118/78', heartRate: 70, temperature: 36.7, weight: 74.8, height: 180, bmi: 23.1 }
                        ],
                        pcmas: [
                            { id: 1, date: '2025-08-08', type: 'Évaluation complète', status: 'completed', score: 85, category: 'Excellent', notes: 'Toutes les composantes au-dessus de la moyenne', components: { physical: 88, mental: 82, social: 87, technical: 86 } },
                            { id: 2, date: '2025-07-15', type: 'Suivi mensuel', status: 'completed', score: 82, category: 'Très bon', notes: 'Légère amélioration de la composante mentale', components: { physical: 85, mental: 79, social: 84, technical: 83 } },
                            { id: 3, date: '2025-06-20', type: 'Évaluation initiale', status: 'completed', score: 78, category: 'Bon', notes: 'Bases solides, axes d\'amélioration identifiés', components: { physical: 80, mental: 75, social: 78, technical: 79 } }
                        ],
                        medicalPredictions: [
                            { id: 1, date: '2025-08-10', riskLevel: 'Faible', prediction: 'Maintien de la forme actuelle', confidence: 85, recommendations: 'Continuer le programme actuel', riskFactors: ['Fatigue légère', 'Charge d\'entraînement élevée'], prevention: 'Récupération active, nutrition optimisée' },
                            { id: 2, date: '2025-08-05', riskLevel: 'Modéré', prediction: 'Risque de fatigue accumulée', confidence: 72, recommendations: 'Augmenter la récupération', riskFactors: ['Sessions intensives consécutives', 'Sommeil insuffisant'], prevention: 'Récupération passive, étirements' },
                            { id: 3, date: '2025-07-28', riskLevel: 'Faible', prediction: 'Amélioration continue', confidence: 88, recommendations: 'Maintenir l\'entraînement progressif', riskFactors: ['Aucun facteur de risque identifié'], prevention: 'Programme d\'entraînement équilibré' }
                        ],
                        injuries: [
                            { id: 1, type: 'Entorse genou droit', date: '2025-06-15', severity: 'Légère', status: 'Guérie', recoveryTime: '3 semaines', notes: 'Rééducation complète, retour au jeu autorisé', location: 'Genou droit', mechanism: 'Torsion lors d\'un changement de direction', treatment: 'Glaçage, compression, élévation, rééducation' },
                            { id: 2, type: 'Contracture ischio-jambiers', date: '2025-05-20', severity: 'Modérée', status: 'En cours', recoveryTime: '2 semaines', notes: 'Récupération progressive, étirements quotidiens', location: 'Ischio-jambiers droit', mechanism: 'Surcharge d\'entraînement', treatment: 'Étirements, massages, repos relatif' },
                            { id: 3, type: 'Contusion cuisse', date: '2025-04-10', severity: 'Légère', status: 'Guérie', recoveryTime: '1 semaine', notes: 'Résolution spontanée', location: 'Cuisse antérieure gauche', mechanism: 'Contact direct lors d\'un tacle', treatment: 'Glaçage, repos, reprise progressive' }
                        ],
                        illnesses: [
                            { id: 1, type: 'Infection respiratoire', date: '2025-07-10', status: 'Guérie', symptoms: 'Toux, fatigue', treatment: 'Antibiotiques, repos', notes: 'Résolution complète en 5 jours', duration: '5 jours', complications: 'Aucune', returnToPlay: '7 jours' },
                            { id: 2, type: 'Gastro-entérite', date: '2025-06-25', status: 'Guérie', symptoms: 'Nausées, diarrhée', treatment: 'Réhydratation, régime', notes: 'Récupération rapide', duration: '3 jours', complications: 'Déshydratation légère', returnToPlay: '5 jours' }
                        ],
                        alerts: [
                            { id: 1, type: 'warning', message: 'Surveillance genou droit post-entorse', priority: 'medium', date: '2025-08-05', category: 'Blessure', action: 'Contrôle quotidien, signaler toute douleur' },
                            { id: 2, type: 'info', message: 'Contrôle tension artérielle recommandé', priority: 'low', date: '2025-08-10', category: 'Prévention', action: 'Mesure 2x/jour pendant 1 semaine' },
                            { id: 3, type: 'success', message: 'Tous les bilans médicaux à jour', priority: 'low', date: '2025-08-10', category: 'Suivi', action: 'Continuer le programme actuel' }
                        ],
                        recommendations: [
                            'Contrôle tension artérielle 2x/jour pendant 1 semaine',
                            'Étirements ischio-jambiers 3x/jour',
                            'Surveillance genou droit lors des entraînements',
                            'Bilan PCMA mensuel maintenu',
                            'Contrôle glycémie post-match',
                            'Hydratation optimale (2.5L/jour minimum)',
                            'Récupération active post-entraînement',
                            'Surveillance fatigue et sommeil',
                            'Étirements pré et post-match obligatoires'
                        ],
                        medications: [
                            { name: 'Vitamine D', dosage: '2000 UI', frequency: '1x/jour', duration: 'Continue', notes: 'Supplémentation hivernale', startDate: '2025-09-01', endDate: '2025-05-31', sideEffects: 'Aucun', interactions: 'Aucune' },
                            { name: 'Oméga-3', dosage: '1000mg', frequency: '2x/jour', duration: 'Continue', notes: 'Anti-inflammatoire naturel', startDate: '2025-01-01', endDate: 'Continue', sideEffects: 'Aucun', interactions: 'Aucune' },
                            { name: 'Magnésium', dosage: '400mg', frequency: '1x/jour', duration: 'Continue', notes: 'Récupération musculaire', startDate: '2025-06-01', endDate: 'Continue', sideEffects: 'Diarrhée légère possible', interactions: 'Aucune' }
                        ],
                        upcomingAppointments: [
                            { id: 1, date: '2025-08-16', time: '14h00', type: 'Contrôle médical', doctor: 'Dr. Martin', location: 'Centre médical PSG', duration: '45 min', purpose: 'Bilan de forme post-matchs' },
                            { id: 2, date: '2025-08-20', time: '10h30', type: 'Bilan PCMA', doctor: 'Dr. Dubois', location: 'Institut du Sport', duration: '60 min', purpose: 'Évaluation complète des composantes' },
                            { id: 3, date: '2025-08-25', time: '16h00', type: 'Suivi genou', doctor: 'Dr. Martin', location: 'Centre médical PSG', duration: '30 min', purpose: 'Contrôle récupération post-entorse' }
                        ],
                        medicalTests: [
                            { id: 1, type: 'Analyse sang', date: '2025-08-10', status: 'Completed', results: 'Normaux', hemoglobin: 15.2, whiteCells: 6.8, platelets: 250, glucose: 5.2, cholesterol: 4.8 },
                            { id: 2, type: 'Test cardiaque', date: '2025-08-05', status: 'Completed', results: 'Normal', heartRate: 72, bloodPressure: '120/80', ecg: 'Normal', stressTest: 'Excellent' },
                            { id: 3, type: 'Test respiratoire', date: '2025-07-28', status: 'Completed', results: 'Normal', vo2Max: 58, lungCapacity: 5.2, respiratoryRate: 16, oxygenSaturation: 98 }
                        ],
                        vaccinations: [
                            { id: 1, name: 'COVID-19', date: '2025-01-15', status: 'Completed', nextDue: '2025-07-15', notes: 'Vaccin bivalent' },
                            { id: 2, name: 'Grippe', date: '2024-10-20', status: 'Completed', nextDue: '2025-10-20', notes: 'Vaccin saisonnier' },
                            { id: 3, name: 'Tétanos', date: '2024-06-10', status: 'Completed', nextDue: '2029-06-10', notes: 'Rappel décennal' }
                        ],
                        familyHistory: [
                            { condition: 'Diabète type 2', relation: 'Père', age: '55', notes: 'Contrôlé par régime et médicaments' },
                            { condition: 'Hypertension', relation: 'Mère', age: '52', notes: 'Légère, surveillance régulière' },
                            { condition: 'Asthme', relation: 'Frère', age: '25', notes: 'Contrôlé, pas de crise depuis 2 ans' }
                        ],
                        lifestyleFactors: {
                            smoking: 'Jamais fumé',
                            alcohol: 'Occasionnel (1-2 verres/semaine)',
                            drugs: 'Aucun',
                            sleep: '7-8h/nuit',
                            stress: 'Géré par méditation et sport',
                            diet: 'Équilibré, riche en protéines'
                        },
                        emergencyContacts: [
                            { name: 'Dr. Martin', role: 'Médecin traitant', phone: '+33 1 42 76 54 32', email: 'dr.martin@psg.fr' },
                            { name: 'Dr. Dubois', role: 'Médecin du sport', phone: '+33 1 45 67 89 12', email: 'dr.dubois@sport.fr' },
                            { name: 'Centre Urgences PSG', role: 'Urgences 24h/24', phone: '+33 1 42 76 54 00', email: 'urgences@psg.fr' }
                        ]
                    },
                    
                    // Données de performance complètes
                    performanceData: {
                        seasonSummary: {
                            goals: { total: 15, trend: '+3', average: 0.6, accuracy: 78 },
                            assists: { total: 12, trend: '+2', average: 0.5, accuracy: 85 },
                            matches: { total: 25, rating: 8.2, distance: '245 km', wins: 18, draws: 4, losses: 3 },
                            minutes: { total: 2250, average: 90, availability: 95 }
                        },
                        offensiveStats: [
                            { name: 'Buts', value: 15 },
                            { name: 'Passes décisives', value: 12 },
                            { name: 'Tirs', value: 45 },
                            { name: 'Tirs cadrés', value: 28 },
                            { name: 'Précision tirs', value: '62%' },
                            { name: 'Occasions créées', value: 32 },
                            { name: 'Dribbles réussis', value: 67 },
                            { name: 'Centres réussis', value: 23 }
                        ],
                        physicalStats: [
                            { name: 'Vitesse max', value: '32.5 km/h' },
                            { name: 'Endurance', value: '92%' },
                            { name: 'Force', value: '78%' },
                            { name: 'Agilité', value: '88%' },
                            { name: 'Distance/match', value: '9.8 km' },
                            { name: 'Sprint', value: '15/match' },
                            { name: 'Récupération', value: '85%' },
                            { name: 'Flexibilité', value: '82%' }
                        ],
                        technicalStats: [
                            { name: 'Précision passes', value: '89%' },
                            { name: 'Passes longues', value: '78%' },
                            { name: 'Contrôle balle', value: '91%' },
                            { name: 'Dribbles', value: '73%' },
                            { name: 'Centres', value: '68%' },
                            { name: 'Tirs lointains', value: '45%' },
                            { name: 'Tacles', value: '82%' },
                            { name: 'Interceptions', value: '79%' }
                        ],
                        defensiveStats: [
                            { name: 'Tacles réussis', value: 45 },
                            { name: 'Interceptions', value: 38 },
                            { name: 'Dégagements', value: 23 },
                            { name: 'Duels gagnés', value: '67%' },
                            { name: 'Aériens gagnés', value: '58%' },
                            { name: 'Fautes commises', value: 12 },
                            { name: 'Cartons jaunes', value: 3 },
                            { name: 'Cartons rouges', value: 0 }
                        ],
                        performanceEvolution: {
                            'Janvier': { rating: 7.5, goals: 2, assists: 1, minutes: 270 },
                            'Février': { rating: 7.8, goals: 3, assists: 2, minutes: 270 },
                            'Mars': { rating: 8.1, goals: 2, assists: 3, minutes: 270 },
                            'Avril': { rating: 8.3, goals: 4, assists: 2, minutes: 270 },
                            'Mai': { rating: 8.5, goals: 3, assists: 3, minutes: 270 },
                            'Juin': { rating: 8.2, goals: 1, assists: 1, minutes: 180 }
                        },
                        recentMatches: [
                            {
                                id: 1,
                                homeTeam: 'PSG',
                                awayTeam: 'OM',
                                result: 'Victoire',
                                score: '3-1',
                                date: '15/08/2025',
                                playerPerformance: {
                                    rating: 8.5,
                                    goals: 1,
                                    assists: 1,
                                    minutes: 90,
                                    shots: 4,
                                    accuracy: 75,
                                    distance: 10.2,
                                    maxSpeed: 32.1
                                }
                            },
                            {
                                id: 2,
                                homeTeam: 'Lyon',
                                awayTeam: 'PSG',
                                result: 'Victoire',
                                score: '2-0',
                                date: '08/08/2025',
                                playerPerformance: {
                                    rating: 8.0,
                                    goals: 0,
                                    assists: 2,
                                    minutes: 90,
                                    shots: 3,
                                    accuracy: 67,
                                    distance: 9.8,
                                    maxSpeed: 31.8
                                }
                            },
                            {
                                id: 3,
                                homeTeam: 'PSG',
                                awayTeam: 'Monaco',
                                result: 'Nul',
                                score: '1-1',
                                date: '01/08/2025',
                                playerPerformance: {
                                    rating: 7.5,
                                    goals: 1,
                                    assists: 0,
                                    minutes: 90,
                                    shots: 5,
                                    accuracy: 80,
                                    distance: 10.5,
                                    maxSpeed: 32.5
                                }
                            }
                        ],
                        seasonComparison: {
                            goals: { current: 15, previous: 12, change: 25 },
                            assists: { current: 12, previous: 15, change: -20 },
                            rating: { current: 8.2, previous: 7.8, change: 5 },
                            minutes: { current: 2250, previous: 1980, change: 14 }
                        }
                    },
                    
                    // Données des chats d'assistance
                    performanceChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Bonjour ! Je suis votre assistant performance. Comment puis-je vous aider aujourd\'hui ?' },
                            { id: 2, type: 'user', text: 'Comment améliorer mes statistiques de passes ?' },
                            { id: 3, type: 'ai', text: 'Pour améliorer vos passes, je recommande : 1) Entraînement technique quotidien 2) Analyse vidéo de vos matchs 3) Exercices de vision périphérique' }
                        ],
                        newMessage: ''
                    },
                    notificationChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Salut ! Je suis votre assistant notifications. Besoin d\'aide ?' },
                            { id: 2, type: 'user', text: 'Comment filtrer mes notifications importantes ?' },
                            { id: 3, type: 'ai', text: 'Utilisez les filtres en haut : Urgentes, Entraînements, Matchs, Médical. Les notifications urgentes sont automatiquement mises en évidence.' }
                        ],
                        newMessage: ''
                    },
                    healthChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Bonjour ! Assistant santé à votre service. Que souhaitez-vous savoir ?' },
                            { id: 2, type: 'user', text: 'Mon score de sommeil est-il bon ?' },
                            { id: 3, type: 'ai', text: 'Votre score de 82/100 est correct mais peut être amélioré. Essayez de dormir 8h par nuit et évitez les écrans avant le coucher.' }
                        ],
                        newMessage: ''
                    },
                    medicalChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Assistant médical en ligne. Comment puis-je vous aider ?' },
                            { id: 2, type: 'user', text: 'Quand dois-je faire mon prochain contrôle ?' },
                            { id: 3, type: 'ai', text: 'Selon vos données, votre prochain contrôle est prévu dans 2 semaines. Je vous enverrai un rappel 3 jours avant.' }
                        ],
                        newMessage: ''
                    },
                    devicesChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Assistant devices connectés. Besoin d\'aide technique ?' },
                            { id: 2, type: 'user', text: 'Mon device ne se synchronise pas' },
                            { id: 3, type: 'ai', text: 'Essayez de redémarrer votre device et vérifiez la connexion WiFi. Si le problème persiste, utilisez le bouton "Synchroniser Tous".' }
                        ],
                        newMessage: ''
                    },
                    dopingChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Assistant anti-dopage. Questions sur les contrôles ?' },
                            { id: 2, type: 'user', text: 'Quand est mon prochain test ?' },
                            { id: 3, type: 'ai', text: 'Votre prochain test est prévu le 22/08/2025. Restez disponible et évitez tout médicament non déclaré.' }
                        ],
                        newMessage: ''
                    },
                    sdohChat: {
                        messages: [
                            { id: 1, type: 'ai', text: 'Assistant SDOH. Questions sur vos déterminants sociaux ?' },
                            { id: 2, type: 'user', text: 'Comment améliorer mon score social ?' },
                            { id: 3, type: 'ai', text: 'Pour améliorer votre score social : 1) Participez aux activités communautaires 2) Renforcez vos relations familiales 3) Développez votre réseau professionnel.' }
                        ],
                        newMessage: ''
                    },
                    
                    // Données SDOH (Social Determinants of Health)
                    sdohData: {
                        globalScore: 87,
                        lastAssessment: '15/08/2025',
                        riskFactors: [
                            {
                                id: 1,
                                factor: 'Stress familial',
                                description: 'Tensions familiales liées à la pression sportive et aux attentes',
                                severity: 'Modéré',
                                impact: 'Performance et bien-être mental',
                                category: 'Environnement Social',
                                recommendedInterventions: [
                                    'Séances de counseling familial',
                                    'Support psychologique',
                                    'Médiation familiale'
                                ]
                            },
                            {
                                id: 2,
                                factor: 'Accès limité aux soins spécialisés',
                                description: 'Difficulté d\'accès aux spécialistes en médecine du sport',
                                severity: 'Modéré',
                                impact: 'Qualité des soins médicaux',
                                category: 'Accès aux Soins',
                                recommendedInterventions: [
                                    'Réseau de médecins partenaires',
                                    'Téléconsultations',
                                    'Accompagnement logistique'
                                ]
                            },
                            {
                                id: 3,
                                factor: 'Insécurité alimentaire saisonnière',
                                description: 'Variations dans la qualité et disponibilité des aliments',
                                severity: 'Faible',
                                impact: 'Nutrition et récupération',
                                category: 'Conditions Économiques',
                                recommendedInterventions: [
                                    'Programme nutritionnel personnalisé',
                                    'Suppléments alimentaires',
                                    'Éducation nutritionnelle'
                                ]
                            }
                        ],
                        interventions: [
                            {
                                id: 1,
                                name: 'Programme de soutien familial',
                                description: 'Intervention psychosociale pour améliorer la dynamique familiale',
                                progress: 75,
                                startDate: '01/06/2025',
                                endDate: '30/09/2025'
                            },
                            {
                                id: 2,
                                name: 'Réseau de soins spécialisés',
                                description: 'Développement d\'un réseau de médecins partenaires',
                                progress: 60,
                                startDate: '15/05/2025',
                                endDate: '31/12/2025'
                            },
                            {
                                id: 3,
                                name: 'Programme nutritionnel',
                                description: 'Mise en place d\'un plan nutritionnel personnalisé',
                                progress: 90,
                                startDate: '01/03/2025',
                                endDate: '31/08/2025'
                            }
                        ],
                        economic: {
                            income: 'Élevé',
                            parentalEmployment: 'Stable',
                            foodSecurity: 'Sécurisé',
                            housing: 'Propriétaire'
                        },
                        social: {
                            familySupport: 8,
                            socialNetwork: 9,
                            community: 7,
                            relationships: 8
                        },
                        healthcare: {
                            coverage: 'Complète',
                            proximity: 'Proche',
                            quality: 9,
                            barriers: 'Minimales'
                        },
                        education: {
                            level: 'Bac+2',
                            healthLiteracy: 8,
                            informationAccess: 9,
                            schoolSupport: 8
                        }
                    },
                    
                    // Données des devices connectés
                    devicesData: {
                        activeDevices: 3,
                        syncStatus: 'À jour',
                        avgBattery: 78,
                        connectedDevices: [
                            {
                                id: 1,
                                name: 'Apple Watch Series 8',
                                model: 'GPS + Cellular',
                                icon: 'fab fa-apple',
                                color: '#000000',
                                battery: 85,
                                connection: 'WiFi',
                                lastSync: 'Il y a 2h',
                                status: 'Online',
                                recentData: {
                                    steps: 12450,
                                    calories: 2850,
                                    distance: 8.7,
                                    sleep: 7.8
                                }
                            },
                            {
                                id: 2,
                                name: 'Garmin Fenix 7',
                                model: 'Sapphire Solar',
                                icon: 'fas fa-running',
                                color: '#0066cc',
                                battery: 92,
                                connection: 'Bluetooth',
                                lastSync: 'Il y a 1h',
                                status: 'Online',
                                recentData: {
                                    steps: 11800,
                                    calories: 3100,
                                    distance: 9.2,
                                    sleep: 8.1
                                }
                            },
                            {
                                id: 3,
                                name: 'Catapult Vector',
                                model: 'S7',
                                icon: 'fas fa-satellite',
                                color: '#ff6600',
                                battery: 58,
                                connection: 'WiFi',
                                lastSync: 'Il y a 30min',
                                status: 'Online',
                                recentData: {
                                    steps: 13200,
                                    calories: 2950,
                                    distance: 10.1,
                                    sleep: 7.9
                                }
                            }
                        ],
                        connectedApps: [
                            {
                                name: 'Apple Health',
                                type: 'Santé & Fitness',
                                icon: 'fab fa-apple',
                                color: '#ff0000',
                                lastSync: 'Il y a 2h',
                                dataPoints: 15420,
                                status: 'Connected'
                            },
                            {
                                name: 'Garmin Connect',
                                type: 'Sport & Performance',
                                icon: 'fas fa-running',
                                color: '#0066cc',
                                lastSync: 'Il y a 1h',
                                dataPoints: 12850,
                                status: 'Connected'
                            },
                            {
                                name: 'Catapult One',
                                type: 'Performance Sportive',
                                icon: 'fas fa-satellite',
                                color: '#ff6600',
                                lastSync: 'Il y a 30min',
                                dataPoints: 9850,
                                status: 'Connected'
                            },
                            {
                                name: 'Strava',
                                type: 'Réseau Social Sportif',
                                icon: 'fab fa-strava',
                                color: '#fc4c02',
                                lastSync: 'Il y a 4h',
                                dataPoints: 8750,
                                status: 'Connected'
                            },
                            {
                                name: 'MyFitnessPal',
                                type: 'Nutrition & Calories',
                                icon: 'fas fa-utensils',
                                color: '#00a0dc',
                                lastSync: 'Il y a 6h',
                                dataPoints: 6540,
                                status: 'Connected'
                            }
                        ],
                        syncStats: {
                            successful: 24,
                            dataTransferred: 156.8,
                            avgTime: 3.2,
                            errors: 2
                        }
                    },
                    
                    // Données du contrôle anti-dopage
                    dopingData: {
                        totalTests: 8,
                        lastTestDate: '15/08/2025',
                        nextTestDate: '22/08/2025',
                        testHistory: [
                            {
                                id: 1,
                                type: 'Test de compétition',
                                result: 'Négatif',
                                date: '15/08/2025',
                                time: '14h30',
                                location: 'Stade de France',
                                controller: 'Dr. Martin',
                                sampleType: 'Urine',
                                sampleNumber: 'FR-2025-001',
                                status: 'Analysé',
                                laboratory: 'Laboratoire National de Détection du Dopage',
                                detailedResults: {
                                    'Stéroïdes anabolisants': 'Négatif',
                                    'Hormones peptidiques': 'Négatif',
                                    'Bêta-2 agonistes': 'Négatif',
                                    'Diurétiques': 'Négatif',
                                    'Stimulants': 'Négatif'
                                }
                            },
                            {
                                id: 2,
                                type: 'Test hors compétition',
                                result: 'Négatif',
                                date: '08/08/2025',
                                time: '09h15',
                                location: 'Centre d\'entraînement PSG',
                                controller: 'Dr. Dubois',
                                sampleType: 'Sang',
                                sampleNumber: 'FR-2025-002',
                                status: 'Analysé',
                                laboratory: 'Laboratoire National de Détection du Dopage'
                            },
                            {
                                id: 3,
                                type: 'Test de compétition',
                                result: 'Négatif',
                                date: '01/08/2025',
                                time: '16h45',
                                location: 'Parc des Princes',
                                controller: 'Dr. Martin',
                                sampleType: 'Urine',
                                sampleNumber: 'FR-2025-003',
                                status: 'Analysé',
                                laboratory: 'Laboratoire National de Détection du Dopage'
                            }
                        ],
                        prohibitedSubstances: [
                            {
                                name: 'Stéroïdes anabolisants',
                                category: 'S1 - Agents anabolisants',
                                description: 'Substances interdites en permanence',
                                status: 'Interdit'
                            },
                            {
                                name: 'Hormone de croissance',
                                category: 'S2 - Hormones peptidiques',
                                description: 'Substances interdites en permanence',
                                status: 'Interdit'
                            },
                            {
                                name: 'EPO',
                                category: 'S2 - Hormones peptidiques',
                                description: 'Substances interdites en permanence',
                                status: 'Interdit'
                            },
                            {
                                name: 'Clenbutérol',
                                category: 'S1.2 - Bêta-2 agonistes',
                                description: 'Substances interdites en permanence',
                                status: 'Interdit'
                            }
                        ],
                        allowedSubstances: [
                            {
                                name: 'Vitamines',
                                category: 'S6 - Stimulants',
                                description: 'Autorisées dans les limites normales',
                                status: 'Autorisé'
                            },
                            {
                                name: 'Minéraux',
                                category: 'S6 - Stimulants',
                                description: 'Autorisés dans les limites normales',
                                status: 'Autorisé'
                            },
                            {
                                name: 'Protéines',
                                category: 'S6 - Stimulants',
                                description: 'Autorisées dans les limites normales',
                                status: 'Autorisé'
                            },
                            {
                                name: 'Créatine',
                                category: 'S6 - Stimulants',
                                description: 'Autorisée dans les limites normales',
                                status: 'Autorisé'
                            }
                        ],
                        medications: [
                            {
                                name: 'Vitamine D',
                                prescription: 'Dr. Martin - 2000 UI/jour',
                                status: 'En cours',
                                dopingStatus: {
                                    authorized: true,
                                    tueRequired: false,
                                    tueStatus: 'Non applicable',
                                    notes: 'Vitamine autorisée sans TUE'
                                }
                            },
                            {
                                name: 'Oméga-3',
                                prescription: 'Dr. Martin - 1000mg 2x/jour',
                                status: 'En cours',
                                dopingStatus: {
                                    authorized: true,
                                    tueRequired: false,
                                    tueStatus: 'Non applicable',
                                    notes: 'Complément alimentaire autorisé'
                                }
                            },
                            {
                                name: 'Magnésium',
                                prescription: 'Dr. Martin - 400mg/jour',
                                status: 'En cours',
                                dopingStatus: {
                                    authorized: true,
                                    tueRequired: false,
                                    tueStatus: 'Non applicable',
                                    notes: 'Minéral autorisé sans TUE'
                                }
                            }
                        ],
                        tueApplications: [
                            {
                                id: 1,
                                medication: 'Cortisone (injection)',
                                diagnosis: 'Inflammation genou droit',
                                doctor: 'Dr. Martin',
                                applicationDate: '10/08/2025',
                                status: 'Approuvée',
                                responseDate: '12/08/2025',
                                validityPeriod: 'Jusqu\'au 20/08/2025',
                                notes: 'TUE approuvée pour traitement anti-inflammatoire'
                            },
                            {
                                id: 2,
                                medication: 'Salbutamol (inhalateur)',
                                diagnosis: 'Asthme d\'effort',
                                doctor: 'Dr. Dubois',
                                applicationDate: '05/08/2025',
                                status: 'En attente',
                                notes: 'Demande en cours d\'évaluation'
                            }
                        ],
                        alerts: [
                            {
                                id: 1,
                                type: 'info',
                                message: 'Prochain test anti-dopage prévu le 22/08/2025',
                                priority: 'medium',
                                date: '15/08/2025'
                            },
                            {
                                id: 2,
                                type: 'success',
                                message: 'TUE pour cortisone approuvée jusqu\'au 20/08/2025',
                                priority: 'low',
                                date: '12/08/2025'
                            },
                            {
                                id: 3,
                                type: 'warning',
                                message: 'Demande TUE pour salbutamol en attente de réponse',
                                priority: 'medium',
                                date: '05/08/2025'
                            }
                        ]
                    }
                }
            },
            mounted() {
                console.log('Vue.js monté avec succès dans Laravel!');
                console.log('Active tab:', this.activeTab);
                console.log('Navigation tabs:', this.navigationTabs);
                console.log('Navigation tabs length:', this.navigationTabs.length);
                alert('Vue.js est monté dans Laravel! Onglets: ' + this.navigationTabs.length);
            },
            methods: {
                increment() {
                    this.counter++;
                },
                
                // Méthodes pour les chats d'assistance
                sendPerformanceMessage() {
                    if (this.performanceChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.performanceChat.newMessage
                        };
                        this.performanceChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generatePerformanceResponse(this.performanceChat.newMessage)
                            };
                            this.performanceChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.performanceChat.newMessage = '';
                    }
                },
                
                sendNotificationMessage() {
                    if (this.notificationChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.notificationChat.newMessage
                        };
                        this.notificationChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateNotificationResponse(this.notificationChat.newMessage)
                            };
                            this.notificationChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.notificationChat.newMessage = '';
                    }
                },
                
                sendHealthMessage() {
                    if (this.healthChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.healthChat.newMessage
                        };
                        this.healthChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateHealthResponse(this.healthChat.newMessage)
                            };
                            this.healthChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.healthChat.newMessage = '';
                    }
                },
                
                sendMedicalMessage() {
                    if (this.medicalChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.medicalChat.newMessage
                        };
                        this.medicalChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateMedicalResponse(this.medicalChat.newMessage)
                            };
                            this.medicalChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.medicalChat.newMessage = '';
                    }
                },
                
                sendDevicesMessage() {
                    if (this.devicesChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.devicesChat.newMessage
                        };
                        this.devicesChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateDevicesResponse(this.devicesChat.newMessage)
                            };
                            this.devicesChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.devicesChat.newMessage = '';
                    }
                },
                
                sendDopingMessage() {
                    if (this.dopingChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.dopingChat.newMessage
                        };
                        this.dopingChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateDopingResponse(this.dopingChat.newMessage)
                            };
                            this.dopingChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.dopingChat.newMessage = '';
                    }
                },
                
                sendSdohMessage() {
                    if (this.sdohChat.newMessage.trim()) {
                        const userMessage = {
                            id: Date.now(),
                            type: 'user',
                            text: this.sdohChat.newMessage
                        };
                        this.sdohChat.messages.push(userMessage);
                        
                        // Simulation de réponse IA
                        setTimeout(() => {
                            const aiResponse = {
                                id: Date.now() + 1,
                                type: 'ai',
                                text: this.generateSdohResponse(this.sdohChat.newMessage)
                            };
                            this.sdohChat.messages.push(aiResponse);
                        }, 1000);
                        
                        this.sdohChat.newMessage = '';
                    }
                },
                
                // Générateurs de réponses IA
                generatePerformanceResponse(message) {
                    const responses = [
                        'Pour améliorer vos performances, je recommande un entraînement régulier et une récupération optimale.',
                        'Vos statistiques montrent une progression constante. Continuez dans cette direction !',
                        'Analysez vos matchs précédents pour identifier les points d\'amélioration.',
                        'La nutrition et le sommeil sont essentiels pour optimiser vos performances.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateNotificationResponse(message) {
                    const responses = [
                        'Utilisez les filtres pour organiser vos notifications par priorité.',
                        'Les notifications urgentes sont automatiquement mises en évidence.',
                        'Vous pouvez personnaliser vos préférences de notification dans les paramètres.',
                        'Restez informé des événements importants de votre équipe.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateHealthResponse(message) {
                    const responses = [
                        'Votre score de santé global est excellent. Continuez vos bonnes habitudes !',
                        'Le sommeil et l\'hydratation sont cruciaux pour votre récupération.',
                        'Écoutez votre corps et ajustez votre entraînement en conséquence.',
                        'Consultez un professionnel de santé si vous avez des préoccupations.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateMedicalResponse(message) {
                    const responses = [
                        'Suivez les recommandations de votre médecin pour un suivi optimal.',
                        'N\'oubliez pas vos rendez-vous médicaux programmés.',
                        'Signalez immédiatement tout symptôme inhabituel.',
                        'Votre dossier médical est à jour et complet.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateDevicesResponse(message) {
                    const responses = [
                        'Vérifiez la connexion WiFi et redémarrez votre device si nécessaire.',
                        'Utilisez le bouton de synchronisation pour forcer la mise à jour.',
                        'Assurez-vous que votre device est chargé et connecté.',
                        'Contactez le support technique si le problème persiste.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateDopingResponse(message) {
                    const responses = [
                        'Respectez strictement les règles anti-dopage en vigueur.',
                        'Consultez la liste des substances autorisées avant toute prise.',
                        'Signalez immédiatement tout médicament prescrit.',
                        'Votre prochain test est programmé selon le calendrier officiel.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                },
                
                generateSdohResponse(message) {
                    const responses = [
                        'Vos déterminants sociaux influencent directement votre santé et performance.',
                        'Le support familial et communautaire est essentiel pour votre bien-être.',
                        'L\'accès aux soins et à l\'éducation améliore vos perspectives.',
                        'Identifiez et adressez les facteurs de risque pour optimiser votre santé.'
                    ];
                    return responses[Math.floor(Math.random() * responses.length)];
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
