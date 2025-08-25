const { exec } = require('child_process');

function forceFrenchLocale() {
  console.log('🇫🇷 Forçage de la locale française...\n');
  
  // Étape 1: Vider les caches
  console.log('1️⃣ Vidage des caches...');
  exec('php artisan config:clear && php artisan cache:clear && php artisan route:clear', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur lors du vidage des caches:', error.message);
    } else {
      console.log('   ✅ Caches vidés avec succès');
    }
    
    // Étape 2: Forcer la locale française
    console.log('\n2️⃣ Forçage de la locale française...');
    exec('php artisan tinker --execute="session([\'locale\' => \'fr\']); echo \'Locale forcée en français\';"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur lors du forçage de locale:', error.message);
      } else {
        console.log('   ✅ Locale forcée en français');
      }
      
      // Étape 3: Vérifier la locale
      console.log('\n3️⃣ Vérification de la locale...');
      exec('php artisan tinker --execute="echo \'Locale actuelle: \' . app()->getLocale();"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur lors de la vérification:', error.message);
        } else {
          const locale = stdout.trim();
          console.log(`   ✅ ${locale}`);
          
          if (locale.includes('fr')) {
            console.log('   ✅ La locale est bien en français');
          } else {
            console.log('   ❌ La locale n\'est pas en français');
          }
        }
        
        // Étape 4: Test des traductions
        console.log('\n4️⃣ Test des traductions...');
        exec('php artisan tinker --execute="echo \'Admin: \' . __(\'navigation.admin\'); echo PHP_EOL; echo \'Dashboard: \' . __(\'navigation.dashboard\');"', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur lors du test des traductions:', error.message);
          } else {
            console.log('   ✅ Traductions:');
            console.log(`      ${stdout.trim()}`);
          }
          
          // Instructions finales
          console.log('\n🎯 INSTRUCTIONS FINALES:');
          console.log('   1. Ouvrez votre navigateur');
          console.log('   2. Allez sur http://localhost:8000/lang/fr');
          console.log('   3. Puis allez sur http://localhost:8000/dashboard');
          console.log('   4. Vérifiez que la navigation s\'affiche en français');
          console.log('\n🔧 Si ça ne marche toujours pas :');
          console.log('   - Vérifiez que vous êtes connecté');
          console.log('   - Videz le cache du navigateur (Ctrl+F5)');
          console.log('   - Vérifiez la console du navigateur (F12)');
          console.log('   - Redémarrez le serveur Laravel si nécessaire');
        });
      });
    });
  });
}

// Exécution
try {
  forceFrenchLocale();
} catch (error) {
  console.error('❌ Erreur lors du forçage de locale :', error.message);
  process.exit(1);
} 