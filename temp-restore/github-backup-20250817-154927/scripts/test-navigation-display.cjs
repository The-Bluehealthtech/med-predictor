const fs = require('fs');
const path = require('path');

function testNavigationDisplay() {
  console.log('🧭 Test de l\'affichage de la navigation...\n');
  
  // Charger les fichiers de traduction
  const fr = JSON.parse(fs.readFileSync('resources/js/i18n/fr.json', 'utf8'));
  const en = JSON.parse(fs.readFileSync('resources/js/i18n/en.json', 'utf8'));
  
  // Simuler l'affichage de la navigation
  console.log('📱 SIMULATION DE L\'AFFICHAGE DE LA NAVIGATION');
  console.log('=' .repeat(50));
  
  // Brand
  console.log('\n🏷️  BRAND:');
  console.log(`   Logo: ${fr.navigation.brand.fit} ${fr.navigation.brand.intelligence}`);
  console.log(`   Sous-titre: ${fr.navigation.brand.football} ${fr.navigation.brand.health}`);
  console.log(`   Badge: ${fr.navigation.brand.fifaConnect}`);
  
  // Menu principal
  console.log('\n📋 MENU PRINCIPAL:');
  console.log(`   📊 ${fr.navigation.main.dashboard}`);
  console.log(`   👥 ${fr.navigation.main.players}`);
  console.log(`   🏆 ${fr.navigation.main.competitions}`);
  console.log(`   📈 ${fr.navigation.main.performance}`);
  console.log(`   🏥 ${fr.navigation.main.medical}`);
  console.log(`   🔄 ${fr.navigation.main.transfers}`);
  console.log(`   ⚽ ${fr.navigation.main.fifa}`);
  console.log(`   ⚙️  ${fr.navigation.main.admin}`);
  
  // Menus déroulants
  console.log('\n📂 MENUS DÉROULANTS:');
  
  console.log('   👥 Players:');
  console.log(`      - ${fr.navigation.dropdowns.players.playerDashboard}`);
  console.log(`      - ${fr.navigation.dropdowns.players.playerRegistration}`);
  
  console.log('   🏆 Competitions:');
  console.log(`      - ${fr.navigation.dropdowns.competitions.leagueChampionship}`);
  console.log(`      - ${fr.navigation.dropdowns.competitions.competitionManagement}`);
  console.log(`      - ${fr.navigation.dropdowns.competitions.rankings}`);
  
  console.log('   ⚽ FIFA:');
  console.log(`      - ${fr.navigation.dropdowns.fifa.fifaSyncDashboard}`);
  console.log(`      - ${fr.navigation.dropdowns.fifa.fifaDashboard}`);
  
  console.log('   ⚙️  Admin:');
  console.log(`      - ${fr.navigation.dropdowns.admin.backOffice}`);
  console.log(`      - ${fr.navigation.dropdowns.admin.userManagement}`);
  console.log(`      - ${fr.navigation.dropdowns.admin.roleManagement}`);
  
  console.log('   👤 User:');
  console.log(`      - ${fr.navigation.dropdowns.user.profile}`);
  console.log(`      - ${fr.navigation.dropdowns.user.settings}`);
  console.log(`      - ${fr.navigation.dropdowns.user.logout}`);
  
  // Vérification des variables d'environnement
  console.log('\n🔧 CONFIGURATION:');
  console.log('   🌍 APP_LOCALE devrait être: fr');
  console.log('   🔄 APP_FALLBACK_LOCALE devrait être: fr');
  console.log('   🪟 window.LARAVEL_LOCALE devrait être: fr');
  
  // Instructions de test
  console.log('\n🧪 INSTRUCTIONS DE TEST:');
  console.log('1. Ouvrez http://localhost:8080 dans votre navigateur');
  console.log('2. Vérifiez que la navigation s\'affiche en français');
  console.log('3. Ouvrez la console (F12) et tapez: console.log(window.LARAVEL_LOCALE)');
  console.log('4. Si ce n\'est pas "fr", allez sur http://localhost:8080/lang/fr');
  console.log('5. Vérifiez que tous les menus sont traduits');
  
  // Vérification des erreurs potentielles
  console.log('\n⚠️  ERREURS POTENTIELLES:');
  console.log('   ❌ Si la navigation reste en anglais:');
  console.log('      - Vérifiez que le serveur a été redémarré');
  console.log('      - Vérifiez que les variables d\'env sont chargées');
  console.log('      - Vérifiez la console du navigateur pour les erreurs JS');
  
  console.log('   ❌ Si certaines clés ne s\'affichent pas:');
  console.log('      - Vérifiez que le composant Navigation.vue utilise $t()');
  console.log('      - Vérifiez que les clés existent dans fr.json');
  
  console.log('   ❌ Si l\'application ne se charge pas:');
  console.log('      - Vérifiez que Vite fonctionne (npm run dev)');
  console.log('      - Vérifiez les erreurs dans la console du navigateur');
  
  return {
    frKeys: Object.keys(fr.navigation || {}).length,
    enKeys: Object.keys(en.navigation || {}).length,
    hasNavigation: !!(fr.navigation && en.navigation)
  };
}

// Exécution
try {
  const result = testNavigationDisplay();
  console.log('\n📊 RÉSULTAT:');
  console.log(`   ✅ Clés FR: ${result.frKeys}`);
  console.log(`   ✅ Clés EN: ${result.enKeys}`);
  console.log(`   ✅ Navigation configurée: ${result.hasNavigation}`);
  
  console.log('\n🎉 Test terminé ! La navigation devrait maintenant s\'afficher en français.');
  
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 