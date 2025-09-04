<template>
  <div class="match-stats">
    <!-- En-tête des statistiques de match -->
    <div class="match-header bg-gradient-to-r from-green-900 to-blue-900 rounded-xl p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-white mb-2">
            <i class="fas fa-futbol mr-3"></i>Statistiques de Match
          </h2>
          <p class="text-green-200">Analyse détaillée des performances en compétition</p>
        </div>
        <div class="text-right">
          <div class="text-4xl font-bold text-yellow-400">{{ currentMatch.rating || '8.5' }}</div>
          <div class="text-sm text-green-200">Rating du Match</div>
        </div>
      </div>
    </div>

    <!-- Statistiques principales du match -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-gray-800 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-red-400 mb-2">{{ currentMatch.goals || 0 }}</div>
        <div class="text-sm text-gray-400">Buts</div>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-blue-400 mb-2">{{ currentMatch.assists || 0 }}</div>
        <div class="text-sm text-gray-400">Passes</div>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-yellow-400 mb-2">{{ currentMatch.shots || 0 }}</div>
        <div class="text-sm text-gray-400">Tirs</div>
      </div>
      <div class="bg-gray-800 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-green-400 mb-2">{{ currentMatch.passes || 0 }}</div>
        <div class="text-sm text-gray-400">Passes</div>
      </div>
    </div>

    <!-- Détails des actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Actions offensives -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-bullseye mr-2"></i>Actions Offensives
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Tirs cadrés</span>
            <span class="text-lg font-bold text-red-400">{{ offensiveActions.shotsOnTarget || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-red-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (offensiveActions.shotsOnTarget / offensiveActions.totalShots * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Dribbles réussis</span>
            <span class="text-lg font-bold text-purple-400">{{ offensiveActions.dribbles || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-purple-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (offensiveActions.dribbles / offensiveActions.totalDribbles * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Centres réussis</span>
            <span class="text-lg font-bold text-yellow-400">{{ offensiveActions.crosses || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (offensiveActions.crosses / offensiveActions.totalCrosses * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Touches de balle</span>
            <span class="text-lg font-bold text-blue-400">{{ offensiveActions.touches || 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Actions défensives -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-shield-alt mr-2"></i>Actions Défensives
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Tacles réussis</span>
            <span class="text-lg font-bold text-blue-400">{{ defensiveActions.tackles || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (defensiveActions.tackles / defensiveActions.totalTackles * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Interceptions</span>
            <span class="text-lg font-bold text-green-400">{{ defensiveActions.interceptions || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (defensiveActions.interceptions / defensiveActions.totalInterceptions * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Dégagements</span>
            <span class="text-lg font-bold text-yellow-400">{{ defensiveActions.clearances || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-1000" 
                 :style="{ width: (defensiveActions.clearances / defensiveActions.totalClearances * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Duels gagnés</span>
            <span class="text-lg font-bold text-purple-400">{{ defensiveActions.duelsWon || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques de match -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Graphique de possession -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-chart-pie mr-2"></i>Possession et Contrôle
        </h3>
        <div class="h-64">
          <canvas ref="possessionChart"></canvas>
        </div>
      </div>

      <!-- Graphique de pression -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-chart-bar mr-2"></i>Pression et Intensité
        </h3>
        <div class="h-64">
          <canvas ref="pressureChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Statistiques comparatives avec l'adversaire -->
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
      <h3 class="text-lg font-bold mb-4 text-purple-300">
        <i class="fas fa-balance-scale mr-2"></i>Comparaison avec l'Adversaire
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-400 mb-2">{{ comparison.possession }}%</div>
          <div class="text-sm text-gray-400">Possession</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-blue-500 h-2 rounded-full" :style="{ width: comparison.possession + '%' }"></div>
          </div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-400 mb-2">{{ comparison.passes }}</div>
          <div class="text-sm text-gray-400">Passes réussies</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-green-500 h-2 rounded-full" :style="{ width: (comparison.passes / comparison.totalPasses * 100) + '%' }"></div>
          </div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-400 mb-2">{{ comparison.shots }}</div>
          <div class="text-sm text-gray-400">Tirs cadrés</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-yellow-500 h-2 rounded-full" :style="{ width: (comparison.shots / comparison.totalShots * 100) + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Métriques de performance physique -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Distance et vitesse -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-running mr-2"></i>Performance Physique
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Distance parcourue</span>
            <span class="text-lg font-bold text-green-400">{{ physicalStats.distance || 0 }}km</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (physicalStats.distance / 12 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Vitesse maximale</span>
            <span class="text-lg font-bold text-yellow-400">{{ physicalStats.maxSpeed || 0 }}km/h</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (physicalStats.maxSpeed / 35 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Sprints</span>
            <span class="text-lg font-bold text-red-400">{{ physicalStats.sprints || 0 }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (physicalStats.sprints / 100 * 100) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Zones de jeu -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-map-marked-alt mr-2"></i>Zones de Jeu
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone offensive</span>
            <span class="text-lg font-bold text-red-400">{{ zoneStats.offensive || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.offensive + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone centrale</span>
            <span class="text-lg font-bold text-blue-400">{{ zoneStats.central || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.central + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone défensive</span>
            <span class="text-lg font-bold text-green-400">{{ zoneStats.defensive || 0 }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.defensive + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'MatchStats',
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      possessionChart: null,
      pressureChart: null,
      currentMatch: {
        rating: 8.5,
        goals: 2,
        assists: 1,
        shots: 5,
        passes: 45
      },
      offensiveActions: {
        shotsOnTarget: 4,
        totalShots: 5,
        dribbles: 8,
        totalDribbles: 12,
        crosses: 3,
        totalCrosses: 5,
        touches: 67
      },
      defensiveActions: {
        tackles: 3,
        totalTackles: 4,
        interceptions: 5,
        totalInterceptions: 7,
        clearances: 2,
        totalClearances: 3,
        duelsWon: 12
      },
      comparison: {
        possession: 58,
        passes: 45,
        totalPasses: 52,
        shots: 4,
        totalShots: 5
      },
      physicalStats: {
        distance: 11.2,
        maxSpeed: 32.8,
        sprints: 78
      },
      zoneStats: {
        offensive: 65,
        central: 25,
        defensive: 10
      }
    }
  },
  mounted() {
    this.initCharts()
  },
  methods: {
    initCharts() {
      this.initPossessionChart()
      this.initPressureChart()
    },
    
    initPossessionChart() {
      const ctx = this.$refs.possessionChart.getContext('2d')
      this.possessionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Possession', 'Contrôle', 'Pression'],
          datasets: [{
            data: [58, 25, 17],
            backgroundColor: [
              'rgba(59, 130, 246, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
              'rgba(59, 130, 246, 1)',
              'rgba(34, 197, 94, 1)',
              'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#D1D5DB' }
            }
          }
        }
      })
    },
    
    initPressureChart() {
      const ctx = this.$refs.pressureChart.getContext('2d')
      this.pressureChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['1-15', '16-30', '31-45', '46-60', '61-75', '76-90'],
          datasets: [{
            label: 'Pression défensive',
            data: [75, 82, 78, 85, 90, 88],
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: 'rgba(239, 68, 68, 1)',
            borderWidth: 2
          }, {
            label: 'Intensité offensive',
            data: [65, 78, 82, 75, 80, 85],
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2
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
              max: 100,
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
    }
  },
  
  beforeUnmount() {
    if (this.possessionChart) {
      this.possessionChart.destroy()
    }
    if (this.pressureChart) {
      this.pressureChart.destroy()
    }
  }
}
</script>

<style scoped>
.match-stats {
  @apply text-gray-100;
}

.match-header {
  background: linear-gradient(135deg, #065f46 0%, #1e3a8a 100%);
}

/* Animations pour les barres de progression */
.w-full.bg-gray-700 > div {
  @apply transition-all duration-1000 ease-out;
}

/* Hover effects */
.space-y-4 > div:hover {
  @apply transform scale-105 transition-transform duration-200;
}

/* Style pour les graphiques */
canvas {
  @apply transition-opacity duration-300;
}

/* Responsive design */
@media (max-width: 768px) {
  .grid {
    @apply grid-cols-1;
  }
}
</style>

