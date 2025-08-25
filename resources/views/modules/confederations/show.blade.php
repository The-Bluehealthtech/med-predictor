<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Confédération - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/test-confederations-view" class="flex items-center text-purple-600 hover:text-purple-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux confédérations
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Détails de la Confédération</h1>
            </div>
            <div class="flex space-x-3">
                <button @click="editConfederation" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    ✏️ Modifier
                </button>
                <button @click="deleteConfederation" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    🗑️ Supprimer
                </button>
            </div>
        </div>

        <!-- Confederation Details -->
        <div v-if="confederation" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header de la confédération -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">[[ confederation.name ]]</h2>
                                <span v-if="confederation.acronym" class="text-lg font-medium text-purple-600 bg-purple-100 px-3 py-1 rounded-full">
                                    [[ confederation.acronym ]]
                                </span>
                            </div>
                            <p class="text-lg text-gray-600">[[ confederation.continent ]] - [[ confederation.type ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(confederation.status)" class="px-4 py-2 rounded-full text-sm font-medium">
                                [[ confederation.status === 'active' ? 'Active' : confederation.status === 'inactive' ? 'Inactive' : 'En attente' ]]
                            </span>
                        </div>
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🌍</span>
                            <div>
                                <p class="text-sm text-gray-500">Continent</p>
                                <p class="font-medium">[[ confederation.continent ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🏆</span>
                            <div>
                                <p class="text-sm text-gray-500">Classement FIFA</p>
                                <p class="font-medium">[[ confederation.fifa_ranking || 'N/A' ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🏛️</span>
                            <div>
                                <p class="text-sm text-gray-500">Fédérations affiliées</p>
                                <p class="font-medium">[[ confederation.federationCount || 0 ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🏟️</span>
                            <div>
                                <p class="text-sm text-gray-500">Clubs affiliés</p>
                                <p class="font-medium">[[ confederation.clubCount || 0 ]]</p>
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
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-medium text-purple-800 mb-3">Identifiant FIFA</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-purple-600">FIFA Connect ID:</span>
                                    <span class="font-mono text-sm font-medium text-purple-800">[[ confederation.fifa_connect_id ]]</span>
                                </div>
                                <div v-if="confederation.fifa_ranking" class="flex justify-between">
                                    <span class="text-sm text-purple-600">Classement FIFA:</span>
                                    <span class="text-sm font-medium text-purple-800">[[ confederation.fifa_ranking ]]</span>
                                </div>
                                <div v-if="confederation.fifa_sync_status" class="flex justify-between">
                                    <span class="text-sm text-purple-600">Statut Sync:</span>
                                    <span class="text-sm font-medium" :class="getSyncStatusClass(confederation.fifa_sync_status)">
                                        [[ confederation.fifa_sync_status ]]
                                    </span>
                                </div>
                                <div v-if="confederation.fifa_sync_date" class="flex justify-between">
                                    <span class="text-sm text-purple-600">Dernière Sync:</span>
                                    <span class="text-sm font-medium text-purple-800">[[ confederation.fifa_sync_date ]]</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">Informations de la confédération</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-600">Type:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ confederation.type ]]</span>
                                </div>
                                <div v-if="confederation.founded_year" class="flex justify-between">
                                    <span class="text-sm text-blue-600">Fondée en:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ confederation.founded_year ]]</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-600">Continent:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ confederation.continent ]]</span>
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
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">[[ confederation.federationCount || 0 ]]</div>
                            <div class="text-sm text-purple-600">Fédérations affiliées</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">[[ confederation.clubCount || 0 ]]</div>
                            <div class="text-sm text-blue-600">Clubs affiliés</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">[[ confederation.fifa_ranking || 'N/A' ]]</div>
                            <div class="text-sm text-green-600">Classement FIFA</div>
                        </div>
                    </div>
                </div>

                <!-- Hiérarchie FIFA Connect -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏗️</span>
                        Hiérarchie FIFA Connect
                    </h3>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6">
                        <div class="text-center mb-6">
                            <h4 class="text-lg font-semibold text-purple-800 mb-2">Structure organisationnelle</h4>
                            <p class="text-sm text-purple-600">Organisation hiérarchique des entités footballistiques</p>
                        </div>
                        
                        <!-- Hiérarchie visuelle -->
                        <div class="space-y-4">
                            <!-- FIFA -->
                            <div class="text-center">
                                <div class="inline-block bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-medium">
                                    🌍 FIFA (Organisation mondiale)
                                </div>
                                <div class="text-yellow-600 text-sm mt-1">↓</div>
                            </div>
                            
                            <!-- Confédération actuelle -->
                            <div class="text-center">
                                <div class="inline-block bg-purple-100 text-purple-800 px-4 py-2 rounded-lg font-medium">
                                    🏛️ [[ confederation.name ]] ([[ confederation.acronym ]])
                                </div>
                                <div class="text-purple-600 text-sm mt-1">↓</div>
                            </div>
                            
                            <!-- Fédérations -->
                            <div class="text-center">
                                <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-medium">
                                    🏛️ [[ confederation.federationCount || 0 ]] Fédérations nationales
                                </div>
                                <div class="text-blue-600 text-sm mt-1">↓</div>
                            </div>
                            
                            <!-- Clubs -->
                            <div class="text-center">
                                <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-lg font-medium">
                                    🏟️ [[ confederation.clubCount || 0 ]] Clubs affiliés
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fédérations affiliées -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏛️</span>
                        Fédérations affiliées
                    </h3>
                    
                    <div v-if="confederation.federationCount > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="i in Math.min(confederation.federationCount, 8)" :key="i" class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">Fédération [[ i ]]</p>
                                    <p class="text-sm text-gray-600">Pays affilié</p>
                                </div>
                                <span class="text-xs text-gray-500">ID: FED_[[ i.toString().padStart(3, '0') ]]</span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Aucune fédération affiliée pour le moment</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <button class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                            🏛️ Gérer les fédérations
                        </button>
                        <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            🏟️ Voir les clubs affiliés
                        </button>
                        <button class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            🏆 Gérer les compétitions
                        </button>
                        <button class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            📋 Licences et certifications
                        </button>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">📍</span>
                            <span class="text-gray-600">[[ confederation.continent ]]</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">🌍</span>
                            <span class="text-gray-600">[[ confederation.type ]]</span>
                        </div>
                        <div v-if="confederation.fifa_connect_id" class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">⚽</span>
                            <span class="text-gray-600">FIFA ID: [[ confederation.fifa_connect_id ]]</span>
                        </div>
                    </div>
                </div>

                <!-- Statut FIFA -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statut FIFA</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Synchronisation:</span>
                            <span v-if="confederation.fifa_sync_status" :class="getSyncStatusClass(confederation.fifa_sync_status)" class="text-sm font-medium">
                                [[ confederation.fifa_sync_status ]]
                            </span>
                            <span v-else class="text-sm text-gray-400">Non configuré</span>
                        </div>
                        <div v-if="confederation.fifa_sync_date" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Dernière sync:</span>
                            <span class="text-sm font-medium text-gray-800">[[ confederation.fifa_sync_date ]]</span>
                        </div>
                        <div v-if="confederation.fifa_ranking" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Classement:</span>
                            <span class="text-sm font-medium text-gray-800">[[ confederation.fifa_ranking ]]</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucune confédération -->
        <div v-else class="text-center py-12">
            <div class="text-6xl mb-4">🌍</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Confédération non trouvée</h3>
            <p class="text-gray-500">La confédération demandée n'existe pas ou a été supprimée.</p>
            <a href="/test-confederations-view" class="inline-block mt-4 px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                Retour aux confédérations
            </a>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    confederation: null,
                    confederationId: null
                };
            },
            mounted() {
                // Récupérer l'ID de la confédération depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                this.confederationId = urlParams.get('id');
                
                if (this.confederationId) {
                    this.loadConfederation();
                }
            },
            methods: {
                loadConfederation() {
                    // Simuler le chargement d'une confédération (en production, ce serait un appel API)
                    const confederations = [
                        {
                            id: 1,
                            name: 'Union des Associations Européennes de Football',
                            acronym: 'UEFA',
                            continent: 'Europe',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_UEFA',
                            fifa_ranking: 1,
                            founded_year: 1954,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 55,
                            clubCount: 800
                        },
                        {
                            id: 2,
                            name: 'Confédération Africaine de Football',
                            acronym: 'CAF',
                            continent: 'Afrique',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_CAF',
                            fifa_ranking: 2,
                            founded_year: 1957,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 54,
                            clubCount: 650
                        },
                        {
                            id: 3,
                            name: 'Confédération de Football d\'Amérique du Nord, Centrale et Caraïbes',
                            acronym: 'CONCACAF',
                            continent: 'Amerique du Nord',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_CONCACAF',
                            fifa_ranking: 3,
                            founded_year: 1961,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 41,
                            clubCount: 450
                        },
                        {
                            id: 4,
                            name: 'Confédération Sud-Américaine de Football',
                            acronym: 'CONMEBOL',
                            continent: 'Amerique du Sud',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_CONMEBOL',
                            fifa_ranking: 4,
                            founded_year: 1916,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 10,
                            clubCount: 300
                        },
                        {
                            id: 5,
                            name: 'Confédération Asiatique de Football',
                            acronym: 'AFC',
                            continent: 'Asie',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_AFC',
                            fifa_ranking: 5,
                            founded_year: 1954,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 47,
                            clubCount: 550
                        },
                        {
                            id: 6,
                            name: 'Confédération de Football d\'Océanie',
                            acronym: 'OFC',
                            continent: 'Oceanie',
                            type: 'continental',
                            fifa_connect_id: 'FIFA_CONF_OFC',
                            fifa_ranking: 6,
                            founded_year: 1966,
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            federationCount: 11,
                            clubCount: 120
                        }
                    ];
                    
                    this.confederation = confederations.find(c => c.id == this.confederationId);
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
                editConfederation() {
                    // Rediriger vers la page d'édition
                    window.location.href = `/test-confederations-view?edit=${this.confederation.id}`;
                },
                deleteConfederation() {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer la confédération "${this.confederation.name}" ?`)) {
                        // En production, ce serait un appel API
                        alert('Confédération supprimée (simulation)');
                        window.location.href = '/test-confederations-view';
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
