<template>
  <div class="competition-standings">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ competition?.name }} - Standings</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $t('auto.key492') }}</p>
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
      <!-- Standings Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key493') }}</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $t('auto.key494') }}</p>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key495') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key496') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key497') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key498') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key499') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key500') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key501') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key502') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key503') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key504') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('auto.key505') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(standing, index) in standings" :key="standing.team.id" :class="getRowClass(index)">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ standing.position }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-600">{{ standing.team.name.charAt(0) }}</span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ standing.team.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.played }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.won }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.drawn }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.lost }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.goals_scored }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ standing.stats.goals_conceded }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span :class="getGoalDifferenceClass(standing.stats.goal_difference)">
                    {{ standing.stats.goal_difference > 0 ? '+' : '' }}{{ standing.stats.goal_difference }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ standing.stats.points }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="flex space-x-1">
                    <span
                      v-for="(result, i) in standing.form"
                      :key="i"
                      :class="getFormClass(result)"
                      class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded"
                    >
                      {{ result }}
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Scorers -->
      <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key506') }}</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $t('auto.key507') }}</p>
        </div>
        <ul class="divide-y divide-gray-200">
          <li v-for="(player, index) in topScorers" :key="player.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-sm font-medium text-indigo-600">{{ index + 1 }}</span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ player.player?.name }}</div>
                  <div class="text-sm text-gray-500">{{ player.team?.name }}</div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">{{ player.matches_played }} matches</div>
                <div class="text-sm font-medium text-gray-900">{{ player.goals }} goals</div>
                <div class="text-sm text-gray-500">{{ player.assists }} assists</div>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Team Statistics -->
      <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Most Wins -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key508') }}</h3>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="(team, index) in mostWins" :key="team.id" class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                      <span class="text-sm font-medium text-green-600">{{ index + 1 }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ team.name }}</div>
                  </div>
                </div>
                <div class="text-sm font-medium text-gray-900">{{ team.stats.won }} wins</div>
              </div>
            </li>
          </ul>
        </div>

        <!-- Best Defense -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $t('auto.key509') }}</h3>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="(team, index) in bestDefense" :key="team.id" class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                      <span class="text-sm font-medium text-blue-600">{{ index + 1 }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ team.name }}</div>
                  </div>
                </div>
                <div class="text-sm font-medium text-gray-900">{{ team.stats.goals_conceded }} conceded</div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompetitionStandings',
  props: {
    competitionId: {
      type: [Number, String],
      default: 1
    }
  },
  data() {
    return {
      competition: null,
      standings: [],
      topScorers: [],
      mostWins: [],
      bestDefense: []
    }
  },
  async mounted() {
    await this.loadCompetition()
    await this.loadStandings()
    await this.loadTopScorers()
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
        }
      } catch (error) {
        console.error('Error loading competition:', error)
      }
    },

    async loadStandings() {
      try {
        const competitionId = this.competitionId || this.$route.params.id || 1
        const response = await fetch(`/api/league-championship/competitions/${competitionId}/standings`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.standings = data.data || []
          this.calculateTeamStats()
        } else {
          console.error('Failed to load standings:', response.status, response.statusText)
        }
      } catch (error) {
        console.error('Error loading standings:', error)
      }
    },

    async loadTopScorers() {
      try {
        const competitionId = this.competitionId || this.$route.params.id || 1
        const response = await fetch(`/api/league-championship/competitions/${competitionId}/player-stats`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.topScorers = data.data || []
        } else {
          console.error('Failed to load top scorers:', response.status, response.statusText)
        }
      } catch (error) {
        console.error('Error loading top scorers:', error)
      }
    },

    calculateTeamStats() {
      // Calculate most wins
      this.mostWins = [...this.standings]
        .sort((a, b) => b.stats.won - a.stats.won)
        .slice(0, 5)

      // Calculate best defense (least goals conceded)
      this.bestDefense = [...this.standings]
        .sort((a, b) => a.stats.goals_conceded - b.stats.goals_conceded)
        .slice(0, 5)
    },

    getToken() {
      return window.Laravel?.csrfToken
    },

    getRowClass(index) {
      if (index === 0) return 'bg-yellow-50' // Champion
      if (index === 1) return 'bg-gray-50'   // Runner-up
      if (index === 2) return 'bg-orange-50' // Third place
      return ''
    },

    getGoalDifferenceClass(gd) {
      if (gd > 0) return 'text-green-600'
      if (gd < 0) return 'text-red-600'
      return 'text-gray-600'
    },

    getFormClass(result) {
      const classes = {
        'W': 'bg-green-100 text-green-800',
        'D': 'bg-yellow-100 text-yellow-800',
        'L': 'bg-red-100 text-red-800'
      }
      return classes[result] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script> 