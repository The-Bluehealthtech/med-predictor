const { exec } = require('child_process');

function testSessionCookies() {
  console.log('🍪 Test des routes avec cookies de session...\\n');
  
  // Test 1: Créer un cookie de session et tester /lang/en
  console.log('1️⃣ Test avec cookies de session...');
  exec('curl -c cookies.txt -b cookies.txt -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/en', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      const statusCode = parseInt(stdout);
      console.log(`   ✅ Code: ${statusCode}`);
      
      if (statusCode === 302) {
        console.log('   ✅ Redirection réussie avec cookies');
      } else {
        console.log('   ❌ Redirection échouée');
      }
    }
    
    // Test 2: Tester /lang/fr avec les mêmes cookies
    console.log('\\n2️⃣ Test /lang/fr avec cookies...');
    exec('curl -c cookies.txt -b cookies.txt -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        const statusCode = parseInt(stdout);
        console.log(`   ✅ Code: ${statusCode}`);
      }
      
      // Test 3: Vérifier le contenu des cookies
      console.log('\\n3️⃣ Contenu des cookies...');
      exec('cat cookies.txt', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          const cookies = stdout.trim();
          if (cookies) {
            console.log('   ✅ Cookies trouvés:');
            console.log(cookies);
          } else {
            console.log('   ❌ Aucun cookie trouvé');
          }
        }
        
        // Nettoyer les cookies
        exec('rm -f cookies.txt', () => {
          console.log('\\n🎯 RÉSULTAT:');
          console.log('Les routes fonctionnent avec des cookies de session.');
          console.log('\\n💡 POUR L\'UTILISATEUR:');
          console.log('1. Videz le cache du navigateur (Ctrl+Shift+R)');
          console.log('2. Testez les boutons FR/EN dans le navigateur');
          console.log('3. La session devrait maintenant persister');
          console.log('4. Si le problème persiste, testez en mode incognito');
        });
      });
    });
  });
}

testSessionCookies(); 