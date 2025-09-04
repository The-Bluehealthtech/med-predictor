<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-watch text-orange-600 mr-2"></i>
            Appareils Connectés
          </h2>
          <p class="text-gray-600 mt-1">Gérez vos appareils de suivi fitness</p>
        </div>
        <button @click="$emit('refresh')" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <i class="fas fa-sync-alt mr-2"></i>
          Actualiser
        </button>
      </div>
    </div>

    <!-- Appareils Connectés -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-link text-green-600 mr-2"></i>
        Vos Appareils
      </h3>
      
      <div v-if="loading" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
        <p class="text-gray-500 mt-2">Chargement des appareils...</p>
      </div>
      
      <div v-else-if="devices.length" class="space-y-4">
        <div v-for="device in devices" :key="device.id" 
             class="border border-gray-200 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                  <i :class="getDeviceIcon(device.type)" class="text-xl text-gray-600"></i>
                </div>
              </div>
              
              <div class="flex-1">
                <h4 class="font-medium text-gray-900">{{ device.name }}</h4>
                <p class="text-sm text-gray-500">{{ getDeviceTypeLabel(device.type) }}</p>
                <p class="text-xs text-gray-400">
                  Dernière synchronisation: {{ formatDate(device.lastSync) }}
                </p>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <span :class="[
                'px-2 py-1 text-xs font-semibold rounded-full',
                device.connected ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ device.connected ? 'Connecté' : 'Déconnecté' }}
              </span>
              
              <button 
                v-if="device.connected"
                @click="disconnectDevice(device.id)"
                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
              >
                <i class="fas fa-unlink mr-1"></i>
                Déconnecter
              </button>
              
              <button 
                v-else
                @click="connectDevice(device.type)"
                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
              >
                <i class="fas fa-link mr-1"></i>
                Connecter
              </button>
            </div>
          </div>
          
          <!-- Données récentes -->
          <div v-if="device.connected" class="mt-4 pt-4 border-t border-gray-200">
            <h5 class="text-sm font-medium text-gray-700 mb-2">Données récentes</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div class="text-center">
                <div class="text-lg font-semibold text-blue-600">{{ getRandomData(8000, 12000) }}</div>
                <div class="text-gray-500">Pas</div>
              </div>
              <div class="text-center">
                <div class="text-lg font-semibold text-green-600">{{ getRandomData(60, 85) }}</div>
                <div class="text-gray-500">BPM</div>
              </div>
              <div class="text-center">
                <div class="text-lg font-semibold text-purple-600">{{ getRandomData(6, 9) }}h</div>
                <div class="text-gray-500">Sommeil</div>
              </div>
              <div class="text-center">
                <div class="text-lg font-semibold text-orange-600">{{ getRandomData(1800, 2500) }}</div>
                <div class="text-gray-500">Calories</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8">
        <i class="fas fa-watch text-gray-400 text-4xl mb-4"></i>
        <h4 class="text-lg font-medium text-gray-900 mb-2">Aucun appareil connecté</h4>
        <p class="text-gray-500 mb-4">Connectez votre premier appareil pour commencer le suivi</p>
      </div>
    </div>

    <!-- Ajouter un Appareil -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-plus text-blue-600 mr-2"></i>
        Ajouter un Appareil
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Garmin -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-watch text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Garmin</h4>
              <p class="text-sm text-gray-500">Montres et trackers</p>
            </div>
          </div>
          <button 
            @click="connectDevice('garmin')"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-link mr-2"></i>
            Connecter Garmin
          </button>
        </div>

        <!-- Apple Watch -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-apple text-green-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Apple Watch</h4>
              <p class="text-sm text-gray-500">Santé et fitness</p>
            </div>
          </div>
          <button 
            @click="connectDevice('apple_watch')"
            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            <i class="fas fa-link mr-2"></i>
            Connecter Apple Watch
          </button>
        </div>

        <!-- Fitbit -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-heartbeat text-purple-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Fitbit</h4>
              <p class="text-sm text-gray-500">Bracelets et trackers</p>
            </div>
          </div>
          <button 
            @click="connectDevice('fitbit')"
            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
          >
            <i class="fas fa-link mr-2"></i>
            Connecter Fitbit
          </button>
        </div>

        <!-- Polar -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-red-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-heart text-red-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Polar</h4>
              <p class="text-sm text-gray-500">Moniteurs cardiaques</p>
            </div>
          </div>
          <button 
            @click="connectDevice('polar')"
            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            <i class="fas fa-link mr-2"></i>
            Connecter Polar
          </button>
        </div>

        <!-- Suunto -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-compass text-orange-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Suunto</h4>
              <p class="text-sm text-gray-500">Montres sportives</p>
            </div>
          </div>
          <button 
            @click="connectDevice('suunto')"
            class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors"
          >
            <i class="fas fa-link mr-2"></i>
            Connecter Suunto
          </button>
        </div>

        <!-- Autre -->
        <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <i class="fas fa-plus text-gray-600"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-900">Autre</h4>
              <p class="text-sm text-gray-500">Appareil personnalisé</p>
            </div>
          </div>
          <button 
            @click="showCustomDeviceModal = true"
            class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
          >
            <i class="fas fa-plus mr-2"></i>
            Ajouter Manuellement
          </button>
        </div>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
        Statistiques des Appareils
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-blue-50 rounded-lg">
          <div class="text-2xl font-bold text-blue-600">{{ devices.filter(d => d.connected).length }}</div>
          <div class="text-sm text-gray-600">Appareils connectés</div>
        </div>
        
        <div class="text-center p-4 bg-green-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ getRandomData(15000, 25000) }}</div>
          <div class="text-sm text-gray-600">Pas aujourd'hui</div>
        </div>
        
        <div class="text-center p-4 bg-purple-50 rounded-lg">
          <div class="text-2xl font-bold text-purple-600">{{ getRandomData(7, 9) }}h</div>
          <div class="text-sm text-gray-600">Sommeil moyen</div>
        </div>
        
        <div class="text-center p-4 bg-orange-50 rounded-lg">
          <div class="text-2xl font-bold text-orange-600">{{ getRandomData(2000, 3500) }}</div>
          <div class="text-sm text-gray-600">Calories brûlées</div>
        </div>
      </div>
    </div>

    <!-- Modal Appareil Personnalisé -->
    <div v-if="showCustomDeviceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              <i class="fas fa-plus text-blue-600 mr-2"></i>
              Ajouter un Appareil
            </h3>
            <button @click="showCustomDeviceModal = false" class="text-gray-400 hover:text-gray-600">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <form @submit.prevent="addCustomDevice" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'appareil</label>
              <input 
                v-model="customDevice.name" 
                type="text" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Type d'appareil</label>
              <select 
                v-model="customDevice.type" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Sélectionner...</option>
                <option value="watch">Montre</option>
                <option value="tracker">Tracker</option>
                <option value="heart_rate">Moniteur cardiaque</option>
                <option value="scale">Balance connectée</option>
                <option value="other">Autre</option>
              </select>
            </div>

            <div class="flex space-x-3">
              <button 
                type="submit" 
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                <i class="fas fa-plus mr-2"></i>
                Ajouter
              </button>
              <button 
                @click="showCustomDeviceModal = false" 
                type="button"
                class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'

