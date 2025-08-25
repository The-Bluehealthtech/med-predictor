<template>
  <div class="manual-scheduler">
    <!-- Header -->
    <div class="scheduler-header">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">
        {{ $t('auto.key1') }}
      </h2>
      
      <div class="flex gap-4 mb-6">
        <button
          @click="generateAutomaticSchedule"
          :disabled="isGenerating"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          <span v-if="isGenerating">{{ $t('auto.key472') }}</span>
          <span v-else>{{ $t('auto.key29') }} Automatiquement</span>
        </button>
        
        <button
          @click="clearSchedule"
          :disabled="isClearing"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
        >
          <span v-if="isClearing">{{ $t('auto.key473') }}</span>
          <span v-else>{{ $t('auto.key474') }}</span>
        </button>
      </div>
    </div>

    <!-- Schedule Grid -->
    <div class="schedule-grid">
      <div
        {{ $t('auto.key2') }}
        :key="matchday"
        {{ $t('auto.key3') }}
      >
        <div class="matchday-header">
          <h3 class="text-lg font-semibold">Journée {{ matchday }}</h3>
          <button
            @click="openAddMatchModal(matchday)"
            {{ $t('auto.key4') }}
          >
            {{ $t('auto.key5') }}
          </button>
        </div>

        <div class="matches-list">
          <div
            {{ $t('auto.key6') }}
            :key="match.id"
            {{ $t('auto.key7') }}
          >
            <div class="match-info">
              <div class="teams">
                <span class="home-team">{{ match.home_team.name }}</span>
                <span class="vs">{{ $t('auto.key475') }}</span>
                <span class="away-team">{{ match.away_team.name }}</span>
              </div>
              <div class="match-details">
                <span class="date">{{ formatDate(match.scheduled_at) }}</span>
                <span class="venue">{{ match.venue }}</span>
              </div>
            </div>
            
            <div class="match-actions">
              <button
                @click="editMatch(match)"
                {{ $t('auto.key8') }}
              >
                {{ $t('auto.key9') }}
              </button>
              <button
                @click="deleteMatch(match)"
                {{ $t('auto.key10') }}
              >
                {{ $t('auto.key11') }}
              </button>
            </div>
          </div>

          <div
            {{ $t('auto.key12') }}
            {{ $t('auto.key13') }}
          >
            {{ $t('auto.key14') }}
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Match Modal -->
    <AddMatchModal
      {{ $t('auto.key15') }}
      :competition="competition"
      :matchday="selectedMatchday"
      :match="editingMatch"
      :venues="venues"
      :teams="teams"
      @close="closeMatchModal"
      @match-created="onMatchCreated"
      @match-updated="onMatchUpdated"
    />

    <!-- Automatic Schedule Modal -->
    <div
      {{ $t('auto.key16') }}
      {{ $t('auto.key17') }}
      @click="closeAutoScheduleModal"
    >
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">{{ $t('auto.key476') }}</h3>
        
        <form @submit.prevent="confirmGenerateSchedule" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('auto.key477') }}</label>
            <input
              {{ $t('auto.key18') }}
              {{ $t('auto.key19') }}
              {{ $t('auto.key20') }}
              {{ $t('auto.key21') }}
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('auto.key478') }}</label>
            <input
              {{ $t('auto.key22') }}
              {{ $t('auto.key23') }}
              {{ $t('auto.key24') }}
              {{ $t('auto.key25') }}
              {{ $t('auto.key20') }}
              {{ $t('auto.key21') }}
            />
          </div>
          
          <div class="flex gap-4">
            <button
              {{ $t('auto.key28') }}
              :disabled="isGenerating"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ $t('auto.key29') }}
            </button>
            <button
              {{ $t('auto.key30') }}
              @click="closeAutoScheduleModal"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400"
            >
              {{ $t('auto.key31') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import AddMatchModal from './AddMatchModal.vue'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'ManualScheduler',
  components: {
    AddMatchModal
  },
  props: {
    competition: {
      type: Object,
      {{ $t('auto.key20') }}: true
    }
  },
  setup(props) {
    const schedule = ref([])
    const teams = ref([])
    const venues = ref([])
    const showMatchModal = ref(false)
    const showAutoScheduleModal = ref(false)
    const selectedMatchday = ref(null)
    const editingMatch = ref(null)
    const isGenerating = ref(false)
    const isClearing = ref(false)
    
    const autoScheduleData = ref({
      start_date: '',
      match_interval_days: 7
    })

    const totalMatchdays = computed(() => {
      if (props.competition.teams_count < 2) return 0
      // Calculate total matchdays for home and away
      return (props.competition.teams_count - 1) * 2
    })

    const loadSchedule = async () => {
      try {
        const response = await axios.get(`/api/calendar/competitions/${props.competition.id}/schedule`)
        schedule.value = response.data.data.schedule
      } catch (error) {
        console.error('Failed to load schedule:', error)
      }
    }

    const loadTeams = async () => {
      try {
        const response = await axios.get(`/api/calendar/competitions/${props.competition.id}/teams`)
        teams.value = response.data.data
      } catch (error) {
        console.error('Failed to load teams:', error)
      }
    }

    const loadVenues = async () => {
      try {
        const response = await axios.get('/api/calendar/venues')
        venues.value = response.data.data
      } catch (error) {
        console.error('Failed to load venues:', error)
      }
    }

    const getMatchesForMatchday = (matchday) => {
      const matchdayData = schedule.value.find(md => md.matchday === matchday)
      return matchdayData ? matchdayData.matches : []
    }

    const openAddMatchModal = (matchday) => {
      selectedMatchday.value = matchday
      editingMatch.value = null
      showMatchModal.value = true
    }

    const editMatch = (match) => {
      editingMatch.value = match
      selectedMatchday.value = match.matchday
      showMatchModal.value = true
    }

    const closeMatchModal = () => {
      showMatchModal.value = false
      editingMatch.value = null
      selectedMatchday.value = null
    }

    const onMatchCreated = (newMatch) => {
      loadSchedule()
      closeMatchModal()
    }

    const onMatchUpdated = (updatedMatch) => {
      loadSchedule()
      closeMatchModal()
    }

    const deleteMatch = async (match) => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer ce match ?')) {
        return
      }

      try {
        await axios.delete(`/api/calendar/matches/${match.id}`)
        await loadSchedule()
      } catch (error) {
        console.error('Failed to delete match:', error)
      }
    }

    const generateAutomaticSchedule = () => {
      showAutoScheduleModal.value = true
    }

    const closeAutoScheduleModal = () => {
      showAutoScheduleModal.value = false
      autoScheduleData.value = {
        start_date: '',
        match_interval_days: 7
      }
    }

    const confirmGenerateSchedule = async () => {
      isGenerating.value = true
      
      try {
        const response = await axios.post(
          `/api/calendar/competitions/${props.competition.id}/generate-full-schedule`,
          autoScheduleData.value
        )
        
        await loadSchedule()
        closeAutoScheduleModal()
        alert('Calendrier généré avec succès !')
      } catch (error) {
        console.error('Failed to generate schedule:', error)
        alert(error.response?.data?.message || 'Erreur lors de la génération du calendrier')
      } finally {
        isGenerating.value = false
      }
    }

    const clearSchedule = async () => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer tout le calendrier ?')) {
        return
      }

      isClearing.value = true
      
      try {
        await axios.delete(`/api/calendar/competitions/${props.competition.id}/schedule`)
        await loadSchedule()
        alert('Calendrier supprimé avec succès !')
      } catch (error) {
        console.error('Failed to clear schedule:', error)
        alert(error.response?.data?.message || 'Erreur lors de la suppression du calendrier')
      } finally {
        isClearing.value = false
      }
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      loadSchedule()
      loadTeams()
      loadVenues()
    })

    return {
      schedule,
      teams,
      venues,
      showMatchModal,
      showAutoScheduleModal,
      selectedMatchday,
      editingMatch,
      isGenerating,
      isClearing,
      autoScheduleData,
      totalMatchdays,
      getMatchesForMatchday,
      openAddMatchModal,
      editMatch,
      closeMatchModal,
      onMatchCreated,
      onMatchUpdated,
      deleteMatch,
      generateAutomaticSchedule,
      closeAutoScheduleModal,
      confirmGenerateSchedule,
      clearSchedule,
      formatDate
    }
  }
}
</script>

<style scoped>
.manual-scheduler {
  @apply p-6;
}

.scheduler-header {
  @apply mb-6;
}

.schedule-grid {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
}

.matchday-card {
  @apply bg-white rounded-lg shadow-md p-4 border;
}

.matchday-header {
  @apply flex justify-between items-center mb-4 pb-2 border-b;
}

.add-match-btn {
  @apply px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700;
}

.matches-list {
  @apply space-y-3;
}

.match-item {
  @apply bg-gray-50 rounded p-3 border-l-4 border-blue-500;
}

.match-info {
  @apply mb-2;
}

.teams {
  @apply font-medium text-gray-800;
}

.vs {
  @apply mx-2 text-gray-500;
}

.match-details {
  @apply text-sm text-gray-600 mt-1;
}

.date {
  @apply mr-4;
}

.match-actions {
  @apply flex gap-2 mt-2;
}

.edit-btn {
  @apply px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600;
}

.delete-btn {
  @apply px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600;
}

.no-matches {
  @apply text-gray-500 text-center py-4 italic;
}

.modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.modal-content {
  @apply bg-white rounded-lg p-6 max-w-md w-full mx-4;
}
</style> 