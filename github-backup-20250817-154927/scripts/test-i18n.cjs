const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

function validateTranslations() {
  console.log('🔍 Validation des fichiers de traduction...\n');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  const issues = {
    missingKeys: [],
    identicalValues: [],
    emptyValues: [],
    untranslatedValues: []
  };
  
  // Fonction pour comparer récursivement
  function compareObjects(frObj, enObj, path = '') {
    const allKeys = new Set([...Object.keys(frObj), ...Object.keys(enObj)]);
    
    for (const key of allKeys) {
      const currentPath = path ? `${path}.${key}` : key;
      
      // Vérifier les clés manquantes
      if (!frObj[key]) {
        issues.missingKeys.push({ path: currentPath, lang: 'fr', value: enObj[key] });
      } else if (!enObj[key]) {
        issues.missingKeys.push({ path: currentPath, lang: 'en', value: frObj[key] });
      } else {
        const frValue = frObj[key];
        const enValue = enObj[key];
        
        // Vérifier les valeurs identiques
        if (frValue === enValue) {
          issues.identicalValues.push({ path: currentPath, value: frValue });
        }
        
        // Vérifier les valeurs vides
        if (frValue === '' || enValue === '') {
          issues.emptyValues.push({ path: currentPath, fr: frValue, en: enValue });
        }
        
        // Vérifier les valeurs non traduites (contiennent des mots français en anglais)
        if (typeof enValue === 'string') {
          const frenchWords = ['Nouvelle', 'Composant', 'En cours', 'développement', 'Équipe', 'Compétition', 'Statut', 'Brouillon', 'Publiée', 'Confirmée', 'Terminée', 'Domicile', 'Extérieur', 'Chargement', 'arbitre', 'Joueur', 'Joueurs'];
          const hasFrenchInEn = frenchWords.some(word => enValue.includes(word));
          if (hasFrenchInEn) {
            issues.untranslatedValues.push({ path: currentPath, value: enValue });
          }
        }
        
        // Récursion pour les objets imbriqués
        if (typeof frValue === 'object' && typeof enValue === 'object' && frValue !== null && enValue !== null) {
          compareObjects(frValue, enValue, currentPath);
        }
      }
    }
  }
  
  compareObjects(fr, en);
  
  // Générer le rapport
  console.log('📊 RAPPORT DE VALIDATION\n');
  console.log(`📁 Fichier FR: ${FR_PATH}`);
  console.log(`📁 Fichier EN: ${EN_PATH}\n`);
  
  console.log(`🔑 Clés totales FR: ${countKeys(fr)}`);
  console.log(`🔑 Clés totales EN: ${countKeys(en)}\n`);
  
  // Afficher les problèmes
  if (issues.missingKeys.length > 0) {
    console.log(`❌ Clés manquantes (${issues.missingKeys.length}):`);
    issues.missingKeys.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.path} (${issue.lang})`);
    });
    if (issues.missingKeys.length > 10) {
      console.log(`   ... et ${issues.missingKeys.length - 10} autres`);
    }
    console.log('');
  }
  
  if (issues.identicalValues.length > 0) {
    console.log(`⚠️  Valeurs identiques (${issues.identicalValues.length}):`);
    issues.identicalValues.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.path}: "${issue.value}"`);
    });
    if (issues.identicalValues.length > 10) {
      console.log(`   ... et ${issues.identicalValues.length - 10} autres`);
    }
    console.log('');
  }
  
  if (issues.emptyValues.length > 0) {
    console.log(`⚠️  Valeurs vides (${issues.emptyValues.length}):`);
    issues.emptyValues.slice(0, 5).forEach(issue => {
      console.log(`   - ${issue.path}: FR="${issue.fr}" EN="${issue.en}"`);
    });
    if (issues.emptyValues.length > 5) {
      console.log(`   ... et ${issues.emptyValues.length - 5} autres`);
    }
    console.log('');
  }
  
  if (issues.untranslatedValues.length > 0) {
    console.log(`⚠️  Valeurs non traduites (${issues.untranslatedValues.length}):`);
    issues.untranslatedValues.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.path}: "${issue.value}"`);
    });
    if (issues.untranslatedValues.length > 10) {
      console.log(`   ... et ${issues.untranslatedValues.length - 10} autres`);
    }
    console.log('');
  }
  
  // Résumé
  const totalIssues = issues.missingKeys.length + issues.identicalValues.length + issues.emptyValues.length + issues.untranslatedValues.length;
  
  if (totalIssues === 0) {
    console.log('✅ Aucun problème détecté ! Les fichiers de traduction sont cohérents.');
  } else {
    console.log(`⚠️  ${totalIssues} problèmes détectés au total.`);
  }
  
  // Sauvegarder le rapport détaillé
  const report = {
    timestamp: new Date().toISOString(),
    summary: {
      totalKeysFr: countKeys(fr),
      totalKeysEn: countKeys(en),
      missingKeys: issues.missingKeys.length,
      identicalValues: issues.identicalValues.length,
      emptyValues: issues.emptyValues.length,
      untranslatedValues: issues.untranslatedValues.length
    },
    issues
  };
  
  fs.writeFileSync('scripts/i18n-validation-report.json', JSON.stringify(report, null, 2), 'utf8');
  console.log('\n📝 Rapport détaillé sauvegardé dans scripts/i18n-validation-report.json');
  
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
  const report = validateTranslations();
  
  console.log('\n🎯 Recommandations :');
  if (report.summary.missingKeys > 0) {
    console.log('1. Ajoutez les clés manquantes dans les deux langues');
  }
  if (report.summary.identicalValues > 0) {
    console.log('2. Traduisez les valeurs identiques');
  }
  if (report.summary.untranslatedValues > 0) {
    console.log('3. Corrigez les valeurs contenant du français en anglais');
  }
  if (report.summary.emptyValues > 0) {
    console.log('4. Remplissez les valeurs vides');
  }
  
} catch (error) {
  console.error('❌ Erreur lors de la validation :', error.message);
  process.exit(1);
} 