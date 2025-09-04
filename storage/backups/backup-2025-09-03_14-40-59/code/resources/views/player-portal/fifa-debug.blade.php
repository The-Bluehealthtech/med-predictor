<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Debug - Test Simple</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div id="app" class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">FIFA Debug - Test Simple</h1>
        
        <!-- Test HTML Statique -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">✅ Test HTML Statique</h2>
            <p class="text-green-600">Si vous voyez ce texte, HTML fonctionne</p>
            <div class="bg-blue-100 p-4 rounded mt-4">
                <p class="font-bold">Zone de test statique</p>
                <p>Cette zone doit être visible immédiatement</p>
            </div>
        </div>
        
        <!-- Test Vue.js -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">🔧 Test Vue.js</h2>
            <div v-if="vueWorking" class="text-green-600">
                <p>✅ Vue.js fonctionne !</p>
                <p>Message: <span v-text="message"></span></p>
                <p>Compteur: <span v-text="counter"></span></p>
                <button @click="increment" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">
                    Incrémenter
                </button>
            </div>
            <div v-else class="text-red-600">
                <p>❌ Vue.js ne fonctionne pas</p>
                <p>Vérifiez la console pour les erreurs</p>
            </div>
        </div>
        
        <!-- Test Tabs -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">📑 Test Tabs</h2>
            <div class="flex space-x-2 mb-4">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['px-4 py-2 rounded', activeTab === tab.id ? 'bg-blue-500 text-white' : 'bg-gray-200']"
                >
                    <span v-text="tab.name"></span>
                </button>
            </div>
            <div v-for="tab in tabs" :key="tab.id" v-show="activeTab === tab.id" class="p-4 bg-gray-50 rounded">
                <h3 v-text="tab.name"></h3>
                <p v-text="tab.content"></p>
            </div>
        </div>
        
        <!-- Console Debug -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">🐛 Console Debug</h2>
            <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm max-h-40 overflow-y-auto">
                <div v-for="log in consoleLogs" :key="log.id" class="mb-1">
                    <span class="text-gray-400">[{{ log.timestamp }}]</span> 
                    <span v-text="log.message"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('Script chargé - début de l\'initialisation');
        
        try {
            const { createApp } = Vue;
            console.log('Vue importé avec succès');
            
            const app = createApp({
                data() {
                    return {
                        vueWorking: false,
                        message: 'Vue.js fonctionne parfaitement !',
                        counter: 0,
                        activeTab: 'tab1',
                        tabs: [
                            { id: 'tab1', name: 'Performance', content: 'Contenu de l\'onglet Performance' },
                            { id: 'tab2', name: 'Notifications', content: 'Contenu de l\'onglet Notifications' },
                            { id: 'tab3', name: 'SDOH', content: 'Contenu de l\'onglet SDOH' }
                        ],
                        consoleLogs: []
                    }
                },
                mounted() {
                    console.log('Vue.js monté avec succès !');
                    this.vueWorking = true;
                    this.log('Vue.js monté avec succès !');
                    this.log('Active tab: ' + this.activeTab);
                    this.log('Nombre de tabs: ' + this.tabs.length);
                },
                methods: {
                    increment() {
                        this.counter++;
                        this.log('Compteur incrémenté: ' + this.counter);
                    },
                    log(message) {
                        const timestamp = new Date().toLocaleTimeString();
                        this.consoleLogs.unshift({
                            id: Date.now(),
                            timestamp: timestamp,
                            message: message
                        });
                        console.log(`[${timestamp}] ${message}`);
                    }
                }
            });
            
            console.log('Application Vue créée, montage en cours...');
            app.mount('#app');
            console.log('Application Vue montée !');
            
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de Vue.js:', error);
            document.body.innerHTML += `
                <div style="position: fixed; top: 20px; right: 20px; background: red; color: white; padding: 20px; border-radius: 8px; z-index: 9999;">
                    <h3>❌ Erreur Vue.js</h3>
                    <p>${error.message}</p>
                    <p>Vérifiez la console pour plus de détails</p>
                </div>
            `;
        }
    </script>
</body>
</html>
