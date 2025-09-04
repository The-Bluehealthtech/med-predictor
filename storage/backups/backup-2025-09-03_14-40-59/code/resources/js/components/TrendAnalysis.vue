<template>
  <div class="trend-analysis">
    <!-- En-tête de l'analyse des tendances -->
    <div class="trend-header bg-gradient-to-r from-orange-900 to-red-900 rounded-xl p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-white mb-2">
            <i class="fas fa-trending-up mr-3"></i>Analyse des Tendances
          </h2>
          <p class="text-orange-200">Évolution des performances sur la saison</p>
        </div>
        <div class="text-right">
          <div class="text-4xl font-bold text-yellow-400">{{ trendDirection }}</div>
          <div class="text-sm text-orange-200">Tendance Générale</div>
        </div>
      </div>
    </div>

    <!-- Indicateurs de tendance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Tendance des buts -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-red-300">
          <i class="fas fa-bullseye mr-2"></i>Tendance des Buts
        </h3>
        <div class="text-center">
          <div class="text-3xl font-bold text-red-400 mb-2">{{ goalTrend.current }}</div>
          <div class="text-sm text-gray-400 mb-4">Buts cette saison</div>
          <div class="flex items-center justify-center space-x-2">
            <i :class="goalTrend.icon + ' text-lg'" :class="goalTrend.color"></i>
            <span :class="goalTrend.color">{{ goalTrend.change }}</span>
          </div>
        </div>
      </div>

      <!-- Tendance des passes -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-share-alt mr-2"></i>Tendance des Passes
        </h3>
        <div class="text-center">
          <div class="text-3xl font-bold text-blue-400 mb-2">{{ assistTrend.current }}</div>
          <div class="text-sm text-gray-400 mb-4">Passes cette saison</div>
          <div class="flex items-center justify-center space-x-2">
            <i :class="assistTrend.icon + ' text-lg'" :class="assistTrend.color"></i>
            <span :class="assistTrend.color">{{ assistTrend.change }}</span>
          </div>
        </div>
      </div>

      <!-- Tendance du rating -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-yellow-300">
          <i class="fas fa-star mr-2"></i>Tendance du Rating
        </h3>
        <div class="text-center">
          <div class="text-3xl font-bold text-yellow-400 mb-2">{{ ratingTrend.current }}</div>
          <div class="text-sm text-gray-400 mb-4">Rating moyen</div>
          <div class="flex items-center justify-center space-x-2">
            <i :class="ratingTrend.icon + ' text-lg'" :class="ratingTrend.color"></i>
            <span :class="ratingTrend.color">{{ ratingTrend.change }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphiques de tendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Évolution saisonnière -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-chart-area mr-2"></i>Évolution Saisonnière
        </h3>
        <div class="h-64">
          <canvas ref="seasonalTrendChart"></canvas>
        </div>
      </div>

      <!-- Analyse des performances par mois -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-chart-bar mr-2"></i>Performances par Mois
        </h3>
        <div class="h-64">
          <canvas ref="monthlyPerformanceChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Analyse des cycles de forme -->
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
      <h3 class="text-lg font-bold mb-4 text-purple-300">
        <i class="fas fa-wave-square mr-2"></i>Cycles de Forme
      </h3>
      <div class="h-80">
        <canvas ref="formCycleChart"></canvas>
      </div>
    </div>

    <!-- Prédictions et projections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Projections de fin de saison -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-crystal-ball mr-2"></i>Projections de Fin de Saison
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Buts projetés</span>
            <span class="text-lg font-bold text-red-400">{{ projections.goals }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (projections.goals / 35 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Passes projetées</span>
            <span class="text-lg font-bold text-blue-400">{{ projections.assists }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (projections.assists / 25 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Rating final</span>
            <span class="text-lg font-bold text-yellow-400">{{ projections.rating }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (projections.rating / 100 * 100) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Analyse des patterns -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-pattern mr-2"></i>Patterns de Performance
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Meilleur moment</span>
            <span class="text-lg font-bold text-green-400">{{ patterns.bestTime }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: patterns.bestTimePercent + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Période difficile</span>
            <span class="text-lg font-bold text-red-400">{{ patterns.difficultTime }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: patterns.difficultTimePercent + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Consistance</span>
            <span class="text-lg font-bold text-blue-400">{{ patterns.consistency }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: patterns.consistency + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'TrendAnalysis',
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      seasonalTrendChart: null,
      monthlyPerformanceChart: null,
      formCycleChart: null,
      trendDirection: '↗️ En hausse',
      goalTrend: {
        current: 24,
        change: '+15%',
        icon: 'fas fa-arrow-up',
        color: 'text-green-400'
      },
      assistTrend: {
        current: 18,
        change: '+8%',
        icon: 'fas fa-arrow-up',
        color: 'text-green-400'
      },
      ratingTrend: {
        current: 8.7,
        change: '+0.3',
        icon: 'fas fa-arrow-up',
        color: 'text-green-400'
      },
      projections: {
        goals: 32,
        assists: 24,
        rating: 89
      },
      patterns: {
        bestTime: 'Octobre-Décembre',
        bestTimePercent: 85,
        difficultTime: 'Janvier-Février',
        difficultTimePercent: 65,
        consistency: 78
      }
    }
  },
  mounted() {
    this.initCharts()
  },
  methods: {
    initCharts() {
      this.initSeasonalTrendChart()
      this.initMonthlyPerformanceChart()
      this.initFormCycleChart()
    },
    
    initSeasonalTrendChart() {
      const ctx = this.$refs.seasonalTrendChart.getContext('2d')
      this.seasonalTrendChart = new Chart(ctx, {
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
    },
    
    initMonthlyPerformanceChart() {
      const ctx = this.$refs.monthlyPerformanceChart.getContext('2d')
      this.monthlyPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Août', 'Sept', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
          datasets: [{
            label: 'Performance mensuelle',
            data: [75, 78, 82, 85, 88, 90, 87, 85, 83, 86],
            backgroundColor: 'rgba(139, 92, 244, 0.8)',
            borderColor: 'rgba(139, 92, 244, 1)',
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
    },
    
    initFormCycleChart() {
      const ctx = this.$refs.formCycleChart.getContext('2d')
      this.formCycleChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6', 'J7', 'J8', 'J9', 'J10', 'J11', 'J12'],
          datasets: [{
            label: 'Forme du joueur',
            data: [7.5, 8.0, 7.8, 8.5, 8.8, 9.0, 8.7, 8.9, 8.6, 8.8, 9.1, 8.7],
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#F59E0B',
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
            y: {
              beginAtZero: true,
              max: 10,
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
    if (this.seasonalTrendChart) {
      this.seasonalTrendChart.destroy()
    }
    if (this.monthlyPerformanceChart) {
      this.monthlyPerformanceChart.destroy()
    }
    if (this.formCycleChart) {
      this.formCycleChart.destroy()
    }
  }
}
</script>

<style scoped>
.trend-analysis {
  @apply text-gray-100;
}

.trend-header {
  background: linear-gradient(135deg, #9a3412 0%, #991b1b 100%);
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

