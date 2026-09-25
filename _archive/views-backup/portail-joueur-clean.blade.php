<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Joueur - FIFA Ultimate Team</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .fifa-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
        }
        .card-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        .tab-active {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div id="app" class="min-h-screen">
        <!-- Header FIFA -->
        <header class="fifa-gradient shadow-2xl">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-trophy text-2xl text-gray-900"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">FIFA Ultimate Team</h1>
                            <p class="text-blue-200">Portail Joueur Pro</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-blue-200">Joueur</p>
                            <p class="font-semibold">Cristiano Ronaldo</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-xl text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sélecteur de Joueur -->
        <div class="bg-gray-800 border-b border-gray-700">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center space-x-4">
                    <label class="text-blue-300 font-medium">Sélectionner un joueur:</label>
                    <select 
                        v-model="selectedPlayerId" 
                        @change="loadPlayerData"
                        class="bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Choisir un joueur...</option>
                        <option v-for="player in players" :key="player.id" :value="player.id">
                            @{{ player.name }} (@{{ player.club?.name || 'Sans club' }})
                        </option>
                    </select>
                    <div v-if="loading" class="flex items-center space-x-2 text-blue-300">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Chargement...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation des Onglets -->
        <nav class="bg-gray-800 border-b border-gray-700">
            <div class="container mx-auto px-6">
                <div class="flex space-x-1">
                    <button 
                        @click="activeTab = 'performance'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'performance' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-chart-line mr-2"></i>Performance
                    </button>
                    <button 
                        @click="activeTab = 'notifications'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'notifications' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </button>
                    <button 
                        @click="activeTab = 'sante'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'sante' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-heartbeat mr-2"></i>Santé
                    </button>
                    <button 
                        @click="activeTab = 'medical'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'medical' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-user-md mr-2"></i>Médical
                    </button>
                    <button 
                        @click="activeTab = 'devices'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'devices' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-mobile-alt mr-2"></i>Devices
                    </button>
                    <button 
                        @click="activeTab = 'dopage'"
                        :class="['px-6 py-3 rounded-t-lg font-medium transition-all duration-200', 
                                activeTab === 'dopage' ? 'tab-active' : 'text-gray-400 hover:text-white hover:bg-gray-700']"
                    >
                        <i class="fas fa-flask mr-2"></i>Dopage
                    </button>
                </div>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <main class="container mx-auto px-6 py-8">
            <!-- Onglet Performance -->
            <div v-if="activeTab === 'performance'" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Graphique Performance -->
                    <div class="bg-gray-800 rounded-xl p-6 card-glow">
                        <h3 class="text-xl font-bold mb-4 text-blue-300">
                            <i class="fas fa-chart-line mr-2"></i>Évolution des Performances
                        </h3>
                        <div class="h-80">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Graphique Comparaison -->
                    <div class="bg-gray-800 rounded-xl p-6 card-glow">
                        <h3 class="text-xl font-bold mb-4 text-green-300">
                            <i class="fas fa-radar-chart mr-2"></i>Comparaison des Statistiques
                        </h3>
                        <div class="h-80">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Statistiques Détaillées -->
                <div class="bg-gray-800 rounded-xl p-6 card-glow">
                    <h3 class="text-xl font-bold mb-6 text-purple-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>Statistiques Détaillées
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-400">88</div>
                            <div class="text-sm text-gray-400">Rating Global</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-400">92</div>
                            <div class="text-sm text-gray-400">Vitesse</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-400">85</div>
                            <div class="text-sm text-gray-400">Technique</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-400">78</div>
                            <div class="text-sm text-gray-400">Mental</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Notifications -->
            <div v-if="activeTab === 'notifications'" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 card-glow">
                    <h3 class="text-xl font-bold mb-6 text-yellow-300">
                        <i class="fas fa-bell mr-2"></i>Centre de Notifications
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-900/30 rounded-lg border-l-4 border-blue-500">
                            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Nouveau Challenge</h4>
                                <p class="text-sm text-gray-300">Défi de vitesse disponible - Améliorez votre sprint !</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-green-900/30 rounded-lg border-l-4 border-green-500">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Objectif Atteint</h4>
                                <p class="text-sm text-gray-300">Félicitations ! Vous avez amélioré votre endurance de 5 points.</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-orange-900/30 rounded-lg border-l-4 border-orange-500">
                            <i class="fas fa-exclamation-triangle text-orange-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Rappel Entraînement</h4>
                                <p class="text-sm text-gray-300">N'oubliez pas votre session d'entraînement technique aujourd'hui.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Santé -->
            <div v-if="activeTab === 'sante'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-800 rounded-xl p-6 card-glow">
                        <h3 class="text-xl font-bold mb-4 text-green-300">
                            <i class="fas fa-heartbeat mr-2"></i>État de Santé
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span>Récupération</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                                <span class="text-green-400 font-bold">85%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Énergie</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 72%"></div>
                                </div>
                                <span class="text-blue-400 font-bold">72%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Hydratation</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-cyan-500 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                                <span class="text-cyan-400 font-bold">90%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6 card-glow">
                        <h3 class="text-xl font-bold mb-4 text-purple-300">
                            <i class="fas fa-dumbbell mr-2"></i>Répartition des Charges
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span>Cardio</span>
                                <span class="text-red-400 font-bold">Élevée</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Musculation</span>
                                <span class="text-yellow-400 font-bold">Modérée</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Technique</span>
                                <span class="text-green-400 font-bold">Faible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Médical -->
            <div v-if="activeTab === 'medical'" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 card-glow">
                    <h3 class="text-xl font-bold mb-6 text-red-300">
                        <i class="fas fa-user-md mr-2"></i>Suivi Médical
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-red-900/30 rounded-lg">
                            <i class="fas fa-thermometer-half text-3xl text-red-400 mb-2"></i>
                            <div class="text-2xl font-bold text-red-400">36.8°C</div>
                            <div class="text-sm text-gray-400">Température</div>
                        </div>
                        <div class="text-center p-4 bg-blue-900/30 rounded-lg">
                            <i class="fas fa-heartbeat text-3xl text-blue-400 mb-2"></i>
                            <div class="text-2xl font-bold text-blue-400">68 BPM</div>
                            <div class="text-sm text-gray-400">Fréquence Cardiaque</div>
                        </div>
                        <div class="text-center p-4 bg-green-900/30 rounded-lg">
                            <i class="fas fa-lungs text-3xl text-green-400 mb-2"></i>
                            <div class="text-2xl font-bold text-green-400">95%</div>
                            <div class="text-sm text-gray-400">Saturation O2</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Devices -->
            <div v-if="activeTab === 'devices'" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 card-glow">
                    <h3 class="text-xl font-bold mb-6 text-cyan-300">
                        <i class="fas fa-mobile-alt mr-2"></i>Mes Devices Connectés
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-cyan-900/30 rounded-lg border border-cyan-500/30">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-watch text-2xl text-cyan-400"></i>
                                <div>
                                    <h4 class="font-semibold">Apple Watch Series 9</h4>
                                    <p class="text-sm text-gray-300">Connecté • 85% batterie</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/30">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-mobile-alt text-2xl text-purple-400"></i>
                                <div>
                                    <h4 class="font-semibold">iPhone 15 Pro</h4>
                                    <p class="text-sm text-gray-300">Connecté • 92% batterie</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Dopage -->
            <div v-if="activeTab === 'dopage'" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6 card-glow">
                    <h3 class="text-xl font-bold mb-6 text-orange-300">
                        <i class="fas fa-flask mr-2"></i>Contrôles Anti-Dopage
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-green-900/30 rounded-lg border-l-4 border-green-500">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Dernier Contrôle</h4>
                                <p class="text-sm text-gray-300">15/01/2025 - Résultat : Négatif</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-blue-900/30 rounded-lg border-l-4 border-blue-500">
                            <i class="fas fa-calendar text-blue-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Prochain Contrôle</h4>
                                <p class="text-sm text-gray-300">15/02/2025 - Contrôle programmé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="{{ asset('js/portail-joueur.js') }}"></script>
</body>
</html>












