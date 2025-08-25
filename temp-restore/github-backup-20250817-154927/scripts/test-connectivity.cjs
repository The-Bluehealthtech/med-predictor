const { exec } = require('child_process');
const http = require('http');

function testConnectivity() {
  console.log('🔌 Test de connectivité des serveurs...\n');
  
  const servers = [
    { name: 'Laravel', port: 8000, url: 'http://localhost:8000' },
    { name: 'Vite', port: 5173, url: 'http://localhost:5173' }
  ];
  
  let allTestsPassed = true;
  
  servers.forEach(server => {
    console.log(`🌐 Test de ${server.name} (port ${server.port})...`);
    
    // Test avec curl
    exec(`curl -s -o /dev/null -w "%{http_code}" ${server.url}`, (error, stdout, stderr) => {
      if (error) {
        console.log(`   ❌ ${server.name} non accessible: ${error.message}`);
        allTestsPassed = false;
      } else {
        const statusCode = parseInt(stdout);
        if (statusCode >= 200 && statusCode < 500) {
          console.log(`   ✅ ${server.name} accessible (HTTP ${statusCode})`);
        } else {
          console.log(`   ⚠️  ${server.name} répond avec HTTP ${statusCode}`);
          allTestsPassed = false;
        }
      }
      
      // Vérifier si c'est le dernier test
      if (server === servers[servers.length - 1]) {
        console.log('\n📊 RÉSULTAT:');
        if (allTestsPassed) {
          console.log('   ✅ Tous les serveurs sont accessibles !');
          console.log('\n🌍 Vous pouvez maintenant accéder à :');
          console.log('   🏠 Page d\'accueil: http://localhost:8000');
          console.log('   🔧 Vite dev server: http://localhost:5173');
          console.log('\n🧭 Pour tester la navigation française :');
          console.log('   1. Ouvrez http://localhost:8000');
          console.log('   2. Vérifiez que la navigation s\'affiche en français');
          console.log('   3. Si ce n\'est pas le cas, allez sur http://localhost:8000/lang/fr');
        } else {
          console.log('   ❌ Certains serveurs ne sont pas accessibles');
          console.log('\n🔧 Solutions :');
          console.log('   1. Redémarrez les serveurs :');
          console.log('      php artisan serve --host=localhost --port=8000');
          console.log('      npm run dev');
          console.log('   2. Vérifiez qu\'aucun autre processus n\'utilise ces ports');
          console.log('   3. Vérifiez les logs d\'erreur');
        }
      }
    });
  });
}

// Exécution
try {
  testConnectivity();
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 