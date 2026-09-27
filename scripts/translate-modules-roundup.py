#!/usr/bin/env python3
"""Localize reviewed module landing pages and connected devices."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Réseau de jumeaux numériques": "Digital Twin Network",
"Simulation et modélisation avancée des athlètes": "Advanced athlete simulation and modelling",
"Prédire": "Predict",
"Modèles numériques des athlètes": "Athlete Digital Models",
"Prévision des performances": "Performance Forecasting",
"Calendrier de récupération": "Recovery Schedule",
"Optimisation de l'entraînement": "Training Optimization",
"Connectivité FIFA et gestion des contrats": "FIFA Connectivity and Contract Management",
"Connectivité FIFA, synchronisation et gestion des contrats": "FIFA connectivity, synchronization and contract management",
"Connectivité": "Connectivity",
"Statistiques détaillées": "Detailed Statistics",
"🔗 Connectivité": "🔗 Connectivity",
"Filtrage par confédération": "Filter by Confederation",
"Voir toutes les confédérations": "View All Confederations",
"Aucun système de comptabilité générale (revenus, dépenses, marge, ROI) n'est encore connecté à cette plateforme. Les seules données financières réelles disponibles concernent les paiements de transferts de joueurs (voir le": "No general accounting system (revenue, expenses, margin, ROI) is connected to this platform yet. The only available financial data concerns player transfer payments (see the",
"Rapports Détaillés": "Detailed Reports",
"Analyse des revenus et dépenses du mois": "Monthly income and expense analysis",
"Générer PDF (à venir)": "Generate PDF (coming soon)",
"Bilan complet de l'année": "Full-Year Report",
"Sélection des Joueurs - FIT Platform": "Player Selection - FIT Platform",
"🏆 Sélection des Joueurs": "🏆 Player Selection",
"Choisissez un joueur pour accéder à son portail": "Choose a player to open their portal",
"Toutes les nationalités": "All Nationalities",
"Défenseur (DF)": "Defender (DF)",
"Accéder au Portail": "Open Portal",
"📋 Gérer": "📋 Manage",
"📝 Données personnelles": "📝 Personal Information",
"Téléphone:": "Phone:",
"Numéro:": "Number:",
"🏆 Gérer le logo de l'association": "🏆 Manage Association Logo",
"🏆 Gérer le logo (pas d'association)": "🏆 Manage Logo (no association)",
"Liste des Licences - Système de Licences": "Licence List - Licence System",
"Aucune licence trouvée.": "No licences found.",
"Créer la première licence": "Create the first licence",
"Créer une licence": "Create a licence",
"Éditer": "Edit",
"Sélectionner un fichier image": "Select an Image File",
"Formats acceptés : JPEG, PNG, JPG, GIF, SVG (max 2MB)": "Accepted formats: JPEG, PNG, JPG, GIF, SVG (max 2 MB)",
"• Utilisez des images de haute qualité (recommandé : 200x200px minimum)": "• Use high-quality images (at least 200 × 200 px recommended)",
"• Les formats SVG sont recommandés pour une meilleure qualité": "• SVG is recommended for better quality",
"• Le logo sera automatiquement redimensionné si nécessaire": "• The logo will be resized automatically if needed",
"• L'ancien logo sera remplacé par le nouveau": "• The old logo will be replaced by the new one",
"Créer et gérer les rôles du système": "Create and Manage System Roles",
"➕ Créer un Nouveau Rôle": "➕ Create a New Role",
"Rôles du Système": "System Roles",
"Rôle système": "System role",
"Créer un Nouveau Rôle": "Create a New Role",
"Nom du rôle": "Role Name",
"Gérer les annonces officielles": "Manage Official Announcements",
"🔴 Haute Priorité": "🔴 High Priority",
"🟡 Moyenne Priorité": "🟡 Medium Priority",
"🟢 Basse Priorité": "🟢 Low Priority",
"Aucune annonce trouvée.": "No announcements found.",
"➕ Créer la première annonce": "➕ Create the first announcement",
"Analyse avancée des performances des athlètes": "Advanced Athlete Performance Analysis",
"Analyse de la vitesse des athlètes": "Athlete Speed Analysis",
"Métriques d'endurance": "Endurance Metrics",
"Tendances d'amélioration": "Improvement Trends",
"Alertes de déclin": "Decline Alerts",
"Modèles de récupération": "Recovery Models",
"Gestion des appareils connectés et dispositifs IoT": "Manage Connected Devices and IoT Equipment",
"Appareils enregistrés": "Registered Devices",
"Actuellement connectés": "Currently Connected",
"Taux de connectivité": "Connectivity Rate",
"Aucun appareil enregistré": "No devices registered",
"Les appareils connectés (montres, trackers) apparaîtront ici une fois synchronisés.": "Connected devices (watches, trackers) will appear here once synchronized.",
"Gestion et suivi de tous les clubs affiliés": "Manage and Track All Affiliated Clubs",
"Aucun club trouvé": "No clubs found",
"Aucun club n'est actuellement enregistré dans la base de données": "No club is currently registered in the database",
"Êtes-vous sûr de vouloir supprimer le club": "Are you sure you want to delete the club",
"⚠️ Cette action est irréversible !": "⚠️ This action cannot be undone!",
"Sélectionnez le club avec lequel vous voulez fusionner": "Select the club to merge with",
"À Venir": "Upcoming",
"Résultats Récents": "Recent Results",
"Aucun résultat récent": "No recent results",
"Résultats & Classements": "Results & Standings",
"Classements Détaillés": "Detailed Standings",
"Gestion des arbitres et désignations": "Manage Referees and Appointments",
"🎭 Gestion des Rôles": "🎭 Role Management",
"Définissez les rôles et leurs permissions prédéfinies": "Define roles and their preset permissions",
"Aucune permission spécifique": "No specific permissions",
"Répartition des Rôles": "Role Breakdown",
"Détails du Rôle": "Role Details",
"Appliquer le Rôle": "Apply Role",
}
paths = (
'modules/dtn/index.blade.php', 'modules/fifa/dashboard.blade.php',
'modules/finance/reports.blade.php', 'player-selection.blade.php',
'portail-joueur-simplifie.blade.php', 'licenses/index.blade.php',
'club-management/logo/upload.blade.php', 'admin/rbac/roles.blade.php',
'admin/content-management/announcements.blade.php',
'modules/performances/index.blade.php', 'modules/portal/devices.blade.php',
'modules/clubs/index.blade.php', 'modules/competitions/index.blade.php',
'modules/role-management/index.blade.php',
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
