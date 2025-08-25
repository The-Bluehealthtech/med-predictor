const fs = require('fs');
const path = require('path');

function debugI18n() {
  console.log('🔍 Diagnostic de l\'internationalisation...\n');
  
  // 1. Vérifier les fichiers de traduction
  console.log('📁 1. Vérification des fichiers de traduction');
  
  const frPath = 'resources/js/i18n/fr.json';
  const enPath = 'resources/js/i18n/en.json';
  const i18nIndexPath = 'resources/js/i18n/index.js';
  
  if (fs.existsSync(frPath)) {
    console.log('   ✅ fr.json existe');
    const frContent = JSON.parse(fs.readFileSync(frPath, 'utf8'));
    console.log(`   📊 ${Object.keys(frContent).length} modules dans fr.json`);
  } else {
    console.log('   ❌ fr.json manquant');
  }
  
  if (fs.existsSync(enPath)) {
    console.log('   ✅ en.json existe');
    const enContent = JSON.parse(fs.readFileSync(enPath, 'utf8'));
    console.log(`   📊 ${Object.keys(enContent).length} modules dans en.json`);
  } else {
    console.log('   ❌ en.json manquant');
  }
  
  if (fs.existsSync(i18nIndexPath)) {
    console.log('   ✅ index.js existe');
  } else {
    console.log('   ❌ index.js manquant');
  }
  
  // 2. Vérifier la configuration i18n
  console.log('\n⚙️  2. Configuration i18n');
  
  if (fs.existsSync(i18nIndexPath)) {
    const i18nContent = fs.readFileSync(i18nIndexPath, 'utf8');
    console.log('   📝 Contenu de index.js:');
    console.log('   ' + i18nContent.split('\n').join('\n   '));
  }
  
  // 3. Vérifier l'intégration dans app.js
  console.log('\n🔧 3. Intégration dans app.js');
  
  const appJsPath = 'resources/js/app.js';
  if (fs.existsSync(appJsPath)) {
    const appJsContent = fs.readFileSync(appJsPath, 'utf8');
    if (appJsContent.includes('i18n')) {
      console.log('   ✅ i18n importé dans app.js');
    } else {
      console.log('   ❌ i18n non importé dans app.js');
    }
    
    if (appJsContent.includes('app.use(i18n)')) {
      console.log('   ✅ i18n utilisé dans l\'application');
    } else {
      console.log('   ❌ i18n non utilisé dans l\'application');
    }
  }
  
  // 4. Vérifier la configuration Laravel
  console.log('\n🌐 4. Configuration Laravel');
  
  const appConfigPath = 'config/app.php';
  if (fs.existsSync(appConfigPath)) {
    const appConfigContent = fs.readFileSync(appConfigPath, 'utf8');
    const localeMatch = appConfigContent.match(/'locale' => env\('APP_LOCALE', '([^']+)'\)/);
    const fallbackMatch = appConfigContent.match(/'fallback_locale' => env\('APP_FALLBACK_LOCALE', '([^']+)'\)/);
    
    if (localeMatch) {
      console.log(`   🌍 Locale par défaut: ${localeMatch[1]}`);
    }
    if (fallbackMatch) {
      console.log(`   🔄 Locale de fallback: ${fallbackMatch[1]}`);
    }
  }
  
  // 5. Vérifier les variables d'environnement
  console.log('\n🔑 5. Variables d\'environnement');
  
  const envPath = '.env';
  if (fs.existsSync(envPath)) {
    const envContent = fs.readFileSync(envPath, 'utf8');
    const appLocaleMatch = envContent.match(/APP_LOCALE=([^\n]+)/);
    const fallbackLocaleMatch = envContent.match(/APP_FALLBACK_LOCALE=([^\n]+)/);
    
    if (appLocaleMatch) {
      console.log(`   🌍 APP_LOCALE: ${appLocaleMatch[1]}`);
    } else {
      console.log('   ⚠️  APP_LOCALE non définie');
    }
    
    if (fallbackLocaleMatch) {
      console.log(`   🔄 APP_FALLBACK_LOCALE: ${fallbackLocaleMatch[1]}`);
    } else {
      console.log('   ⚠️  APP_FALLBACK_LOCALE non définie');
    }
  }
  
  // 6. Vérifier les clés de navigation spécifiques
  console.log('\n🧭 6. Clés de navigation');
  
  if (fs.existsSync(frPath) && fs.existsSync(enPath)) {
    const fr = JSON.parse(fs.readFileSync(frPath, 'utf8'));
    const en = JSON.parse(fs.readFileSync(enPath, 'utf8'));
    
    const navigationKeys = [
      'navigation.brand.fit',
      'navigation.main.dashboard',
      'navigation.main.players',
      'navigation.dropdowns.user.logout'
    ];
    
    for (const key of navigationKeys) {
      const frValue = getNestedValue(fr, key);
      const enValue = getNestedValue(en, key);
      
      if (frValue && enValue) {
        console.log(`   ✅ ${key}: "${frValue}" / "${enValue}"`);
      } else {
        console.log(`   ❌ ${key}: Clé manquante`);
      }
    }
  }
  
  // 7. Recommandations
  console.log('\n💡 7. Recommandations');
  console.log('   🔧 Pour forcer le français, ajoutez dans .env:');
  console.log('      APP_LOCALE=fr');
  console.log('      APP_FALLBACK_LOCALE=fr');
  console.log('\n   🔄 Pour tester le changement de langue:');
  console.log('      - Vérifiez que la route /lang/fr fonctionne');
  console.log('      - Vérifiez que window.LARAVEL_LOCALE est défini');
  console.log('\n   🧭 Pour déboguer la navigation:');
  console.log('      - Ouvrez la console du navigateur');
  console.log('      - Vérifiez les erreurs JavaScript');
  console.log('      - Testez: console.log(window.LARAVEL_LOCALE)');
}

function getNestedValue(obj, path) {
  return path.split('.').reduce((current, key) => current && current[key], obj);
}

// Exécution
try {
  debugI18n();
  console.log('\n🎯 Diagnostic terminé !');
} catch (error) {
  console.error('❌ Erreur lors du diagnostic :', error.message);
  process.exit(1);
} 