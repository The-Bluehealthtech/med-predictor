const { exec } = require('child_process');

function testNavigationFrench() {
  console.log('🇫🇷 Test de la navigation française...\n');
  
  // Test 1: Vérifier que la locale est bien définie
  console.log('1️⃣ Test de la locale Laravel...');
  exec('php artisan tinker --execute="echo app()->getLocale();"', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur lors du test de locale:', error.message);
    } else {
      const locale = stdout.trim();
      console.log(`   ✅ Locale actuelle: ${locale}`);
      
      if (locale === 'fr') {
        console.log('   ✅ La locale est correctement définie en français');
      } else {
        console.log('   ❌ La locale n\'est pas en français');
      }
    }
    
    // Test 2: Vérifier les traductions
    console.log('\n2️⃣ Test des traductions de navigation...');
    exec('php artisan tinker --execute="echo __(\'navigation.admin\');"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur lors du test de traduction:', error.message);
      } else {
        const translation = stdout.trim();
        console.log(`   ✅ Traduction de 'navigation.admin': ${translation}`);
        
        if (translation === 'Admin') {
          console.log('   ✅ La traduction fonctionne correctement');
        } else {
          console.log('   ❌ La traduction ne fonctionne pas');
        }
      }
      
      // Test 3: Vérifier l'accès au dashboard
      console.log('\n3️⃣ Test d\'accès au dashboard...');
      exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/dashboard', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur lors du test d\'accès:', error.message);
        } else {
          const statusCode = parseInt(stdout);
          console.log(`   ✅ Code de statut du dashboard: ${statusCode}`);
          
          if (statusCode === 200) {
            console.log('   ✅ Le dashboard est accessible');
          } else {
            console.log('   ❌ Le dashboard n\'est pas accessible');
          }
        }
        
        // Résumé final
        console.log('\n📊 RÉSUMÉ:');
        console.log('   🌐 Pour tester la navigation française :');
        console.log('      1. Allez sur http://localhost:8000/lang/fr');
        console.log('      2. Puis allez sur http://localhost:8000/dashboard');
        console.log('      3. Vérifiez que la navigation s\'affiche en français');
        console.log('   🔧 Si ça ne marche pas :');
        console.log('      - Vérifiez que le serveur Laravel tourne');
        console.log('      - Vérifiez que les caches sont vidés');
        console.log('      - Vérifiez la console du navigateur pour les erreurs');
      });
    });
  });
}

// Exécution
try {
  testNavigationFrench();
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 