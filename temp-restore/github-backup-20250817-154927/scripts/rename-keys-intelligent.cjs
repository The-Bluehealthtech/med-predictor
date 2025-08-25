const fs = require('fs');
const path = require('path');

// Configuration
const FR_PATH = 'resources/js/i18n/fr.json';
const EN_PATH = 'resources/js/i18n/en.json';

// Patterns de renommage intelligents
const RENAMING_PATTERNS = {
  // RPM Patterns
  'rpm.key1': 'rpm.newSession',
  'rpm.key3': 'rpm.debugBanner',
  'rpm.key5': 'rpm.dashboard',
  'rpm.key6': 'rpm.welcomeMessage',
  'rpm.key7': 'rpm.sessionsList',
  'rpm.key8': 'rpm.sessionsListComponent',
  'rpm.key9': 'rpm.sessionDetail',
  'rpm.key10': 'rpm.sessionDetailComponent',
  'rpm.key11': 'rpm.sessionAttendance',
  'rpm.key12': 'rpm.sessionAttendanceComponent',
  'rpm.key13': 'rpm.sync',
  'rpm.key14': 'rpm.syncComponent',
  'rpm.key15': 'rpm.settings',
  'rpm.key16': 'rpm.settingsComponent',
  'rpm.key17': 'rpm.reports',
  'rpm.key18': 'rpm.reportsComponent',
  'rpm.key19': 'rpm.playerLoadDetail',
  'rpm.key20': 'rpm.playerLoadDetailComponent',
  'rpm.key21': 'rpm.matchesList',
  'rpm.key22': 'rpm.matchesListComponent',
  'rpm.key23': 'rpm.matchDetail',
  'rpm.key24': 'rpm.matchDetailComponent',
  'rpm.key25': 'rpm.portal',
  'rpm.key26': 'rpm.trainingCalendar',
  'rpm.key27': 'rpm.sessionEditor',
  'rpm.key28': 'rpm.playerLoadMonitor',
  'rpm.key29': 'rpm.matchPreparation',
  'rpm.key30': 'rpm.attendanceTracker',
  'rpm.key91': 'rpm.trainingSessions',
  'rpm.key113': 'rpm.trainingSessionsCount',

  // DTN Patterns
  'dtn.key33': 'dtn.selectionPlayers',
  'dtn.key34': 'dtn.selectionPlayersComponent',
  'dtn.key35': 'dtn.selectionForm',
  'dtn.key36': 'dtn.selectionFormComponent',
  'dtn.key37': 'dtn.selectionDetail',
  'dtn.key38': 'dtn.selectionDetailComponent',
  'dtn.key39': 'dtn.newSelection',
  'dtn.key40': 'dtn.exportFifa',
  'dtn.key41': 'dtn.team',
  'dtn.key42': 'dtn.allTeams',
  'dtn.key43': 'dtn.competition',
  'dtn.key44': 'dtn.allCompetitions',
  'dtn.key45': 'dtn.africaCup',
  'dtn.key46': 'dtn.friendlyMatch',
  'dtn.key47': 'dtn.qualifications',
  'dtn.key48': 'dtn.status',
  'dtn.key49': 'dtn.allStatuses',
  'dtn.key50': 'dtn.draft',
  'dtn.key51': 'dtn.published',
  'dtn.key52': 'dtn.confirmed',
  'dtn.key53': 'dtn.completed',
  'dtn.key54': 'dtn.date',
  'dtn.key55': 'dtn.dashboard',
  'dtn.key56': 'dtn.technicalPlanning',
  'dtn.key57': 'dtn.nationalTeams',
  'dtn.key58': 'dtn.medicalClubInterface',
  'dtn.key59': 'dtn.internationalSelections',
  'dtn.key60': 'dtn.expatriateClubSync',
  'dtn.key292': 'dtn.nationality',
  'dtn.key392': 'dtn.nationalityLabel',

  // Referee Patterns
  'referee.key61': 'referee.debugBanner',
  'referee.key62': 'referee.debugMessage',
  'referee.key63': 'referee.myMatches',
  'referee.key64': 'referee.loading',
  'referee.key65': 'referee.date',
  'referee.key66': 'referee.competition',
  'referee.key67': 'referee.home',
  'referee.key68': 'referee.away',
  'referee.key69': 'referee.status',
  'referee.key70': 'referee.action',
  'referee.key71': 'referee.soccerBall',
  'referee.key72': 'referee.ghsScore',
  'referee.key73': 'referee.chart',
  'referee.key74': 'referee.injuryRisk',
  'referee.key75': 'referee.trend',
  'referee.key76': 'referee.contribution',
  'referee.key77': 'referee.clipboard',
  'referee.key78': 'referee.status',
  'referee.key79': 'referee.active',
  'referee.key124': 'referee.matchSheet',
  'referee.key133': 'referee.matchEvents',
  'referee.key314': 'referee.tournamentManagement',
  'referee.key447': 'referee.selectPlayers',
  'referee.key454': 'referee.loadingMatch',
  'referee.key478': 'referee.matchInterval',
  'referee.key494': 'referee.currentStandings',
  'referee.key510': 'referee.manageSchedule',
  'referee.key519': 'referee.allMatches',

  // Player Patterns
  'player.key19': 'player.loadDetail',
  'player.key20': 'player.loadDetailComponent',
  'player.key80': 'player.profilePicture',
  'player.key81': 'player.personalInfo',
  'player.key82': 'player.name',
  'player.key83': 'player.fifaId',
  'player.key84': 'player.position',
  'player.key85': 'player.club',
  'player.key86': 'player.licenseStatus',
  'player.key87': 'player.status',
  'player.key88': 'player.expires',
  'player.key89': 'player.careerStats',
  'player.key90': 'player.matches',
  'player.key91': 'player.trainingSessions',
  'player.key92': 'player.healthRecords',
  'player.key93': 'player.ghsBreakdown',
  'player.key94': 'player.physical',
  'player.key95': 'player.mental',
  'player.key96': 'player.civic',
  'player.key97': 'player.fitnessData',
  'player.key98': 'player.sleepScore',
  'player.key99': 'player.riskScore',
  'player.key140': 'player.management',
  'player.key143': 'player.player',
  'player.key214': 'player.playersOnly',
  'player.key218': 'player.totalPlayers',
  'player.key229': 'player.profile',
  'player.key246': 'player.joueur',
  'player.key261': 'player.totalPlayersCount',
  'player.key286': 'player.addNew',
  'player.key335': 'player.joueurFr',
  'player.key337': 'player.joueurFr2',
  'player.key347': 'player.joueurRequired',
  'player.key373': 'player.eligiblePlayers',
  'player.key378': 'player.joueurFr3',
  'player.key396': 'player.management',
  'player.key429': 'player.joueurFr4',
  'player.key448': 'player.availablePlayers',

  // Common Patterns
  'common.key2': 'common.exportPerformance',
  'common.key4': 'common.debugMessage',
  'common.key21': 'common.matchesList',
  'common.key22': 'common.matchesListComponent',
  'common.key23': 'common.matchDetail',
  'common.key24': 'common.matchDetailComponent',
  'common.key31': 'common.teamDetail',
  'common.key32': 'common.teamDetailComponent',
  'common.key33': 'common.selectionPlayers',
  'common.key34': 'common.selectionPlayersComponent',
  'common.key35': 'common.selectionForm',
  'common.key36': 'common.selectionFormComponent',
  'common.key37': 'common.selectionDetail',
  'common.key38': 'common.selectionDetailComponent',
  'common.key39': 'common.newSelection',
  'common.key40': 'common.exportFifa',
  'common.key41': 'common.team',
  'common.key42': 'common.allTeams',
  'common.key43': 'common.competition',
  'common.key44': 'common.allCompetitions',
  'common.key45': 'common.africaCup',
  'common.key46': 'common.friendlyMatch',
  'common.key47': 'common.qualifications',
  'common.key48': 'common.status',
  'common.key49': 'common.allStatuses',
  'common.key50': 'common.draft',
  'common.key51': 'common.published',
  'common.key52': 'common.confirmed',
  'common.key53': 'common.completed',
  'common.key54': 'common.date',
  'common.key55': 'common.dashboard',
  'common.key56': 'common.technicalPlanning',
  'common.key57': 'common.nationalTeams',
  'common.key58': 'common.medicalClubInterface',
  'common.key59': 'common.internationalSelections',
  'common.key60': 'common.expatriateClubSync',
  'common.key61': 'common.debugBanner',
  'common.key62': 'common.debugMessage',
  'common.key63': 'common.myMatches',
  'common.key64': 'common.loading',
  'common.key65': 'common.date',
  'common.key66': 'common.competition',
  'common.key67': 'common.home',
  'common.key68': 'common.away',
  'common.key69': 'common.status',
  'common.key70': 'common.action',
  'common.key71': 'common.soccerBall',
  'common.key72': 'common.ghsScore',
  'common.key73': 'common.chart',
  'common.key74': 'common.injuryRisk',
  'common.key75': 'common.trend',
  'common.key76': 'common.contribution',
  'common.key77': 'common.clipboard',
  'common.key78': 'common.status',
  'common.key79': 'common.active'
};

