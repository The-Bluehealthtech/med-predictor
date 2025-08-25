<template>
  <div class="visit-administrative">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">🏥 Gestion Administrative - Visite #{{ visit.id }}</h2>
          <p class="text-gray-600 mt-1">{{ visit.athlete_name }} - {{ formatDateTime(visit.visit_date) }}</p>
        </div>
        
        <div class="flex space-x-3">
          <span 
            class="px-3 py-1 rounded-full text-sm font-medium"
            :class="getStatusClasses(visit.status)"
          >
            {{ visit.status }}
          </span>
          <button 
            @click="saveAdministrativeData"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            💾 Sauvegarder
          </button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Informations de Contact -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📞 Informations de Contact</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone principal</label>
            <input 
              v-model="administrativeData.contact.primary_phone"
              type="tel"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="+33 1 23 45 67 89"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
            <input 
              v-model="administrativeData.contact.secondary_phone"
              type="tel"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="+33 1 23 45 67 89"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              v-model="administrativeData.contact.email"
              type="email"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="athlete@example.com"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <textarea 
              v-model="administrativeData.contact.address"
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Adresse complète..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact d'urgence</label>
            <input 
              v-model="administrativeData.contact.emergency_contact"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Nom et téléphone du contact d'urgence"
            >
          </div>
        </div>
      </div>

      <!-- Facturation -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Facturation</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type de facturation</label>
            <select v-model="administrativeData.billing.type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Sélectionner un type</option>
              <option value="direct">Paiement direct</option>
              <option value="insurance">Assurance</option>
              <option value="club">Club</option>
              <option value="federation">Fédération</option>
              <option value="free">Gratuit</option>
            </select>
          </div>
          
          <div v-if="administrativeData.billing.type === 'insurance'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Assurance</label>
            <input 
              v-model="administrativeData.billing.insurance_provider"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Nom de l'assurance"
            >
          </div>
          
          <div v-if="administrativeData.billing.type === 'insurance'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de police</label>
            <input 
              v-model="administrativeData.billing.policy_number"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Numéro de police d'assurance"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Montant (€)</label>
            <input 
              v-model="administrativeData.billing.amount"
              type="number"
              step="0.01"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="0.00"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes de facturation</label>
            <textarea 
              v-model="administrativeData.billing.notes"
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Notes spécifiques à la facturation..."
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Documents Administratifs -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Documents Administratifs</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Consentements</label>
            <div class="space-y-2">
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.consent_forms.medical_consent"
                  type="checkbox"
                  class="mr-2"
                >
                Consentement médical signé
              </label>
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.consent_forms.privacy_consent"
                  type="checkbox"
                  class="mr-2"
                >
                Consentement RGPD signé
              </label>
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.consent_forms.treatment_consent"
                  type="checkbox"
                  class="mr-2"
                >
                Consentement au traitement
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Documents requis</label>
            <div class="space-y-2">
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.documents.id_card"
                  type="checkbox"
                  class="mr-2"
                >
                Carte d'identité
              </label>
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.documents.insurance_card"
                  type="checkbox"
                  class="mr-2"
                >
                Carte d'assurance
              </label>
              <label class="flex items-center">
                <input 
                  v-model="administrativeData.documents.medical_history"
                  type="checkbox"
                  class="mr-2"
                >
                Historique médical
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload de documents</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
              <input 
                type="file" 
                multiple
                @change="handleFileUpload"
                class="hidden"
                ref="fileInput"
                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
              >
              <button 
                @click="$refs.fileInput.click()"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
              >
                📁 Choisir des fichiers
              </button>
              <p class="text-sm text-gray-500 mt-2">PDF, JPG, PNG, DOC acceptés</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Assistant IA pour Documents -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🤖 Assistant IA pour Documents</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type de document</label>
            <select v-model="aiDocumentType" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Sélectionner un type</option>
              <option value="medical_report">Rapport médical</option>
              <option value="consent_form">Formulaire de consentement</option>
              <option value="insurance_form">Formulaire d'assurance</option>
              <option value="prescription">Ordonnance</option>
              <option value="lab_result">Résultat de laboratoire</option>
              <option value="imaging_report">Rapport d'imagerie</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document à analyser</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
              <input 
                type="file" 
                @change="handleAIFileUpload"
                class="hidden"
                ref="aiFileInput"
                accept=".pdf,.jpg,.jpeg,.png"
              >
              <button 
                @click="$refs.aiFileInput.click()"
                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
              >
                📄 Choisir un document
              </button>
              <p class="text-sm text-gray-500 mt-2">L'IA analysera et extraira les informations</p>
            </div>
          </div>
          
          <div v-if="aiAnalysis" class="bg-blue-50 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-2">📊 Analyse IA</h4>
            <div class="space-y-2 text-sm">
              <div v-if="aiAnalysis.suggested_title">
                <strong>Titre suggéré:</strong> {{ aiAnalysis.suggested_title }}
              </div>
              <div v-if="aiAnalysis.suggested_description">
                <strong>Description:</strong> {{ aiAnalysis.suggested_description }}
              </div>
              <div v-if="aiAnalysis.extracted_data">
                <strong>Données extraites:</strong>
                <ul class="list-disc list-inside mt-1">
                  <li v-for="(value, key) in aiAnalysis.extracted_data" :key="key">
                    {{ key }}: {{ value }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <button 
            v-if="aiAnalysis"
            @click="applyAIAnalysis"
            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            ✅ Appliquer l'analyse IA
          </button>
        </div>
      </div>
    </div>

    <!-- Résumé des actions -->
    <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Résumé des Actions</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center p-4 bg-green-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ visit.id }}</div>
          <div class="text-sm text-green-700">Visite créée</div>
        </div>
        
        <div class="text-center p-4 bg-blue-50 rounded-lg">
          <div class="text-2xl font-bold text-blue-600">{{ documentsCount }}</div>
          <div class="text-sm text-blue-700">Documents uploadés</div>
        </div>
        
        <div class="text-center p-4 bg-yellow-50 rounded-lg">
          <div class="text-2xl font-bold text-yellow-600">{{ aiAnalysisCount }}</div>
          <div class="text-sm text-yellow-700">Analyses IA</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'VisitAdministrative',
  props: {
    visit: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const administrativeData = reactive({
      contact: {
        primary_phone: '',
        secondary_phone: '',
        email: '',
        address: '',
        emergency_contact: ''
      },
      billing: {
        type: '',
        insurance_provider: '',
        policy_number: '',
        amount: '',
        notes: ''
      },
      consent_forms: {
        medical_consent: false,
        privacy_consent: false,
        treatment_consent: false
      },
      documents: {
        id_card: false,
        insurance_card: false,
        medical_history: false
      }
    })

    const aiDocumentType = ref('')
    const aiAnalysis = ref(null)
    const documentsCount = ref(0)
    const aiAnalysisCount = ref(0)

    const getStatusClasses = (status) => {
      const classes = {
        'Planifié': 'bg-blue-100 text-blue-800',
        'Enregistré': 'bg-yellow-100 text-yellow-800',
        'En cours': 'bg-red-100 text-red-800',
        'Terminé': 'bg-green-100 text-green-800',
        'Annulé': 'bg-gray-100 text-gray-800'
      }
      return classes[status] || classes['Planifié']
    }

    const formatDateTime = (dateTime) => {
      return new Date(dateTime).toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const handleFileUpload = (event) => {
      const files = event.target.files
      // Logique pour gérer l'upload de fichiers
      documentsCount.value += files.length
    }

    const handleAIFileUpload = async (event) => {
      const file = event.target.files[0]
      if (!file || !aiDocumentType.value) return

      const formData = new FormData()
      formData.append('file', file)
      formData.append('document_type', aiDocumentType.value)
      formData.append('visit_id', props.visit.id)

      try {
        const response = await axios.post('/documents/analyze-upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          aiAnalysis.value = response.data
          aiAnalysisCount.value++
        }
      } catch (error) {
        console.error('Erreur lors de l\'analyse IA:', error)
      }
    }

    const applyAIAnalysis = () => {
      if (aiAnalysis.value) {
        // Appliquer les données extraites par l'IA
        if (aiAnalysis.value.suggested_title) {
          // Logique pour appliquer le titre suggéré
        }
        if (aiAnalysis.value.extracted_data) {
          // Logique pour appliquer les données extraites
        }
      }
    }

    const saveAdministrativeData = async () => {
      try {
        await axios.post(`/visits/${props.visit.id}/administrative-data`, {
          administrative_data: administrativeData
        })
        // Afficher une notification de succès
      } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error)
      }
    }

    onMounted(() => {
      // Charger les données administratives existantes si disponibles
      if (props.visit.administrative_data) {
        Object.assign(administrativeData, props.visit.administrative_data)
      }
    })

    return {
      administrativeData,
      aiDocumentType,
      aiAnalysis,
      documentsCount,
      aiAnalysisCount,
      getStatusClasses,
      formatDateTime,
      handleFileUpload,
      handleAIFileUpload,
      applyAIAnalysis,
      saveAdministrativeData
    }
  }
}
</script>

<style scoped>
.visit-administrative {
  @apply max-w-7xl mx-auto p-6;
}

/* Responsive design */
@media (max-width: 768px) {
  .visit-administrative {
    @apply p-4;
  }
}
</style> 