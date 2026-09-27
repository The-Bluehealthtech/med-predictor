#!/usr/bin/env python3
"""Localize transfer, team, licence, role and association screens."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Gérer les transferts de joueurs connecté à FIFA TMS": "Manage player transfers connected to FIFA TMS",
"Statut de la connexion avec le système FIFA Transfer Matching System": "Connection status for FIFA Transfer Matching System",
"Configuration présente": "Configuration present",
"🔄 Synchronisation TMS reportée": "🔄 TMS synchronization postponed",
"Dernière synchronisation:": "Last synchronization:",
"Approuvés": "Approved",
"Rejetés": "Rejected",
"Cliquer pour gérer →": "Click to manage →",
"Gérer les transferts de joueurs": "Manage Player Transfers",
"← Retour à la Gestion des Transferts": "← Back to Transfer Management",
"Approuvé": "Approved",
"Rejeté": "Rejected",
"🔄 Prêt": "🔄 Ready",
"Créé:": "Created:",
"Aucun transfert trouvé.": "No transfers found.",
"Les transferts apparaîtront ici une fois qu'ils seront créés ou synchronisés avec FIFA TMS.": "Transfers will appear here once created or synchronized with FIFA TMS.",
"Gestion complète des joueurs, profils et informations": "Manage players, profiles and information",
"🏥 Dossiers Médicaux": "🏥 Medical Records",
"Dernière Mise à Jour": "Last Updated",
"✅ Approuvée": "✅ Approved",
"❌ Rejetée": "❌ Rejected",
"Aucun joueur enregistré": "No players registered",
"Licences Approuvées": "Approved Licences",
"Rejetées": "Rejected",
"Créer une Équipe": "Create a Team",
"Ajouter une nouvelle équipe selon les standards FIFA Connect": "Add a new team following FIFA Connect standards",
"Nom de l'équipe *": "Team Name *",
"Sélectionner un niveau": "Select a Level",
"Catégorie d'âge *": "Age Category *",
"Sélectionner une catégorie": "Select a Category",
"Sélectionner une discipline": "Select a Discipline",
"Créer l'équipe": "Create Team",
"🏆 Gérer le Logo": "🏆 Manage Logo",
"Logo personnalisé actuel": "Current Custom Logo",
"🔄 Réinitialiser au logo national": "🔄 Restore National Logo",
"🌍 Mise à jour des logos nationaux": "🌍 Update National Logos",
"Télécharger les derniers logos depuis l'API-Football": "Download the latest logos from API-Football",
"🔄 Mettre à jour depuis l'API": "🔄 Update from API",
"Logo personnalisé :": "Custom logo:",
"Cette licence nécessite des modifications avant réapprobation.": "This licence needs changes before it can be approved again.",
"Sélectionnez un poste": "Select a Position",
"Staff médical": "Medical Staff",
"Sélectionnez le type": "Select the Type",
"Période de validité": "Validity Period",
"Sélectionnez la période": "Select the Period",
"Photo actuellement enregistrée": "Currently Saved Photo",
"Rôles, permissions et contrôle d'accès": "Roles, Permissions and Access Control",
"Rôles Actifs": "Active Roles",
"👥 Gérer les Rôles": "👥 Manage Roles",
"🔑 Gérer les Permissions": "🔑 Manage Permissions",
"👤 Gérer les Utilisateurs": "👤 Manage Users",
"Vue d'ensemble des Rôles": "Role Overview",
"Répartition des Utilisateurs par Rôle": "Users by Role",
"Assigner des rôles aux utilisateurs": "Assign Roles to Users",
"Utilisateurs du Système": "System Users",
"Rôle Actuel": "Current Role",
"Assigner Rôle": "Assign Role",
"Assigner un Rôle": "Assign a Role",
"Nouveau Rôle": "New Role",
"Sélectionner un rôle": "Select a Role",
}
paths = (
'admin/transfer-management/index.blade.php', 'admin/transfer-management/transfers.blade.php',
'modules/players/index.blade.php', 'modules/teams/create.blade.php',
'associations/edit-logo.blade.php', 'licenses/edit.blade.php',
'admin/rbac/index.blade.php', 'admin/rbac/users.blade.php',
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
