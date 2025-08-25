<template>
  <div class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h5 class="modal-title">{{ $t('auto.key302') }}</h5>
        <button @click="closeModal" class="btn-close" aria-label="Close"></button>
      </div>
      
      <form @submit.prevent="createMatch">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="competition" class="form-label">{{ $t('auto.key303') }}</label>
                <select 
                  v-model="form.competition_id" 
                  class="form-select" 
                  required
                  :disabled="loading"
                >
                  <option value="">{{ $t('auto.key304') }}</option>
                  <option v-for="competition in competitions" :key="competition.id" :value="competition.id">
                    {{ competition.name }}
                  </option>
                </select>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="match_date" class="form-label">{{ $t('auto.key305') }}</label>
                <input 
                  type="date" 
                  v-model="form.match_date" 
                  class="form-control" 
                  required
                  :disabled="loading"
                >
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="kickoff_time" class="form-label">{{ $t('auto.key306') }}</label>
                <input 
                  type="time" 
                  v-model="form.kickoff_time" 
                  class="form-control" 
                  required
                  :disabled="loading"
                >
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="venue" class="form-label">{{ $t('auto.key307') }}</label>
                <input 
                  type="text" 
                  v-model="form.venue" 
                  class="form-control"
                  :disabled="loading"
                >
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="home_team" class="form-label">{{ $t('auto.key308') }}</label>
                <select 
                  v-model="form.home_team_id" 
                  class="form-select" 
                  required
                  :disabled="loading || !form.competition_id"
                >
                  <option value="">{{ $t('auto.key309') }}</option>
                  <option v-for="team in availableTeams" :key="team.id" :value="team.id">
                    {{ team.name }}
                  </option>
                </select>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="away_team" class="form-label">{{ $t('auto.key310') }}</label>
                <select 
                  v-model="form.away_team_id" 
                  class="form-select" 
                  required
                  :disabled="loading || !form.competition_id"
                >
                  <option value="">{{ $t('auto.key311') }}</option>
                  <option v-for="team in availableTeams" :key="team.id" :value="team.id">
                    {{ team.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="notes" class="form-label">{{ $t('auto.key312') }}</label>
            <textarea 
              v-model="form.notes" 
              class="form-control" 
              rows="3"
              :disabled="loading"
            ></textarea>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" @click="closeModal" class="btn btn-secondary" :disabled="loading">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Create Match
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import apiService from '../services/ApiService.js';

export default {
  name: 'CreateMatchModal',
  data() {
    return {
      loading: false,
      competitions: [],
      teams: [],
      form: {
        competition_id: '',
        match_date: '',
        kickoff_time: '',
        venue: '',
        home_team_id: '',
        away_team_id: '',
        notes: ''
      }
    };
  },
  computed: {
    availableTeams() {
      if (!this.form.competition_id) return [];
      return this.teams.filter(team => 
        team.competitions?.some(comp => comp.id == this.form.competition_id)
      );
    },
    isFormValid() {
      return this.form.competition_id && 
             this.form.match_date && 
             this.form.kickoff_time && 
             this.form.home_team_id && 
             this.form.away_team_id &&
             this.form.home_team_id !== this.form.away_team_id;
    }
  },
  async mounted() {
    await this.loadData();
  },
  methods: {
    async loadData() {
      try {
        this.loading = true;
        
        const [competitionsRes, teamsRes] = await Promise.all([
          apiService.getCompetitions({ status: 'active' }),
          apiService.getClubs({ include: 'teams,competitions' })
        ]);
        
        if (competitionsRes.success) {
          this.competitions = competitionsRes.data.data || [];
        }
        
        if (teamsRes.success) {
          // Extract teams from clubs
          this.teams = teamsRes.data.data.flatMap(club => 
            club.teams?.map(team => ({
              ...team,
              competitions: club.competitions
            })) || []
          );
        }
      } catch (error) {
        console.error('Error loading data:', error);
        this.$emit('error', error.message);
      } finally {
        this.loading = false;
      }
    },
    
    async createMatch() {
      if (!this.isFormValid) return;
      
      try {
        this.loading = true;
        
        const matchData = {
          ...this.form,
          match_date: `${this.form.match_date} ${this.form.kickoff_time}:00`
        };
        
        const response = await apiService.createMatch(matchData);
        
        if (response.success) {
          this.$emit('created', response.data);
          this.closeModal();
        } else {
          this.$emit('error', response.error || 'Failed to create match');
        }
      } catch (error) {
        console.error('Error creating match:', error);
        this.$emit('error', error.message);
      } finally {
        this.loading = false;
      }
    },
    
    closeModal() {
      this.$emit('close');
    }
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  padding: 1rem;
}

.modal-footer {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-control:disabled,
.form-select:disabled {
  background-color: #f8f9fa;
  opacity: 0.6;
}
</style> 