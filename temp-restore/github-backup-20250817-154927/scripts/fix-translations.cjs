const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

// Dictionnaire de traduction étendu
const TRANSLATION_DICT = {
  // FR -> EN
  'Date': 'Date',
  'Score': 'Score',
  'Actions': 'Actions',
  'Club': 'Club',
  'Type': 'Type',
  'N/A': 'N/A',
  '🚨 RPM DASHBOARD DEBUG BANNER 🚨': '🚨 RPM DASHBOARD DEBUG BANNER 🚨',
  'RPM Dashboard': 'RPM Dashboard',
  'Welcome to the RPM (Recovery Performance Management) system.': 'Welcome to the RPM (Recovery Performance Management) system.',
  'SessionsList': 'SessionsList',
  'Composant SessionsList - En cours de développement': 'SessionsList Component - Under development',
  'SessionDetail': 'SessionDetail',
  'Composant SessionDetail - En cours de développement': 'SessionDetail Component - Under development',
  'SessionAttendance': 'SessionAttendance',
  'Composant SessionAttendance - En cours de développement': 'SessionAttendance Component - Under development',
  'RPMSync': 'RPMSync',
  'Composant RPMSync - En cours de développement': 'RPMSync Component - Under development',
  'RPMSettings': 'RPMSettings',
  'Composant RPMSettings - En cours de développement': 'RPMSettings Component - Under development',
  'RPMReports': 'RPMReports',
  'Composant RPMReports - En cours de développement': 'RPMReports Component - Under development',
  'RPM Portal': 'RPM Portal',
  'Training Calendar (RPM)': 'Training Calendar (RPM)',
  'Session Editor (RPM)': 'Session Editor (RPM)',
  'Player Load Monitor (RPM)': 'Player Load Monitor (RPM)',
  'Match Preparation (RPM)': 'Match Preparation (RPM)',
  'Attendance Tracker (RPM)': 'Attendance Tracker (RPM)',
  'Training Sessions:': 'Training Sessions:',
  'SelectionPlayers': 'SelectionPlayers',
  'Composant SelectionPlayers - En cours de développement': 'SelectionPlayers Component - Under development',
  'SelectionForm': 'SelectionForm',
  'Composant SelectionForm - En cours de développement': 'SelectionForm Component - Under development',
  'SelectionDetail': 'SelectionDetail',
  'Composant SelectionDetail - En cours de développement': 'SelectionDetail Component - Under development',
  'DTN Manager Dashboard': 'DTN Manager Dashboard',
  'Technical Planning (DTN)': 'Technical Planning (DTN)',
  'National Teams management (DTN)': 'National Teams management (DTN)',
  'Medical Club Interface (DTN)': 'Medical Club Interface (DTN)',
  'International Selections management (DTN)': 'International Selections management (DTN)',
  'Expatriate Club Synchronization (DTN)': 'Expatriate Club Synchronization (DTN)',
  'Nationality': 'Nationality',
  'Nationalité:': 'Nationality:',
  'Mes matchs à arbitrer': 'My matches to referee',
  'Feuille de match': 'Match sheet',
  'Événements du match': 'Match events',
  'Tournament and match management': 'Tournament and match management',
  'Select 11-18 players for your match roster': 'Select 11-18 players for your match roster',
  'Loading match details...': 'Loading match details...',
  'Intervalle entre matchs (jours)': 'Interval between matches (days)',
  'Current standings based on match results': 'Current standings based on match results',
  'Manage match schedule and generate fixtures': 'Manage match schedule and generate fixtures',
  'All matches in this competition': 'All matches in this competition',
  'PlayerLoadDetail': 'PlayerLoadDetail',
  'Composant PlayerLoadDetail - En cours de développement': 'PlayerLoadDetail Component - Under development',
  '📸 Profile Picture': '📸 Profile Picture',
  'Players Management': 'Players Management',
  'Player': 'Player',
  'Players Only': 'Players Only',
  'Total Players': 'Total Players',
  'Profile': 'Profile',
  'Joueur': 'Player',
  'Add New Player': 'Add New Player',
  'Joueur *': 'Player *',
  'Joueurs Éligibles': 'Eligible Players',
  'Available Players': 'Available Players',
  'Exporter Performance': 'Export Performance',
  'Si vous voyez ceci, Vue.js fonctionne !': 'If you see this, Vue.js is working!',
  'MatchesList': 'MatchesList',
  'Composant MatchesList - En cours de développement': 'MatchesList Component - Under development',
  'MatchDetail': 'MatchDetail',
  'Composant MatchDetail - En cours de développement': 'MatchDetail Component - Under development',
  'TeamDetail': 'TeamDetail',
  'Composant TeamDetail - En cours de développement': 'TeamDetail Component - Under development',
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
  'Domicile': 'Home',
  'Extérieur': 'Away',
  'Chargement...': 'Loading...',
  'Action': 'Action',
  '⚽': '⚽',
  'GHS Score': 'GHS Score',
  '📊': '📊',
  'Injury Risk': 'Injury Risk',
  '📈': '📈',
  'Contribution': 'Contribution',
  '📋': '📋',
  'Status': 'Status',
  'Active': 'Active',
  '👤 Personal Information': '👤 Personal Information',
  'Name:': 'Name:',
  'FIFA ID:': 'FIFA ID:',
  'Position:': 'Position:',
  'Club:': 'Club:',
  '📋 License Status': '📋 License Status',
  'Status:': 'Status:',
  'Expires:': 'Expires:',
  '🏆 Career Stats': '🏆 Career Stats',
  'Matches:': 'Matches:',
  'Health Records:': 'Health Records:',
  '📊 GHS Breakdown': '📊 GHS Breakdown',
  'Physical:': 'Physical:',
  'Mental:': 'Mental:',
  'Civic:': 'Civic:',
  '💪 Fitness Data': '💪 Fitness Data',
  'Sleep Score:': 'Sleep Score:',
  'Risk Score:': 'Risk Score:'
};

