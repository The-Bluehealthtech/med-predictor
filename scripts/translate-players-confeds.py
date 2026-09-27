#!/usr/bin/env python3
"""Localize player editor and confederation screens."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Algérie": "Algeria", "Égypte": "Egypt", "États-Unis": "United States",
"Brésil": "Brazil", "Sénégal": "Senegal", "Côte d'Ivoire": "Ivory Coast",
"Milieu défensif (CDM)": "Defensive Midfielder (CDM)",
"Défenseur central (CB)": "Centre Back (CB)",
"Arrière droit (RB)": "Right Back (RB)",
"Arrière gauche (LB)": "Left Back (LB)",
"Formats acceptés: JPG, PNG, GIF. Taille max: 5MB": "Accepted formats: JPG, PNG, GIF. Maximum size: 5 MB",
"Photo actuelle / Aperçu": "Current Photo / Preview",
"Mettre à jour": "Update",
"Pied préféré": "Preferred Foot",
"Réputation internationale (1-5)": "International Reputation (1-5)",
"Aperçu de la photo": "Photo Preview",
"Retour aux confédérations": "Back to Confederations",
"Détails de la Confédération": "Confederation Details",
"Associations affiliées": "Affiliated Associations",
"Année de fondation": "Year Founded",
"Dernière sync:": "Last sync:",
"Voir toutes les associations de cette confédération": "View all associations in this confederation",
"Aucune association affiliée": "No affiliated associations",
"Cette confédération n'a pas encore d'associations affiliées.": "This confederation has no affiliated associations yet.",
"🏛️ Gérer les associations": "🏛️ Manage Associations",
"📋 Licences de la confédération": "📋 Confederation Licences",
"Retour aux détails": "Back to Details",
"Modifier la confédération": "Edit Confederation",
"🏠 Retour à la liste": "🏠 Back to List",
"Nom de la confédération *": "Confederation Name *",
"Nom abrégé *": "Abbreviated Name *",
"Logo de la confédération": "Confederation Logo",
"Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)": "Accepted formats: JPEG, PNG, JPG, GIF (max 2 MB)",
"Dernière sync :": "Last sync:",
"Dernière erreur :": "Last error:",
"Confédérations FIFA - Plateforme FIT": "FIFA Confederations - FIT Platform",
"🌍 Confédérations FIFA": "🌍 FIFA Confederations",
"Gestion des confédérations continentales et internationales": "Manage continental and international confederations",
"+ Ajouter une confédération": "+ Add a Confederation",
"Hiérarchie FIFA :": "FIFA hierarchy:",
"Confédération → Association → Club → Équipe → Joueur": "Confederation → Association → Club → Team → Player",
"👁️ Voir détails": "👁️ View Details",
"Confédérations": "Confederations",
"Synchronisées FIFA": "Synchronized with FIFA",
}
files = (
'players/edit.blade.php', 'players/create.blade.php',
'modules/confederations/show.blade.php', 'modules/confederations/edit.blade.php',
'modules/confederations/index.blade.php',
)
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
used = {}
for name in files:
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
