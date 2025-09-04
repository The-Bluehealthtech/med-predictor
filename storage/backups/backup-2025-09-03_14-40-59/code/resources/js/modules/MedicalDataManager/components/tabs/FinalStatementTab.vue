<template>
  <div class="final-statement-tab">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">✅ Déclaration Finale</h2>
      <p class="text-gray-600">Résumé de l'évaluation et décision finale de clearance</p>
    </div>

    <form @submit.prevent="validateForm" class="space-y-8">
      <!-- Assessment Summary Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé de l'Évaluation</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Résumé de l'évaluation médicale
            </label>
            <textarea
              v-model="formData.assessment_summary"
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Résumez les principales découvertes de l'évaluation médicale complète..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Recommandations médicales
            </label>
            <textarea
              v-model="formData.recommendations"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Détaillez les recommandations médicales spécifiques..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Restrictions ou limitations
            </label>
            <textarea
              v-model="formData.restrictions"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Spécifiez les restrictions ou limitations imposées..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Follow-up Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Suivi Médical</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.follow_up_required"
              type="checkbox"
              id="follow_up_required"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="follow_up_required" class="text-sm font-medium text-gray-700">
              Suivi médical requis
            </label>
          </div>
          
          <div v-if="formData.follow_up_required">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Détails du suivi
            </label>
            <textarea
              v-model="formData.follow_up_details"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Spécifiez les détails du suivi médical requis..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Clearance Decision Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Décision de Clearance</h3>
        
        <div class="space-y-6">
          <!-- Clearance Options -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-500 transition-colors">
              <div class="flex items-center space-x-3">
                <input
                  v-model="clearanceDecision"
                  type="radio"
                  value="cleared"
                  id="cleared_for_competition"
                  class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                />
                <label for="cleared_for_competition" class="text-sm font-medium text-gray-700">
                  ✅ Autorisé pour la compétition
                </label>
              </div>
              <p class="text-xs text-gray-500 mt-2">
                Le joueur est médicalement apte à participer à la compétition
              </p>
            </div>
            
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-yellow-500 transition-colors">
              <div class="flex items-center space-x-3">
                <input
                  v-model="clearanceDecision"
                  type="radio"
                  value="cleared_with_restrictions"
                  id="cleared_with_restrictions"
                  class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300"
                />
                <label for="cleared_with_restrictions" class="text-sm font-medium text-gray-700">
                  ⚠️ Autorisé avec restrictions
                </label>
              </div>
              <p class="text-xs text-gray-500 mt-2">
                Le joueur peut participer avec des limitations spécifiques
              </p>
            </div>
            
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-red-500 transition-colors">
              <div class="flex items-center space-x-3">
                <input
                  v-model="clearanceDecision"
                  type="radio"
                  value="not_cleared"
                  id="not_cleared"
                  class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                />
                <label for="not_cleared" class="text-sm font-medium text-gray-700">
                  ❌ Non autorisé
                </label>
              </div>
              <p class="text-xs text-gray-500 mt-2">
                Le joueur n'est pas médicalement apte à participer
              </p>
            </div>
          </div>
          
          <!-- Reason for not cleared -->
          <div v-if="clearanceDecision === 'not_cleared'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Raison de la non-autorisation
            </label>
            <textarea
              v-model="formData.reason_for_not_cleared"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Détaillez les raisons médicales de la non-autorisation..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Physician Information Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du Médecin</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Signature du médecin
            </label>
            <input
              v-model="formData.physician_signature"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Nom et signature du médecin"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Numéro de licence médicale
            </label>
            <input
              v-model="formData.physician_license_number"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Numéro de licence du médecin"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date de l'évaluation
            </label>
            <input
              v-model="formData.assessment_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- FIFA Compliance Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conformité FIFA</h3>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <input
              v-model="formData.fifa_compliant"
              type="checkbox"
              id="fifa_compliant"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="fifa_compliant" class="text-sm font-medium text-gray-700">
              Cette évaluation est conforme aux standards FIFA
            </label>
          </div>
          
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-medium text-blue-900">Standards FIFA</h4>
                <p class="text-sm text-blue-700 mt-1">
                  Cette évaluation respecte les standards internationaux FIFA pour les évaluations médicales pré-compétition.
                  Tous les tests requis ont été effectués et documentés selon les protocoles établis.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Card -->
      <div class="bg-gradient-to-r from-blue-50 to-green-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé de la Décision</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Statut de Clearance</h4>
            <div class="flex items-center space-x-2">
              <span class="w-3 h-3 rounded-full" :class="getClearanceStatusClass()"></span>
              <span class="text-sm font-medium" :class="getClearanceStatusTextClass()">
                {{ getClearanceStatusText() }}
              </span>
            </div>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Date d'évaluation</h4>
            <p class="text-sm text-gray-900">{{ formData.assessment_date || 'Non spécifiée' }}</p>
          </div>
          
          <div v-if="formData.follow_up_required">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Suivi requis</h4>
            <p class="text-sm text-gray-900">Oui - {{ formData.follow_up_details || 'Détails non spécifiés' }}</p>
          </div>
          
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Médecin évaluateur</h4>
            <p class="text-sm text-gray-900">{{ formData.physician_signature || 'Non spécifié' }}</p>
          </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-blue-200">
          <p class="text-sm text-gray-600">
            <strong>Note:</strong> Cette évaluation est valide pour une période de 12 mois à compter de la date d'évaluation,
            conformément aux réglementations FIFA en vigueur.
          </p>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'FinalStatementTab',
  props: {
    value: {
      type: Object,
      default: () => ({})
    },
    medicalHistory: {
      type: Object,
      default: () => ({})
    },
    physicalExamination: {
      type: Object,
      default: () => ({})
    },
    cardiovascularInvestigations: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formData: {
        assessment_summary: '',
        recommendations: '',
        restrictions: '',
        follow_up_required: false,
        follow_up_details: '',
        cleared_for_competition: false,
        cleared_with_restrictions: false,
        not_cleared: false,
        reason_for_not_cleared: '',
        physician_signature: '',
        physician_license_number: '',
        assessment_date: new Date().toISOString().split('T')[0],
        fifa_compliant: true
      },
      clearanceDecision: ''
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
    clearanceDecision: {
      handler(newVal) {
        this.formData.cleared_for_competition = newVal === 'cleared'
        this.formData.cleared_with_restrictions = newVal === 'cleared_with_restrictions'
        this.formData.not_cleared = newVal === 'not_cleared'
      }
    }
  },
  methods: {
    getClearanceStatusClass() {
      if (this.formData.cleared_for_competition) return 'bg-green-500'
      if (this.formData.cleared_with_restrictions) return 'bg-yellow-500'
      if (this.formData.not_cleared) return 'bg-red-500'
      return 'bg-gray-400'
    },
    getClearanceStatusTextClass() {
      if (this.formData.cleared_for_competition) return 'text-green-700'
      if (this.formData.cleared_with_restrictions) return 'text-yellow-700'
      if (this.formData.not_cleared) return 'text-red-700'
      return 'text-gray-700'
    },
    getClearanceStatusText() {
      if (this.formData.cleared_for_competition) return 'Autorisé pour la compétition'
      if (this.formData.cleared_with_restrictions) return 'Autorisé avec restrictions'
      if (this.formData.not_cleared) return 'Non autorisé'
      return 'Non déterminé'
    },
    validateForm() {
      const hasSummary = this.formData.assessment_summary.trim() !== ''
      const hasDecision = this.clearanceDecision !== ''
      const hasPhysician = this.formData.physician_signature.trim() !== ''
      const hasDate = this.formData.assessment_date !== ''
      
      const isValid = hasSummary && hasDecision && hasPhysician && hasDate
      this.$emit('update:valid', isValid)
    }
  },
  mounted() {
    // Set initial clearance decision based on form data
    if (this.formData.cleared_for_competition) this.clearanceDecision = 'cleared'
    else if (this.formData.cleared_with_restrictions) this.clearanceDecision = 'cleared_with_restrictions'
    else if (this.formData.not_cleared) this.clearanceDecision = 'not_cleared'
  }
}
</script> 