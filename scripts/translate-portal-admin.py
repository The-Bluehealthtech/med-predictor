#!/usr/bin/env python3
"""Localize player 360, club, association and system audit screens."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"SCORE DE SANTÉ FIT": "FIT HEALTH SCORE",
"VALEUR ESTIMÉE": "ESTIMATED VALUE",
"Marché & Tendances": "Market & Trends",
"Performance & Évolution": "Performance & Trends",
"Évolution du Score FIT": "FIT Score Trend",
"Répartition Santé": "Health Breakdown",
"Timeline de Carrière": "Career Timeline",
"Évolution Valeur Marchande": "Market Value Trend",
"Prédictions Futures": "Future Predictions",
"Aucun club associé": "No associated club",
"Données de Santé": "Health Data",
"Dossiers médicaux": "Medical Records",
"Évaluations PCMA": "PCMA Assessments",
"Évaluations de performance": "Performance Assessments",
"Aucune donnée de performance disponible": "No performance data available",
"Erreur d'accès :": "Access error:",
"Aucun joueur associé à votre compte.": "No player associated with your account.",
"Contactez l'administrateur pour résoudre ce problème.": "Contact the administrator to resolve this issue.",
"Détails du Club": "Club Details",
"Nom abrégé :": "Abbreviated name:",
"Fondé en": "Founded in",
"Confédération": "Confederation",
"Association affiliée": "Affiliated association",
"Équipes du club": "Club teams",
"Ajouter une équipe": "Add a team",
"Catégorie:": "Category:",
"Aucune équipe trouvée": "No teams found",
"Ce club n'a pas encore d'équipes enregistrées.": "This club has no registered teams yet.",
"Créer la première équipe": "Create the first team",
"Gestion de l'Association, validation et détection de fraude": "Association management, validation and fraud detection",
"Détecter": "Detect",
"Cas Résolus": "Resolved Cases",
"Taux de Détection": "Detection Rate",
"23 licences à valider": "23 licences to validate",
"Licences Validées": "Validated Licences",
"156 licences approuvées": "156 approved licences",
"Licences Rejetées": "Rejected Licences",
"8 licences rejetées": "8 rejected licences",
"🛡️ Détection de fraude indisponible": "🛡️ Fraud detection unavailable",
"← Retour à l'Audit Trail": "← Back to Audit Trail",
"Type d'événement": "Event Type",
"Aucun utilisateur associé (action système)": "No associated user (system action)",
"Informations Réseau": "Network Information",
"Méthode HTTP": "HTTP Method",
"Informations Modèle": "Model Information",
"Type de Modèle": "Model Type",
"ID du Modèle": "Model ID",
"Nom du Modèle": "Model Name",
"← Retour à l'Administration": "← Back to Administration",
"➕ Nouveau Paramètre": "➕ New Setting",
"⚡ Initialiser les Paramètres": "⚡ Initialize Settings",
"Groupes de Paramètres": "Setting Groups",
"Clé:": "Key:",
"Défaut:": "Default:",
}
paths = ('player-portal/player-360-simple.blade.php',
'modules/clubs/show.blade.php', 'modules/association/index.blade.php',
'admin/audit-trail/show.blade.php', 'admin/system-settings/index.blade.php')
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
