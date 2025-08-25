const { exec } = require('child_process');

function testFinalNavigation() {
  console.log('🎯 TEST FINAL - Navigation Française\n');
  
  // Test 1: Forcer la locale française
  console.log('1️⃣ Forçage de la locale française...');
  exec('php artisan tinker --execute="session([\'locale\' => \'fr\']); echo \'Locale forcée en français\';"', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log('   ✅ Locale forcée en français');
    }
    
    // Test 2: Vérifier que la locale est bien appliquée
    console.log('\n2️⃣ Vérification de la locale...');
    exec('php artisan tinker --execute="echo \'Locale actuelle: \' . app()->getLocale();"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        console.log(`   ✅ ${stdout.trim()}`);
      }
      
      // Test 3: Test des traductions clés
      console.log('\n3️⃣ Test des traductions principales...');
      const keyTests = [
        { key: 'navigation.admin', expected: 'Admin' },
        { key: 'navigation.club_management', expected: 'Gestion Club' },
        { key: 'navigation.association_management', expected: 'Gestion Association' },
        { key: 'navigation.fifa', expected: 'FIFA' },
        { key: 'navigation.device_connections', expected: 'Connexions Appareils' }
      ];
      
      let completedTests = 0;
      let allTestsPassed = true;
      
      keyTests.forEach((test, index) => {
        exec(`php artisan tinker --execute="echo __('${test.key}');"`, (error, stdout, stderr) => {
          completedTests++;
          
          if (error) {
            console.log(`   ❌ ${test.key}: Erreur - ${error.message}`);
            allTestsPassed = false;
          } else {
            const result = stdout.trim();
            if (result === test.expected) {
              console.log(`   ✅ ${test.key}: "${result}"`);
            } else {
              console.log(`   ❌ ${test.key}: "${result}" (attendu: "${test.expected}")`);
              allTestsPassed = false;
            }
          }
          
          if (completedTests === keyTests.length) {
            console.log('\n🎉 RÉSULTAT FINAL:');
            if (allTestsPassed) {
              console.log('✅ TOUTES LES TRADUCTIONS FONCTIONNENT CORRECTEMENT');
              console.log('\n📋 INSTRUCTIONS POUR L\'UTILISATEUR:');
              console.log('1. Videz le cache du navigateur (Ctrl+Shift+R)');
              console.log('2. Allez sur http://localhost:8000/lang/fr');
              console.log('3. Puis sur http://localhost:8000/dashboard');
              console.log('4. La navigation devrait s\'afficher en français');
              console.log('\n💡 Si vous voyez encore "auto.key", utilisez le mode incognito');
            } else {
              console.log('❌ CERTAINES TRADUCTIONS NE FONCTIONNENT PAS');
              console.log('\n🔧 Actions à effectuer:');
              console.log('1. Videz tous les caches: php artisan config:clear && php artisan cache:clear');
              console.log('2. Redémarrez le serveur Laravel');
              console.log('3. Testez à nouveau');
            }
          }
        });
      });
    });
  });
}

testFinalNavigation(); 