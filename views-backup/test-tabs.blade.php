<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des Onglets - TabView</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tab-view.js') }}"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Test du Composant TabView</h1>
        
        <!-- Test des onglets -->
        <div id="test-tabs" class="bg-white rounded-lg shadow-lg">
            <!-- Navigation des onglets -->
            <div class="tabs-nav">
                <button class="tab-button" data-tab="general">
                    <span class="tab-icon">👤</span>
                    <span class="tab-label">Informations Générales</span>
                </button>
                <button class="tab-button" data-tab="medical">
                    <span class="tab-icon">🏥</span>
                    <span class="tab-label">Médical</span>
                </button>
                <button class="tab-button" data-tab="performance">
                    <span class="tab-icon">📊</span>
                    <span class="tab-label">Performance</span>
                </button>
                <button class="tab-button" data-tab="settings">
                    <span class="tab-icon">⚙️</span>
                    <span class="tab-label">Paramètres</span>
                </button>
            </div>
            
            <!-- Contenu des onglets -->
            <div class="tab-content">
                <div data-tab-content="general" class="tab-panel">
                    <h2 class="text-2xl font-bold mb-4">Informations Générales</h2>
                    <p class="text-gray-600">Contenu de l'onglet Informations Générales</p>
                    <div class="mt-4 p-4 bg-blue-50 rounded">
                        <p>Cet onglet contient les informations de base du joueur.</p>
                    </div>
                </div>
                
                <div data-tab-content="medical" class="tab-panel">
                    <h2 class="text-2xl font-bold mb-4">Dossier Médical</h2>
                    <p class="text-gray-600">Contenu de l'onglet Médical</p>
                    <div class="mt-4 p-4 bg-green-50 rounded">
                        <p>Ici se trouvent tous les dossiers médicaux et évaluations.</p>
                    </div>
                </div>
                
                <div data-tab-content="performance" class="tab-panel">
                    <h2 class="text-2xl font-bold mb-4">Performance</h2>
                    <p class="text-gray-600">Contenu de l'onglet Performance</p>
                    <div class="mt-4 p-4 bg-purple-50 rounded">
                        <p>Statistiques et métriques de performance du joueur.</p>
                    </div>
                </div>
                
                <div data-tab-content="settings" class="tab-panel">
                    <h2 class="text-2xl font-bold mb-4">Paramètres</h2>
                    <p class="text-gray-600">Contenu de l'onglet Paramètres</p>
                    <div class="mt-4 p-4 bg-yellow-50 rounded">
                        <p>Configuration et préférences de l'utilisateur.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations de débogage -->
        <div class="mt-8 p-4 bg-gray-100 rounded">
            <h3 class="font-bold mb-2">Informations de Débogage</h3>
            <p>Onglet actif: <span id="active-tab-info">-</span></p>
            <p>Nombre d'onglets: <span id="tabs-count">-</span></p>
            <button onclick="testTabView()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tester TabView
            </button>
        </div>
    </div>

    <script>
        // Initialisation du composant TabView
        let tabView;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, initialisation de TabView...');
            
            try {
                tabView = new TabView('test-tabs', {
                    defaultTab: 'general',
                    onTabChange: function(tabId) {
                        console.log('Onglet changé vers:', tabId);
                        document.getElementById('active-tab-info').textContent = tabId;
                    }
                });
                
                console.log('TabView initialisé avec succès!');
                console.log('Onglets trouvés:', tabView.getTabs());
                
                // Mettre à jour les informations de débogage
                document.getElementById('active-tab-info').textContent = tabView.getActiveTab();
                document.getElementById('tabs-count').textContent = tabView.getTabs().length;
                
                // Écouter les événements de changement d'onglet
                document.getElementById('test-tabs').addEventListener('tab-change', function(event) {
                    console.log('Événement tab-change reçu:', event.detail);
                });
                
            } catch (error) {
                console.error('Erreur lors de l\'initialisation de TabView:', error);
                alert('Erreur lors de l\'initialisation de TabView: ' + error.message);
            }
        });
        
        function testTabView() {
            if (tabView) {
                console.log('Test de TabView:');
                console.log('- Onglet actif:', tabView.getActiveTab());
                console.log('- Onglets disponibles:', tabView.getTabs().map(t => t.id));
                
                // Tester le changement d'onglet
                const nextTab = tabView.getTabs().find(t => t.id !== tabView.getActiveTab());
                if (nextTab) {
                    console.log('Changement vers l\'onglet:', nextTab.id);
                    tabView.setActiveTab(nextTab.id);
                }
            } else {
                console.error('TabView non initialisé');
                alert('TabView non initialisé');
            }
        }
    </script>
</body>
</html>
