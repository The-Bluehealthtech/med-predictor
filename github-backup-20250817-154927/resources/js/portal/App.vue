<template>
  <div id="athlete-portal" class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">
              <i class="fas fa-user-circle text-blue-600 mr-2"></i>
              Portail Athlète
            </h1>
          </div>
          
          <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-700">
              <i class="fas fa-user mr-1"></i>
              {{ athlete.name }}
            </div>
            <button @click="logout" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-sign-out-alt"></i>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Navigation par onglets -->
    <nav class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-8">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <i :class="tab.icon + ' mr-2'"></i>
            {{ tab.label }}
          </button>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Dashboard -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        <DashboardView 
          :summary="dashboardSummary" 
          :loading="loading.dashboard"
          @refresh="loadDashboardSummary"
        />
      </div>

      <!-- Dossier Médical -->
      <div v-if="activeTab === 'medical'" class="space-y-6">
        <MedicalRecordView 
          :records="medicalRecords" 
          :loading="loading.medical"
          @refresh="loadMedicalRecords"
        />
      </div>

      <!-- Formulaire Bien-être -->
      <div v-if="activeTab === 'wellness'" class="space-y-6">
        <WellnessFormView 
          :history="wellnessHistory"
          :loading="loading.wellness"
          @submit="submitWellnessForm"
          @refresh="loadWellnessHistory"
        />
      </div>

      <!-- Rendez-vous -->
      <div v-if="activeTab === 'appointments'" class="space-y-6">
        <AppointmentsView 
          :appointments="appointments"
          :loading="loading.appointments"
          @refresh="loadAppointments"
        />
      </div>

      <!-- Documents -->
      <div v-if="activeTab === 'documents'" class="space-y-6">
        <DocumentsView 
          :documents="documents"
          :loading="loading.documents"
          @refresh="loadDocuments"
        />
      </div>

      <!-- Appareils Connectés -->
      <div v-if="activeTab === 'devices'" class="space-y-6">
        <ConnectedDevicesView 
          :devices="connectedDevices"
          :loading="loading.devices"
          @connect="connectDevice"
          @disconnect="disconnectDevice"
        />
      </div>
    </main>

    <!-- Notifications -->
    <div v-if="notification.show" 
         :class="[
           'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm',
           notification.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
         ]"
    >
      <div class="flex items-center">
        <i :class="notification.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'" class="mr-2"></i>
        <span>{{ notification.message }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import DashboardView from './views/DashboardView.vue'
import MedicalRecordView from './views/MedicalRecordView.vue'
import WellnessFormView from './views/WellnessFormView.vue'
import AppointmentsView from './views/AppointmentsView.vue'
import DocumentsView from './views/DocumentsView.vue'
import ConnectedDevicesView from './views/ConnectedDevicesView.vue'

export default {
  name: 'AthletePortal',
  components: {
    DashboardView,
    MedicalRecordView,
    WellnessFormView,
    AppointmentsView,
    DocumentsView,
    ConnectedDevicesView
  },
  setup() {
    // État de l'application
    const activeTab = ref('dashboard')
    const athlete = ref({})
    const loading = reactive({
      dashboard: false,
      medical: false,
      wellness: false,
      appointments: false,
      documents: false,
      devices: false
    })

    // Données
    const dashboardSummary = ref({})
    const medicalRecords = ref([])
    const wellnessHistory = ref([])
    const appointments = ref([])
    const documents = ref([])
    const connectedDevices = ref([])

    // Notifications
    const notification = ref({
      show: false,
      message: '',
      type: 'success'
    })

    // Onglets de navigation
    const tabs = [
      { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-tachometer-alt' },
      { id: 'medical', label: 'Dossier Médical', icon: 'fas fa-file-medical' },
      { id: 'wellness', label: 'Bien-être', icon: 'fas fa-heart' },
      { id: 'appointments', label: 'Rendez-vous', icon: 'fas fa-calendar-alt' },
      { id: 'documents', label: 'Documents', icon: 'fas fa-file-alt' },
      { id: 'devices', label: 'Appareils', icon: 'fas fa-watch' }
    ]

    // Méthodes
    const showNotification = (message, type = 'success') => {
      notification.value = {
        show: true,
        message,
        type
      }
      setTimeout(() => {
        notification.value.show = false
      }, 3000)
    }

    const apiRequest = async (endpoint, options = {}) => {
      try {
        const response = await fetch(`/api/v1/portal/${endpoint}`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            ...options.headers
          },
          ...options
        })

        if (!response.ok) {
          throw new Error('Erreur de requête')
        }

        return await response.json()
      } catch (error) {
        console.error('Erreur API:', error)
        showNotification('Erreur de connexion', 'error')
        throw error
      }
    }

    const loadDashboardSummary = async () => {
      loading.dashboard = true
      try {
        const data = await apiRequest('dashboard-summary')
        dashboardSummary.value = data
        athlete.value = data.athlete
      } catch (error) {
        console.error('Erreur chargement dashboard:', error)
      } finally {
        loading.dashboard = false
      }
    }

    const loadMedicalRecords = async () => {
      loading.medical = true
      try {
        const data = await apiRequest('medical-record-summary')
        medicalRecords.value = data.records
      } catch (error) {
        console.error('Erreur chargement dossier médical:', error)
      } finally {
        loading.medical = false
      }
    }

    const loadWellnessHistory = async () => {
      loading.wellness = true
      try {
        const data = await apiRequest('wellness-history')
        wellnessHistory.value = data.history
      } catch (error) {
        console.error('Erreur chargement historique bien-être:', error)
      } finally {
        loading.wellness = false
      }
    }

    const loadAppointments = async () => {
      loading.appointments = true
      try {
        const data = await apiRequest('appointments')
        appointments.value = data.appointments
      } catch (error) {
        console.error('Erreur chargement rendez-vous:', error)
      } finally {
        loading.appointments = false
      }
    }

    const loadDocuments = async () => {
      loading.documents = true
      try {
        const data = await apiRequest('documents')
        documents.value = data.documents
      } catch (error) {
        console.error('Erreur chargement documents:', error)
      } finally {
        loading.documents = false
      }
    }

    const submitWellnessForm = async (formData) => {
      try {
        const response = await apiRequest('wellness-form', {
          method: 'POST',
          body: JSON.stringify(formData)
        })
        
        showNotification('Formulaire soumis avec succès')
        await loadWellnessHistory()
        await loadDashboardSummary()
      } catch (error) {
        console.error('Erreur soumission formulaire:', error)
        showNotification('Erreur lors de la soumission', 'error')
      }
    }

    const connectDevice = async (deviceType) => {
      try {
        // Simulation de connexion d'appareil
        showNotification(`Appareil ${deviceType} connecté`)
        await loadConnectedDevices()
      } catch (error) {
        console.error('Erreur connexion appareil:', error)
        showNotification('Erreur de connexion appareil', 'error')
      }
    }

    const disconnectDevice = async (deviceId) => {
      try {
        // Simulation de déconnexion d'appareil
        showNotification('Appareil déconnecté')
        await loadConnectedDevices()
      } catch (error) {
        console.error('Erreur déconnexion appareil:', error)
        showNotification('Erreur de déconnexion', 'error')
      }
    }

    const loadConnectedDevices = async () => {
      loading.devices = true
      try {
        // Simulation des appareils connectés
        connectedDevices.value = [
          { id: 1, name: 'Garmin Forerunner 945', type: 'watch', connected: true, lastSync: '2024-01-20 10:30' },
          { id: 2, name: 'Apple Watch Series 7', type: 'watch', connected: false, lastSync: '2024-01-19 15:45' }
        ]
      } catch (error) {
        console.error('Erreur chargement appareils:', error)
      } finally {
        loading.devices = false
      }
    }

    const logout = () => {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }

    // Initialisation
    onMounted(async () => {
      await loadDashboardSummary()
      await loadMedicalRecords()
      await loadWellnessHistory()
      await loadAppointments()
      await loadDocuments()
      await loadConnectedDevices()
    })

    return {
      activeTab,
      athlete,
      loading,
      dashboardSummary,
      medicalRecords,
      wellnessHistory,
      appointments,
      documents,
      connectedDevices,
      notification,
      tabs,
      loadDashboardSummary,
      loadMedicalRecords,
      loadWellnessHistory,
      loadAppointments,
      loadDocuments,
      submitWellnessForm,
      connectDevice,
      disconnectDevice,
      logout
    }
  }
}
</script>

<style scoped>
/* Styles spécifiques au portail athlète */
.min-h-screen {
  min-height: 100vh;
}

/* Animation pour les notifications */
.fixed {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style> 