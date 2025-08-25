<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de Licences (Demande côté Club) - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">📋 Demandes de Licences (Demande côté Club)</h1>
                <p class="text-lg text-gray-600">Gestion des demandes de licences FIFA Connect soumises par les clubs</p>
            </div>
            <div class="flex space-x-3">
                <button @click="showBatchValidationModal = true" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <span class="mr-2">🔄</span>
                    Validation en lot
                </button>
                <button @click="showCreateLicenseModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <span class="mr-2">➕</span>
                    Nouvelle licence
                </button>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtres et recherche</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input v-model="searchQuery" type="text" placeholder="Nom, ID, type..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select v-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="valid">Valide</option>
                        <option value="pending">En attente</option>
                        <option value="expired">Expirée</option>
                        <option value="suspended">Suspendue</option>
                        <option value="revoked">Révoquée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select v-model="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les types</option>
                        <option value="player">Joueur</option>
                        <option value="coach">Entraîneur</option>
                        <option value="referee">Arbitre</option>
                        <option value="medical">Médical</option>
                        <option value="administrative">Administrative</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">FIFA Connect</label>
                    <select v-model="fifaFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="synced">Synchronisées</option>
                        <option value="pending">En attente</option>
                        <option value="error">Erreur</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date d'expiration</label>
                    <select v-model="expirationFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes</option>
                        <option value="expired">Expirées</option>
                        <option value="expiring_soon">Expirent bientôt</option>
                        <option value="valid">Valides</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistiques des licences -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <span class="text-2xl text-green-600">✅</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Licences valides</p>
                        <p class="text-2xl font-bold text-gray-900">[[ licenseStats.valid ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <span class="text-2xl text-yellow-600">⏳</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-gray-900">[[ licenseStats.pending ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <span class="text-2xl text-red-600">❌</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Expirées/Suspendues</p>
                        <p class="text-2xl font-bold text-gray-900">[[ licenseStats.expired + licenseStats.suspended ]]</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <span class="text-2xl text-blue-600">🔄</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">FIFA Connect</p>
                        <p class="text-2xl font-bold text-gray-900">[[ licenseStats.fifaSynced ]]</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des licences -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Licences à valider</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Licence
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Titulaire
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                FIFA Connect
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expiration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="license in filteredLicenses" :key="license.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">[[ license.type.charAt(0).toUpperCase() ]]</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">[[ license.licenseNumber ]]</div>
                                        <div class="text-sm text-gray-500">[[ license.category ]]</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">[[ license.holderName ]]</div>
                                <div class="text-sm text-gray-500">[[ license.holderId ]]</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getTypeClass(license.type)">
                                    [[ license.type ]]
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(license.status)">
                                    [[ getStatusText(license.status) ]]
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span v-if="license.fifaConnectId" class="text-sm font-medium text-green-600">
                                        [[ license.fifaConnectId ]]
                                    </span>
                                    <span v-else class="text-sm text-gray-400">Non configuré</span>
                                </div>
                                <div v-if="license.fifaSyncStatus" class="text-xs text-gray-500">
                                    Sync: [[ license.fifaSyncStatus ]]
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">[[ license.expirationDate ]]</div>
                                <div v-if="isExpiringSoon(license.expirationDate)" class="text-xs text-yellow-600">
                                    Expire bientôt
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewLicense(license)" class="text-blue-600 hover:text-blue-900">
                                        👁️ Voir
                                    </button>
                                    <button @click="validateLicense(license)" v-if="license.status === 'pending'" class="text-green-600 hover:text-green-900">
                                        ✅ Valider
                                    </button>
                                    <button @click="rejectLicense(license)" v-if="license.status === 'pending'" class="text-red-600 hover:text-red-900">
                                        ❌ Rejeter
                                    </button>
                                    <button @click="editLicense(license)" class="text-yellow-600 hover:text-yellow-900">
                                        ✏️ Modifier
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Message si aucune licence -->
        <div v-if="filteredLicenses.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune licence trouvée</h3>
            <p class="text-gray-500">Aucune licence ne correspond aux critères de recherche.</p>
        </div>

        <!-- Modal Validation en lot -->
        <div v-if="showBatchValidationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">🔄 Validation en lot des licences</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-2">Licences sélectionnées pour validation</h4>
                            <p class="text-sm text-blue-600">[[ selectedLicenses.length ]] licences en attente de validation</p>
                        </div>
                        
                        <div class="space-y-3">
                            <div v-for="license in selectedLicenses" :key="license.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">[[ license.licenseNumber ]]</p>
                                    <p class="text-sm text-gray-600">[[ license.holderName ]] - [[ license.type ]]</p>
                                </div>
                                <button @click="removeFromSelection(license)" class="text-red-600 hover:text-red-800">
                                    ❌
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button @click="validateBatch" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                ✅ Valider toutes les licences sélectionnées
                            </button>
                            <button @click="showBatchValidationModal = false" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Créer/Modifier Licence -->
        <div v-if="showCreateLicenseModal || showEditLicenseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        [[ showEditLicenseModal ? 'Modifier la licence' : 'Créer une nouvelle licence' ]]
                    </h3>
                    
                    <form @submit.prevent="saveLicense" class="space-y-4">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de licence *</label>
                                <input v-model="licenseForm.licenseNumber" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type de licence *</label>
                                <select v-model="licenseForm.type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionner un type</option>
                                    <option value="player">Joueur</option>
                                    <option value="coach">Entraîneur</option>
                                    <option value="referee">Arbitre</option>
                                    <option value="medical">Médical</option>
                                    <option value="administrative">Administrative</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Informations du titulaire -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du titulaire *</label>
                                <input v-model="licenseForm.holderName" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ID du titulaire</label>
                                <input v-model="licenseForm.holderId" type="text" placeholder="ex: PLAYER_001, COACH_123" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Informations FIFA Connect -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-3">⚽ Informations FIFA Connect</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">FIFA Connect ID</label>
                                    <input v-model="licenseForm.fifaConnectId" type="text" placeholder="ex: FIFA_LIC_PLAYER_001" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-blue-600 mt-1">Format: FIFA_LIC_[TYPE]_[ID]</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Statut de synchronisation</label>
                                    <select v-model="licenseForm.fifaSyncStatus" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="pending">En attente</option>
                                        <option value="synced">Synchronisée</option>
                                        <option value="error">Erreur</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations supplémentaires -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                                <input v-model="licenseForm.category" type="text" placeholder="ex: Professionnel, Amateur, Jeune" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date d'expiration</label>
                                <input v-model="licenseForm.expirationDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                [[ showEditLicenseModal ? 'Modifier' : 'Créer' ]]
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
                    licenses: [
                        {
                            id: 1,
                            licenseNumber: 'LIC_PLAYER_001',
                            type: 'player',
                            category: 'Professionnel',
                            holderName: 'Kylian Mbappé',
                            holderId: 'PLAYER_001',
                            status: 'valid',
                            fifaConnectId: 'FIFA_LIC_PLAYER_001',
                            fifaSyncStatus: 'synced',
                            expirationDate: '2026-06-30',
                            fifaSyncDate: '2025-08-24'
                        },
                        {
                            id: 2,
                            licenseNumber: 'LIC_COACH_001',
                            type: 'coach',
                            category: 'Professionnel',
                            holderName: 'Didier Deschamps',
                            holderId: 'COACH_001',
                            status: 'pending',
                            fifaConnectId: 'FIFA_LIC_COACH_001',
                            fifaSyncStatus: 'pending',
                            expirationDate: '2026-12-31',
                            fifaSyncDate: null
                        },
                        {
                            id: 3,
                            licenseNumber: 'LIC_REF_001',
                            type: 'referee',
                            category: 'International',
                            holderName: 'Clément Turpin',
                            holderId: 'REF_001',
                            status: 'valid',
                            fifaConnectId: 'FIFA_LIC_REF_001',
                            fifaSyncStatus: 'synced',
                            expirationDate: '2026-03-15',
                            fifaSyncDate: '2025-08-24'
                        },
                        {
                            id: 4,
                            licenseNumber: 'LIC_MED_001',
                            type: 'medical',
                            category: 'Professionnel',
                            holderName: 'Dr. Jean Dupont',
                            holderId: 'MED_001',
                            status: 'expired',
                            fifaConnectId: 'FIFA_LIC_MED_001',
                            fifaSyncStatus: 'synced',
                            expirationDate: '2025-01-15',
                            fifaSyncDate: '2025-01-10'
                        },
                        {
                            id: 5,
                            licenseNumber: 'LIC_ADMIN_001',
                            type: 'administrative',
                            category: 'Fédération',
                            holderName: 'Marie Martin',
                            holderId: 'ADMIN_001',
                            status: 'pending',
                            fifaConnectId: null,
                            fifaSyncStatus: null,
                            expirationDate: '2026-08-31',
                            fifaSyncDate: null
                        }
                    ],
                    searchQuery: '',
                    statusFilter: '',
                    typeFilter: '',
                    fifaFilter: '',
                    expirationFilter: '',
                    showBatchValidationModal: false,
                    showCreateLicenseModal: false,
                    showEditLicenseModal: false,
                    selectedLicenses: [],
                    licenseForm: {
                        licenseNumber: '',
                        type: '',
                        category: '',
                        holderName: '',
                        holderId: '',
                        fifaConnectId: '',
                        fifaSyncStatus: 'pending',
                        expirationDate: ''
                    },
                    editingLicenseId: null
                };
            },
            computed: {
                filteredLicenses() {
                    return this.licenses.filter(license => {
                        const matchesSearch = license.licenseNumber.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           license.holderName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           license.holderId.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesStatus = !this.statusFilter || license.status === this.statusFilter;
                        const matchesType = !this.typeFilter || license.type === this.typeFilter;
                        const matchesFifa = !this.fifaFilter || 
                                          (this.fifaFilter === 'synced' && license.fifaSyncStatus === 'synced') ||
                                          (this.fifaFilter === 'pending' && license.fifaSyncStatus === 'pending') ||
                                          (this.fifaFilter === 'error' && license.fifaSyncStatus === 'error');
                        const matchesExpiration = !this.expirationFilter || 
                                                (this.expirationFilter === 'expired' && this.isExpired(license.expirationDate)) ||
                                                (this.expirationFilter === 'expiring_soon' && this.isExpiringSoon(license.expirationDate)) ||
                                                (this.expirationFilter === 'valid' && !this.isExpired(license.expirationDate));
                        
                        return matchesSearch && matchesStatus && matchesType && matchesFifa && matchesExpiration;
                    });
                },
                licenseStats() {
                    const stats = {
                        valid: 0,
                        pending: 0,
                        expired: 0,
                        suspended: 0,
                        fifaSynced: 0
                    };
                    
                    this.licenses.forEach(license => {
                        if (license.status === 'valid') stats.valid++;
                        else if (license.status === 'pending') stats.pending++;
                        else if (license.status === 'expired') stats.expired++;
                        else if (license.status === 'suspended') stats.suspended++;
                        
                        if (license.fifaSyncStatus === 'synced') stats.fifaSynced++;
                    });
                    
                    return stats;
                }
            },
            methods: {
                getTypeClass(type) {
                    const classes = {
                        'player': 'bg-blue-100 text-blue-800',
                        'coach': 'bg-green-100 text-green-800',
                        'referee': 'bg-purple-100 text-purple-800',
                        'medical': 'bg-red-100 text-red-800',
                        'administrative': 'bg-gray-100 text-gray-800'
                    };
                    return classes[type] || 'bg-gray-100 text-gray-800';
                },
                getStatusClass(status) {
                    const classes = {
                        'valid': 'bg-green-100 text-green-800',
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'expired': 'bg-red-100 text-red-800',
                        'suspended': 'bg-orange-100 text-orange-800',
                        'revoked': 'bg-gray-100 text-gray-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                },
                getStatusText(status) {
                    const texts = {
                        'valid': 'Valide',
                        'pending': 'En attente',
                        'expired': 'Expirée',
                        'suspended': 'Suspendue',
                        'revoked': 'Révoquée'
                    };
                    return texts[status] || status;
                },
                isExpired(date) {
                    return new Date(date) < new Date();
                },
                isExpiringSoon(date) {
                    const expirationDate = new Date(date);
                    const now = new Date();
                    const thirtyDaysFromNow = new Date(now.getTime() + (30 * 24 * 60 * 60 * 1000));
                    return expirationDate <= thirtyDaysFromNow && expirationDate > now;
                },
                viewLicense(license) {
                    alert(`Voir la licence: ${license.licenseNumber}`);
                },
                validateLicense(license) {
                    if (confirm(`Valider la licence "${license.licenseNumber}" ?`)) {
                        license.status = 'valid';
                        license.fifaSyncStatus = 'synced';
                        license.fifaSyncDate = new Date().toISOString().split('T')[0];
                    }
                },
                rejectLicense(license) {
                    if (confirm(`Rejeter la licence "${license.licenseNumber}" ?`)) {
                        license.status = 'revoked';
                    }
                },
                editLicense(license) {
                    this.editingLicenseId = license.id;
                    this.licenseForm = { ...license };
                    this.showEditLicenseModal = true;
                },
                saveLicense() {
                    if (this.showEditLicenseModal) {
                        // Modifier la licence existante
                        const index = this.licenses.findIndex(l => l.id === this.editingLicenseId);
                        if (index !== -1) {
                            this.licenses[index] = { ...this.licenses[index], ...this.licenseForm };
                        }
                    } else {
                        // Créer une nouvelle licence
                        const newLicense = {
                            id: Date.now(),
                            ...this.licenseForm,
                            status: 'pending',
                            fifaSyncDate: null
                        };
                        this.licenses.push(newLicense);
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showCreateLicenseModal = false;
                    this.showEditLicenseModal = false;
                    this.editingLicenseId = null;
                    this.licenseForm = {
                        licenseNumber: '',
                        type: '',
                        category: '',
                        holderName: '',
                        holderId: '',
                        fifaConnectId: '',
                        fifaSyncStatus: 'pending',
                        expirationDate: ''
                    };
                },
                validateBatch() {
                    if (confirm(`Valider [[ selectedLicenses.length ]] licences en lot ?`)) {
                        this.selectedLicenses.forEach(license => {
                            const index = this.licenses.findIndex(l => l.id === license.id);
                            if (index !== -1) {
                                this.licenses[index].status = 'valid';
                                this.licenses[index].fifaSyncStatus = 'synced';
                                this.licenses[index].fifaSyncDate = new Date().toISOString().split('T')[0];
                            }
                        });
                        this.selectedLicenses = [];
                        this.showBatchValidationModal = false;
                    }
                },
                removeFromSelection(license) {
                    this.selectedLicenses = this.selectedLicenses.filter(l => l.id !== license.id);
                }
            },
            mounted() {
                // Pré-sélectionner les licences en attente pour la validation en lot
                this.selectedLicenses = this.licenses.filter(l => l.status === 'pending');
            }
        }).mount('#app');
    </script>
</body>
</html>
