<!DOCTYPE html>
<html>
<head>
    <title>Test Vue.js Debug</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div id="fifa-app">
        <h1>Test Vue.js Debug - Portail</h1>
        
        <h2>Données du Joueur (Laravel) :</h2>
        <p><strong>Nom :</strong> {{ $player->first_name }} {{ $player->last_name }}</p>
        <p><strong>Club :</strong> {{ $player->club->name ?? 'Non défini' }}</p>
        <p><strong>Nationalité :</strong> {{ $player->nationality }}</p>
        
        <h2>Données FIFA (Laravel) :</h2>
        <p><strong>OVR :</strong> {{ $portalData['fifaStats']['overall_rating'] ?? 'N/A' }}</p>
        <p><strong>POT :</strong> {{ $portalData['fifaStats']['potential_rating'] ?? 'N/A' }}</p>
        <p><strong>Fitness :</strong> {{ $portalData['fifaStats']['fitness_score'] ?? 'N/A' }}</p>
        
        <h2>Test Vue.js :</h2>
        <div>
            <p><strong>OVR Vue :</strong> @{{ fifaStats.overall_rating }}</p>
            <p><strong>POT Vue :</strong> @{{ fifaStats.potential_rating }}</p>
            <p><strong>Fitness Vue :</strong> @{{ fifaStats.fitness_score }}</p>
        </div>
        
        <button @click="testClick">Test Click Vue.js</button>
        <p>@{{ message }}</p>
        
        <h2>Debug Info :</h2>
        <div id="debug-info">
            <p>Vue.js va afficher des informations ici</p>
        </div>
    </div>

    <script>
        console.log('🚀 Script chargé');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📱 DOM chargé');
            
            try {
                if (typeof Vue === 'undefined') {
                    throw new Error('❌ Vue.js n\'est pas chargé');
                }
                
                console.log('✅ Vue.js détecté, version:', Vue.version);
                
                const createApp = Vue.createApp;
                console.log('🔧 Création de l\'application Vue...');
                
                const app = createApp({
                    data() {
                        console.log('📊 Initialisation des données Vue...');
                        const data = {
                            fifaStats: @json($portalData['fifaStats'] ?? []),
                            performanceData: @json($portalData['performanceData'] ?? []),
                            sdohData: @json($portalData['sdohData'] ?? []),
                            message: 'Vue.js fonctionne !'
                        };
                        console.log('📊 Données Vue initialisées:', data);
                        return data;
                    },
                    methods: {
                        testClick() {
                            this.message = 'Click fonctionne ! ' + new Date().toLocaleTimeString();
                            console.log('✅ Click Vue.js fonctionne !');
                        }
                    },
                    mounted() {
                        console.log('🎯 Vue.js monté avec succès !');
                        console.log('📊 FIFA Stats:', this.fifaStats);
                        console.log('📊 Performance Data:', this.performanceData);
                        console.log('📊 SDOH Data:', this.sdohData);
                        
                        // Afficher les infos de debug
                        document.getElementById('debug-info').innerHTML = `
                            <p style="color: green;">✅ Vue.js monté avec succès !</p>
                            <p><strong>FIFA Stats:</strong> ${JSON.stringify(this.fifaStats)}</p>
                            <p><strong>Performance Data:</strong> ${JSON.stringify(this.performanceData)}</p>
                            <p><strong>SDOH Data:</strong> ${JSON.stringify(this.sdohData)}</p>
                        `;
                    }
                });
                
                console.log('🔧 Application Vue créée, montage en cours...');
                app.mount('#fifa-app');
                console.log('✅ Application Vue montée avec succès !');
                
            } catch (error) {
                console.error('❌ Erreur lors de l\'initialisation de Vue.js:', error);
                document.getElementById('debug-info').innerHTML = `
                    <div style="color: red;">
                        <h3>❌ Erreur Vue.js</h3>
                        <p>${error.message}</p>
                        <p>Vérifiez la console pour plus de détails</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>





