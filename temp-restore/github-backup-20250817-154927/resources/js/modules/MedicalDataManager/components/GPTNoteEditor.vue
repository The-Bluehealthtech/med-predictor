<template>
  <div class="gpt-note-editor">
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Modifier la Note Médicale' : 'Nouvelle Note Médicale' }}
          </h3>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Athlète: {{ athleteName }}</span>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
          </div>
        </div>
      </div>

      <div class="p-6">
        <!-- AI Generation Section -->
        <div v-if="!isEditing && showAIGeneration" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-blue-900">Génération IA</h4>
            <button
              @click="generateAINote"
              :disabled="aiGenerating"
              class="text-xs bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
            >
              {{ aiGenerating ? 'Génération...' : 'Générer avec IA' }}
            </button>
          </div>
          <p class="text-xs text-blue-700">
            L'IA peut générer une note médicale basée sur les données disponibles.
            Toutes les suggestions doivent être révisées par un médecin.
          </p>
        </div>

        <!-- Two Panel Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Panel 1: Input -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de Note
              </label>
              <select 
                v-model="form.note_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
              >
                <option value="">Sélectionner le type</option>
                <option value="consultation">Consultation</option>
                <option value="examination">Examen</option>
                <option value="treatment">Traitement</option>
                <option value="follow_up">Suivi</option>
                <option value="emergency">Urgence</option>
              </select>
            </div>

            <!-- ICD-11 Diagnostic Section -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Diagnostic ICD-11
              </label>
              <ICDSearchInput
                v-model="form.icd11_diagnostic"
                placeholder="Rechercher un diagnostic ICD-11..."
                @selected="onDiagnosticSelected"
              />
              <p class="mt-1 text-sm text-gray-500">
                Sélectionnez le diagnostic ICD-11 standardisé pour cette note clinique
              </p>
            </div>

            <!-- Selected Diagnostic Display -->
            <div v-if="selectedDiagnostic" class="p-3 bg-blue-50 border border-blue-200 rounded-md">
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-blue-900">{{ selectedDiagnostic.code }}</span>
                  <span class="text-sm text-blue-700 ml-2">{{ selectedDiagnostic.label }}</span>
                </div>
                <button 
                  @click="clearDiagnostic"
                  class="text-red-600 hover:text-red-800 text-sm"
                >
                  ✕
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Transcription Clinique
              </label>
              <textarea 
                v-model="transcript"
                rows="12"
                placeholder="Collez ici la transcription de l'encounter clinique..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
              ></textarea>
              <p class="text-xs text-gray-500 mt-1">
                Entrez la transcription brute de l'encounter pour générer une note structurée
              </p>
            </div>

            <div class="flex space-x-3">
              <button
                @click="generateAINote"
                :disabled="aiGenerating || !transcript || !form.note_type"
                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                {{ aiGenerating ? 'Génération...' : 'Générer Note IA' }}
              </button>
              <button
                @click="clearTranscript"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
              >
                Effacer
              </button>
            </div>
          </div>

          <!-- Panel 2: Output -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Note Médicale Générée
                <span v-if="generatedByAI" class="text-blue-600 text-xs ml-2">(IA)</span>
              </label>
              <textarea 
                v-model="form.note_content"
                rows="12"
                placeholder="La note médicale générée apparaîtra ici..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
              ></textarea>
            </div>

            <!-- AI Metadata Display -->
            <div v-if="aiMetadata" class="p-3 bg-gray-50 rounded-md">
              <h5 class="text-sm font-medium text-gray-700 mb-2">Métadonnées IA</h5>
              <div class="text-xs text-gray-600 space-y-1">
                <div>Confiance: {{ aiMetadata.confidence_score }}%</div>
                <div>Modèle: {{ aiMetadata.ai_model_version }}</div>
                <div>Généré le: {{ formatDate(aiMetadata.generation_timestamp) }}</div>
              </div>
            </div>

            <!-- Human-in-the-Loop Warning -->
            <div v-if="generatedByAI" class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <span class="text-yellow-600">⚠️</span>
                </div>
                <div class="ml-3">
                  <h5 class="text-sm font-medium text-yellow-800">Révision Requise</h5>
                  <p class="text-xs text-yellow-700 mt-1">
                    Cette note a été générée par IA. Veuillez la réviser et l'éditer avant de l'approuver.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
          <button 
            type="button"
            @click="$emit('cancel')"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Annuler
          </button>
          
          <div class="flex space-x-3">
            <button 
              type="button"
              @click="saveAsDraft"
              :disabled="loading"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors disabled:opacity-50"
            >
              {{ loading ? 'Sauvegarde...' : 'Sauvegarder brouillon' }}
            </button>
            
            <button 
              type="button"
              @click="saveAndApprove"
              :disabled="loading || !form.note_content"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50"
            >
              {{ loading ? 'Soumission...' : 'Sauvegarder & Approuver' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ICDSearchInput from './ICDSearchInput.vue'

export default {
  name: 'GPTNoteEditor',
  components: {
    ICDSearchInput
  },
  props: {
    note: {
      type: Object,
      default: null
    },
    athleteId: {
      type: [String, Number],
      required: true
    },
    athleteName: {
      type: String,
      default: 'Athlète'
    },
    showAIGeneration: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      form: {
        athlete_id: this.athleteId,
        note_type: '',
        note_content: '',
        generated_by_ai: false,
        ai_metadata: null,
        icd11_diagnostic: null
      },
      transcript: '',
      aiGenerating: false,
      loading: false,
      generatedByAI: false,
      aiMetadata: null,
      selectedDiagnostic: null
    }
  },
  computed: {
    isEditing() {
      return !!this.note
    }
  },
  mounted() {
    if (this.note) {
      this.loadNoteData()
    }
  },
  methods: {
    loadNoteData() {
      this.form = {
        athlete_id: this.athleteId,
        note_type: this.note.note_type || '',
        note_content: this.note.note_json?.content || '',
        generated_by_ai: this.note.generated_by_ai || false,
        ai_metadata: this.note.ai_metadata || null,
        icd11_diagnostic: this.note.icd11_diagnostic || null
      }
      this.generatedByAI = this.note.generated_by_ai || false
      this.aiMetadata = this.note.ai_metadata || null
      this.selectedDiagnostic = this.note.icd11_diagnostic || null
    },

    async generateAINote() {
      if (!this.transcript || !this.form.note_type) {
        this.$toast.error('Veuillez entrer une transcription et sélectionner un type de note')
        return
      }

      this.aiGenerating = true
      try {
        const response = await this.$http.post('/api/v1/medical-notes/ai/generate-draft', {
          transcript: this.transcript,
          athlete_id: this.athleteId,
          note_type: this.form.note_type,
          context: this.getAthleteContext()
        })

        const aiData = response.data.data
        this.form.note_content = aiData.draft_content
        this.form.generated_by_ai = true
        this.form.ai_metadata = aiData.metadata
        this.generatedByAI = true
        this.aiMetadata = aiData.metadata

        this.$toast.success('Note générée par IA avec succès')
      } catch (error) {
        console.error('Error generating AI note:', error)
        
        if (error.response?.data?.error) {
          this.$toast.error(`Erreur IA: ${error.response.data.error}`)
        } else {
          this.$toast.error('Erreur lors de la génération IA')
        }
      } finally {
        this.aiGenerating = false
      }
    },

    getAthleteContext() {
      // This would typically fetch athlete's medical history
      return {
        athlete_id: this.athleteId,
        recent_injuries: [],
        medical_history: [],
        current_medications: []
      }
    },

    clearTranscript() {
      this.transcript = ''
      this.form.note_content = ''
      this.generatedByAI = false
      this.aiMetadata = null
    },

    onDiagnosticSelected(diagnostic) {
      this.selectedDiagnostic = diagnostic
    },

    clearDiagnostic() {
      this.selectedDiagnostic = null
      this.form.icd11_diagnostic = null
    },

    async saveAsDraft() {
      if (!this.form.note_content) {
        this.$toast.error('Veuillez entrer le contenu de la note')
        return
      }

      this.loading = true
      try {
        const data = {
          athlete_id: this.athleteId,
          note_json: {
            content: this.form.note_content,
            type: this.form.note_type
          },
          note_type: this.form.note_type,
          generated_by_ai: this.generatedByAI,
          ai_metadata: this.aiMetadata,
          icd11_diagnostic: this.selectedDiagnostic,
          status: 'draft'
        }

        if (this.isEditing) {
          await this.$http.put(`/api/v1/medical-notes/${this.note.id}`, data)
          this.$toast.success('Note mise à jour avec succès')
        } else {
          await this.$http.post('/api/v1/medical-notes', data)
          this.$toast.success('Note enregistrée avec succès')
        }
        
        this.$emit('saved')
      } catch (error) {
        console.error('Error saving note:', error)
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join(', ')
          this.$toast.error(`Erreur de validation: ${errorMessages}`)
        } else {
          this.$toast.error('Erreur lors de la sauvegarde')
        }
      } finally {
        this.loading = false
      }
    },

    async saveAndApprove() {
      if (!this.form.note_content) {
        this.$toast.error('Veuillez entrer le contenu de la note')
        return
      }

      this.loading = true
      try {
        const data = {
          athlete_id: this.athleteId,
          note_json: {
            content: this.form.note_content,
            type: this.form.note_type
          },
          note_type: this.form.note_type,
          generated_by_ai: this.generatedByAI,
          ai_metadata: this.aiMetadata,
          icd11_diagnostic: this.selectedDiagnostic,
          status: 'signed'
        }

        if (this.isEditing) {
          await this.$http.put(`/api/v1/medical-notes/${this.note.id}`, data)
          this.$toast.success('Note mise à jour et approuvée')
        } else {
          await this.$http.post('/api/v1/medical-notes', data)
          this.$toast.success('Note enregistrée et approuvée')
        }
        
        this.$emit('saved')
      } catch (error) {
        console.error('Error saving and approving note:', error)
        
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors
          const errorMessages = Object.values(errors).flat().join(', ')
          this.$toast.error(`Erreur de validation: ${errorMessages}`)
        } else {
          this.$toast.error('Erreur lors de la sauvegarde')
        }
      } finally {
        this.loading = false
      }
    },

    formatDate(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString('fr-FR')
    }
  }
}
</script>

<style scoped>
.gpt-note-editor {
  @apply max-w-6xl mx-auto;
}
</style> 