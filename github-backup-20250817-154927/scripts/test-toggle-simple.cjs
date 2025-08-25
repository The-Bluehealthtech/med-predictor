const { exec } = require('child_process');

function testToggleSimple() {
  console.log('🔄 Test simple des boutons de bascule...\\n');
  
  // Test 1: Vérifier les routes
  console.log('1️⃣ Vérification des routes...');
  exec('php artisan route:list | grep lang', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log('   ✅ Routes trouvées:');
      console.log(stdout);
    }
    
    // Test 2: Tester POST /lang/fr
    console.log('\\n2️⃣ Test POST /lang/fr...');
    exec('curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost:8000/lang/fr', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        console.log(`   ✅ Code: ${stdout}`);
      }
      
      // Test 3: Vérifier la locale
      console.log('\\n3️⃣ Vérification de la locale...');
      exec('php artisan tinker --execute="echo app()->getLocale();"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          console.log(`   ✅ Locale: ${stdout.trim()}`);
        }
        
        console.log('\\n🎯 RÉSULTAT:');
        console.log('Si les tests passent, les boutons devraient fonctionner.');
        console.log('Si les boutons ne fonctionnent pas dans le navigateur:');
        console.log('1. Videz le cache (Ctrl+Shift+R)');
        console.log('2. Testez en mode incognito');
        console.log('3. Vérifiez la console du navigateur');
      });
    });
  });
}

testToggleSimple(); 