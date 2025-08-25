// scripts/merge-i18n-keys.js
const fs = require('fs');
const path = require('path');

function mergeUnderAuto(mainPath, autoPath) {
  const main = fs.existsSync(mainPath) ? JSON.parse(fs.readFileSync(mainPath, 'utf8')) : {};
  const auto = fs.existsSync(autoPath) ? JSON.parse(fs.readFileSync(autoPath, 'utf8')) : {};
  if (!Object.keys(auto).length) {
    console.log(`Aucune clé à fusionner depuis ${autoPath}`);
    return [];
  }
  if (!main.auto) main.auto = {};
  const conflicts = [];
  for (const key in auto) {
    if (Object.prototype.hasOwnProperty.call(main.auto, key)) {
      if (main.auto[key] !== auto[key]) {
        conflicts.push(key);
        // On garde la valeur existante
      }
    } else {
      main.auto[key] = auto[key];
    }
  }
  fs.writeFileSync(mainPath, JSON.stringify(main, null, 2), 'utf8');
  return conflicts;
}

const folder = 'resources/js/i18n';
const mainFr = path.join(folder, 'fr.json');
const mainEn = path.join(folder, 'en.json');
const autoFr = path.join(folder, 'auto_i18n_fr.json');
const autoEn = path.join(folder, 'auto_i18n_en.json');

let allConflicts = [];
let anyFusion = false;

if (fs.existsSync(autoFr)) {
  anyFusion = true;
  const conflicts = mergeUnderAuto(mainFr, autoFr);
  if (conflicts.length) {
    console.log(`Conflits détectés dans ${mainFr} (section 'auto') :\n`, conflicts);
    allConflicts = allConflicts.concat(conflicts.map(c => `${mainFr}: auto.${c}`));
  } else {
    console.log(`Fusion réussie pour ${mainFr} (section 'auto')`);
  }
} else {
  console.log(`Fichier auto non trouvé : ${autoFr}`);
}

if (fs.existsSync(autoEn)) {
  anyFusion = true;
  const conflicts = mergeUnderAuto(mainEn, autoEn);
  if (conflicts.length) {
    console.log(`Conflits détectés dans ${mainEn} (section 'auto') :\n`, conflicts);
    allConflicts = allConflicts.concat(conflicts.map(c => `${mainEn}: auto.${c}`));
  } else {
    console.log(`Fusion réussie pour ${mainEn} (section 'auto')`);
  }
} else {
  console.log(`Fichier auto non trouvé : ${autoEn}`);
}

if (!anyFusion) {
  console.log('\nAucune fusion effectuée : aucun fichier auto_i18n trouvé dans le dossier resources/js/i18n/.');
} else if (allConflicts.length) {
  console.log('\nAttention : des conflits ont été détectés. Les valeurs existantes ont été conservées.');
} else {
  console.log('\nFusion terminée sans conflit.');
} 