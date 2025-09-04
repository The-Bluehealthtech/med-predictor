<template>
  <div class="comparison-analysis">
    <!-- En-tête de l'analyse comparative -->
    <div class="comparison-header bg-gradient-to-r from-purple-900 to-pink-900 rounded-xl p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-white mb-2">
            <i class="fas fa-balance-scale mr-3"></i>Analyse Comparative
          </h2>
          <p class="text-purple-200">Comparaison avec les meilleurs joueurs de la position</p>
        </div>
        <div class="text-right">
          <div class="text-4xl font-bold text-yellow-400">{{ comparisonRanking }}</div>
          <div class="text-sm text-purple-200">Rang dans la Ligue</div>
        </div>
      </div>
    </div>

    <!-- Comparaison avec les pairs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Comparaison des statistiques -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-blue-300">
          <i class="fas fa-chart-bar mr-2"></i>Comparaison des Statistiques
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Buts par match</span>
            <div class="flex items-center space-x-2">
              <span class="text-lg font-bold text-red-400">{{ playerStats.goalsPerMatch }}</span>
              <span class="text-sm text-gray-400">vs {{ leagueAvg.goalsPerMatch }}</span>
            </div>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (playerStats.goalsPerMatch / leagueAvg.goalsPerMatch * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Passes par match</span>
            <div class="flex items-center space-x-2">
              <span class="text-lg font-bold text-blue-400">{{ playerStats.assistsPerMatch }}</span>
              <span class="text-sm text-gray-400">vs {{ leagueAvg.assistsPerMatch }}</span>
            </div>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (playerStats.assistsPerMatch / leagueAvg.assistsPerMatch * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Précision des passes</span>
            <div class="flex items-center space-x-2">
              <span class="text-lg font-bold text-green-400">{{ playerStats.passAccuracy }}%</span>
              <span class="text-sm text-gray-400">vs {{ leagueAvg.passAccuracy }}%</span>
            </div>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (playerStats.passAccuracy / leagueAvg.passAccuracy * 100) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Classement de la position -->
      <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-lg font-bold mb-4 text-green-300">
          <i class="fas fa-trophy mr-2"></i>Classement de la Position
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Rating global</span>
            <span class="text-lg font-bold text-yellow-400">{{ positionRanking.overallRating }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-yellow-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (positionRanking.overallRating / 100 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Buts marqués</span>
            <span class="text-lg font-bold text-red-400">{{ positionRanking.goals }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-red-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (positionRanking.goals / 30 * 100) + '%' }"></div>
          </div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-300">Passes décisives</span>
            <span class="text-lg font-bold text-blue-400">{{ positionRanking.assists }}</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" 
                 :style="{ width: (positionRanking.assists / 25 * 100) + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Graphique de comparaison -->
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
      <h3 class="text-lg font-bold mb-4 text-purple-300">
        <i class="fas fa-chart-line mr-2"></i>Comparaison des Performances
      </h3>
      <div class="h-80">
        <canvas ref="comparisonChart"></canvas>
      </div>
    </div>

    <!-- Tableau des meilleurs joueurs -->
    <div class="bg-gray-800 rounded-xl p-6">
      <h3 class="text-lg font-bold mb-4 text-yellow-300">
        <i class="fas fa-medal mr-2"></i>Top 10 de la Position
      </h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-700">
              <th class="text-left py-2 text-gray-300">Rang</th>
              <th class="text-left py-2 text-gray-300">Joueur</th>
              <th class="text-center py-2 text-gray-300">Club</th>
              <th class="text-center py-2 text-gray-300">Rating</th>
              <th class="text-center py-2 text-gray-300">Buts</th>
              <th class="text-center py-2 text-gray-300">Passes</th>
              <th class="text-center py-2 text-gray-300">Forme</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(player, index) in topPlayers" :key="player.id" 
                :class="[
                  'border-b border-gray-700/50',
                  player.isCurrentPlayer ? 'bg-blue-900/30' : ''
                ]">
              <td class="py-2 text-gray-300">
                <span class="px-2 py-1 rounded text-xs font-bold" 
                      :class="getRankClass(index + 1)">
                  {{ index + 1 }}
                </span>
              </td>
              <td class="py-2 text-gray-300">{{ player.name }}</td>
              <td class="text-center py-2 text-gray-300">{{ player.club }}</td>
              <td class="text-center py-2 text-yellow-400 font-bold">{{ player.rating }}</td>
              <td class="text-center py-2 text-red-400 font-bold">{{ player.goals }}</td>
              <td class="text-center py-2 text-blue-400 font-bold">{{ player.assists }}</td>
              <td class="text-center py-2">
                <span class="px-2 py-1 rounded text-xs font-bold" 
                      :class="getFormClass(player.form)">
                  {{ player.form }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'ComparisonAnalysis',
  props: {
    player: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      comparisonChart: null,
      comparisonRanking: 'Top 5%',
      playerStats: {
        goalsPerMatch: 0.85,
        assistsPerMatch: 0.64,
        passAccuracy: 87
      },
      leagueAvg: {
        goalsPerMatch: 0.45,
        assistsPerMatch: 0.38,
        passAccuracy: 78
      },
      positionRanking: {
        overallRating: 88,
        goals: 24,
        assists: 18
      },
      topPlayers: [
        { id: 1, name: 'Erling Haaland', club: 'Man City', rating: 91, goals: 28, assists: 8, form: 'Excellent', isCurrentPlayer: false },
        { id: 2, name: 'Harry Kane', club: 'Bayern', rating: 90, goals: 25, assists: 12, form: 'Excellent', isCurrentPlayer: false },
        { id: 3, name: 'Victor Osimhen', club: 'Napoli', rating: 89, goals: 23, assists: 6, form: 'Très bien', isCurrentPlayer: false },
        { id: 4, name: 'Lautaro Martinez', club: 'Inter', rating: 88, goals: 22, assists: 9, form: 'Très bien', isCurrentPlayer: false },
        { id: 5, name: 'Cristiano Ronaldo', club: 'Al Nassr', rating: 88, goals: 24, assists: 18, form: 'Excellent', isCurrentPlayer: true },
        { id: 6, name: 'Kylian Mbappé', club: 'PSG', rating: 87, goals: 21, assists: 15, form: 'Très bien', isCurrentPlayer: false },
        { id: 7, name: 'Robert Lewandowski', club: 'Barcelona', rating: 87, goals: 20, assists: 7, form: 'Bien', isCurrentPlayer: false },
        { id: 8, name: 'Karim Benzema', club: 'Al Ittihad', rating: 86, goals: 19, assists: 10, form: 'Bien', isCurrentPlayer: false },
        { id: 9, name: 'Mohamed Salah', club: 'Liverpool', rating: 86, goals: 18, assists: 14, form: 'Très bien', isCurrentPlayer: false },
        { id: 10, name: 'Darwin Núñez', club: 'Liverpool', rating: 85, goals: 17, assists: 11, form: 'Bien', isCurrentPlayer: false }
      ]
    }
  },
  mounted() {
    this.initComparisonChart()
  },
  methods: {
    initComparisonChart() {
      const ctx = this.$refs.comparisonChart.getContext('2d')
      this.comparisonChart = new Chart(ctx, {
        type: 'radar',
        data: {
          labels: ['Buts', 'Passes', 'Précision', 'Vitesse', 'Technique', 'Physique'],
          datasets: [{
            label: 'Joueur actuel',
            data: [88, 85, 87, 90, 92, 85],
            borderColor: '#8B5CF6',
            backgroundColor: 'rgba(139, 92, 244, 0.2)',
            pointBackgroundColor: '#8B5CF6',
            pointBorderColor: '#fff'
          }, {
            label: 'Moyenne de la ligue',
            data: [65, 70, 78, 75, 80, 78],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            pointBackgroundColor: '#10B981',
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
    
    getRankClass(rank) {
      if (rank === 1) return 'bg-yellow-600 text-white'
      if (rank === 2) return 'bg-gray-400 text-white'
      if (rank === 3) return 'bg-orange-600 text-white'
      return 'bg-gray-600 text-white'
    },
    
    getFormClass(form) {
      if (form === 'Excellent') return 'bg-green-600 text-white'
      if (form === 'Très bien') return 'bg-blue-600 text-white'
      if (form === 'Bien') return 'bg-yellow-600 text-white'
      return 'bg-red-600 text-white'
    }
  },
  
  beforeUnmount() {
    if (this.comparisonChart) {
      this.comparisonChart.destroy()
    }
  }
}
</script>

<style scoped>
.comparison-analysis {
  @apply text-gray-100;
}

.comparison-header {
  background: linear-gradient(135deg, #581c87 0%, #be185d 100%);
}

/* Animations pour les barres de progression */
.w-full.bg-gray-700 > div {
  @apply transition-all duration-1000 ease-out;
}

/* Hover effects */
.space-y-4 > div:hover {
  @apply transform scale-105 transition-transform duration-200;
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

/* Responsive design */
@media (max-width: 768px) {
  .grid {
    @apply grid-cols-1;
  }
}
</style>

