<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Ultimate Dashboard - Simple</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .fifa-nav-tab {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        .fifa-nav-tab.active {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen">
        <!-- Hero Zone Simple -->
        <div class="bg-gradient-to-r from-blue-900 to-purple-900 text-white p-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl font-bold mb-4">FIFA Ultimate Dashboard</h1>
                <p class="text-xl">Portail du joueur - Version Simple</p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 mb-6 mt-8">
            <div class="flex flex-wrap gap-4">
                <button 
                    v-for="tab in navigationTabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['fifa-nav-tab relative px-6 py-3 rounded-lg text-white font-medium transition-all', activeTab === tab.id ? 'active' : '']"
                >
                    <i :class="tab.icon" class="mr-2"></i>
                    <span v-text="tab.name"></span>
                    <span v-if="tab.count" class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs" v-text="tab.count"></span>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="px-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4" v-text="'Onglet actif : ' + activeTab"></h2>
                <p class="text-gray-600">Contenu de l'onglet sélectionné</p>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    activeTab: 'performance',
                    navigationTabs: [
                        { id: 'performance', name: 'Performance', icon: 'fas fa-chart-line', count: null },
                        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell', count: 12 },
                        { id: 'health', name: 'Santé & Bien-être', icon: 'fas fa-heart', count: null },
                        { id: 'medical', name: 'Médical', icon: 'fas fa-user-md', count: 4 },
                        { id: 'devices', name: 'Devices', icon: 'fas fa-mobile-alt', count: 3 },
                        { id: 'doping', name: 'Dopage', icon: 'fas fa-exclamation-triangle', count: 2 }
                    ]
                }
            },
            mounted() {
                console.log('Vue.js mounted successfully!');
                console.log('Active tab:', this.activeTab);
                console.log('Navigation tabs:', this.navigationTabs);
                alert('Vue.js est monté! Onglets: ' + this.navigationTabs.length);
            }
        }).mount('#app');
    </script>
</body>
</html>



