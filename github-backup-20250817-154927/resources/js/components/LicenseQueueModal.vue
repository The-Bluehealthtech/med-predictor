<template>
  <div class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h5 class="modal-title">{{ $t('auto.key141') }}</h5>
        <button @click="closeModal" class="btn-close" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="mb-3">
          <div class="btn-group" role="group">
            <button 
              @click="setFilter('pending')" 
              :class="['btn', filter === 'pending' ? 'btn-primary' : 'btn-outline-primary']"
            >
              Pending
            </button>
            <button 
              @click="setFilter('approved')" 
              :class="['btn', filter === 'approved' ? 'btn-primary' : 'btn-outline-primary']"
            >
              Approved
            </button>
            <button 
              @click="setFilter('rejected')" 
              :class="['btn', filter === 'rejected' ? 'btn-primary' : 'btn-outline-primary']"
            >
              Rejected
            </button>
          </div>
        </div>
        
        <div v-if="loading" class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ $t('auto.key142') }}</span>
          </div>
        </div>
        
        <div v-else-if="licenses.length === 0" class="text-center text-muted">
          No licenses found
        </div>
        
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ $t('auto.key143') }}</th>
                <th>{{ $t('auto.key144') }}</th>
                <th>{{ $t('auto.key145') }}</th>
                <th>{{ $t('auto.key146') }}</th>
                <th>{{ $t('auto.key147') }}</th>
                <th v-if="filter === 'pending'">{{ $t('auto.key148') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="license in licenses" :key="license.id">
                <td>{{ license.player?.full_name }}</td>
                <td>{{ license.club?.name }}</td>
                <td>{{ license.license_type }}</td>
                <td>
                  <span :class="getStatusBadgeClass(license.status)">
                    {{ license.status }}
                  </span>
                </td>
                <td>{{ formatDate(license.created_at) }}</td>
                <td v-if="filter === 'pending'">
                  <button @click="approveLicense(license.id)" class="btn btn-sm btn-success me-1">
                    Approve
                  </button>
                  <button @click="rejectLicense(license.id)" class="btn btn-sm btn-danger">
                    Reject
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="modal-footer">
        <button @click="closeModal" class="btn btn-secondary">{{ $t('auto.key149') }}</button>
      </div>
    </div>
  </div>
</template>

<script>
import apiService from '../services/ApiService.js';

export default {
  name: 'LicenseQueueModal',
  data() {
    return {
      loading: false,
      licenses: [],
      filter: 'pending'
    };
  },
  async mounted() {
    await this.loadLicenses();
  },
  methods: {
    async loadLicenses() {
      try {
        this.loading = true;
        
        const response = await apiService.getPlayerLicenses({ 
          status: this.filter,
          per_page: 50
        });
        
        if (response.success) {
          this.licenses = response.data.data || [];
        }
      } catch (error) {
        console.error('Error loading licenses:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async setFilter(status) {
      this.filter = status;
      await this.loadLicenses();
    },
    
    async approveLicense(licenseId) {
      try {
        const response = await apiService.approveLicense(licenseId);
        if (response.success) {
          await this.loadLicenses();
          this.$emit('license-updated');
        }
      } catch (error) {
        console.error('Error approving license:', error);
      }
    },
    
    async rejectLicense(licenseId) {
      const reason = prompt('Please provide a reason for rejection:');
      if (!reason) return;
      
      try {
        const response = await apiService.rejectLicense(licenseId, reason);
        if (response.success) {
          await this.loadLicenses();
          this.$emit('license-updated');
        }
      } catch (error) {
        console.error('Error rejecting license:', error);
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString();
    },
    
    getStatusBadgeClass(status) {
      const classes = {
        'pending': 'badge bg-warning',
        'approved': 'badge bg-success',
        'rejected': 'badge bg-danger'
      };
      return classes[status] || 'badge bg-secondary';
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
  max-width: 800px;
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
}

.table th {
  border-top: none;
  font-weight: 600;
}
</style> 