<template>
  <div class="eligible-players-selector">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">{{ $t('auto.key252') }}</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">{{ $t('auto.key253') }}</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div v-else>
      <!-- Competition Info -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-900">{{ competition.name }}</h3>
        <p class="text-sm text-blue-700">Saison: {{ competition.season }}</p>
        <p v-if="competition.require_federation_license" class="text-sm text-blue-700">
          <span class="font-medium">{{ $t('auto.key254') }}</span>
        </p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">{{ $t('auto.key255') }}</p>
              <p class="text-2xl font-bold text-green-900">{{ stats.eligible }}</p>
            </div>
          </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">{{ $t('auto.key256') }}</p>
              <p class="text-2xl font-bold text-red-900">{{ stats.ineligible }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-800">{{ $t('auto.key257') }}</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Eligible Players -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Joueurs Éligibles ({{ eligiblePlayers.length }})
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="player in eligiblePlayers" 
            :key="player.id"
            class="bg-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
            :class="{ 'ring-2 ring-blue-500': selectedPlayers.includes(player.id) }"
            @click="togglePlayerSelection(player.id)"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-green-800 font-semibold">{{ player.jersey_number || '?' }}</span>
                  </div>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">{{ player.name }}</h4>
                  <p class="text-xs text-gray-500">{{ player.position }} • {{ player.nationality }}</p>
                  <p class="text-xs text-gray-400">Rating: {{ player.overall_rating || 'N/A' }}</p>
                </div>
              </div>
              <div class="flex-shrink-0">
                <input 
                  type="checkbox" 
                  :checked="selectedPlayers.includes(player.id)"
                  @change="togglePlayerSelection(player.id)"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ineligible Players -->
      <div v-if="ineligiblePlayers.length > 0">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <svg class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          Joueurs Non Éligibles ({{ ineligiblePlayers.length }})
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="player in ineligiblePlayers" 
            :key="player.id"
            class="bg-white border border-red-200 rounded-lg p-4 opacity-75"
          >
            <div class="flex items-center space-x-3">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                  <span class="text-red-800 font-semibold">{{ player.jersey_number || '?' }}</span>
                </div>
              </div>
              <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-900">{{ player.name }}</h4>
                <p class="text-xs text-gray-500">{{ player.position }} • {{ player.nationality }}</p>
                <p class="text-xs text-red-600 font-medium mt-1">{{ player.ineligibility_reason }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Selection Summary -->
      <div v-if="selectedPlayers.length > 0" class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-900 mb-2">
          Joueurs sélectionnés ({{ selectedPlayers.length }})
        </h4>
        <div class="flex flex-wrap gap-2">
          <span 
            v-for="playerId in selectedPlayers" 
            :key="playerId"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
          >
            {{ getPlayerName(playerId) }}
            <button 
              @click="removePlayer(playerId)"
              class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500"
            >
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EligiblePlayersSelector',
  props: {
    competitionId: {
      type: [Number, String],
      required: true
    },
    initialSelected: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      loading: true,
      error: null,
      eligiblePlayers: [],
      ineligiblePlayers: [],
      selectedPlayers: [...this.initialSelected],
      competition: {},
      stats: {
        eligible: 0,
        ineligible: 0,
        total: 0
      }
    }
  },
  async mounted() {
    await this.loadEligiblePlayers()
  },
  methods: {
    async loadEligiblePlayers() {
      this.loading = true
      this.error = null
      
      try {
        const response = await fetch(`/api/club/eligible-players/${this.competitionId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        
        if (!response.ok) {
          throw new Error('Erreur lors du chargement des joueurs')
        }
        
        const data = await response.json()
        
        if (data.success) {
          this.eligiblePlayers = data.data.eligible_players
          this.ineligiblePlayers = data.data.ineligible_players
          this.competition = data.data.competition
          this.stats = {
            eligible: data.data.total_eligible,
            ineligible: data.data.total_ineligible,
            total: data.data.total_eligible + data.data.total_ineligible
          }
        } else {
          throw new Error(data.message || 'Erreur inconnue')
        }
      } catch (error) {
        this.error = error.message
        console.error('Error loading eligible players:', error)
      } finally {
        this.loading = false
      }
    },
    
    togglePlayerSelection(playerId) {
      const index = this.selectedPlayers.indexOf(playerId)
      if (index > -1) {
        this.selectedPlayers.splice(index, 1)
      } else {
        this.selectedPlayers.push(playerId)
      }
      this.$emit('selection-changed', this.selectedPlayers)
    },
    
    removePlayer(playerId) {
      const index = this.selectedPlayers.indexOf(playerId)
      if (index > -1) {
        this.selectedPlayers.splice(index, 1)
        this.$emit('selection-changed', this.selectedPlayers)
      }
    },
    
    getPlayerName(playerId) {
      const player = this.eligiblePlayers.find(p => p.id === playerId)
      return player ? player.name : 'Joueur inconnu'
    },
    
    getSelectedPlayers() {
      return this.selectedPlayers
    },
    
    clearSelection() {
      this.selectedPlayers = []
      this.$emit('selection-changed', [])
    }
  }
}
</script>

<style scoped>
.eligible-players-selector {
  @apply max-w-7xl mx-auto;
}
</style> 