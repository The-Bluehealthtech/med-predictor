<template>
  <div class="ai-recommendation-card" :class="getCardClass()">
    <div class="card-header">
      <div class="header-left">
        <div class="ai-indicator">
          <svg class="ai-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
          </svg>
          <span class="ai-label">{{ $t('auto.key405') }}</span>
        </div>
        
        <div class="recommendation-meta">
          <h3 class="recommendation-title">{{ recommendation.title }}</h3>
          <div class="recommendation-tags">
            <span class="tag category" :class="getCategoryClass()">{{ recommendation.category }}</span>
            <span class="tag priority" :class="getPriorityClass()">{{ recommendation.priority }}</span>
            <span class="tag timeframe">{{ recommendation.timeframe }}</span>
          </div>
        </div>
      </div>
      
      <div class="header-right">
        <div class="confidence-score" :class="getConfidenceClass()">
          <span class="confidence-value">{{ (recommendation.confidence_score * 100).toFixed(0) }}%</span>
          <span class="confidence-label">{{ $t('auto.key406') }}</span>
        </div>
        
        <div class="card-actions">
          <button 
            v-if="recommendation.status === 'pending'"
            @click="implementRecommendation"
            class="action-btn implement-btn"
            :disabled="implementing"
          >
            <svg v-if="!implementing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            {{ implementing ? 'Implémentation...' : 'Implémenter' }}
          </button>
          
          <button @click="toggleDetails" class="action-btn details-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <div class="card-body">
      <p class="recommendation-description">{{ recommendation.description }}</p>
      
      <div v-if="showDetails" class="recommendation-details">
        <div v-if="recommendation.detailed_analysis" class="detail-section">
          <h4 class="detail-title">{{ $t('auto.key407') }}</h4>
          <p class="detail-content">{{ recommendation.detailed_analysis }}</p>
        </div>
        
        <div v-if="recommendation.recommended_actions && recommendation.recommended_actions.length" class="detail-section">
          <h4 class="detail-title">{{ $t('auto.key408') }}</h4>
          <ul class="actions-list">
            <li v-for="(action, index) in recommendation.recommended_actions" :key="index" class="action-item">
              {{ action }}
            </li>
          </ul>
        </div>
        
        <div v-if="recommendation.expected_outcomes && recommendation.expected_outcomes.length" class="detail-section">
          <h4 class="detail-title">{{ $t('auto.key409') }}</h4>
          <ul class="outcomes-list">
            <li v-for="(outcome, index) in recommendation.expected_outcomes" :key="index" class="outcome-item">
              {{ outcome }}
            </li>
          </ul>
        </div>
        
        <div v-if="recommendation.success_metrics && recommendation.success_metrics.length" class="detail-section">
          <h4 class="detail-title">{{ $t('auto.key410') }}</h4>
          <div class="metrics-grid">
            <div v-for="(metric, index) in recommendation.success_metrics" :key="index" class="metric-item">
              <span class="metric-label">{{ metric.label }}</span>
              <span class="metric-target">{{ metric.target }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="card-footer">
      <div class="progress-section">
        <div class="progress-header">
          <span class="progress-label">{{ $t('auto.key411') }}</span>
          <span class="progress-percentage">{{ recommendation.progress || 0 }}%</span>
        </div>
        <div class="progress-bar">
          <div 
            class="progress-fill" 
            :style="{ width: `${recommendation.progress || 0}%` }"
            :class="getProgressClass()"
          ></div>
        </div>
      </div>
      
      <div class="status-section">
        <div class="status-indicator" :class="getStatusClass()">
          <div class="status-dot"></div>
          <span class="status-text">{{ getStatusText() }}</span>
        </div>
        
        <div v-if="recommendation.assigned_to" class="assigned-to">
          <span class="assigned-label">{{ $t('auto.key412') }}</span>
          <span class="assigned-name">{{ recommendation.assigned_to.name }}</span>
        </div>
      </div>
    </div>
    
    <!-- Modal d'implémentation -->
    <div v-if="showImplementationModal" class="modal-overlay" @click="closeImplementationModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ $t('auto.key413') }}</h3>
          <button @click="closeImplementationModal" class="modal-close">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="submitImplementation" class="modal-body">
          <div class="form-group">
            <label for="startDate" class="form-label">{{ $t('auto.key414') }}</label>
            <input 
              id="startDate" 
              v-model="implementationData.start_date" 
              type="date" 
              required
              class="form-input"
            >
          </div>
          
          <div class="form-group">
            <label for="endDate" class="form-label">{{ $t('auto.key415') }}</label>
            <input 
              id="endDate" 
              v-model="implementationData.expected_completion_date" 
              type="date" 
              required
              class="form-input"
            >
          </div>
          
          <div class="form-group">
            <label for="assignedTo" class="form-label">{{ $t('auto.key416') }}</label>
            <select id="assignedTo" v-model="implementationData.assigned_to" class="form-input">
              <option value="">{{ $t('auto.key417') }}</option>
              <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="notes" class="form-label">{{ $t('auto.key418') }}</label>
            <textarea 
              id="notes" 
              v-model="implementationData.implementation_notes" 
              rows="3"
              class="form-input"
              placeholder="Notes sur l'implémentation..."
            ></textarea>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="closeImplementationModal" class="btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn-primary" :disabled="implementing">
              {{ implementing ? 'Implémentation...' : 'Implémenter' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AIRecommendationCard',
  props: {
    recommendation: {
      type: Object,
      required: true
    },
    availableUsers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      showDetails: false,
      showImplementationModal: false,
      implementing: false,
      implementationData: {
        start_date: '',
        expected_completion_date: '',
        assigned_to: '',
        implementation_notes: ''
      }
    }
  },
  methods: {
    getCardClass() {
      return {
        'card-pending': this.recommendation.status === 'pending',
        'card-in-progress': this.recommendation.status === 'in_progress',
        'card-completed': this.recommendation.status === 'completed',
        'card-expanded': this.showDetails
      }
    },
    
    getCategoryClass() {
      const categoryColors = {
        physical: 'category-physical',
        technical: 'category-technical',
        tactical: 'category-tactical',
        mental: 'category-mental',
        social: 'category-social',
        medical: 'category-medical'
      }
      return categoryColors[this.recommendation.category] || 'category-default'
    },
    
    getPriorityClass() {
      const priorityColors = {
        critical: 'priority-critical',
        high: 'priority-high',
        medium: 'priority-medium',
        low: 'priority-low'
      }
      return priorityColors[this.recommendation.priority] || 'priority-medium'
    },
    
    getConfidenceClass() {
      const confidence = this.recommendation.confidence_score
      if (confidence >= 0.8) return 'confidence-high'
      if (confidence >= 0.6) return 'confidence-medium'
      return 'confidence-low'
    },
    
    getProgressClass() {
      const progress = this.recommendation.progress || 0
      if (progress >= 80) return 'progress-excellent'
      if (progress >= 60) return 'progress-good'
      if (progress >= 40) return 'progress-fair'
      return 'progress-poor'
    },
    
    getStatusClass() {
      const statusColors = {
        pending: 'status-pending',
        in_progress: 'status-in-progress',
        completed: 'status-completed'
      }
      return statusColors[this.recommendation.status] || 'status-pending'
    },
    
    getStatusText() {
      const statusTexts = {
        pending: 'En attente',
        in_progress: 'En cours',
        completed: 'Terminé'
      }
      return statusTexts[this.recommendation.status] || 'En attente'
    },
    
    toggleDetails() {
      this.showDetails = !this.showDetails
    },
    
    implementRecommendation() {
      this.showImplementationModal = true
    },
    
    closeImplementationModal() {
      this.showImplementationModal = false
      this.implementationData = {
        start_date: '',
        expected_completion_date: '',
        assigned_to: '',
        implementation_notes: ''
      }
    },
    
    async submitImplementation() {
      this.implementing = true
      
      try {
        const response = await fetch(`/api/performance-recommendations/${this.recommendation.id}/implement`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(this.implementationData)
        })
        
        if (response.ok) {
          const result = await response.json()
          this.$emit('recommendation-updated', result.recommendation)
          this.closeImplementationModal()
          this.$toast.success('Recommandation implémentée avec succès')
        } else {
          throw new Error('Erreur lors de l\'implémentation')
        }
      } catch (error) {
        console.error('Erreur:', error)
        this.$toast.error('Erreur lors de l\'implémentation')
      } finally {
        this.implementing = false
      }
    },
    
    // Méthodes publiques
    updateProgress(progress) {
      this.$emit('progress-updated', { id: this.recommendation.id, progress })
    },
    
    markAsCompleted() {
      this.$emit('status-changed', { id: this.recommendation.id, status: 'completed' })
    }
  }
}
</script>