function renameKeys() {
  console.log('🔄 Début du renommage intelligent des clés...');
  
  // Charger les fichiers
  const fr = JSON.parse(fs.readFileSync(FR_PATH, 'utf8'));
  const en = JSON.parse(fs.readFileSync(EN_PATH, 'utf8'));
  
  let renamedCount = 0;
  const renamedKeys = {};
  
  // Fonction pour renommer récursivement
  function renameInObject(obj, category) {
    const newObj = {};
    
    for (const key in obj) {
      const newKey = RENAMING_PATTERNS[key] || key;
      
      if (newKey !== key) {
        renamedKeys[key] = newKey;
        renamedCount++;
        console.log(`  ✅ ${key} → ${newKey}`);
      }
      
      if (typeof obj[key] === 'object' && obj[key] !== null) {
        newObj[newKey] = renameInObject(obj[key], category);
      } else {
        newObj[newKey] = obj[key];
      }
    }
    
    return newObj;
  }
  
  // Renommer dans chaque catégorie
  for (const category of ['rpm', 'dtn', 'referee', 'player', 'common']) {
    if (fr[category]) {
      fr[category] = renameInObject(fr[category], category);
    }
    if (en[category]) {
      en[category] = renameInObject(en[category], category);
    }
  }
  
  // Sauvegarder les fichiers
  fs.writeFileSync(FR_PATH, JSON.stringify(fr, null, 2), 'utf8');
  fs.writeFileSync(EN_PATH, JSON.stringify(en, null, 2), 'utf8');
  
  console.log(`\n✅ Renommage terminé ! ${renamedCount} clés renommées.`);
  
  // Sauvegarder le mapping des renommages
  fs.writeFileSync('scripts/renamed-keys-mapping.json', JSON.stringify(renamedKeys, null, 2), 'utf8');
  console.log('📝 Mapping des renommages sauvegardé dans scripts/renamed-keys-mapping.json');
  
  return renamedKeys;
}

// Exécution
try {
  const renamedKeys = renameKeys();
  
  console.log('\n🎉 Renommage intelligent terminé avec succès !');
  console.log('\n📋 Prochaines étapes :');
  console.log('1. Vérifiez que les nouvelles clés sont plus descriptives');
  console.log('2. Testez l\'application pour vérifier que tout fonctionne');
  console.log('3. Mettez à jour les composants Vue.js si nécessaire');
  console.log('4. Corrigez manuellement les traductions restantes');
  
} catch (error) {
  console.error('❌ Erreur lors du renommage :', error.message);
  process.exit(1);
} 