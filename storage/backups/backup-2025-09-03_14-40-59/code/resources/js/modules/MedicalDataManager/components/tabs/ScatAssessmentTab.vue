<template>
  <div class="scat-assessment-tab">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">🧠 Évaluation SCAT 5</h2>
      <p class="text-gray-600">Évaluation des commotions cérébrales selon le protocole SCAT 5</p>
    </div>

    <form @submit.prevent="validateForm" class="space-y-8">
      <!-- SCAT Assessment Overview -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Vue d'ensemble de l'évaluation SCAT</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.performed"
              type="checkbox"
              id="scat_performed"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="scat_performed" class="text-sm font-medium text-gray-700">
              Évaluation SCAT 5 réalisée
            </label>
          </div>
          
          <div v-if="formData.performed" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de l'évaluation SCAT
              </label>
              <input
                v-model="formData.date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Symptom Severity Score -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Score de Sévérité des Symptômes</h3>
        <p class="text-gray-600 mb-4">Évaluez la sévérité de chaque symptôme sur une échelle de 0 à 6</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="symptom in symptoms" :key="symptom.key" class="border border-gray-200 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ symptom.name }}
            </label>
            <select
              v-model="formData.symptom_scores[symptom.key]"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Sélectionner</option>
              <option value="0">0 - Aucun</option>
              <option value="1">1 - Léger</option>
              <option value="2">2 - Modéré</option>
              <option value="3">3 - Modéré à sévère</option>
              <option value="4">4 - Sévère</option>
              <option value="5">5 - Très sévère</option>
              <option value="6">6 - Extrêmement sévère</option>
            </select>
          </div>
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">Score total de sévérité des symptômes:</span>
            <span class="text-lg font-bold text-blue-600">{{ symptomSeverityScore }}/132</span>
          </div>
        </div>
      </div>

      <!-- Cognitive Assessment -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation Cognitive</h3>
        
        <div class="space-y-6">
          <!-- Orientation -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Orientation (5 points)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quel jour de la semaine sommes-nous?
                </label>
                <select
                  v-model="formData.cognitive_assessment.orientation.day"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quel mois sommes-nous?
                </label>
                <select
                  v-model="formData.cognitive_assessment.orientation.month"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quel jour du mois sommes-nous?
                </label>
                <select
                  v-model="formData.cognitive_assessment.orientation.date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quelle année sommes-nous?
                </label>
                <select
                  v-model="formData.cognitive_assessment.orientation.year"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Quelle heure est-il?
                </label>
                <select
                  v-model="formData.cognitive_assessment.orientation.time"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
            </div>
            
            <div class="mt-3 p-3 bg-gray-50 rounded">
              <span class="text-sm font-medium text-gray-700">Score d'orientation: {{ orientationScore }}/5</span>
            </div>
          </div>

          <!-- Immediate Memory -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Mémoire Immédiate (15 points)</h4>
            <p class="text-sm text-gray-600 mb-3">Lisez la liste de mots suivante: "ÉLÉPHANT, PERSIL, BALLON, RÉFRIGÉRATEUR, MOULIN"</p>
            
            <div class="space-y-3">
              <div v-for="(word, index) in memoryWords" :key="index" class="flex items-center space-x-3">
                <input
                  v-model="formData.cognitive_assessment.immediate_memory[word]"
                  type="checkbox"
                  :id="`immediate_${word}`"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="`immediate_${word}`" class="text-sm font-medium text-gray-700">
                  {{ word }}
                </label>
              </div>
            </div>
            
            <div class="mt-3 p-3 bg-gray-50 rounded">
              <span class="text-sm font-medium text-gray-700">Score mémoire immédiate: {{ immediateMemoryScore }}/15</span>
            </div>
          </div>

          <!-- Concentration -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Concentration (5 points)</h4>
            <p class="text-sm text-gray-600 mb-3">Répétez les chiffres dans l'ordre inverse</p>
            
            <div class="space-y-3">
              <div v-for="(sequence, index) in concentrationSequences" :key="index">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Séquence {{ index + 1 }}: {{ sequence }}
                </label>
                <select
                  v-model="formData.cognitive_assessment.concentration[`sequence_${index + 1}`]"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="1">Correct (1 point)</option>
                  <option value="0">Incorrect (0 point)</option>
                </select>
              </div>
            </div>
            
            <div class="mt-3 p-3 bg-gray-50 rounded">
              <span class="text-sm font-medium text-gray-700">Score de concentration: {{ concentrationScore }}/5</span>
            </div>
          </div>

          <!-- Delayed Recall -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Rappel Différé (5 points)</h4>
            <p class="text-sm text-gray-600 mb-3">Rappelez-vous les mots de la liste précédente</p>
            
            <div class="space-y-3">
              <div v-for="(word, index) in memoryWords" :key="index" class="flex items-center space-x-3">
                <input
                  v-model="formData.cognitive_assessment.delayed_recall[word]"
                  type="checkbox"
                  :id="`delayed_${word}`"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="`delayed_${word}`" class="text-sm font-medium text-gray-700">
                  {{ word }}
                </label>
              </div>
            </div>
            
            <div class="mt-3 p-3 bg-gray-50 rounded">
              <span class="text-sm font-medium text-gray-700">Score rappel différé: {{ delayedRecallScore }}/5</span>
            </div>
          </div>
        </div>
        
        <div class="mt-6 p-4 bg-green-50 rounded-lg">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">Score cognitif total:</span>
            <span class="text-lg font-bold text-green-600">{{ cognitiveScore }}/30</span>
          </div>
        </div>
      </div>

      <!-- Neck Pain Assessment -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation de la Douleur Cervicale</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Avez-vous des douleurs au cou?
            </label>
            <select
              v-model="formData.neck_pain_assessment.has_pain"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Sélectionner</option>
              <option value="yes">Oui</option>
              <option value="no">Non</option>
            </select>
          </div>
          
          <div v-if="formData.neck_pain_assessment.has_pain === 'yes'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Intensité de la douleur (0-10)
            </label>
            <input
              v-model.number="formData.neck_pain_assessment.pain_intensity"
              type="number"
              min="0"
              max="10"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="0-10"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Description de la douleur
            </label>
            <textarea
              v-model="formData.neck_pain_assessment.pain_description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la nature et la localisation de la douleur..."
            ></textarea>
          </div>
        </div>
        
        <div class="mt-4 p-3 bg-yellow-50 rounded">
          <span class="text-sm font-medium text-gray-700">Score douleur cervicale: {{ neckPainScore }}/10</span>
        </div>
      </div>

      <!-- Balance Assessment -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation de l'Équilibre</h3>
        
        <div class="space-y-6">
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Test de l'équilibre sur une jambe</h4>
            <p class="text-sm text-gray-600 mb-3">Le patient doit tenir l'équilibre sur une jambe pendant 20 secondes</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Jambe droite (secondes)
                </label>
                <input
                  v-model.number="formData.balance_assessment.right_leg"
                  type="number"
                  min="0"
                  max="20"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="0-20"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Jambe gauche (secondes)
                </label>
                <input
                  v-model.number="formData.balance_assessment.left_leg"
                  type="number"
                  min="0"
                  max="20"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="0-20"
                />
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Observations de l'équilibre
            </label>
            <textarea
              v-model="formData.balance_assessment.observations"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez les observations de l'équilibre..."
            ></textarea>
          </div>
        </div>
        
        <div class="mt-4 p-3 bg-blue-50 rounded">
          <span class="text-sm font-medium text-gray-700">Score d'équilibre: {{ balanceScore }}/30</span>
        </div>
      </div>

      <!-- Coordination Assessment -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation de la Coordination</h3>
        
        <div class="space-y-4">
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Test doigt-nez</h4>
            <p class="text-sm text-gray-600 mb-3">Le patient doit toucher son nez avec son index alternativement avec chaque main</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Main droite (1-5)
                </label>
                <select
                  v-model="formData.coordination_assessment.finger_nose.right"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="5">5 - Normal</option>
                  <option value="4">4 - Légèrement anormal</option>
                  <option value="3">3 - Modérément anormal</option>
                  <option value="2">2 - Sévèrement anormal</option>
                  <option value="1">1 - Très sévèrement anormal</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Main gauche (1-5)
                </label>
                <select
                  v-model="formData.coordination_assessment.finger_nose.left"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="5">5 - Normal</option>
                  <option value="4">4 - Légèrement anormal</option>
                  <option value="3">3 - Modérément anormal</option>
                  <option value="2">2 - Sévèrement anormal</option>
                  <option value="1">1 - Très sévèrement anormal</option>
                </select>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Observations de la coordination
            </label>
            <textarea
              v-model="formData.coordination_assessment.observations"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez les observations de la coordination..."
            ></textarea>
          </div>
        </div>
        
        <div class="mt-4 p-3 bg-purple-50 rounded">
          <span class="text-sm font-medium text-gray-700">Score de coordination: {{ coordinationScore }}/10</span>
        </div>
      </div>

      <!-- SCAT Summary -->
      <div v-if="formData.performed" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé SCAT 5</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="p-4 bg-blue-50 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">{{ symptomSeverityScore }}</div>
            <div class="text-sm text-gray-600">Symptômes</div>
          </div>
          
          <div class="p-4 bg-green-50 rounded-lg">
            <div class="text-2xl font-bold text-green-600">{{ cognitiveScore }}</div>
            <div class="text-sm text-gray-600">Cognitif</div>
          </div>
          
          <div class="p-4 bg-yellow-50 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">{{ neckPainScore }}</div>
            <div class="text-sm text-gray-600">Douleur cervicale</div>
          </div>
          
          <div class="p-4 bg-purple-50 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">{{ totalScore }}</div>
            <div class="text-sm text-gray-600">Total</div>
          </div>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Interprétation
            </label>
            <textarea
              v-model="formData.interpretation"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Interprétation des résultats SCAT..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Recommandations
            </label>
            <textarea
              v-model="formData.recommendations"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Recommandations basées sur l'évaluation SCAT..."
            ></textarea>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'ScatAssessmentTab',
  props: {
    value: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formData: {
        performed: false,
        date: '',
        symptom_scores: {},
        cognitive_assessment: {
          orientation: {
            day: '',
            month: '',
            date: '',
            year: '',
            time: ''
          },
          immediate_memory: {},
          concentration: {},
          delayed_recall: {}
        },
        neck_pain_assessment: {
          has_pain: '',
          pain_intensity: null,
          pain_description: ''
        },
        balance_assessment: {
          right_leg: null,
          left_leg: null,
          observations: ''
        },
        coordination_assessment: {
          finger_nose: {
            right: '',
            left: ''
          },
          observations: ''
        },
        interpretation: '',
        recommendations: ''
      },
      symptoms: [
        { key: 'headache', name: 'Mal de tête' },
        { key: 'pressure_in_head', name: 'Pression dans la tête' },
        { key: 'neck_pain', name: 'Douleur au cou' },
        { key: 'nausea_vomiting', name: 'Nausée/vomissements' },
        { key: 'dizziness', name: 'Vertiges' },
        { key: 'blurred_vision', name: 'Vision floue' },
        { key: 'balance_problems', name: 'Problèmes d\'équilibre' },
        { key: 'sensitivity_to_light', name: 'Sensibilité à la lumière' },
        { key: 'sensitivity_to_noise', name: 'Sensibilité au bruit' },
        { key: 'feeling_slowed_down', name: 'Sensation de ralentissement' },
        { key: 'feeling_in_fog', name: 'Sensation de brouillard' },
        { key: 'dont_feel_right', name: 'Ne se sent pas bien' },
        { key: 'difficulty_concentrating', name: 'Difficulté de concentration' },
        { key: 'difficulty_remembering', name: 'Difficulté de mémorisation' },
        { key: 'fatigue_low_energy', name: 'Fatigue/faible énergie' },
        { key: 'confusion', name: 'Confusion' },
        { key: 'drowsiness', name: 'Somnolence' },
        { key: 'trouble_falling_asleep', name: 'Difficulté à s\'endormir' },
        { key: 'more_emotional', name: 'Plus émotif' },
        { key: 'irritability', name: 'Irritabilité' },
        { key: 'sadness', name: 'Tristesse' },
        { key: 'nervous_anxious', name: 'Nervosité/anxiété' }
      ],
      memoryWords: ['ÉLÉPHANT', 'PERSIL', 'BALLON', 'RÉFRIGÉRATEUR', 'MOULIN'],
      concentrationSequences: ['6-2-9', '4-1-5']
    }
  },
  computed: {
    symptomSeverityScore() {
      return Object.values(this.formData.symptom_scores).reduce((sum, score) => {
        return sum + (parseInt(score) || 0)
      }, 0)
    },
    orientationScore() {
      const scores = Object.values(this.formData.cognitive_assessment.orientation)
      return scores.reduce((sum, score) => sum + (parseInt(score) || 0), 0)
    },
    immediateMemoryScore() {
      const scores = Object.values(this.formData.cognitive_assessment.immediate_memory)
      return scores.reduce((sum, score) => sum + (parseInt(score) || 0), 0)
    },
    concentrationScore() {
      const scores = Object.values(this.formData.cognitive_assessment.concentration)
      return scores.reduce((sum, score) => sum + (parseInt(score) || 0), 0)
    },
    delayedRecallScore() {
      const scores = Object.values(this.formData.cognitive_assessment.delayed_recall)
      return scores.reduce((sum, score) => sum + (parseInt(score) || 0), 0)
    },
    cognitiveScore() {
      return this.orientationScore + this.immediateMemoryScore + this.concentrationScore + this.delayedRecallScore
    },
    neckPainScore() {
      if (this.formData.neck_pain_assessment.has_pain === 'yes') {
        return this.formData.neck_pain_assessment.pain_intensity || 0
      }
      return 0
    },
    balanceScore() {
      const rightLeg = this.formData.balance_assessment.right_leg || 0
      const leftLeg = this.formData.balance_assessment.left_leg || 0
      return Math.min(rightLeg + leftLeg, 30)
    },
    coordinationScore() {
      const right = parseInt(this.formData.coordination_assessment.finger_nose.right) || 0
      const left = parseInt(this.formData.coordination_assessment.finger_nose.left) || 0
      return right + left
    },
    totalScore() {
      return this.symptomSeverityScore + this.cognitiveScore + this.neckPainScore + this.balanceScore + this.coordinationScore
    }
  },
  watch: {
    value: {
      handler(newVal) {
        if (newVal && Object.keys(newVal).length > 0) {
          this.formData = { ...this.formData, ...newVal }
        }
      },
      immediate: true,
      deep: true
    },
    formData: {
      handler(newVal) {
        this.$emit('input', newVal)
        this.validateForm()
      },
      deep: true
    }
  },
  methods: {
    validateForm() {
      const isValid = this.formData.performed
      this.$emit('update:valid', isValid)
    }
  }
}
</script> 