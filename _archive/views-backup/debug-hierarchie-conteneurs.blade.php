<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Hiérarchie Conteneurs FIFA</title>
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
        .hierarchy-tree { background: #1f2937; padding: 15px; margin: 15px 0; border-radius: 5px; border: 1px solid #4b5563; font-family: monospace; }
        .container-info { background: #374151; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #4b5563; }
    </style>
</head>
<body>
    <h1>🏗️ Debug Hiérarchie Conteneurs FIFA</h1>
    
    <div class="debug-section">
        <h2>1. Portail Principal FIFA (iframe)</h2>
        <div class="iframe-container">
            <iframe id="portail-iframe" src="/portail-joueur/7"></iframe>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>2. Diagnostic de la Hiérarchie des Conteneurs</h2>
        <button onclick="diagnosticHierarchie()">🏗️ Diagnostiquer Hiérarchie</button>
        <div id="hierarchie-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Test de Correction des Dimensions</h2>
        <button onclick="corrigerDimensions()">🔧 Corriger Dimensions</button>
        <div id="correction-result"></div>
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

        // Diagnostic de la hiérarchie des conteneurs
        function diagnosticHierarchie() {
            log('🏗️ Diagnostic de la hiérarchie des conteneurs FIFA...');
            const resultDiv = document.getElementById('hierarchie-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                log('✅ Accès au DOM de l\'iframe réussi');
                
                // Hiérarchie des conteneurs à vérifier
                const containerHierarchy = [
                    'performance-tab',
                    'performance-dashboard',
                    'overview-tab',
                    'performance-content'
                ];
                
                let hierarchyTree = '';
                let containerDetails = '';
                
                containerHierarchy.forEach((containerId, index) => {
                    const container = iframeDoc.getElementById(containerId);
                    if (container) {
                        // Obtenir les styles calculés
                        const computedStyle = iframeDoc.defaultView.getComputedStyle(container);
                        const display = computedStyle.display;
                        const visibility = computedStyle.visibility;
                        const opacity = computedStyle.opacity;
                        const position = computedStyle.position;
                        const width = computedStyle.width;
                        const height = computedStyle.height;
                        const overflow = computedStyle.overflow;
                        
                        // Obtenir les dimensions réelles
                        const rect = container.getBoundingClientRect();
                        const isVisible = rect.width > 0 && rect.height > 0;
                        
                        // Obtenir les classes CSS
                        const classes = container.className;
                        const hasHiddenClass = classes.includes('hidden');
                        
                        // Indentation pour l'arbre hiérarchique
                        const indent = '  '.repeat(index);
                        const statusIcon = isVisible ? '✅' : '❌';
                        const statusText = isVisible ? 'Visible' : 'Masqué';
                        
                        hierarchyTree += `${indent}${statusIcon} ${containerId} (${statusText})\n`;
                        
                        // Détails du conteneur
                        containerDetails += `
                            <div class="container-info">
                                <h4>${containerId}</h4>
                                <div><strong>Classes CSS:</strong> ${classes}</div>
                                <div><strong>Classe 'hidden':</strong> ${hasHiddenClass ? '❌ Oui' : '✅ Non'}</div>
                                <div><strong>Display:</strong> ${display}</div>
                                <div><strong>Visibility:</strong> ${visibility}</div>
                                <div><strong>Opacity:</strong> ${opacity}</div>
                                <div><strong>Position:</strong> ${position}</div>
                                <div><strong>Width:</strong> ${width}</div>
                                <div><strong>Height:</strong> ${height}</div>
                                <div><strong>Overflow:</strong> ${overflow}</div>
                                <div><strong>Dimensions réelles:</strong> ${rect.width}px × ${rect.height}px</div>
                                <div><strong>Visible:</strong> ${isVisible ? '✅' : '❌'}</div>
                            </div>
                        `;
                        
                        log(`🔍 ${containerId}:`);
                        log(`   Classes: ${classes}`);
                        log(`   Classe 'hidden': ${hasHiddenClass ? 'Oui' : 'Non'}`);
                        log(`   Display: ${display}, Visibility: ${visibility}, Opacity: ${opacity}`);
                        log(`   Dimensions CSS: ${width} × ${height}`);
                        log(`   Dimensions réelles: ${rect.width}px × ${rect.height}px`);
                        log(`   Visible: ${isVisible ? '✅' : '❌'}`);
                        
                    } else {
                        hierarchyTree += `  ❌ ${containerId} (Non trouvé)\n`;
                        log(`❌ Conteneur NON trouvé: ${containerId}`, 'error');
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="info">🏗️ Diagnostic de la Hiérarchie des Conteneurs FIFA</div>
                    <div style="margin-top: 15px;">
                        <h4>Arbre hiérarchique:</h4>
                        <div class="hierarchy-tree">${hierarchyTree}</div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Détails des conteneurs:</h4>
                        ${containerDetails}
                    </div>
                `);
                
            } catch (error) {
                log(`❌ Erreur diagnostic hiérarchie: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Test de correction des dimensions
        function corrigerDimensions() {
            log('🔧 Test de correction des dimensions des conteneurs...');
            const resultDiv = document.getElementById('correction-result');
            
            try {
                const iframe = document.getElementById('portail-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                if (!iframeDoc) {
                    log('⚠️ Impossible d\'accéder au contenu de l\'iframe', 'warning');
                    resultDiv.innerHTML = '<div class="warning">⚠️ Impossible d\'accéder au contenu de l\'iframe</div>';
                    return;
                }
                
                // Conteneurs à corriger
                const containersToFix = [
                    'performance-tab',
                    'performance-dashboard',
                    'overview-tab',
                    'performance-content'
                ];
                
                let fixedCount = 0;
                let notFoundCount = 0;
                let correctionDetails = '';
                
                containersToFix.forEach(containerId => {
                    const container = iframeDoc.getElementById(containerId);
                    if (container) {
                        // Forcer l'affichage avec styles inline
                        container.style.display = 'block';
                        container.style.visibility = 'visible';
                        container.style.opacity = '1';
                        container.style.width = 'auto';
                        container.style.height = 'auto';
                        container.style.overflow = 'visible';
                        
                        // Supprimer la classe 'hidden' si présente
                        if (container.classList.contains('hidden')) {
                            container.classList.remove('hidden');
                        }
                        
                        // Vérifier les dimensions après correction
                        const rect = container.getBoundingClientRect();
                        const isVisible = rect.width > 0 && rect.height > 0;
                        
                        correctionDetails += `
                            <div class="container-info">
                                <h4>${containerId} - Après correction</h4>
                                <div><strong>Dimensions:</strong> ${rect.width}px × ${rect.height}px</div>
                                <div><strong>Visible:</strong> ${isVisible ? '✅' : '❌'}</div>
                                <div><strong>Styles appliqués:</strong> display: block, visibility: visible, opacity: 1</div>
                            </div>
                        `;
                        
                        if (isVisible) {
                            fixedCount++;
                            log(`✅ ${containerId}: Dimensions corrigées - ${rect.width}px × ${rect.height}px`);
                        } else {
                            log(`⚠️ ${containerId}: Dimensions toujours nulles après correction`, 'warning');
                        }
                        
                    } else {
                        notFoundCount++;
                        log(`❌ Conteneur NON trouvé: ${containerId}`, 'error');
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="success">🔧 Correction des Dimensions des Conteneurs</div>
                    <div style="margin-top: 15px;">
                        <div><strong>Conteneurs corrigés:</strong> ${fixedCount}</div>
                        <div><strong>Conteneurs non trouvés:</strong> ${notFoundCount}</div>
                        <div><strong>Total traités:</strong> ${containersToFix.length}</div>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Détails des corrections:</h4>
                        ${correctionDetails}
                    </div>
                `);
                
                log(`🎉 Correction terminée: ${fixedCount} conteneurs corrigés`);
                
            } catch (error) {
                log(`❌ Erreur correction dimensions: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug hiérarchie conteneurs chargée !');
            log('🔧 Prêt pour le diagnostic de la hiérarchie...');
            
            // Attendre que l'iframe soit chargée
            const iframe = document.getElementById('portail-iframe');
            iframe.onload = function() {
                log('📄 Portail principal chargé dans l\'iframe');
                log('🔍 Prêt pour le diagnostic de la hiérarchie des conteneurs...');
                log('💡 Cliquez sur "Diagnostiquer Hiérarchie" pour commencer...');
            };
        });
    </script>
</body>
</html>
