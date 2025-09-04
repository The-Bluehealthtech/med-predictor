<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
          <p class="text-gray-600 mt-1">Bienvenue, {{ summary.athlete?.name }}</p>
        </div>
        <button @click="$emit('refresh')" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <i class="fas fa-sync-alt mr-2"></i>
          Actualiser
        </button>
      </div>
    </div>

    <!-- Score de Santé -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-heartbeat text-red-600 mr-2"></i>
        Score de Santé
      </h3>
      
      <div class="flex items-center space-x-6">
        <div class="flex-shrink-0">
          <div class="relative">
            <svg class="w-24 h-24" viewBox="0 0 36 36">
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                    fill="none" 
                    stroke="#e5e7eb" 
                    stroke-width="2"/>
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                    fill="none" 
                    stroke="#3b82f6" 
                    stroke-width="2" 
                    stroke-dasharray="100, 100"
                    :stroke-dashoffset="100 - (summary.athlete?.health_score || 0)"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-xl font-bold text-gray-900">{{ summary.athlete?.health_score || 0 }}%</span>
            </div>
          </div>
        </div>
        
        <div class="flex-1">
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ summary.appointments?.upcoming || 0 }}</div>
              <div class="text-sm text-gray-600">Rendez-vous à venir</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ summary.documents?.total || 0 }}</div>
              <div class="text-sm text-gray-600">Documents</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Rendez-vous Récents -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
          Rendez-vous Récents
        </h3>
        
        <div v-if="loading" class="text-center py-4">
          <i class="fas fa-spinner fa-spin text-blue-600"></i>
        </div>
        
        <div v-else-if="summary.appointments?.recent?.length" class="space-y-3">
          <div v-for="appointment in summary.appointments.recent.slice(0, 3)" 
               :key="appointment.id"
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div>
              <div class="font-medium text-gray-900">{{ appointment.title }}</div>
              <div class="text-sm text-gray-600">
                {{ formatDate(appointment.appointment_date) }}
              </div>
            </div>
            <span :class="[
              'px-2 py-1 text-xs font-semibold rounded-full',
              appointment.status === 'confirmed' ? 'bg-green-100 text-green-800' :
              appointment.status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' :
              'bg-gray-100 text-gray-800'
            ]">
              {{ getStatusLabel(appointment.status) }}
            </span>
          </div>
        </div>
        
        <div v-else class="text-center py-4 text-gray-500">
          <i class="fas fa-calendar-times text-gray-400 mb-2"></i>
          <p>Aucun rendez-vous récent</p>
        </div>
      </div>

      <!-- Documents Récents -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          <i class="fas fa-file-medical text-green-600 mr-2"></i>
          Documents Récents
        </h3>
        
        <div v-if="loading" class="text-center py-4">
          <i class="fas fa-spinner fa-spin text-green-600"></i>
        </div>
        
        <div v-else-if="summary.documents?.recent?.length" class="space-y-3">
          <div v-for="document in summary.documents.recent.slice(0, 3)" 
               :key="document.id"
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div>
              <div class="font-medium text-gray-900">{{ document.title }}</div>
              <div class="text-sm text-gray-600">
                {{ getDocumentTypeLabel(document.document_type) }}
              </div>
            </div>
            <span :class="[
              'px-2 py-1 text-xs font-semibold rounded-full',
              document.status === 'analyzed' ? 'bg-green-100 text-green-800' :
              document.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
              'bg-gray-100 text-gray-800'
            ]">
              {{ getDocumentStatusLabel(document.status) }}
            </span>
          </div>
        </div>
        
        <div v-else class="text-center py-4 text-gray-500">
          <i class="fas fa-file-times text-gray-400 mb-2"></i>
          <p>Aucun document récent</p>
        </div>
      </div>

      <!-- Dossier Médical -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          <i class="fas fa-user-md text-purple-600 mr-2"></i>
          Dossier Médical
        </h3>
        
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Total des dossiers</span>
            <span class="font-semibold text-gray-900">{{ summary.health_records?.total || 0 }}</span>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Dernière mise à jour</span>
            <span class="text-sm text-gray-500">
              {{ summary.health_records?.last_updated ? formatDate(summary.health_records.last_updated) : 'Jamais' }}
            </span>
          </div>
          
          <div class="pt-4">
            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
              <i class="fas fa-eye mr-2"></i>
              Voir le dossier complet
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Rapides -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
        Actions Rapides
      </h3>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
          <i class="fas fa-heart text-blue-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-900">Bien-être</span>
        </button>
        
        <button class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
          <i class="fas fa-calendar-plus text-green-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-900">Rendez-vous</span>
        </button>
        
        <button class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
          <i class="fas fa-file-medical text-purple-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-900">Documents</span>
        </button>
        
        <button class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
          <i class="fas fa-watch text-orange-600 text-2xl mb-2"></i>
          <span class="text-sm font-medium text-gray-900">Appareils</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'DashboardView',
  props: {
    summary: {
      type: Object,
      default: () => ({})
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['refresh'],
  setup() {
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const getStatusLabel = (status) => {
      const labels = {
        'scheduled': 'Programmé',
        'confirmed': 'Confirmé',
        'completed': 'Terminé',
        'cancelled': 'Annulé'
      }
      return labels[status] || status
    }

    const getDocumentTypeLabel = (type) => {
      const labels = {
        'medical_record': 'Dossier Médical',
        'imaging': 'Imagerie',
        'lab_result': 'Résultats Labo',
        'prescription': 'Ordonnance',
        'certificate': 'Certificat',
        'other': 'Autre'
      }
      return labels[type] || type
    }

    const getDocumentStatusLabel = (status) => {
      const labels = {
        'pending': 'En attente',
        'processed': 'Traité',
        'analyzed': 'Analysé',
        'archived': 'Archivé'
      }
      return labels[status] || status
    }

    return {
      formatDate,
      getStatusLabel,
      getDocumentTypeLabel,
      getDocumentStatusLabel
    }
  }
})
</script> 