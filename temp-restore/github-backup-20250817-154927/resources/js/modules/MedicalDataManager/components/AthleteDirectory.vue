<template>
  <div class="athlete-directory">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">
          <span class="text-red-600">👥</span> Répertoire des Athlètes
        </h1>
        <p class="text-gray-600 mt-2">Gestion des profils médicaux des athlètes</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
      >
        <span class="mr-2">➕</span> Nouvel Athlète
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
          <input 
            v-model="filters.search"
            type="text"
            placeholder="Nom de l'athlète..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Équipe</label>
          <select 
            v-model="filters.team_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="">Toutes les équipes</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">
              {{ team.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
          <select 
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="">Tous les statuts</option>
            <option value="active">Actif</option>
            <option value="injured">Blessé</option>
            <option value="inactive">Inactif</option>
          </select>
        </div>
        
        <div class="flex items-end">
          <button 
            @click="loadAthletes"
            class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
          >
            🔍 Filtrer
          </button>
        </div>
      </div>
    </div>

    <!-- Athletes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div 
        v-for="athlete in athletes" 
        :key="athlete.id"
        class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewAthlete(athlete.id)"
      >
        <!-- Athlete Card Header -->
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
              <span class="text-lg font-medium">{{ getInitials(athlete.name) }}</span>
            </div>
            <div class="flex space-x-2">
              <div class="w-3 h-3 rounded-full" :class="getHealthStatusColor(athlete.health_score)"></div>
              <span class="text-xs px-2 py-1 rounded-full" :class="getStatusClass(athlete.active)">
                {{ athlete.active ? 'Actif' : 'Inactif' }}
              </span>
            </div>
          </div>
          
          <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ athlete.name }}</h3>
          <p class="text-sm text-gray-600">{{ athlete.team?.name }}</p>
          <p class="text-xs text-gray-500">{{ athlete.position }} • {{ athlete.age }} ans</p>
        </div>

        <!-- Athlete Card Body -->
        <div class="p-6">
          <div class="space-y-3">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Score de santé:</span>
              <span class="font-medium" :class="getHealthScoreColor(athlete.health_score)">
                {{ athlete.health_score }}%
              </span>
            </div>
            
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Blessures actives:</span>
              <span class="font-medium">{{ athlete.medical_status?.has_active_injuries ? 'Oui' : 'Non' }}</span>
            </div>
            
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">PCMA en attente:</span>
              <span class="font-medium">{{ athlete.medical_status?.has_pending_pcma ? 'Oui' : 'Non' }}</span>
            </div>
            
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Alertes:</span>
              <span class="font-medium">{{ athlete.medical_status?.has_unresolved_alerts ? 'Oui' : 'Non' }}</span>
            </div>
          </div>
        </div>

        <!-- Athlete Card Footer -->
        <div class="px-6 py-3 bg-gray-50 rounded-b-lg">
          <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">FIFA ID: {{ athlete.fifa_id }}</span>
            <button 
              @click.stop="viewAthlete(athlete.id)"
              class="text-red-600 hover:text-red-700 text-sm font-medium"
            >
              Voir profil →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="mt-8 flex justify-center">
      <nav class="flex items-center space-x-2">
        <button 
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
        >
          Précédent
        </button>
        
        <span class="px-3 py-2 text-sm text-gray-700">
          Page {{ pagination.current_page }} sur {{ pagination.last_page }}
        </span>
        
        <button 
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
        >
          Suivant
        </button>
      </nav>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && athletes.length === 0" class="text-center py-12">
      <div class="text-6xl mb-4">👥</div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun athlète trouvé</h3>
      <p class="text-gray-600 mb-4">Aucun athlète ne correspond à vos critères de recherche.</p>
      <button 
        @click="resetFilters"
        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
      >
        Réinitialiser les filtres
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AthleteDirectory',
  data() {
    return {
      athletes: [],
      teams: [],
      loading: false,
      filters: {
        search: '',
        team_id: '',
        status: ''
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: 0
      },
      showCreateModal: false
    }
  },
  async mounted() {
    await this.loadTeams()
    await this.loadAthletes()
  },
  methods: {
    async loadAthletes() {
      this.loading = true
      try {
        const params = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page,
          ...this.filters
        }
        
        const response = await this.$http.get('/api/v1/athletes', { params })
        this.athletes = response.data.data
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total
        }
      } catch (error) {
        console.error('Error loading athletes:', error)
        this.$toast.error('Erreur lors du chargement des athlètes')
      } finally {
        this.loading = false
      }
    },

    async loadTeams() {
      try {
        const response = await this.$http.get('/api/v1/teams')
        this.teams = response.data.data
      } catch (error) {
        console.error('Error loading teams:', error)
      }
    },

    getInitials(name) {
      return name.split(' ').map(n => n[0]).join('').toUpperCase()
    },

    getHealthStatusColor(score) {
      if (score >= 80) return 'bg-green-500'
      if (score >= 60) return 'bg-yellow-500'
      return 'bg-red-500'
    },

    getHealthScoreColor(score) {
      if (score >= 80) return 'text-green-600'
      if (score >= 60) return 'text-yellow-600'
      return 'text-red-600'
    },

    getStatusClass(active) {
      return active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
    },

    viewAthlete(athleteId) {
      this.$router.push({ name: 'medical.player.profile', params: { id: athleteId } })
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page
        this.loadAthletes()
      }
    },

    resetFilters() {
      this.filters = {
        search: '',
        team_id: '',
        status: ''
      }
      this.pagination.current_page = 1
      this.loadAthletes()
    }
  }
}
</script>

<style scoped>
.athlete-directory {
  @apply p-6;
}
</style> 