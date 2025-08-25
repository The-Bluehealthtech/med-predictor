<template>
  <div class="injury-form">
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Modifier la Blessure' : 'Nouvelle Blessure' }}
          </h3>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Athlète: {{ athleteName }}</span>
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
          </div>
        </div>
      </div>

      <form @submit.prevent="saveInjury" class="p-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Type de Blessure *
            </label>
            <input 
              v-model="form.type"
              type="text"
              required
              placeholder="Ex: Entorse, Fracture, Contusion..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Zone du Corps *
            </label>
            <select 
              v-model="form.body_zone"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Sélectionner la zone</option>
              <option value="Tête">Tête</option>
              <option value="Cou">Cou</option>
              <option value="Épaule droite">Épaule droite</option>
              <option value="Épaule gauche">Épaule gauche</option>
              <option value="Bras droit">Bras droit</option>
              <option value="Bras gauche">Bras gauche</option>
              <option value="Main droite">Main droite</option>
              <option value="Main gauche">Main gauche</option>
              <option value="Thorax">Thorax</option>
              <option value="Abdomen">Abdomen</option>
              <option value="Dos">Dos</option>
              <option value="Hanche droite">Hanche droite</option>
              <option value="Hanche gauche">Hanche gauche</option>
              <option value="Cuisse droite">Cuisse droite</option>
              <option value="Cuisse gauche">Cuisse gauche</option>
              <option value="Genou droit">Genou droit</option>
              <option value="Genou gauche">Genou gauche</option>
              <option value="Jambe droite">Jambe droite</option>
              <option value="Jambe gauche">Jambe gauche</option>
              <option value="Cheville droite">Cheville droite</option>
              <option value="Cheville gauche">Cheville gauche</option>
              <option value="Pied droit">Pied droit</option>
              <option value="Pied gauche">Pied gauche</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Sévérité *
            </label>
            <select 
              v-model="form.severity"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Sélectionner la sévérité</option>
              <option value="minor">Mineur</option>
              <option value="moderate">Modéré</option>
              <option value="severe">Sévère</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date de la Blessure *
            </label>
            <input 
              v-model="form.date"
              type="date"
              required
              :max="today"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>
        </div>

        <!-- ICD-11 Diagnostic -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Diagnostic ICD-11 *
          </label>
          <ICDSearchInput
            v-model="form.icd11_diagnostic"
            placeholder="Rechercher un diagnostic ICD-11..."
            @selected="onDiagnosticSelected"
          />
          <p class="mt-1 text-sm text-gray-500">
            Sélectionnez le diagnostic ICD-11 standardisé pour cette blessure
          </p>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Description de la Blessure *
          </label>
          <textarea 
            v-model="form.description"
            required
            rows="4"
            placeholder="Décrivez la blessure, les symptômes, le mécanisme..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
          ></textarea>
        </div>

        <!-- Recovery Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Jours de Récupération Estimés
            </label>
            <input 
              v-model="form.estimated_recovery_days"
              type="number"
              min="1"
              max="365"
              placeholder="Ex: 21"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date de Retour Prévue
            </label>
            <input 
              v-model="form.expected_return_date"
              type="date"
              :min="tomorrow"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Statut
            </label>
            <select 
              v-model="form.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="open">Ouverte</option>
              <option value="resolved">Résolue</option>
              <option value="recurring">Récurrente</option>
            </select>
          </div>
        </div>

        <!-- Treatment Plan -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Plan de Traitement (JSON)
          </label>
          <div class="space-y-3">
            <div class="flex space-x-2 mb-3">
              <button 
                type="button"
                @click="loadTreatmentTemplate('conservative')"
                class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors"
              >
                Template Conservateur
              </button>
              <button 
                type="button"
                @click="loadTreatmentTemplate('surgical')"
                class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors"
              >
                Template Chirurgical
              </button>
              <button 
                type="button"
                @click="loadTreatmentTemplate('rehabilitation')"
                class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors"
              >
                Template Rééducation
              </button>
            </div>

            <textarea 
              v-model="form.treatment_plan"
              rows="6"
              placeholder="Entrez le plan de traitement au format JSON..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 font-mono text-sm"
              :class="{ 'border-red-300': jsonError }"
            ></textarea>
            
            <div v-if="jsonError" class="text-red-600 text-sm">
              {{ jsonError }}
            </div>
            
            <div v-else-if="isValidTreatmentJson" class="text-green-600 text-sm">
              ✓ JSON valide
            </div>
          </div>
        </div>

        <!-- FIFA Compliance Data -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Données de Conformité FIFA (JSON)
          </label>
          <textarea 
            v-model="form.fifa_injury_data"
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
import ICDSearchInput from '../../../components/ICDSearchInput.vue'

