<template>
  <div class="appointment-calendar">
    <!-- Header avec navigation -->
    <div class="calendar-header bg-white shadow-sm rounded-lg p-4 mb-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <h2 class="text-2xl font-bold text-gray-900">📅 Calendrier des Rendez-vous</h2>
          <div class="flex space-x-2">
            <button 
              @click="previousMonth" 
              class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors"
            >
              ←
            </button>
            <span class="text-lg font-semibold text-gray-700">{{ currentMonthYear }}</span>
            <button 
              @click="nextMonth" 
              class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors"
            >
              →
            </button>
          </div>
        </div>
        
        <div class="flex space-x-4">
          <button 
            @click="showToday" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Aujourd'hui
          </button>
          <button 
            @click="showCreateModal = true" 
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            + Nouveau RDV
          </button>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div class="filters bg-white shadow-sm rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select v-model="filters.status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Tous les statuts</option>
            <option value="Planifié">Planifié</option>
            <option value="Confirmé">Confirmé</option>
            <option value="Enregistré">Enregistré</option>
            <option value="En cours">En cours</option>
            <option value="Terminé">Terminé</option>
            <option value="Annulé">Annulé</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
          <select v-model="filters.doctor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Tous les médecins</option>
            <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
              {{ doctor.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Athlète</label>
          <select v-model="filters.athlete_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Tous les athlètes</option>
            <option v-for="athlete in athletes" :key="athlete.id" :value="athlete.id">
              {{ athlete.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
          <input 
            type="date" 
            v-model="filters.date" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2"
          >
        </div>
      </div>
    </div>

    <!-- Calendrier -->
    <div class="calendar-container bg-white shadow-sm rounded-lg p-6">
      <!-- En-têtes des jours -->
      <div class="grid grid-cols-7 gap-1 mb-4">
        <div 
          v-for="day in weekDays" 
          :key="day" 
          class="text-center font-semibold text-gray-600 py-2"
        >
          {{ day }}
        </div>
      </div>

      <!-- Grille du calendrier -->
      <div class="grid grid-cols-7 gap-1">
        <div 
          v-for="day in calendarDays" 
          :key="day.date" 
          class="min-h-32 border border-gray-200 p-2"
          :class="{
            'bg-gray-50': !day.isCurrentMonth,
            'bg-blue-50': day.isToday
          }"
        >
          <!-- Numéro du jour -->
          <div class="text-sm font-medium mb-2" :class="{
            'text-gray-400': !day.isCurrentMonth,
            'text-blue-600 font-bold': day.isToday
          }">
            {{ day.dayNumber }}
          </div>

          <!-- Rendez-vous du jour -->
          <div class="space-y-1">
            <div 
              v-for="appointment in getAppointmentsForDay(day.date)" 
              :key="appointment.id"
              class="text-xs p-1 rounded cursor-pointer transition-colors"
              :class="getAppointmentClasses(appointment)"
              @click="showAppointmentDetails(appointment)"
            >
              <div class="font-medium truncate">{{ appointment.athlete_name }}</div>
              <div class="text-xs opacity-75">{{ appointment.start_time }}</div>
              <div class="flex justify-between items-center mt-1">
                <span class="text-xs">{{ appointment.doctor_name }}</span>
                <button 
                  v-if="appointment.canCheckIn"
                  @click.stop="checkInPatient(appointment)"
                  class="text-xs bg-green-500 text-white px-1 py-0.5 rounded hover:bg-green-600"
                >
                  ✓ Check-in
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de détails du rendez-vous -->
    <div v-if="selectedAppointment" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Détails du Rendez-vous</h3>
          <button @click="selectedAppointment = null" class="text-gray-500 hover:text-gray-700">
            ✕
          </button>
        </div>
        
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Athlète</label>
            <p class="text-sm text-gray-900">{{ selectedAppointment.athlete_name }}</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Médecin</label>
            <p class="text-sm text-gray-900">{{ selectedAppointment.doctor_name }}</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Date et heure</label>
            <p class="text-sm text-gray-900">{{ formatDateTime(selectedAppointment.appointment_date) }}</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <p class="text-sm text-gray-900">{{ selectedAppointment.appointment_type }}</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Statut</label>
            <span 
              class="inline-block px-2 py-1 text-xs rounded-full"
              :class="getStatusClasses(selectedAppointment.status)"
            >
              {{ selectedAppointment.status }}
            </span>
          </div>
          
          <div v-if="selectedAppointment.reason">
            <label class="block text-sm font-medium text-gray-700">Motif</label>
            <p class="text-sm text-gray-900">{{ selectedAppointment.reason }}</p>
          </div>
        </div>
        
        <div class="flex space-x-3 mt-6">
          <button 
            v-if="selectedAppointment.canCheckIn"
            @click="checkInPatient(selectedAppointment)"
            class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700"
          >
            Enregistrer le patient
          </button>
          
          <button 
            v-if="selectedAppointment.status === 'Planifié'"
            @click="confirmAppointment(selectedAppointment)"
            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
          >
            Confirmer
          </button>
          
          <button 
            v-if="['Planifié', 'Confirmé'].includes(selectedAppointment.status)"
            @click="cancelAppointment(selectedAppointment)"
            class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de création de rendez-vous -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Nouveau Rendez-vous</h3>
          <button @click="showCreateModal = false" class="text-gray-500 hover:text-gray-700">
            ✕
          </button>
        </div>
        
        <form @submit.prevent="createAppointment" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Athlète *</label>
            <select v-model="newAppointment.athlete_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Sélectionner un athlète</option>
              <option v-for="athlete in athletes" :key="athlete.id" :value="athlete.id">
                {{ athlete.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
            <select v-model="newAppointment.doctor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Sélectionner un médecin</option>
              <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                {{ doctor.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date et heure *</label>
            <input 
              type="datetime-local" 
              v-model="newAppointment.appointment_date" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durée (minutes) *</label>
            <input 
              type="number" 
              v-model="newAppointment.duration_minutes" 
              min="15" 
              max="240" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
            <select v-model="newAppointment.appointment_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Sélectionner un type</option>
              <option value="consultation">Consultation</option>
              <option value="emergency">Urgence</option>
              <option value="follow_up">Suivi</option>
              <option value="pre_season">Pré-saison</option>
              <option value="post_match">Post-match</option>
              <option value="rehabilitation">Rééducation</option>
              <option value="routine_checkup">Contrôle de routine</option>
              <option value="injury_assessment">Évaluation de blessure</option>
              <option value="cardiac_evaluation">Évaluation cardiaque</option>
              <option value="concussion_assessment">Évaluation de commotion</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
            <textarea 
              v-model="newAppointment.reason" 
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-3 py-2"
              placeholder="Motif du rendez-vous..."
            ></textarea>
          </div>
          
          <div class="flex space-x-3">
            <button 
              type="submit" 
              class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700"
            >
              Créer le RDV
            </button>
            <button 
              type="button" 
              @click="showCreateModal = false"
              class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'AppointmentCalendar',
  setup() {
    const appointments = ref([])
    const doctors = ref([])
    const athletes = ref([])
    const currentDate = ref(new Date())
    const selectedAppointment = ref(null)
    const showCreateModal = ref(false)
    const filters = ref({
      status: '',
      doctor_id: '',
      athlete_id: '',
      date: ''
    })

    const newAppointment = ref({
      athlete_id: '',
      doctor_id: '',
      appointment_date: '',
      duration_minutes: 30,
      appointment_type: '',
      reason: ''
    })

    const weekDays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

    const currentMonthYear = computed(() => {
      return currentDate.value.toLocaleDateString('fr-FR', { 
        month: 'long', 
        year: 'numeric' 
      })
    })

    const calendarDays = computed(() => {
      const year = currentDate.value.getFullYear()
      const month = currentDate.value.getMonth()
      const firstDay = new Date(year, month, 1)
      const lastDay = new Date(year, month + 1, 0)
      const startDate = new Date(firstDay)
      startDate.setDate(startDate.getDate() - (firstDay.getDay() || 7) + 1)
      
      const days = []
      const today = new Date()
      
      for (let i = 0; i < 42; i++) {
        const date = new Date(startDate)
        date.setDate(startDate.getDate() + i)
        
        days.push({
          date: date.toISOString().split('T')[0],
          dayNumber: date.getDate(),
          isCurrentMonth: date.getMonth() === month,
          isToday: date.toDateString() === today.toDateString()
        })
      }
      
      return days
    })

    const loadAppointments = async () => {
      try {
        const params = { ...filters.value }
        const response = await axios.get('/appointments', { params })
        appointments.value = response.data.data
      } catch (error) {
        console.error('Erreur lors du chargement des rendez-vous:', error)
      }
    }

    const loadDoctors = async () => {
      try {
        const response = await axios.get('/api/doctors')
        doctors.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des médecins:', error)
      }
    }

    const loadAthletes = async () => {
      try {
        const response = await axios.get('/api/athletes')
        athletes.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des athlètes:', error)
      }
    }

    const getAppointmentsForDay = (date) => {
      return appointments.value.filter(appointment => 
        appointment.appointment_date.startsWith(date)
      )
    }

    const getAppointmentClasses = (appointment) => {
      const baseClasses = 'border-l-4'
      const statusClasses = {
        'Planifié': 'bg-blue-50 border-blue-400 hover:bg-blue-100',
        'Confirmé': 'bg-green-50 border-green-400 hover:bg-green-100',
        'Enregistré': 'bg-yellow-50 border-yellow-400 hover:bg-yellow-100',
        'En cours': 'bg-red-50 border-red-400 hover:bg-red-100',
        'Terminé': 'bg-gray-50 border-gray-400 hover:bg-gray-100',
        'Annulé': 'bg-gray-50 border-gray-400 hover:bg-gray-100'
      }
      return `${baseClasses} ${statusClasses[appointment.status] || statusClasses['Planifié']}`
    }

    const getStatusClasses = (status) => {
      const classes = {
        'Planifié': 'bg-blue-100 text-blue-800',
        'Confirmé': 'bg-green-100 text-green-800',
        'Enregistré': 'bg-yellow-100 text-yellow-800',
        'En cours': 'bg-red-100 text-red-800',
        'Terminé': 'bg-gray-100 text-gray-800',
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

    const previousMonth = () => {
      currentDate.value.setMonth(currentDate.value.getMonth() - 1)
      loadAppointments()
    }

    const nextMonth = () => {
      currentDate.value.setMonth(currentDate.value.getMonth() + 1)
      loadAppointments()
    }

    const showToday = () => {
      currentDate.value = new Date()
      loadAppointments()
    }

    const showAppointmentDetails = (appointment) => {
      selectedAppointment.value = appointment
    }

    const checkInPatient = async (appointment) => {
      try {
        const response = await axios.post(`/appointments/${appointment.id}/check-in`)
        if (response.data.success) {
          await loadAppointments()
          selectedAppointment.value = null
          // Afficher une notification de succès
        }
      } catch (error) {
        console.error('Erreur lors du check-in:', error)
      }
    }

    const confirmAppointment = async (appointment) => {
      try {
        const response = await axios.post(`/appointments/${appointment.id}/confirm`)
        if (response.data.success) {
          await loadAppointments()
          selectedAppointment.value = null
        }
      } catch (error) {
        console.error('Erreur lors de la confirmation:', error)
      }
    }

    const cancelAppointment = async (appointment) => {
      try {
        const response = await axios.post(`/appointments/${appointment.id}/cancel`)
        if (response.data.success) {
          await loadAppointments()
          selectedAppointment.value = null
        }
      } catch (error) {
        console.error('Erreur lors de l\'annulation:', error)
      }
    }

    const createAppointment = async () => {
      try {
        await axios.post('/appointments', newAppointment.value)
        showCreateModal.value = false
        await loadAppointments()
        // Réinitialiser le formulaire
        newAppointment.value = {
          athlete_id: '',
          doctor_id: '',
          appointment_date: '',
          duration_minutes: 30,
          appointment_type: '',
          reason: ''
        }
      } catch (error) {
        console.error('Erreur lors de la création du rendez-vous:', error)
      }
    }

    onMounted(() => {
      loadAppointments()
      loadDoctors()
      loadAthletes()
    })

    return {
      appointments,
      doctors,
      athletes,
      currentDate,
      selectedAppointment,
      showCreateModal,
      filters,
      newAppointment,
      weekDays,
      currentMonthYear,
      calendarDays,
      getAppointmentsForDay,
      getAppointmentClasses,
      getStatusClasses,
      formatDateTime,
      previousMonth,
      nextMonth,
      showToday,
      showAppointmentDetails,
      checkInPatient,
      confirmAppointment,
      cancelAppointment,
      createAppointment
    }
  }
}
</script>

<style scoped>
.appointment-calendar {
  @apply max-w-7xl mx-auto p-6;
}

.calendar-container {
  @apply overflow-x-auto;
}

/* Responsive design */
@media (max-width: 768px) {
  .calendar-header {
    @apply flex-col space-y-4;
  }
  
  .filters {
    @apply grid-cols-1;
  }
}
</style> 