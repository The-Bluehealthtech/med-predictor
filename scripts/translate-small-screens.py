#!/usr/bin/env python3
"""Translate static text on reviewed compact administration and module screens."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"État de toutes les cartes des modules": "Status of All Module Cards",
"Vue non trouvée": "View not found",
"Détail des Modules": "Module Details",
"VUE NON TROUVÉE": "VIEW NOT FOUND",
"Résumé par Module": "Summary by Module",
"📋 Dossiers Médicaux": "📋 Medical Records",
"Haute gravité": "High Severity",
"Moyenne gravité": "Medium Severity",
"Faible gravité": "Low Severity",
"Gravité": "Severity",
"Créer et gérer les permissions du système": "Create and Manage System Permissions",
"➕ Créer une Permission": "➕ Create a Permission",
"Aucune permission trouvée": "No permissions found",
"Initialisez les permissions par défaut pour commencer.": "Initialize the default permissions to get started.",
"Créer une Nouvelle Permission": "Create a New Permission",
"Gérer les questions fréquemment posées": "Manage Frequently Asked Questions",
"Questions Fréquemment Posées": "Frequently Asked Questions",
"Modifié:": "Modified:",
"Aucune FAQ trouvée.": "No FAQs found.",
"➕ Créer la première FAQ": "➕ Create the first FAQ",
"Données enregistrées dans player_performances. Aucune valeur de démonstration.": "Data recorded in player_performances. No demonstration values.",
"Évolution du score global": "Overall Score Trend",
"Aucune donnée historique disponible.": "No historical data available.",
"Meilleures moyennes enregistrées": "Highest Recorded Averages",
"Aucune donnée de performance disponible.": "No performance data available.",
"Détails de l'Association": "Association Details",
"Clubs affiliés": "Affiliated Clubs",
"📞 Téléphone:": "📞 Phone:",
"🏟️ Gérer les clubs": "🏟️ Manage Clubs",
"Gestion des Équipes": "Team Management",
"Gestion des équipes selon les standards FIFA Connect": "Manage teams following FIFA Connect standards",
"Total Équipes": "Total Teams",
"Création en Masse": "Bulk Creation",
"Liste des Équipes": "Team List",
"Édition Transaction": "Edit Transaction",
"Modifier les données financières": "Edit Financial Data",
"Dépense": "Expense",
"Frais de déplacement": "Travel Expenses",
"Équipement": "Equipment",
"Gestion des joueurs enregistrés dans le système": "Manage players registered in the system",
"Joueurs Enregistrés": "Registered Players",
"Aucun joueur trouvé": "No players found",
"Commencez par créer votre premier joueur.": "Start by creating your first player.",
"➕ Créer le premier joueur": "➕ Create the first player",
"Désignation des arbitres": "Referee Appointments",
"Matchs programmés et arbitres enregistrés.": "Scheduled matches and registered referees.",
"Trois arbitres enregistrés sont requis.": "Three registered referees are required.",
"Aucun match programmé à désigner.": "No scheduled matches need referee appointments.",
"🏃‍♂️ Portail Athlète": "🏃‍♂️ Athlete Portal",
"🏥 Secrétariat Médical": "🏥 Medical Secretariat",
"Licence validée -": "Licence approved -",
"Nouveau dossier médical créé pour Athlète #1234": "New medical record created for Athlete #1234",
"Dernières mesures réellement enregistrées dans player_real_time_health.": "Latest measurements recorded in player_real_time_health.",
"Mesures chargées": "Measurements loaded",
"Dernière mesure": "Latest measurement",
"Aucune mesure temps réel n'est disponible dans le périmètre autorisé. Le module ne simule aucune donnée.": "No real-time measurements are available within the authorized scope. The module does not simulate data.",
"Agrégats et alertes réels dans le périmètre autorisé.": "Recorded aggregates and alerts within the authorized scope.",
"Tendances et scores enregistrés dans player_performances.": "Trends and scores recorded in player_performances.",
"Scénarios de simulation, séparés des données observées.": "Simulated scenarios, separate from observed data.",
"Aucune alerte active dans le périmètre autorisé.": "No active alerts within the authorized scope.",
"Journal des activités et événements système": "Activity and System Event Log",
"Date de début": "Start Date",
"Journal des Activités": "Activity Log",
"Aucun log trouvé avec les filtres actuels.": "No logs found with the current filters.",
"Gérer les articles, pages, médias et contenu du site": "Manage articles, pages, media and site content",
"Médias": "Media",
"Publiés": "Published",
"Découvrez comment utiliser efficacement le Content Management avec des copies d'écran détaillées": "Learn how to use Content Management effectively with detailed screenshots",
"Gérer les pages statiques du site": "Manage the site's static pages",
"Aucune page trouvée.": "No pages found.",
"➕ Créer la première page": "➕ Create the first page",
"Gérer les fichiers multimédias": "Manage media files",
"Bibliothèque Médias": "Media Library",
"Aucun fichier média trouvé.": "No media files found.",
"Arbitres enregistrés et affectations réelles.": "Registered referees and actual appointments.",
"Gérer les affectations": "Manage Appointments",
"Statut enregistré": "Recorded Status",
"Aucun arbitre enregistré.": "No referees registered.",
"Démonstration Logos Officiels des Fédérations": "Federation Official Logos Demonstration",
"Gestion des fédérations nationales et régionales": "Manage national and regional federations",
"Fondée en:": "Founded in:",
"À développer": "To be developed",
}
paths = (
'module-test-results.blade.php', 'players/health-records.blade.php',
'admin/rbac/permissions.blade.php', 'admin/content-management/faq.blade.php',
'modules/performances/analytics-canonical.blade.php', 'modules/associations/show.blade.php',
'modules/teams/index.blade.php', 'modules/finance/edit-transaction.blade.php',
'modules/player-registration/index.blade.php', 'modules/player-registration/health-records.blade.php',
'admin/referee-assignments.blade.php', 'layouts/navigation.blade.php',
'rpm/index-canonical.blade.php', 'analytics/dashboard-canonical.blade.php',
'admin/audit-trail/index.blade.php', 'admin/content-management/index.blade.php',
'admin/content-management/pages.blade.php', 'admin/content-management/media.blade.php',
'modules/referees/index.blade.php', 'modules/associations/index.blade.php',
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
