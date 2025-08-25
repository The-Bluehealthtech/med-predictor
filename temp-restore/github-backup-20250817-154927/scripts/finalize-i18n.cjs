const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';
const AUTO_FR_PATH = 'auto_i18n_fr.json';
const AUTO_EN_PATH = 'auto_i18n_en.json';

// Dictionnaire de traduction simple pour les termes courants
const TRANSLATION_DICT = {
  // FR -> EN
  'Nouvelle Session': 'New Session',
  'Exporter Performance': 'Export Performance',
  'Composant': 'Component',
  'En cours de développement': 'Under development',
  'Nouvelle Sélection': 'New Selection',
  'Exporter FIFA': 'Export FIFA',
  'Équipe': 'Team',
  'Toutes les équipes': 'All teams',
  'Compétition': 'Competition',
  'Toutes les compétitions': 'All competitions',
  'Coupe d\'Afrique': 'Africa Cup',
  'Match Amical': 'Friendly Match',
  'Qualifications': 'Qualifications',
  'Statut': 'Status',
  'Tous les statuts': 'All statuses',
  'Brouillon': 'Draft',
  'Publiée': 'Published',
  'Confirmée': 'Confirmed',
  'Terminée': 'Completed',
  'Date': 'Date',
  'Domicile': 'Home',
  'Extérieur': 'Away',
  'Action': 'Action',
  'Chargement...': 'Loading...',
  'Mes matchs à arbitrer': 'My matches to referee',
  'Si vous voyez ceci, Vue.js fonctionne !': 'If you see this, Vue.js is working!',
  
  // EN -> FR
  'New Session': 'Nouvelle Session',
  'Export Performance': 'Exporter Performance',
  'Component': 'Composant',
  'Under development': 'En cours de développement',
  'New Selection': 'Nouvelle Sélection',
  'Export FIFA': 'Exporter FIFA',
  'Team': 'Équipe',
  'All teams': 'Toutes les équipes',
  'Competition': 'Compétition',
  'All competitions': 'Toutes les compétitions',
  'Africa Cup': 'Coupe d\'Afrique',
  'Friendly Match': 'Match Amical',
  'Status': 'Statut',
  'All statuses': 'Tous les statuts',
  'Draft': 'Brouillon',
  'Published': 'Publiée',
  'Confirmed': 'Confirmée',
  'Completed': 'Terminée',
  'Home': 'Domicile',
  'Away': 'Extérieur',
  'Loading...': 'Chargement...',
  'My matches to referee': 'Mes matchs à arbitrer',
  'If you see this, Vue.js is working!': 'Si vous voyez ceci, Vue.js fonctionne !'
};

function translateText(text, fromLang, toLang) {
  if (fromLang === 'fr' && toLang === 'en') {
    return TRANSLATION_DICT[text] || text;
  } else if (fromLang === 'en' && toLang === 'fr') {
    return TRANSLATION_DICT[text] || text;
  }
  return text;
}

