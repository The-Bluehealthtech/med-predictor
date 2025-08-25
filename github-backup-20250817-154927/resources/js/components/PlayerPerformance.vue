<template>
  <div class="player-performance">
    <!-- En-tête des Performances -->
    <div class="performance-header bg-gradient-to-r from-blue-900 to-purple-900 rounded-xl p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-white mb-2">
            <i class="fas fa-chart-line mr-3"></i>Centre de Performances
          </h2>
          <p class="text-blue-200">Analyse complète des performances et statistiques</p>
        </div>
        <div class="text-right">
          <div class="text-4xl font-bold text-yellow-400">{{ player.overall_rating || 88 }}</div>
          <div class="text-sm text-blue-200">Rating Global</div>
        </div>
      </div>
    </div>

    <!-- Grille principale des performances -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Statistiques Offensives -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-400">
          <i class="fas fa-bullseye mr-2"></i>Statistiques Offensives
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Buts marqués</span>
            <span class="text-2xl font-bold text-red-400">{{ offensiveStats.goals || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Passes décisives</span>
            <span class="text-2xl font-bold text-blue-400">{{ offensiveStats.assists || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Tirs cadrés</span>
            <span class="text-2xl font-bold text-yellow-400">{{ offensiveStats.shotsOnTarget || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Précision des tirs</span>
            <span class="text-2xl font-bold text-green-400">{{ offensiveStats.shotAccuracy || 0 }}%</span>
          </div>
        </div>
      </div>

      <!-- Statistiques Défensives -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-400">
          <i class="fas fa-shield-alt mr-2"></i>Statistiques Défensives
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Tacles réussis</span>
            <span class="text-2xl font-bold text-blue-400">{{ defensiveStats.tackles || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Interceptions</span>
            <span class="text-2xl font-bold text-green-400">{{ defensiveStats.interceptions || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Dégagements</span>
            <span class="text-2xl font-bold text-yellow-400">{{ defensiveStats.clearances || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Duels gagnés</span>
            <span class="text-2xl font-bold text-purple-400">{{ defensiveStats.duelsWon || 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Statistiques Physiques -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-400">
          <i class="fas fa-running mr-2"></i>Statistiques Physiques
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Distance parcourue</span>
            <span class="text-2xl font-bold text-green-400">{{ physicalStats.distance || 0 }}km</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Vitesse max</span>
            <span class="text-2xl font-bold text-yellow-400">{{ physicalStats.maxSpeed || 0 }}km/h</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Sprint</span>
            <span class="text-2xl font-bold text-red-400">{{ physicalStats.sprints || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Endurance</span>
            <span class="text-2xl font-bold text-blue-400">{{ physicalStats.endurance || 0 }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques de performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Évolution des performances -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-chart-line mr-2"></i>Évolution des Performances
        </h3>
        <div class="h-64">
          <canvas ref="performanceChart"></canvas>
        </div>
      </div>

      <!-- Radar des compétences -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-radar-chart mr-2"></i>Radar des Compétences
        </h3>
        <div class="h-64">
          <canvas ref="skillsRadar"></canvas>
        </div>
      </div>
    </div>

    <!-- Statistiques détaillées par match -->
    <div class="bg-gray-800 rounded-xl p-6">
      <h3 class="text-lg font-bold mb-4 text-purple-300">
        <i class="fas fa-list-alt mr-2"></i>Performances par Match
      </h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-700">
              <th class="text-left py-2 text-gray-300">Match</th>
              <th class="text-center py-2 text-gray-300">Buts</th>
              <th class="text-center py-2 text-gray-300">Passes</th>
              <th class="text-center py-2 text-gray-300">Tirs</th>
              <th class="text-center py-2 text-gray-300">Précision</th>
              <th class="text-center py-2 text-gray-300">Distance</th>
              <th class="text-center py-2 text-gray-300">Rating</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="match in matchStats" :key="match.id" class="border-b border-gray-700/50">
              <td class="py-2 text-gray-300">{{ match.opponent }}</td>
              <td class="text-center py-2 text-red-400 font-bold">{{ match.goals }}</td>
              <td class="text-center py-2 text-blue-400 font-bold">{{ match.assists }}</td>
              <td class="text-center py-2 text-gray-300">{{ match.shots }}</td>
              <td class="text-center py-2 text-green-400">{{ match.accuracy }}%</td>
              <td class="text-center py-2 text-yellow-400">{{ match.distance }}km</td>
              <td class="text-center py-2">
                <span class="px-2 py-1 rounded text-xs font-bold" 
                      :class="getRatingClass(match.rating)">
                  {{ match.rating }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Métriques avancées -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Efficacité offensive -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-chart-pie mr-2"></i>Efficacité Offensive
        </h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Conversion des occasions</span>
            <span class="text-lg font-bold text-red-400">{{ offensiveEfficiency.conversionRate || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-red-500 h-2 rounded-full" :style="{ width: (offensiveEfficiency.conversionRate || 0) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Précision des passes</span>
            <span class="text-lg font-bold text-blue-400">{{ offensiveEfficiency.passAccuracy || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full" :style="{ width: (offensiveEfficiency.passAccuracy || 0) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Dribbles réussis</span>
            <span class="text-lg font-bold text-green-400">{{ offensiveEfficiency.dribbleSuccess || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full" :style="{ width: (offensiveEfficiency.dribbleSuccess || 0) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Analyse tactique -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-purple-300">
          <i class="fas fa-chess mr-2"></i>Analyse Tactique
        </h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Positionnement</span>
            <span class="text-lg font-bold text-purple-400">{{ tacticalAnalysis.positioning || 0 }}/100</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-purple-500 h-2 rounded-full" :style="{ width: (tacticalAnalysis.positioning || 0) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Vision du jeu</span>
            <span class="text-lg font-bold text-blue-400">{{ tacticalAnalysis.vision || 0 }}/100</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full" :style="{ width: (tacticalAnalysis.vision || 0) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Décisions</span>
            <span class="text-lg font-bold text-green-400">{{ tacticalAnalysis.decisionMaking || 0 }}/100</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full" :style="{ width: (tacticalAnalysis.decisionMaking || 0) + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'PlayerPerformance',
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      performanceChart: null,
      skillsRadar: null,
      offensiveStats: {
        goals: 24,
        assists: 18,
        shotsOnTarget: 67,
        shotAccuracy: 78
      },
      defensiveStats: {
        tackles: 45,
        interceptions: 32,
        clearances: 28,
        duelsWon: 156
      },
      physicalStats: {
        distance: 12.8,
        maxSpeed: 34.2,
        sprints: 89,
        endurance: 92
      },
      matchStats: [
        { id: 1, opponent: 'Real Madrid', goals: 2, assists: 1, shots: 5, accuracy: 80, distance: 11.2, rating: 9.2 },
        { id: 2, opponent: 'Barcelona', goals: 1, assists: 0, shots: 3, accuracy: 67, distance: 12.1, rating: 7.8 },
        { id: 3, opponent: 'Atletico', goals: 0, assists: 2, shots: 2, accuracy: 50, distance: 10.8, rating: 8.1 },
        { id: 4, opponent: 'Sevilla', goals: 3, assists: 1, shots: 6, accuracy: 83, distance: 11.9, rating: 9.5 }
      ],
      offensiveEfficiency: {
        conversionRate: 23,
        passAccuracy: 87,
        dribbleSuccess: 68
      },
      tacticalAnalysis: {
        positioning: 85,
        vision: 92,
        decisionMaking: 88
      }
    }
  },
  mounted() {
    this.initCharts()
  },
  methods: {
    initCharts() {
      this.initPerformanceChart()
      this.initSkillsRadar()
    },
    
    initPerformanceChart() {
      const ctx = this.$refs.performanceChart.getContext('2d')
      this.performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6', 'J7', 'J8'],
          datasets: [{
            label: 'Rating Match',
            data: [8.2, 7.8, 9.1, 8.5, 7.9, 8.8, 9.2, 8.7],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }, {
            label: 'Buts + Passes',
            data: [2, 1, 3, 2, 1, 2, 3, 2],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: '#D1D5DB' }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#374151' },
              ticks: { color: '#D1D5DB' }
            },
            x: {
              grid: { color: '#374151' },
              ticks: { color: '#D1D5DB' }
            }
          }
        }
      })
    },
    
    initSkillsRadar() {
      const ctx = this.$refs.skillsRadar.getContext('2d')
      this.skillsRadar = new Chart(ctx, {
        type: 'radar',
        data: {
          labels: ['Vitesse', 'Technique', 'Physique', 'Mental', 'Tactique', 'Finition'],
          datasets: [{
            label: 'Compétences actuelles',
            data: [88, 92, 85, 90, 87, 94],
            borderColor: '#8B5CF6',
            backgroundColor: 'rgba(139, 92, 244, 0.2)',
            pointBackgroundColor: '#8B5CF6',
            pointBorderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: '#D1D5DB' }
            }
          },
          scales: {
            r: {
              beginAtZero: true,
              max: 100,
              grid: { color: '#374151' },
              ticks: { 
                color: '#D1D5DB',
                stepSize: 20
              },
              pointLabels: { color: '#D1D5DB' }
            }
          }
        }
      })
    },
    
    getRatingClass(rating) {
      if (rating >= 9.0) return 'bg-green-600 text-white'
      if (rating >= 8.0) return 'bg-blue-600 text-white'
      if (rating >= 7.0) return 'bg-yellow-600 text-white'
      return 'bg-red-600 text-white'
    }
  },
  
  beforeUnmount() {
    if (this.performanceChart) {
      this.performanceChart.destroy()
    }
    if (this.skillsRadar) {
      this.skillsRadar.destroy()
    }
  }
}
</script>

<style scoped>
.player-performance {
  @apply text-gray-100;
}

.performance-header {
  background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%);
}

/* Animations pour les statistiques */
.space-y-4 > div {
  @apply transition-all duration-300 ease-in-out;
}

.space-y-4 > div:hover {
  @apply transform scale-105;
}

/* Style pour les tableaux */
table th {
  @apply font-semibold;
}

table td {
  @apply transition-colors duration-200;
}

table tr:hover {
  @apply bg-gray-700/50;
}

/* Barres de progression */
.w-full.bg-gray-700 {
  @apply transition-all duration-500 ease-out;
}
</style>

