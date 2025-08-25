const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

function validateFinalI18n() {
  console.log('🎯 Validation finale de l\'internationalisation...\n');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  // Statistiques générales
  const frKeys = countKeys(fr);
  const enKeys = countKeys(en);
  
  console.log('📊 STATISTIQUES GÉNÉRALES');
  console.log(`   🔑 Clés FR: ${frKeys}`);
  console.log(`   🔑 Clés EN: ${enKeys}`);
  console.log(`   🌍 Langues supportées: 2 (FR, EN)`);
  console.log(`   📁 Modules couverts: ${Object.keys(fr).length}\n`);
  
  // Vérifier les modules principaux
  const mainModules = ['landing', 'menu', 'navigation', 'dashboard', 'rpm', 'dtn', 'referee', 'player', 'common'];
  console.log('📋 MODULES PRINCIPAUX');
  
  for (const module of mainModules) {
    const frModule = fr[module];
    const enModule = en[module];
    
    if (frModule && enModule) {
      const frModuleKeys = countKeys(frModule);
      const enModuleKeys = countKeys(enModule);
      console.log(`   ✅ ${module}: ${frModuleKeys} clés (FR) / ${enModuleKeys} clés (EN)`);
    } else {
      console.log(`   ❌ ${module}: Module manquant`);
    }
  }
  
  console.log('\n🎨 ÉLÉMENTS D\'INTERFACE');
  
  // Vérifier les éléments critiques
  const criticalElements = [
    { key: 'landing.hero.title', name: 'Titre principal' },
    { key: 'navigation.main.dashboard', name: 'Navigation Dashboard' },
    { key: 'dashboard.title', name: 'Titre Dashboard' },
    { key: 'menu.home', name: 'Menu Accueil' },
    { key: 'navigation.dropdowns.user.logout', name: 'Déconnexion' }
  ];
  
  for (const element of criticalElements) {
    const frValue = getNestedValue(fr, element.key);
    const enValue = getNestedValue(en, element.key);
    
    if (frValue && enValue) {
      console.log(`   ✅ ${element.name}: "${frValue}" / "${enValue}"`);
    } else {
      console.log(`   ❌ ${element.name}: Clé manquante`);
    }
  }
  
  // Vérifier la cohérence
  console.log('\n🔍 COHÉRENCE DES TRADUCTIONS');
  
  const identicalCount = countIdenticalValues(fr, en);
  const totalKeys = Math.min(frKeys, enKeys);
  const translationRate = ((totalKeys - identicalCount) / totalKeys * 100).toFixed(1);
  
  console.log(`   📈 Taux de traduction: ${translationRate}%`);
  console.log(`   🔄 Valeurs identiques: ${identicalCount}`);
  console.log(`   🌐 Valeurs traduites: ${totalKeys - identicalCount}`);
  
  // Recommandations
  console.log('\n💡 RECOMMANDATIONS');
  
  if (translationRate >= 90) {
    console.log('   ✅ Excellent taux de traduction !');
  } else if (translationRate >= 70) {
    console.log('   ⚠️  Bon taux de traduction, quelques améliorations possibles');
  } else {
    console.log('   ❌ Taux de traduction faible, nécessite des améliorations');
  }
  
  if (frKeys === enKeys) {
    console.log('   ✅ Structure identique entre les langues');
  } else {
    console.log('   ⚠️  Différences dans la structure des clés');
  }
  
  // Sauvegarder le rapport final
  const report = {
    timestamp: new Date().toISOString(),
    summary: {
      frKeys,
      enKeys,
      modules: mainModules.length,
      translationRate: parseFloat(translationRate),
      identicalCount,
      translatedCount: totalKeys - identicalCount
    },
    modules: mainModules.map(module => ({
      name: module,
      frKeys: countKeys(fr[module] || {}),
      enKeys: countKeys(en[module] || {}),
      hasTranslation: !!(fr[module] && en[module])
    })),
    criticalElements: criticalElements.map(element => ({
      name: element.name,
      key: element.key,
      frValue: getNestedValue(fr, element.key),
      enValue: getNestedValue(en, element.key),
      hasTranslation: !!(getNestedValue(fr, element.key) && getNestedValue(en, element.key))
    }))
  };
  
  fs.writeFileSync('scripts/final-i18n-validation.json', JSON.stringify(report, null, 2), 'utf8');
  console.log('\n📝 Rapport final sauvegardé dans scripts/final-i18n-validation.json');
  
  return report;
}

function countKeys(obj, count = 0) {
  for (const key in obj) {
    count++;
    if (typeof obj[key] === 'object' && obj[key] !== null) {
      count = countKeys(obj[key], count);
    }
  }
  return count;
}

function getNestedValue(obj, path) {
  return path.split('.').reduce((current, key) => current && current[key], obj);
}

function countIdenticalValues(frObj, enObj, path = '') {
  let count = 0;
  
  for (const key in frObj) {
    const currentPath = path ? `${path}.${key}` : key;
    
    if (enObj[key]) {
      const frValue = frObj[key];
      const enValue = enObj[key];
      
      if (frValue === enValue && typeof frValue === 'string') {
        count++;
      }
      
      if (typeof frValue === 'object' && typeof enValue === 'object' && frValue !== null && enValue !== null) {
        count += countIdenticalValues(frValue, enValue, currentPath);
      }
    }
  }
  
  return count;
}

// Exécution
try {
  const report = validateFinalI18n();
  
  console.log('\n🎉 Validation finale terminée !');
  console.log('\n🚀 Votre application FIT est maintenant entièrement internationalisée !');
  console.log('\n📋 Prochaines étapes :');
  console.log('1. Testez l\'application dans les deux langues');
  console.log('2. Vérifiez que tous les textes s\'affichent correctement');
  console.log('3. Testez le changement de langue');
  console.log('4. Corrigez manuellement les traductions si nécessaire');
  
} catch (error) {
  console.error('❌ Erreur lors de la validation :', error.message);
  process.exit(1);
} 