const { exec } = require('child_process');

function diagnosticAutoKey() {
  console.log('🔍 DIAGNOSTIC - Problème auto.key...\\n');
  
  // Test 1: Vérifier le serveur Laravel
  console.log('1️⃣ Test du serveur Laravel...');
  exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Serveur Laravel non accessible:', error.message);
    } else {
      const statusCode = parseInt(stdout);
      console.log(`   ✅ Serveur Laravel accessible (HTTP ${statusCode})`);
    }
    
    // Test 2: Vérifier la locale
    console.log('\\n2️⃣ Test de la locale...');
    exec('php artisan tinker --execute="echo app()->getLocale();"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur lors du test de locale:', error.message);
      } else {
        const locale = stdout.trim();
        console.log(`   ✅ Locale actuelle: ${locale}`);
      }
      
      // Test 3: Vérifier les traductions
      console.log('\\n3️⃣ Test des traductions...');
      const testKeys = ['navigation.admin', 'navigation.club_management', 'navigation.association_management'];
      let completedTests = 0;
      
      testKeys.forEach((key, index) => {
        exec(`php artisan tinker --execute="echo __('${key}');"`, (error, stdout, stderr) => {
          completedTests++;
          
          if (error) {
            console.log(`   ❌ Erreur pour ${key}:`, error.message);
          } else {
            const translation = stdout.trim();
            console.log(`   ✅ ${key}: "${translation}"`);
          }
          
          if (completedTests === testKeys.length) {
            // Test 4: Vérifier le contenu de la page
            console.log('\\n4️⃣ Test du contenu de la page...');
            exec('curl -s http://localhost:8000/lang/fr | grep -o "auto\\.key[0-9]*" | head -3', (error, stdout, stderr) => {
              if (error) {
                console.log('   ❌ Erreur lors du test de la page:', error.message);
              } else {
                const autoKeys = stdout.trim();
                if (autoKeys) {
                  console.log('   ❌ Clés auto.key trouvées:', autoKeys);
                  console.log('   💡 SOLUTION: Videz le cache du navigateur (Ctrl+Shift+R)');
                } else {
                  console.log('   ✅ Aucune clé auto.key trouvée');
                }
              }
              
              // Test 5: Vérifier les fichiers de traduction
              console.log('\\n5️⃣ Vérification des fichiers de traduction...');
              exec('ls -la resources/lang/fr/', (error, stdout, stderr) => {
                if (error) {
                  console.log('   ❌ Erreur lors de la vérification des fichiers:', error.message);
                } else {
                  console.log('   ✅ Fichiers de traduction:');
                  console.log(stdout);
                }
                
                console.log('\\n🎯 RECOMMANDATIONS:');
                console.log('1. Videz le cache du navigateur (Ctrl+Shift+R ou Cmd+Shift+R)');
                console.log('2. Testez en mode incognito/navigation privée');
                console.log('3. Vérifiez que vous êtes sur http://localhost:8000/lang/fr');
                console.log('4. Puis allez sur http://localhost:8000/dashboard');
              });
            });
          }
        });
      });
    });
  });
}

diagnosticAutoKey(); 