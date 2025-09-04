<template>
  <div class="cardiovascular-investigations-tab">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">❤️ Investigations Cardiovasculaires</h2>
      <p class="text-gray-600">Tests et examens cardiovasculaires spécialisés</p>
    </div>

    <form @submit.prevent="validateForm" class="space-y-8">
      <!-- ECG Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Électrocardiogramme (ECG)</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.ecg.performed"
              type="checkbox"
              id="ecg_performed"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="ecg_performed" class="text-sm font-medium text-gray-700">
              ECG réalisé
            </label>
          </div>
          
          <div v-if="formData.ecg.performed" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de l'ECG
              </label>
              <input
                v-model="formData.ecg.date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Résultat
              </label>
              <select
                v-model="formData.ecg.result"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Sélectionner</option>
                <option value="normal">Normal</option>
                <option value="abnormal">Anormal</option>
                <option value="borderline">Limite</option>
                <option value="inconclusive">Non concluant</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Anomalies détectées
              </label>
              <textarea
                v-model="formData.ecg.abnormalities"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les anomalies détectées..."
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Interprétation
              </label>
              <textarea
                v-model="formData.ecg.interpretation"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Interprétation de l'ECG..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Echocardiogram Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Échocardiographie</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.echocardiogram.performed"
              type="checkbox"
              id="echo_performed"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="echo_performed" class="text-sm font-medium text-gray-700">
              Échocardiographie réalisée
            </label>
          </div>
          
          <div v-if="formData.echocardiogram.performed" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de l'échocardiographie
              </label>
              <input
                v-model="formData.echocardiogram.date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Résultat
              </label>
              <select
                v-model="formData.echocardiogram.result"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Sélectionner</option>
                <option value="normal">Normal</option>
                <option value="abnormal">Anormal</option>
                <option value="borderline">Limite</option>
                <option value="inconclusive">Non concluant</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Anomalies détectées
              </label>
              <textarea
                v-model="formData.echocardiogram.abnormalities"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les anomalies détectées..."
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Interprétation
              </label>
              <textarea
                v-model="formData.echocardiogram.interpretation"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Interprétation de l'échocardiographie..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Stress Test Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Test d'Effort</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.stress_test.performed"
              type="checkbox"
              id="stress_test_performed"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="stress_test_performed" class="text-sm font-medium text-gray-700">
              Test d'effort réalisé
            </label>
          </div>
          
          <div v-if="formData.stress_test.performed" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date du test d'effort
              </label>
              <input
                v-model="formData.stress_test.date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Résultat
              </label>
              <select
                v-model="formData.stress_test.result"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Sélectionner</option>
                <option value="normal">Normal</option>
                <option value="abnormal">Anormal</option>
                <option value="borderline">Limite</option>
                <option value="inconclusive">Non concluant</option>
                <option value="positive">Positif</option>
                <option value="negative">Négatif</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Anomalies détectées
              </label>
              <textarea
                v-model="formData.stress_test.abnormalities"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Décrivez les anomalies détectées..."
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Interprétation
              </label>
              <textarea
                v-model="formData.stress_test.interpretation"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Interprétation du test d'effort..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Other Tests Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Autres Tests Cardiovasculaires</h3>
        
        <div class="space-y-4">
          <div v-for="(test, index) in formData.other_tests" :key="index" class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-md font-medium text-gray-900">Test {{ index + 1 }}</h4>
              <button
                @click="removeTest(index)"
                type="button"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Supprimer
              </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Nom du test
                </label>
                <input
                  v-model="test.test_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ex: Holter, Tilt test, etc."
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Date
                </label>
                <input
                  v-model="test.date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              
              <div class="flex items-center space-x-3">
                <input
                  v-model="test.performed"
                  type="checkbox"
                  :id="`test_performed_${index}`"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="`test_performed_${index}`" class="text-sm font-medium text-gray-700">
                  Test réalisé
                </label>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Résultat
                </label>
                <select
                  v-model="test.result"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Sélectionner</option>
                  <option value="normal">Normal</option>
                  <option value="abnormal">Anormal</option>
                  <option value="borderline">Limite</option>
                  <option value="inconclusive">Non concluant</option>
                </select>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Anomalies détectées
                </label>
                <textarea
                  v-model="test.abnormalities"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Décrivez les anomalies détectées..."
                ></textarea>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Interprétation
                </label>
                <textarea
                  v-model="test.interpretation"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Interprétation du test..."
                ></textarea>
              </div>
            </div>
          </div>
          
          <button
            @click="addTest"
            type="button"
            class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors"
          >
            + Ajouter un autre test
          </button>
        </div>
      </div>

      <!-- Summary Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé des Investigations</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tests réalisés
            </label>
            <div class="space-y-2">
              <div v-if="formData.ecg.performed" class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-sm text-gray-700">ECG - {{ formData.ecg.result || 'Résultat non spécifié' }}</span>
              </div>
              <div v-if="formData.echocardiogram.performed" class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-sm text-gray-700">Échocardiographie - {{ formData.echocardiogram.result || 'Résultat non spécifié' }}</span>
              </div>
              <div v-if="formData.stress_test.performed" class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-sm text-gray-700">Test d'effort - {{ formData.stress_test.result || 'Résultat non spécifié' }}</span>
              </div>
              <div v-for="(test, index) in formData.other_tests" :key="index" v-if="test.performed" class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-sm text-gray-700">{{ test.test_name || 'Test non nommé' }} - {{ test.result || 'Résultat non spécifié' }}</span>
              </div>
              <div v-if="!hasAnyTests" class="text-sm text-gray-500">
                Aucun test réalisé
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Recommandations basées sur les résultats
            </label>
            <textarea
              v-model="formData.recommendations"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Recommandations basées sur les résultats des tests..."
            ></textarea>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'CardiovascularInvestigationsTab',
  props: {
    value: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formData: {
        ecg: {
          performed: false,
          date: '',
          result: '',
          abnormalities: '',
          interpretation: ''
        },
        echocardiogram: {
          performed: false,
          date: '',
          result: '',
          abnormalities: '',
          interpretation: ''
        },
        stress_test: {
          performed: false,
          date: '',
          result: '',
          abnormalities: '',
          interpretation: ''
        },
        other_tests: [],
        recommendations: ''
      }
    }
  },
  computed: {
    hasAnyTests() {
      return this.formData.ecg.performed || 
             this.formData.echocardiogram.performed || 
             this.formData.stress_test.performed ||
             this.formData.other_tests.some(test => test.performed)
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
    addTest() {
      this.formData.other_tests.push({
        test_name: '',
        performed: false,
        date: '',
        result: '',
        abnormalities: '',
        interpretation: ''
      })
    },
    removeTest(index) {
      this.formData.other_tests.splice(index, 1)
    },
    validateForm() {
      // Basic validation - at least one test should be performed
      const hasTests = this.hasAnyTests
      this.$emit('update:valid', hasTests)
    }
  }
}
</script> 