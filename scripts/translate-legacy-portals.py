#!/usr/bin/env python3
"""Translate retained legacy player portal templates."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"← Précédent": "← Previous",
"📷 Gérer": "📷 Manage", "🏳️ Gérer": "🏳️ Manage",
"🏟️ Gérer": "🏟️ Manage", "🏆 Gérer": "🏆 Manage",
"🏴 Gérer": "🏴 Manage", "🏆 Palmarès": "🏆 Honours",
"N/A% complétée": "N/A% complete", "Performances récentes": "Recent Performances",
"MODÉRÉ": "MODERATE", "Disponibilité": "Availability",
"✅ LIMITÉ": "✅ LIMITED", "❤️ Santé & Bien-être": "❤️ Health & Well-being",
"🏥 Médical": "🏥 Medical",
"Données dynamiques basées sur vos vraies statistiques FIFA": "Dynamic data based on your real FIFA statistics",
"Précision des tirs": "Shot Accuracy",
"Statistiques Défensives": "Defensive Statistics",
"Tacles réussis": "Successful Tackles",
"Dégagements": "Clearances",
"Duels gagnés": "Duels Won",
"Évolution des Performances": "Performance Trends",
"Radar des Compétences": "Skills Radar",
"Performances par Match (Données FIFA)": "Performance by Match (FIFA Data)",
"Passes réussies": "Successful Passes",
"Statistiques Avancées": "Advanced Statistics",
"Module en cours de développement - Intégration des composants Vue.js en cours": "Module under development - Vue.js component integration in progress",
"Défi de vitesse disponible - Améliorez votre sprint !": "Speed challenge available - Improve your sprint!",
"Félicitations ! Vous avez amélioré votre endurance de 5 points.": "Congratulations! Your endurance improved by 5 points.",
"Rappel Entraînement": "Training Reminder",
"N'oubliez pas votre session d'entraînement technique aujourd'hui.": "Don't forget your technical training session today.",
"Non renseigné": "Not provided", "Énergie": "Energy",
"Répartition des Charges": "Training Load Breakdown",
"Mes Devices Connectés": "My Connected Devices",
"Appareil non renseigné": "Device not specified",
"État de connexion indisponible": "Connection status unavailable",
"État de connexion et batterie indisponibles": "Connection and battery status unavailable",
"Contrôles Anti-Dopage": "Anti-Doping Controls",
"Dernier Contrôle": "Last Control",
"15/01/2025 - Résultat : Négatif": "15/01/2025 - Result: Negative",
"Prochain Contrôle": "Next Control",
"15/02/2025 - Contrôle programmé": "15/02/2025 - Control scheduled",
"Statistiques Détaillées": "Detailed Statistics",
"Connecté • 85% batterie": "Connected • 85% battery",
"Connecté • 92% batterie": "Connected • 92% battery",
}
paths = ('portail-joueur-final-corrige-dynamique.blade.php',
         'portail-joueur-FONCTIONNEL-DRAPEAUX-OK.blade.php')
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
used = {}
for name in paths:
    file = root / 'resources/views' / name
    source = file.read_text()
    count = [0]
    def replace(match):
        label = ' '.join(match.group(1).split())
        if label not in translations or '{{' in match.group(1) or '@' in match.group(1):
            return match.group(0)
        count[0] += 1
        used[label] = translations[label]
        return '>' + "{{ __('" + label.replace("'", "\\'") + "') }}" + '<'
    file.write_text(re.sub(r'>([^<>]+)<', replace, source))
    print(name, count[0])
new = {key: value for key, value in used.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = old.rfind('}')
    en_file.write_text(old[:pos].rstrip() + ',\n' + items + '\n' + old[pos:])
print('New English translations:', len(new))
