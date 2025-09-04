<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-heart text-red-600 mr-2"></i>
            Formulaire de Bien-être
          </h2>
          <p class="text-gray-600 mt-1">Suivi quotidien de votre état de santé</p>
        </div>
        <button @click="$emit('refresh')" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <i class="fas fa-sync-alt mr-2"></i>
          Actualiser
        </button>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow p-6">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Sommeil -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-bed mr-2"></i>
            Heures de sommeil (hier soir)
          </label>
          <input 
            v-model="form.sleep_hours" 
            type="number" 
            min="0" 
            max="24" 
            step="0.5"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
        </div>

        <!-- Niveau de stress -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-brain mr-2"></i>
            Niveau de stress (1-10)
          </label>
          <div class="flex items-center space-x-4">
            <input 
              v-model="form.stress_level" 
              type="range" 
              min="1" 
              max="10" 
              class="flex-1"
            >
            <span class="text-lg font-semibold text-gray-900 w-8 text-center">
              {{ form.stress_level }}
            </span>
          </div>
          <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>Faible</span>
            <span>Élevé</span>
          </div>
        </div>

        <!-- Niveau d'énergie -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-bolt mr-2"></i>
            Niveau d'énergie (1-10)
          </label>
          <div class="flex items-center space-x-4">
            <input 
              v-model="form.energy_level" 
              type="range" 
              min="1" 
              max="10" 
              class="flex-1"
            >
            <span class="text-lg font-semibold text-gray-900 w-8 text-center">
              {{ form.energy_level }}
            </span>
          </div>
          <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>Faible</span>
            <span>Élevé</span>
          </div>
        </div>

        <!-- Humeur -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-smile mr-2"></i>
            Humeur générale
          </label>
          <select 
            v-model="form.mood" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
            <option value="">Sélectionner...</option>
            <option value="excellent">😊 Excellent</option>
            <option value="good">🙂 Bon</option>
            <option value="neutral">😐 Neutre</option>
            <option value="bad">😔 Mauvais</option>
            <option value="terrible">😢 Terrible</option>
          </select>
        </div>

        <!-- Niveau de douleur -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-pain mr-2"></i>
            Niveau de douleur (0-10)
          </label>
          <div class="flex items-center space-x-4">
            <input 
              v-model="form.pain_level" 
              type="range" 
              min="0" 
              max="10" 
              class="flex-1"
            >
            <span class="text-lg font-semibold text-gray-900 w-8 text-center">
              {{ form.pain_level }}
            </span>
          </div>
          <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>Aucune</span>
            <span>Intense</span>
          </div>
        </div>

        <!-- Localisation de la douleur -->
        <div v-if="form.pain_level > 0">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-map-marker-alt mr-2"></i>
            Localisation de la douleur
          </label>
          <input 
            v-model="form.pain_location" 
            type="text" 
            placeholder="Ex: Genou droit, Épaule gauche..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>

        <!-- Symptômes -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-thermometer-half mr-2"></i>
            Symptômes (optionnel)
          </label>
          <div class="space-y-2">
            <div v-for="(symptom, index) in form.symptoms" :key="index" class="flex space-x-2">
              <input 
                v-model="form.symptoms[index]" 
                type="text" 
                placeholder="Symptôme..."
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <button 
                @click="removeSymptom(index)" 
                type="button"
                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
            <button 
              @click="addSymptom" 
              type="button"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              <i class="fas fa-plus mr-2"></i>
              Ajouter un symptôme
            </button>
          </div>
        </div>

        <!-- Médicaments pris -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-pills mr-2"></i>
            Médicaments pris (optionnel)
          </label>
          <div class="space-y-2">
            <div v-for="(medication, index) in form.medication_taken" :key="index" class="flex space-x-2">
              <input 
                v-model="form.medication_taken[index]" 
                type="text" 
                placeholder="Médicament..."
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <button 
                @click="removeMedication(index)" 
                type="button"
                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
            <button 
              @click="addMedication" 
              type="button"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              <i class="fas fa-plus mr-2"></i>
              Ajouter un médicament
            </button>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-sticky-note mr-2"></i>
            Notes supplémentaires (optionnel)
          </label>
          <textarea 
            v-model="form.notes" 
            rows="4"
            placeholder="Décrivez votre état général, préoccupations, etc..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
        </div>

        <!-- Boutons -->
        <div class="flex space-x-4">
          <button 
            type="submit" 
            :disabled="submitting"
            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
            <i v-else class="fas fa-paper-plane mr-2"></i>
            {{ submitting ? 'Envoi en cours...' : 'Soumettre' }}
          </button>
          <button 
            @click="resetForm" 
            type="button"
            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
          >
            <i class="fas fa-undo mr-2"></i>
            Réinitialiser
          </button>
        </div>
      </form>
    </div>

    <!-- Historique -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-history text-blue-600 mr-2"></i>
        Historique Récent
      </h3>
      
      <div v-if="loading" class="text-center py-4">
        <i class="fas fa-spinner fa-spin text-blue-600"></i>
        <p class="text-gray-500 mt-2">Chargement de l'historique...</p>
      </div>
      
      <div v-else-if="history.length" class="space-y-4">
        <div v-for="entry in history.slice(0, 5)" :key="entry.date" 
             class="border border-gray-200 rounded-lg p-4">
          <div class="flex justify-between items-start mb-2">
            <span class="font-medium text-gray-900">{{ formatDate(entry.date) }}</span>
            <span class="text-sm font-semibold" :class="getScoreColor(entry.wellness_score)">
              {{ entry.wellness_score }}%
            </span>
          </div>
          
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <span class="text-gray-600">Sommeil:</span>
              <span class="font-medium">{{ entry.sleep_hours }}h</span>
            </div>
            <div>
              <span class="text-gray-600">Stress:</span>
              <span class="font-medium">{{ entry.stress_level }}/10</span>
            </div>
            <div>
              <span class="text-gray-600">Énergie:</span>
              <span class="font-medium">{{ entry.energy_level }}/10</span>
            </div>
            <div>
              <span class="text-gray-600">Humeur:</span>
              <span class="font-medium">{{ getMoodLabel(entry.mood) }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-4 text-gray-500">
        <i class="fas fa-history text-gray-400 mb-2"></i>
        <p>Aucun historique disponible</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'

export default {
  name: 'WellnessFormView',
  props: {
    history: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['submit', 'refresh'],
  setup(props, { emit }) {
    const submitting = ref(false)
    
    const form = reactive({
      sleep_hours: 8,
      stress_level: 5,
      energy_level: 7,
      mood: '',
      pain_level: 0,
      pain_location: '',
      symptoms: [''],
      medication_taken: [''],
      notes: ''
    })

    const submitForm = async () => {
      submitting.value = true
      
      try {
        // Nettoyer les tableaux vides
        const cleanForm = {
          ...form,
          symptoms: form.symptoms.filter(s => s.trim()),
          medication_taken: form.medication_taken.filter(m => m.trim())
        }
        
        await emit('submit', cleanForm)
        resetForm()
      } catch (error) {
        console.error('Erreur soumission formulaire:', error)
      } finally {
        submitting.value = false
      }
    }

    const resetForm = () => {
      Object.assign(form, {
        sleep_hours: 8,
        stress_level: 5,
        energy_level: 7,
        mood: '',
        pain_level: 0,
        pain_location: '',
        symptoms: [''],
        medication_taken: [''],
        notes: ''
      })
    }

    const addSymptom = () => {
      form.symptoms.push('')
    }

    const removeSymptom = (index) => {
      form.symptoms.splice(index, 1)
      if (form.symptoms.length === 0) {
        form.symptoms.push('')
      }
    }

    const addMedication = () => {
      form.medication_taken.push('')
    }

    const removeMedication = (index) => {
      form.medication_taken.splice(index, 1)
      if (form.medication_taken.length === 0) {
        form.medication_taken.push('')
      }
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    }

    const getScoreColor = (score) => {
      if (score >= 80) return 'text-green-600'
      if (score >= 60) return 'text-yellow-600'
      return 'text-red-600'
    }

    const getMoodLabel = (mood) => {
      const labels = {
        'excellent': '😊 Excellent',
        'good': '🙂 Bon',
        'neutral': '😐 Neutre',
        'bad': '😔 Mauvais',
        'terrible': '😢 Terrible'
      }
      return labels[mood] || mood
    }

    return {
      form,
      submitting,
      submitForm,
      resetForm,
      addSymptom,
      removeSymptom,
      addMedication,
      removeMedication,
      formatDate,
      getScoreColor,
      getMoodLabel
    }
  }
}
</script> 