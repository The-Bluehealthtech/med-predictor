<template>
  <div class="medical-dashboard">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord Médical</h1>
          <p class="text-sm text-gray-600">Vue d'ensemble de la santé des athlètes</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-right">
            <p class="text-sm text-gray-500">Dernière mise à jour</p>
            <p class="text-sm font-medium text-gray-900">{{ lastUpdated }}</p>
          </div>
          <button 
            @click="refreshData"
            :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50"
          >
            {{ loading ? 'Actualisation...' : 'Actualiser' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <span class="text-red-600 text-lg">🩹</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Blessures Actives</p>
              <p class="text-2xl font-bold text-gray-900">{{ dashboardData.statistics?.active_injuries || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="text-orange-600 text-lg">📋</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">PCMA en Attente</p>
              <p class="text-2xl font-bold text-gray-900">{{ dashboardData.statistics?.pending_pcmas || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                <span class="text-yellow-600 text-lg">⚠️</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Alertes Non Résolues</p>
              <p class="text-2xl font-bold text-gray-900">{{ dashboardData.statistics?.unresolved_alerts || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-green-600 text-lg">👥</span>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Athlètes Actifs</p>
              <p class="text-2xl font-bold text-gray-900">{{ dashboardData.statistics?.total_athletes || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Health Statistics -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques de Santé</h3>
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Taux de Blessures</span>
              <span class="text-sm font-medium text-gray-900">{{ dashboardData.health_statistics?.injury_rate || 0 }}%</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Taux de Commotions</span>
              <span class="text-sm font-medium text-gray-900">{{ dashboardData.health_statistics?.concussion_rate || 0 }}%</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Score de Santé Moyen</span>
              <span class="text-sm font-medium text-gray-900">{{ dashboardData.health_statistics?.average_health_score || 0 }}/100</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Taux de Conformité</span>
              <span class="text-sm font-medium text-gray-900">{{ dashboardData.health_statistics?.compliance_rate || 0 }}%</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
          <div class="grid grid-cols-2 gap-3">
            <button 
              @click="navigateTo('/medical/athletes/new')"
              class="p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors text-left"
            >
              <div class="text-blue-600 text-lg mb-1">👤</div>
              <div class="text-sm font-medium text-gray-900">Nouvel Athlète</div>
            </button>
            <button 
              @click="navigateTo('/medical/pcma/new')"
              class="p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors text-left"
            >
              <div class="text-red-600 text-lg mb-1">📋</div>
              <div class="text-sm font-medium text-gray-900">Nouveau PCMA</div>
            </button>
            <button 
              @click="navigateTo('/medical/injuries/new')"
              class="p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors text-left"
            >
              <div class="text-orange-600 text-lg mb-1">🩹</div>
              <div class="text-sm font-medium text-gray-900">Nouvelle Blessure</div>
            </button>
            <button 
              @click="navigateTo('/medical/notes/new')"
              class="p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors text-left"
            >
              <div class="text-green-600 text-lg mb-1">📝</div>
              <div class="text-sm font-medium text-gray-900">Nouvelle Note</div>
            </button>
          </div>
        </div>
      </div>

      <!-- Alerts Section -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Alertes de Risque</h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">{{ dashboardData.recent_activities?.alerts?.length || 0 }} alertes</span>
              <button 
                @click="navigateTo('/medical/alerts')"
                class="text-sm text-red-600 hover:text-red-700 font-medium"
              >
                Voir toutes
              </button>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div v-if="dashboardData.recent_activities?.alerts?.length === 0" class="text-center py-8">
            <div class="text-gray-400 text-lg mb-2">🎉</div>
            <p class="text-gray-500">Aucune alerte active</p>
          </div>
          
          <div v-else class="space-y-4">
            <RiskAlert 
              v-for="alert in dashboardData.recent_activities?.alerts"
              :key="alert.id"
              :alert="alert"
              @acknowledged="handleAlertAcknowledged"
            />
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Injuries -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Blessures Récentes</h3>
          </div>
          <div class="p-6">
            <div v-if="dashboardData.recent_activities?.injuries?.length === 0" class="text-center py-4">
              <p class="text-gray-500">Aucune blessure récente</p>
            </div>
            <div v-else class="space-y-3">
              <div 
                v-for="injury in dashboardData.recent_activities?.injuries"
                :key="injury.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <p class="font-medium text-gray-900">{{ injury.athlete?.name }}</p>
                  <p class="text-sm text-gray-600">{{ injury.type }} - {{ injury.body_zone }}</p>
                </div>
                <div class="text-right">
                  <span :class="getSeverityClass(injury.severity)" class="text-xs px-2 py-1 rounded-full">
                    {{ injury.severity_display }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">{{ formatDate(injury.date) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent PCMAs -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">PCMA Récents</h3>
          </div>
          <div class="p-6">
            <div v-if="dashboardData.recent_activities?.pcmas?.length === 0" class="text-center py-4">
              <p class="text-gray-500">Aucun PCMA récent</p>
            </div>
            <div v-else class="space-y-3">
              <div 
                v-for="pcma in dashboardData.recent_activities?.pcmas"
                :key="pcma.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <p class="font-medium text-gray-900">{{ pcma.athlete?.name }}</p>
                  <p class="text-sm text-gray-600">{{ pcma.type_display }}</p>
                </div>
                <div class="text-right">
                  <span :class="getPCMAStatusClass(pcma.status)" class="text-xs px-2 py-1 rounded-full">
                    {{ pcma.status_display }}
                  </span>
                  <p class="text-xs text-gray-500 mt-1">{{ formatDate(pcma.created_at) }}</p>
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
import RiskAlert from '../components/RiskAlert.vue'

export default {
  name: 'MedicalDashboard',
  components: {
    RiskAlert
  },
  data() {
    return {
      loading: false,
      dashboardData: {
        statistics: {},
        recent_activities: {
          alerts: [],
          injuries: [],
          pcmas: []
        },
        health_statistics: {},
        team_statistics: [],
        compliance_data: {}
      }
    }
  },
  computed: {
    lastUpdated() {
      return new Date().toLocaleString('fr-FR')
    }
  },
  mounted() {
    this.loadDashboardData()
  },
  methods: {
    async loadDashboardData() {
      this.loading = true
      try {
        const response = await this.$http.get('/api/v1/dashboard')
        this.dashboardData = response.data.data
      } catch (error) {
        console.error('Error loading dashboard data:', error)
        this.$toast.error('Erreur lors du chargement des données')
      } finally {
        this.loading = false
      }
    },

    async refreshData() {
      await this.loadDashboardData()
      this.$toast.success('Données actualisées')
    },

    navigateTo(route) {
      this.$router.push(route)
    },

    handleAlertAcknowledged(alertId) {
      // Remove the acknowledged alert from the list
      this.dashboardData.recent_activities.alerts = this.dashboardData.recent_activities.alerts.filter(
        alert => alert.id !== alertId
      )
      this.$toast.success('Alerte reconnue')
    },

    formatDate(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('fr-FR')
    },

    getSeverityClass(severity) {
      const classes = {
        'minor': 'bg-green-100 text-green-800',
        'moderate': 'bg-yellow-100 text-yellow-800',
        'severe': 'bg-red-100 text-red-800'
      }
      return classes[severity] || 'bg-gray-100 text-gray-800'
    },

    getPCMAStatusClass(status) {
      const classes = {
        'completed': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'failed': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script>

<style scoped>
.medical-dashboard {
  @apply min-h-screen bg-gray-50;
}
</style> 