const { exec } = require('child_process');

function diagnosticSession() {
  console.log('🔍 DIAGNOSTIC - Problème de session et locale...\\n');
  
  // Test 1: Vérifier la configuration de session
  console.log('1️⃣ Configuration de session...');
  exec('php artisan tinker --execute="echo \\'Session driver: \\' . config(\\'session.driver\\');"', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log(`   ✅ ${stdout.trim()}`);
    }
    
    // Test 2: Vérifier la locale actuelle
    console.log('\\n2️⃣ Locale actuelle...');
    exec('php artisan tinker --execute="echo \\'Locale: \\' . app()->getLocale();"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        console.log(`   ✅ ${stdout.trim()}`);
      }
      
      // Test 3: Vérifier la session
      console.log('\\n3️⃣ Contenu de la session...');
      exec('php artisan tinker --execute="echo \\'Session locale: \\' . session(\\'locale\\', \\'non définie\\');"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          console.log(`   ✅ ${stdout.trim()}`);
        }
        
        // Test 4: Forcer le changement de locale
        console.log('\\n4️⃣ Test de changement de locale...');
        exec('php artisan tinker --execute="session([\\'locale\\' => \\'en\\']); echo \\'Locale forcée en anglais\\';"', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur:', error.message);
          } else {
            console.log(`   ✅ ${stdout.trim()}`);
          }
          
          // Test 5: Vérifier après changement
          console.log('\\n5️⃣ Vérification après changement...');
          exec('php artisan tinker --execute="echo \\'Nouvelle locale: \\' . app()->getLocale(); echo \\' - Session: \\' . session(\\'locale\\');"', (error, stdout, stderr) => {
            if (error) {
              console.log('   ❌ Erreur:', error.message);
            } else {
              console.log(`   ✅ ${stdout.trim()}`);
            }
            
            // Test 6: Vérifier les traductions
            console.log('\\n6️⃣ Test des traductions...');
            exec('php artisan tinker --execute="echo \\'Admin (FR): \\' . __(\\'navigation.admin\\');"', (error, stdout, stderr) => {
              if (error) {
                console.log('   ❌ Erreur:', error.message);
              } else {
                console.log(`   ✅ ${stdout.trim()}`);
              }
              
              console.log('\\n🎯 DIAGNOSTIC COMPLET:');
              console.log('Le problème semble être un décalage entre:');
              console.log('1. La session (qui change)');
              console.log('2. L\'affichage de la navigation (qui reste en cache)');
              console.log('\\n💡 SOLUTIONS:');
              console.log('1. Videz le cache du navigateur (Ctrl+Shift+R)');
              console.log('2. Videz le cache Laravel: php artisan cache:clear');
              console.log('3. Redémarrez le serveur Laravel');
              console.log('4. Testez en mode incognito');
            });
          });
        });
      });
    });
  });
}

diagnosticSession(); 