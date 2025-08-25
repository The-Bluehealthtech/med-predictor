<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confédérations - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">🌍 Confédérations FIFA</h1>
                <p class="text-lg text-gray-600">Gestion des confédérations continentales et internationales</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                <span class="mr-2">➕</span>
                Créer une confédération
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtres et recherche</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input v-model="searchQuery" type="text" placeholder="Nom, acronyme, continent..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select v-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">En attente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Continent</label>
                    <select v-model="continentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Tous les continents</option>
                        <option value="Europe">Europe</option>
                        <option value="Afrique">Afrique</option>
                        <option value="Amerique du Nord">Amérique du Nord</option>
                        <option value="Amerique du Sud">Amérique du Sud</option>
                        <option value="Asie">Asie</option>
                        <option value="Oceanie">Océanie</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select v-model="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Tous les types</option>
                        <option value="continental">Confédération continentale</option>
                        <option value="international">Organisation internationale</option>
                        <option value="regional">Confédération régionale</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des confédérations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="confederation in filteredConfederations" :key="confederation.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header de la carte -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-semibold text-gray-800">[[ confederation.name ]]</h3>
                                <span v-if="confederation.acronym" class="text-sm font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
                                    [[ confederation.acronym ]]
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">[[ confederation.continent ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(confederation.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                [[ confederation.status === 'active' ? 'Active' : confederation.status === 'inactive' ? 'Inactive' : 'En attente' ]]
                            </span>
                        </div>
                    </div>

                    <!-- Informations de la confédération -->
                    <div class="space-y-2 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🌍</span>
                                <span>[[ confederation.type ]]</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🏆</span>
                                <span>[[ confederation.fifa_ranking || 'N/A' ]]</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🏛️</span>
                                <span>[[ confederation.federationCount || 0 ]] fédérations</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🏟️</span>
                                <span>[[ confederation.clubCount || 0 ]] clubs</span>
                            </div>
                        </div>
                        
                        <!-- FIFA Connect Status -->
                        <div v-if="confederation.fifa_connect_id" class="mt-3 pt-3 border-t border-purple-200 bg-purple-50 p-2 rounded">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-purple-700">FIFA Connect ID:</span>
                                <span class="font-mono text-purple-800">[[ confederation.fifa_connect_id ]]</span>
                            </div>
                            <div v-if="confederation.fifa_sync_status" class="flex items-center mt-1">
                                <span class="text-xs text-purple-600">Sync: [[ confederation.fifa_sync_status ]]</span>
                                <span v-if="confederation.fifa_sync_date" class="text-xs text-purple-500 ml-2">([[ confederation.fifa_sync_date ]])</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 pt-4 border-t border-gray-200">
                        <button @click="viewConfederation(confederation)" class="flex-1 px-3 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm">
                            👁️ Voir
                        </button>
                        <button @click="editConfederation(confederation)" class="flex-1 px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                            ✏️ Modifier
                        </button>
                        <button @click="deleteConfederation(confederation)" class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            🗑️ Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucune confédération -->
        <div v-if="filteredConfederations.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">🌍</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune confédération trouvée</h3>
            <p class="text-gray-500">Commencez par créer votre première confédération !</p>
        </div>

        <!-- Modal Créer/Modifier Confédération -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        [[ showEditModal ? 'Modifier la confédération' : 'Créer une nouvelle confédération' ]]
                    </h3>
                    
                    <form @submit.prevent="saveConfederation" class="space-y-4">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la confédération *</label>
                                <input v-model="confederationForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Acronyme</label>
                                <input v-model="confederationForm.acronym" type="text" placeholder="ex: UEFA, CAF, CONCACAF" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        
                        <!-- Localisation et type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Continent *</label>
                                <select v-model="confederationForm.continent" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Sélectionner un continent</option>
                                    <option value="Europe">Europe</option>
                                    <option value="Afrique">Afrique</option>
                                    <option value="Amerique du Nord">Amérique du Nord</option>
                                    <option value="Amerique du Sud">Amérique du Sud</option>
                                    <option value="Asie">Asie</option>
                                    <option value="Oceanie">Océanie</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                <select v-model="confederationForm.type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="">Sélectionner un type</option>
                                    <option value="continental">Confédération continentale</option>
                                    <option value="international">Organisation internationale</option>
                                    <option value="regional">Confédération régionale</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Informations FIFA Connect -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-purple-800 mb-3">⚽ Informations FIFA Connect</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-purple-700 mb-2">FIFA Connect ID</label>
                                    <input v-model="confederationForm.fifa_connect_id" type="text" placeholder="ex: FIFA_CONF_UEFA, FIFA_CONF_CAF" class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-purple-600 mt-1">Format: FIFA_CONF_[ACRONYME]</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-purple-700 mb-2">Classement FIFA</label>
                                    <input v-model="confederationForm.fifa_ranking" type="number" placeholder="ex: 1, 2, 3" class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations supplémentaires -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                <select v-model="confederationForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">En attente</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Année de fondation</label>
                                <input v-model="confederationForm.founded_year" type="number" placeholder="ex: 1954, 1957" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                [[ showEditModal ? 'Modifier' : 'Créer' ]]
                            </button>
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    confederations: [
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
                    ],
                    searchQuery: '',
                    statusFilter: '',
                    continentFilter: '',
                    typeFilter: '',
                    showCreateModal: false,
                    showEditModal: false,
                    confederationForm: {
                        name: '',
                        acronym: '',
                        continent: '',
                        type: '',
                        fifa_connect_id: '',
                        fifa_ranking: '',
                        founded_year: '',
                        status: 'active'
                    },
                    editingConfederationId: null
                };
            },
            computed: {
                filteredConfederations() {
                    return this.confederations.filter(confederation => {
                        const matchesSearch = confederation.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           confederation.acronym.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           confederation.continent.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           confederation.type.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesStatus = !this.statusFilter || confederation.status === this.statusFilter;
                        const matchesContinent = !this.continentFilter || confederation.continent === this.continentFilter;
                        const matchesType = !this.typeFilter || confederation.type === this.typeFilter;
                        
                        return matchesSearch && matchesStatus && matchesContinent && matchesType;
                    });
                }
            },
            methods: {
                getStatusClass(status) {
                    const classes = {
                        'active': 'bg-green-100 text-green-800',
                        'inactive': 'bg-red-100 text-red-800',
                        'pending': 'bg-yellow-100 text-yellow-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },
                viewConfederation(confederation) {
                    // Rediriger vers la vue détaillée de la confédération
                    window.location.href = `/test-confederations-view/show?id=${confederation.id}`;
                },
                editConfederation(confederation) {
                    this.editingConfederationId = confederation.id;
                    this.confederationForm = { ...confederation };
                    this.showEditModal = true;
                },
                deleteConfederation(confederation) {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer la confédération "${confederation.name}" ?`)) {
                        this.confederations = this.confederations.filter(c => c.id !== confederation.id);
                    }
                },
                saveConfederation() {
                    if (this.showEditModal) {
                        // Modifier la confédération existante
                        const index = this.confederations.findIndex(c => c.id === this.editingConfederationId);
                        if (index !== -1) {
                            this.confederations[index] = { ...this.confederations[index], ...this.confederationForm };
                        }
                    } else {
                        // Créer une nouvelle confédération
                        const newConfederation = {
                            id: Date.now(),
                            ...this.confederationForm,
                            federationCount: 0,
                            clubCount: 0,
                            fifa_sync_status: 'pending',
                            fifa_sync_date: new Date().toISOString().split('T')[0]
                        };
                        this.confederations.push(newConfederation);
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.editingConfederationId = null;
                    this.confederationForm = {
                        name: '',
                        acronym: '',
                        continent: '',
                        type: '',
                        fifa_connect_id: '',
                        fifa_ranking: '',
                        founded_year: '',
                        status: 'active'
                    };
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
