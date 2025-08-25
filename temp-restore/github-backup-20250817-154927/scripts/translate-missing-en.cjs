const fs = require('fs');
const path = require('path');
const https = require('https');

const DEEPL_API_KEY = process.env.DEEPL_API_KEY;
if (!DEEPL_API_KEY) {
  console.error('Veuillez définir la variable d\'environnement DEEPL_API_KEY');
  process.exit(1);
}

const frPath = path.join('resources/js/i18n', 'fr.json');
const enPath = path.join('resources/js/i18n', 'en.json');

const fr = JSON.parse(fs.readFileSync(frPath, 'utf8'));
const en = fs.existsSync(enPath) ? JSON.parse(fs.readFileSync(enPath, 'utf8')) : {};

function isObject(val) {
  return val && typeof val === 'object' && !Array.isArray(val);
}

async function translateText(text) {
  return new Promise((resolve, reject) => {
    const data = new URLSearchParams({
      auth_key: DEEPL_API_KEY,
      text,
      source_lang: 'FR',
      target_lang: 'EN',
    }).toString();
    const options = {
      hostname: 'api-free.deepl.com',
      path: '/v2/translate',
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Length': data.length,
      },
    };
    const req = https.request(options, (res) => {
      let body = '';
      res.on('data', (chunk) => (body += chunk));
      res.on('end', () => {
        try {
          const json = JSON.parse(body);
          if (json.translations && json.translations[0] && json.translations[0].text) {
            resolve(json.translations[0].text);
          } else {
            reject(json);
          }
        } catch (e) {
          reject(e);
        }
      });
    });
    req.on('error', reject);
    req.write(data);
    req.end();
  });
}

async function translateRecursive(frObj, enObj, prefix = '') {
  let changed = false;
  for (const key in frObj) {
    const frVal = frObj[key];
    if (isObject(frVal)) {
      if (!enObj[key]) enObj[key] = {};
      const subChanged = await translateRecursive(frVal, enObj[key], prefix + key + '.');
      if (subChanged) changed = true;
    } else {
      const enVal = enObj[key];
      if (!enVal || enVal === frVal) {
        const translated = await translateText(frVal);
        enObj[key] = translated;
        changed = true;
        console.log(`[${prefix + key}] \n  FR: ${frVal}\n  EN: ${translated}`);
        // Petite pause pour éviter le rate limit
        await new Promise((r) => setTimeout(r, 200));
      }
    }
  }
  return changed;
}

(async () => {
  console.log('Traduction automatique des valeurs manquantes ou identiques (FR → EN) via DeepL...');
  const changed = await translateRecursive(fr, en);
  if (changed) {
    fs.writeFileSync(enPath, JSON.stringify(en, null, 2), 'utf8');
    console.log('\nMise à jour de en.json terminée.');
  } else {
    console.log('Aucune traduction nécessaire.');
  }
})(); 