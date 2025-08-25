const { exec } = require('child_process');

function testLanguageSwitching() {
  console.log('🌐 Test du système de bascule de langue...\\n');
  
  // Test 1: Vérifier la configuration actuelle
  console.log('1️⃣ Configuration actuelle:');
  exec('php artisan tinker --execute="echo \\'Locale: \\' . App::getLocale() . PHP_EOL; echo \\'Session locale: \\' . Session::get(\\'locale\\', \\'non définie\\') . PHP_EOL; echo \\'Config locale: \\' . config(\\'app.locale\\') . PHP_EOL;"', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log('   ✅ Configuration:');
      console.log(stdout);
    }
    
    // Test 2: Tester les routes de langue
    console.log('\\n2️⃣ Test des routes de langue:');
    exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur route FR:', error.message);
      } else {
        console.log(`   ✅ Route /lang/fr: ${stdout}`);
      }
      
      exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/en', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur route EN:', error.message);
        } else {
          console.log(`   ✅ Route /lang/en: ${stdout}`);
        }
        
        // Test 3: Tester avec cookies pour simuler une session
        console.log('\\n3️⃣ Test avec session (cookies):');
        exec('curl -s -c cookies.txt -b cookies.txt -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur session FR:', error.message);
          } else {
            console.log(`   ✅ Session FR: ${stdout}`);
          }
          
          exec('curl -s -c cookies.txt -b cookies.txt -o /dev/null -w "%{http_code}" http://localhost:8000/lang/en', (error, stdout, stderr) => {
            if (error) {
              console.log('   ❌ Erreur session EN:', error.message);
            } else {
              console.log(`   ✅ Session EN: ${stdout}`);
            }
            
            // Test 4: Vérifier les traductions
            console.log('\\n4️⃣ Test des traductions:');
            exec('php artisan tinker --execute="App::setLocale(\\'fr\\'); echo \\'FR - Admin: \\' . __(\\'navigation.admin\\') . PHP_EOL;"', (error, stdout, stderr) => {
              if (error) {
                console.log('   ❌ Erreur traduction FR:', error.message);
              } else {
                console.log(`   ✅ ${stdout.trim()}`);
              }
              
              exec('php artisan tinker --execute="App::setLocale(\\'en\\'); echo \\'EN - Admin: \\' . __(\\'navigation.admin\\') . PHP_EOL;"', (error, stdout, stderr) => {
                if (error) {
                  console.log('   ❌ Erreur traduction EN:', error.message);
                } else {
                  console.log(`   ✅ ${stdout.trim()}`);
                }
                
                // Test 5: Vérifier le middleware
                console.log('\\n5️⃣ Test du middleware SetLocale:');
                exec('php artisan tinker --execute="Session::put(\\'locale\\', \\'en\\'); echo \\'Session mise à jour: \\' . Session::get(\\'locale\\') . PHP_EOL;"', (error, stdout, stderr) => {
                  if (error) {
                    console.log('   ❌ Erreur middleware:', error.message);
                  } else {
                    console.log(`   ✅ ${stdout.trim()}`);
                  }
                  
                  console.log('\\n🔧 DIAGNOSTIC TERMINÉ');
                  console.log('\\n📋 INSTRUCTIONS POUR L\'UTILISATEUR:');
                  console.log('1. Vérifiez que le serveur Laravel tourne sur http://localhost:8000');
                  console.log('2. Connectez-vous à l\'application');
                  console.log('3. Cliquez sur les boutons FR/EN en haut à droite');
                  console.log('4. Vérifiez que la navigation change de langue');
                  console.log('\\n🔍 PROBLÈMES POSSIBLES:');
                  console.log('- Session non persistante');
                  console.log('- Middleware SetLocale non appliqué');
                  console.log('- Routes de langue non accessibles');
                  console.log('- Cache Laravel non vidé');
                });
              });
            });
          });
        });
      });
    });
  });
}

testLanguageSwitching(); 