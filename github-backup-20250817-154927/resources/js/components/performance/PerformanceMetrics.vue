<template>
  <div class="performance-metrics">
    <div class="metrics-grid">
      <div 
        v-for="metric in metrics" 
        :key="metric.id" 
        class="metric-card"
        :class="getMetricClass(metric)"
      >
        <div class="metric-icon">
          <component :is="metric.icon" />
        </div>
        
        <div class="metric-content">
          <div class="metric-header">
            <h4 class="metric-title">{{ metric.title }}</h4>
            <div class="metric-trend" :class="getTrendClass(metric.trend)">
              <component :is="getTrendIcon(metric.trend)" />
              <span>{{ formatTrend(metric.trend) }}</span>
            </div>
          </div>
          
          <div class="metric-value">
            <span class="value-number" ref="valueRef">{{ animatedValue }}</span>
            <span class="value-unit">{{ metric.unit }}</span>
          </div>
          
          <div v-if="metric.progress !== undefined" class="metric-progress">
            <div class="progress-bar">
              <div 
                class="progress-fill" 
                :style="{ width: `${metric.progress}%`, backgroundColor: metric.color }"
              ></div>
            </div>
            <span class="progress-text">{{ metric.progress }}%</span>
          </div>
          
          <div v-if="metric.subtitle" class="metric-subtitle">
            {{ metric.subtitle }}
          </div>
        </div>
        
        <div v-if="metric.badge" class="metric-badge" :class="getBadgeClass(metric.badge)">
          {{ metric.badge }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { 
  UserIcon, 
  ChartBarIcon, 
  TrendingUpIcon, 
  TrendingDownIcon,
  MinusIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'

export default {
  name: 'PerformanceMetrics',
  components: {
    UserIcon,
    ChartBarIcon,
    TrendingUpIcon,
    TrendingDownIcon,
    MinusIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon
  },
  props: {
    metrics: {
      type: Array,
      required: true,
      validator: (metrics) => {
        return metrics.every(metric => 
          metric.id && 
          metric.title && 
          metric.value !== undefined &&
          metric.icon
        )
      }
    },
    animate: {
      type: Boolean,
      default: true
    },
    animationDuration: {
      type: Number,
      default: 1000
    }
  },
  data() {
    return {
      animatedValues: {},
      animationFrames: {}
    }
  },
  computed: {
    animatedValue() {
      return this.animatedValues[this.currentMetricId] || 0
    },
    currentMetricId() {
      return this.metrics[0]?.id
    }
  },
  mounted() {
    if (this.animate) {
      this.animateMetrics()
    }
  },
  beforeUnmount() {
    // Nettoyer les animations en cours
    Object.values(this.animationFrames).forEach(frameId => {
      cancelAnimationFrame(frameId)
    })
  },
  watch: {
    metrics: {
      handler() {
        if (this.animate) {
          this.animateMetrics()
        }
      },
      deep: true
    }
  },
  methods: {
    animateMetrics() {
      this.metrics.forEach(metric => {
        this.animateValue(metric.id, metric.value)
      })
    },
    
    animateValue(metricId, targetValue) {
      const startValue = this.animatedValues[metricId] || 0
      const startTime = performance.now()
      const duration = this.animationDuration
      
      const animate = (currentTime) => {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)
        
        // Fonction d'easing (ease-out)
        const easeOut = 1 - Math.pow(1 - progress, 3)
        
        const currentValue = startValue + (targetValue - startValue) * easeOut
        
        this.$set(this.animatedValues, metricId, this.formatValue(currentValue, metricId))
        
        if (progress < 1) {
          this.animationFrames[metricId] = requestAnimationFrame(animate)
        }
      }
      
      this.animationFrames[metricId] = requestAnimationFrame(animate)
    },
    
    formatValue(value, metricId) {
      const metric = this.metrics.find(m => m.id === metricId)
      if (!metric) return value
      
      if (metric.format === 'percentage') {
        return Math.round(value) + '%'
      } else if (metric.format === 'decimal') {
        return value.toFixed(1)
      } else if (metric.format === 'currency') {
        return new Intl.NumberFormat('fr-FR', {
          style: 'currency',
          currency: 'EUR'
        }).format(value)
      } else if (metric.format === 'number') {
        return new Intl.NumberFormat('fr-FR').format(Math.round(value))
      }
      
      return value
    },
    
    getMetricClass(metric) {
      return {
        'metric-card--success': metric.status === 'success',
        'metric-card--warning': metric.status === 'warning',
        'metric-card--danger': metric.status === 'danger',
        'metric-card--info': metric.status === 'info'
      }
    },
    
    getTrendClass(trend) {
      if (!trend) return ''
      
      return {
        'trend-up': trend > 0,
        'trend-down': trend < 0,
        'trend-neutral': trend === 0
      }
    },
    
    getTrendIcon(trend) {
      if (trend > 0) return 'TrendingUpIcon'
      if (trend < 0) return 'TrendingDownIcon'
      return 'MinusIcon'
    },
    
    formatTrend(trend) {
      if (trend === undefined || trend === null) return ''
      
      const absValue = Math.abs(trend)
      const sign = trend > 0 ? '+' : trend < 0 ? '-' : ''
      
      if (typeof trend === 'number') {
        return `${sign}${absValue.toFixed(1)}%`
      }
      
      return trend
    },
    
    getBadgeClass(badge) {
      if (typeof badge === 'string') {
        return {
          'badge-success': badge.toLowerCase().includes('success') || badge.toLowerCase().includes('completed'),
          'badge-warning': badge.toLowerCase().includes('warning') || badge.toLowerCase().includes('pending'),
          'badge-danger': badge.toLowerCase().includes('error') || badge.toLowerCase().includes('failed'),
          'badge-info': badge.toLowerCase().includes('info') || badge.toLowerCase().includes('active')
        }
      }
      return {}
    },
    
    // Méthodes publiques
    updateMetric(metricId, newValue) {
      const metric = this.metrics.find(m => m.id === metricId)
      if (metric) {
        metric.value = newValue
        if (this.animate) {
          this.animateValue(metricId, newValue)
        }
      }
    },
    
    addMetric(metric) {
      this.metrics.push(metric)
    },
    
    removeMetric(metricId) {
      const index = this.metrics.findIndex(m => m.id === metricId)
      if (index > -1) {
        this.metrics.splice(index, 1)
      }
    }
  }
}
</script>

<style scoped>
.performance-metrics {
  width: 100%;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.metric-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  position: relative;
  transition: all 0.3s ease;
  overflow: hidden;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.metric-card--success {
  border-left: 4px solid #10b981;
}

.metric-card--warning {
  border-left: 4px solid #f59e0b;
}

.metric-card--danger {
  border-left: 4px solid #ef4444;
}

.metric-card--info {
  border-left: 4px solid #3b82f6;
}

.metric-icon {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: #6b7280;
}

.metric-card--success .metric-icon {
  background: #d1fae5;
  color: #10b981;
}

.metric-card--warning .metric-icon {
  background: #fef3c7;
  color: #f59e0b;
}

.metric-card--danger .metric-icon {
  background: #fee2e2;
  color: #ef4444;
}

.metric-card--info .metric-icon {
  background: #dbeafe;
  color: #3b82f6;
}

.metric-content {
  margin-right: 3rem;
}

.metric-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.metric-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin: 0;
  line-height: 1.4;
}

.metric-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
}

.trend-up {
  color: #10b981;
  background: #d1fae5;
}

.trend-down {
  color: #ef4444;
  background: #fee2e2;
}

.trend-neutral {
  color: #6b7280;
  background: #f3f4f6;
}

.metric-value {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
  margin-bottom: 0.75rem;
}

.value-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
}

.value-unit {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.metric-progress {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
  min-width: 2.5rem;
  text-align: right;
}

.metric-subtitle {
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.4;
}

.metric-badge {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.badge-success {
  background: #d1fae5;
  color: #065f46;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.badge-danger {
  background: #fee2e2;
  color: #991b1b;
}

.badge-info {
  background: #dbeafe;
  color: #1e40af;
}

@media (max-width: 768px) {
  .metrics-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .metric-card {
    padding: 1rem;
  }
  
  .metric-icon {
    width: 2rem;
    height: 2rem;
    top: 0.75rem;
    right: 0.75rem;
  }
  
  .metric-content {
    margin-right: 2.5rem;
  }
  
  .value-number {
    font-size: 1.5rem;
  }
}
</style> 