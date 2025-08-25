const { exec } = require('child_process');

function diagnosticFinal() {
  console.log('🔍 DIAGNOSTIC FINAL - Navigation Française\n');
  
  const tests = [
    {
      name: 'Serveur Laravel',
      command: 'curl -s -o /dev/null -w "%{http_code}" http://localhost:8000',
      expected: '200'
    },
    {
      name: 'Locale Laravel',
      command: 'php artisan tinker --execute="echo app()->getLocale();"',
      expected: 'fr'
    },
    {
      name: 'Traduction Admin',
      command: 'php artisan tinker --execute="echo __(\'navigation.admin\');"',
      expected: 'Admin'
    },
    {
      name: 'Traduction Dashboard',
      command: 'php artisan tinker --execute="echo __(\'navigation.dashboard\');"',
      expected: 'Dashboard'
    },
    {
      name: 'Route /lang/fr',
      command: 'curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr',
      expected: '302'
    }
  ];
  
  let allTestsPassed = true;
  let completedTests = 0;
  
  tests.forEach((test, index) => {
    console.log(`${index + 1}️⃣ Test: ${test.name}...`);
    
    exec(test.command, (error, stdout, stderr) => {
      completedTests++;
      
      if (error) {
        console.log(`   ❌ Erreur: ${error.message}`);
        allTestsPassed = false;
      } else {
        const result = stdout.trim();
        console.log(`   ✅ Résultat: ${result}`);
        
        if (result === test.expected) {
          console.log(`   ✅ Test réussi`);
        } else {
          console.log(`   ❌ Test échoué (attendu: ${test.expected})`);
          allTestsPassed = false;
        }
      }
      
      // Si c'est le dernier test, afficher le résumé
      if (completedTests === tests.length) {
        console.log('\n📊 RÉSUMÉ DU DIAGNOSTIC:');
        
        if (allTestsPassed) {
          console.log('   🎉 TOUS LES TESTS SONT RÉUSSIS !');
          console.log('\n🌍 La navigation française devrait fonctionner :');
          console.log('   1. Ouvrez http://localhost:8000/lang/fr');
          console.log('   2. Puis http://localhost:8000/dashboard');
          console.log('   3. La navigation devrait s\'afficher en français');
        } else {
          console.log('   ❌ CERTAINS TESTS ONT ÉCHOUÉ');
          console.log('\n🔧 Solutions possibles :');
          console.log('   - Redémarrez le serveur Laravel');
          console.log('   - Videz les caches: php artisan config:clear');
          console.log('   - Vérifiez les logs d\'erreur');
        }
        
        console.log('\n💡 CONSEILS POUR LE TEST :');
        console.log('   - Utilisez un navigateur en mode incognito');
        console.log('   - Videz le cache du navigateur (Ctrl+Shift+R)');
        console.log('   - Vérifiez la console du navigateur (F12)');
        console.log('   - Assurez-vous d\'être connecté à l\'application');
      }
    });
  });
}

// Exécution
try {
  diagnosticFinal();
} catch (error) {
  console.error('❌ Erreur lors du diagnostic :', error.message);
  process.exit(1);
} 