export default {
  name: 'InjuryForm',
  components: {
    ICDSearchInput
  },
  props: {
    injury: {
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
        type: '',
        body_zone: '',
        severity: '',
        date: '',
        icd11_diagnostic: null,
        description: '',
        status: 'open',
        estimated_recovery_days: null,
        expected_return_date: '',
        actual_return_date: '',
        diagnosed_by: '',
        treatment_plan: '',
        rehabilitation_progress: '',
        fifa_injury_data: ''
      },
      loading: false,
      jsonError: null
    }
  },
  computed: {
    isEditing() {
      return !!this.injury
    },
    isValidTreatmentJson() {
      if (!this.form.treatment_plan) return false
      try {
        JSON.parse(this.form.treatment_plan)
        return true
      } catch {
        return false
      }
    },
    isFormValid() {
      return this.form.type && 
             this.form.body_zone && 
             this.form.severity &&
             this.form.date &&
             this.form.icd11_diagnostic &&
             this.form.description &&
             this.form.athlete_id
    },
    today() {
      return new Date().toISOString().split('T')[0]
    },
    tomorrow() {
      const tomorrow = new Date()
      tomorrow.setDate(tomorrow.getDate() + 1)
      return tomorrow.toISOString().split('T')[0]
    }
  },
  watch: {
    'form.treatment_plan'(newValue) {
      this.validateJson(newValue)
    }
  },
  mounted() {
    if (this.injury) {
      this.loadInjuryData()
    } else {
      this.form.date = this.today
    }
  },
  methods: {
    loadInjuryData() {
      this.form = {
        athlete_id: this.athleteId,
        type: this.injury.type || '',
        body_zone: this.injury.body_zone || '',
        severity: this.injury.severity || '',
        date: this.injury.date ? this.injury.date.split('T')[0] : this.today,
        icd11_diagnostic: this.injury.icd11_diagnostic || null,
        description: this.injury.description || '',
        status: this.injury.status || 'open',
        estimated_recovery_days: this.injury.estimated_recovery_days || null,
        expected_return_date: this.injury.expected_return_date ? this.injury.expected_return_date.split('T')[0] : '',
        actual_return_date: this.injury.actual_return_date ? this.injury.actual_return_date.split('T')[0] : '',
        diagnosed_by: this.injury.diagnosed_by || '',
        treatment_plan: this.injury.treatment_plan ? JSON.stringify(this.injury.treatment_plan, null, 2) : '',
        rehabilitation_progress: this.injury.rehabilitation_progress ? JSON.stringify(this.injury.rehabilitation_progress, null, 2) : '',
        fifa_injury_data: this.injury.fifa_injury_data ? JSON.stringify(this.injury.fifa_injury_data, null, 2) : ''
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

    loadTreatmentTemplate(type) {
      const templates = {
        conservative: {
          treatment_plan: JSON.stringify({
            phase: "conservative",
            initial_treatment: {
              rest: "Repos relatif 48-72h",
              ice: "Glace 20min toutes les 2h",
              compression: "Bandage élastique",
              elevation: "Surélévation"
            },
            medication: {
              anti_inflammatory: "Ibuprofène 400mg 3x/jour",
              pain_relief: "Paracétamol si nécessaire"
            },
            follow_up: "Contrôle à 7 jours",
            return_to_play: "Progressive selon tolérance"
          }, null, 2)
        },
        surgical: {
          treatment_plan: JSON.stringify({
            phase: "surgical",
            pre_operative: {
              imaging: "IRM/Scanner confirmé",
              consultation: "Chirurgien orthopédique"
            },
            surgical_procedure: {
              type: "À définir selon lésion",
              duration: "Variable selon complexité"
            },
            post_operative: {
              immobilization: "Attelle/Plâtre 6 semaines",
              weight_bearing: "Non portant 6 semaines"
            },
            rehabilitation: "Début rééducation post-opératoire"
          }, null, 2)
        },
        rehabilitation: {
          treatment_plan: JSON.stringify({
            phase: "rehabilitation",
            goals: {
              pain_control: "Gestion de la douleur",
              range_of_motion: "Récupération amplitude",
              strength: "Renforcement progressif",
              proprioception: "Récupération équilibre"
            },
            exercises: {
              week_1_2: "Exercices isométriques",
              week_3_4: "Renforcement progressif",
              week_5_6: "Exercices fonctionnels",
              week_7_8: "Retour au sport"
            },
            progression: "Selon tolérance et objectifs"
          }, null, 2)
        }
      }

      const template = templates[type]
      if (template) {
        this.form.treatment_plan = template.treatment_plan
      }
    },

    onDiagnosticSelected(diagnostic) {
      if (diagnostic) {
        // Update the injury type based on the ICD-11 diagnostic
        this.form.type = diagnostic.label
        console.log('ICD-11 diagnostic selected:', diagnostic)
      }
    },

    async saveInjury() {
      if (!this.isFormValid) {
        this.$toast.error('Veuillez corriger les erreurs dans le formulaire')
        return
      }

      this.loading = true
      try {
        const data = {
          ...this.form,
          treatment_plan: this.form.treatment_plan ? JSON.parse(this.form.treatment_plan) : null,
          rehabilitation_progress: this.form.rehabilitation_progress ? JSON.parse(this.form.rehabilitation_progress) : null,
          fifa_injury_data: this.form.fifa_injury_data ? JSON.parse(this.form.fifa_injury_data) : null
        }

        if (this.isEditing) {
          await this.$http.put(`/api/v1/injuries/${this.injury.id}`, data)
          this.$toast.success('Blessure mise à jour avec succès')
        } else {
          await this.$http.post('/api/v1/injuries', data)
          this.$toast.success('Blessure enregistrée avec succès')
        }
        
        this.$emit('saved')
        
      } catch (error) {
        console.error('Error saving injury:', error)
        
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
          status: 'open',
          treatment_plan: this.form.treatment_plan ? JSON.parse(this.form.treatment_plan) : {},
          rehabilitation_progress: this.form.rehabilitation_progress ? JSON.parse(this.form.rehabilitation_progress) : null,
          fifa_injury_data: this.form.fifa_injury_data ? JSON.parse(this.form.fifa_injury_data) : null
        }

        await this.$http.post('/api/v1/injuries', data)
        
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
.injury-form {
  @apply max-w-4xl mx-auto;
}
</style> 