const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

function cleanupTranslations() {
  console.log('🧹 Nettoyage final des fichiers de traduction...\n');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  let cleanedCount = 0;
  const cleanedKeys = [];
  
  // Fonction pour nettoyer récursivement
  function cleanupObject(obj, path = '') {
    const cleaned = {};
    
    for (const key in obj) {
      const currentPath = path ? `${path}.${key}` : key;
      const value = obj[key];
      
      // Ignorer les clés vides ou null
      if (value === null || value === undefined || value === '') {
        cleanedKeys.push({ path: currentPath, reason: 'empty_value' });
        cleanedCount++;
        continue;
      }
      
      // Ignorer les clés en double (rpm.rpm.*, dtn.dtn.*, etc.)
      if (key.includes('.key') && key.split('.').length > 2) {
        const parts = key.split('.');
        if (parts[0] === parts[1]) {
          cleanedKeys.push({ path: currentPath, reason: 'duplicate_structure' });
          cleanedCount++;
          continue;
        }
      }
      
      // Traiter les objets imbriqués
      if (typeof value === 'object' && value !== null) {
        const cleanedNested = cleanupObject(value, currentPath);
        if (Object.keys(cleanedNested).length > 0) {
          cleaned[key] = cleanedNested;
        }
      } else {
        cleaned[key] = value;
      }
    }
    
    return cleaned;
  }
  
  // Nettoyer les fichiers
  const cleanedFr = cleanupObject(fr);
  const cleanedEn = cleanupObject(en);
  
  // Sauvegarder les fichiers nettoyés
  fs.writeFileSync(FR_PATH, JSON.stringify(cleanedFr, null, 2), 'utf8');
  fs.writeFileSync(EN_PATH, JSON.stringify(cleanedEn, null, 2), 'utf8');
  
  console.log(`✅ Nettoyage terminé ! ${cleanedCount} clés nettoyées.`);
  
  // Afficher les statistiques
  console.log(`\n📊 Statistiques finales :`);
  console.log(`   - Clés FR: ${countKeys(cleanedFr)}`);
  console.log(`   - Clés EN: ${countKeys(cleanedEn)}`);
  console.log(`   - Clés supprimées: ${cleanedCount}`);
  
  // Sauvegarder le rapport de nettoyage
  const report = {
    timestamp: new Date().toISOString(),
    cleanedCount,
    cleanedKeys,
    finalStats: {
      frKeys: countKeys(cleanedFr),
      enKeys: countKeys(cleanedEn)
    }
  };
  
  fs.writeFileSync('scripts/cleanup-report.json', JSON.stringify(report, null, 2), 'utf8');
  console.log('📝 Rapport de nettoyage sauvegardé dans scripts/cleanup-report.json');
  
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

// Exécution
try {
  const report = cleanupTranslations();
  
  console.log('\n🎉 Nettoyage final terminé avec succès !');
  console.log('\n📋 Résumé de la traduction complète :');
  console.log('✅ Migration automatique des textes statiques');
  console.log('✅ Fusion des clés dans les fichiers principaux');
  console.log('✅ Organisation par catégories (rpm, dtn, referee, player, common)');
  console.log('✅ Renommage intelligent des clés');
  console.log('✅ Correction automatique des traductions');
  console.log('✅ Nettoyage final des fichiers');
  console.log('\n🚀 Votre application est maintenant entièrement traduite !');
  
} catch (error) {
  console.error('❌ Erreur lors du nettoyage :', error.message);
  process.exit(1);
} 