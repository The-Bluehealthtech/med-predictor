#!/usr/bin/env python3
"""Localize DTN, analytics, FIFA and association portal labels."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Pilotage technique basé sur les données du périmètre association autorisé.": "Technical oversight based on data within the authorized association scope.",
"Effectifs et profils du périmètre autorisé.": "Squads and profiles within the authorized scope.",
"👥 Équipes": "👥 Teams",
"Organisation technique des équipes.": "Technical organization of teams.",
"🏆 Compétitions": "🏆 Competitions",
"Compétitions réellement enregistrées.": "Competitions actually recorded.",
"Analyse des performances enregistrées.": "Analysis of recorded performances.",
"Dernières évaluations de performance": "Latest Performance Assessments",
"Aucune performance enregistrée dans le périmètre autorisé.": "No performances recorded within the authorized scope.",
"🔄 Digital Twin — scénario what-if": "🔄 Digital Twin — What-if Scenario",
"Simulation mathématique basée sur la dernière performance réellement enregistrée.": "Mathematical simulation based on the latest recorded performance.",
"Les résultats ci-dessous sont des": "The results below are",
"scénarios simulés": "simulated scenarios",
", pas des mesures, pas un diagnostic médical et pas le score FIT canonique. L'ajustement applique simplement le pourcentage choisi aux scores disponibles.": ", not measurements, a medical diagnosis or the canonical FIT Score. The adjustment simply applies the selected percentage to available scores.",
"Ajustement du scénario (%)": "Scenario Adjustment (%)",
"Plage autorisée : -20 % à +20 %.": "Allowed range: -20% to +20%.",
"Calculer le scénario": "Calculate Scenario",
"Aucune performance réelle n'est disponible pour ce joueur. Aucun scénario n'est généré.": "No recorded performance is available for this player. No scenario is generated.",
"Enregistrement avec détection de fraude GPT-4": "Registration with GPT-4 Fraud Detection",
"Association Régionale": "Regional Association",
"Téléphone de Contact *": "Contact Phone *",
"Sélectionner un pays": "Select a Country",
"Formats acceptés: JPEG, PNG, JPG. Taille max: 2MB": "Accepted formats: JPEG, PNG, JPG. Maximum size: 2 MB",
"🛡️ Détection de Fraude GPT-4": "🛡️ GPT-4 Fraud Detection",
"🔍 Activer la Détection": "🔍 Enable Detection",
"Résultats de l'Analyse GPT-4": "GPT-4 Analysis Results",
"GPT-4 Intégré": "GPT-4 Integrated",
"Portail Athlète": "Athlete Portal",
"Accès mobile aux dossiers médicaux et formulaires de bien-être": "Mobile access to medical records and well-being forms",
"Santé Générale": "General Health",
"Appareils Connectés": "Connected Devices",
"Formulaire Bien-être": "Well-being Form",
"Formulaire de bien-être soumis": "Well-being form submitted",
"Rendez-vous médical confirmé": "Medical appointment confirmed",
"Demain à 14h00": "Tomorrow at 2:00 PM",
"Planification et suivi des consultations médicales": "Schedule and track medical consultations",
"Tous les médecins": "All Doctors",
"26 Août 2025": "26 August 2025",
"Médecine sportive": "Sports Medicine",
"résultats": "results",
"Confirmés": "Confirmed",
"Annulés": "Cancelled",
"📚 Ressource recommandée : Flag Icons": "📚 Recommended Resource: Flag Icons",
"Pour gérer les drapeaux de nationalité et d'association, nous recommandons d'utiliser le projet": "To manage nationality and association flags, we recommend using the project",
"qui fournit une collection complète de drapeaux SVG de tous les pays.": "which provides a complete set of SVG flags for every country.",
"🎏 Voir la démo": "🎏 View Demo",
"Sélectionner une image": "Select an Image",
"🔗 Mettre à jour": "🔗 Update",
"🎨 Générer un avatar": "🎨 Generate an Avatar",
"Générez automatiquement un avatar basé sur le nom du joueur en utilisant l'API DiceBear.": "Automatically generate an avatar based on the player's name using the DiceBear API.",
"🎨 Générer Avatar": "🎨 Generate Avatar",
"Récents (30j)": "Recent (30d)",
"Cliquez sur un joueur pour accéder à son portail": "Click a player to open their portal",
"joueurs trouvés": "players found",
"Base de données FIT:": "FIT database:",
"Dernière mise à jour FIT:": "Last FIT update:",
'• Cliquez sur "FIT Portal" pour accéder au portail FIT du joueur': '• Click "FIT Portal" to open the player\'s FIT portal',
"• Utilisez la barre de navigation pour passer d'un joueur à l'autre": "• Use the navigation bar to switch between players",
"• Les données sont maintenant 100% dynamiques": "• Data is now fully dynamic",
"Données locales traçables et état de l'intégration FIFA Connect": "Traceable local data and FIFA Connect integration status",
"Simulation explicite — non connectée": "Explicit simulation — not connected",
"Connexion live vérifiée": "Live connection verified",
"Dernière vérification": "Last verification",
"Aucun joueur n'est disponible dans le périmètre autorisé.": "No player is available within the authorized scope.",
"Les valeurs ci-dessous proviennent de l'enregistrement local du joueur. Aucun fallback numérique n'est généré.": "The values below come from the player's local record. No numeric fallback is generated.",
"Synchronisation FIFA Connect indisponible : aucun FIFA Connect ID n'est associé à ce joueur.": "FIFA Connect synchronization unavailable: no FIFA Connect ID is associated with this player.",
"Synchronisation live non exécutée : l'API FIFA Connect n'est pas actuellement connectée.": "Live synchronization not run: the FIFA Connect API is not currently connected.",
"Analyse avancée et insights": "Advanced Analysis and Insights",
"Analyse avancée, tendances et alertes de performance": "Advanced analysis, trends and performance alerts",
"Données en temps réel": "Real-time Data",
"Analyse des performances des athlètes": "Athlete Performance Analysis",
"Suivi de la santé et bien-être": "Health and Well-being Monitoring",
"Analyse des sessions d'entraînement": "Training Session Analysis",
"Évolution des performances": "Performance Trends",
"Temps de récupération": "Recovery Time",
"Analyse détaillée des performances des joueurs": "Detailed Player Performance Analysis",
"Détail des Performances": "Performance Details",
"Aucun joueur trouvé avec les filtres appliqués": "No players found with the selected filters",
}
paths = (
'dtn/index-canonical.blade.php', 'analytics/digital-twin-canonical.blade.php',
'modules/association/registration.blade.php', 'modules/portal/dashboard.blade.php',
'modules/appointments/index.blade.php', 'player/photo/upload.blade.php',
'admin/dashboard.blade.php', 'fifa/portal.blade.php',
'analytics/dashboard.blade.php', 'reports/player-performance.blade.php',
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
