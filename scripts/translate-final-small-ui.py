#!/usr/bin/env python3
"""Localize reviewed remaining short views; preserve names and source identifiers."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Licences joueurs enregistrées dans player_licenses.": "Player licences recorded in player_licenses.",
"Rejetées / révoquées": "Rejected / revoked",
"Aucune licence dans le périmètre autorisé.": "No licences within the authorized scope.",
"Retour à l'accueil": "Back to Home",
"Développé par": "Developed by",
"&copy; 2025 The Blue Healthtech Ltd. Tous droits réservés.": "&copy; 2025 The Blue Healthtech Ltd. All rights reserved.",
"Buts Marqués": "Goals Scored",
"Effectif de l'Équipe": "Team Squad",
"Matchs Récents": "Recent Matches",
"Gérer les articles de presse et actualités": "Manage press articles and news",
"Aucun article trouvé.": "No articles found.",
"➕ Créer le premier article": "➕ Create the first article",
"Cette page est destinée aux": "This page is intended for",
"Ce module permet aux clubs de demander des licences pour leurs joueurs. Sélectionnez un joueur dans la liste ci-dessous pour initier une demande de licence.": "This module allows clubs to request licences for their players. Select a player below to start a licence request.",
'Liste des joueurs disponibles pour une demande de licence. Cliquez sur "📋 Demander Licence" pour initier le processus. Les données existantes du joueur seront automatiquement pré-remplies dans le formulaire.': 'Players available for a licence request. Click "📋 Request Licence" to begin. Existing player data will be filled in automatically.',
"Nom abrégé": "Abbreviated Name",
"Confédération *": "Confederation *",
"Les identifiants et statuts de synchronisation FIFA sont renseignés uniquement par l'intégration FIFA.": "FIFA identifiers and synchronization statuses are supplied exclusively by the FIFA integration.",
"🚧 Cette section est en cours de développement.": "🚧 This section is under development.",
"Les classements et rankings seront bientôt disponibles.": "Rankings and standings will be available soon.",
"← Retour aux compétitions": "← Back to Competitions",
"Détails de l'équipe": "Team Details",
"Catégorie d'âge": "Age Category",
"ID Équipe": "Team ID",
"Tableau de bord analytique pour le suivi des données FIFA Connect": "Analytics dashboard for FIFA Connect data monitoring",
"Nouveaux joueurs ajoutés": "New Players Added",
"Clubs enregistrés": "Registered Clubs",
"Gérez les utilisateurs, leurs rôles et permissions": "Manage users, their roles and permissions",
"+ Créer Utilisateur": "+ Create User",
"Liste de tous les utilisateurs du système": "List of all system users",
"Gestion des connexions d'appareils et données IoT": "Manage device connections and IoT data",
"IoT connecté": "Connected IoT",
"Aucun passeport enregistré": "No passports registered",
"Commencez par créer un nouveau passeport.": "Start by creating a new passport.",
"Créer un passeport": "Create a Passport",
"Plateforme Fédération Internationale de Tunisie": "International Federation of Tunisia Platform",
"Essayez de modifier vos critères de recherche.": "Try changing your search criteria.",
"Évaluation de la valeur et qualité des données FIFA": "FIFA Data Value and Quality Assessment",
"Accéder aux Analytics": "Open Analytics",
"Rapport Soumis avec Succès!": "Report Submitted Successfully!",
"Matches Assignés (À Venir)": "Assigned Matches (Upcoming)",
"Matches Terminés - Créer un Rapport": "Completed Matches - Create a Report",
"Début": "Start",
"Créer la demande": "Create Request",
"Transcription audio avec OpenAI Whisper pour contexte médical": "Audio transcription with OpenAI Whisper for medical context",
"Analyse médicale avancée avec Google Gemini AI": "Advanced medical analysis with Google Gemini AI",
"Nom court utilisé pour l'affichage": "Short name used for display",
"L'affichage des recommandations enregistrées n'est pas encore disponible.": "Displaying recorded recommendations is not available yet.",
"Voir les données de performance enregistrées": "View recorded performance data",
"Aucun type de licence configuré": "No licence types configured",
"Durée de validité": "Validity Period",
"Recherche des joueurs enregistrés": "Search registered players",
"Aucun joueur trouvé.": "No players found.",
"Créer un dossier (fonctionnalité à venir)": "Create a record (coming soon)",
"La demande utilise uniquement l'identifiant déjà associé au joueur sélectionné. Aucun identifiant n'est généré ici.": "The request uses only the identifier already associated with the selected player. No identifier is generated here.",
"Les préférences de notification ne sont pas encore configurables individuellement pour le moment. Cette fonctionnalité arrivera dans une prochaine mise à jour.": "Notification preferences cannot yet be configured individually. This feature will be added in a future update.",
"Aucune demande enregistrée": "No requests registered",
"Modifier l'Équipe": "Edit Team",
"Formats acceptés: JPEG, PNG, JPG, GIF. Taille max: 2MB": "Accepted formats: JPEG, PNG, JPG, GIF. Maximum size: 2 MB",
"Commencez par créer une nouvelle licence.": "Start by creating a new licence.",
"Cette application ne dispose pas encore d'un module de comptabilité générale : il n'existe aujourd'hui aucune donnée réelle de budget annuel, de budget par catégorie (salaires, maintenance, déplacements, équipement...) ni de suivi trimestriel. Les chiffres qui s'affichaient ici auparavant (budget annuel, montants dépensés, pourcentages) étaient des exemples fixes, identiques pour tous les clubs et associations, et ont été retirés.": "This application does not yet have a general accounting module: there is currently no real annual budget, category budget (payroll, maintenance, travel, equipment, etc.) or quarterly tracking data. Previously displayed figures were fixed examples shared by all clubs and associations and have been removed.",
"Mettre en place une vraie gestion de budgets nécessiterait de créer ce modèle de données dans l'application ; cela dépasse le cadre d'un nettoyage de données factices et devrait être traité comme un projet à part.": "Real budget management requires a corresponding data model in the application. This goes beyond removing sample data and should be handled as a separate project.",
"⚠️ Aucune banque n'est réellement connectée": "⚠️ No bank is connected",
"Ces éléments ont été retirés. Mettre en place de vraies connexions bancaires nécessiterait d'intégrer un vrai fournisseur Open Banking/PSD2 et de créer le modèle de données correspondant (comptes, identifiants, synchronisations) ; cela dépasse le cadre d'un nettoyage de données factices et devrait être traité comme un projet à part.": "These items have been removed. Real banking connections require an Open Banking/PSD2 provider and a matching data model (accounts, identifiers and synchronizations). This goes beyond removing sample data and should be handled as a separate project.",
}
paths = (
'licenses/validation-canonical.blade.php', 'account-request/create.blade.php',
'team-portal/team-details.blade.php', 'admin/content-management/articles.blade.php',
'modules/licenses/index.blade.php', 'modules/organization-cards/form.blade.php',
'modules/rankings/index.blade.php', 'modules/teams/show.blade.php',
'modules/fifa/analytics.blade.php', 'modules/user-management/index.blade.php',
'modules/device-connections/index.blade.php', 'modules/player-passports/index.blade.php',
'players-list-public.blade.php', 'dashboard.blade.php',
'referee/report-success.blade.php', 'referee/create-match-report.blade.php',
'licenses/player-request.blade.php', 'ai-testing/index.blade.php',
'modules/clubs/edit.blade.php', 'modules/performance-recommendations/index.blade.php',
'modules/finance/budgets.blade.php', 'modules/finance/bank-integrations.blade.php',
'modules/license-types/index.blade.php', 'modules/fifa/players/search.blade.php',
'players/show.blade.php', 'club-management/licenses/create.blade.php',
'modules/referee/settings.blade.php', 'modules/registration-requests/index.blade.php',
'modules/teams/edit.blade.php', 'modules/player-registration/edit.blade.php',
'modules/club/player-licenses/index.blade.php',
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
