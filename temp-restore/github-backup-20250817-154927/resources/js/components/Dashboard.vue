<template>
  <div class="dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <h1>{{ $t('dashboard.title') }}</h1>
      <div class="user-info" v-if="user">
        <span>{{ $t('dashboard.welcome') }}, {{ user.name }}</span>
        <button @click="logout" class="btn btn-outline-danger btn-sm">{{ $t('dashboard.logout') }}</button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">{{ $t('dashboard.loading') }}</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="alert alert-danger" role="alert">
      {{ error }}
      <button @click="clearError" class="btn-close" aria-label="Close"></button>
    </div>

    <!-- Dashboard Content -->
    <div v-if="!loading && !error" class="dashboard-content">
      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <h5 class="card-title">{{ $t('dashboard.total_matches') }}</h5>
              <h2 class="card-text">{{ stats.matches || 0 }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <h5 class="card-title">{{ $t('dashboard.active_players') }}</h5>
              <h2 class="card-text">{{ stats.players || 0 }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <h5 class="card-title">{{ $t('dashboard.pending_licenses') }}</h5>
              <h2 class="card-text">{{ stats.pendingLicenses || 0 }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <h5 class="card-title">{{ $t('dashboard.active_competitions') }}</h5>
              <h2 class="card-text">{{ stats.competitions || 0 }}</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5>{{ $t('dashboard.quick_actions') }}</h5>
            </div>
            <div class="card-body">
              <div class="btn-group" role="group">
                <button @click="showCreateMatchModal = true" class="btn btn-primary">
                  <i class="fas fa-plus"></i> {{ $t('dashboard.new_match') }}
                </button>
                <button @click="showCreatePlayerModal = true" class="btn btn-success">
                  <i class="fas fa-user-plus"></i> {{ $t('dashboard.add_player') }}
                </button>
                <button @click="showLicenseQueue = true" class="btn btn-warning">
                  <i class="fas fa-clipboard-list"></i> {{ $t('dashboard.license_queue') }}
                </button>
                <button @click="exportData" class="btn btn-info">
                  <i class="fas fa-download"></i> {{ $t('dashboard.export_data') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Matches -->
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>{{ $t('dashboard.recent_matches') }}</h5>
              <button @click="loadMatches" class="btn btn-sm btn-outline-primary">{{ $t('dashboard.refresh') }}</button>
            </div>
            <div class="card-body">
              <div v-if="matches.length === 0" class="text-center text-muted">
                {{ $t('dashboard.no_matches') }}
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>{{ $t('dashboard.date') }}</th>
                      <th>{{ $t('dashboard.home_team') }}</th>
                      <th>{{ $t('dashboard.score') }}</th>
                      <th>{{ $t('dashboard.away_team') }}</th>
                      <th>{{ $t('dashboard.status') }}</th>
                      <th>{{ $t('dashboard.actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="match in matches" :key="match.id">
                      <td>{{ formatDate(match.match_date) }}</td>
                      <td>{{ match.home_team?.name || $t('dashboard.tbd') }}</td>
                      <td>{{ match.home_score || 0 }} - {{ match.away_score || 0 }}</td>
                      <td>{{ match.away_team?.name || $t('dashboard.tbd') }}</td>
                      <td>
                        <span :class="getStatusBadgeClass(match.status)">
                          {{ match.status }}
                        </span>
                      </td>
                      <td>
                        <button @click="viewMatch(match.id)" class="btn btn-sm btn-outline-primary">
                          {{ $t('dashboard.view') }}
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Current Season Info -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h5>{{ $t('dashboard.current_season') }}</h5>
            </div>
            <div class="card-body">
              <div v-if="currentSeason">
                <h6>{{ currentSeason.name }}</h6>
                <p class="text-muted">{{ currentSeason.short_name }}</p>
                <div class="season-dates">
                  <small>
                    <strong>{{ $t('dashboard.start') }}</strong> {{ formatDate(currentSeason.start_date) }}<br>
                    <strong>{{ $t('dashboard.end') }}</strong> {{ formatDate(currentSeason.end_date) }}
                  </small>
                </div>
                <div class="mt-3">
                  <span :class="getStatusBadgeClass(currentSeason.status)">
                    {{ currentSeason.status }}
                  </span>
                </div>
              </div>
              <div v-else class="text-center text-muted">
                {{ $t('dashboard.no_active_season') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Licenses -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>{{ $t('dashboard.pending_license_approvals') }}</h5>
              <button @click="loadPendingLicenses" class="btn btn-sm btn-outline-primary">{{ $t('dashboard.refresh') }}</button>
            </div>
            <div class="card-body">
              <div v-if="pendingLicenses.length === 0" class="text-center text-muted">
                {{ $t('dashboard.no_pending_licenses') }}
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>{{ $t('dashboard.player') }}</th>
                      <th>{{ $t('dashboard.club') }}</th>
                      <th>{{ $t('dashboard.type') }}</th>
                      <th>{{ $t('dashboard.submitted') }}</th>
                      <th>{{ $t('dashboard.actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="license in pendingLicenses" :key="license.id">
                      <td>{{ license.player?.full_name }}</td>
                      <td>{{ license.club?.name }}</td>
                      <td>{{ license.license_type }}</td>
                      <td>{{ formatDate(license.created_at) }}</td>
                      <td>
                        <button @click="approveLicense(license.id)" class="btn btn-sm btn-success">
                          {{ $t('dashboard.approve') }}
                        </button>
                        <button @click="rejectLicense(license.id)" class="btn btn-sm btn-danger">
                          {{ $t('dashboard.reject') }}
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <CreateMatchModal 
      v-if="showCreateMatchModal" 
      @close="showCreateMatchModal = false"
      @created="onMatchCreated"
    />
    
    <CreatePlayerModal 
      v-if="showCreatePlayerModal" 
      @close="showCreatePlayerModal = false"
      @created="onPlayerCreated"
    />
    
    <LicenseQueueModal 
      v-if="showLicenseQueue" 
      @close="showLicenseQueue = false"
    />
  </div>
</template>

<script>
import apiService from '../services/ApiService.js';

export default {
  name: 'Dashboard',
  data() {
    return {
      loading: true,
      error: null,
      user: null,
      stats: {},
      matches: [],
      currentSeason: null,
      pendingLicenses: [],
      showCreateMatchModal: false,
      showCreatePlayerModal: false,
      showLicenseQueue: false
    };
  },
  async mounted() {
    await this.initializeDashboard();
  },
  methods: {
    async initializeDashboard() {
      try {
        this.loading = true;
        this.clearError();
        
        // Load user profile
        const profileResponse = await apiService.getProfile();
        if (profileResponse.success) {
          this.user = profileResponse.data;
        }
        
        // Load dashboard data in parallel
        await Promise.all([
          this.loadStats(),
          this.loadMatches(),
          this.loadCurrentSeason(),
          this.loadPendingLicenses()
        ]);
        
      } catch (error) {
        this.handleError(error);
      } finally {
        this.loading = false;
      }
    },
    
    async loadStats() {
      try {
        // Load basic stats from various endpoints
        const [matchesRes, playersRes, licensesRes, competitionsRes] = await Promise.all([
          apiService.getMatches({ per_page: 1 }),
          apiService.getPlayers({ per_page: 1 }),
          apiService.getPlayerLicenses({ status: 'pending', per_page: 1 }),
          apiService.getCompetitions({ status: 'active', per_page: 1 })
        ]);
        
        this.stats = {
          matches: matchesRes.success ? matchesRes.data.total : 0,
          players: playersRes.success ? playersRes.data.total : 0,
          pendingLicenses: licensesRes.success ? licensesRes.data.total : 0,
          competitions: competitionsRes.success ? competitionsRes.data.total : 0
        };
      } catch (error) {
        console.error('Error loading stats:', error);
      }
    },
    
    async loadMatches() {
      try {
        const response = await apiService.getMatches({ 
          per_page: 10,
          sort: 'match_date',
          order: 'desc'
        });
        
        if (response.success) {
          this.matches = response.data.data || [];
        }
      } catch (error) {
        this.handleError(error);
      }
    },
    
    async loadCurrentSeason() {
      try {
        const response = await apiService.getCurrentSeason();
        if (response.success) {
          this.currentSeason = response.data;
        }
      } catch (error) {
        console.error('Error loading current season:', error);
      }
    },
    
    async loadPendingLicenses() {
      try {
        const response = await apiService.getPlayerLicenses({ 
          status: 'pending',
          per_page: 10
        });
        
        if (response.success) {
          this.pendingLicenses = response.data.data || [];
        }
      } catch (error) {
        this.handleError(error);
      }
    },
    
    async approveLicense(licenseId) {
      try {
        const response = await apiService.approveLicense(licenseId);
        if (response.success) {
          this.showNotification('License approved successfully', 'success');
          await this.loadPendingLicenses();
          await this.loadStats();
        } else {
          this.showNotification('Failed to approve license', 'error');
        }
      } catch (error) {
        this.handleError(error);
      }
    },
    
    async rejectLicense(licenseId) {
      const reason = prompt('Please provide a reason for rejection:');
      if (!reason) return;
      
      try {
        const response = await apiService.rejectLicense(licenseId, reason);
        if (response.success) {
          this.showNotification('License rejected successfully', 'success');
          await this.loadPendingLicenses();
          await this.loadStats();
        } else {
          this.showNotification('Failed to reject license', 'error');
        }
      } catch (error) {
        this.handleError(error);
      }
    },
    
    async logout() {
      try {
        await apiService.logout();
        window.location.href = '/login';
      } catch (error) {
        console.error('Logout error:', error);
        window.location.href = '/login';
      }
    },
    
    viewMatch(matchId) {
      window.location.href = `/matches/${matchId}`;
    },
    
    async exportData() {
      try {
        const blob = await apiService.exportData('/matches/export');
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'matches-export.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      } catch (error) {
        this.handleError(error);
      }
    },
    
    onMatchCreated() {
      this.showCreateMatchModal = false;
      this.loadMatches();
      this.loadStats();
      this.showNotification('Match created successfully', 'success');
    },
    
    onPlayerCreated() {
      this.showCreatePlayerModal = false;
      this.loadStats();
      this.showNotification('Player created successfully', 'success');
    },
    
    formatDate(dateString) {
      if (!dateString) return this.$t('dashboard.na');
      return new Date(dateString).toLocaleDateString();
    },
    
    getStatusBadgeClass(status) {
      const classes = {
        'active': 'badge bg-success',
        'pending': 'badge bg-warning',
        'completed': 'badge bg-secondary',
        'cancelled': 'badge bg-danger',
        'draft': 'badge bg-info'
      };
      return classes[status] || 'badge bg-secondary';
    },
    
    handleError(error) {
      this.error = error.message || 'An error occurred';
      console.error('Dashboard error:', error);
    },
    
    clearError() {
      this.error = null;
    },
    
    showNotification(message, type = 'info') {
      // You can integrate with your preferred notification system
      console.log(`${type.toUpperCase()}: ${message}`);
    }
  }
};
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #dee2e6;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.stat-card {
  text-align: center;
  border: none;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-card .card-title {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 10px;
}

.stat-card .card-text {
  color: #007bff;
  font-weight: bold;
  margin: 0;
}

.season-dates {
  margin-top: 10px;
}

.table th {
  border-top: none;
  font-weight: 600;
}

.btn-group .btn {
  margin-right: 5px;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .btn-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  
  .btn-group .btn {
    margin-right: 0;
  }
}
</style> 