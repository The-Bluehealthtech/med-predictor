<template>
  <div class="physical-examination-tab">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">🏥 Examen Physique</h2>
      <p class="text-gray-600">Enregistrement du médecin - Examen physique complet avec diagrammes anatomiques interactifs</p>
    </div>

    <form @submit.prevent="validateForm" class="space-y-8">
      <!-- General Appearance Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Apparence Générale</h3>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Apparence générale
          </label>
          <textarea
            v-model="formData.general_appearance"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Décrivez l'apparence générale du patient..."
          ></textarea>
        </div>
      </div>

      <!-- Vital Signs Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Signes Vitaux</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tension artérielle systolique (mmHg)
            </label>
            <input
              v-model.number="formData.vital_signs.blood_pressure_systolic"
              type="number"
              min="0"
              max="300"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="120"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tension artérielle diastolique (mmHg)
            </label>
            <input
              v-model.number="formData.vital_signs.blood_pressure_diastolic"
              type="number"
              min="0"
              max="200"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="80"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fréquence cardiaque (bpm)
            </label>
            <input
              v-model.number="formData.vital_signs.heart_rate"
              type="number"
              min="0"
              max="300"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="72"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Température (°C)
            </label>
            <input
              v-model.number="formData.vital_signs.temperature"
              type="number"
              step="0.1"
              min="30"
              max="45"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="37.0"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Poids (kg)
            </label>
            <input
              v-model.number="formData.vital_signs.weight"
              type="number"
              step="0.1"
              min="0"
              max="300"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="70.0"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Taille (cm)
            </label>
            <input
              v-model.number="formData.vital_signs.height"
              type="number"
              step="0.1"
              min="0"
              max="300"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="175.0"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              IMC
            </label>
            <input
              v-model.number="formData.vital_signs.bmi"
              type="number"
              step="0.1"
              min="0"
              max="100"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="22.9"
              readonly
            />
            <p class="text-xs text-gray-500 mt-1">Calculé automatiquement</p>
          </div>
        </div>
      </div>

      <!-- Cardiovascular Examination Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Examen Cardiovasculaire</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Sons cardiaques
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.heart_sounds"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez les sons cardiaques..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pouls
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.pulse"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez le pouls..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tension artérielle
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.blood_pressure"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la tension artérielle..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Temps de recoloration capillaire
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.capillary_refill"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez le temps de recoloration capillaire..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pression veineuse jugulaire
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.jugular_venous_pressure"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la pression veineuse jugulaire..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Œdème périphérique
            </label>
            <textarea
              v-model="formData.cardiovascular_examination.peripheral_edema"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez l'œdème périphérique..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Respiratory Examination Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Examen Respiratoire</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mode respiratoire
            </label>
            <textarea
              v-model="formData.respiratory_examination.breathing_pattern"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez le mode respiratoire..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Sons pulmonaires
            </label>
            <textarea
              v-model="formData.respiratory_examination.lung_sounds"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez les sons pulmonaires..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mouvement thoracique
            </label>
            <textarea
              v-model="formData.respiratory_examination.chest_movement"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez le mouvement thoracique..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Musculoskeletal Examination Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Examen Musculo-squelettique</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mobilité générale
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.general_mobility"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la mobilité générale..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Amplitude articulaire
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.joint_range_of_motion"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez l'amplitude articulaire..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Force musculaire
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.muscle_strength"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la force musculaire..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Coordination
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.coordination"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la coordination..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Équilibre
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.balance"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez l'équilibre..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Démarche
            </label>
            <textarea
              v-model="formData.musculoskeletal_examination.gait"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la démarche..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Interactive Anatomical Diagrams Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Diagrammes Anatomiques Interactifs</h3>
        <p class="text-gray-600 mb-6">Cliquez sur les diagrammes pour ajouter des annotations sur les zones d'anomalie</p>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Anterior View -->
          <div>
            <h4 class="text-md font-semibold text-gray-900 mb-4">Vue Antérieure</h4>
            <div class="relative border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50">
              <svg
                @click="addAnnotation('anterior', $event)"
                class="w-full h-96 cursor-crosshair"
                viewBox="0 0 300 600"
                xmlns="http://www.w3.org/2000/svg"
              >
                <!-- Human body outline - anterior view -->
                <path
                  d="M150 50 C 120 50, 100 80, 100 120 L 100 200 C 100 220, 120 240, 150 240 L 200 240 C 230 240, 250 220, 250 200 L 250 120 C 250 80, 230 50, 200 50 Z"
                  fill="none"
                  stroke="#374151"
                  stroke-width="2"
                />
                
                <!-- Head -->
                <circle cx="150" cy="80" r="25" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Arms -->
                <path d="M 100 120 L 80 180 L 70 200" fill="none" stroke="#374151" stroke-width="2"/>
                <path d="M 200 120 L 220 180 L 230 200" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Legs -->
                <path d="M 120 240 L 110 350 L 100 400" fill="none" stroke="#374151" stroke-width="2"/>
                <path d="M 180 240 L 190 350 L 200 400" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Annotations -->
                <circle
                  v-for="annotation in anteriorAnnotations"
                  :key="annotation.id"
                  :cx="annotation.x"
                  :cy="annotation.y"
                  r="8"
                  fill="#ef4444"
                  stroke="#dc2626"
                  stroke-width="2"
                  @click.stop="removeAnnotation('anterior', annotation.id)"
                  class="cursor-pointer hover:r-10 transition-all"
                />
              </svg>
              
              <!-- Annotations List -->
              <div class="p-4 bg-white border-t border-gray-200">
                <h5 class="text-sm font-medium text-gray-900 mb-2">Annotations Antérieures</h5>
                <div v-if="anteriorAnnotations.length === 0" class="text-sm text-gray-500">
                  Aucune annotation
                </div>
                <div v-else class="space-y-2">
                  <div
                    v-for="annotation in anteriorAnnotations"
                    :key="annotation.id"
                    class="flex items-center justify-between p-2 bg-red-50 rounded"
                  >
                    <span class="text-sm text-gray-700">{{ annotation.note }}</span>
                    <button
                      @click="removeAnnotation('anterior', annotation.id)"
                      class="text-red-600 hover:text-red-800 text-sm"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Posterior View -->
          <div>
            <h4 class="text-md font-semibold text-gray-900 mb-4">Vue Postérieure</h4>
            <div class="relative border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50">
              <svg
                @click="addAnnotation('posterior', $event)"
                class="w-full h-96 cursor-crosshair"
                viewBox="0 0 300 600"
                xmlns="http://www.w3.org/2000/svg"
              >
                <!-- Human body outline - posterior view -->
                <path
                  d="M150 50 C 120 50, 100 80, 100 120 L 100 200 C 100 220, 120 240, 150 240 L 200 240 C 230 240, 250 220, 250 200 L 250 120 C 250 80, 230 50, 200 50 Z"
                  fill="none"
                  stroke="#374151"
                  stroke-width="2"
                />
                
                <!-- Head -->
                <circle cx="150" cy="80" r="25" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Arms -->
                <path d="M 100 120 L 80 180 L 70 200" fill="none" stroke="#374151" stroke-width="2"/>
                <path d="M 200 120 L 220 180 L 230 200" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Legs -->
                <path d="M 120 240 L 110 350 L 100 400" fill="none" stroke="#374151" stroke-width="2"/>
                <path d="M 180 240 L 190 350 L 200 400" fill="none" stroke="#374151" stroke-width="2"/>
                
                <!-- Spine -->
                <path d="M 150 120 L 150 240" fill="none" stroke="#374151" stroke-width="3" stroke-dasharray="5,5"/>
                
                <!-- Annotations -->
                <circle
                  v-for="annotation in posteriorAnnotations"
                  :key="annotation.id"
                  :cx="annotation.x"
                  :cy="annotation.y"
                  r="8"
                  fill="#ef4444"
                  stroke="#dc2626"
                  stroke-width="2"
                  @click.stop="removeAnnotation('posterior', annotation.id)"
                  class="cursor-pointer hover:r-10 transition-all"
                />
              </svg>
              
              <!-- Annotations List -->
              <div class="p-4 bg-white border-t border-gray-200">
                <h5 class="text-sm font-medium text-gray-900 mb-2">Annotations Postérieures</h5>
                <div v-if="posteriorAnnotations.length === 0" class="text-sm text-gray-500">
                  Aucune annotation
                </div>
                <div v-else class="space-y-2">
                  <div
                    v-for="annotation in posteriorAnnotations"
                    :key="annotation.id"
                    class="flex items-center justify-between p-2 bg-red-50 rounded"
                  >
                    <span class="text-sm text-gray-700">{{ annotation.note }}</span>
                    <button
                      @click="removeAnnotation('posterior', annotation.id)"
                      class="text-red-600 hover:text-red-800 text-sm"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Neurological Examination Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Examen Neurologique</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              État mental
            </label>
            <textarea
              v-model="formData.neurological_examination.mental_status"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez l'état mental..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nerfs crâniens
            </label>
            <textarea
              v-model="formData.neurological_examination.cranial_nerves"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez l'examen des nerfs crâniens..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fonction motrice
            </label>
            <textarea
              v-model="formData.neurological_examination.motor_function"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la fonction motrice..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fonction sensitive
            </label>
            <textarea
              v-model="formData.neurological_examination.sensory_function"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la fonction sensitive..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Réflexes
            </label>
            <textarea
              v-model="formData.neurological_examination.reflexes"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez les réflexes..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Coordination
            </label>
            <textarea
              v-model="formData.neurological_examination.coordination"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Décrivez la coordination..."
            ></textarea>
          </div>
        </div>
      </div>
    </form>

    <!-- Annotation Modal -->
    <div v-if="showAnnotationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter une annotation</h3>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Description de l'anomalie
          </label>
          <textarea
            v-model="newAnnotationNote"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Décrivez l'anomalie observée..."
          ></textarea>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button
            @click="cancelAnnotation"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
          >
            Annuler
          </button>
          <button
            @click="confirmAnnotation"
            :disabled="!newAnnotationNote.trim()"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Ajouter
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PhysicalExaminationTab',
  props: {
    value: {
      type: Object,
      default: () => ({})
    },
    anatomicalAnnotations: {
      type: Object,
      default: () => ({
        anterior: [],
        posterior: []
      })
    }
  },
  data() {
    return {
      formData: {
        general_appearance: '',
        vital_signs: {
          blood_pressure_systolic: null,
          blood_pressure_diastolic: null,
          heart_rate: null,
          temperature: null,
          weight: null,
          height: null,
          bmi: null
        },
        cardiovascular_examination: {
          heart_sounds: '',
          pulse: '',
          blood_pressure: '',
          capillary_refill: '',
          jugular_venous_pressure: '',
          peripheral_edema: ''
        },
        respiratory_examination: {
          breathing_pattern: '',
          lung_sounds: '',
          chest_movement: ''
        },
        musculoskeletal_examination: {
          general_mobility: '',
          joint_range_of_motion: '',
          muscle_strength: '',
          coordination: '',
          balance: '',
          gait: ''
        },
        neurological_examination: {
          mental_status: '',
          cranial_nerves: '',
          motor_function: '',
          sensory_function: '',
          reflexes: '',
          coordination: ''
        }
      },
      showAnnotationModal: false,
      newAnnotationNote: '',
      pendingAnnotation: null
    }
  },
  computed: {
    anteriorAnnotations() {
      return this.anatomicalAnnotations.anterior || []
    },
    posteriorAnnotations() {
      return this.anatomicalAnnotations.posterior || []
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
    },
    'formData.vital_signs.weight'(newVal) {
      this.calculateBMI()
    },
    'formData.vital_signs.height'(newVal) {
      this.calculateBMI()
    }
  },
  methods: {
    calculateBMI() {
      const weight = this.formData.vital_signs.weight
      const height = this.formData.vital_signs.height
      
      if (weight && height && height > 0) {
        const heightInMeters = height / 100
        this.formData.vital_signs.bmi = Math.round((weight / (heightInMeters * heightInMeters)) * 10) / 10
      }
    },
    addAnnotation(view, event) {
      const rect = event.target.getBoundingClientRect()
      const x = event.clientX - rect.left
      const y = event.clientY - rect.top
      
      this.pendingAnnotation = {
        view,
        x: Math.round(x),
        y: Math.round(y)
      }
      
      this.showAnnotationModal = true
    },
    confirmAnnotation() {
      if (this.pendingAnnotation && this.newAnnotationNote.trim()) {
        const annotation = {
          id: Date.now().toString(),
          x: this.pendingAnnotation.x,
          y: this.pendingAnnotation.y,
          note: this.newAnnotationNote.trim(),
          created_at: new Date().toISOString()
        }
        
        const updatedAnnotations = { ...this.anatomicalAnnotations }
        if (!updatedAnnotations[this.pendingAnnotation.view]) {
          updatedAnnotations[this.pendingAnnotation.view] = []
        }
        updatedAnnotations[this.pendingAnnotation.view].push(annotation)
        
        this.$emit('update:annotations', updatedAnnotations)
        
        this.cancelAnnotation()
      }
    },
    cancelAnnotation() {
      this.showAnnotationModal = false
      this.newAnnotationNote = ''
      this.pendingAnnotation = null
    },
    removeAnnotation(view, annotationId) {
      const updatedAnnotations = { ...this.anatomicalAnnotations }
      if (updatedAnnotations[view]) {
        updatedAnnotations[view] = updatedAnnotations[view].filter(
          annotation => annotation.id !== annotationId
        )
        this.$emit('update:annotations', updatedAnnotations)
      }
    },
    validateForm() {
      // Basic validation - at least some data should be entered
      const hasData = Object.values(this.formData).some(value => {
        if (typeof value === 'string') {
          return value.trim() !== ''
        }
        if (typeof value === 'object' && value !== null) {
          return Object.values(value).some(v => v !== '' && v !== null)
        }
        return value !== null
      })
      
      this.$emit('update:valid', hasData)
    }
  }
}
</script> 