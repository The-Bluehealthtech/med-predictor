<template>
  <div class="health-score-meter">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Score de Santé</h3>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Dernière mise à jour</span>
          <span class="text-sm font-medium text-gray-900">{{ formatDate(healthData.current_score?.calculated_date) }}</span>
        </div>
      </div>

      <!-- Main Score Display -->
      <div class="flex items-center justify-center mb-6">
        <div class="relative">
          <!-- Circular Progress -->
          <div class="w-32 h-32 relative">
            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
              <!-- Background circle -->
              <circle
                cx="60"
                cy="60"
                r="54"
                stroke="currentColor"
                stroke-width="8"
                fill="transparent"
                class="text-gray-200"
              />
              <!-- Progress circle -->
              <circle
                cx="60"
                cy="60"
                r="54"
                stroke="currentColor"
                stroke-width="8"
                fill="transparent"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="strokeDashoffset"
                :class="getScoreColorClass()"
                class="transition-all duration-500 ease-in-out"
              />
            </svg>
            
            <!-- Score Display -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center">
                <div class="text-3xl font-bold" :class="getScoreTextColorClass()">
                  {{ healthData.current_score?.score || 0 }}
                </div>
                <div class="text-sm text-gray-600">/ 100</div>
              </div>
            </div>
          </div>

          <!-- Trend Indicator -->
          <div v-if="healthData.current_score?.trend_icon" class="absolute -top-2 -right-2">
            <div class="bg-white rounded-full p-1 shadow-sm">
              <span class="text-lg">{{ healthData.current_score.trend_icon }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Status and Grade -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusClass()">
          {{ healthData.current_score?.status || 'Non disponible' }}
        </div>
        <p class="text-sm text-gray-600 mt-2">{{ healthData.current_score?.trend_text || '' }}</p>
      </div>

      <!-- Contributing Factors -->
      <div v-if="healthData.current_score?.contributing_factors" class="mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Facteurs Contributifs</h4>
        <div class="space-y-2">
          <div 
            v-for="(factor, key) in healthData.current_score.contributing_factors" 
            :key="key"
            class="flex items-center justify-between p-2 bg-gray-50 rounded-md"
          >
            <span class="text-sm text-gray-700">{{ getFactorDisplayName(key) }}</span>
            <div class="flex items-center space-x-2">
              <span class="text-sm font-medium" :class="factor.impact === 'positive' ? 'text-green-600' : 'text-red-600'">
                {{ factor.impact === 'positive' ? '+' : '-' }}{{ factor.bonus || factor.deduction }}
              </span>
              <span class="text-xs text-gray-500">({{ factor.count }})</span>
            </div>
          </div>
        </div>
      </div>

      <!-- AI Analysis -->
      <div v-if="healthData.current_score?.ai_analysis" class="mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Analyse IA</h4>
        <div class="space-y-3">
          <div v-if="healthData.current_score.ai_analysis.insights?.length" class="bg-blue-50 p-3 rounded-md">
            <h5 class="text-xs font-medium text-blue-800 mb-2">Observations</h5>
            <ul class="text-xs text-blue-700 space-y-1">
              <li v-for="insight in healthData.current_score.ai_analysis.insights" :key="insight" class="flex items-start">
                <span class="mr-2">•</span>
                <span>{{ insight }}</span>
              </li>
            </ul>
          </div>
          
          <div v-if="healthData.current_score.ai_analysis.recommendations?.length" class="bg-green-50 p-3 rounded-md">
            <h5 class="text-xs font-medium text-green-800 mb-2">Recommandations</h5>
            <ul class="text-xs text-green-700 space-y-1">
              <li v-for="recommendation in healthData.current_score.ai_analysis.recommendations" :key="recommendation" class="flex items-start">
                <span class="mr-2">•</span>
                <span>{{ recommendation }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Historical Trend -->
      <div v-if="healthData.historical_scores?.length" class="mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Évolution (30 derniers jours)</h4>
        <div class="h-20 bg-gray-50 rounded-md p-3">
          <div class="flex items-end justify-between h-full">
            <div 
              v-for="(score, index) in reversedHistoricalScores" 
              :key="index"
              class="flex-1 mx-1"
            >
              <div 
                class="w-full rounded-sm transition-all duration-300"
                :class="getBarColorClass(score.score)"
                :style="{ height: `${score.score}%` }"
              ></div>
            </div>
          </div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">
          <span>{{ formatDate(healthData.historical_scores[healthData.historical_scores.length - 1]?.date) }}</span>
          <span>{{ formatDate(healthData.historical_scores[0]?.date) }}</span>
        </div>
      </div>

      <!-- Trend Analysis -->
      <div v-if="healthData.trend_analysis" class="bg-gray-50 p-4 rounded-md">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Analyse des Tendances</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-gray-600">Direction:</span>
            <span class="ml-2 font-medium" :class="getTrendDirectionClass()">
              {{ getTrendDirectionText() }}
            </span>
          </div>
          <div>
            <span class="text-gray-600">Taux de changement:</span>
            <span class="ml-2 font-medium" :class="getChangeRateClass()">
              {{ healthData.trend_analysis.change_rate > 0 ? '+' : '' }}{{ healthData.trend_analysis.change_rate }}
            </span>
          </div>
          <div>
            <span class="text-gray-600">Volatilité:</span>
            <span class="ml-2 font-medium" :class="getVolatilityClass()">
              {{ getVolatilityText() }}
            </span>
          </div>
          <div>
            <span class="text-gray-600">Moyenne 30j:</span>
            <span class="ml-2 font-medium text-gray-900">
              {{ healthData.average_score_30_days }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HealthScoreMeter',
  props: {
    healthData: {
      type: Object,
      required: true,
      default: () => ({
        current_score: null,
        historical_scores: [],
        average_score_30_days: 0,
        trend_analysis: {},
      })
    }
  },
  computed: {
    circumference() {
      return 2 * Math.PI * 54; // radius = 54
    },
    strokeDashoffset() {
      const score = this.healthData.current_score?.score || 0;
      const progress = score / 100;
      return this.circumference * (1 - progress);
    },
    reversedHistoricalScores() {
      return [...this.healthData.historical_scores].reverse().slice(0, 10);
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString('fr-FR');
    },

    getScoreColorClass() {
      const score = this.healthData.current_score?.score || 0;
      if (score >= 90) return 'text-blue-500';
      if (score >= 80) return 'text-green-500';
      if (score >= 70) return 'text-yellow-500';
      if (score >= 60) return 'text-orange-500';
      return 'text-red-500';
    },

    getScoreTextColorClass() {
      const score = this.healthData.current_score?.score || 0;
      if (score >= 90) return 'text-blue-600';
      if (score >= 80) return 'text-green-600';
      if (score >= 70) return 'text-yellow-600';
      if (score >= 60) return 'text-orange-600';
      return 'text-red-600';
    },

    getStatusClass() {
      const status = this.healthData.current_score?.status;
      switch (status) {
        case 'Wellness': return 'bg-blue-100 text-blue-800';
        case 'Fit': return 'bg-green-100 text-green-800';
        case 'Monitor': return 'bg-yellow-100 text-yellow-800';
        case 'Intervention': return 'bg-orange-100 text-orange-800';
        case 'Critical': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
      }
    },

    getBarColorClass(score) {
      if (score >= 90) return 'bg-blue-500';
      if (score >= 80) return 'bg-green-500';
      if (score >= 70) return 'bg-yellow-500';
      if (score >= 60) return 'bg-orange-500';
      return 'bg-red-500';
    },

    getFactorDisplayName(key) {
      const names = {
        'active_injuries': 'Blessures Actives',
        'unresolved_alerts': 'Alertes Non Résolues',
        'critical_alerts': 'Alertes Critiques',
        'recent_concussions': 'Commotions Récentes',
        'pcmas_completed': 'PCMA Complétés',
        'recent_notes': 'Notes Récentes',
      };
      return names[key] || key.replace(/_/g, ' ');
    },

    getTrendDirectionClass() {
      const direction = this.healthData.trend_analysis?.direction;
      switch (direction) {
        case 'up': return 'text-green-600';
        case 'down': return 'text-red-600';
        default: return 'text-gray-600';
      }
    },

    getTrendDirectionText() {
      const direction = this.healthData.trend_analysis?.direction;
      switch (direction) {
        case 'up': return 'Amélioration';
        case 'down': return 'Détérioration';
        default: return 'Stable';
      }
    },

    getChangeRateClass() {
      const rate = this.healthData.trend_analysis?.change_rate || 0;
      if (rate > 0) return 'text-green-600';
      if (rate < 0) return 'text-red-600';
      return 'text-gray-600';
    },

    getVolatilityClass() {
      const volatility = this.healthData.trend_analysis?.volatility;
      switch (volatility) {
        case 'high': return 'text-red-600';
        case 'medium': return 'text-yellow-600';
        case 'low': return 'text-green-600';
        default: return 'text-gray-600';
      }
    },

    getVolatilityText() {
      const volatility = this.healthData.trend_analysis?.volatility;
      switch (volatility) {
        case 'high': return 'Élevée';
        case 'medium': return 'Moyenne';
        case 'low': return 'Faible';
        default: return 'Inconnue';
      }
    }
  }
}
</script>

<style scoped>
.health-score-meter {
  @apply max-w-md mx-auto;
}

.circle-progress {
  transition: stroke-dashoffset 0.5s ease-in-out;
}
</style> 