<style scoped>
.ai-recommendation-card {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  overflow: hidden;
}

.ai-recommendation-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-1px);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem;
  border-bottom: 1px solid #f3f4f6;
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  flex: 1;
}

.ai-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
}

.ai-icon {
  width: 1rem;
  height: 1rem;
}

.recommendation-meta {
  flex: 1;
}

.recommendation-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
  line-height: 1.4;
}

.recommendation-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.category-physical { background: #dbeafe; color: #1e40af; }
.category-technical { background: #d1fae5; color: #065f46; }
.category-tactical { background: #fef3c7; color: #92400e; }
.category-mental { background: #fce7f3; color: #be185d; }
.category-social { background: #e0e7ff; color: #3730a3; }
.category-medical { background: #fee2e2; color: #991b1b; }
.category-default { background: #f3f4f6; color: #374151; }

.priority-critical { background: #fee2e2; color: #991b1b; }
.priority-high { background: #fef3c7; color: #92400e; }
.priority-medium { background: #dbeafe; color: #1e40af; }
.priority-low { background: #d1fae5; color: #065f46; }

.header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1rem;
}

.confidence-score {
  text-align: center;
  padding: 0.5rem;
  border-radius: 8px;
  min-width: 60px;
}

.confidence-high { background: #d1fae5; color: #065f46; }
.confidence-medium { background: #fef3c7; color: #92400e; }
.confidence-low { background: #fee2e2; color: #991b1b; }

.confidence-value {
  display: block;
  font-size: 1rem;
  font-weight: 700;
}

.confidence-label {
  display: block;
  font-size: 0.75rem;
  opacity: 0.8;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.implement-btn {
  background: #3b82f6;
  color: white;
}

.implement-btn:hover:not(:disabled) {
  background: #2563eb;
}

.implement-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.details-btn {
  background: #f3f4f6;
  color: #374151;
}

.details-btn:hover {
  background: #e5e7eb;
}

.card-body {
  padding: 1.5rem;
}

.recommendation-description {
  color: #4b5563;
  line-height: 1.6;
  margin: 0 0 1rem 0;
}

.recommendation-details {
  border-top: 1px solid #f3f4f6;
  padding-top: 1rem;
  margin-top: 1rem;
}

.detail-section {
  margin-bottom: 1.5rem;
}

.detail-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.detail-content {
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
}

.actions-list,
.outcomes-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.action-item,
.outcome-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f3f4f6;
  color: #6b7280;
  position: relative;
  padding-left: 1.5rem;
}

.action-item::before,
.outcome-item::before {
  content: '•';
  position: absolute;
  left: 0;
  color: #3b82f6;
  font-weight: bold;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.metric-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem;
  background: #f9fafb;
  border-radius: 0.375rem;
}

.metric-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.metric-target {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.card-footer {
  padding: 1.5rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.progress-section {
  margin-bottom: 1rem;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.progress-percentage {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-excellent { background: #10b981; }
.progress-good { background: #3b82f6; }
.progress-fair { background: #f59e0b; }
.progress-poor { background: #ef4444; }

.status-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-pending .status-dot { background: #f59e0b; }
.status-in-progress .status-dot { background: #3b82f6; }
.status-completed .status-dot { background: #10b981; }

.status-text {
  font-size: 0.875rem;
  font-weight: 500;
}

.status-pending .status-text { color: #92400e; }
.status-in-progress .status-text { color: #1e40af; }
.status-completed .status-text { color: #065f46; }

.assigned-to {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.assigned-label {
  font-size: 0.75rem;
  color: #6b7280;
}

.assigned-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  border: none;
  background: #3b82f6;
  color: white;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-right {
    width: 100%;
    flex-direction: row;
    justify-content: space-between;
  }
  
  .recommendation-tags {
    flex-direction: column;
  }
  
  .status-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style> 