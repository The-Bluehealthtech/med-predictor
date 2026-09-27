#!/usr/bin/env python3
"""Localize player portal chart labels while preserving numerical series."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
labels = {
"Physique": "Physical", "Technique": "Technical",
"Tactique": "Tactical", "Mental": "Mental", "Social": "Social",
"Matchs": "Matches", "Minutes": "Minutes", "Buts": "Goals",
"Passes": "Assists", "Jaunes": "Yellow Cards", "Rouges": "Red Cards",
"Statistiques de saison": "Season Statistics",
"Minutes jouées": "Minutes Played",
"Environnement": "Environment",
"Soutien Social": "Social Support",
"Accès Soins": "Access to Healthcare",
"Situation Financière": "Financial Situation",
"Blessures": "Injuries", "Maladies": "Diseases",
"Chirurgies": "Surgeries", "Rééducation": "Rehabilitation",
"Légère": "Mild", "Modérée": "Moderate",
"Grave": "Severe", "Critique": "Critical",
}
file = root / 'resources/views/test-portail-joueur-simple.blade.php'
source = file.read_text()
count = [0]
def convert(script):
    content = script.group(2)
    for french in labels:
        before = "'" + french + "'"
        if before in content:
            count[0] += content.count(before)
            content = content.replace(before, "@json(__('" + french.replace("'", "\\'") + "'))")
    return script.group(1) + content + script.group(3)
source = re.sub(r'(?is)(<script\b[^>]*>)(.*?)(</script>)', convert, source)
file.write_text(source)
print('Chart labels localized:', count[0])

en_file = root / 'resources/lang/en.json'
original = en_file.read_text()
known = json.loads(original)
new = {key: value for key, value in labels.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = original.rfind('}')
    en_file.write_text(original[:pos].rstrip() + ',\n' + items + '\n' + original[pos:])
print('New translations:', len(new))
