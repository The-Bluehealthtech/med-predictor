<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header with Football Type Info -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                  <span class="text-white font-bold text-lg">{{ $t('auto.key258') }}</span>
                </div>
                <div class="ml-3">
                  <h1 class="text-2xl font-bold text-gray-900">
                    {{ currentType?.displayName || 'Football Intelligence & Tracking' }}
                  </h1>
                  <p class="text-sm text-gray-600">
                    {{ currentType?.description || 'Select a football format to get started' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
              <span class="text-2xl">{{ currentType?.emoji }}</span>
              <span class="text-sm text-gray-600">{{ currentType?.players }} players</span>
            </div>
            <button 
              @click="changeFootballType"
              class="text-gray-600 hover:text-gray-900 text-sm font-medium"
            >
              Change Format
            </button>
            <a href="/admin/login" class="text-gray-600 hover:text-gray-900 text-sm font-medium">{{ $t('auto.key259') }}</a>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-blue-600 text-sm">{{ $t('auto.key260') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">{{ $t('auto.key261') }}</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.totalPlayers }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="text-green-600 text-sm">{{ $t('auto.key262') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">{{ $t('auto.key263') }}</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.activeClubs }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="text-purple-600 text-sm">{{ $t('auto.key264') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">{{ $t('auto.key265') }}</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.competitions }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <span class="text-orange-600 text-sm">{{ $t('auto.key266') }}</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">{{ $t('auto.key267') }}</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.pendingLicenses }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">{{ $t('auto.key268') }}</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button 
              @click="navigateToModule('licenses')"
              class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <span class="text-blue-600 text-lg">{{ $t('auto.key269') }}</span>
              </div>
              <div class="text-left">
                <h3 class="font-medium text-gray-900">{{ $t('auto.key270') }}</h3>
                <p class="text-sm text-gray-600">{{ $t('auto.key271') }}</p>
              </div>
            </button>
            <button 
              @click="navigateToModule('medical')"
              class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <span class="text-green-600 text-lg">{{ $t('auto.key272') }}</span>
              </div>
              <div class="text-left">
                <h3 class="font-medium text-gray-900">{{ $t('auto.key273') }}</h3>
                <p class="text-sm text-gray-600">{{ $t('auto.key274') }}</p>
              </div>
            </button>
            <button 
              @click="navigateToModule('competitions')"
              class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                <span class="text-purple-600 text-lg">{{ $t('auto.key275') }}</span>
              </div>
              <div class="text-left">
                <h3 class="font-medium text-gray-900">{{ $t('auto.key276') }}</h3>
                <p class="text-sm text-gray-600">{{ $t('auto.key277') }}</p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Test Stakeholders Data -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ $t('auto.key278') }}</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Clubs -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ $t('auto.key279') }}</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="club in topClubs" :key="club.id" class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-gray-600 text-xs font-medium">{{ club.name.charAt(0) }}</span>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ club.name }}</p>
                  <p class="text-xs text-gray-600">{{ club.players }} players</p>
                </div>
                <div class="flex-shrink-0">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ club.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Football Type Rules -->
      <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">{{ currentType?.displayName }} Rules</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ currentTypeRules?.teamSize || '-' }}</div>
              <p class="text-sm text-gray-600">{{ $t('auto.key280') }}</p>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ currentTypeRules?.substitutes || '-' }}</div>
              <p class="text-sm text-gray-600">{{ $t('auto.key281') }}</p>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ currentTypeRules?.ballSize || '-' }}</div>
              <p class="text-sm text-gray-600">{{ $t('auto.key282') }}</p>
            </div>
            <div class="text-center">
              <div class="text-sm font-bold text-gray-600">{{ currentTypeRules?.fieldDimensions || '-' }}</div>
              <p class="text-sm text-gray-600">{{ $t('auto.key283') }}</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { useFootballTypeStore } from '../stores/footballTypeStore'
import { computed, onMounted } from 'vue'

