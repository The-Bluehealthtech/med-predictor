<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Portail Principal FIFA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1f2937; color: white; }
        .debug-section { background: #374151; padding: 20px; margin: 20px 0; border-radius: 8px; }
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
        .element-visible { background: #10b981; color: white; }
        .element-hidden { background: #ef4444; color: white; }
        .iframe-container { width: 100%; height: 600px; border: 2px solid #4b5563; border-radius: 8px; margin: 20px 0; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>
    <h1>🔍 Debug Portail Principal FIFA</h1>
    
    <div class="debug-section">
        <h2>1. Portail Principal FIFA (iframe)</h2>
        <p>Cette iframe charge le portail principal pour vérifier les éléments FIFA :</p>
        <div class="iframe-container">
            <iframe id="portail-iframe" src="/portail-joueur/7"></iframe>
        </div>
        <button onclick="checkPortailElements()">🔍 Vérifier Éléments FIFA dans le Portail</button>
        <div id="portail-check-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>2. Test Direct des Éléments FIFA</h2>
        <button onclick="testDirectElements()">🎯 Test Direct des Éléments</button>
        <div id="direct-test-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Vérification de la Structure HTML</h2>
        <button onclick="checkHTMLStructure()">🏗️ Vérifier Structure HTML</button>
        <div id="html-structure-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>4. Logs en Temps Réel</h2>
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

        // Vérifier les éléments FIFA dans le portail
        function checkPortailElements() {
            log('🔍 Vérification des éléments FIFA dans le portail principal...');
            const resultDiv = document.getElementById('portail-check-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                // Liste des éléments FIFA à vérifier
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
                        elementsStatus += `<span class="element-status element-found">${elementId} ✅ "${currentValue}"</span>`;
                        log(`✅ ${elementId} trouvé: "${currentValue}"`);
                    } else {
                        notFoundCount++;
                        elementsStatus += `<span class="element-status element-not-found">${elementId} ❌ Non trouvé</span>`;
                        log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="info">🔍 Vérification des Éléments FIFA dans le Portail Principal</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Éléments trouvés:</strong> ${foundCount}/${fifaElements.length}</div>
                        <div><strong>Éléments manquants:</strong> ${notFoundCount}</div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Statut des éléments:</h4>
                        ${elementsStatus}
                    </div>
                `;
                
                if (foundCount === 0) {
                    log('⚠️ Aucun élément FIFA trouvé dans le portail !', 'warning');
                } else {
                    log(`🎉 ${foundCount} éléments FIFA trouvés dans le portail`);
                }
                
            } catch (error) {
                log(`❌ Erreur vérification portail: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Test direct des éléments
        function testDirectElements() {
            log('🎯 Test direct des éléments FIFA...');
            const resultDiv = document.getElementById('direct-test-result');
            
            try {
                // Essayer d'accéder au portail principal
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                // Vérifier la structure de base
                const performanceTab = iframeDoc.getElementById('performance-tab');
                const performanceDashboard = iframeDoc.getElementById('performance-dashboard');
                const performanceContent = iframeDoc.getElementById('performance-content');
                
                let structureStatus = '';
                
                if (performanceTab) {
                    structureStatus += '<span class="element-status element-found">performance-tab ✅</span>';
                    log('✅ performance-tab trouvé');
                } else {
                    structureStatus += '<span class="element-status element-not-found">performance-tab ❌</span>';
                    log('❌ performance-tab NON trouvé', 'error');
                }
                
                if (performanceDashboard) {
                    structureStatus += '<span class="element-status element-found">performance-dashboard ✅</span>';
                    log('✅ performance-dashboard trouvé');
                } else {
                    structureStatus += '<span class="element-status element-not-found">performance-dashboard ❌</span>';
                    log('❌ performance-dashboard NON trouvé', 'error');
                }
                
                if (performanceContent) {
                    const isVisible = performanceContent.style.display !== 'none' && performanceContent.classList.contains('hidden') === false;
                    const visibilityClass = isVisible ? 'element-visible' : 'element-hidden';
                    const visibilityText = isVisible ? 'Visible' : 'Masqué';
                    structureStatus += `<span class="element-status ${visibilityClass}">performance-content ${visibilityClass === 'element-visible' ? '✅' : '❌'} ${visibilityText}</span>`;
                    log(`✅ performance-content trouvé: ${visibilityText}`);
                } else {
                    structureStatus += '<span class="element-status element-not-found">performance-content ❌</span>';
                    log('❌ performance-content NON trouvé', 'error');
                }
                
                resultDiv.innerHTML = `
                    <div class="info">🏗️ Vérification de la Structure HTML</div>
                    <div style="margin-top: 10px;">
                        <h4>Éléments de base:</h4>
                        ${structureStatus}
                    </div>
                `;
                
            } catch (error) {
                log(`❌ Erreur test direct: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Vérification de la structure HTML
        function checkHTMLStructure() {
            log('🏗️ Vérification de la structure HTML...');
            const resultDiv = document.getElementById('html-structure-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                // Vérifier les onglets de performance
                const performanceTabs = iframeDoc.querySelector('.performance-tabs');
                if (performanceTabs) {
                    const tabButtons = performanceTabs.querySelectorAll('button');
                    log(`📊 ${tabButtons.length} boutons d'onglets trouvés`);
                    
                    let tabsStatus = '';
                    tabButtons.forEach((button, index) => {
                        const isActive = button.classList.contains('bg-blue-600');
                        const buttonText = button.textContent.trim();
                        tabsStatus += `<div>Bouton ${index + 1}: ${buttonText} (actif: ${isActive ? '✅' : '❌'})</div>`;
                    });
                    
                    resultDiv.innerHTML = `
                        <div class="success">✅ Structure des onglets analysée</div>
                        <div style="margin-top: 10px;">
                            <div><strong>Boutons d'onglets:</strong> ${tabButtons.length}</div>
                            <div style="margin-top: 10px;">
                                ${tabsStatus}
                            </div>
                        </div>
                    `;
                } else {
                    log('❌ Navigation des onglets de performance NON trouvée', 'error');
                    resultDiv.innerHTML = '<div class="error">❌ Navigation des onglets de performance NON trouvée</div>';
                }
                
            } catch (error) {
                log(`❌ Erreur vérification structure: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug portail principal chargée avec succès !');
            log('🔧 Prêt pour le débogage du portail principal FIFA...');
            
            // Attendre que l'iframe soit chargée
            const iframe = document.getElementById('portail-iframe');
            iframe.onload = function() {
                log('📄 Portail principal chargé dans l\'iframe');
                log('🔍 Prêt pour la vérification des éléments FIFA...');
            };
        });
    </script>
</body>
</html>
