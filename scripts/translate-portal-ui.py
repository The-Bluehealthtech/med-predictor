#!/usr/bin/env python3
"""Translate remaining static player portal labels without changing data sources."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"🌍 Confédération:": "🌍 Confederation:",
"Aucun snapshot antérieur comparable": "No comparable earlier snapshot",
"Le Score FIT nécessite les cinq axes vérifiés.": "The FIT Score requires all five verified dimensions.",
"Aucune métrique de performance enregistrée pour ce joueur.": "No performance metrics recorded for this player.",
"Aucune métrique de performance sur les 30 derniers jours.": "No performance metrics in the last 30 days.",
"Aucune métrique vérifiée dans l'historique disponible.": "No verified metrics in the available history.",
"Aucun snapshot FIT enregistré pour ces données.": "No FIT snapshot recorded for this data.",
"Document d'identité:": "Identity document:",
"Aucune évaluation de performance enregistrée.": "No performance assessment recorded.",
"📊 Statistiques avancées": "📊 Advanced Statistics",
"Tacles gagnés": "Successful tackles",
"Tirs cadrés": "Shots on target",
"Précision des passes": "Pass accuracy",
"Évolution & Tendances (dernières mesures)": "Changes & Trends (latest measurements)",
"Qualité logement, stabilité": "Housing quality and stability",
"Rapidité, disponibilité": "Speed and availability",
"Stabilité économique": "Economic stability",
"Niveau académique, compétences": "Education and skills",
"Données SDOH non disponibles": "SDOH data unavailable",
"Dernière zone blessée:": "Most recently injured area:",
"Alertes médicales:": "Medical alerts:",
"Temps de Récupération": "Recovery Time",
"Qualité de Récupération": "Recovery Quality",
"Réserve Cardiaque": "Heart Rate Reserve",
"Métabolisme de Base": "Basal Metabolic Rate",
"Répartition par type": "Breakdown by Type",
"Répartition par gravité": "Breakdown by Severity",
"Bien-être": "Well-being",
"Résultat individuel non enregistré": "Individual result not recorded",
"Aucune substance interdite enregistrée": "No prohibited substances recorded",
"Période:": "Period:",
"Autorité d'approbation:": "Approving authority:",
"Non vérifiée": "Not verified",
"Période de formation:": "Training period:",
"Prime simulée de test:": "Simulated test training compensation:",
}
file = root / 'resources/views/test-portail-joueur-simple.blade.php'
source = file.read_text()
count = [0]
def replace(match):
    original = match.group(1)
    label = ' '.join(original.split())
    if label not in translations or '{{' in original or '@' in original:
        return match.group(0)
    count[0] += 1
    return '>' + "{{ __('" + label.replace("'", "\\'") + "') }}" + '<'
source = re.sub(r'>([^<>]+)<', replace, source)
file.write_text(source)
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
new = {key: value for key, value in translations.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = old.rfind('}')
    en_file.write_text(old[:pos].rstrip() + ',\n' + items + '\n' + old[pos:])
print('Portal replacements:', count[0], 'new English translations:', len(new))
