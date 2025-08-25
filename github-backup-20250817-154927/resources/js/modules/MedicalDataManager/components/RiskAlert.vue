<template>
  <div class="risk-alert" :class="getAlertClasses()">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <div class="flex items-center space-x-2 mb-2">
          <span class="text-lg">{{ getAlertIcon() }}</span>
          <div>
            <h4 class="font-medium text-gray-900">{{ alert.athlete?.name || 'Athlète inconnu' }}</h4>
            <p class="text-sm text-gray-600">{{ alert.type_display }}</p>
          </div>
        </div>
        
        <p class="text-sm text-gray-700 mb-2">{{ alert.message }}</p>
        
        <div class="flex items-center space-x-4 text-xs text-gray-500">
          <span>Score: {{ alert.score_percentage }}%</span>
          <span>Confiance: {{ Math.round(alert.ai_metadata?.confidence_score * 100) || 0 }}%</span>
          <span>{{ formatDate(alert.created_at) }}</span>
        </div>

        <!-- AI Recommendations -->
        <div v-if="alert.recommendations" class="mt-3 p-3 bg-blue-50 rounded-md">
          <h5 class="text-xs font-medium text-blue-800 mb-1">Recommandations IA:</h5>
          <ul class="text-xs text-blue-700 space-y-1">
            <li v-if="alert.recommendations.immediate_action">
              • {{ alert.recommendations.immediate_action }}
            </li>
            <li v-if="alert.recommendations.follow_up">
              • {{ alert.recommendations.follow_up }}
            </li>
            <li v-if="alert.recommendations.monitoring">
              • {{ alert.recommendations.monitoring }}
            </li>
          </ul>
        </div>
      </div>

      <div class="flex flex-col items-end space-y-2">
        <!-- Priority Badge -->
        <span :class="getPriorityClass()" class="text-xs px-2 py-1 rounded-full font-medium">
          {{ alert.priority_display }}
        </span>

        <!-- Urgency Indicator -->
        <div v-if="alert.urgency_indicator !== 'resolved'" class="flex items-center space-x-1">
          <div :class="getUrgencyClass()" class="w-2 h-2 rounded-full"></div>
          <span class="text-xs text-gray-500">{{ getUrgencyText() }}</span>
        </div>

        <!-- Acknowledge Button -->
        <button 
          v-if="!alert.resolved"
          @click="acknowledgeAlert"
          :disabled="acknowledging"
          class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700 transition-colors disabled:opacity-50"
        >
          {{ acknowledging ? 'Reconnaissance...' : 'Reconnaître' }}
        </button>
      </div>
    </div>

    <!-- AI Metadata -->
    <div v-if="alert.ai_metadata" class="mt-3 pt-3 border-t border-gray-200">
      <div class="flex items-center justify-between text-xs text-gray-500">
        <span>Généré par IA ({{ alert.ai_metadata.model_version }})</span>
        <span>{{ formatDate(alert.ai_metadata.analysis_timestamp) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RiskAlert',
  props: {
    alert: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      acknowledging: false
    }
  },
  methods: {
    getAlertClasses() {
      const baseClasses = 'p-4 rounded-lg border'
      
      if (this.alert.resolved) {
        return `${baseClasses} bg-gray-50 border-gray-200`
      }

      const priorityClasses = {
        'critical': 'bg-red-50 border-red-200',
        'high': 'bg-orange-50 border-orange-200',
        'medium': 'bg-yellow-50 border-yellow-200',
        'low': 'bg-blue-50 border-blue-200'
      }

      return `${baseClasses} ${priorityClasses[this.alert.priority] || 'bg-gray-50 border-gray-200'}`
    },

    getAlertIcon() {
      const icons = {
        'sca': '❤️',
        'injury': '🩹',
        'concussion': '🧠',
        'medication': '💊',
        'other': '⚠️'
      }
      return icons[this.alert.type] || '⚠️'
    },

    getPriorityClass() {
      const classes = {
        'critical': 'bg-red-100 text-red-800',
        'high': 'bg-orange-100 text-orange-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-blue-100 text-blue-800'
      }
      return classes[this.alert.priority] || 'bg-gray-100 text-gray-800'
    },

    getUrgencyClass() {
      const classes = {
        'urgent': 'bg-red-500',
        'overdue': 'bg-red-600',
        'normal': 'bg-green-500'
      }
      return classes[this.alert.urgency_indicator] || 'bg-gray-400'
    },

    getUrgencyText() {
      const texts = {
        'urgent': 'Urgent',
        'overdue': 'En retard',
        'normal': 'Normal'
      }
      return texts[this.alert.urgency_indicator] || 'Normal'
    },

    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      const now = new Date()
      const diffHours = Math.floor((now - date) / (1000 * 60 * 60))
      
      if (diffHours < 1) {
        return 'À l\'instant'
      } else if (diffHours < 24) {
        return `Il y a ${diffHours}h`
      } else {
        return date.toLocaleDateString('fr-FR')
      }
    },

    async acknowledgeAlert() {
      this.acknowledging = true
      try {
        await this.$http.patch(`/api/v1/risk-alerts/${this.alert.id}/acknowledge`, {
          notes: 'Alerte reconnue via le tableau de bord',
          action_taken: 'Examen médical programmé'
        })
        
        this.$emit('acknowledged', this.alert.id)
        this.$toast.success('Alerte reconnue avec succès')
      } catch (error) {
        console.error('Error acknowledging alert:', error)
        this.$toast.error('Erreur lors de la reconnaissance de l\'alerte')
      } finally {
        this.acknowledging = false
      }
    }
  }
}
</script>

<style scoped>
.risk-alert {
  @apply transition-all duration-200;
}

.risk-alert:hover {
  @apply shadow-md;
}
</style> 