<template>
  <div class="scat-assessment-form">
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Modifier l\'Évaluation SCAT' : 'Nouvelle Évaluation SCAT' }}
          </h3>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Athlète: {{ athleteName }}</span>
            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
          </div>
        </div>
      </div>

      <form @submit.prevent="saveAssessment" class="p-6">
        <!-- Assessment Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Type d'Évaluation *
            </label>
            <select 
              v-model="form.assessment_type"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Sélectionner le type</option>
              <option value="baseline">Évaluation de base</option>
              <option value="post_injury">Post-blessure</option>
              <option value="follow_up">Suivi</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date d'Évaluation *
            </label>
            <input 
              v-model="form.assessment_date"
              type="date"
              required
              :max="today"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Résultat *
            </label>
            <select 
              v-model="form.result"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Sélectionner le résultat</option>
              <option value="normal">Normal</option>
              <option value="abnormal">Anormal</option>
              <option value="unclear">Imprécis</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Score SCAT
            </label>
            <input 
              v-model="form.scat_score"
              type="number"
              min="0"
              max="132"
              placeholder="Ex: 85"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>
        </div>

        <!-- Concussion Confirmation -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Commotion Confirmée *
          </label>
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input 
                v-model="form.concussion_confirmed"
                type="radio"
                :value="true"
                class="mr-2"
              />
              <span>Oui</span>
            </label>
            <label class="flex items-center">
              <input 
                v-model="form.concussion_confirmed"
                type="radio"
                :value="false"
                class="mr-2"
              />
              <span>Non</span>
            </label>
          </div>
        </div>

        <!-- SCAT Assessment Data -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Données d'Évaluation SCAT (JSON) *
          </label>
          <div class="space-y-3">
            <!-- Quick Templates -->
            <div class="flex space-x-2 mb-3">
              <button 
                type="button"
                @click="loadTemplate('baseline')"
                class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors"
              >
                Template Base
              </button>
              <button 
                type="button"
                @click="loadTemplate('post_injury')"
                class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors"
              >
                Template Post-Blessure
              </button>
              <button 
                type="button"
                @click="loadTemplate('follow_up')"
                class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors"
              >
                Template Suivi
              </button>
            </div>

            <textarea 
              v-model="form.data_json"
              required
              rows="12"
              placeholder="Entrez les données d'évaluation SCAT au format JSON..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 font-mono text-sm"
              :class="{ 'border-red-300': jsonError }"
            ></textarea>
            
            <div v-if="jsonError" class="text-red-600 text-sm">
              {{ jsonError }}
            </div>
            
            <div v-else-if="isValidJson" class="text-green-600 text-sm">
              ✓ JSON valide
            </div>
          </div>
        </div>

        <!-- Recommendations -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Recommandations
          </label>
          <textarea 
            v-model="form.recommendations"
            rows="4"
            placeholder="Recommandations médicales, restrictions, suivi..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
          ></textarea>
        </div>

        <!-- FIFA Compliance Data -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Données de Conformité FIFA (JSON)
          </label>
          <textarea 
            v-model="form.fifa_concussion_data"
            rows="4"
            placeholder="Données spécifiques à la conformité FIFA..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 font-mono text-sm"
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
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
              type="submit"
              :disabled="loading || !isFormValid"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50"
            >
              {{ loading ? 'Soumission...' : (isEditing ? 'Mettre à jour' : 'Enregistrer') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SCATAssessmentForm',
  props: {
    assessment: {
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
    }
  },
  data() {
    return {
      form: {
        athlete_id: this.athleteId,
        assessment_type: '',
        assessment_date: '',
        result: '',
        concussion_confirmed: false,
        scat_score: null,
        data_json: '',
        recommendations: '',
        fifa_concussion_data: ''
      },
      loading: false,
      jsonError: null
    }
  },
  computed: {
    isEditing() {
      return !!this.assessment
    },
    isValidJson() {
      if (!this.form.data_json) return false
      try {
        JSON.parse(this.form.data_json)
        return true
      } catch {
        return false
      }
    },
    isFormValid() {
      return this.form.assessment_type && 
             this.form.assessment_date &&
             this.form.result &&
             this.form.data_json &&
             this.isValidJson &&
             this.form.athlete_id
    },
    today() {
      return new Date().toISOString().split('T')[0]
    }
  },
  watch: {
    'form.data_json'(newValue) {
      this.validateJson(newValue)
    }
  },
  mounted() {
    if (this.assessment) {
      this.loadAssessmentData()
    } else {
      this.form.assessment_date = this.today
    }
  },
  methods: {
    loadAssessmentData() {
      this.form = {
        athlete_id: this.athleteId,
        assessment_type: this.assessment.assessment_type || '',
        assessment_date: this.assessment.assessment_date ? this.assessment.assessment_date.split('T')[0] : this.today,
        result: this.assessment.result || '',
        concussion_confirmed: this.assessment.concussion_confirmed || false,
        scat_score: this.assessment.scat_score || null,
        data_json: this.assessment.data_json ? JSON.stringify(this.assessment.data_json, null, 2) : '',
        recommendations: this.assessment.recommendations || '',
        fifa_concussion_data: this.assessment.fifa_concussion_data ? JSON.stringify(this.assessment.fifa_concussion_data, null, 2) : ''
      }
    },

    validateJson(jsonString) {
      if (!jsonString) {
        this.jsonError = null
        return
      }
      
      try {
        JSON.parse(jsonString)
        this.jsonError = null
      } catch (error) {
        this.jsonError = `Erreur JSON: ${error.message}`
      }
    },

    loadTemplate(type) {
      const templates = {
        baseline: {
          assessment_type: 'baseline',
          data_json: JSON.stringify({
            symptom_evaluation: {
              headache: 0,
              pressure_in_head: 0,
              neck_pain: 0,
              nausea_vomiting: 0,
              dizziness: 0,
              blurred_vision: 0,
              balance_problems: 0,
              sensitivity_to_light: 0,
              sensitivity_to_noise: 0,
              feeling_like_in_fog: 0,
              feeling_slowed_down: 0,
              difficulty_concentrating: 0,
              difficulty_remembering: 0,
              fatigue_low_energy: 0,
              confusion: 0,
              drowsiness: 0,
              trouble_falling_asleep: 0,
              more_emotional: 0,
              irritability: 0,
              sadness: 0,
              nervous_anxious: 0
            },
            cognitive_assessment: {
              orientation: {
                month: "correct",
                date: "correct",
                day_of_week: "correct",
                year: "correct",
                time: "correct"
              },
              immediate_memory: {
                trial_1: 5,
                trial_2: 5,
                trial_3: 5
              },
              concentration: {
                digits_backward: 4,
                months_backward: "correct"
              },
              delayed_recall: 5
            },
            neurological_screen: {
              pupillary_response: "normal",
              coordination: "normal",
              sensation: "normal"
            }
          }, null, 2)
        },
        post_injury: {
          assessment_type: 'post_injury',
          data_json: JSON.stringify({
            symptom_evaluation: {
              headache: 2,
              pressure_in_head: 1,
              neck_pain: 0,
              nausea_vomiting: 0,
              dizziness: 1,
              blurred_vision: 0,
              balance_problems: 1,
              sensitivity_to_light: 1,
              sensitivity_to_noise: 0,
              feeling_like_in_fog: 2,
              feeling_slowed_down: 1,
              difficulty_concentrating: 2,
              difficulty_remembering: 1,
              fatigue_low_energy: 1,
              confusion: 0,
              drowsiness: 0,
              trouble_falling_asleep: 0,
              more_emotional: 0,
              irritability: 0,
              sadness: 0,
              nervous_anxious: 0
            },
            cognitive_assessment: {
              orientation: {
                month: "correct",
                date: "correct",
                day_of_week: "correct",
                year: "correct",
                time: "correct"
              },
              immediate_memory: {
                trial_1: 3,
                trial_2: 4,
                trial_3: 4
              },
              concentration: {
                digits_backward: 3,
                months_backward: "correct"
              },
              delayed_recall: 3
            },
            neurological_screen: {
              pupillary_response: "normal",
              coordination: "normal",
              sensation: "normal"
            }
          }, null, 2)
        },
        follow_up: {
          assessment_type: 'follow_up',
          data_json: JSON.stringify({
            symptom_evaluation: {
              headache: 0,
              pressure_in_head: 0,
              neck_pain: 0,
              nausea_vomiting: 0,
              dizziness: 0,
              blurred_vision: 0,
              balance_problems: 0,
              sensitivity_to_light: 0,
              sensitivity_to_noise: 0,
              feeling_like_in_fog: 0,
              feeling_slowed_down: 0,
              difficulty_concentrating: 0,
              difficulty_remembering: 0,
              fatigue_low_energy: 0,
              confusion: 0,
              drowsiness: 0,
              trouble_falling_asleep: 0,
              more_emotional: 0,
              irritability: 0,
              sadness: 0,
              nervous_anxious: 0
            },
            cognitive_assessment: {
              orientation: {
                month: "correct",
                date: "correct",
                day_of_week: "correct",
                year: "correct",
                time: "correct"
              },
              immediate_memory: {
                trial_1: 5,
                trial_2: 5,
                trial_3: 5
              },
              concentration: {
                digits_backward: 4,
                months_backward: "correct"
              },
              delayed_recall: 5
            },
            neurological_screen: {
              pupillary_response: "normal",
              coordination: "normal",
              sensation: "normal"
            }
          }, null, 2)
        }
      }

      const template = templates[type]
      if (template) {
        this.form.assessment_type = template.assessment_type
        this.form.data_json = template.data_json
      }
    },

    async saveAssessment() {
      if (!this.isFormValid) {
        this.$toast.error('Veuillez corriger les erreurs dans le formulaire')
        return
      }

      this.loading = true
      try {
        const data = {
          ...this.form,
          data_json: JSON.parse(this.form.data_json),
          fifa_concussion_data: this.form.fifa_concussion_data ? JSON.parse(this.form.fifa_concussion_data) : null
        }

        if (this.isEditing) {
          await this.$http.put(`/api/v1/scat-assessments/${this.assessment.id}`, data)
          this.$toast.success('Évaluation SCAT mise à jour avec succès')
        } else {
          await this.$http.post('/api/v1/scat-assessments', data)
          this.$toast.success('Évaluation SCAT enregistrée avec succès')
        }
        
        this.$emit('saved')
        
      } catch (error) {
        console.error('Error saving SCAT assessment:', error)
        
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

    async saveAsDraft() {
      this.loading = true
      try {
        const data = {
          ...this.form,
          data_json: this.form.data_json ? JSON.parse(this.form.data_json) : {},
          fifa_concussion_data: this.form.fifa_concussion_data ? JSON.parse(this.form.fifa_concussion_data) : null
        }

        await this.$http.post('/api/v1/scat-assessments', data)
        
        this.$toast.success('Brouillon sauvegardé')
        this.$emit('draft-saved')
        
      } catch (error) {
        console.error('Error saving draft:', error)
        this.$toast.error('Erreur lors de la sauvegarde du brouillon')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.scat-assessment-form {
  @apply max-w-4xl mx-auto;
}
</style> 