import './bootstrap';
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'

// Import composants de test (qui fonctionnent)
import BasicTest from './components/BasicTest.vue'
import SimpleTest from './components/SimpleTest.vue'
import DebugTest from './components/DebugTest.vue'
import UltraDebug from './components/UltraDebug.vue'
import ForceDisplay from './components/ForceDisplay.vue'

// Import composants FIFA (avec gestion d'erreur)
let FifaDashboard = null
let FifaNavigation = null
let FifaCard = null
let FifaButton = null
let FitDashboard = null
let PlayersList = null
let FifaTestPage = null

try {
  FifaDashboard = require('./components/FifaDashboard.vue').default
  console.log('✅ FifaDashboard importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FifaDashboard:', error.message)
  FifaDashboard = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FifaDashboard - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  FifaNavigation = require('./components/FifaNavigation.vue').default
  console.log('✅ FifaNavigation importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FifaNavigation:', error.message)
  FifaNavigation = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FifaNavigation - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  FifaCard = require('./components/FifaCard.vue').default
  console.log('✅ FifaCard importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FifaCard:', error.message)
  FifaCard = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FifaCard - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  FifaButton = require('./components/FifaButton.vue').default
  console.log('✅ FifaButton importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FifaButton:', error.message)
  FifaButton = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FifaButton - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  FitDashboard = require('./components/FitDashboard.vue').default
  console.log('✅ FitDashboard importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FitDashboard:', error.message)
  FitDashboard = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FitDashboard - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  PlayersList = require('./components/players/PlayersList.vue').default
  console.log('✅ PlayersList importé avec succès')
} catch (error) {
  console.log('❌ Erreur import PlayersList:', error.message)
  PlayersList = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>PlayersList - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

try {
  FifaTestPage = require('./components/FifaTestPage.vue').default
  console.log('✅ FifaTestPage importé avec succès')
} catch (error) {
  console.log('❌ Erreur import FifaTestPage:', error.message)
  FifaTestPage = {
    template: '<div style="padding: 20px; background: #f0f0f0; border: 2px solid red;"><h2>FifaTestPage - Composant non disponible</h2><p>Erreur: ' + error.message + '</p></div>'
  }
}

// Create Vue App
const app = createApp({
  name: 'FITApp',
  components: {
    FifaDashboard,
    FifaNavigation,
    FifaCard,
    FifaButton,
    FitDashboard,
    PlayersList,
    FifaTestPage,
    SimpleTest,
    DebugTest,
    UltraDebug,
    ForceDisplay,
    BasicTest
  }
})

// Create Router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: BasicTest
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: FifaDashboard || BasicTest
    },
    {
      path: '/players',
      name: 'players',
      component: PlayersList || BasicTest
    },
    {
      path: '/medical',
      name: 'medical',
      component: FifaDashboard || BasicTest
    },
    {
      path: '/analytics',
      name: 'analytics',
      component: FifaDashboard || BasicTest
    },
    {
      path: '/settings',
      name: 'settings',
      component: FifaDashboard || BasicTest
    },
    {
      path: '/profile',
      name: 'profile',
      component: FifaDashboard || BasicTest
    },
    {
      path: '/test',
      name: 'test',
      component: FifaTestPage || BasicTest
    },
    {
      path: '/simple-test',
      name: 'simple-test',
      component: SimpleTest
    }
  ]
})

// Create Pinia Store
const pinia = createPinia()

// Global Properties
app.config.globalProperties.$fifa = {
  version: '1.0.0',
  theme: 'fifa-light'
}

// Global Components
app.component('FifaCard', FifaCard)
app.component('FifaButton', FifaButton)

// Use Plugins
app.use(router)
app.use(pinia)

// Mount App
app.mount('#app')

console.log('🟢 Application Vue.js progressive montée avec succès')

// Hide loading screen after Vue app is mounted
setTimeout(() => {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        loadingScreen.style.pointerEvents = 'none';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 300);
    }
}, 100);

// Export for use in other files
export default app 