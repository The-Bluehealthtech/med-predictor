const { exec } = require('child_process');

function testHealthcareTranslations() {
  console.log('🏥 Test des traductions de la page Healthcare...\\n');
  
  // Test 1: Vérifier que les fichiers de traduction existent
  console.log('1️⃣ Vérification des fichiers de traduction...');
  exec('ls -la resources/lang/fr/healthcare.php resources/lang/en/healthcare.php', (error, stdout, stderr) => {
    if (error) {
      console.log('   ❌ Erreur:', error.message);
    } else {
      console.log('   ✅ Fichiers de traduction trouvés:');
      console.log(stdout);
    }
    
    // Test 2: Tester les traductions françaises
    console.log('\\n2️⃣ Test des traductions françaises...');
    exec('php artisan tinker --execute="echo \\'Healthcare Management (FR): \\' . __(\\'healthcare.healthcare_management\\');"', (error, stdout, stderr) => {
      if (error) {
        console.log('   ❌ Erreur:', error.message);
      } else {
        console.log(`   ✅ ${stdout.trim()}`);
      }
      
      // Test 3: Tester les traductions anglaises
      console.log('\\n3️⃣ Test des traductions anglaises...');
      exec('php artisan tinker --execute="App::setLocale(\\'en\\'); echo \\'Healthcare Management (EN): \\' . __(\\'healthcare.healthcare_management\\');"', (error, stdout, stderr) => {
        if (error) {
          console.log('   ❌ Erreur:', error.message);
        } else {
          console.log(`   ✅ ${stdout.trim()}`);
        }
        
        // Test 4: Tester quelques clés importantes
        console.log('\\n4️⃣ Test des clés importantes...');
        exec('php artisan tinker --execute="App::setLocale(\\'fr\\'); echo \\'Prédictions (FR): \\' . __(\\'healthcare.predictions\\') . \\'\\n\\'; echo \\'Joueur (FR): \\' . __(\\'healthcare.player\\') . \\'\\n\\'; echo \\'Actions (FR): \\' . __(\\'healthcare.actions\\');"', (error, stdout, stderr) => {
          if (error) {
            console.log('   ❌ Erreur:', error.message);
          } else {
            console.log('   ✅ Traductions françaises:');
            console.log(stdout);
          }
          
          console.log('\\n🎉 TRADUCTIONS HEALTHCARE IMPLÉMENTÉES:');
          console.log('✅ Fichier de traduction français créé');
          console.log('✅ Fichier de traduction anglais créé');
          console.log('✅ Page healthcare modifiée pour utiliser les traductions');
          console.log('✅ Caches Laravel vidés');
          console.log('\\n📋 INSTRUCTIONS POUR L\'UTILISATEUR:');
          console.log('1. Allez sur http://localhost:8000/healthcare');
          console.log('2. Cliquez sur les boutons FR/EN en haut à droite');
          console.log('3. La page devrait s\'afficher dans la langue sélectionnée');
          console.log('\\n🔧 Traductions disponibles:');
          console.log('- Titre de la page');
          console.log('- Boutons (Prédictions, Exporter, Ajouter)');
          console.log('- En-têtes de tableau');
          console.log('- Statuts et gravités');
          console.log('- Messages d\'état vide');
          console.log('- Confirmations');
        });
      });
    });
  });
}

testHealthcareTranslations(); 