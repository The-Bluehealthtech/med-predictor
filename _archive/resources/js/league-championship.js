import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import LeagueDashboard from './components/LeagueChampionship/LeagueDashboard.vue'
import CompetitionSchedule from './components/LeagueChampionship/CompetitionSchedule.vue'
import CompetitionStandings from './components/LeagueChampionship/CompetitionStandings.vue'
import MatchSheet from './components/LeagueChampionship/MatchSheet.vue'
import RosterSubmission from './components/LeagueChampionship/RosterSubmission.vue'
import './bootstrap'

// Production configuration
const isProduction = window.AppConfig?.environment === 'production'

// Create router with proper base path
const router = createRouter({
  history: createWebHistory('/league-championship/'),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: LeagueDashboard,
      props: { competitionId: 1 }
    },
    {
      path: '/competition/:id',
      name: 'competition-detail',
      component: LeagueDashboard,
      props: true
    },
    {
      path: '/competition/:id/schedule',
      name: 'competition-schedule',
      component: CompetitionSchedule,
      props: true
    },
    {
      path: '/competition/:id/standings',
      name: 'competition-standings',
      component: CompetitionStandings,
      props: true
    },
    {
      path: '/match/:id',
      name: 'match-sheet',
      component: MatchSheet,
      props: true
    },
    {
      path: '/match/:id/roster',
      name: 'roster-submission',
      component: RosterSubmission,
      props: true
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: LeagueDashboard,
      props: { competitionId: 1 }
    }
  ]
})

// Create Vue app with production-ready template
const app = createApp({
  template: `
    <div id="app">
      <router-view v-slot="{ Component, route }">
        <transition name="fade" mode="out-in">
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </div>
  `,
  data() {
    return {
      loading: false,
      error: null,
      notifications: {
        success: null,
        error: null,
        warning: null
      }
    }
  },
  mounted() {
    // Initialize production features
    this.initializeProductionFeatures()
    
    // Set up global error handling
    this.$nextTick(() => {
      this.loading = false
    })
  },
  methods: {
    initializeProductionFeatures() {
      // Performance monitoring
      if (window.performance && window.performance.mark) {
        window.performance.mark('app-mounted')
      }
      
      // Error tracking setup
      if (isProduction) {
        this.setupErrorTracking()
      }
      
      // Real-time updates if enabled
      if (window.AppConfig?.features?.realTimeUpdates) {
        this.setupRealTimeUpdates()
      }
    },
    
    setupErrorTracking() {
      // Production error tracking
      window.addEventListener('unhandledrejection', (event) => {
        console.error('Unhandled promise rejection:', event.reason)
        this.showNotification('error', 'An unexpected error occurred. Please try again.')
      })
    },
    
    setupRealTimeUpdates() {
      // Setup WebSocket or polling for real-time updates
      console.log('Real-time updates enabled')
    },
    
    showNotification(type, message, duration = 5000) {
      this.notifications[type] = message
      
      if (duration > 0) {
        setTimeout(() => {
          this.notifications[type] = null
        }, duration)
      }
    }
  }
})

// Global error handler for production
app.config.errorHandler = (err, vm, info) => {
  console.error('Vue Error:', err)
  console.error('Error info:', info)
  console.error('Component:', vm?.$options?.name || 'Unknown')
  
  // Show user-friendly error message
  if (vm && vm.showNotification) {
    vm.showNotification('error', 'An error occurred. Please refresh the page.')
  }
  
  // In production, you might want to send this to an error tracking service
  if (isProduction) {
    // Send to error tracking service (e.g., Sentry)
    console.log('Error would be sent to tracking service in production')
  }
}

// Global properties for production
app.config.globalProperties.$isProduction = isProduction
app.config.globalProperties.$appConfig = window.AppConfig || {}

// Use router
app.use(router)

// Mount app to the correct element
const appElement = document.getElementById('match-app') || document.getElementById('app')
if (appElement) {
  try {
    app.mount(appElement)
    
    // Performance mark for app ready
    if (window.performance && window.performance.mark) {
      window.performance.mark('app-ready')
      window.performance.measure('app-startup', 'app-mounted', 'app-ready')
    }
    
  } catch (error) {
    console.error('Failed to mount app:', error)
    
    // Fallback error display
    appElement.innerHTML = `
      <div class="flex items-center justify-center min-h-64">
        <div class="text-center">
          <div class="text-red-600 mb-4">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Application Error</h3>
          <p class="text-gray-600 mb-4">Failed to load the application. Please refresh the page.</p>
          <button onclick="window.location.reload()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
            Refresh Page
          </button>
        </div>
      </div>
    `
  }
} else {
  console.error('App element not found')
}

// Export for potential external use
window.LeagueChampionship = app

// Production-ready global functions
window.showAppNotification = (type, message) => {
  if (app._instance && app._instance.proxy.showNotification) {
    app._instance.proxy.showNotification(type, message)
  }
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  if (window.performance && window.performance.mark) {
    window.performance.mark('app-unload')
  }
}) 