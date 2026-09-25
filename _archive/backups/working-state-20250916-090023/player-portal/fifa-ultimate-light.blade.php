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
        .fifa-ultimate-card {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fifa-rating-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4);
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div id="fifa-app">
        <!-- Hero Zone Simplifiée -->
        <div class="fifa-ultimate-card text-white p-8 m-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <!-- Photo joueur -->
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white/20">
                        <img src="/images/players/{{ $player->photo ?? 'default.jpg' }}" 
                             alt="{{ $player->first_name }} {{ $player->last_name }}"
                             class="w-full h-full object-cover"
                             onerror="this.src='/images/defaults/player_default.png'">
                    </div>
                    
                    <!-- Info joueur -->
                    <div>
                        <h1 class="text-3xl font-bold">{{ $player->first_name }} {{ $player->last_name }}</h1>
                        <p class="text-xl opacity-80">{{ $player->club->name ?? 'Sans club' }}</p>
                        <div class="flex space-x-4 mt-2">
                            <span class="text-sm opacity-70">Age: 37</span>
                            <span class="text-sm opacity-70">Position: RW</span>
                        </div>
                    </div>
                </div>
                
                <!-- Rating FIFA -->
                <div class="fifa-rating-badge text-black p-4 text-center">
                    <div class="text-2xl font-bold">93</div>
                    <div class="text-xs">OVR</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 mb-6">
            <div class="flex space-x-4">
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['fifa-nav-tab px-6 py-3 rounded-lg text-white font-medium', activeTab === tab.id ? 'active' : '']"
                >
                    <i :class="tab.icon" class="mr-2"></i>
                    @{{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Contenu des Tabs -->
        <div class="px-6 pb-6">
            <!-- Tab Performance -->
            <div v-show="activeTab === 'performance'" class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">📈 Performances Récentes</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">12</div>
                            <div class="text-sm text-gray-600">Buts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">8</div>
                            <div class="text-sm text-gray-600">Assists</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">85%</div>
                            <div class="text-sm text-gray-600">Précision passes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Notifications -->
            <div v-show="activeTab === 'notifications'" class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">🔔 Notifications</h3>
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-flag text-blue-600 mr-3"></i>
                            <div>
                                <div class="font-medium">Convocation Équipe de France</div>
                                <div class="text-sm text-gray-600">Rassemblement: 18 Mars 2025</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-futbol text-green-600 mr-3"></i>
                            <div>
                                <div class="font-medium">Entraînement Technique</div>
                                <div class="text-sm text-gray-600">Aujourd'hui 10:00 - 12:00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Santé -->
            <div v-show="activeTab === 'health'" class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">💚 Santé & Bien-être</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">85</div>
                            <div class="text-sm text-gray-600">Score FIT</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">15%</div>
                            <div class="text-sm text-gray-600">Risque blessure</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Médical -->
            <div v-show="activeTab === 'medical'" class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">🏥 Dossier Médical</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">Consultation Cardiologique</div>
                                <div class="text-sm text-gray-600">Dr. Jean Martin • 05/03/2025</div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terminé</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">Examen Orthopédique</div>
                                <div class="text-sm text-gray-600">Dr. Sophie Moreau • 28/02/2025</div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terminé</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-3 mt-4">
                        <button onclick="alert('Fonction de partage')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-share mr-2"></i>Partager
                        </button>
                        <button onclick="alert('Fonction d\\'impression')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-print mr-2"></i>Imprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Dopage -->
            <div v-show="activeTab === 'doping'" class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">🧪 Contrôles Antidopage</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">28/02/2025</div>
                            <div class="text-sm text-gray-600">Dernier contrôle</div>
                            <div class="text-xs text-green-600 mt-1">✅ NÉGATIF</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">15/04/2025</div>
                            <div class="text-sm text-gray-600">Prochain contrôle</div>
                            <div class="text-xs text-blue-600 mt-1">📅 PROGRAMMÉ</div>
                        </div>
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
                        { id: 'health', name: 'Santé', icon: 'fas fa-heart' },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md' },
                        { id: 'doping', name: 'Dopage', icon: 'fas fa-vial' }
                    ]
                }
            },
            mounted() {
                console.log('FIFA Ultimate Light mounted successfully');
            }
        }).mount('#fifa-app');
    </script>
</body>
</html>
