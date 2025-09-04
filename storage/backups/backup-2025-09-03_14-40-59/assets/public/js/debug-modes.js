// �� SCRIPT DE DÉBOGAGE ET CORRECTION DES MODES - NE TOUCHE RIEN AU FICHIER PRINCIPAL
console.log('🔍 DÉBOGAGE ET CORRECTION DES MODES - DÉMARRAGE...');

// 🔧 FONCTION DE CORRECTION AUTOMATIQUE
function corrigerModesCollecte() {
    console.log('🔧 CORRECTION AUTOMATIQUE DES MODES...');
    
    const modeManuel = document.getElementById('mode-manuel');
    const modeVocal = document.getElementById('mode-vocal');
    const consoleVocale = document.getElementById('console-vocale');
    
    if (!modeManuel || !modeVocal || !consoleVocale) {
        console.error('❌ ÉLÉMENTS MANQUANTS POUR LA CORRECTION !');
        return;
    }
    
    // 🔧 ÉTAPE 1 : Supprimer tous les event listeners existants
    console.log('🔧 Suppression des event listeners existants...');
    
    // Cloner les éléments pour supprimer tous les event listeners
    const modeManuelClone = modeManuel.cloneNode(true);
    const modeVocalClone = modeVocal.cloneNode(true);
    
    // Remplacer les éléments originaux
    modeManuel.parentNode.replaceChild(modeManuelClone, modeManuel);
    modeVocal.parentNode.replaceChild(modeVocalClone, modeVocal);
    
    // Récupérer les nouveaux éléments
    const newModeManuel = document.getElementById('mode-manuel');
    const newModeVocal = document.getElementById('mode-vocal');
    
    // 🔧 ÉTAPE 2 : Réinitialiser l'état
    console.log('🔧 Réinitialisation de l\'état...');
    
    // Mode Manuel par défaut (actif)
    newModeManuel.classList.remove('bg-gray-100', 'text-gray-700');
    newModeManuel.classList.add('bg-blue-600', 'text-white');
    
    newModeVocal.classList.remove('bg-blue-600', 'text-white');
    newModeVocal.classList.add('bg-gray-100', 'text-gray-700');
    
    // Masquer la console vocale
    consoleVocale.classList.add('hidden');
    consoleVocale.style.setProperty('display', 'none', 'important');
    consoleVocale.style.setProperty('visibility', 'hidden', 'important');
    consoleVocale.style.setProperty('opacity', '0', 'important');
    
    // 🔧 ÉTAPE 3 : Ajouter les nouveaux event listeners propres
    console.log('🔧 Ajout des nouveaux event listeners...');
    
    newModeManuel.addEventListener('click', function() {
        console.log('🎯 Clic sur Mode Manuel (nouveau event listener)');
        
        // Activer le mode Manuel
        newModeManuel.classList.remove('bg-gray-100', 'text-gray-700');
        newModeManuel.classList.add('bg-blue-600', 'text-white');
        
        newModeVocal.classList.remove('bg-blue-600', 'text-white');
        newModeVocal.classList.add('bg-gray-100', 'text-gray-700');
        
        // Masquer la console vocale
        consoleVocale.classList.add('hidden');
        consoleVocale.style.setProperty('display', 'none', 'important');
        consoleVocale.style.setProperty('visibility', 'hidden', 'important');
        consoleVocale.style.setProperty('opacity', '0', 'important');
        
        console.log('✅ Mode Manuel activé - Console vocale masquée');
    });
    
    newModeVocal.addEventListener('click', function() {
        console.log('🎯 Clic sur Mode Vocal (nouveau event listener)');
        
        // Activer le mode Vocal
        newModeVocal.classList.remove('bg-gray-100', 'text-gray-700');
        newModeVocal.classList.add('bg-blue-600', 'text-white');
        
        newModeManuel.classList.remove('bg-blue-600', 'text-white');
        newModeManuel.classList.add('bg-gray-100', 'text-gray-700');
        
        // Afficher la console vocale
        consoleVocale.classList.remove('hidden');
        consoleVocale.style.setProperty('display', 'block', 'important');
        consoleVocale.style.setProperty('visibility', 'visible', 'important');
        consoleVocale.style.setProperty('opacity', '1', 'important');
        
        console.log('✅ Mode Vocal activé - Console vocale affichée');
    });
    
    console.log('✅ Correction automatique terminée !');
    
    // 🔧 ÉTAPE 4 : Test de vérification
    setTimeout(() => {
        console.log('🧪 TEST DE VÉRIFICATION APRÈS CORRECTION...');
        
        console.log('🔍 État après correction:');
        console.log('   - Mode Manuel actif:', newModeManuel.classList.contains('bg-blue-600'));
        console.log('   - Mode Vocal actif:', newModeVocal.classList.contains('bg-blue-600'));
        console.log('   - Console vocale masquée:', consoleVocale.classList.contains('hidden'));
        console.log('   - Console vocale style display:', consoleVocale.style.display);
        
        // Test du clic sur Mode Vocal
        console.log('🧪 Test du clic sur Mode Vocal après correction...');
        newModeVocal.click();
        
        setTimeout(() => {
            console.log('🔍 Vérification après clic (après correction):');
            console.log('   - Mode Manuel actif:', newModeManuel.classList.contains('bg-blue-600'));
            console.log('   - Mode Vocal actif:', newModeVocal.classList.contains('bg-blue-600'));
            console.log('   - Console vocale masquée:', consoleVocale.classList.contains('hidden'));
            console.log('   - Console vocale style display:', consoleVocale.style.display);
            
            if (newModeVocal.classList.contains('bg-blue-600') && !consoleVocale.classList.contains('hidden')) {
                console.log('✅ CORRECTION RÉUSSIE ! Le basculement fonctionne maintenant !');
            } else {
                console.error('❌ CORRECTION ÉCHOUÉE ! Le problème persiste !');
            }
        }, 100);
        
    }, 500);
}

