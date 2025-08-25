<template>
  <div class="player-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else class="space-y-6">
      <!-- Quick Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-600 text-lg">{{ $t('auto.key71') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('auto.key72') }}</p>
              <p class="text-2xl font-semibold text-blue-600">{{ player.ghs_overall_score || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="text-green-600 text-lg">{{ $t('auto.key73') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('auto.key74') }}</p>
              <p class="text-2xl font-semibold text-green-600">{{ player.injury_risk_level || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="text-purple-600 text-lg">{{ $t('auto.key75') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('auto.key76') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ player.contribution_score || '0' }}%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <span class="text-yellow-600 text-lg">{{ $t('auto.key77') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('auto.key78') }}</p>
              <p class="text-lg font-semibold text-gray-900">{{ $t('auto.key79') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Tabs -->
      <div class="bg-white rounded-lg shadow-md">
        <div class="border-b border-gray-200">
          <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
              ]"
            >
              {{ tab.name }}
            </button>
          </nav>
        </div>

        <div class="p-6">
          <!-- Profile Tab -->
          <div v-if="activeTab === 'profile'" class="space-y-6">
            <!-- Profile Picture Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key80') }}</h3>
              <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                  <img v-if="player.player_picture_url" 
                       :src="player.player_picture_url" 
                       :alt="player.first_name + ' ' + player.last_name + ' Profile Picture'"
                       class="w-24 h-24 rounded-full object-cover border-4 border-blue-100">
                  <div v-else class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center border-4 border-blue-100">
                    <span class="text-blue-600 font-bold text-3xl">
                      {{ (player.first_name?.charAt(0) || '') + (player.last_name?.charAt(0) || '') }}
                    </span>
                  </div>
                </div>
                <div>
                  <h4 class="text-xl font-bold text-gray-900">{{ player.first_name }} {{ player.last_name }}</h4>
                  <p class="text-gray-600">{{ player.position }} • {{ player.club?.name || 'N/A' }}</p>
                  <p class="text-sm text-gray-500 mt-1">FIFA Connect ID: {{ player.fifa_connect_id }}</p>
                </div>
              </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key81') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key82') }}</span>
                    <span class="font-medium">{{ player.first_name }} {{ player.last_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key83') }}</span>
                    <span class="font-mono text-blue-600">{{ player.fifa_connect_id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key84') }}</span>
                    <span class="font-medium">{{ player.position }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key85') }}</span>
                    <span class="font-medium">{{ player.club?.name || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key86') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key87') }}</span>
                    <span class="px-2 py-1 rounded text-sm font-medium bg-green-100 text-green-800">
                      Active
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key88') }}</span>
                    <span class="font-medium">2026-06-30</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Performance Tab -->
          <div v-if="activeTab === 'performance'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key89') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key90') }}</span>
                    <span class="font-medium">{{ player.matches_contributed || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key91') }}</span>
                    <span class="font-medium">{{ player.training_sessions_logged || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key92') }}</span>
                    <span class="font-medium">{{ player.health_records_contributed || 0 }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key93') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key94') }}</span>
                    <span class="font-medium">{{ player.ghs_physical_score || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key95') }}</span>
                    <span class="font-medium">{{ player.ghs_mental_score || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key96') }}</span>
                    <span class="font-medium">{{ player.ghs_civic_score || 0 }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key97') }}</h3>
                <div class="space-y-2">
                  <div class="text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">{{ $t('auto.key98') }}</span>
                      <span class="font-medium">{{ player.ghs_sleep_score || 0 }}</span>
                    </div>
                  </div>
                  <div class="text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">{{ $t('auto.key99') }}</span>
                      <span class="font-medium">{{ (player.injury_risk_score * 100).toFixed(1) || 0 }}%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- GHS Tab -->
          <div v-if="activeTab === 'ghs'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key100') }}</h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $t('auto.key101') }}</span>
                    <div class="flex items-center space-x-2">
                      <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" :style="{ width: (player.ghs_physical_score || 0) + '%' }"></div>
                      </div>
                      <span class="font-medium w-8">{{ player.ghs_physical_score || 0 }}</span>
                    </div>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $t('auto.key102') }}</span>
                    <div class="flex items-center space-x-2">
                      <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" :style="{ width: (player.ghs_mental_score || 0) + '%' }"></div>
                      </div>
                      <span class="font-medium w-8">{{ player.ghs_mental_score || 0 }}</span>
                    </div>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $t('auto.key103') }}</span>
                    <div class="flex items-center space-x-2">
                      <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" :style="{ width: (player.ghs_civic_score || 0) + '%' }"></div>
                      </div>
                      <span class="font-medium w-8">{{ player.ghs_civic_score || 0 }}</span>
                    </div>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ $t('auto.key104') }}</span>
                    <div class="flex items-center space-x-2">
                      <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" :style="{ width: (player.ghs_sleep_score || 0) + '%' }"></div>
                      </div>
                      <span class="font-medium w-8">{{ player.ghs_sleep_score || 0 }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key105') }}</h3>
                <div v-if="player.ghs_ai_suggestions && player.ghs_ai_suggestions.length" class="space-y-3">
                  <div v-for="(suggestion, index) in player.ghs_ai_suggestions" :key="index" class="bg-white p-3 rounded border-l-4 border-blue-500">
                    <p class="text-sm text-gray-700">{{ suggestion }}</p>
                  </div>
                </div>
                <div v-else class="text-gray-500 text-sm">{{ $t('auto.key106') }}</div>
              </div>
            </div>
          </div>

          <!-- Data Ownership Tab -->
          <div v-if="activeTab === 'data-ownership'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key107') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key108') }}</span>
                    <span class="font-medium">{{ player.contribution_score || 0 }}%</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key109') }}</span>
                    <span class="font-medium">${{ player.data_value_estimate || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key110') }}</span>
                    <span class="font-medium">{{ formatDate(player.last_data_export) }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('auto.key111') }}</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key112') }}</span>
                    <span class="font-medium">{{ player.matches_contributed || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key113') }}</span>
                    <span class="font-medium">{{ player.training_sessions_logged || 0 }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">{{ $t('auto.key114') }}</span>
                    <span class="font-medium">{{ player.health_records_contributed || 0 }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlayerDashboard',
  props: {
    player: {
      type: Object,
      required: true
    },
    apiEndpoints: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      loading: false,
      activeTab: 'profile',
      tabs: [
        { id: 'profile', name: '👤 Profile' },
        { id: 'performance', name: '📊 Performance' },
        { id: 'ghs', name: '🏥 GHS' },
        { id: 'data-ownership', name: '📈 Data Ownership' }
      ]
    }
  },
  mounted() {
    console.log('PlayerDashboard component mounted');
  },
  methods: {
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString();
    }
  }
}
</script>

<style scoped>
.player-dashboard {
  min-height: 100vh;
}
</style> 