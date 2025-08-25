<!DOCTYPE html>
<html>
<head>
    <title>Test Vue.js Ultra Simple</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div id="app">
        <h1>Test Vue.js</h1>
        <p>Message: {{ message }}</p>
        <p>Compteur: {{ counter }}</p>
        <button @click="increment">Cliquer</button>
        
        <div v-if="showTabs">
            <h2>Onglets:</h2>
            <button v-for="tab in tabs" :key="tab.id" @click="selectTab(tab.id)">
                {{ tab.name }}
            </button>
            <p>Onglet actif: {{ activeTab }}</p>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    message: 'Vue.js fonctionne!',
                    counter: 0,
                    showTabs: true,
                    activeTab: 'performance',
                    tabs: [
                        { id: 'performance', name: 'Performance' },
                        { id: 'notifications', name: 'Notifications' },
                        { id: 'health', name: 'Santé' }
                    ]
                }
            },
            mounted() {
                console.log('Vue.js monté avec succès!');
                alert('Vue.js est monté! Onglets: ' + this.tabs.length);
            },
            methods: {
                increment() {
                    this.counter++;
                },
                selectTab(tabId) {
                    this.activeTab = tabId;
                }
            }
        }).mount('#app');
    </script>
</body>
</html>