function fixTranslations() {
  console.log('🔧 Correction automatique des traductions...\n');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  let fixedCount = 0;
  const fixedKeys = [];
  
  // Fonction pour corriger récursivement
  function fixInObject(frObj, enObj, path = '') {
    for (const key in frObj) {
      const currentPath = path ? `${path}.${key}` : key;
      
      if (enObj[key]) {
        const frValue = frObj[key];
        const enValue = enObj[key];
        
        // Corriger les valeurs identiques
        if (frValue === enValue && typeof frValue === 'string') {
          const translation = TRANSLATION_DICT[frValue];
          if (translation && translation !== frValue) {
            enObj[key] = translation;
            fixedKeys.push({ path: currentPath, from: frValue, to: translation });
            fixedCount++;
            console.log(`  ✅ ${currentPath}: "${frValue}" → "${translation}"`);
          }
        }
        
        // Récursion pour les objets imbriqués
        if (typeof frValue === 'object' && typeof enValue === 'object' && frValue !== null && enValue !== null) {
          fixInObject(frValue, enValue, currentPath);
        }
      }
    }
  }
  
  fixInObject(fr, en);
  
  // Sauvegarder les fichiers
  fs.writeFileSync(FR_PATH, JSON.stringify(fr, null, 2), 'utf8');
  fs.writeFileSync(EN_PATH, JSON.stringify(en, null, 2), 'utf8');
  
  console.log(`\n✅ Correction terminée ! ${fixedCount} traductions corrigées.`);
  
  // Sauvegarder le rapport des corrections
  const report = {
    timestamp: new Date().toISOString(),
    fixedCount,
    fixedKeys
  };
  
  fs.writeFileSync('scripts/translation-fixes-report.json', JSON.stringify(report, null, 2), 'utf8');
  console.log('📝 Rapport des corrections sauvegardé dans scripts/translation-fixes-report.json');
  
  return report;
}

// Exécution
try {
  const report = fixTranslations();
  
  console.log('\n🎉 Corrections automatiques terminées !');
  console.log('\n📋 Prochaines étapes :');
  console.log('1. Relancez la validation pour vérifier les améliorations');
  console.log('2. Corrigez manuellement les traductions restantes');
  console.log('3. Testez l\'application pour vérifier que tout fonctionne');
  
} catch (error) {
  console.error('❌ Erreur lors de la correction :', error.message);
  process.exit(1);
} 