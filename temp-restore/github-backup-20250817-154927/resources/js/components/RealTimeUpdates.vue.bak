<template>
  <div class="real-time-updates">
    <!-- Real-time notification toast -->
    <div v-if="showNotification" 
         :class="['notification-toast', notificationType]"
         @click="hideNotification">
      <div class="notification-content">
        <div class="notification-icon">
          <svg v-if="notificationType === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <svg v-else-if="notificationType === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
        </div>
        <div class="notification-text">
          <h4 class="notification-title">{{ notificationTitle }}</h4>
          <p class="notification-message">{{ notificationMessage }}</p>
        </div>
        <button @click="hideNotification" class="notification-close">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Connection status indicator -->
    <div class="connection-status" :class="{ 'connected': isConnected, 'disconnected': !isConnected }">
      <div class="status-indicator"></div>
      <span class="status-text">{{ connectionStatusText }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RealTimeUpdates',
  data() {
    return {
      showNotification: false,
      notificationTitle: '',
      notificationMessage: '',
      notificationType: 'info',
      isConnected: false,
      connectionStatusText: 'Connecting...',
      notificationTimeout: null
    }
  },
  mounted() {
    this.initializeRealTimeUpdates();
  },
  beforeUnmount() {
    this.cleanup();
  },
  methods: {
    initializeRealTimeUpdates() {
      if (typeof window.Echo !== 'undefined') {
        this.setupConnectionStatus();
        this.setupEventListeners();
        this.isConnected = true;
        this.connectionStatusText = 'Connected';
      } else {
        console.warn('Laravel Echo not available');
        this.connectionStatusText = 'Not available';
      }
    },

    setupConnectionStatus() {
      window.Echo.connector.pusher.connection.bind('connected', () => {
        this.isConnected = true;
        this.connectionStatusText = 'Connected';
      });

      window.Echo.connector.pusher.connection.bind('disconnected', () => {
        this.isConnected = false;
        this.connectionStatusText = 'Disconnected';
      });

      window.Echo.connector.pusher.connection.bind('error', () => {
        this.isConnected = false;
        this.connectionStatusText = 'Error';
      });
    },

    setupEventListeners() {
      // League Championship events
      window.Echo.channel('league-championship')
        .listen('MatchUpdated', (e) => {
          this.showNotificationMessage(
            'Match Updated',
            `Match ${e.match.home_team?.name || 'Home'} vs ${e.match.away_team?.name || 'Away'} has been updated`,
            'success'
          );
          this.$emit('match-updated', e.match);
        });

      // Lineup events
      window.Echo.channel('lineups')
        .listen('LineupUpdated', (e) => {
          this.showNotificationMessage(
            'Lineup Updated',
            `Lineup for ${e.lineup.team?.name || 'Team'} has been updated`,
            'success'
          );
          this.$emit('lineup-updated', e.lineup);
        });

      // Player Registration events
      window.Echo.channel('player-registration')
        .listen('PlayerRegistered', (e) => {
          this.showNotificationMessage(
            'Player Registered',
            `${e.player.first_name} ${e.player.last_name} has been registered`,
            'success'
          );
          this.$emit('player-registered', e.player);
        });

      // Healthcare events
      window.Echo.channel('healthcare')
        .listen('HealthRecordCreated', (e) => {
          this.showNotificationMessage(
            'Health Record Created',
            `Health record for ${e.healthRecord.player?.first_name || 'Player'} has been created`,
            'success'
          );
          this.$emit('health-record-created', e.healthRecord);
        })
        .listen('MedicalPredictionCreated', (e) => {
          this.showNotificationMessage(
            'Medical Prediction Created',
            `Medical prediction for ${e.prediction.player?.first_name || 'Player'} has been generated`,
            'success'
          );
          this.$emit('medical-prediction-created', e.prediction);
        });

      // Club Management events
      window.Echo.channel('club-management')
        .listen('TeamCreated', (e) => {
          this.showNotificationMessage(
            'Team Created',
            `Team ${e.team.name} has been created`,
            'success'
          );
          this.$emit('team-created', e.team);
        });

      // Specific match events
      window.Echo.channel('match.*')
        .listen('MatchUpdated', (e) => {
          this.showNotificationMessage(
            'Match Updated',
            `Match details have been updated`,
            'info'
          );
          this.$emit('match-updated', e.match);
        });

      // Specific team events
      window.Echo.channel('team.*')
        .listen('LineupUpdated', (e) => {
          this.showNotificationMessage(
            'Lineup Updated',
            `Team lineup has been updated`,
            'info'
          );
          this.$emit('lineup-updated', e.lineup);
        });

      // Specific player events
      window.Echo.channel('player.*')
        .listen('HealthRecordCreated', (e) => {
          this.showNotificationMessage(
            'Health Record Created',
            `Player health record has been updated`,
            'info'
          );
          this.$emit('health-record-created', e.healthRecord);
        })
        .listen('MedicalPredictionCreated', (e) => {
          this.showNotificationMessage(
            'Medical Prediction Created',
            `Player medical prediction has been generated`,
            'info'
          );
          this.$emit('medical-prediction-created', e.prediction);
        });

      // Specific club events
      window.Echo.channel('club.*')
        .listen('TeamCreated', (e) => {
          this.showNotificationMessage(
            'Team Created',
            `New team has been created`,
            'info'
          );
          this.$emit('team-created', e.team);
        })
        .listen('PlayerRegistered', (e) => {
          this.showNotificationMessage(
            'Player Registered',
            `New player has been registered`,
            'info'
          );
          this.$emit('player-registered', e.player);
        });
    },

    showNotificationMessage(title, message, type = 'info') {
      this.notificationTitle = title;
      this.notificationMessage = message;
      this.notificationType = type;
      this.showNotification = true;

      // Auto-hide after 5 seconds
      if (this.notificationTimeout) {
        clearTimeout(this.notificationTimeout);
      }
      this.notificationTimeout = setTimeout(() => {
        this.hideNotification();
      }, 5000);
    },

    hideNotification() {
      this.showNotification = false;
      if (this.notificationTimeout) {
        clearTimeout(this.notificationTimeout);
        this.notificationTimeout = null;
      }
    },

    cleanup() {
      if (typeof window.Echo !== 'undefined') {
        window.Echo.disconnect();
      }
      if (this.notificationTimeout) {
        clearTimeout(this.notificationTimeout);
      }
    }
  }
}
</script>

