#!/usr/bin/env python3
"""Localize dental test, logo demonstrations and registration notices."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"🦷 Test Diagramme Dentaire Adapté": "🦷 Responsive Dental Chart Test",
"Vérification de l'intégration du diagramme dentaire dans l'espace disponible": "Check that the dental chart fits in the available space",
"pour la sélectionner (elle doit devenir plus foncée)": "to select it (it should become darker)",
"Vérifiez que les numéros": "Check that the numbers",
"sont bien centrés dans chaque zone": "are centered in each area",
"Testez l'interactivité": "Test the interaction",
"en cliquant sur différentes dents": "by clicking different teeth",
"Utilisez les contrôles": "Use the controls",
"pour changer l'état des dents": "to change the tooth status",
"Dent sélectionnée :": "Selected tooth:",
"En attente de sélection": "Waiting for selection",
"🦷 Contrôles de la Dent": "🦷 Tooth Controls",
"État de la dent :": "Tooth status:",
"Hauteur adaptée :": "Adjusted height:",
"ViewBox adapté :": "Adjusted viewBox:",
"Ajustée pour la nouvelle taille": "Adjusted for the new size",
"Numéros :": "Numbers:",
"Repositionnés et redimensionnés (10px)": "Repositioned and resized (10 px)",
"Interactivité :": "Interactivity:",
"Améliorée avec feedback visuel": "Improved with visual feedback",
"Légende :": "Legend:",
"Repositionnée et redimensionnée": "Repositioned and resized",
"Fédération Tunisienne de Football - Ligue 1": "Tunisian Football Federation - Ligue 1",
"Tous les logos ont été générés automatiquement": "All logos were generated automatically",
"🎉 Résumé": "🎉 Summary",
"Générés": "Generated",
"Génération :": "Generation:",
"Icône 🏟️ si logo manquant": "🏟️ icon if logo is missing",
"Liens vers les sites officiels et logos réels": "Links to official websites and real logos",
"• Les logos générés automatiquement ne sont pas les vrais logos des clubs": "• Automatically generated logos are not the clubs' real logos",
"• Les vrais logos sont protégés par des droits d'auteur": "• Real logos are protected by copyright",
"Les logos affichés sur cette page sont des placeholders. Pour utiliser les vrais logos des clubs FTF, vous devez obtenir l'autorisation officielle de chaque club ou de la Fédération Tunisienne de Football.": "The logos on this page are placeholders. To use the real FTF club logos, you need official permission from each club or the Tunisian Football Federation.",
"🏆 Fédération Tunisienne de Football": "🏆 Tunisian Football Federation",
"⚠️ Photo modifiable même en mode lecture seule": "⚠️ Photo can be changed even in read-only mode",
'Un PCMA (Protocole de Concertation Médicale d\'Aptitude) valide et signé avec statut "cleared" est obligatoire pour soumettre la demande de licence.': 'A valid, signed PCMA assessment with "cleared" status is required to submit the licence request.',
"Un PCMA (Protocole de Concertation Médicale d'Aptitude) valide et signé est requis pour soumettre la demande de licence à l'association.": "A valid, signed PCMA assessment is required to submit the licence request to the association.",
'Enregistre les données de la demande dans l\'état "En cours" (brouillon)': 'Saves the request as "In progress" (draft)',
'Passe la demande à l\'état "Demande envoyée" et l\'envoie à l\'association pour validation': 'Changes the request to "Submitted" and sends it to the association for validation',
"Une fois envoyée, la demande ne peut plus être modifiée par le club.": "Once submitted, the club can no longer edit the request.",
}
paths = ('dental-chart-test.blade.php','logos-clubs-ftf.blade.php',
'logos-originaux-ftf.blade.php','modules/player-registration/create.blade.php')
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
used = {}
for name in paths:
    file = root / 'resources/views' / name
    source = file.read_text()
    count = [0]
    def replace(match):
        raw = match.group(1)
        label = ' '.join(raw.split())
        if label not in translations or any(marker in raw for marker in ('{{', '@', '[[')):
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
