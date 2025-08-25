<template>
  <div class="advanced-stats">
    <!-- Métriques de performance avancées -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Analyse des zones de jeu -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-map-marked-alt mr-2"></i>Zones de Jeu
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone offensive</span>
            <span class="text-lg font-bold text-red-400">{{ zoneStats.offensive }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.offensive + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone centrale</span>
            <span class="text-lg font-bold text-blue-400">{{ zoneStats.central }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.central + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Zone défensive</span>
            <span class="text-lg font-bold text-green-400">{{ zoneStats.defensive }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: zoneStats.defensive + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Efficacité des actions -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-percentage mr-2"></i>Efficacité des Actions
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Passes réussies</span>
            <span class="text-lg font-bold text-blue-400">{{ actionEfficiency.passes }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: actionEfficiency.passes + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Dribbles réussis</span>
            <span class="text-lg font-bold text-purple-400">{{ actionEfficiency.dribbles }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-purple-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: actionEfficiency.dribbles + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Centres réussis</span>
            <span class="text-lg font-bold text-yellow-400">{{ actionEfficiency.crosses }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: actionEfficiency.crosses + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques spécialisés -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Graphique de chaleur des zones -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-fire mr-2"></i>Zones de Chaleur
        </h3>
        <div class="h-64">
          <canvas ref="heatmapChart"></canvas>
        </div>
      </div>

      <!-- Graphique de progression saisonnière -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-chart-area mr-2"></i>Progression Saisonnière
        </h3>
        <div class="h-64">
          <canvas ref="seasonalChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Statistiques comparatives -->
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
      <h3 class="text-lg font-bold mb-4 text-purple-300">
        <i class="fas fa-balance-scale mr-2"></i>Comparaison avec la Ligue
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-400 mb-2">{{ leagueComparison.goals }}</div>
          <div class="text-sm text-gray-400">Buts (Top 10%)</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-blue-500 h-2 rounded-full" style="width: 90%"></div>
          </div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-400 mb-2">{{ leagueComparison.assists }}</div>
          <div class="text-sm text-gray-400">Passes (Top 15%)</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
          </div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-400 mb-2">{{ leagueComparison.rating }}</div>
          <div class="text-sm text-gray-400">Rating (Top 5%)</div>
          <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-yellow-500 h-2 rounded-full" style="width: 95%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Métriques de pression et intensité -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Pression défensive -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-compress-arrows-alt mr-2"></i>Pression Défensive
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Pressing haut</span>
            <span class="text-lg font-bold text-red-400">{{ defensivePressure.high }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: defensivePressure.high + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Pressing moyen</span>
            <span class="text-lg font-bold text-yellow-400">{{ defensivePressure.medium }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: defensivePressure.medium + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Pressing bas</span>
            <span class="text-lg font-bold text-green-400">{{ defensivePressure.low }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: defensivePressure.low + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Intensité du jeu -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-tachometer-alt mr-2"></i>Intensité du Jeu
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Intensité moyenne</span>
            <span class="text-lg font-bold text-blue-400">{{ gameIntensity.average }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: gameIntensity.average + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Pics d'intensité</span>
            <span class="text-lg font-bold text-red-400">{{ gameIntensity.peaks }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: gameIntensity.peaks + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Récupération</span>
            <span class="text-lg font-bold text-green-400">{{ gameIntensity.recovery }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: gameIntensity.recovery + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'AdvancedStats',
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      heatmapChart: null,
      seasonalChart: null,
      zoneStats: {
        offensive: 65,
        central: 25,
        defensive: 10
      },
      actionEfficiency: {
        passes: 89,
        dribbles: 72,
        crosses: 68
      },
      leagueComparison: {
        goals: 24,
        assists: 18,
        rating: 8.7
      },
      defensivePressure: {
        high: 35,
        medium: 45,
        low: 20
      },
      gameIntensity: {
        average: 78,
        peaks: 92,
        recovery: 85
      }
    }
  },
  mounted() {
    this.initCharts()
  },
  methods: {
    initCharts() {
      this.initHeatmapChart()
      this.initSeasonalChart()
    },
    
    initHeatmapChart() {
      const ctx = this.$refs.heatmapChart.getContext('2d')
      this.heatmapChart = new Chart(ctx, {
        type: 'scatter',
        data: {
          datasets: [{
            label: 'Zones de jeu',
            data: [
              { x: 20, y: 80, r: 15 }, // Zone offensive
              { x: 50, y: 50, r: 10 }, // Zone centrale
              { x: 80, y: 20, r: 8 }   // Zone défensive
            ],
            backgroundColor: [
              'rgba(239, 68, 68, 0.7)',   // Rouge pour offensive
              'rgba(59, 130, 246, 0.7)',  // Bleu pour central
              'rgba(34, 197, 94, 0.7)'    // Vert pour défensif
            ],
            borderColor: [
              'rgba(239, 68, 68, 1)',
              'rgba(59, 130, 246, 1)',
              'rgba(34, 197, 94, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              max: 100,
              grid: { color: '#374151' },
              ticks: { color: '#D1D5DB' }
            },
            y: {
              beginAtZero: true,
              max: 100,
              grid: { color: '#374151' },
              ticks: { color: '#D1D5DB' }
            }
          }
        }
      })
    },
    
    initSeasonalChart() {
      const ctx = this.$refs.seasonalChart.getContext('2d')
      this.seasonalChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Août', 'Sept', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
          datasets: [{
            label: 'Rating moyen',
            data: [7.8, 8.1, 8.3, 8.5, 8.7, 8.9, 8.8, 8.6, 8.4, 8.2],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
          }, {
            label: 'Buts + Passes',
            data: [3, 5, 7, 9, 11, 13, 15, 17, 19, 21],
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
    }
  },
  
  beforeUnmount() {
    if (this.heatmapChart) {
      this.heatmapChart.destroy()
    }
    if (this.seasonalChart) {
      this.seasonalChart.destroy()
    }
  }
}
</script>

<style scoped>
.advanced-stats {
  @apply text-gray-100;
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

