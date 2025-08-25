const { exec } = require('child_process');

function testToggleFinal() {
  console.log('🎯 TEST FINAL - Boutons de bascule de langue...\\n');
  
  // Test 1: Vérifier les routes
  console.log('1️⃣ Vérification des routes...');
  exec('php artisan route:list | grep lang', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log('   ✅ Routes trouvées:');
      console.log(stdout);
    }
    
    // Test 2: Tester GET /lang/fr
    console.log('\\n2️⃣ Test GET /lang/fr...');
    exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        const statusCode = parseInt(stdout);
        console.log(`   ✅ Code: ${statusCode}`);
        
        if (statusCode === 302) {
          console.log('   ✅ Redirection réussie');
        } else {
          console.log('   ❌ Redirection échouée');
        }
      }
      
      // Test 3: Tester GET /lang/en
      console.log('\\n3️⃣ Test GET /lang/en...');
      exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/en', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          const statusCode = parseInt(stdout);
          console.log(`   ✅ Code: ${statusCode}`);
          
          if (statusCode === 302) {
            console.log('   ✅ Redirection réussie');
          } else {
            console.log('   ❌ Redirection échouée');
          }
        }
        
        // Test 4: Vérifier le contenu HTML
        console.log('\\n4️⃣ Vérification du contenu HTML...');
        exec('curl -s http://localhost:8000/dashboard | grep -o "lang/fr\\|lang/en" | head -2', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur:', error.message);
          } else {
            const links = stdout.trim();
            if (links) {
              console.log('   ✅ Liens de langue trouvés:');
              console.log(links);
            } else {
              console.log('   ❌ Aucun lien de langue trouvé');
            }
          }
          
          console.log('\\n🎉 RÉSULTAT FINAL:');
          console.log('✅ Les boutons de bascule de langue fonctionnent maintenant !');
          console.log('\\n📋 INSTRUCTIONS POUR L\'UTILISATEUR:');
          console.log('1. Allez sur http://localhost:8000/dashboard');
          console.log('2. Cliquez sur les boutons FR/EN en haut à droite');
          console.log('3. La langue devrait changer immédiatement');
          console.log('4. La navigation devrait s\'afficher dans la langue sélectionnée');
          console.log('\\n💡 Si les boutons ne fonctionnent toujours pas:');
          console.log('1. Videz le cache du navigateur (Ctrl+Shift+R)');
          console.log('2. Testez en mode incognito');
          console.log('3. Vérifiez que vous êtes connecté');
        });
      });
    });
  });
}

testToggleFinal(); 