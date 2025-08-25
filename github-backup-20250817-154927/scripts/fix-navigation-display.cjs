const { exec } = require('child_process');

function fixNavigationDisplay() {
  console.log('🔧 Résolution du problème d\'affichage de la navigation...\n');
  
  // Étape 1: Vider tous les caches Laravel
  console.log('1️⃣ Vidage complet des caches Laravel...');
  exec('php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur lors du vidage des caches:', error.message);
    } else {
      console.log('   ✅ Tous les caches Laravel vidés');
    }
    
    // Étape 2: Vérifier que le serveur fonctionne
    console.log('\n2️⃣ Vérification du serveur...');
    exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Serveur non accessible:', error.message);
      } else {
        const statusCode = parseInt(stdout);
        console.log(`   ✅ Serveur accessible (HTTP ${statusCode})`);
      }
      
      // Étape 3: Forcer la locale française
      console.log('\n3️⃣ Forçage de la locale française...');
      exec('php artisan tinker --execute="session([\'locale\' => \'fr\']); echo \'Locale forcée en français\';"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur lors du forçage de locale:', error.message);
        } else {
          console.log('   ✅ Locale forcée en français');
        }
        
        // Instructions finales
        console.log('\n🎯 INSTRUCTIONS POUR RÉSOUDRE LE PROBLÈME :');
        console.log('\n📱 ÉTAPE 1 - Videz le cache du navigateur :');
        console.log('   • Chrome/Edge : Ctrl+Shift+R (ou Cmd+Shift+R sur Mac)');
        console.log('   • Firefox : Ctrl+F5 (ou Cmd+F5 sur Mac)');
        console.log('   • Ou utilisez le mode incognito/navigation privée');
        
        console.log('\n🌐 ÉTAPE 2 - Testez la navigation :');
        console.log('   1. Ouvrez http://localhost:8000/lang/fr');
        console.log('   2. Puis http://localhost:8000/dashboard');
        console.log('   3. Vérifiez que la navigation s\'affiche en français');
        
        console.log('\n🔍 ÉTAPE 3 - Si le problème persiste :');
        console.log('   • Ouvrez la console du navigateur (F12)');
        console.log('   • Regardez s\'il y a des erreurs JavaScript');
        console.log('   • Vérifiez les requêtes réseau');
        console.log('   • Testez avec un autre navigateur');
        
        console.log('\n⚡ ÉTAPE 4 - Solutions avancées :');
        console.log('   • Redémarrez le serveur Laravel');
        console.log('   • Vérifiez que vous êtes connecté');
        console.log('   • Testez en mode développement');
        
        console.log('\n💡 CONSEILS :');
        console.log('   • Le problème "auto.key" indique un cache JavaScript');
        console.log('   • Les traductions Laravel fonctionnent côté serveur');
        console.log('   • Le problème est probablement côté client (navigateur)');
        console.log('   • Utilisez le mode incognito pour éviter les problèmes de cache');
      });
    });
  });
}

// Exécution
try {
  fixNavigationDisplay();
} catch (error) {
  console.error('❌ Erreur lors de la résolution :', error.message);
  process.exit(1);
} 