export default {
  name: 'ConnectedDevicesView',
  props: {
    devices: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['connect', 'disconnect', 'refresh'],
  setup(props, { emit }) {
    const showCustomDeviceModal = ref(false)
    
    const customDevice = reactive({
      name: '',
      type: ''
    })

    const getDeviceIcon = (type) => {
      const icons = {
        'watch': 'fas fa-watch',
        'tracker': 'fas fa-heartbeat',
        'heart_rate': 'fas fa-heart',
        'scale': 'fas fa-weight',
        'other': 'fas fa-mobile-alt'
      }
      return icons[type] || 'fas fa-mobile-alt'
    }

    const getDeviceTypeLabel = (type) => {
      const labels = {
        'watch': 'Montre connectée',
        'tracker': 'Tracker fitness',
        'heart_rate': 'Moniteur cardiaque',
        'scale': 'Balance connectée',
        'other': 'Autre appareil'
      }
      return labels[type] || 'Appareil'
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Jamais'
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const getRandomData = (min, max) => {
      return Math.floor(Math.random() * (max - min + 1)) + min
    }

    const connectDevice = (deviceType) => {
      emit('connect', deviceType)
    }

    const disconnectDevice = (deviceId) => {
      emit('disconnect', deviceId)
    }

    const addCustomDevice = () => {
      if (customDevice.name && customDevice.type) {
        // Simuler l'ajout d'un appareil personnalisé
        console.log('Ajout appareil:', customDevice)
        showCustomDeviceModal.value = false
        customDevice.name = ''
        customDevice.type = ''
      }
    }

    return {
      showCustomDeviceModal,
      customDevice,
      getDeviceIcon,
      getDeviceTypeLabel,
      formatDate,
      getRandomData,
      connectDevice,
      disconnectDevice,
      addCustomDevice
    }
  }
}
</script> 