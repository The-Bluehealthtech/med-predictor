<template>
  <div class="vaccination-record-view">
    <!-- Header with sync button -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Dossier de Vaccination</h2>
        <p class="text-gray-600">Gestion des vaccins et synchronisation avec le registre national</p>
      </div>
      
      <div class="flex space-x-3">
        <button
          @click="syncWithRegistry"
          :disabled="syncing"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
        >
          <svg v-if="syncing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          {{ syncing ? 'Synchronisation...' : 'Synchroniser avec le registre' }}
        </button>
        
        <button
          @click="showAddForm = true"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Ajouter un vaccin
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Vaccins</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.total || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Actifs</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.active || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Expirent bientôt</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.expiring_soon || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Expirés</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.expired || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select v-model="filters.status" @change="loadImmunisations" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">Tous</option>
            <option value="active">Actif</option>
            <option value="expired">Expiré</option>
            <option value="pending">En attente</option>
            <option value="incomplete">Incomplet</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Vaccin</label>
          <input
            v-model="filters.vaccine"
            @input="debouncedLoad"
            type="text"
            placeholder="Rechercher un vaccin..."
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
          <input
            v-model="filters.date_from"
            @change="loadImmunisations"
            type="date"
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
          <input
            v-model="filters.date_to"
            @change="loadImmunisations"
            type="date"
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      </div>
    </div>

    <!-- Immunisation List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="immunisation in immunisations" :key="immunisation.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <div class="flex items-center">
                  <p class="text-sm font-medium text-gray-900">{{ immunisation.vaccine_name }}</p>
                  <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(immunisation.status)">
                    {{ getStatusText(immunisation.status) }}
                  </span>
                  <span v-if="immunisation.sync_status" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getSyncStatusClass(immunisation.sync_status)">
                    {{ getSyncStatusText(immunisation.sync_status) }}
                  </span>
                </div>
                <div class="mt-1 text-sm text-gray-500">
                  <span class="font-medium">{{ immunisation.vaccine_code }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ formatDate(immunisation.date_administered) }}</span>
                  <span v-if="immunisation.lot_number" class="mx-2">•</span>
                  <span v-if="immunisation.lot_number">Lot: {{ immunisation.lot_number }}</span>
                  <span v-if="immunisation.dose_number && immunisation.total_doses" class="mx-2">•</span>
                  <span v-if="immunisation.dose_number && immunisation.total_doses">Dose {{ immunisation.dose_number }}/{{ immunisation.total_doses }}</span>
                </div>
                <div v-if="immunisation.expiration_date" class="mt-1 text-sm">
                  <span class="text-gray-500">Expire le:</span>
                  <span :class="getExpirationClass(immunisation.expiration_date)">{{ formatDate(immunisation.expiration_date) }}</span>
                </div>
                <div v-if="immunisation.notes" class="mt-1 text-sm text-gray-500">
                  {{ immunisation.notes }}
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button
                @click="editImmunisation(immunisation)"
                class="text-blue-600 hover:text-blue-900"
                title="Modifier"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              
              <button
                @click="deleteImmunisation(immunisation)"
                class="text-red-600 hover:text-red-900"
                title="Supprimer"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </li>
      </ul>
      
      <!-- Empty state -->
      <div v-if="immunisations.length === 0 && !loading" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun vaccin trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un vaccin ou synchroniser avec le registre national.</p>
      </div>
      
      <!-- Loading state -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Chargement...</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.total > pagination.per_page" class="mt-6 flex items-center justify-between">
      <div class="flex-1 flex justify-between sm:hidden">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
        >
          Précédent
        </button>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
        >
          Suivant
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Affichage de <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span> à <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span> sur <span class="font-medium">{{ pagination.total }}</span> résultats
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <button
              v-for="page in getPageNumbers()"
              :key="page"
              @click="changePage(page)"
              :class="page === pagination.current_page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
            >
              {{ page }}
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddForm || editingImmunisation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingImmunisation ? 'Modifier le vaccin' : 'Ajouter un vaccin' }}
          </h3>
          
          <form @submit.prevent="saveImmunisation" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Code vaccin (ICD-11)</label>
              <ICDSearchInput
                v-model="form.vaccine_code"
                placeholder="Rechercher un code vaccin..."
                @selected="onVaccineSelected"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom du vaccin</label>
              <input
                v-model="form.vaccine_name"
                type="text"
                required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date d'administration</label>
              <input
                v-model="form.date_administered"
                type="date"
                required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de lot</label>
                <input
                  v-model="form.lot_number"
                  type="text"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fabricant</label>
                <input
                  v-model="form.manufacturer"
                  type="text"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
              <input
                v-model="form.expiration_date"
                type="date"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dose actuelle</label>
                <input
                  v-model="form.dose_number"
                  type="number"
                  min="1"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total des doses</label>
                <input
                  v-model="form.total_doses"
                  type="number"
                  min="1"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Voie d'administration</label>
                <select v-model="form.route" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="IM">Intramusculaire</option>
                  <option value="SC">Sous-cutanée</option>
                  <option value="ID">Intradermique</option>
                  <option value="IN">Intranasale</option>
                  <option value="PO">Orale</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Site d'injection</label>
                <select v-model="form.site" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="LA">Bras gauche</option>
                  <option value="RA">Bras droit</option>
                  <option value="LD">Deltroïde gauche</option>
                  <option value="RD">Deltroïde droit</option>
                  <option value="LG">Fessier gauche</option>
                  <option value="RG">Fessier droit</option>
                  <option value="LVL">Vaste latéral gauche</option>
                  <option value="RVL">Vaste latéral droit</option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
              <textarea
                v-model="form.notes"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeForm"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ saving ? 'Enregistrement...' : (editingImmunisation ? 'Modifier' : 'Ajouter') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { debounce } from 'lodash'
