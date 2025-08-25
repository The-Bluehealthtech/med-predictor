const fs = require('fs');
const path = require('path');
const {TranslationServiceClient} = require('@google-cloud/translate').v3;

const projectId = 'TON_PROJECT_ID'; // <-- à adapter
const location = 'global';

const enPath = path.join('resources/js/i18n', 'en.json');
const frPath = path.join('resources/js/i18n', 'fr.json');

const en = JSON.parse(fs.readFileSync(enPath, 'utf8'));
const fr = fs.existsSync(frPath) ? JSON.parse(fs.readFileSync(frPath, 'utf8')) : {};

function isObject(val) {
  return val && typeof val === 'object' && !Array.isArray(val);
}

const translationClient = new TranslationServiceClient();

async function translateText(text) {
  const request = {
    parent: `projects/${projectId}/locations/${location}`,
    contents: [text],
    mimeType: 'text/plain',
    sourceLanguageCode: 'en',
    targetLanguageCode: 'fr',
  };
  const [response] = await translationClient.translateText(request);
  return response.translations[0].translatedText;
}

async function translateRecursive(enObj, frObj, prefix = '') {
  let changed = false;
  for (const key in enObj) {
    const enVal = enObj[key];
    if (isObject(enVal)) {
      if (!frObj[key]) frObj[key] = {};
      const subChanged = await translateRecursive(enVal, frObj[key], prefix + key + '.');
      if (subChanged) changed = true;
    } else {
      const frVal = frObj[key];
      if (!frVal || frVal === enVal) {
        try {
          const translated = await translateText(enVal);
          frObj[key] = translated;
          changed = true;
          console.log(`[${prefix + key}] \n  EN: ${enVal}\n  FR: ${translated}`);
        } catch (e) {
          console.error(`Erreur de traduction pour [${prefix + key}]:`, e.message);
        }
      }
    }
  }
  return changed;
}

(async () => {
  console.log('Traduction automatique des valeurs manquantes ou identiques (EN → FR) via Google Cloud Translate...');
  const changed = await translateRecursive(en, fr);
  if (changed) {
    fs.writeFileSync(frPath, JSON.stringify(fr, null, 2), 'utf8');
    console.log('\nMise à jour de fr.json terminée.');
  } else {
    console.log('Aucune traduction nécessaire.');
  }
})(); 