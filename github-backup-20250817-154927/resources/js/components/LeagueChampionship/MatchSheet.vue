<template>
  <div class="match-sheet">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Notifications -->
      <div v-if="notifications.success" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ notifications.success }}</p>
          </div>
          <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
              <button @click="notifications.success = null" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 transition-colors duration-200">
                <span class="sr-only">{{ $t('auto.key452') }}</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="notifications.error" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ notifications.error }}</p>
          </div>
          <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
              <button @click="notifications.error = null" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 transition-colors duration-200">
                <span class="sr-only">{{ $t('auto.key453') }}</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading.match" class="flex items-center justify-center min-h-64">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">{{ $t('auto.key454') }}</p>
        </div>
      </div>

      <!-- Match Content -->
      <div v-if="match && !loading.match">
        <!-- Match Header Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg mb-8 overflow-hidden">
          <div class="px-6 py-8">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center space-x-8">
                  <!-- Home Team -->
                  <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <span class="text-2xl font-bold text-indigo-600">{{ match.home_team?.name?.charAt(0) || 'H' }}</span>
                    </div>
                    <h3 class="text-white font-bold text-lg">{{ match.home_team?.name || 'Home Team' }}</h3>
                  </div>
                  
                  <!-- VS -->
                  <div class="text-center">
                    <div class="text-white text-4xl font-bold mb-2">{{ $t('auto.key455') }}</div>
                    <div class="text-indigo-200 text-sm">{{ formatDate(match.scheduled_at) }}</div>
                    <div class="text-indigo-200 text-sm">{{ formatTime(match.kickoff_time) }}</div>
                  </div>
                  
                  <!-- Away Team -->
                  <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-3 shadow-lg">
                      <span class="text-2xl font-bold text-indigo-600">{{ match.away_team?.name?.charAt(0) || 'A' }}</span>
                    </div>
                    <h3 class="text-white font-bold text-lg">{{ match.away_team?.name || 'Away Team' }}</h3>
                  </div>
                </div>
              </div>
              
              <!-- Match Status -->
              <div class="text-right">
                <div :class="getStatusClass(match.status)" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium mb-2">
                  {{ $t('auto.key1') }}
                </div>
                <div class="text-indigo-200 text-sm">Matchday {{ match.matchday }}</div>
                <div class="text-indigo-200 text-sm">{{ match.stadium || 'Venue TBD' }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- {{ $t('auto.key2') }} Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Match Details -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $t('auto.key2') }}
              </h3>
            </div>
            <div class="p-6">
              <dl class="grid grid-cols-1 gap-4">
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key456') }}</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(match.scheduled_at) }} at {{ formatTime(match.kickoff_time) }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key457') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.stadium || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key458') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.capacity ? match.capacity.toLocaleString() : 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key459') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.weather_conditions || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key460') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.pitch_condition || 'TBD' }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- {{ $t('auto.key3') }}s -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ $t('auto.key3') }}s
              </h3>
            </div>
            <div class="p-6">
              <dl class="grid grid-cols-1 gap-4">
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key461') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.referee || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key462') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.assistant_referee_1 || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key463') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.assistant_referee_2 || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key464') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.fourth_official || 'TBD' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key465') }}</dt>
                  <dd class="text-sm text-gray-900">{{ match.var_referee || 'TBD' }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Workflow Status -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              {{ $t('auto.key4') }}s
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <!-- Workflow Steps -->
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">{{ $t('auto.key466') }}</div>
                  <div class="text-sm text-gray-500">{{ $t('auto.key467') }}</div>
                </div>
              </div>
              
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div :class="match.status === 'in_progress' || match.status === 'completed' ? 'bg-green-100' : 'bg-gray-100'" class="h-10 w-10 rounded-full flex items-center justify-center">
                    <svg v-if="match.status === 'in_progress' || match.status === 'completed'" class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">{{ $t('auto.key468') }}</div>
                  <div class="text-sm text-gray-500">{{ $t('auto.key469') }}</div>
                </div>
              </div>
              
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div :class="match.status === 'completed' ? 'bg-green-100' : 'bg-gray-100'" class="h-10 w-10 rounded-full flex items-center justify-center">
                    <svg v-if="match.status === 'completed'" class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">{{ $t('auto.key470') }}</div>
                  <div class="text-sm text-gray-500">{{ $t('auto.key471') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center">
          <button @click="viewMatchSheet" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            {{ $t('auto.key5') }}
          </button>
          
          <button @click="exportData" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ $t('auto.key6') }}
          </button>
          
          <button @click="printReport" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            {{ $t('auto.key7') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MatchSheet',
  data() {
    return {
      match: null,
      events: [],
      homeRoster: [],
      awayRoster: [],
      showAddEvent: false,
      loading: {
        match: true,
        events: true,
        rosters: true,
        score: false,
        statistics: false,
        events: false
      },
      notifications: {
        success: null,
        error: null
      },
      scoreForm: {
        home_score: 0,
        away_score: 0
      },
      eventForm: {
        type: '',
        minute: '',
        player_id: '',
        description: ''
      },
      statsForm: {
        home_shots: 0,
        home_shots_on_target: 0,
        home_possession: 0,
        home_corners: 0,
        home_fouls: 0,
        home_offsides: 0,
        away_shots: 0,
        away_shots_on_target: 0,
        away_possession: 0,
        away_corners: 0,
        away_fouls: 0,
        away_offsides: 0
      }
    }
  },
  computed: {
    allPlayers() {
      return [...this.homeRoster, ...this.awayRoster]
    }
  },
  async mounted() {
    await this.loadMatch()
    await this.loadEvents()
    await this.loadRosters()
  },
  methods: {
    async loadMatch() {
      try {
        this.loading.match = true
        this.notifications.error = null
        
        const matchId = this.$route.params.id
        
        // Get the API base URL from Laravel data
        const laravelData = window.Laravel || {}
        const apiUrl = laravelData.apiUrl || '/api'
        
        const response = await fetch(`${apiUrl}/league-championship/matches/${matchId}`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': laravelData.csrfToken || ''
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.match = data.data
          this.scoreForm.home_score = this.match.home_score || 0
          this.scoreForm.away_score = this.match.away_score || 0
          
          // Populate stats form
          this.statsForm = {
            home_shots: this.match.home_shots || 0,
            home_shots_on_target: this.match.home_shots_on_target || 0,
            home_possession: this.match.home_possession || 0,
            home_corners: this.match.home_corners || 0,
            home_fouls: this.match.home_fouls || 0,
            home_offsides: this.match.home_offsides || 0,
            away_shots: this.match.away_shots || 0,
            away_shots_on_target: this.match.away_shots_on_target || 0,
            away_possession: this.match.away_possession || 0,
            away_corners: this.match.away_corners || 0,
            away_fouls: this.match.away_fouls || 0,
            away_offsides: this.match.away_offsides || 0
          }
        } else {
          this.notifications.error = `Failed to load match: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error loading match: ${error.message}`
      } finally {
        this.loading.match = false
      }
    },

    async loadEvents() {
      try {
        this.loading.events = true
        this.notifications.error = null
        
        const matchId = this.$route.params.id
        
        const laravelData = window.Laravel || {}
        const apiUrl = laravelData.apiUrl || '/api'
        
        const response = await fetch(`${apiUrl}/league-championship/matches/${matchId}/events`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': laravelData.csrfToken || ''
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.events = data.data || []
        } else {
          this.notifications.error = `Failed to load events: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error loading events: ${error.message}`
      } finally {
        this.loading.events = false
      }
    },

    async loadRosters() {
      try {
        this.loading.rosters = true
        this.notifications.error = null
        
        const matchId = this.$route.params.id
        
        const laravelData = window.Laravel || {}
        const apiUrl = laravelData.apiUrl || '/api'
        
        const response = await fetch(`${apiUrl}/league-championship/matches/${matchId}/rosters`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': laravelData.csrfToken || ''
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          const rosters = data.data || []
          
          this.homeRoster = rosters.find(r => r.team_id === this.match?.home_team_id)?.players || []
          this.awayRoster = rosters.find(r => r.team_id === this.match?.away_team_id)?.players || []
        } else {
          this.notifications.error = `Failed to load rosters: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error loading rosters: ${error.message}`
      } finally {
        this.loading.rosters = false
      }
    },

    async updateScore() {
      try {
        this.loading.score = true
        this.notifications.error = null
        this.notifications.success = null
        
        const matchId = this.$route.params.id
        const response = await fetch(`/api/league-championship/matches/${matchId}/status`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            status: this.match.status,
            home_score: this.scoreForm.home_score,
            away_score: this.scoreForm.away_score
          })
        })

        if (response.ok) {
          await this.loadMatch()
          this.notifications.success = 'Score updated successfully'
        } else {
          this.notifications.error = `Failed to update score: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error updating score: ${error.message}`
      } finally {
        this.loading.score = false
      }
    },

    async addEvent() {
      try {
        this.loading.events = true
        this.notifications.error = null
        this.notifications.success = null
        
        const matchId = this.$route.params.id
        const response = await fetch(`/api/league-championship/matches/${matchId}/events`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify(this.eventForm)
        })

        if (response.ok) {
          this.showAddEvent = false
          this.eventForm = { type: '', minute: '', player_id: '', description: '' }
          await this.loadEvents()
          this.notifications.success = 'Event added successfully'
        } else {
          this.notifications.error = `Failed to add event: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error adding event: ${error.message}`
      } finally {
        this.loading.events = false
      }
    },

    async deleteEvent(event) {
      if (!confirm('Are you sure you want to delete this event?')) return

      try {
        this.loading.events = true
        this.notifications.error = null
        this.notifications.success = null
        
        const matchId = this.$route.params.id
        const response = await fetch(`/api/league-championship/matches/${matchId}/events/${event.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin'
        })

        if (response.ok) {
          await this.loadEvents()
          this.notifications.success = 'Event deleted successfully'
        } else {
          this.notifications.error = `Failed to delete event: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error deleting event: ${error.message}`
      } finally {
        this.loading.events = false
      }
    },

    async updateStatistics() {
      try {
        this.loading.statistics = true
        this.notifications.error = null
        this.notifications.success = null
        
        const matchId = this.$route.params.id
        const response = await fetch(`/api/league-championship/matches/${matchId}/statistics`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify(this.statsForm)
        })

        if (response.ok) {
          await this.loadMatch()
          this.notifications.success = 'Statistics updated successfully'
        } else {
          this.notifications.error = `Failed to update statistics: ${response.status} ${response.statusText}`
        }
      } catch (error) {
        this.notifications.error = `Error updating statistics: ${error.message}`
      } finally {
        this.loading.statistics = false
      }
    },

    getToken() {
      return localStorage.getItem('auth_token') || window.Laravel?.csrfToken
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString()
    },

    formatTime(minute) {
      return `${minute}'`
    },

    formatStatus(status) {
      const statusMap = {
        'scheduled': 'Scheduled',
        'in_progress': 'In Progress',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
      }
      return statusMap[status] || status
    },

    getStatusClass(status) {
      const classes = {
        'scheduled': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },

    getEventTypeClass(type) {
      const classes = {
        'goal': 'bg-green-100 text-green-800',
        'yellow_card': 'bg-yellow-100 text-yellow-800',
        'red_card': 'bg-red-100 text-red-800',
        'substitution': 'bg-blue-100 text-blue-800',
        'injury': 'bg-orange-100 text-orange-800'
      }
      return classes[type] || 'bg-gray-100 text-gray-800'
    },

    getEventIconClass(type) {
      const classes = {
        'goal': 'bg-green-100 text-green-600',
        'yellow_card': 'bg-yellow-100 text-yellow-600',
        'red_card': 'bg-red-100 text-red-600',
        'substitution': 'bg-blue-100 text-blue-600',
        'injury': 'bg-orange-100 text-orange-600'
      }
      return classes[type] || 'bg-gray-100 text-gray-600'
    },

    getEventIcon(type) {
      const icons = {
        'goal': 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        'yellow_card': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        'red_card': 'M6 18L18 6M6 6l12 12',
        'substitution': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        'injury': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'
      }
      return icons[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },

    viewMatchSheet() {
      // Navigate to the match sheet route
      const matchId = this.$route.params.id;
      window.location.href = `/matches/${matchId}/match-sheet`;
    },

    exportData() {
      // Export match data as JSON
      const matchData = {
        match: this.match,
        events: this.events,
        homeRoster: this.homeRoster,
        awayRoster: this.awayRoster,
        exportDate: new Date().toISOString()
      };
      
      const dataStr = JSON.stringify(matchData, null, 2);
      const dataBlob = new Blob([dataStr], { type: 'application/json' });
      const url = URL.createObjectURL(dataBlob);
      
      const link = document.createElement('a');
      link.href = url;
      link.download = `match-${this.match?.id || 'data'}-${new Date().toISOString().split('T')[0]}.json`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
      
      this.notifications.success = 'Match data exported successfully';
    },

    printReport() {
      // Print the current match view
      const printContent = document.querySelector('.match-sheet').innerHTML;
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
          <head>
            <title>Match Report - ${this.match?.home_team?.name || 'Home'} vs ${this.match?.away_team?.name || 'Away'}</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 20px; }
              .match-header { text-align: center; margin-bottom: 30px; }
              .team-info { display: flex; justify-content: space-around; margin: 20px 0; }
              .match-details { margin: 20px 0; }
              .events { margin: 20px 0; }
              @media print { body { margin: 0; } }
            </style>
          </head>
          <body>
            <div class="match-header">
              <h1>Match Report</h1>
              <h2>${this.match?.home_team?.name || 'Home'} vs ${this.match?.away_team?.name || 'Away'}</h2>
              <p>Date: ${this.formatDate(this.match?.scheduled_at)} | Time: ${this.formatTime(this.match?.kickoff_time)}</p>
            </div>
            ${printContent}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
    }
  }
}
</script> 