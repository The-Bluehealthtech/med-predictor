import { createApp } from 'vue'

// Composant ultra-simple
const SimpleComponent = {
  name: 'SimpleComponent',
  template: `
    <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: purple; color: white; z-index: 99999; display: flex; flex-direction: column; justify-content: center; align-items: center; font-size: 48px; font-weight: bold; text-align: center;">
      <h1>🟣 VUE.JS SIMPLE TEST</h1>
      <p>Si vous voyez ceci, Vue.js fonctionne !</p>
      <p>Compteur: {{ count }}</p>
      <button @click="count++" style="background: white; color: purple; padding: 30px; border: none; font-size: 32px; cursor: pointer; margin: 30px; border-radius: 10px;">
        CLICK ME
      </button>
      <p style="font-size: 24px; margin-top: 30px;">Timestamp: {{ timestamp }}</p>
    </div>
  `,
  data() {
    return {
      count: 0,
      timestamp: new Date().toLocaleTimeString()
    }
  },
  mounted() {
    console.log('🟣 SimpleComponent mounted!')
    alert('Vue.js fonctionne ! Composant SimpleComponent monté.')
    
    // Forcer la suppression de l'écran de chargement
    const loadingScreen = document.getElementById('loading-screen')
    if (loadingScreen) {
      loadingScreen.style.display = 'none'
      console.log('Écran de chargement masqué')
    }
  },
  updated() {
    this.timestamp = new Date().toLocaleTimeString()
  }
}

// Créer l'application Vue
const app = createApp(SimpleComponent)

// Monter l'application
app.mount('#app')

console.log('🟣 Vue app créée et montée')

// Export pour debug
export default app 