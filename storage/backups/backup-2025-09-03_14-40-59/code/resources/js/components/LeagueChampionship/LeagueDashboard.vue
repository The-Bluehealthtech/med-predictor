<template>
  <div class="league-dashboard">
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">{{ $t('auto.key479') }}</p>
      </div>
    </div>

    <div v-else-if="error" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="text-red-600 text-6xl mb-4">{{ $t('auto.key480') }}</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t('auto.key481') }}</h2>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button @click="loadCompetition" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
          {{ $t('auto.key1') }}
        </button>
      </div>
    </div>

    <div v-else class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">{{ competition.name }} {{ competition.season }}</h1>
              <p class="text-gray-600">{{ competition.description }}</p>
            </div>
            <div class="flex space-x-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ competition.teams?.length || 0 }}</div>
                <div class="text-sm text-gray-500">{{ $t('auto.key482') }}</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ competition.game_matches?.length || 0 }}</div>
                <div class="text-sm text-gray-500">{{ $t('auto.key483') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav class="flex space-x-8">
            <button
              {{ $t('auto.key2') }}
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                {{ $t('auto.key3') }}
                  {{ $t('auto.key4') }}
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                {{ $t('auto.key5') }}
              ]"
            >
              {{ $t('auto.key6') }}
            </button>
          </nav>
        </div>
      </div>

      <!-- Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Overview Tab -->
        <div v-if="activeTab === 'overview'" class="space-y-8">
          <!-- Competition Details -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('auto.key484') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p><strong>{{ $t('auto.key485') }}</strong> {{ competition.name }}</p>
                <p><strong>{{ $t('auto.key486') }}</strong> {{ competition.short_name }}</p>
                <p><strong>{{ $t('auto.key487') }}</strong> {{ competition.type }}</p>
              </div>
              <div>
                <p><strong>{{ $t('auto.key488') }}</strong> {{ competition.season }}</p>
                <p><strong>{{ $t('auto.key489') }}</strong> {{ competition.status }}</p>
                <p><strong>{{ $t('auto.key490') }}</strong> {{ competition.description }}</p>
              </div>
            </div>
          </div>

          <!-- Teams -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Teams ({{ competition.teams?.length || 0 }})</h2>
            <div v-if="competition.teams && competition.teams.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="team in competition.teams" :key="team.id" class="border rounded-lg p-4">
                <h3 class="font-semibold">{{ team.club?.name || team.name }}</h3>
                <p class="text-sm text-gray-600">{{ team.name }}</p>
                <p class="text-xs text-gray-500">Formation: {{ team.formation }}</p>
              </div>
            </div>
            <div v-else class="text-gray-500 text-center py-8">
              {{ $t('auto.key7') }}
            </div>
          </div>

          <!-- Recent Matches -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Matches ({{ competition.game_matches?.length || 0 }})</h2>
            <div v-if="competition.game_matches && competition.game_matches.length > 0" class="space-y-4">
              <div v-for="match in competition.game_matches.slice(0, 5)" :key="match.id" class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ match.home_team?.club?.name || match.home_team?.name }}
                      <span v-if="match.home_team?.club?.name" class="text-xs text-gray-500">({{ match.home_team?.name }})</span>
                    </div>
                    <div class="text-sm text-gray-500">{{ $t('auto.key491') }}</div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ match.away_team?.club?.name || match.away_team?.name }}
                      <span v-if="match.away_team?.club?.name" class="text-xs text-gray-500">({{ match.away_team?.name }})</span>
                    </div>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ new Date(match.match_date).toLocaleDateString() }}
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-gray-500 text-center py-8">
              No matches found for this competition.
            </div>
          </div>
        </div>

        <!-- Schedule Tab -->
        <div v-else-if="activeTab === 'schedule'">
          <CompetitionSchedule :competition-id="competition.id" />
        </div>

        <!-- Standings Tab -->
        <div v-else-if="activeTab === 'standings'">
          <CompetitionStandings :competition-id="competition.id" />
        </div>

        <!-- Roster Submission Tab -->
        <div v-else-if="activeTab === 'rosters'">
          <RosterSubmission :competition-id="competition.id" />
        </div>

        <!-- Manual Scheduler Tab -->
        <div v-else-if="activeTab === 'scheduler'">
          <ManualScheduler :competition-id="competition.id" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import CompetitionSchedule from './CompetitionSchedule.vue'
import ManualScheduler from './ManualScheduler.vue'
import CompetitionStandings from './CompetitionStandings.vue'
import MatchSheet from './MatchSheet.vue'
import RosterSubmission from './RosterSubmission.vue'
import axios from 'axios'

export default {
  name: 'LeagueDashboard',
  components: {
    CompetitionSchedule,
    ManualScheduler,
    CompetitionStandings,
    MatchSheet,
    RosterSubmission
  },
  props: {
    competitionId: {
      type: [String, Number],
      default: 1
    }
  },
  setup(props) {
    const competition = ref({
      id: props.competitionId || 1,
      name: 'Premier League',
      season: '2024/25',
      description: 'English Premier League Championship'
    })
    const activeTab = ref('schedule')
    const loading = ref(true)
    const error = ref(null)

    const tabs = [
      { id: 'overview', name: 'Overview' },
      { id: 'schedule', name: 'Schedule' },
      { id: 'standings', name: 'Standings' },
      { id: 'rosters', name: 'Rosters' },
      { id: 'scheduler', name: 'Scheduler' }
    ]

    const loadCompetition = async () => {
      try {
        loading.value = true
        error.value = null
        
        // Configure axios defaults
        axios.defaults.headers.common['Accept'] = 'application/json'
        axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel?.csrfToken
        
        const apiUrl = `/api/league-championship/competitions/${props.competitionId || 1}`
        
        const response = await axios.get(apiUrl)
        
        if (response.data.success) {
          competition.value = response.data.data
        } else {
          error.value = 'Failed to load competition data'
        }
        
      } catch (err) {
        error.value = `Failed to load competition data: ${err.message}`
        // Use fallback data if API fails
        competition.value = {
          id: props.competitionId || 1,
          name: 'Premier League',
          season: '2024/25',
          description: 'English Premier League Championship'
        }
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadCompetition()
    })

    return {
      competition,
      activeTab,
      tabs,
      loading,
      error,
      loadCompetition
    }
  }
}
</script>

<style scoped>
.league-dashboard {
  @apply p-6;
}

.dashboard-content {
  @apply min-h-screen;
}

.dashboard-header {
  @apply mb-6;
}

.tabs {
  @apply flex border-b border-gray-200 mb-6;
}

.tab-button {
  @apply px-4 py-2 text-sm font-medium border-b-2 border-transparent hover:text-blue-600 hover:border-blue-300;
}

.tab-button.active {
  @apply text-blue-600 border-blue-600;
}

.tab-content {
  @apply min-h-[500px];
}

.tab-panel {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { 
    opacity: 0; 
    transform: translateY(10px);
  }
  to { 
    opacity: 1; 
    transform: translateY(0);
  }
}
</style> 