<template>
  <div class="performance-dashboard">
    <!-- Navigation des onglets de performance -->
    <div class="performance-tabs mb-6">
      <div class="flex space-x-1 bg-gray-800 rounded-lg p-1">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
            activeTab === tab.id 
              ? 'bg-blue-600 text-white shadow-lg' 
              : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700'
          ]"
        >
          <i :class="tab.icon + ' mr-2'"></i>
          {{ tab.name }}
        </button>
      </div>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content">
      <!-- Onglet Vue d'ensemble -->
      <div v-if="activeTab === 'overview'" class="space-y-6">
        <PlayerPerformance :player="player" />
      </div>

      <!-- Onglet Statistiques avancées -->
      <div v-if="activeTab === 'advanced'" class="space-y-6">
        <AdvancedStats :player="player" />
      </div>

      <!-- Onglet Statistiques de match -->
      <div v-if="activeTab === 'match'" class="space-y-6">
        <MatchStats :player="player" />
      </div>

      <!-- Onglet Analyse comparative -->
      <div v-if="activeTab === 'comparison'" class="space-y-6">
        <ComparisonAnalysis :player="player" />
      </div>

      <!-- Onglet Tendances -->
      <div v-if="activeTab === 'trends'" class="space-y-6">
        <TrendAnalysis :player="player" />
      </div>
    </div>

    <!-- Indicateurs de performance rapides -->
    <div class="quick-stats mt-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-white mb-1">{{ quickStats.overallRating }}</div>
          <div class="text-xs text-blue-200">Rating Global</div>
        </div>
        <div class="bg-gradient-to-r from-green-900 to-green-700 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-white mb-1">{{ quickStats.seasonGoals }}</div>
          <div class="text-xs text-green-200">Buts Saison</div>
        </div>
        <div class="bg-gradient-to-r from-purple-900 to-purple-700 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-white mb-1">{{ quickStats.seasonAssists }}</div>
          <div class="text-xs text-purple-200">Passes Saison</div>
        </div>
        <div class="bg-gradient-to-r from-red-900 to-red-700 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-white mb-1">{{ quickStats.formRating }}</div>
          <div class="text-xs text-red-200">Forme Actuelle</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PlayerPerformance from './PlayerPerformance.vue'
import AdvancedStats from './AdvancedStats.vue'
import MatchStats from './MatchStats.vue'
import ComparisonAnalysis from './ComparisonAnalysis.vue'
import TrendAnalysis from './TrendAnalysis.vue'

export default {
  name: 'PerformanceDashboard',
  components: {
    PlayerPerformance,
    AdvancedStats,
    MatchStats,
    ComparisonAnalysis,
    TrendAnalysis
  },
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      activeTab: 'overview',
      tabs: [
        { id: 'overview', name: 'Vue d\'ensemble', icon: 'fas fa-chart-line' },
        { id: 'advanced', name: 'Statistiques avancées', icon: 'fas fa-chart-bar' },
        { id: 'match', name: 'Statistiques de match', icon: 'fas fa-futbol' },
        { id: 'comparison', name: 'Analyse comparative', icon: 'fas fa-balance-scale' },
        { id: 'trends', name: 'Tendances', icon: 'fas fa-trending-up' }
      ],
      quickStats: {
        overallRating: 88,
        seasonGoals: 24,
        seasonAssists: 18,
        formRating: 8.7
      }
    }
  },
  methods: {
    // Méthodes pour gérer les interactions
  }
}
</script>

<style scoped>
.performance-dashboard {
  @apply text-gray-100;
}

.performance-tabs {
  @apply sticky top-0 z-10 bg-gray-900/95 backdrop-blur-sm;
}

.tab-content {
  @apply min-h-screen;
}

/* Animations de transition */
.tab-content > div {
  @apply transition-all duration-300 ease-in-out;
}

/* Style pour les onglets actifs */
.performance-tabs button.active {
  @apply transform scale-105;
}

/* Responsive design */
@media (max-width: 768px) {
  .performance-tabs {
    @apply overflow-x-auto;
  }
  
  .tabs {
    @apply flex-nowrap;
  }
}
</style>

