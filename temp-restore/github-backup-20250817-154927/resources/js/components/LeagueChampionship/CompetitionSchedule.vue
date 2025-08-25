<template>
  <div class="competition-schedule">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ competition?.name }} - Schedule</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $t('auto.key510') }}</p>
          </div>
          <div class="flex space-x-3">
            <button
              v-if="!scheduleGenerated"
              @click="showGenerateSchedule = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Generate Schedule
            </button>
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
      <!-- Competition Info -->
      <div v-if="competition" class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key511') }}</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ competition.description }}</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
          <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key512') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span :class="getStatusClass(competition.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ competition.status }}
                </span>
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key513') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ competition.teams?.length || 0 }} teams</dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key514') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ matches.length }}</dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">{{ $t('auto.key515') }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ completedMatches }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Schedule Generation -->
      <div v-if="!scheduleGenerated" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">{{ $t('auto.key516') }}</h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>{{ $t('auto.key517') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Matches List -->
      <div v-if="scheduleGenerated" class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key518') }}</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $t('auto.key519') }}</p>
        </div>
        
        <!-- Matchday Filter -->
        <div class="px-4 py-3 border-b border-gray-200">
          <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">{{ $t('auto.key520') }}</label>
            <select
              v-model="selectedMatchday"
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">{{ $t('auto.key521') }}</option>
              <option v-for="matchday in matchdays" :key="matchday" :value="matchday">
                Matchday {{ matchday }}
              </option>
            </select>
          </div>
        </div>

        <ul class="divide-y divide-gray-200">
          <li v-for="match in filteredMatches" :key="match.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ match.home_team?.club?.name || match.home_team?.name }}
                  <span v-if="match.home_team?.club?.name" class="text-xs text-gray-500">({{ match.home_team?.name }})</span>
                </div>
                <div class="text-sm text-gray-500">{{ $t('auto.key522') }}</div>
                <div class="text-sm font-medium text-gray-900">
                  {{ match.away_team?.club?.name || match.away_team?.name }}
                  <span v-if="match.away_team?.club?.name" class="text-xs text-gray-500">({{ match.away_team?.name }})</span>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                  <span class="font-medium">Matchday {{ match.matchday }}</span>
                  <br>
                  {{ formatDate(match.scheduled_at) }}
                </div>
                <span :class="getStatusClass(match.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ match.status }}
                </span>
                <div v-if="match.status === 'completed'" class="text-sm font-medium text-gray-900">
                  {{ match.home_score }} - {{ match.away_score }}
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="viewMatch(match)"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    View
                  </button>
                  <button
                    v-if="match.status === 'scheduled'"
                    @click="updateMatchStatus(match, 'in_progress')"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    Start
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Generate Schedule Modal -->
    <div v-if="showGenerateSchedule" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('auto.key523') }}</h3>
          <form @submit.prevent="generateSchedule">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">{{ $t('auto.key524') }}</label>
              <input
                v-model="scheduleForm.start_date"
                type="date"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">{{ $t('auto.key525') }}</label>
              <input
                v-model="scheduleForm.match_interval_days"
                type="number"
                min="1"
                max="14"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showGenerateSchedule = false"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="generating"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
              >
                {{ generating ? 'Generating...' : 'Generate' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompetitionSchedule',
  props: {
    competitionId: {
      type: [Number, String],
      default: 1
    }
  },
  data() {
    return {
      competition: null,
      matches: [],
      selectedMatchday: '',
      showGenerateSchedule: false,
      generating: false,
      scheduleForm: {
        start_date: '',
        match_interval_days: 7
      }
    }
  },
  computed: {
    scheduleGenerated() {
      return this.matches.length > 0
    },
    completedMatches() {
      return this.matches.filter(match => match.status === 'completed').length
    },
    matchdays() {
      return [...new Set(this.matches.map(match => match.matchday))].sort((a, b) => a - b)
    },
    filteredMatches() {
      if (!this.selectedMatchday) {
        return this.matches
      }
      return this.matches.filter(match => match.matchday === parseInt(this.selectedMatchday))
    }
  },
  async mounted() {
    await this.loadCompetition()
    await this.loadMatches()
  },
  methods: {
    async loadCompetition() {
      try {
        const competitionId = this.competitionId || this.$route.params.id || 1
        
        const response = await fetch(`/api/league-championship/competitions/${competitionId}`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.competition = data.data
        } else {
          console.error('Failed to load competition:', response.status, response.statusText)
          const errorData = await response.text()
          console.error('Error response body:', errorData)
        }
      } catch (error) {
        console.error('Error loading competition:', error)
      }
    },

    async loadMatches() {
      try {
        const competitionId = this.competitionId || this.$route.params.id || 1
        const response = await fetch(`/api/league-championship/competitions/${competitionId}/schedule`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.matches = data.data || []
        } else {
          console.error('Failed to load matches:', response.status, response.statusText)
        }
      } catch (error) {
        console.error('Error loading matches:', error)
      }
    },

    async generateSchedule() {
      try {
        this.generating = true
        const competitionId = this.competitionId || this.$route.params.id || 1
        const response = await fetch(`/api/league-championship/competitions/${competitionId}/generate-schedule`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify(this.scheduleForm)
        })

        if (response.ok) {
          this.showGenerateSchedule = false
          await this.loadMatches()
        } else {
          const error = await response.json()
          alert('Error generating schedule: ' + error.message)
        }
      } catch (error) {
        console.error('Error generating schedule:', error)
        alert('Error generating schedule')
      } finally {
        this.generating = false
      }
    },

    async updateMatchStatus(match, status) {
      try {
        const response = await fetch(`/api/matches/${match.id}/status`, {
          method: 'PUT',
          headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({ status })
        })

        if (response.ok) {
          await this.loadMatches()
        }
      } catch (error) {
        console.error('Error updating match status:', error)
      }
    },

    viewMatch(match) {
      this.$router.push(`/match/${match.id}`)
    },

    getToken() {
      return window.Laravel?.csrfToken
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