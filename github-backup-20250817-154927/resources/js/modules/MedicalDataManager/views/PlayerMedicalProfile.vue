<template>
  <div class="player-medical-profile">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center">
        <button 
          @click="$router.go(-1)"
          class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors"
        >
          ← Retour
        </button>
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            <span class="text-red-600">👤</span> {{ athlete?.name }}
          </h1>
          <p class="text-gray-600 mt-1">{{ athlete?.team?.name }} • {{ athlete?.position }}</p>
        </div>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="editMode = !editMode"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
        >
          {{ editMode ? 'Annuler' : 'Modifier' }}
        </button>
        <button 
          v-if="editMode"
          @click="saveAthlete"
          class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
        >
          Sauvegarder
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
    </div>

    <div v-else-if="athlete" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - Basic Info -->
      <div class="lg:col-span-1">
        <!-- Athlete Card -->
        <div class="bg-white rounded-lg shadow mb-6">
          <div class="p-6">
            <div class="flex items-center mb-4">
              <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                <span class="text-2xl font-medium">{{ getInitials(athlete.name) }}</span>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900">{{ athlete.name }}</h3>
                <p class="text-gray-600">{{ athlete.team?.name }}</p>
              </div>
            </div>
            
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">FIFA ID:</span>
                <span class="font-medium">{{ athlete.fifa_id }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Âge:</span>
                <span class="font-medium">{{ athlete.age }} ans</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Nationalité:</span>
                <span class="font-medium">{{ athlete.nationality }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Position:</span>
                <span class="font-medium">{{ athlete.position }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Numéro:</span>
                <span class="font-medium">{{ athlete.jersey_number || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Groupe sanguin:</span>
                <span class="font-medium">{{ athlete.blood_type || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Health Score -->
        <div class="bg-white rounded-lg shadow mb-6">
          <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Score de Santé</h4>
            <div class="text-center">
              <div class="relative w-24 h-24 mx-auto mb-4">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                  <path
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    stroke="#e5e7eb"
                    stroke-width="3"
                  />
                  <path
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none"
                    :stroke="getHealthScoreColor(athlete.health_score)"
                    stroke-width="3"
                    :stroke-dasharray="`${athlete.health_score}, 100`"
                  />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-2xl font-bold" :class="getHealthScoreTextColor(athlete.health_score)">
                    {{ athlete.health_score }}%
                  </span>
                </div>
              </div>
              <p class="text-sm text-gray-600">{{ getHealthScoreDescription(athlete.health_score) }}</p>
            </div>
          </div>
        </div>

        <!-- FIFA Compliance -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Conformité FIFA</h4>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-gray-600">PCMA Compliant:</span>
                <span class="flex items-center">
                  <div class="w-3 h-3 rounded-full mr-2" :class="athlete.fifa_compliance?.pcma_compliant ? 'bg-green-500' : 'bg-red-500'"></div>
                  {{ athlete.fifa_compliance?.pcma_compliant ? 'Oui' : 'Non' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600">TUE Compliant:</span>
                <span class="flex items-center">
                  <div class="w-3 h-3 rounded-full mr-2" :class="athlete.fifa_compliance?.tue_compliant ? 'bg-green-500' : 'bg-red-500'"></div>
                  {{ athlete.fifa_compliance?.tue_compliant ? 'Oui' : 'Non' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600">Fully Compliant:</span>
                <span class="flex items-center">
                  <div class="w-3 h-3 rounded-full mr-2" :class="athlete.fifa_compliance?.fully_compliant ? 'bg-green-500' : 'bg-red-500'"></div>
                  {{ athlete.fifa_compliance?.fully_compliant ? 'Oui' : 'Non' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Medical Data -->
      <div class="lg:col-span-2">
        <!-- Medical Status -->
        <div class="bg-white rounded-lg shadow mb-6">
          <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Statut Médical</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="text-center p-4 border rounded-lg" :class="athlete.medical_status?.has_active_injuries ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'">
                <div class="text-2xl mb-2">{{ athlete.medical_status?.has_active_injuries ? '🩹' : '✅' }}</div>
                <p class="font-medium">{{ athlete.medical_status?.has_active_injuries ? 'Blessures Actives' : 'Aucune Blessure' }}</p>
              </div>
              <div class="text-center p-4 border rounded-lg" :class="athlete.medical_status?.has_pending_pcma ? 'border-yellow-200 bg-yellow-50' : 'border-green-200 bg-green-50'">
                <div class="text-2xl mb-2">{{ athlete.medical_status?.has_pending_pcma ? '⚠️' : '✅' }}</div>
                <p class="font-medium">{{ athlete.medical_status?.has_pending_pcma ? 'PCMA En Attente' : 'PCMA Compliant' }}</p>
              </div>
              <div class="text-center p-4 border rounded-lg" :class="athlete.medical_status?.has_unresolved_alerts ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'">
                <div class="text-2xl mb-2">{{ athlete.medical_status?.has_unresolved_alerts ? '🚨' : '✅' }}</div>
                <p class="font-medium">{{ athlete.medical_status?.has_unresolved_alerts ? 'Alertes Non Résolues' : 'Aucune Alerte' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Medical History Tabs -->
        <div class="bg-white rounded-lg shadow">
          <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
              <button 
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                class="py-4 px-1 border-b-2 font-medium text-sm"
                :class="activeTab === tab.id ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
          
          <div class="p-6">
            <!-- Injuries Tab -->
            <div v-if="activeTab === 'injuries'">
              <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-medium text-gray-900">Historique des Blessures</h5>
                <button 
                  @click="showInjuryModal = true"
                  class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouvelle Blessure
                </button>
              </div>
              <div v-if="injuries.length === 0" class="text-center py-8">
                <p class="text-gray-500">Aucune blessure enregistrée</p>
              </div>
              <div v-else class="space-y-4">
                <div 
                  v-for="injury in injuries" 
                  :key="injury.id"
                  class="border rounded-lg p-4"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h6 class="font-medium text-gray-900">{{ injury.type }}</h6>
                      <p class="text-sm text-gray-600">{{ injury.body_zone }} • {{ injury.severity }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(injury.date) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" :class="getInjuryStatusClass(injury.status)">
                      {{ injury.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- PCMA Tab -->
            <div v-if="activeTab === 'pcma'">
              <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-medium text-gray-900">Évaluations PCMA</h5>
                <button 
                  @click="showPCMAModal = true"
                  class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition-colors text-sm"
                >
                  + Nouvelle Évaluation
                </button>
              </div>
              <div v-if="pcmas.length === 0" class="text-center py-8">
                <p class="text-gray-500">Aucune évaluation PCMA enregistrée</p>
              </div>
              <div v-else class="space-y-4">
                <div 
                  v-for="pcma in pcmas" 
                  :key="pcma.id"
                  class="border rounded-lg p-4"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h6 class="font-medium text-gray-900">{{ pcma.type.toUpperCase() }}</h6>
                      <p class="text-sm text-gray-600">{{ pcma.assessor?.name }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(pcma.completed_at) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" :class="getPCMAStatusClass(pcma.status)">
                      {{ pcma.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Alerts Tab -->
            <div v-if="activeTab === 'alerts'">
              <h5 class="text-lg font-medium text-gray-900 mb-4">Alertes Médicales</h5>
              <div v-if="alerts.length === 0" class="text-center py-8">
                <p class="text-gray-500">Aucune alerte médicale</p>
              </div>
              <div v-else class="space-y-4">
                <div 
                  v-for="alert in alerts" 
                  :key="alert.id"
                  class="border rounded-lg p-4"
                  :class="getAlertBorderClass(alert.priority)"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h6 class="font-medium text-gray-900">{{ alert.type.toUpperCase() }}</h6>
                      <p class="text-sm text-gray-600">{{ alert.message }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(alert.created_at) }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(alert.priority)">
                      {{ alert.priority }}
                    </span>
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
  name: 'PlayerMedicalProfile',
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      athlete: null,
      injuries: [],
      pcmas: [],
      alerts: [],
      loading: true,
      editMode: false,
      activeTab: 'injuries',
      showInjuryModal: false,
      showPCMAModal: false,
      tabs: [
        { id: 'injuries', name: 'Blessures' },
        { id: 'pcma', name: 'PCMA' },
        { id: 'alerts', name: 'Alertes' }
      ]
    }
  },
  async mounted() {
    await this.loadAthleteData()
  },
  methods: {
    async loadAthleteData() {
      this.loading = true
      try {
        const response = await this.$http.get(`/api/v1/athletes/${this.id}/medical-dashboard`)
        this.athlete = response.data.data.athlete
        this.injuries = response.data.data.recent_activities.injuries
        this.pcmas = response.data.data.recent_activities.pcmas
        this.alerts = response.data.data.recent_activities.alerts
      } catch (error) {
        console.error('Error loading athlete data:', error)
        this.$toast.error('Erreur lors du chargement des données')
      } finally {
        this.loading = false
      }
    },

    getInitials(name) {
      return name.split(' ').map(n => n[0]).join('').toUpperCase()
    },

    getHealthScoreColor(score) {
      if (score >= 80) return '#10b981'
      if (score >= 60) return '#f59e0b'
      return '#ef4444'
    },

    getHealthScoreTextColor(score) {
      if (score >= 80) return 'text-green-600'
      if (score >= 60) return 'text-yellow-600'
      return 'text-red-600'
    },

    getHealthScoreDescription(score) {
      if (score >= 80) return 'Excellent état de santé'
      if (score >= 60) return 'État de santé correct'
      return 'Attention requise'
    },

    getInjuryStatusClass(status) {
      switch (status) {
        case 'open': return 'bg-red-100 text-red-800'
        case 'resolved': return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    getPCMAStatusClass(status) {
      switch (status) {
        case 'completed': return 'bg-green-100 text-green-800'
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'failed': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    getAlertBorderClass(priority) {
      switch (priority) {
        case 'critical': return 'border-red-200 bg-red-50'
        case 'high': return 'border-orange-200 bg-orange-50'
        case 'medium': return 'border-yellow-200 bg-yellow-50'
        default: return 'border-gray-200 bg-gray-50'
      }
    },

    getPriorityClass(priority) {
      switch (priority) {
        case 'critical': return 'bg-red-100 text-red-800'
        case 'high': return 'bg-orange-100 text-orange-800'
        case 'medium': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR')
    },

    async saveAthlete() {
      try {
        await this.$http.put(`/api/v1/athletes/${this.id}`, this.athlete)
        this.editMode = false
        this.$toast.success('Athlète mis à jour avec succès')
      } catch (error) {
        console.error('Error saving athlete:', error)
        this.$toast.error('Erreur lors de la sauvegarde')
      }
    }
  }
}
</script>

<style scoped>
.player-medical-profile {
  @apply p-6;
}
</style> 