import ICDSearchInput from './ICDSearchInput.vue'

export default {
  name: 'VaccinationRecordView',
  components: {
    ICDSearchInput
  },
  props: {
    athleteId: {
      type: [Number, String],
      required: true
    }
  },
  setup(props) {
    const immunisations = ref([])
    const statistics = ref({})
    const loading = ref(false)
    const syncing = ref(false)
    const saving = ref(false)
    const showAddForm = ref(false)
    const editingImmunisation = ref(null)
    const pagination = ref(null)
    
    const filters = reactive({
      status: '',
      vaccine: '',
      date_from: '',
      date_to: ''
    })
    
    const form = reactive({
      vaccine_code: '',
      vaccine_name: '',
      date_administered: '',
      lot_number: '',
      manufacturer: '',
      expiration_date: '',
      dose_number: 1,
      total_doses: 1,
      route: 'IM',
      site: 'LA',
      notes: ''
    })

    // Debounced load function
    const debouncedLoad = debounce(() => {
      loadImmunisations()
    }, 300)

    // Load immunisations
    const loadImmunisations = async () => {
      loading.value = true
      try {
        const params = new URLSearchParams()
        Object.keys(filters).forEach(key => {
          if (filters[key]) {
            params.append(key, filters[key])
          }
        })
        
        const response = await fetch(`/api/v1/athletes/${props.athleteId}/immunisations?${params}`)
        const data = await response.json()
        
        if (data.success) {
          immunisations.value = data.data
          pagination.value = data.pagination
        }
      } catch (error) {
        console.error('Error loading immunisations:', error)
      } finally {
        loading.value = false
      }
    }

    // Load statistics
    const loadStatistics = async () => {
      try {
        const response = await fetch(`/api/v1/athletes/${props.athleteId}/immunisations/statistics`)
        const data = await response.json()
        
        if (data.success) {
          statistics.value = data.data
        }
      } catch (error) {
        console.error('Error loading statistics:', error)
      }
    }

    // Sync with registry
    const syncWithRegistry = async () => {
      syncing.value = true
      try {
        const response = await fetch(`/api/v1/athletes/${props.athleteId}/immunisations/sync`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        
        const data = await response.json()
        
        if (data.success) {
          // Reload data
          await loadImmunisations()
          await loadStatistics()
        }
        
        // Show notification
        // You can implement a notification system here
        console.log(data.message)
      } catch (error) {
        console.error('Error syncing with registry:', error)
      } finally {
        syncing.value = false
      }
    }

    // Save immunisation
    const saveImmunisation = async () => {
      saving.value = true
      try {
        const url = editingImmunisation.value 
          ? `/api/v1/immunisations/${editingImmunisation.value.id}`
          : `/api/v1/athletes/${props.athleteId}/immunisations`
        
        const method = editingImmunisation.value ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(form)
        })
        
        const data = await response.json()
        
        if (data.success) {
          closeForm()
          await loadImmunisations()
          await loadStatistics()
        }
      } catch (error) {
        console.error('Error saving immunisation:', error)
      } finally {
        saving.value = false
      }
    }

    // Edit immunisation
    const editImmunisation = (immunisation) => {
      editingImmunisation.value = immunisation
      Object.assign(form, {
        vaccine_code: immunisation.vaccine_code,
        vaccine_name: immunisation.vaccine_name,
        date_administered: immunisation.date_administered.split('T')[0],
        lot_number: immunisation.lot_number || '',
        manufacturer: immunisation.manufacturer || '',
        expiration_date: immunisation.expiration_date ? immunisation.expiration_date.split('T')[0] : '',
        dose_number: immunisation.dose_number || 1,
        total_doses: immunisation.total_doses || 1,
        route: immunisation.route || 'IM',
        site: immunisation.site || 'LA',
        notes: immunisation.notes || ''
      })
    }

    // Delete immunisation
    const deleteImmunisation = async (immunisation) => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer ce vaccin ?')) {
        return
      }
      
      try {
        const response = await fetch(`/api/v1/immunisations/${immunisation.id}`, {
          method: 'DELETE',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        
        const data = await response.json()
        
        if (data.success) {
          await loadImmunisations()
          await loadStatistics()
        }
      } catch (error) {
        console.error('Error deleting immunisation:', error)
      }
    }

    // Close form
    const closeForm = () => {
      showAddForm.value = false
      editingImmunisation.value = null
      Object.assign(form, {
        vaccine_code: '',
        vaccine_name: '',
        date_administered: '',
        lot_number: '',
        manufacturer: '',
        expiration_date: '',
        dose_number: 1,
        total_doses: 1,
        route: 'IM',
        site: 'LA',
        notes: ''
      })
    }

    // Vaccine selected from ICD search
    const onVaccineSelected = (vaccine) => {
      if (vaccine) {
        form.vaccine_code = vaccine.code
        form.vaccine_name = vaccine.label
      }
    }

    // Change page
    const changePage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page) {
        loadImmunisations(page)
      }
    }

    // Get page numbers for pagination
    const getPageNumbers = () => {
      if (!pagination.value) return []
      
      const pages = []
      const current = pagination.value.current_page
      const last = pagination.value.last_page
      
      for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
        pages.push(i)
      }
      
      return pages
    }

    // Utility functions
    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('fr-FR')
    }

    const getStatusClass = (status) => {
      return {
        'bg-green-100 text-green-800': status === 'active',
        'bg-red-100 text-red-800': status === 'expired',
        'bg-yellow-100 text-yellow-800': status === 'pending',
        'bg-blue-100 text-blue-800': status === 'incomplete'
      }
    }

    const getStatusText = (status) => {
      return {
        'active': 'Actif',
        'expired': 'Expiré',
        'pending': 'En attente',
        'incomplete': 'Incomplet'
      }[status] || status
    }

    const getSyncStatusClass = (status) => {
      return {
        'bg-green-100 text-green-800': status === 'synced',
        'bg-yellow-100 text-yellow-800': status === 'pending',
        'bg-red-100 text-red-800': status === 'failed'
      }
    }

    const getSyncStatusText = (status) => {
      return {
        'synced': 'Synchronisé',
        'pending': 'En attente',
        'failed': 'Échec'
      }[status] || status
    }

    const getExpirationClass = (expirationDate) => {
      const date = new Date(expirationDate)
      const now = new Date()
      const daysUntilExpiration = Math.ceil((date - now) / (1000 * 60 * 60 * 24))
      
      if (daysUntilExpiration < 0) return 'text-red-600 font-medium'
      if (daysUntilExpiration <= 30) return 'text-yellow-600 font-medium'
      return 'text-gray-600'
    }

    // Initialize
    onMounted(() => {
      loadImmunisations()
      loadStatistics()
    })

    return {
      immunisations,
      statistics,
      loading,
      syncing,
      saving,
      showAddForm,
      editingImmunisation,
      pagination,
      filters,
      form,
      loadImmunisations,
      loadStatistics,
      syncWithRegistry,
      saveImmunisation,
      editImmunisation,
      deleteImmunisation,
      closeForm,
      onVaccineSelected,
      changePage,
      getPageNumbers,
      debouncedLoad,
      formatDate,
      getStatusClass,
      getStatusText,
      getSyncStatusClass,
      getSyncStatusText,
      getExpirationClass
    }
  }
}
</script> 