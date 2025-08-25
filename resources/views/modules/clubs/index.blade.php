<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                🏟️ Clubs de Football
            </h1>
            <p class="text-lg text-gray-600">
                Gestion et suivi des clubs affiliés à la plateforme FIT
            </p>
        </div>

        <!-- Navigation -->
        <div class="mb-8 flex justify-between items-center">
            <a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                ← Retour à l'accueil
            </a>
            
            <!-- Bouton Créer un club -->
            <button @click="showCreateModal = true" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                ➕ Créer un club
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input v-model="searchQuery" type="text" placeholder="Nom du club, ville, pays..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select v-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                        <option value="pending">En attente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                    <select v-model="countryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les pays</option>
                        <option value="France">France</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Belgique">Belgique</option>
                        <option value="Suisse">Suisse</option>
                        <option value="Luxembourg">Luxembourg</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-2">Ligue</label>
                    <select v-model="leagueFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes les ligues</option>
                        <option value="Ligue 1">Ligue 1</option>
                        <option value="Ligue 2">Ligue 2</option>
                        <option value="National">National</option>
                        <option value="Régional">Régional</option>
                        <option value="Départemental">Départemental</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des clubs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Club Card Template -->
            <div v-for="club in filteredClubs" :key="club.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="p-6">
                    <!-- Header de la carte -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl font-semibold text-gray-800">[[ club.name ]]</h3>
                                <span v-if="club.short_name" class="text-sm font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">[[ club.short_name ]]</span>
                            </div>
                            <p class="text-sm text-gray-600">[[ club.city ]], [[ club.country ]]</p>
                        </div>
                        <div class="flex space-x-2">
                            <span :class="getStatusClass(club.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                [[ club.status === 'active' ? 'Actif' : club.status === 'inactive' ? 'Inactif' : 'En attente' ]]
                            </span>
                        </div>
                    </div>

                    <!-- Informations du club -->
                    <div class="space-y-2 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2">🏆</span>
                                <span>[[ club.league ]] - [[ club.division ]]</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2">🏟️</span>
                                <span v-if="club.stadium">[[ club.stadium ]]</span>
                                <span v-else>Stade non défini</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2">👥</span>
                                <span>[[ club.playerCount ]] joueurs</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2">⚽</span>
                                <span>[[ club.teamCount ]] équipes</span>
                            </div>
                        </div>
                        
                        <!-- FIFA Connect Status -->
                        <div v-if="club.fifa_connect_id" class="mt-3 pt-3 border-t border-blue-200 bg-blue-50 p-2 rounded">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-blue-700">FIFA Connect ID:</span>
                                <span class="font-mono text-blue-800">[[ club.fifa_connect_id ]]</span>
                            </div>
                            <div v-if="club.founded_year" class="text-xs text-blue-600 mt-1">
                                Fondé en [[ club.founded_year ]]
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 pt-4 border-t border-gray-200">
                        <button @click="viewClub(club)" class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            👁️ Voir
                        </button>
                        <button @click="editClub(club)" class="flex-1 px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                            ✏️ Modifier
                        </button>
                        <button @click="deleteClub(club)" class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            🗑️ Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucun club -->
        <div v-if="filteredClubs.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">🏟️</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun club trouvé</h3>
            <p class="text-gray-500">Commencez par créer votre premier club !</p>
        </div>

        <!-- Modal Créer/Modifier Club -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        [[ showEditModal ? 'Modifier le club' : 'Créer un nouveau club' ]]
                    </h3>
                    
                    <form @submit.prevent="saveClub" class="space-y-4">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du club *</label>
                                <input v-model="clubForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom court</label>
                                <input v-model="clubForm.short_name" type="text" placeholder="ex: OM, PSG" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Localisation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                                <select v-model="clubForm.country" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner un pays</option>
                                    <option value="France">France</option>
                                    <option value="Monaco">Monaco</option>
                                    <option value="Belgique">Belgique</option>
                                    <option value="Suisse">Suisse</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                                <input v-model="clubForm.city" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Stade -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stade</label>
                                <input v-model="clubForm.stadium" type="text" placeholder="Nom du stade" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Capacité du stade</label>
                                <input v-model="clubForm.stadium_capacity" type="number" placeholder="Nombre de places" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Informations FIFA Connect -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-3">⚽ Informations FIFA Connect</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">FIFA Connect ID</label>
                                    <input v-model="clubForm.fifa_connect_id" type="text" placeholder="ex: FIFA_CLUB_FRA_XXX" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-blue-600 mt-1">Format: FIFA_CLUB_[PAYS]_[CODE]</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Année de fondation</label>
                                    <input v-model="clubForm.founded_year" type="number" placeholder="ex: 1899" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Compétition et statut -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ligue</label>
                                <select v-model="clubForm.league" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner une ligue</option>
                                    <option value="Ligue 1">Ligue 1</option>
                                    <option value="Ligue 2">Ligue 2</option>
                                    <option value="National">National</option>
                                    <option value="Régional">Régional</option>
                                    <option value="Départemental">Départemental</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Division</label>
                                <select v-model="clubForm.division" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner une division</option>
                                    <option value="Ligue 1">Ligue 1</option>
                                    <option value="Ligue 2">Ligue 2</option>
                                    <option value="National">National</option>
                                    <option value="Régional">Régional</option>
                                    <option value="Départemental">Départemental</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select v-model="clubForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="pending">En attente</option>
                            </select>
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
                    clubs: [
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
                    ],
                    searchQuery: '',
                    statusFilter: '',
                    countryFilter: '',
                    leagueFilter: '',
                    showCreateModal: false,
                    showEditModal: false,
                    clubForm: {
                        name: '',
                        short_name: '',
                        country: '',
                        city: '',
                        stadium: '',
                        stadium_capacity: '',
                        founded_year: '',
                        fifa_connect_id: '',
                        league: '',
                        division: '',
                        status: 'active'
                    },
                    editingClubId: null
                };
            },
            computed: {
                filteredClubs() {
                    return this.clubs.filter(club => {
                        const matchesSearch = club.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           club.city.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           club.country.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           (club.short_name && club.short_name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                        const matchesStatus = !this.statusFilter || club.status === this.statusFilter;
                        const matchesCountry = !this.countryFilter || club.country === this.countryFilter;
                        const matchesLeague = !this.leagueFilter || club.league === this.leagueFilter;
                        
                        return matchesSearch && matchesStatus && matchesCountry && matchesLeague;
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
                viewClub(club) {
                    // Rediriger vers la vue détaillée du club
                    window.location.href = `/test-clubs-view/show?id=${club.id}`;
                },
                editClub(club) {
                    this.editingClubId = club.id;
                    this.clubForm = { ...club };
                    this.showEditModal = true;
                },
                deleteClub(club) {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer le club "${club.name}" ?`)) {
                        this.clubs = this.clubs.filter(c => c.id !== club.id);
                    }
                },
                saveClub() {
                    if (this.showEditModal) {
                        // Modifier le club existant
                        const index = this.clubs.findIndex(c => c.id === this.editingClubId);
                        if (index !== -1) {
                            this.clubs[index] = { ...this.clubs[index], ...this.clubForm };
                        }
                    } else {
                        // Créer un nouveau club
                        const newClub = {
                            id: Date.now(),
                            ...this.clubForm,
                            playerCount: 0,
                            teamCount: 0
                        };
                        this.clubs.push(newClub);
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.editingClubId = null;
                    this.clubForm = {
                        name: '',
                        short_name: '',
                        country: '',
                        city: '',
                        stadium: '',
                        stadium_capacity: '',
                        founded_year: '',
                        fifa_connect_id: '',
                        league: '',
                        division: '',
                        status: 'active'
                    };
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
