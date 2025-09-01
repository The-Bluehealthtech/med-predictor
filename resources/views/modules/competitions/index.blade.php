@extends('layouts.app')

@section('title', 'Liste des Compétitions - FIFA Connect')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    📋 Liste des Compétitions
                </h1>
                <p class="text-gray-600">
                    Gestion et suivi de toutes les compétitions FIFA Connect
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('competitions.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    🏠 Dashboard
                </a>
                <a href="{{ route('competitions.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    ➕ Nouvelle Compétition
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">🔍 Filtres Avancés</h2>
            <button @click="toggleAdvancedFilters" 
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <span v-if="showAdvancedFilters">Masquer</span>
                <span v-else>Afficher</span> les filtres avancés
            </button>
        </div>
        
        <!-- Filtres de base -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <input type="text" 
                       v-model="filters.search" 
                       placeholder="Rechercher..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <select v-model="filters.association_id" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Toutes les associations</option>
                @foreach($associations as $association)
                    <option value="{{ $association->id }}">{{ $association->name }}</option>
                @endforeach
            </select>
            
            <select v-model="filters.season_id" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Toutes les saisons</option>
                @foreach($seasons as $season)
                    <option value="{{ $season->id }}">{{ $season->name }}</option>
                @endforeach
            </select>
            
            <select v-model="filters.status" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tous les statuts</option>
                <option value="draft">Brouillon</option>
                <option value="submitted">Soumis</option>
                <option value="validated">Validé</option>
                <option value="published">Publié</option>
                <option value="cancelled">Annulé</option>
            </select>
        </div>
        
        <!-- Filtres avancés -->
        <div v-show="showAdvancedFilters" class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select v-model="filters.type" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les types</option>
                    <option value="championship">Championnat</option>
                    <option value="cup">Coupe</option>
                    <option value="tournament">Tournoi</option>
                    <option value="friendly">Match amical</option>
                    <option value="international">International</option>
                </select>
                
                <select v-model="filters.category" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les catégories</option>
                    <option value="senior">Senior</option>
                    <option value="youth">Jeunesse</option>
                    <option value="women">Féminin</option>
                    <option value="futsal">Futsal</option>
                    <option value="beach">Beach Soccer</option>
                </select>
                
                <select v-model="filters.format" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les formats</option>
                    <option value="round_robin">Aller-retour</option>
                    <option value="knockout">Élimination directe</option>
                    <option value="group_final">Groupes + Finale</option>
                    <option value="mixed">Mixte</option>
                </select>
                
                <select v-model="filters.date_range" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les dates</option>
                    <option value="upcoming">À venir</option>
                    <option value="active">En cours</option>
                    <option value="completed">Terminées</option>
                </select>
            </div>
        </div>
        
        <!-- Boutons d'action des filtres -->
        <div class="flex justify-between items-center mt-4">
            <div class="flex space-x-2">
                <button @click="applyFilters" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                    🔍 Appliquer les filtres
                </button>
                
                <button @click="clearFilters" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    🗑️ Effacer
                </button>
            </div>
            
            <div class="text-sm text-gray-600">
                <span v-text="filteredCompetitions.length"></span> compétition(s) trouvée(s)
            </div>
        </div>
    </div>

    <!-- Actions en lot -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <input type="checkbox" 
                       @change="toggleSelectAll" 
                       :checked="isAllSelected"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">
                    Sélectionner tout (<span v-text="selectedCompetitions.length"></span>)
                </span>
            </div>
            
            <div class="flex space-x-2" v-show="selectedCompetitions.length > 0">
                <button @click="bulkSubmit" 
                        :disabled="!canBulkSubmit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors disabled:opacity-50">
                    📤 Soumettre en lot
                </button>
                
                <button @click="bulkValidate" 
                        :disabled="!canBulkValidate"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors disabled:opacity-50">
                    ✅ Valider en lot
                </button>
                
                <button @click="bulkPublish" 
                        :disabled="!canBulkPublish"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors disabled:opacity-50">
                    🚀 Publier en lot
                </button>
                
                <button @click="bulkDelete" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                    🗑️ Supprimer en lot
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des compétitions -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" 
                                   @change="toggleSelectAll" 
                                   :checked="isAllSelected"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Compétition
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Association
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saison
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($competitions as $competition)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   :value="{{ $competition->id }}"
                                   v-model="selectedCompetitions"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ strtoupper(substr($competition->short_name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $competition->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $competition->type_label }} - {{ $competition->category_label }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        ID: {{ $competition->fifa_connect_id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $competition->association->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $competition->season->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                       @if($competition->status === 'published') bg-green-100 text-green-800
                                       @elseif($competition->status === 'validated') bg-blue-100 text-blue-800
                                       @elseif($competition->status === 'submitted') bg-yellow-100 text-yellow-800
                                       @elseif($competition->status === 'draft') bg-gray-100 text-gray-800
                                       @else bg-red-100 text-red-800 @endif">
                                {{ $competition->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $competition->start_date->format('d/m/Y') }}</div>
                            <div class="text-gray-500">au {{ $competition->end_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('competitions.show', $competition) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    👁️ Voir
                                </a>
                                <a href="{{ route('competitions.edit', $competition) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    ✏️ Modifier
                                </a>
                                
                                @if($competition->status === 'draft')
                                    <button @click="submitCompetition({{ $competition->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        📤 Soumettre
                                    </button>
                                @endif
                                
                                @if($competition->status === 'submitted' && auth()->user()->role === 'federation_admin')
                                    <button @click="validateCompetition({{ $competition->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        ✅ Valider
                                    </button>
                                @endif
                                
                                @if($competition->status === 'validated')
                                    <button @click="publishCompetition({{ $competition->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        🚀 Publier
                                    </button>
                                @endif
                                
                                @if($competition->status === 'published')
                                    <a href="{{ route('competition-management.show', $competition) }}" 
                                       class="text-orange-600 hover:text-orange-900">
                                        📊 Gérer
                                    </a>
                                @endif
                                
                                <button @click="deleteCompetition({{ $competition->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    🗑️ Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-lg font-medium mb-2">Aucune compétition trouvée</p>
                                <p class="text-sm">Ajustez vos filtres ou créez une nouvelle compétition</p>
                                <a href="{{ route('competitions.create') }}" 
                                   class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                    ➕ Créer une compétition
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($competitions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $competitions->links() }}
        </div>
        @endif
    </div>

    <!-- Statistiques de la liste -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total affiché</p>
                    <p class="text-2xl font-semibold text-gray-900" v-text="filteredCompetitions.length"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Publiées</p>
                    <p class="text-2xl font-semibold text-gray-900" v-text="publishedCount"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En attente</p>
                    <p class="text-2xl font-semibold text-gray-900" v-text="pendingCount"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Actives</p>
                    <p class="text-2xl font-semibold text-gray-900" v-text="activeCount"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div v-if="showConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                Confirmation
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" v-text="confirmMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button @click="confirmAction" 
                        class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Confirmer
                </button>
                <button @click="cancelAction" 
                        class="mt-2 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const app = new Vue({
    el: '#app',
    data: {
        filters: {
            search: '',
            association_id: '',
            season_id: '',
            status: '',
            type: '',
            category: '',
            format: '',
            date_range: ''
        },
        showAdvancedFilters: false,
        selectedCompetitions: [],
        showConfirmModal: false,
        confirmMessage: '',
        pendingAction: null,
        pendingCompetitionId: null,
        competitions: @json($competitions->items())
    },
    
    computed: {
        filteredCompetitions() {
            let filtered = this.competitions;
            
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(c => 
                    c.name.toLowerCase().includes(search) ||
                    c.short_name.toLowerCase().includes(search)
                );
            }
            
            if (this.filters.association_id) {
                filtered = filtered.filter(c => c.association_id == this.filters.association_id);
            }
            
            if (this.filters.season_id) {
                filtered = filtered.filter(c => c.season_id == this.filters.season_id);
            }
            
            if (this.filters.status) {
                filtered = filtered.filter(c => c.status === this.filters.status);
            }
            
            if (this.filters.type) {
                filtered = filtered.filter(c => c.type === this.filters.type);
            }
            
            if (this.filters.category) {
                filtered = filtered.filter(c => c.category === this.filters.category);
            }
            
            if (this.filters.format) {
                filtered = filtered.filter(c => c.format === this.filters.format);
            }
            
            return filtered;
        },
        
        isAllSelected() {
            return this.filteredCompetitions.length > 0 && 
                   this.selectedCompetitions.length === this.filteredCompetitions.length;
        },
        
        publishedCount() {
            return this.filteredCompetitions.filter(c => c.status === 'published').length;
        },
        
        pendingCount() {
            return this.filteredCompetitions.filter(c => c.status === 'submitted').length;
        },
        
        activeCount() {
            return this.filteredCompetitions.filter(c => c.status === 'published').length;
        },
        
        canBulkSubmit() {
            return this.selectedCompetitions.length > 0 && 
                   this.selectedCompetitions.every(id => {
                       const comp = this.competitions.find(c => c.id === id);
                       return comp && comp.status === 'draft';
                   });
        },
        
        canBulkValidate() {
            return this.selectedCompetitions.length > 0 && 
                   this.selectedCompetitions.every(id => {
                       const comp = this.competitions.find(c => c.id === id);
                       return comp && comp.status === 'submitted';
                   });
        },
        
        canBulkPublish() {
            return this.selectedCompetitions.length > 0 && 
                   this.selectedCompetitions.every(id => {
                       const comp = this.competitions.find(c => c.id === id);
                       return comp && comp.status === 'validated';
                   });
        }
    },
    
    methods: {
        toggleAdvancedFilters() {
            this.showAdvancedFilters = !this.showAdvancedFilters;
        },
        
        applyFilters() {
            // Les filtres sont appliqués automatiquement via computed
            console.log('Filtres appliqués');
        },
        
        clearFilters() {
            this.filters = {
                search: '',
                association_id: '',
                season_id: '',
                status: '',
                type: '',
                category: '',
                format: '',
                date_range: ''
            };
        },
        
        toggleSelectAll() {
            if (this.isAllSelected) {
                this.selectedCompetitions = [];
            } else {
                this.selectedCompetitions = this.filteredCompetitions.map(c => c.id);
            }
        },
        
        submitCompetition(competitionId) {
            this.showConfirmModal = true;
            this.confirmMessage = 'Êtes-vous sûr de vouloir soumettre cette compétition pour validation ?';
            this.pendingAction = 'submit';
            this.pendingCompetitionId = competitionId;
        },
        
        validateCompetition(competitionId) {
            this.showConfirmModal = true;
            this.confirmMessage = 'Êtes-vous sûr de vouloir valider cette compétition ?';
            this.pendingAction = 'validate';
            this.pendingCompetitionId = competitionId;
        },
        
        publishCompetition(competitionId) {
            this.showConfirmModal = true;
            this.confirmMessage = 'Êtes-vous sûr de vouloir publier cette compétition ?';
            this.pendingAction = 'publish';
            this.pendingCompetitionId = competitionId;
        },
        
        deleteCompetition(competitionId) {
            this.showConfirmModal = true;
            this.confirmMessage = 'Êtes-vous sûr de vouloir supprimer cette compétition ? Cette action est irréversible.';
            this.pendingAction = 'delete';
            this.pendingCompetitionId = competitionId;
        },
        
        bulkSubmit() {
            this.showConfirmModal = true;
            this.confirmMessage = `Êtes-vous sûr de vouloir soumettre ${this.selectedCompetitions.length} compétition(s) pour validation ?`;
            this.pendingAction = 'bulkSubmit';
        },
        
        bulkValidate() {
            this.showConfirmModal = true;
            this.confirmMessage = `Êtes-vous sûr de vouloir valider ${this.selectedCompetitions.length} compétition(s) ?`;
            this.pendingAction = 'bulkValidate';
        },
        
        bulkPublish() {
            this.showConfirmModal = true;
            this.confirmMessage = `Êtes-vous sûr de vouloir publier ${this.selectedCompetitions.length} compétition(s) ?`;
            this.pendingAction = 'bulkPublish';
        },
        
        bulkDelete() {
            this.showConfirmModal = true;
            this.confirmMessage = `Êtes-vous sûr de vouloir supprimer ${this.selectedCompetitions.length} compétition(s) ? Cette action est irréversible.`;
            this.pendingAction = 'bulkDelete';
        },
        
        confirmAction() {
            if (this.pendingAction && this.pendingCompetitionId) {
                this.executeAction(this.pendingAction, this.pendingCompetitionId);
            } else if (this.pendingAction && this.pendingAction.startsWith('bulk')) {
                this.executeBulkAction(this.pendingAction);
            }
            this.showConfirmModal = false;
        },
        
        cancelAction() {
            this.showConfirmModal = false;
            this.pendingAction = null;
            this.pendingCompetitionId = null;
        },
        
        async executeAction(action, competitionId) {
            try {
                const response = await fetch(`/competitions/${competitionId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert('Erreur: ' + (error.message || 'Action échouée'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'exécution de l\'action');
            }
        },
        
        async executeBulkAction(action) {
            try {
                const response = await fetch(`/competitions/bulk/${action.replace('bulk', '').toLowerCase()}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        competition_ids: this.selectedCompetitions
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert('Erreur: ' + (error.message || 'Action échouée'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'exécution de l\'action en lot');
            }
        }
    }
});
</script>
@endpush 