<style scoped>
.real-time-updates {
  position: relative;
}

.notification-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  max-width: 400px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-left: 4px solid;
  animation: slideIn 0.3s ease-out;
}

.notification-toast.success {
  border-left-color: #10b981;
}

.notification-toast.error {
  border-left-color: #ef4444;
}

.notification-toast.info {
  border-left-color: #3b82f6;
}

.notification-content {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  gap: 12px;
}

.notification-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.notification-icon.success {
  color: #10b981;
}

.notification-icon.error {
  color: #ef4444;
}

.notification-icon.info {
  color: #3b82f6;
}

.notification-text {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-weight: 600;
  font-size: 14px;
  margin: 0 0 4px 0;
  color: #1f2937;
}

.notification-message {
  font-size: 13px;
  margin: 0;
  color: #6b7280;
  line-height: 1.4;
}

.notification-close {
  flex-shrink: 0;
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.notification-close:hover {
  color: #6b7280;
}

.connection-status {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  font-size: 12px;
  z-index: 1000;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #9ca3af;
  transition: background-color 0.3s;
}

.connection-status.connected .status-indicator {
  background: #10b981;
}

.connection-status.disconnected .status-indicator {
  background: #ef4444;
}

.status-text {
  color: #6b7280;
  font-weight: 500;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style> 