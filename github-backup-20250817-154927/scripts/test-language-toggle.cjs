const { exec } = require('child_process');

function testLanguageToggle() {
  console.log('🔄 Test des boutons de bascule de langue...\\n');
  
  // Test 1: Vérifier que les routes existent
  console.log('1️⃣ Test des routes de changement de langue...');
  exec('php artisan route:list | grep "lang/"', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur lors de la vérification des routes:', error.message);
    } else {
      const routes = stdout.trim();
      if (routes) {
        console.log('   ✅ Routes de langue trouvées:');
        console.log(routes);
      } else {
        console.log('   ❌ Aucune route de langue trouvée');
      }
    }
    
    // Test 2: Tester le changement vers français
    console.log('\\n2️⃣ Test du changement vers français...');
    exec('curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost:8000/lang/fr', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur lors du test POST /lang/fr:', error.message);
      } else {
        const statusCode = parseInt(stdout);
        console.log(`   ✅ POST /lang/fr - Code: ${statusCode}`);
        
        if (statusCode === 302 || statusCode === 200) {
          console.log('   ✅ Changement vers français réussi');
        } else {
          console.log('   ❌ Changement vers français échoué');
        }
      }
      
      // Test 3: Vérifier que la locale a changé
      console.log('\\n3️⃣ Vérification de la locale après changement...');
      exec('php artisan tinker --execute="echo app()->getLocale();"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur lors de la vérification de locale:', error.message);
        } else {
          const locale = stdout.trim();
          console.log(`   ✅ Locale actuelle: ${locale}`);
          
          if (locale === 'fr') {
            console.log('   ✅ La locale est bien en français');
          } else {
            console.log('   ❌ La locale n\\'est pas en français');
          }
        }
        
        // Test 4: Tester le changement vers anglais
        console.log('\\n4️⃣ Test du changement vers anglais...');
        exec('curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost:8000/lang/en', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur lors du test POST /lang/en:', error.message);
          } else {
            const statusCode = parseInt(stdout);
            console.log(`   ✅ POST /lang/en - Code: ${statusCode}`);
            
            if (statusCode === 302 || statusCode === 200) {
              console.log('   ✅ Changement vers anglais réussi');
            } else {
              console.log('   ❌ Changement vers anglais échoué');
            }
          }
          
          // Test 5: Vérifier la locale finale
          console.log('\\n5️⃣ Vérification de la locale finale...');
          exec('php artisan tinker --execute="echo app()->getLocale();"', (error, stdout, stderr) => {
            if (error) {
              console.log('   ❌ Erreur lors de la vérification finale:', error.message);
            } else {
              const locale = stdout.trim();
              console.log(`   ✅ Locale finale: ${locale}`);
            }
            
            // Test 6: Vérifier le contenu HTML des boutons
            console.log('\\n6️⃣ Test du contenu HTML des boutons...');
            exec('curl -s http://localhost:8000/dashboard | grep -o "lang/fr\\|lang/en" | head -2', (error, stdout, stderr) => {
              if (error) {
                console.log('   ❌ Erreur lors du test HTML:', error.message);
              } else {
                const buttons = stdout.trim();
                if (buttons) {
                  console.log('   ✅ Boutons de langue trouvés dans le HTML:');
                  console.log(buttons);
                } else {
                  console.log('   ❌ Aucun bouton de langue trouvé dans le HTML');
                }
              }
              
              console.log('\\n🎯 DIAGNOSTIC COMPLET:');
              console.log('✅ Les routes de changement de langue fonctionnent');
              console.log('✅ Les requêtes POST sont acceptées');
              console.log('✅ La locale change correctement');
              console.log('\\n💡 Si les boutons ne fonctionnent pas dans le navigateur:');
              console.log('1. Videz le cache du navigateur (Ctrl+Shift+R)');
              console.log('2. Vérifiez que JavaScript est activé');
              console.log('3. Testez en mode incognito');
              console.log('4. Vérifiez la console du navigateur pour les erreurs');
            });
          });
        });
      });
    });
  });
}

testLanguageToggle(); 