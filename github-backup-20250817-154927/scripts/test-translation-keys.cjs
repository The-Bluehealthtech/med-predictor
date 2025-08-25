const { exec } = require('child_process');

function testTranslationKeys() {
  console.log('🔍 Test des clés de traduction...\n');
  
  const keys = [
    'navigation.admin',
    'navigation.club_management',
    'navigation.association_management',
    'navigation.fifa',
    'navigation.device_connections',
    'navigation.healthcare',
    'navigation.referee_portal',
    'navigation.performance',
    'navigation.dtn_manager',
    'navigation.rpm'
  ];
  
  let allKeysFound = true;
  let completedTests = 0;
  
  keys.forEach((key, index) => {
    console.log(`${index + 1}️⃣ Test de la clé: ${key}`);
    
    exec(`php artisan tinker --execute="echo __('${key}');"`, (error, stdout, stderr) => {
      completedTests++;
      
      if (error) {
        console.log(`   ❌ Erreur: ${error.message}`);
        allKeysFound = false;
      } else {
        const result = stdout.trim();
        console.log(`   ✅ Résultat: "${result}"`);
        
        if (result === key || result.includes('auto.key')) {
          console.log(`   ❌ Clé non trouvée ou non traduite`);
          allKeysFound = false;
        } else {
          console.log(`   ✅ Traduction trouvée`);
        }
      }
      
      // Si c'est le dernier test, afficher le résumé
      if (completedTests === keys.length) {
        console.log('\n📊 RÉSUMÉ:');
        
        if (allKeysFound) {
          console.log('   ✅ Toutes les clés de traduction fonctionnent');
        } else {
          console.log('   ❌ Certaines clés de traduction ne fonctionnent pas');
          console.log('\n🔧 Solutions :');
          console.log('   1. Vérifiez que le fichier resources/lang/fr/navigation.php existe');
          console.log('   2. Videz les caches: php artisan config:clear');
          console.log('   3. Vérifiez que la locale est bien définie');
          console.log('   4. Redémarrez le serveur Laravel');
        }
        
        console.log('\n💡 Pour forcer le rechargement :');
        console.log('   - Allez sur http://localhost:8000/lang/fr');
        console.log('   - Puis http://localhost:8000/dashboard');
        console.log('   - Videz le cache du navigateur (Ctrl+Shift+R)');
      }
    });
  });
}

// Exécution
try {
  testTranslationKeys();
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 