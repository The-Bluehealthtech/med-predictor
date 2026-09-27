const { execFileSync } = require('child_process');
const fs = require('fs');

function run(label, args, env = {}) {
  try {
    const output = execFileSync(args[0], args.slice(1), {
      encoding: 'utf8',
      env: { ...process.env, ...env },
    }).trim();
    console.log('✅ ' + label + ': ' + output);
  } catch (error) {
    console.error('❌ ' + label + ': ' + (error.stderr || error.message).toString().trim());
    process.exitCode = 1;
  }
}

console.log('🏥 Test des traductions Healthcare');
for (const file of ['resources/lang/fr/healthcare.php', 'resources/lang/en/healthcare.php']) {
  if (!fs.existsSync(file)) {
    console.error('❌ Fichier manquant: ' + file);
    process.exitCode = 1;
  } else {
    console.log('✅ Fichier trouvé: ' + file);
  }
}

run('FR healthcare_management', ['php', 'artisan', 'tinker', '--execute', "echo __('healthcare.medical_predictions');"], { APP_LOCALE: 'fr' });
run('EN healthcare_management', ['php', 'artisan', 'tinker', '--execute', "App::setLocale('en'); echo __('healthcare.medical_predictions');"]);
run('FR clés principales', ['php', 'artisan', 'tinker', '--execute', "App::setLocale('fr'); echo __('healthcare.predictions').'|'.__('healthcare.actions').'|'.__('healthcare.medical_predictions');"]);

if (!process.exitCode) console.log('✅ Test healthcare terminé');
