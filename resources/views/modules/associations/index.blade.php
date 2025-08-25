<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associations - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">🏛️ Associations de Football</h1>
                <p class="text-lg text-gray-600">Gestion des fédérations nationales et régionales</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <span class="mr-2">➕</span>
                Créer une association
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtres et recherche</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input v-model="searchQuery" type="text" placeholder="Nom, pays, confédération..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select v-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">En attente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                    <select v-model="countryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous les pays</option>
                        <option value="France">France</option>
                        <option value="Belgique">Belgique</option>
                        <option value="Suisse">Suisse</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Monaco">Monaco</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confédération</label>
                    <select v-model="confederationFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Toutes les confédérations</option>
                        <option value="UEFA">UEFA (Europe)</option>
                        <option value="CAF">CAF (Afrique)</option>
                        <option value="CONCACAF">CONCACAF (Amérique du Nord)</option>
                        <option value="CONMEBOL">CONMEBOL (Amérique du Sud)</option>
                        <option value="AFC">AFC (Asie)</option>
                        <option value="OFC">OFC (Océanie)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des associations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="association in filteredAssociations" :key="association.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header de la carte -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-semibold text-gray-800">[[ association.name ]]</h3>
                                <span v-if="association.short_name" class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                    [[ association.short_name ]]
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">[[ association.country ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(association.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                [[ association.status === 'active' ? 'Active' : association.status === 'inactive' ? 'Inactive' : 'En attente' ]]
                            </span>
                        </div>
                    </div>

                    <!-- Informations de l'association -->
                    <div class="space-y-2 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">🌍</span>
                                <span>[[ association.confederation ]]</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">🏆</span>
                                <span>[[ association.fifa_ranking || 'N/A' ]]</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">🏟️</span>
                                <span>[[ association.clubCount || 0 ]] clubs</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">👥</span>
                                <span>[[ association.playerCount || 0 ]] joueurs</span>
                            </div>
                        </div>
                        
                        <!-- FIFA Connect Status -->
                        <div v-if="association.fifa_id" class="mt-3 pt-3 border-t border-green-200 bg-green-50 p-2 rounded">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-green-700">FIFA ID:</span>
                                <span class="font-mono text-green-800">[[ association.fifa_id ]]</span>
                            </div>
                            <div v-if="association.fifa_sync_status" class="flex items-center mt-1">
                                <span class="text-xs text-green-600">Sync: [[ association.fifa_sync_status ]]</span>
                                <span v-if="association.fifa_sync_date" class="text-xs text-green-500 ml-2">([[ association.fifa_sync_date ]])</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 pt-4 border-t border-gray-200">
                        <button @click="viewAssociation(association)" class="flex-1 px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                            👁️ Voir
                        </button>
                        <button @click="editAssociation(association)" class="flex-1 px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                            ✏️ Modifier
                        </button>
                        <button @click="deleteAssociation(association)" class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            🗑️ Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucune association -->
        <div v-if="filteredAssociations.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">🏛️</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune association trouvée</h3>
            <p class="text-gray-500">Commencez par créer votre première association !</p>
        </div>

        <!-- Modal Créer/Modifier Association -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        [[ showEditModal ? 'Modifier l\'association' : 'Créer une nouvelle association' ]]
                    </h3>
                    
                    <form @submit.prevent="saveAssociation" class="space-y-4">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'association *</label>
                                <input v-model="associationForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom court</label>
                                <input v-model="associationForm.short_name" type="text" placeholder="ex: FFF, KBVB" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <!-- Localisation et confédération -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                                <select v-model="associationForm.country" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">Sélectionner un pays</option>
                                    <option value="France">France</option>
                                    <option value="Belgique">Belgique</option>
                                    <option value="Suisse">Suisse</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                    <option value="Monaco">Monaco</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confédération *</label>
                                <select v-model="associationForm.confederation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">Sélectionner une confédération</option>
                                    <option value="UEFA">UEFA (Europe)</option>
                                    <option value="CAF">CAF (Afrique)</option>
                                    <option value="CONCACAF">CONCACAF (Amérique du Nord)</option>
                                    <option value="CONMEBOL">CONMEBOL (Amérique du Sud)</option>
                                    <option value="AFC">AFC (Asie)</option>
                                    <option value="OFC">OFC (Océanie)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Informations FIFA Connect -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-green-800 mb-3">⚽ Informations FIFA Connect</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">FIFA ID</label>
                                    <input v-model="associationForm.fifa_id" type="text" placeholder="ex: FRA, BEL, CHE" class="w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <p class="text-xs text-green-600 mt-1">Code pays FIFA (ex: FRA pour France)</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">Classement FIFA</label>
                                    <input v-model="associationForm.fifa_ranking" type="number" placeholder="ex: 1, 15, 50" class="w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statut et version -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                <select v-model="associationForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">En attente</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Version FIFA</label>
                                <input v-model="associationForm.fifa_version" type="text" placeholder="ex: FIFA 25" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
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
                    associations: [
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
                    ],
                    searchQuery: '',
                    statusFilter: '',
                    countryFilter: '',
                    confederationFilter: '',
                    showCreateModal: false,
                    showEditModal: false,
                    associationForm: {
                        name: '',
                        short_name: '',
                        country: '',
                        confederation: '',
                        fifa_id: '',
                        fifa_ranking: '',
                        fifa_version: '',
                        status: 'active'
                    },
                    editingAssociationId: null
                };
            },
            computed: {
                filteredAssociations() {
                    return this.associations.filter(association => {
                        const matchesSearch = association.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           association.country.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           association.confederation.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           (association.short_name && association.short_name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                        const matchesStatus = !this.statusFilter || association.status === this.statusFilter;
                        const matchesCountry = !this.countryFilter || association.country === this.countryFilter;
                        const matchesConfederation = !this.confederationFilter || association.confederation === this.confederationFilter;
                        
                        return matchesSearch && matchesStatus && matchesCountry && matchesConfederation;
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
                viewAssociation(association) {
                    // Rediriger vers la vue détaillée de l'association
                    window.location.href = `/test-associations-view/show?id=${association.id}`;
                },
                editAssociation(association) {
                    this.editingAssociationId = association.id;
                    this.associationForm = { ...association };
                    this.showEditModal = true;
                },
                deleteAssociation(association) {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer l'association "${association.name}" ?`)) {
                        this.associations = this.associations.filter(a => a.id !== association.id);
                    }
                },
                saveAssociation() {
                    if (this.showEditModal) {
                        // Modifier l'association existante
                        const index = this.associations.findIndex(a => a.id === this.editingAssociationId);
                        if (index !== -1) {
                            this.associations[index] = { ...this.associations[index], ...this.associationForm };
                        }
                    } else {
                        // Créer une nouvelle association
                        const newAssociation = {
                            id: Date.now(),
                            ...this.associationForm,
                            clubCount: 0,
                            playerCount: 0,
                            fifa_sync_status: 'pending',
                            fifa_sync_date: new Date().toISOString().split('T')[0]
                        };
                        this.associations.push(newAssociation);
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.editingAssociationId = null;
                    this.associationForm = {
                        name: '',
                        short_name: '',
                        country: '',
                        confederation: '',
                        fifa_id: '',
                        fifa_ranking: '',
                        fifa_version: '',
                        status: 'active'
                    };
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