function mergeAndOrganizeKeys() {
  console.log('🔄 Début de la fusion et organisation des clés...');
  
  // Charger les fichiers existants
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  // Charger les fichiers auto_i18n
  const autoFr = JSON.parse(fs.readFileSync(AUTO_FR_PATH, 'utf8'));
  const autoEn = JSON.parse(fs.readFileSync(AUTO_EN_PATH, 'utf8'));
  
  // Organiser les clés par catégorie
  const organizedKeys = {
    rpm: {},
    dtn: {},
    referee: {},
    player: {},
    common: {}
  };
  
  let keyCounter = 1;
  
  // Traiter chaque clé auto
  for (const key in autoFr) {
    const frValue = autoFr[key];
    const enValue = autoEn[key] || frValue;
    
    // Déterminer la catégorie basée sur le contenu
    let category = 'common';
    if (frValue.includes('RPM') || frValue.includes('Session') || frValue.includes('Training')) {
      category = 'rpm';
    } else if (frValue.includes('DTN') || frValue.includes('National') || frValue.includes('Selection')) {
      category = 'dtn';
    } else if (frValue.includes('arbitre') || frValue.includes('referee') || frValue.includes('match')) {
      category = 'referee';
    } else if (frValue.includes('Player') || frValue.includes('Joueur') || frValue.includes('Profile')) {
      category = 'player';
    }
    
    // Créer une clé organisée
    const organizedKey = `${category}.key${keyCounter}`;
    organizedKeys[category][organizedKey] = {
      fr: frValue,
      en: enValue
    };
    keyCounter++;
  }
  
  // Fusionner dans les fichiers principaux
  for (const category in organizedKeys) {
    if (!fr[category]) fr[category] = {};
    if (!en[category]) en[category] = {};
    
    for (const key in organizedKeys[category]) {
      const values = organizedKeys[category][key];
      
      // Traduire si nécessaire
      const finalFrValue = values.fr;
      const finalEnValue = values.en === values.fr ? 
        translateText(values.fr, 'fr', 'en') : values.en;
      
      fr[category][key] = finalFrValue;
      en[category][key] = finalEnValue;
    }
  }
  
  // Sauvegarder les fichiers
  fs.writeFileSync(FR_PATH, JSON.stringify(fr, null, 2), 'utf8');
  fs.writeFileSync(EN_PATH, JSON.stringify(en, null, 2), 'utf8');
  
  console.log('✅ Fusion terminée !');
  console.log(`📊 Clés ajoutées :`);
  for (const category in organizedKeys) {
    console.log(`   - ${category}: ${Object.keys(organizedKeys[category]).length} clés`);
  }
  
  return organizedKeys;
}

function createRenamingScript(organizedKeys) {
  console.log('\n📝 Création du script de renommage...');
  
  let renamingScript = '// Script de renommage des clés - À exécuter manuellement\n';
  renamingScript += '// Remplacez les clés auto par des noms plus descriptifs\n\n';
  
  for (const category in organizedKeys) {
    renamingScript += `// === ${category.toUpperCase()} ===\n`;
    for (const key in organizedKeys[category]) {
      const values = organizedKeys[category][key];
      const sampleValue = values.fr.length > 30 ? values.fr.substring(0, 30) + '...' : values.fr;
      renamingScript += `// ${key} -> "Nouveau nom descriptif" (${sampleValue})\n`;
    }
    renamingScript += '\n';
  }
  
  fs.writeFileSync('scripts/rename-keys-suggestions.txt', renamingScript, 'utf8');
  console.log('✅ Suggestions de renommage sauvegardées dans scripts/rename-keys-suggestions.txt');
}

function cleanupAutoFiles() {
  console.log('\n🧹 Nettoyage des fichiers temporaires...');
  
  if (fs.existsSync(AUTO_FR_PATH)) {
    fs.unlinkSync(AUTO_FR_PATH);
    console.log('✅ auto_i18n_fr.json supprimé');
  }
  
  if (fs.existsSync(AUTO_EN_PATH)) {
    fs.unlinkSync(AUTO_EN_PATH);
    console.log('✅ auto_i18n_en.json supprimé');
  }
}

// Exécution principale
try {
  const organizedKeys = mergeAndOrganizeKeys();
  createRenamingScript(organizedKeys);
  cleanupAutoFiles();
  
  console.log('\n🎉 Traduction finalisée avec succès !');
  console.log('\n📋 Prochaines étapes :');
  console.log('1. Vérifiez les traductions dans resources/js/i18n/fr.json et en.json');
  console.log('2. Renommez les clés auto.* en noms plus descriptifs');
  console.log('3. Testez l\'application pour vérifier que tout fonctionne');
  console.log('4. Corrigez manuellement les traductions si nécessaire');
  
} catch (error) {
  console.error('❌ Erreur lors de la finalisation :', error.message);
  process.exit(1);
} 