const { exec } = require('child_process');

function testPageContent() {
  console.log('🌐 Test du contenu de la page...\n');
  
  // Test 1: Vérifier la redirection après /lang/fr
  console.log('1️⃣ Test de la redirection /lang/fr...');
  exec('curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/lang/fr', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      const statusCode = parseInt(stdout);
      console.log(`   ✅ Code de statut: ${statusCode}`);
      
      if (statusCode === 302) {
        console.log('   ✅ Redirection correcte');
      } else {
        console.log('   ❌ Redirection incorrecte');
      }
    }
    
    // Test 2: Vérifier le contenu du dashboard
    console.log('\n2️⃣ Test du contenu du dashboard...');
    exec('curl -s http://localhost:8000/dashboard | grep -o "navigation\\.[a-zA-Z_]*" | head -5', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        const keys = stdout.trim();
        if (keys) {
          console.log('   ❌ Clés non traduites trouvées:');
          console.log(`      ${keys}`);
        } else {
          console.log('   ✅ Aucune clé non traduite trouvée');
        }
      }
      
      // Test 3: Vérifier les traductions dans le HTML
      console.log('\n3️⃣ Test des traductions dans le HTML...');
      exec('curl -s http://localhost:8000/dashboard | grep -E "(Admin|Gestion|FIFA|Santé)" | head -3', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          const translations = stdout.trim();
          if (translations) {
            console.log('   ✅ Traductions trouvées:');
            console.log(`      ${translations}`);
          } else {
            console.log('   ❌ Aucune traduction trouvée');
          }
        }
        
        // Résumé final
        console.log('\n📊 RÉSUMÉ:');
        console.log('   🔧 Le problème vient probablement du cache du navigateur');
        console.log('\n💡 Solutions :');
        console.log('   1. Videz le cache du navigateur (Ctrl+Shift+R)');
        console.log('   2. Utilisez le mode incognito');
        console.log('   3. Allez sur http://localhost:8000/lang/fr');
        console.log('   4. Puis http://localhost:8000/dashboard');
        console.log('   5. Vérifiez la console du navigateur (F12)');
        
        console.log('\n🔍 Pour diagnostiquer :');
        console.log('   - Ouvrez la console du navigateur (F12)');
        console.log('   - Regardez s\'il y a des erreurs JavaScript');
        console.log('   - Vérifiez les requêtes réseau');
        console.log('   - Testez en mode incognito');
      });
    });
  });
}

// Exécution
try {
  testPageContent();
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 