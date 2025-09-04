<template>
  <div class="fifa-compliant-pcma-form">
    <div class="max-w-7xl mx-auto p-6">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📋 PCMA FIFA-Compliant</h1>
            <p class="text-gray-600">Formulaire d'évaluation médicale pré-compétition conforme FIFA</p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
              Version {{ formVersion }}
            </span>
            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
              FIFA Compliant
            </span>
          </div>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">Progression</span>
          <span class="text-sm text-gray-500">{{ completedSteps }}/{{ totalSteps }} étapes</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${(completedSteps / totalSteps) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="mb-8">
        <nav class="flex space-x-8 border-b border-gray-200">
          <button
            v-for="(tab, index) in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center space-x-2">
              <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs"
                    :class="getTabStatusClass(tab.id)">
                {{ getTabStatusIcon(tab.id) }}
              </span>
              <span>{{ tab.title }}</span>
            </div>
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Medical History Tab -->
        <div v-if="activeTab === 'medical-history'" class="p-6">
          <MedicalHistoryTab 
            v-model="formData.medical_history"
            :athlete="athlete"
            @update:valid="updateTabStatus('medical-history', $event)"
          />
        </div>

        <!-- Physical Examination Tab -->
        <div v-if="activeTab === 'physical-examination'" class="p-6">
          <PhysicalExaminationTab 
            v-model="formData.physical_examination"
            :anatomical-annotations="formData.anatomical_annotations"
            @update:valid="updateTabStatus('physical-examination', $event)"
            @update:annotations="updateAnatomicalAnnotations"
          />
        </div>

        <!-- Cardiovascular Investigations Tab -->
        <div v-if="activeTab === 'cardiovascular-investigations'" class="p-6">
          <CardiovascularInvestigationsTab 
            v-model="formData.cardiovascular_investigations"
            @update:valid="updateTabStatus('cardiovascular-investigations', $event)"
          />
        </div>

        <!-- Final Statement Tab -->
        <div v-if="activeTab === 'final-statement'" class="p-6">
          <FinalStatementTab 
            v-model="formData.final_statement"
            :medical-history="formData.medical_history"
            :physical-examination="formData.physical_examination"
            :cardiovascular-investigations="formData.cardiovascular_investigations"
            @update:valid="updateTabStatus('final-statement', $event)"
          />
        </div>

        <!-- SCAT Assessment Tab -->
        <div v-if="activeTab === 'scat-assessment'" class="p-6">
          <ScatAssessmentTab 
            v-model="formData.scat_assessment"
            @update:valid="updateTabStatus('scat-assessment', $event)"
          />
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="flex justify-between items-center mt-8">
        <button
          @click="previousTab"
          :disabled="!canGoPrevious"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          ← Précédent
        </button>

        <div class="flex space-x-4">
          <button
            @click="saveDraft"
            :disabled="saving"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="saving" class="flex items-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600 mr-2"></div>
              Sauvegarde...
            </span>
            <span v-else>💾 Sauvegarder brouillon</span>
          </button>

          <button
            v-if="activeTab === 'final-statement'"
            @click="submitPCMA"
            :disabled="!isFormValid || submitting"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="submitting" class="flex items-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
              Soumission...
            </span>
            <span v-else>✅ Soumettre PCMA</span>
          </button>

          <button
            v-else
            @click="nextTab"
            :disabled="!canGoNext"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Suivant →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import MedicalHistoryTab from './tabs/MedicalHistoryTab.vue'
import PhysicalExaminationTab from './tabs/PhysicalExaminationTab.vue'
import CardiovascularInvestigationsTab from './tabs/CardiovascularInvestigationsTab.vue'
import FinalStatementTab from './tabs/FinalStatementTab.vue'
import ScatAssessmentTab from './tabs/ScatAssessmentTab.vue'

export default {
  name: 'FifaCompliantPCMAForm',
  components: {
    MedicalHistoryTab,
    PhysicalExaminationTab,
    CardiovascularInvestigationsTab,
    FinalStatementTab,
    ScatAssessmentTab
  },
  props: {
    athlete: {
      type: Object,
      required: true
    },
    initialData: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      activeTab: 'medical-history',
      formVersion: '1.0',
      saving: false,
      submitting: false,
      tabStatus: {
        'medical-history': false,
        'physical-examination': false,
        'cardiovascular-investigations': false,
        'final-statement': false,
        'scat-assessment': false
      },
      formData: {
        athlete_id: this.athlete.id,
        type: 'cardio',
        status: 'pending',
        assessor_id: null,
        assessment_date: new Date().toISOString().split('T')[0],
        notes: '',
        fifa_id: '',
        competition_name: '',
        competition_date: '',
        team_name: '',
        position: '',
        medical_history: {},
        physical_examination: {},
        cardiovascular_investigations: {},
        final_statement: {},
        scat_assessment: {},
        anatomical_annotations: {
          anterior: [],
          posterior: []
        },
        attachments: [],
        form_version: '1.0',
        last_updated_at: new Date().toISOString()
      },
      tabs: [
        { id: 'medical-history', title: 'Historique Médical', icon: '📋' },
        { id: 'physical-examination', title: 'Examen Physique', icon: '🏥' },
        { id: 'cardiovascular-investigations', title: 'Investigations Cardiovasculaires', icon: '❤️' },
        { id: 'final-statement', title: 'Déclaration Finale', icon: '✅' },
        { id: 'scat-assessment', title: 'Évaluation SCAT', icon: '🧠' }
      ]
    }
  },
  computed: {
    totalSteps() {
      return this.tabs.length
    },
    completedSteps() {
      return Object.values(this.tabStatus).filter(Boolean).length
    },
    canGoPrevious() {
      const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab)
      return currentIndex > 0
    },
    canGoNext() {
      const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab)
      return currentIndex < this.tabs.length - 1 && this.tabStatus[this.activeTab]
    },
    isFormValid() {
      return Object.values(this.tabStatus).every(Boolean)
    }
  },
  methods: {
    updateTabStatus(tabId, isValid) {
      this.$set(this.tabStatus, tabId, isValid)
    },
    updateAnatomicalAnnotations(annotations) {
      this.formData.anatomical_annotations = annotations
    },
    getTabStatusClass(tabId) {
      if (this.tabStatus[tabId]) {
        return 'bg-green-100 text-green-600'
      }
      return 'bg-gray-100 text-gray-600'
    },
    getTabStatusIcon(tabId) {
      if (this.tabStatus[tabId]) {
        return '✓'
      }
      return this.tabs.find(tab => tab.id === tabId)?.icon || '📋'
    },
    previousTab() {
      const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab)
      if (currentIndex > 0) {
        this.activeTab = this.tabs[currentIndex - 1].id
      }
    },
    nextTab() {
      const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab)
      if (currentIndex < this.tabs.length - 1 && this.tabStatus[this.activeTab]) {
        this.activeTab = this.tabs[currentIndex + 1].id
      }
    },
    async saveDraft() {
      this.saving = true
      try {
        const response = await this.$http.post('/api/v1/pcmas/draft', this.formData)
        this.$toast.success('Brouillon sauvegardé avec succès')
      } catch (error) {
        this.$toast.error('Erreur lors de la sauvegarde du brouillon')
        console.error('Save draft error:', error)
      } finally {
        this.saving = false
      }
    },
    async submitPCMA() {
      this.submitting = true
      try {
        const response = await this.$http.post('/api/v1/pcmas', this.formData)
        this.$toast.success('PCMA soumis avec succès')
        this.$emit('submitted', response.data)
      } catch (error) {
        this.$toast.error('Erreur lors de la soumission du PCMA')
        console.error('Submit PCMA error:', error)
      } finally {
        this.submitting = false
      }
    }
  },
  mounted() {
    // Initialize form data with initial data if provided
    if (this.initialData) {
      this.formData = { ...this.formData, ...this.initialData }
    }
  }
}
</script>

<style scoped>
.fifa-compliant-pcma-form {
  min-height: 100vh;
  background-color: #f9fafb;
}
</style> 