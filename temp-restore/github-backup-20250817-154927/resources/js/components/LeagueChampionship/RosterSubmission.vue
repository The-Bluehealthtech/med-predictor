<template>
  <div class="roster-submission">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $t('auto.key438') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ match?.home_team?.name }} vs {{ match?.away_team?.name }}</p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="$router.go(-1)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Back
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Match Info -->
      <div v-if="match" class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key439') }}</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
          <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key440') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ match.matchday }}</dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key441') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(match.scheduled_at) }}</dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key442') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span :class="getStatusClass(match.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ match.status }}
                </span>
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Team Selection -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key443') }}</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $t('auto.key444') }}</p>
        </div>
        <div class="px-4 py-5 sm:px-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
              @click="selectTeam(match?.home_team)"
              :class="selectedTeam?.id === match?.home_team?.id ? 'ring-2 ring-indigo-500' : ''"
              class="relative p-4 border border-gray-300 rounded-lg hover:border-indigo-500 focus:outline-none"
            >
              <div class="text-center">
                <div class="text-lg font-medium text-gray-900">{{ match?.home_team?.name }}</div>
                <div class="text-sm text-gray-500">{{ $t('auto.key445') }}</div>
              </div>
            </button>
            <button
              @click="selectTeam(match?.away_team)"
              :class="selectedTeam?.id === match?.away_team?.id ? 'ring-2 ring-indigo-500' : ''"
              class="relative p-4 border border-gray-300 rounded-lg hover:border-indigo-500 focus:outline-none"
            >
              <div class="text-center">
                <div class="text-lg font-medium text-gray-900">{{ match?.away_team?.name }}</div>
                <div class="text-sm text-gray-500">{{ $t('auto.key446') }}</div>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Roster Builder -->
      <div v-if="selectedTeam" class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ selectedTeam.name }} - Roster Builder</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $t('auto.key447') }}</p>
        </div>
        
        <div class="px-4 py-5 sm:px-6">
          <!-- Available Players -->
          <div class="mb-6">
            <h4 class="text-md font-medium text-gray-900 mb-4">{{ $t('auto.key448') }}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="player in availablePlayers"
                :key="player.id"
                @click="addPlayer(player)"
                class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 cursor-pointer"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ player.name }}</div>
                    <div class="text-sm text-gray-500">{{ player.position }}</div>
                  </div>
                  <div class="text-sm text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Selected Players -->
          <div class="mb-6">
            <h4 class="text-md font-medium text-gray-900 mb-4">Selected Players ({{ selectedPlayers.length }}/18)</h4>
            
            <!-- Starters -->
            <div class="mb-4">
              <h5 class="text-sm font-medium text-gray-700 mb-2">Starters ({{ starters.length }}/11)</h5>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="player in selectedPlayers"
                  :key="player.id"
                  class="p-4 border border-gray-200 rounded-lg"
                >
                  <div class="flex items-center justify-between mb-2">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ player.name }}</div>
                      <div class="text-sm text-gray-500">{{ player.position }}</div>
                    </div>
                    <button
                      @click="removePlayer(player)"
                      class="text-red-600 hover:text-red-900"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div>
                      <label class="block text-xs font-medium text-gray-700">{{ $t('auto.key449') }}</label>
                      <input
                        v-model.number="player.jersey_number"
                        type="number"
                        min="1"
                        max="99"
                        class="mt-1 block w-16 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                      />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700">{{ $t('auto.key450') }}</label>
                      <input
                        v-model="player.is_starter"
                        type="checkbox"
                        :disabled="starters.length >= 11 && !player.is_starter"
                        class="mt-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Validation Messages -->
            <div v-if="validationErrors.length > 0" class="mb-4">
              <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ $t('auto.key451') }}</h3>
                    <div class="mt-2 text-sm text-red-700">
                      <ul class="list-disc pl-5 space-y-1">
                        <li v-for="error in validationErrors" :key="error">{{ error }}</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button
                @click="submitRoster"
                :disabled="!canSubmit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Submit Roster
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RosterSubmission',
  data() {
    return {
      match: null,
      selectedTeam: null,
      availablePlayers: [],
      selectedPlayers: [],
      validationErrors: []
    }
  },
  computed: {
    starters() {
      return this.selectedPlayers.filter(player => player.is_starter)
    },
    canSubmit() {
      return this.validationErrors.length === 0 && 
             this.selectedPlayers.length >= 11 && 
             this.selectedPlayers.length <= 18 &&
             this.starters.length === 11
    }
  },
  watch: {
    selectedPlayers: {
      handler() {
        this.validateRoster()
      },
      deep: true
    }
  },
  async mounted() {
    await this.loadMatch()
    await this.loadTeamPlayers()
  },
  methods: {
    async loadMatch() {
      try {
        const matchId = this.$route.params.id
        const response = await fetch(`/api/matches/${matchId}`, {
          headers: {
            'Authorization': `Bearer ${this.getToken()}`,
            'Content-Type': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.match = data.data
        }
      } catch (error) {
        console.error('Error loading match:', error)
      }
    },

    async loadTeamPlayers() {
      try {
        // This would typically load players from the selected team
        // For now, we'll use mock data
        this.availablePlayers = [
          { id: 1, name: 'John Doe', position: 'Goalkeeper' },
          { id: 2, name: 'Jane Smith', position: 'Defender' },
          { id: 3, name: 'Bob Johnson', position: 'Midfielder' },
          { id: 4, name: 'Alice Brown', position: 'Forward' },
          // Add more players as needed
        ]
      } catch (error) {
        console.error('Error loading team players:', error)
      }
    },

    selectTeam(team) {
      this.selectedTeam = team
      this.selectedPlayers = []
    },

    addPlayer(player) {
      if (this.selectedPlayers.length >= 18) {
        alert('Maximum 18 players allowed')
        return
      }
      
      if (this.selectedPlayers.find(p => p.id === player.id)) {
        alert('Player already selected')
        return
      }

      this.selectedPlayers.push({
        ...player,
        is_starter: this.starters.length < 11,
        jersey_number: this.getNextJerseyNumber()
      })
    },

    removePlayer(player) {
      const index = this.selectedPlayers.findIndex(p => p.id === player.id)
      if (index > -1) {
        this.selectedPlayers.splice(index, 1)
      }
    },

    getNextJerseyNumber() {
      const usedNumbers = this.selectedPlayers.map(p => p.jersey_number)
      for (let i = 1; i <= 99; i++) {
        if (!usedNumbers.includes(i)) {
          return i
        }
      }
      return 1
    },

    validateRoster() {
      this.validationErrors = []

      if (this.selectedPlayers.length < 11) {
        this.validationErrors.push('Minimum 11 players required')
      }

      if (this.selectedPlayers.length > 18) {
        this.validationErrors.push('Maximum 18 players allowed')
      }

      if (this.starters.length !== 11) {
        this.validationErrors.push('Exactly 11 starters required')
      }

      // Check for duplicate jersey numbers
      const jerseyNumbers = this.selectedPlayers.map(p => p.jersey_number)
      const duplicates = jerseyNumbers.filter((num, index) => jerseyNumbers.indexOf(num) !== index)
      if (duplicates.length > 0) {
        this.validationErrors.push('Duplicate jersey numbers not allowed')
      }

      // Check for missing jersey numbers
      const missingJerseys = this.selectedPlayers.filter(p => !p.jersey_number)
      if (missingJerseys.length > 0) {
        this.validationErrors.push('All players must have jersey numbers')
      }
    },

    async submitRoster() {
      if (!this.canSubmit) return

      try {
        const matchId = this.$route.params.id
        const response = await fetch(`/api/matches/${matchId}/submit-roster`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${this.getToken()}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            team_id: this.selectedTeam.id,
            players: this.selectedPlayers.map(player => ({
              player_id: player.id,
              position: player.position,
              is_starter: player.is_starter,
              jersey_number: player.jersey_number
            }))
          })
        })

        if (response.ok) {
          alert('Roster submitted successfully!')
          this.$router.go(-1)
        } else {
          const error = await response.json()
          alert('Error submitting roster: ' + error.message)
        }
      } catch (error) {
        console.error('Error submitting roster:', error)
        alert('Error submitting roster')
      }
    },

    getToken() {
      return localStorage.getItem('auth_token') || window.Laravel?.csrfToken
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString()
    },

    getStatusClass(status) {
      const classes = {
        'scheduled': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script> 