export default {
  name: 'DynamicDashboard',
  setup() {
    const footballTypeStore = useFootballTypeStore()

    const currentType = computed(() => footballTypeStore.currentType)
    const currentTypeRules = computed(() => footballTypeStore.currentTypeRules)

    // Test data that adapts based on football type
    const stats = computed(() => {
      const baseStats = {
        '11aside': { totalPlayers: 1250, activeClubs: 45, competitions: 12, pendingLicenses: 23 },
        'womens': { totalPlayers: 890, activeClubs: 32, competitions: 8, pendingLicenses: 15 },
        'futsal': { totalPlayers: 680, activeClubs: 28, competitions: 6, pendingLicenses: 12 },
        'beach': { totalPlayers: 420, activeClubs: 18, competitions: 4, pendingLicenses: 8 }
      }
      return baseStats[footballTypeStore.currentTypeId] || baseStats['11aside']
    })

    const recentActivities = computed(() => {
      const activities = {
        '11aside': [
          { id: 1, description: 'New player registration: John Smith (Manchester United)', time: '2 hours ago' },
          { id: 2, description: 'License renewal approved for 15 players', time: '4 hours ago' },
          { id: 3, description: 'Medical clearance completed for 8 players', time: '6 hours ago' },
          { id: 4, description: 'New competition "Premier League 2024" created', time: '1 day ago' }
        ],
        'womens': [
          { id: 1, description: 'New player registration: Sarah Johnson (Arsenal Women)', time: '1 hour ago' },
          { id: 2, description: 'License renewal approved for 12 players', time: '3 hours ago' },
          { id: 3, description: 'Medical clearance completed for 6 players', time: '5 hours ago' },
          { id: 4, description: 'New competition "Women\'s Super League" created', time: '1 day ago' }
        ],
        'futsal': [
          { id: 1, description: 'New player registration: Carlos Silva (Futsal Club)', time: '30 minutes ago' },
          { id: 2, description: 'License renewal approved for 8 players', time: '2 hours ago' },
          { id: 3, description: 'Medical clearance completed for 4 players', time: '4 hours ago' },
          { id: 4, description: 'New competition "Futsal Championship" created', time: '1 day ago' }
        ],
        'beach': [
          { id: 1, description: 'New player registration: Mike Wilson (Beach FC)', time: '45 minutes ago' },
          { id: 2, description: 'License renewal approved for 6 players', time: '1.5 hours ago' },
          { id: 3, description: 'Medical clearance completed for 3 players', time: '3 hours ago' },
          { id: 4, description: 'New competition "Beach Soccer Cup" created', time: '1 day ago' }
        ]
      }
      return activities[footballTypeStore.currentTypeId] || activities['11aside']
    })

    const topClubs = computed(() => {
      const clubs = {
        '11aside': [
          { id: 1, name: 'Manchester United', players: 25, status: 'Active' },
          { id: 2, name: 'Liverpool FC', players: 23, status: 'Active' },
          { id: 3, name: 'Arsenal FC', players: 22, status: 'Active' },
          { id: 4, name: 'Chelsea FC', players: 24, status: 'Active' }
        ],
        'womens': [
          { id: 1, name: 'Arsenal Women', players: 20, status: 'Active' },
          { id: 2, name: 'Chelsea Women', players: 19, status: 'Active' },
          { id: 3, name: 'Manchester City Women', players: 21, status: 'Active' },
          { id: 4, name: 'Liverpool Women', players: 18, status: 'Active' }
        ],
        'futsal': [
          { id: 1, name: 'Futsal United', players: 12, status: 'Active' },
          { id: 2, name: 'Indoor FC', players: 10, status: 'Active' },
          { id: 3, name: 'Court Masters', players: 11, status: 'Active' },
          { id: 4, name: 'Futsal Elite', players: 9, status: 'Active' }
        ],
        'beach': [
          { id: 1, name: 'Beach United', players: 8, status: 'Active' },
          { id: 2, name: 'Sand Warriors', players: 7, status: 'Active' },
          { id: 3, name: 'Coastal FC', players: 9, status: 'Active' },
          { id: 4, name: 'Beach Elite', players: 6, status: 'Active' }
        ]
      }
      return clubs[footballTypeStore.currentTypeId] || clubs['11aside']
    })

    const changeFootballType = () => {
      window.location.href = '/'
    }

    const navigateToModule = (module) => {
      const url = footballTypeStore.getModuleUrl(module)
      window.location.href = url
    }

    onMounted(() => {
      // Initialize football type from URL if not set
      if (!footballTypeStore.hasSelectedType) {
        footballTypeStore.initializeFromUrl()
      }
    })

    return {
      currentType,
      currentTypeRules,
      stats,
      recentActivities,
      topClubs,
      changeFootballType,
      navigateToModule
    }
  }
}
</script>

<style scoped>
/* Additional custom styles if needed */
</style> 