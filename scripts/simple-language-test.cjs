 const { exec } = require('child_process');

console.log('🌐 Test simple du système de langue...\\n');

// Test 1: Vérifier la locale actuelle
exec('php artisan tinker --execute="echo \\'Locale actuelle: \\' . App::getLocale() . PHP_EOL;"', (error, stdout, stderr) => {
  if (error) {
    console.log('❌ Erreur:', error.message);
  } else {
    console.log(`✅ ${stdout.trim()}`);
  }
  
  // Test 2: Tester les traductions
  exec('php artisan tinker --execute="echo \\'FR - Admin: \\' . __(\\'navigation.admin\\') . PHP_EOL;"', (error, stdout, stderr) => {
    if (error) {
      console.log('❌ Erreur traduction FR:', error.message);
    } else {
      console.log(`✅ ${stdout.trim()}`);
    }
    
    exec('php artisan tinker --execute="App::setLocale(\\'en\\'); echo \\'EN - Admin: \\' . __(\\'navigation.admin\\') . PHP_EOL;"', (error, stdout, stderr) => {
      if (error) {
        console.log('❌ Erreur traduction EN:', error.message);
      } else {
        console.log(`✅ ${stdout.trim()}`);
      }
      
      console.log('\\n🎯 INSTRUCTIONS POUR L\'UTILISATEUR:');
      console.log('1. Ouvrez http://localhost:8000 dans votre navigateur');
      console.log('2. Connectez-vous à l\'application');
      console.log('3. Cliquez sur les boutons FR/EN en haut à droite de la navigation');
      console.log('4. Vérifiez que la navigation change de langue');
      console.log('\\n🔧 Si ça ne fonctionne pas:');
      console.log('- Vérifiez que le serveur Laravel tourne');
      console.log('- Vérifiez que vous êtes connecté');
      console.log('- Essayez de vider le cache du navigateur');
      console.log('- Vérifiez les logs Laravel dans storage/logs/laravel.log');
    });
  });
}); 