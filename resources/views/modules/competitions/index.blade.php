<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Compétitions - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">🏆 Gestion des Compétitions</h1>
                <p class="text-lg text-gray-600">Gestion complète des compétitions FIFA Connect</p>
            </div>
            <div class="flex space-x-3">
                <button @click="showCreateModal = true" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <span class="mr-2">➕</span>
                    Nouvelle compétition
                </button>
                <button @click="showBatchModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <span class="mr-2">🔄</span>
                    Gestion en lot
                </button>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtres et recherche</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input v-model="searchQuery" type="text" placeholder="Nom, type, saison..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select v-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="upcoming">À venir</option>
                        <option value="active">Active</option>
                        <option value="completed">Terminée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select v-model="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous les types</option>
                        <option value="league">Ligue</option>
                        <option value="cup">Coupe</option>
                        <option value="friendly">Match amical</option>
                        <option value="international">International</option>
                        <option value="tournament">Tournoi</option>
                        <option value="playoff">Playoff</option>
                        <option value="exhibition">Exhibition</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select v-model="formatFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous les formats</option>
                        <option value="round_robin">Aller-retour</option>
                        <option value="knockout">Éliminatoire</option>
                        <option value="mixed">Mixte</option>
                        <option value="single_round">Simple tour</option>
                        <option value="group_stage">Phase de groupes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">FIFA Connect</label>
                    <select v-model="fifaFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="synced">Synchronisées</option>
                        <option value="pending">En attente</option>
                        <option value="error">Erreur</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistiques des compétitions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <span class="text-2xl text-green-600">🏆</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total compétitions</p>
                        <p class="text-2xl font-bold text-gray-900">[[ competitionStats.total ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <span class="text-2xl text-blue-600">⏳</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">À venir</p>
                        <p class="text-2xl font-bold text-gray-900">[[ competitionStats.upcoming ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <span class="text-2xl text-yellow-600">🔥</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Actives</p>
                        <p class="text-2xl font-bold text-gray-900">[[ competitionStats.active ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <span class="text-2xl text-purple-600">🔄</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">FIFA Connect</p>
                        <p class="text-2xl font-bold text-gray-900">[[ competitionStats.fifaSynced ]]</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des compétitions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="competition in filteredCompetitions" :key="competition.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header de la carte -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-semibold text-gray-800">[[ competition.name ]]</h3>
                                <span v-if="competition.short_name" class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                    [[ competition.short_name ]]
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">[[ competition.type ]] - [[ competition.season ]]</p>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusClass(competition.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                [[ getStatusText(competition.status) ]]
                            </span>
                        </div>
                    </div>

                    <!-- Informations de la compétition -->
                    <div class="space-y-2 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">📅</span>
                                <span>[[ competition.start_date ]]</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">🏁</span>
                                <span>[[ competition.end_date ]]</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">🏟️</span>
                                <span>[[ competition.format ]]</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-green-600">👥</span>
                                <span>[[ competition.min_teams || 0 ]]-[[ competition.max_teams || 0 ]] équipes</span>
                            </div>
                        </div>
                        
                        <!-- FIFA Connect Status -->
                        <div v-if="competition.fifa_connect_id" class="mt-3 pt-3 border-t border-green-200 bg-green-50 p-2 rounded">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-green-700">FIFA Connect ID:</span>
                                <span class="font-mono text-green-800">[[ competition.fifa_connect_id ]]</span>
                            </div>
                            <div v-if="competition.fifa_sync_status" class="flex items-center mt-1">
                                <span class="text-xs text-green-600">Sync: [[ competition.fifa_sync_status ]]</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 pt-4 border-t border-gray-200">
                        <button @click="viewCompetition(competition)" class="flex-1 px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                            👁️ Voir
                        </button>
                        <button @click="editCompetition(competition)" class="flex-1 px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                            ✏️ Modifier
                        </button>
                        <button @click="deleteCompetition(competition)" class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            🗑️ Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message si aucune compétition -->
        <div v-if="filteredCompetitions.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune compétition trouvée</h3>
            <p class="text-gray-500">Commencez par créer votre première compétition !</p>
        </div>

        <!-- Modal Créer/Modifier Compétition -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        [[ showEditModal ? 'Modifier la compétition' : 'Créer une nouvelle compétition' ]]
                    </h3>
                    
                    <form @submit.prevent="saveCompetition" class="space-y-4">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la compétition *</label>
                                <input v-model="competitionForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom court</label>
                                <input v-model="competitionForm.short_name" type="text" placeholder="ex: L1, CDL, UCL" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <!-- Type et saison -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                <select v-model="competitionForm.type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">Sélectionner un type</option>
                                    <option value="league">Ligue</option>
                                    <option value="cup">Coupe</option>
                                    <option value="friendly">Match amical</option>
                                    <option value="international">International</option>
                                    <option value="tournament">Tournoi</option>
                                    <option value="playoff">Playoff</option>
                                    <option value="exhibition">Exhibition</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Saison *</label>
                                <input v-model="competitionForm.season" type="text" placeholder="ex: 2024-2025, 2025" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de début *</label>
                                <input v-model="competitionForm.start_date" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin *</label>
                                <input v-model="competitionForm.end_date" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date limite d'inscription</label>
                                <input v-model="competitionForm.registration_deadline" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <!-- Format et équipes -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Format *</label>
                                <select v-model="competitionForm.format" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">Sélectionner un format</option>
                                    <option value="round_robin">Aller-retour</option>
                                    <option value="knockout">Éliminatoire</option>
                                    <option value="mixed">Mixte</option>
                                    <option value="single_round">Simple tour</option>
                                    <option value="group_stage">Phase de groupes</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Équipes min</label>
                                <input v-model="competitionForm.min_teams" type="number" placeholder="ex: 8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Équipes max</label>
                                <input v-model="competitionForm.max_teams" type="number" placeholder="ex: 20" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <!-- Informations FIFA Connect -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-green-800 mb-3">⚽ Informations FIFA Connect</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">FIFA Connect ID</label>
                                    <input v-model="competitionForm.fifa_connect_id" type="text" placeholder="ex: FIFA_COMP_L1_2025" class="w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <p class="text-xs text-green-600 mt-1">Format: FIFA_COMP_[TYPE]_[SAISON]</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">Synchronisation FIFA</label>
                                    <select v-model="competitionForm.fifa_sync_enabled" class="w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                        <option :value="false">Désactivée</option>
                                        <option :value="true">Activée</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations supplémentaires -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                <select v-model="competitionForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="upcoming">À venir</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Terminée</option>
                                    <option value="cancelled">Annulée</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Frais d'inscription</label>
                                <input v-model="competitionForm.entry_fee" type="number" placeholder="ex: 1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dotation</label>
                                <input v-model="competitionForm.prize_pool" type="number" placeholder="ex: 50000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Licence fédération requise</label>
                                <select v-model="competitionForm.require_federation_license" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option :value="false">Non</option>
                                    <option :value="true">Oui</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea v-model="competitionForm.description" rows="3" placeholder="Description de la compétition..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Règlement</label>
                                <textarea v-model="competitionForm.rules" rows="3" placeholder="Règlement de la compétition..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
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
                    competitions: [
                        {
                            id: 1,
                            name: 'Ligue 1 Uber Eats',
                            short_name: 'L1',
                            type: 'league',
                            season: '2024-2025',
                            start_date: '2024-08-16',
                            end_date: '2025-05-24',
                            registration_deadline: '2024-07-31',
                            min_teams: 20,
                            max_teams: 20,
                            format: 'round_robin',
                            status: 'active',
                            description: 'Championnat de France de football professionnel',
                            rules: 'Format aller-retour, 38 journées',
                            entry_fee: 0,
                            prize_pool: 0,
                            require_federation_license: true,
                            fifa_sync_enabled: true,
                            fifa_connect_id: 'FIFA_COMP_L1_2025',
                            fifa_sync_status: 'synced'
                        },
                        {
                            id: 2,
                            name: 'Coupe de France',
                            short_name: 'CDF',
                            type: 'cup',
                            season: '2024-2025',
                            start_date: '2024-11-16',
                            end_date: '2025-05-03',
                            registration_deadline: '2024-10-31',
                            min_teams: 64,
                            max_teams: 64,
                            format: 'knockout',
                            status: 'upcoming',
                            description: 'Coupe nationale de football',
                            rules: 'Format éliminatoire simple',
                            entry_fee: 500,
                            prize_pool: 100000,
                            require_federation_license: true,
                            fifa_sync_enabled: true,
                            fifa_connect_id: 'FIFA_COMP_CDF_2025',
                            fifa_sync_status: 'pending'
                        },
                        {
                            id: 3,
                            name: 'Ligue des Champions',
                            short_name: 'UCL',
                            type: 'international',
                            season: '2024-2025',
                            start_date: '2024-09-17',
                            end_date: '2025-06-01',
                            registration_deadline: '2024-08-31',
                            min_teams: 32,
                            max_teams: 32,
                            format: 'mixed',
                            status: 'active',
                            description: 'Compétition européenne de clubs',
                            rules: 'Phase de groupes + éliminatoire',
                            entry_fee: 0,
                            prize_pool: 2000000,
                            require_federation_license: true,
                            fifa_sync_enabled: true,
                            fifa_connect_id: 'FIFA_COMP_UCL_2025',
                            fifa_sync_status: 'synced'
                        }
                    ],
                    searchQuery: '',
                    statusFilter: '',
                    typeFilter: '',
                    formatFilter: '',
                    fifaFilter: '',
                    showCreateModal: false,
                    showEditModal: false,
                    showBatchModal: false,
                    competitionForm: {
                        name: '',
                        short_name: '',
                        type: '',
                        season: '',
                        start_date: '',
                        end_date: '',
                        registration_deadline: '',
                        min_teams: '',
                        max_teams: '',
                        format: '',
                        status: 'upcoming',
                        description: '',
                        rules: '',
                        entry_fee: '',
                        prize_pool: '',
                        require_federation_license: false,
                        fifa_sync_enabled: false,
                        fifa_connect_id: ''
                    },
                    editingCompetitionId: null
                };
            },
            computed: {
                filteredCompetitions() {
                    return this.competitions.filter(competition => {
                        const matchesSearch = competition.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           competition.short_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           competition.type.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           competition.season.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesStatus = !this.statusFilter || competition.status === this.statusFilter;
                        const matchesType = !this.typeFilter || competition.type === this.typeFilter;
                        const matchesFormat = !this.formatFilter || competition.format === this.formatFilter;
                        const matchesFifa = !this.fifaFilter || 
                                          (this.fifaFilter === 'synced' && competition.fifa_sync_status === 'synced') ||
                                          (this.fifaFilter === 'pending' && competition.fifa_sync_status === 'pending') ||
                                          (this.fifaFilter === 'error' && competition.fifa_sync_status === 'error');
                        
                        return matchesSearch && matchesStatus && matchesType && matchesFormat && matchesFifa;
                    });
                },
                competitionStats() {
                    const stats = {
                        total: this.competitions.length,
                        upcoming: this.competitions.filter(c => c.status === 'upcoming').length,
                        active: this.competitions.filter(c => c.status === 'active').length,
                        completed: this.competitions.filter(c => c.status === 'completed').length,
                        fifaSynced: this.competitions.filter(c => c.fifa_sync_status === 'synced').length
                    };
                    return stats;
                }
            },
            methods: {
                getStatusClass(status) {
                    const classes = {
                        'upcoming': 'bg-blue-100 text-blue-800',
                        'active': 'bg-green-100 text-green-800',
                        'completed': 'bg-gray-100 text-gray-800',
                        'cancelled': 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },
                getStatusText(status) {
                    const texts = {
                        'upcoming': 'À venir',
                        'active': 'Active',
                        'completed': 'Terminée',
                        'cancelled': 'Annulée'
                    };
                    return texts[status] || status;
                },
                viewCompetition(competition) {
                    alert(`Voir la compétition: ${competition.name}`);
                },
                editCompetition(competition) {
                    this.editingCompetitionId = competition.id;
                    this.competitionForm = { ...competition };
                    this.showEditModal = true;
                },
                deleteCompetition(competition) {
                    if (confirm(`Êtes-vous sûr de vouloir supprimer la compétition "${competition.name}" ?`)) {
                        this.competitions = this.competitions.filter(c => c.id !== competition.id);
                    }
                },
                saveCompetition() {
                    if (this.showEditModal) {
                        // Modifier la compétition existante
                        const index = this.competitions.findIndex(c => c.id === this.editingCompetitionId);
                        if (index !== -1) {
                            this.competitions[index] = { ...this.competitions[index], ...this.competitionForm };
                        }
                    } else {
                        // Créer une nouvelle compétition
                        const newCompetition = {
                            id: Date.now(),
                            ...this.competitionForm,
                            fifa_connect_id: this.competitionForm.fifa_sync_enabled ? `FIFA_COMP_${this.competitionForm.short_name}_${this.competitionForm.season.split('-')[1]}` : null,
                            fifa_sync_status: this.competitionForm.fifa_sync_enabled ? 'pending' : null
                        };
                        this.competitions.push(newCompetition);
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showBatchModal = false;
                    this.editingCompetitionId = null;
                    this.competitionForm = {
                        name: '',
                        short_name: '',
                        type: '',
                        season: '',
                        start_date: '',
                        end_date: '',
                        registration_deadline: '',
                        min_teams: '',
                        max_teams: '',
                        format: '',
                        status: 'upcoming',
                        description: '',
                        rules: '',
                        entry_fee: '',
                        prize_pool: '',
                        require_federation_license: false,
                        fifa_sync_enabled: false,
                        fifa_connect_id: ''
                    };
                }
            }
        }).mount('#app');
    </script>
</body>
</html> 