// 🔧 FONCTION DE DÉBOGAGE DÉTAILLÉ
function debugModesCollecte() {
    console.log('🔍 DÉBOGAGE DÉTAILLÉ DES MODES...');
    
    const modeManuel = document.getElementById('mode-manuel');
    const modeVocal = document.getElementById('mode-vocal');
    const consoleVocale = document.getElementById('console-vocale');
    
    if (!modeManuel || !modeVocal || !consoleVocale) {
        console.error('❌ ÉLÉMENTS MANQUANTS !');
        return;
    }
    
    console.log('✅ Tous les éléments trouvés');
    console.log('🔍 État initial:');
    console.log('   - Mode Manuel actif:', modeManuel.classList.contains('bg-blue-600'));
    console.log('   - Mode Vocal actif:', modeVocal.classList.contains('bg-blue-600'));
    console.log('   - Console vocale masquée:', consoleVocale.classList.contains('hidden'));
    console.log('   - Console vocale style display:', consoleVocale.style.display);
    
    // 🔍 VÉRIFICATION DES EVENT LISTENERS
    console.log('🔍 Vérification des event listeners...');
    console.log('   - Mode Manuel onclick:', modeManuel.onclick);
    console.log('   - Mode Vocal onclick:', modeVocal.onclick);
    
    // 🔍 TEST SIMPLE DU CLIC
    console.log('🧪 Test simple du clic sur Mode Vocal...');
    console.log('   - AVANT le clic:');
    console.log('     * Mode Manuel actif:', modeManuel.classList.contains('bg-blue-600'));
    console.log('     * Mode Vocal actif:', modeVocal.classList.contains('bg-blue-600'));
    console.log('     * Console vocale masquée:', consoleVocale.classList.contains('hidden'));
    
    // Cliquer sur Mode Vocal
    modeVocal.click();
    
    // Vérifier après 100ms
    setTimeout(() => {
        console.log('   - APRÈS le clic (100ms):');
        console.log('     * Mode Manuel actif:', modeManuel.classList.contains('bg-blue-600'));
        console.log('     * Mode Vocal actif:', modeVocal.classList.contains('bg-blue-600'));
        console.log('     * Console vocale masquée:', consoleVocale.classList.contains('hidden'));
        console.log('     * Console vocale style display:', consoleVocale.style.display);
        
        // 🔍 VÉRIFICATION DES FONCTIONS
        console.log('🔍 Vérification des fonctions...');
        console.log('   - setModeVocal disponible:', typeof setModeVocal === 'function');
        console.log('   - setModeManuel disponible:', typeof setModeManuel === 'function');
        
        if (typeof setModeVocal === 'function') {
            console.log('✅ setModeVocal est disponible');
            console.log('🔍 Test manuel de setModeVocal...');
            
            // Sauvegarder l'état avant
            const avant = {
                modeManuel: modeManuel.classList.contains('bg-blue-600'),
                modeVocal: modeVocal.classList.contains('bg-blue-600'),
                consoleMasquee: consoleVocale.classList.contains('hidden')
            };
            
            console.log('   - État AVANT setModeVocal():', avant);
            
            // Appeler la fonction
            setModeVocal();
            
            // Vérifier après 100ms
            setTimeout(() => {
                const apres = {
                    modeManuel: modeManuel.classList.contains('bg-blue-600'),
                    modeVocal: modeVocal.classList.contains('bg-blue-600'),
                    consoleMasquee: consoleVocale.classList.contains('hidden')
                };
                
                console.log('   - État APRÈS setModeVocal():', apres);
                
                if (avant.modeManuel === apres.modeManuel && avant.modeVocal === apres.modeVocal) {
                    console.error('❌ setModeVocal() n\'a RIEN CHANGÉ !');
                    console.log('🔧 CORRECTION AUTOMATIQUE NÉCESSAIRE !');
                    corrigerModesCollecte();
                } else {
                    console.log('✅ setModeVocal() a fonctionné !');
                }
            }, 100);
        } else {
            console.error('❌ setModeVocal n\'est PAS disponible !');
            console.log('🔧 CORRECTION AUTOMATIQUE NÉCESSAIRE !');
            corrigerModesCollecte();
        }
        
    }, 100);
}

// 🚀 DÉMARRAGE AUTOMATIQUE
setTimeout(() => {
    console.log('🔍 DÉBOGAGE DES MODES APRÈS 2 SECONDES...');
    debugModesCollecte();
}, 2000);

console.log('🔍 SCRIPT DE DÉBOGAGE ET CORRECTION CHARGÉ - ATTENTE DE 2 SECONDES...');
