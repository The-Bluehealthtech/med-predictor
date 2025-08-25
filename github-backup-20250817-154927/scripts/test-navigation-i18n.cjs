const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

function testNavigationTranslations() {
  console.log('🧭 Test des traductions de navigation...\n');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  // Clés de navigation à tester
  const navigationKeys = [
    'navigation.brand.fit',
    'navigation.brand.intelligence',
    'navigation.brand.football',
    'navigation.brand.health',
    'navigation.brand.fifaConnect',
    'navigation.main.dashboard',
    'navigation.main.players',
    'navigation.main.competitions',
    'navigation.main.performance',
    'navigation.main.medical',
    'navigation.main.transfers',
    'navigation.main.fifa',
    'navigation.main.admin',
    'navigation.dropdowns.players.playerDashboard',
    'navigation.dropdowns.players.playerRegistration',
    'navigation.dropdowns.competitions.leagueChampionship',
    'navigation.dropdowns.competitions.competitionManagement',
    'navigation.dropdowns.competitions.rankings',
    'navigation.dropdowns.fifa.fifaSyncDashboard',
    'navigation.dropdowns.fifa.fifaDashboard',
    'navigation.dropdowns.admin.backOffice',
    'navigation.dropdowns.admin.userManagement',
    'navigation.dropdowns.admin.roleManagement',
    'navigation.dropdowns.user.profile',
    'navigation.dropdowns.user.settings',
    'navigation.dropdowns.user.logout'
  ];
  
  let passedTests = 0;
  let failedTests = 0;
  const results = [];
  
  // Fonction pour obtenir une valeur imbriquée
  function getNestedValue(obj, path) {
    return path.split('.').reduce((current, key) => current && current[key], obj);
  }
  
  // Tester chaque clé
  for (const key of navigationKeys) {
    const frValue = getNestedValue(fr, key);
    const enValue = getNestedValue(en, key);
    
    if (frValue && enValue) {
      if (frValue !== enValue) {
        console.log(`✅ ${key}:`);
        console.log(`   FR: "${frValue}"`);
        console.log(`   EN: "${enValue}"`);
        passedTests++;
        results.push({ key, fr: frValue, en: enValue, status: 'passed' });
      } else {
        console.log(`⚠️  ${key}: Valeurs identiques "${frValue}"`);
        failedTests++;
        results.push({ key, fr: frValue, en: enValue, status: 'identical' });
      }
    } else {
      console.log(`❌ ${key}: Clé manquante`);
      failedTests++;
      results.push({ key, fr: frValue, en: enValue, status: 'missing' });
    }
  }
  
  // Résumé
  console.log('\n📊 Résumé des tests :');
  console.log(`   ✅ Tests réussis: ${passedTests}`);
  console.log(`   ⚠️  Valeurs identiques: ${failedTests}`);
  console.log(`   📝 Total: ${navigationKeys.length}`);
  
  // Sauvegarder le rapport
  const report = {
    timestamp: new Date().toISOString(),
    summary: {
      total: navigationKeys.length,
      passed: passedTests,
      failed: failedTests
    },
    results
  };
  
  fs.writeFileSync('scripts/navigation-test-report.json', JSON.stringify(report, null, 2), 'utf8');
  console.log('\n📝 Rapport sauvegardé dans scripts/navigation-test-report.json');
  
  if (failedTests === 0) {
    console.log('\n🎉 Toutes les clés de navigation sont correctement traduites !');
  } else {
    console.log('\n⚠️  Certaines clés nécessitent une attention.');
  }
  
  return report;
}

// Exécution
try {
  const report = testNavigationTranslations();
  
  console.log('\n🚀 Test de navigation terminé !');
  
} catch (error) {
  console.error('❌ Erreur lors du test :', error.message);
  process.exit(1);
} 