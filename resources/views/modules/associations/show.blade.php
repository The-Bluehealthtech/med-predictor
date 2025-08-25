<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Association - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/test-associations-view" class="flex items-center text-green-600 hover:text-green-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux associations
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Détails de l'Association</h1>
            </div>
            <div class="flex space-x-3">
                <button @click="editAssociation" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    ✏️ Modifier
                </button>
                <button @click="deleteAssociation" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    🗑️ Supprimer
                </button>
            </div>
        </div>

        <!-- Association Details -->
        <div v-if="association" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header de l'association -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">[[ association.name ]]</h2>
                                <span v-if="association.short_name" class="text-lg font-medium text-green-600 bg-green-100 px-3 py-1 rounded-full">
                                    [[ association.short_name ]]
                                </span>
                            </div>
                            <p class="text-lg text-gray-600">[[ association.country ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(association.status)" class="px-4 py-2 rounded-full text-sm font-medium">
                                [[ association.status === 'active' ? 'Active' : association.status === 'inactive' ? 'Inactive' : 'En attente' ]]
                            </span>
                        </div>
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🌍</span>
                            <div>
                                <p class="text-sm text-gray-500">Confédération</p>
                                <p class="font-medium">[[ association.confederation ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🏆</span>
                            <div>
                                <p class="text-sm text-gray-500">Classement FIFA</p>
                                <p class="font-medium">[[ association.fifa_ranking || 'N/A' ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🏟️</span>
                            <div>
                                <p class="text-sm text-gray-500">Clubs affiliés</p>
                                <p class="font-medium">[[ association.clubCount || 0 ]]</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">Joueurs</p>
                                <p class="font-medium">[[ association.playerCount || 0 ]]</p>
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
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-800 mb-3">Identifiant FIFA</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-green-600">FIFA ID:</span>
                                    <span class="font-mono text-sm font-medium text-green-800">[[ association.fifa_id ]]</span>
                                </div>
                                <div v-if="association.fifa_version" class="flex justify-between">
                                    <span class="text-sm text-green-600">Version FIFA:</span>
                                    <span class="text-sm font-medium text-green-800">[[ association.fifa_version ]]</span>
                                </div>
                                <div v-if="association.fifa_sync_status" class="flex justify-between">
                                    <span class="text-sm text-green-600">Statut Sync:</span>
                                    <span class="text-sm font-medium" :class="getSyncStatusClass(association.fifa_sync_status)">
                                        [[ association.fifa_sync_status ]]
                                    </span>
                                </div>
                                <div v-if="association.fifa_sync_date" class="flex justify-between">
                                    <span class="text-sm text-green-600">Dernière Sync:</span>
                                    <span class="text-sm font-medium text-green-800">[[ association.fifa_sync_date ]]</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">Informations de l'association</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-600">Pays:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ association.country ]]</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-600">Confédération:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ association.confederation ]]</span>
                                </div>
                                <div v-if="association.fifa_ranking" class="flex justify-between">
                                    <span class="text-sm text-blue-600">Classement:</span>
                                    <span class="text-sm font-medium text-blue-800">[[ association.fifa_ranking ]]</span>
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
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">[[ association.clubCount || 0 ]]</div>
                            <div class="text-sm text-green-600">Clubs affiliés</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">[[ association.playerCount || 0 ]]</div>
                            <div class="text-sm text-blue-600">Joueurs</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">[[ association.fifa_ranking || 'N/A' ]]</div>
                            <div class="text-sm text-purple-600">Classement FIFA</div>
                        </div>
                    </div>
                </div>

                <!-- Clubs affiliés -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏟️</span>
                        Clubs affiliés
                    </h3>
                    
                    <div v-if="association.clubCount > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="i in Math.min(association.clubCount, 6)" :key="i" class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">Club [[ i ]]</p>
                                    <p class="text-sm text-gray-600">Ligue 1</p>
                                </div>
                                <span class="text-xs text-gray-500">ID: CLUB_[[ i.toString().padStart(3, '0') ]]</span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Aucun club affilié pour le moment</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                    <div class="space-y-3">
                        <button class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            🏟️ Gérer les clubs
                        </button>
                        <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            👥 Gérer les joueurs
                        </button>
                        <button class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
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
                            <span class="text-gray-600">[[ association.country ]]</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">🌍</span>
                            <span class="text-gray-600">[[ association.confederation ]]</span>
                        </div>
                        <div v-if="association.fifa_id" class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-500">⚽</span>
                            <span class="text-gray-600">FIFA ID: [[ association.fifa_id ]]</span>
                        </div>
                    </div>
                </div>

                <!-- Statut FIFA -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statut FIFA</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Synchronisation:</span>
                            <span v-if="association.fifa_sync_status" :class="getSyncStatusClass(association.fifa_sync_status)" class="text-sm font-medium">
                                [[ association.fifa_sync_status ]]
                            </span>
                            <span v-else class="text-sm text-gray-400">Non configuré</span>
                        </div>
                        <div v-if="association.fifa_sync_date" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Dernière sync:</span>
                            <span class="text-sm font-medium text-gray-800">[[ association.fifa_sync_date ]]</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucune association -->
        <div v-else class="text-center py-12">
            <div class="text-6xl mb-4">🏛️</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Association non trouvée</h3>
            <p class="text-gray-500">L'association demandée n'existe pas ou a été supprimée.</p>
            <a href="/test-associations-view" class="inline-block mt-4 px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                Retour aux associations
            </a>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    association: null,
                    associationId: null
                };
            },
            mounted() {
                // Récupérer l'ID de l'association depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                this.associationId = urlParams.get('id');
                
                if (this.associationId) {
                    this.loadAssociation();
                }
            },
            methods: {
                loadAssociation() {
                    // Simuler le chargement d'une association (en production, ce serait un appel API)
                    const associations = [
                        {
                            id: 1,
                            name: 'Fédération Française de Football',
                            short_name: 'FFF',
                            country: 'France',
                            confederation: 'UEFA',
                            fifa_id: 'FRA',
                            fifa_ranking: 2,
                            fifa_version: 'FIFA 25',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            clubCount: 18,
                            playerCount: 1250
                        },
                        {
                            id: 2,
                            name: 'Union Royale Belge des Sociétés de Football Association',
                            short_name: 'URBSFA',
                            country: 'Belgique',
                            confederation: 'UEFA',
                            fifa_id: 'BEL',
                            fifa_ranking: 3,
                            fifa_version: 'FIFA 25',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            clubCount: 12,
                            playerCount: 890
                        },
                        {
                            id: 3,
                            name: 'Association Suisse de Football',
                            short_name: 'ASF',
                            country: 'Suisse',
                            confederation: 'UEFA',
                            fifa_id: 'CHE',
                            fifa_ranking: 18,
                            fifa_version: 'FIFA 25',
                            status: 'active',
                            fifa_sync_status: 'synced',
                            fifa_sync_date: '2025-08-24',
                            clubCount: 10,
                            playerCount: 650
                        }
                    ];
                    
                    this.association = associations.find(a => a.id == this.associationId);
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
                editAssociation() {
                    // Rediriger vers la page d'édition
                    window.location.href = `/test-associations-view?edit=${this.association.id}`;
                },
                deleteAssociation() {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer l'association "${this.association.name}" ?`)) {
                        // En production, ce serait un appel API
                        alert('Association supprimée (simulation)');
                        window.location.href = '/test-associations-view';
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
