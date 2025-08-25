<template>
  <div class="athlete-imaging">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-3 text-gray-600">Chargement des études d'imagerie...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8">
      <div class="text-6xl mb-4">📷</div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur de chargement</h3>
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <button 
        @click="loadImagingStudies"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
      >
        Réessayer
      </button>
    </div>

    <!-- Content -->
    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Imagerie Médicale</h2>
          <p class="text-gray-600">{{ studies.length }} études trouvées</p>
        </div>
        <div class="flex items-center space-x-2">
          <div class="flex items-center space-x-1">
            <div class="w-3 h-3 rounded-full" :class="pacsConnected ? 'bg-green-500' : 'bg-red-500'"></div>
            <span class="text-sm text-gray-600">
              {{ pacsConnected ? 'PACS connecté' : 'PACS déconnecté' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Studies List -->
      <div v-if="studies.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="study in studies" 
          :key="study.study_instance_uid"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer border border-gray-200"
          @click="viewStudy(study)"
        >
          <div class="p-4">
            <!-- Study Header -->
            <div class="flex items-start justify-between mb-3">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 text-sm mb-1">
                  {{ study.study_description }}
                </h3>
                <p class="text-xs text-gray-500">
                  {{ study.formatted_date }} à {{ study.formatted_time }}
                </p>
              </div>
              <div class="flex items-center space-x-1">
                <span 
                  v-for="modality in study.modalities_in_study" 
                  :key="modality"
                  class="px-2 py-1 text-xs rounded-full"
                  :class="getModalityClass(modality)"
                >
                  {{ modality }}
                </span>
              </div>
            </div>

            <!-- Study Details -->
            <div class="space-y-2 text-xs text-gray-600">
              <div class="flex justify-between">
                <span>Institution:</span>
                <span class="font-medium">{{ study.institution_name || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span>Séries:</span>
                <span class="font-medium">{{ study.number_of_series }}</span>
              </div>
              <div class="flex justify-between">
                <span>Images:</span>
                <span class="font-medium">{{ study.number_of_instances }}</span>
              </div>
              <div v-if="study.accession_number" class="flex justify-between">
                <span>N° Accession:</span>
                <span class="font-medium">{{ study.accession_number }}</span>
              </div>
            </div>

            <!-- Physicians -->
            <div v-if="study.referring_physician_name || study.performing_physician_name" class="mt-3 pt-3 border-t border-gray-100">
              <div class="text-xs text-gray-500 space-y-1">
                <div v-if="study.referring_physician_name">
                  <span class="font-medium">Médecin prescripteur:</span>
                  <span class="ml-1">{{ study.referring_physician_name }}</span>
                </div>
                <div v-if="study.performing_physician_name">
                  <span class="font-medium">Médecin exécutant:</span>
                  <span class="ml-1">{{ study.performing_physician_name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Studies -->
      <div v-else class="text-center py-12">
        <div class="text-6xl mb-4">📷</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune étude d'imagerie</h3>
        <p class="text-gray-600">
          {{ pacsConnected 
            ? 'Aucune étude d\'imagerie trouvée pour cet athlète.' 
            : 'Impossible de se connecter au serveur PACS.' 
          }}
        </p>
      </div>
    </div>

    <!-- DICOM Viewer Modal -->
    <div v-if="showViewer" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl h-5/6 mx-4">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              {{ selectedStudy?.study_description }}
            </h3>
            <p class="text-sm text-gray-600">
              {{ selectedStudy?.formatted_date }} - {{ selectedStudy?.institution_name }}
            </p>
          </div>
          <button 
            @click="closeViewer"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="flex-1 p-4">
          <!-- DICOM Viewer Container -->
          <div id="dicom-viewer" class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
            <div v-if="viewerLoading" class="text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p class="text-gray-600">Chargement de l'image...</p>
            </div>
            <div v-else-if="viewerError" class="text-center">
              <div class="text-4xl mb-4">❌</div>
              <p class="text-gray-600">{{ viewerError }}</p>
            </div>
            <div v-else class="text-center">
              <div class="text-4xl mb-4">🖼️</div>
              <p class="text-gray-600">Visualiseur DICOM</p>
              <p class="text-sm text-gray-500 mt-2">
                Intégration OHIF Viewer ou Cornerstone.js
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AthleteImaging',
  props: {
    athleteId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      studies: [],
      loading: true,
      error: null,
      pacsConnected: false,
      showViewer: false,
      selectedStudy: null,
      viewerLoading: false,
      viewerError: null
    }
  },
  async mounted() {
    await this.loadImagingStudies()
  },
  methods: {
    async loadImagingStudies() {
      this.loading = true
      this.error = null
      
      try {
        const response = await this.$http.get(`/api/v1/athletes/${this.athleteId}/imaging-studies`)
        const data = response.data.data
        
        this.studies = data.studies || []
        this.pacsConnected = data.pacs_connected
        
        if (!data.pacs_connected && data.error) {
          this.error = data.error
        }
      } catch (error) {
        console.error('Error loading imaging studies:', error)
        this.error = 'Erreur lors du chargement des études d\'imagerie'
        this.pacsConnected = false
      } finally {
        this.loading = false
      }
    },

    viewStudy(study) {
      this.selectedStudy = study
      this.showViewer = true
      this.viewerLoading = true
      this.viewerError = null
      
      // Simulate loading DICOM viewer
      setTimeout(() => {
        this.viewerLoading = false
        // Here you would integrate with OHIF Viewer or Cornerstone.js
        // For now, we'll just show a placeholder
      }, 2000)
    },

    closeViewer() {
      this.showViewer = false
      this.selectedStudy = null
      this.viewerLoading = false
      this.viewerError = null
    },

    getModalityClass(modality) {
      const classes = {
        'CT': 'bg-blue-100 text-blue-800',
        'MR': 'bg-purple-100 text-purple-800',
        'CR': 'bg-green-100 text-green-800',
        'DX': 'bg-yellow-100 text-yellow-800',
        'US': 'bg-orange-100 text-orange-800',
        'XA': 'bg-red-100 text-red-800',
        'NM': 'bg-indigo-100 text-indigo-800',
        'PT': 'bg-pink-100 text-pink-800'
      }
      
      return classes[modality] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script>

<style scoped>
.athlete-imaging {
  @apply p-6;
}

#dicom-viewer {
  min-height: 400px;
}
</style> 