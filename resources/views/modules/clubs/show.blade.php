<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Club - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/test-clubs-view" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux clubs
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Détails du Club</h1>
            </div>
            <div class="flex space-x-3">
                <button @click="editClub" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    ✏️ Modifier
                </button>
                <button @click="deleteClub" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    🗑️ Supprimer
                </button>
            </div>
        </div>

        <!-- Club Details -->
        <div v-if="club" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header du club -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">[[ club.name ]]</h2>
                                <span v-if="club.short_name" class="text-lg font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                                    [[ club.short_name ]]
                                </span>
                            </div>
                            <p class="text-lg text-gray-600">[[ club.city ]], [[ club.country ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(club.status)" class="px-4 py-2 rounded-full text-sm font-medium">
                                [[ club.status === 'active' ? 'Actif' : club.status === 'inactive' ? 'Inactif' : 'En attente' ]]
                            </span>
                        </div>
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">🏆</span>
                            <div>
                                <p class="text-sm text-gray-500">Ligue</p>
                                <p class="font-medium">[[ club.league ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">⚽</span>
                            <div>
                                <p class="text-sm text-gray-500">Division</p>
                                <p class="font-medium">[[ club.division ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">Joueurs</p>
                                <p class="font-medium">[[ club.playerCount ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">🏟️</span>
                            <div>
                                <p class="text-sm text-gray-500">Équipes</p>
                                <p class="font-medium">[[ club.teamCount ]]</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations FIFA Connect -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">⚽</span>
                        Informations FIFA Connect
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">Identifiant FIFA</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-600">FIFA Connect ID:</span>
                                    <span class="font-mono text-sm font-medium text-blue-800">[[ club.fifa_connect_id ]]</span>
                                </div>
                                <div v-if="club.fifa_sync_status" class="flex justify-between">
                                    <span class="text-sm text-blue-600">Statut Sync:</span>
                                    <span class="text-sm font-medium" :class="getSyncStatusClass(club.fifa_sync_status)">
                                        [[ club.fifa_sync_status ]]
                                    </span>
                                </div>
                                <div v-if="club.fifa_sync_date" class="flex justify-between">
                                    <span class="text-sm text-blue-600">Dernière Sync:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ club.fifa_sync_date ]]</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-800 mb-3">Informations du club</h4>
                            <div class="space-y-2">
                                <div v-if="club.founded_year" class="flex justify-between">
                                    <span class="text-sm text-green-600">Fondé en:</span>
                                    <span class="text-sm font-medium text-green-800">[[ club.founded_year ]]</span>
                                </div>
                                <div v-if="club.stadium" class="flex justify-between">
                                    <span class="text-sm text-green-600">Stade:</span>
                                    <span class="text-sm font-medium text-green-800">[[ club.stadium ]]</span>
                                </div>
                                <div v-if="club.stadium_capacity" class="flex justify-between">
                                    <span class="text-sm text-green-600">Capacité:</span>
                                    <span class="text-sm font-medium text-green-800">[[ club.stadium_capacity ]] places</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques et métriques -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">📊</span>
                        Statistiques
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">[[ club.playerCount ]]</div>
                            <div class="text-sm text-blue-600">Joueurs actifs</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">[[ club.teamCount ]]</div>
                            <div class="text-sm text-green-600">Équipes</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">[[ club.founded_year ]]</div>
                            <div class="text-sm text-purple-600">Années d'existence</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            👥 Gérer les joueurs
                        </button>
                        <button class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            ⚽ Gérer les équipes
                        </button>
                        <button class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                            📋 Voir les licences
                        </button>
                        <button class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            🏥 Dossier médical
                        </button>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">📍</span>
                            <span class="text-gray-600">[[ club.city ]], [[ club.country ]]</span>
                        </div>
                        <div v-if="club.stadium" class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">🏟️</span>
                            <span class="text-gray-600">[[ club.stadium ]]</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucun club -->
        <div v-else class="text-center py-12">
            <div class="text-6xl mb-4">🏟️</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Club non trouvé</h3>
            <p class="text-gray-500">Le club demandé n'existe pas ou a été supprimé.</p>
            <a href="/test-clubs-view" class="inline-block mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                Retour aux clubs
            </a>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    club: null,
                    clubId: null
                };
            },
            mounted() {
                // Récupérer l'ID du club depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                this.clubId = urlParams.get('id');
                
                if (this.clubId) {
                    this.loadClub();
                }
            },
            methods: {
                loadClub() {
                    // Simuler le chargement d'un club (en production, ce serait un appel API)
                    const clubs = [
                        {
                            id: 1,
                            name: 'Olympique de Marseille',
                            short_name: 'OM',
                            country: 'France',
                            city: 'Marseille',
                            stadium: 'Orange Vélodrome',
                            stadium_capacity: 67394,
                            founded_year: 1899,
                            fifa_connect_id: 'FIFA_CLUB_FRA_OM',
                            league: 'Ligue 1',
                            division: 'Ligue 1',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            playerCount: 45,
                            teamCount: 8
                        },
                        {
                            id: 2,
                            name: 'Paris Saint-Germain',
                            short_name: 'PSG',
                            country: 'France',
                            city: 'Paris',
                            stadium: 'Parc des Princes',
                            stadium_capacity: 47929,
                            founded_year: 1970,
                            fifa_connect_id: 'FIFA_CLUB_FRA_PSG',
                            league: 'Ligue 1',
                            division: 'Ligue 1',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            playerCount: 52,
                            teamCount: 10
                        },
                        {
                            id: 3,
                            name: 'AS Monaco',
                            short_name: 'ASM',
                            country: 'Monaco',
                            city: 'Monaco',
                            stadium: 'Stade Louis II',
                            stadium_capacity: 18523,
                            founded_year: 1924,
                            fifa_connect_id: 'FIFA_CLUB_MON_ASM',
                            league: 'Ligue 1',
                            division: 'Ligue 1',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            playerCount: 38,
                            teamCount: 6
                        }
                    ];
                    
                    this.club = clubs.find(c => c.id == this.clubId);
                },
                getStatusClass(status) {
                    const classes = {
                        'active': 'bg-green-100 text-green-800',
                        'inactive': 'bg-red-100 text-red-800',
                        'pending': 'bg-yellow-100 text-yellow-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },
                getSyncStatusClass(status) {
                    const classes = {
                        'synced': 'text-green-600',
                        'pending': 'text-yellow-600',
                        'error': 'text-red-600'
                    };
                    return classes[status] || 'text-gray-600';
                },
                editClub() {
                    // Rediriger vers la page d'édition
                    window.location.href = `/test-clubs-view?edit=${this.club.id}`;
                },
                deleteClub() {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer le club "${this.club.name}" ?`)) {
                        // En production, ce serait un appel API
                        alert('Club supprimé (simulation)');
                        window.location.href = '/test-clubs-view';
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
