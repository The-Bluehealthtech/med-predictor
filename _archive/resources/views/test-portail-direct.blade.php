<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Direct Portail Principal FIFA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1f2937; color: white; }
        .test-section { background: #374151; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .info { color: #3b82f6; }
        button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2563eb; }
        .log { background: #111827; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; max-height: 400px; overflow-y: auto; }
        .element-status { display: inline-block; margin: 5px; padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .element-found { background: #10b981; color: white; }
        .element-not-found { background: #ef4444; color: white; }
        .iframe-container { width: 100%; height: 800px; border: 2px solid #4b5563; border-radius: 8px; margin: 20px 0; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>
    <h1>🎯 Test Direct Portail Principal FIFA</h1>
    
    <div class="test-section">
        <h2>🚀 Portail Principal FIFA (Direct)</h2>
        <p><strong>IMPORTANT :</strong> Cette page charge directement le portail principal pour identifier le problème !</p>
        <div class="iframe-container">
            <iframe id="portail-iframe" src="/portail-joueur/7"></iframe>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🔍 Diagnostic des Éléments FIFA</h2>
        <button onclick="diagnosticComplet()">🔍 Diagnostic Complet FIFA</button>
        <div id="diagnostic-result"></div>
    </div>
    
    <div class="test-section">
        <h2>📊 Logs de Diagnostic</h2>
        <div id="logs" class="log"></div>
    </div>

    <script>
        // Fonction de logging
        function log(message, type = 'info') {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
            
            logsDiv.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
            logsDiv.scrollTop = logsDiv.scrollHeight;
            console.log(message);
        }

        // Diagnostic complet des éléments FIFA
        function diagnosticComplet() {
            log('🔍 Diagnostic complet des éléments FIFA...');
            const resultDiv = document.getElementById('diagnostic-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                log('✅ Accès au DOM de l\'iframe réussi');
                
                // 1. Vérifier l'onglet Performance principal
                const performanceTab = iframeDoc.getElementById('performance-tab');
                if (performanceTab) {
                    log('✅ performance-tab trouvé');
                    const isVisible = performanceTab.style.display !== 'none' && performanceTab.offsetParent !== null;
                    log(`   Visibilité: ${isVisible ? 'Visible' : 'Masqué'}`);
                } else {
                    log('❌ performance-tab NON trouvé !', 'error');
                }
                
                // 2. Vérifier le dashboard de performance
                const performanceDashboard = iframeDoc.getElementById('performance-dashboard');
                if (performanceDashboard) {
                    log('✅ performance-dashboard trouvé');
                } else {
                    log('❌ performance-dashboard NON trouvé !', 'error');
                }
                
                // 3. Vérifier le contenu de performance
                const performanceContent = iframeDoc.getElementById('performance-content');
                if (performanceContent) {
                    log('✅ performance-content trouvé');
                    const hasHiddenClass = performanceContent.classList.contains('hidden');
                    const isDisplayNone = performanceContent.style.display === 'none';
                    log(`   Classe 'hidden': ${hasHiddenClass}`);
                    log(`   Style 'display: none': ${isDisplayNone}`);
                } else {
                    log('❌ performance-content NON trouvé !', 'error');
                }
                
                // 4. Vérifier les éléments FIFA spécifiques
                const fifaElements = [
                    'dynamic-overall-rating',
                    'dynamic-goals',
                    'dynamic-assists',
                    'quick-goals',
                    'quick-assists',
                    'quick-form'
                ];
                
                let foundCount = 0;
                let notFoundCount = 0;
                let elementsStatus = '';
                
                fifaElements.forEach(elementId => {
                    const element = iframeDoc.getElementById(elementId);
                    if (element) {
                        foundCount++;
                        const currentValue = element.textContent;
                        const isVisible = element.offsetParent !== null && element.style.display !== 'none';
                        const visibilityClass = isVisible ? 'element-found' : 'element-not-found';
                        const visibilityText = isVisible ? 'Visible' : 'Masqué';
                        elementsStatus += `<span class="element-status ${visibilityClass}">${elementId} ${isVisible ? '✅' : '❌'} "${currentValue}" (${visibilityText})</span>`;
                        log(`✅ ${elementId} trouvé: "${currentValue}" (${visibilityText})`);
                    } else {
                        notFoundCount++;
                        elementsStatus += `<span class="element-status element-not-found">${elementId} ❌ Non trouvé</span>`;
                        log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                    }
                });
                
                // 5. Vérifier la structure des onglets
                const performanceTabs = iframeDoc.querySelector('.performance-tabs');
                if (performanceTabs) {
                    const tabButtons = performanceTabs.querySelectorAll('button');
                    log(`📊 ${tabButtons.length} boutons d'onglets trouvés`);
                    
                    tabButtons.forEach((button, index) => {
                        const isActive = button.classList.contains('bg-blue-600');
                        const buttonText = button.textContent.trim();
                        log(`   Bouton ${index + 1}: ${buttonText} (actif: ${isActive ? '✅' : '❌'})`);
                    });
                } else {
                    log('❌ Navigation des onglets de performance NON trouvée', 'error');
                }
                
                // 6. Résumé du diagnostic
                resultDiv.innerHTML = `
                    <div class="info">🔍 Diagnostic Complet des Éléments FIFA</div>
                    <div style="margin-top: 15px;">
                        <div><strong>Onglet Performance:</strong> ${performanceTab ? '✅ Trouvé' : '❌ Non trouvé'}</div>
                        <div><strong>Dashboard Performance:</strong> ${performanceDashboard ? '✅ Trouvé' : '❌ Non trouvé'}</div>
                        <div><strong>Contenu Performance:</strong> ${performanceContent ? '✅ Trouvé' : '❌ Non trouvé'}</div>
                        <div><strong>Éléments FIFA trouvés:</strong> ${foundCount}/${fifaElements.length}</div>
                        <div><strong>Éléments FIFA manquants:</strong> ${notFoundCount}</div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Statut des éléments FIFA:</h4>
                        ${elementsStatus}
                    </div>
                `;
                
                if (foundCount === 0) {
                    log('⚠️ Aucun élément FIFA trouvé dans le portail !', 'warning');
                } else {
                    log(`🎉 ${foundCount} éléments FIFA trouvés dans le portail`);
                }
                
            } catch (error) {
                log(`❌ Erreur diagnostic: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Diagnostic: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de test direct du portail principal chargée !');
            log('🔧 Prêt pour le diagnostic FIFA...');
            
            // Attendre que l'iframe soit chargée
            const iframe = document.getElementById('portail-iframe');
            iframe.onload = function() {
                log('📄 Portail principal chargé dans l\'iframe');
                log('🔍 Prêt pour le diagnostic complet des éléments FIFA...');
                log('💡 Cliquez sur "Diagnostic Complet FIFA" pour commencer...');
            };
        });
    </script>
</body>
</html>
