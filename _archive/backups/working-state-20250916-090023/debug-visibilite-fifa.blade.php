<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Visibilité Éléments FIFA</title>
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
        .iframe-container { width: 100%; height: 800px; border: 2px solid #4b5563; border-radius: 8px; margin: 20px 0; }
        iframe { width: 100%; height: 100%; border: none; }
        .css-debug { background: #1f2937; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #4b5563; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔍 Debug Visibilité Éléments FIFA</h1>
    
    <div class="debug-section">
        <h2>1. Portail Principal FIFA (iframe)</h2>
        <div class="iframe-container">
            <iframe id="portail-iframe" src="/portail-joueur/7"></iframe>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>2. Diagnostic de Visibilité FIFA</h2>
        <button onclick="diagnosticVisibilite()">🔍 Diagnostiquer Visibilité FIFA</button>
        <div id="visibilite-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Test de Rendu des Éléments</h2>
        <button onclick="testRenduElements()">🎨 Tester Rendu des Éléments</button>
        <div id="rendu-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>4. Logs de Diagnostic</h2>
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

        // Diagnostic de visibilité des éléments FIFA
        function diagnosticVisibilite() {
            log('🔍 Diagnostic de visibilité des éléments FIFA...');
            const resultDiv = document.getElementById('visibilite-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                log('✅ Accès au DOM de l\'iframe réussi');
                
                // Éléments FIFA à vérifier
                const fifaElements = [
                    'dynamic-overall-rating',
                    'dynamic-goals',
                    'dynamic-assists',
                    'quick-goals',
                    'quick-assists',
                    'quick-form'
                ];
                
                let elementsStatus = '';
                let visibleCount = 0;
                let hiddenCount = 0;
                
                fifaElements.forEach(elementId => {
                    const element = iframeDoc.getElementById(elementId);
                    if (element) {
                        // Vérifications de visibilité détaillées
                        const computedStyle = iframeDoc.defaultView.getComputedStyle(element);
                        const display = computedStyle.display;
                        const visibility = computedStyle.visibility;
                        const opacity = computedStyle.opacity;
                        const position = computedStyle.position;
                        const zIndex = computedStyle.zIndex;
                        
                        // Vérifier si l'élément est dans le flux du document
                        const rect = element.getBoundingClientRect();
                        const isInViewport = rect.width > 0 && rect.height > 0;
                        
                        // Vérifier si l'élément est masqué par CSS
                        const isHiddenByCSS = display === 'none' || visibility === 'hidden' || opacity === '0';
                        
                        // Vérifier si l'élément est masqué par position
                        const isHiddenByPosition = position === 'absolute' && (rect.left < 0 || rect.top < 0);
                        
                        // Vérifier si l'élément est masqué par overflow
                        const parent = element.parentElement;
                        const parentRect = parent ? parent.getBoundingClientRect() : null;
                        const isHiddenByOverflow = parentRect && (rect.left >= parentRect.right || rect.top >= parentRect.bottom);
                        
                        const isVisible = !isHiddenByCSS && !isHiddenByPosition && !isHiddenByOverflow && isInViewport;
                        
                        if (isVisible) {
                            visibleCount++;
                            elementsStatus += `<span class="element-status element-visible">${elementId} ✅ Visible</span>`;
                            log(`✅ ${elementId}: Visible (display: ${display}, visibility: ${visibility}, opacity: ${opacity})`);
                        } else {
                            hiddenCount++;
                            elementsStatus += `<span class="element-status element-hidden">${elementId} ❌ Masqué</span>`;
                            
                            let reason = '';
                            if (isHiddenByCSS) reason = 'CSS (display/visibility/opacity)';
                            else if (isHiddenByPosition) reason = 'Position hors écran';
                            else if (isHiddenByOverflow) reason = 'Overflow parent';
                            else if (!isInViewport) reason = 'Dimensions nulles';
                            else reason = 'Autre';
                            
                            log(`❌ ${elementId}: Masqué - Raison: ${reason}`, 'warning');
                            log(`   Détails: display=${display}, visibility=${visibility}, opacity=${opacity}, position=${position}, z-index=${zIndex}`);
                            log(`   Dimensions: width=${rect.width}, height=${rect.height}, left=${rect.left}, top=${rect.top}`);
                        }
                        
                        // Afficher les styles CSS de l'élément
                        log(`   Styles CSS de ${elementId}:`);
                        log(`     display: ${display}`);
                        log(`     visibility: ${visibility}`);
                        log(`     opacity: ${opacity}`);
                        log(`     position: ${position}`);
                        log(`     z-index: ${zIndex}`);
                        log(`     width: ${rect.width}px, height: ${rect.height}px`);
                        
                    } else {
                        elementsStatus += `<span class="element-status element-not-found">${elementId} ❌ Non trouvé</span>`;
                        log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="info">🔍 Diagnostic de Visibilité des Éléments FIFA</div>
                    <div style="margin-top: 15px;">
                        <div><strong>Éléments visibles:</strong> ${visibleCount}</div>
                        <div><strong>Éléments masqués:</strong> ${hiddenCount}</div>
                        <div><strong>Total analysés:</strong> ${fifaElements.length}</div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Statut des éléments:</h4>
                        ${elementsStatus}
                    </div>
                `;
                
                if (visibleCount === 0) {
                    log('⚠️ Aucun élément FIFA n\'est visible !', 'warning');
                } else {
                    log(`🎉 ${visibleCount} éléments FIFA sont visibles`);
                }
                
            } catch (error) {
                log(`❌ Erreur diagnostic visibilité: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Test de rendu des éléments
        function testRenduElements() {
            log('🎨 Test de rendu des éléments FIFA...');
            const resultDiv = document.getElementById('rendu-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                // Tester la visibilité du conteneur parent
                const performanceContent = iframeDoc.getElementById('performance-content');
                if (performanceContent) {
                    const computedStyle = iframeDoc.defaultView.getComputedStyle(performanceContent);
                    const display = computedStyle.display;
                    const visibility = computedStyle.visibility;
                    const opacity = computedStyle.opacity;
                    const height = computedStyle.height;
                    const overflow = computedStyle.overflow;
                    
                    log('🔍 Analyse du conteneur performance-content:');
                    log(`   display: ${display}`);
                    log(`   visibility: ${visibility}`);
                    log(`   opacity: ${opacity}`);
                    log(`   height: ${height}`);
                    log(`   overflow: ${overflow}`);
                    
                    // Vérifier si le conteneur est visible
                    const rect = performanceContent.getBoundingClientRect();
                    const isContainerVisible = rect.width > 0 && rect.height > 0;
                    
                    log(`   Dimensions du conteneur: width=${rect.width}px, height=${rect.height}px`);
                    log(`   Conteneur visible: ${isContainerVisible ? '✅' : '❌'}`);
                    
                    resultDiv.innerHTML = `
                        <div class="info">🎨 Test de Rendu des Éléments FIFA</div>
                        <div style="margin-top: 15px;">
                            <div><strong>Conteneur performance-content:</strong></div>
                            <div class="css-debug">
                                display: ${display}<br>
                                visibility: ${visibility}<br>
                                opacity: ${opacity}<br>
                                height: ${height}<br>
                                overflow: ${overflow}<br>
                                dimensions: ${rect.width}px × ${rect.height}px<br>
                                visible: ${isContainerVisible ? '✅' : '❌'}
                            </div>
                        </div>
                    `;
                    
                    if (!isContainerVisible) {
                        log('⚠️ Le conteneur performance-content est masqué !', 'warning');
                    }
                } else {
                    log('❌ Conteneur performance-content non trouvé', 'error');
                }
                
            } catch (error) {
                log(`❌ Erreur test rendu: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug visibilité FIFA chargée !');
            log('🔧 Prêt pour le diagnostic de visibilité...');
            
            // Attendre que l'iframe soit chargée
            const iframe = document.getElementById('portail-iframe');
            iframe.onload = function() {
                log('📄 Portail principal chargé dans l\'iframe');
                log('🔍 Prêt pour le diagnostic de visibilité des éléments FIFA...');
                log('💡 Cliquez sur "Diagnostiquer Visibilité FIFA" pour commencer...');
            };
        });
    </script>
</body>
</html>
