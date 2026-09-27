#!/usr/bin/env python3
"""Localize reviewed clinical, medical record and team dashboard labels."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Système de support clinique basé sur Google Gemini": "Clinical support system powered by Google Gemini",
"Visites Médicales": "Medical Visits",
"Résultats de l'Analyse Clinique IA": "AI Clinical Analysis Results",
"Tous les paramètres cliniques sont normaux.": "All clinical parameters are normal.",
"📊 PCMA Récents avec Analyse Clinique": "📊 Recent PCMA Assessments with Clinical Analysis",
"📋 Détails": "📋 Details",
"Aucun PCMA récent": "No Recent PCMA Assessments",
"Aucun PCMA n'a été effectué récemment.": "No PCMA assessment has been performed recently.",
"🏥 Visites Médicales Récentes": "🏥 Recent Medical Visits",
"Aucune visite récente": "No Recent Visits",
"Aucune visite médicale n'a été enregistrée récemment.": "No medical visits have been recorded recently.",
"🩺 Détails du Dossier Médical": "🩺 Medical Record Details",
"Informations détaillées du dossier médical": "Detailed medical record information",
"Date de Création": "Creation Date",
"Modéré": "Moderate",
"Aucun antécédent médical significatif noté.": "No significant medical history noted.",
"Aucun médicament en cours.": "No current medications.",
"Conditions Spéciales": "Special Conditions",
"Aucune condition spéciale.": "No special conditions.",
"Prédictions IA": "AI Predictions",
"Évaluation basée sur les données de performance": "Assessment based on performance data",
"Exercices de prévention recommandés": "Preventive exercises recommended",
"✏️ Modifier le Dossier Médical": "✏️ Edit Medical Record",
"Modifier les informations du dossier médical": "Edit medical record information",
"Dashboard technique pour staffs d'équipe": "Technical Dashboard for Team Staff",
"Outil professionnel de gestion et d'analyse des équipes pour les staffs techniques": "Professional team management and analysis tool for technical staff",
"Données temps réel": "Real-time Data",
"Analytics avancées": "Advanced Analytics",
"Équipes Actives": "Active Teams",
"Répartition par Position": "Breakdown by Position",
"Taux de présence": "Attendance Rate",
"Intensité moyenne": "Average Intensity",
"État Physique": "Physical Condition",
"Joueurs blessés": "Injured Players",
"Buts encaissés": "Goals Conceded",
"Équipes Récentes": "Recent Teams",
}
paths = (
'modules/clinical/support-dashboard.blade.php',
'modules/healthcare/records/show.blade.php',
'modules/healthcare/records/edit.blade.php',
'team-portal/dashboard.blade.php',
)
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
