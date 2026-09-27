#!/usr/bin/env python3
"""Localize system settings, permissions, licence photo and fixtures screens."""
import json
import pathlib
import re

root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Nouveau Paramètre": "New Setting",
"Créer un nouveau paramètre système": "Create a new system setting",
"← Retour aux Paramètres": "← Back to Settings",
"Clé du paramètre": "Setting Key",
"Identifiant unique du paramètre (en anglais, sans espaces)": "Unique setting identifier (in English, without spaces)",
"Nom du paramètre": "Setting Name",
"Nom affiché dans l'interface": "Name displayed in the interface",
"Sélectionner un groupe": "Select a Group",
"Valeur par défaut": "Default Value",
"Règles de validation": "Validation Rules",
"Règles Laravel (ex: min:1|max:100|required)": "Laravel rules (e.g. min:1|max:100|required)",
"Paramètre public": "Public Setting",
"Peut être consulté sans authentification": "Can be viewed without signing in",
"Peut être modifié par les administrateurs": "Can be edited by administrators",
"Paramètre requis": "Required Setting",
"Doit avoir une valeur pour le fonctionnement du système": "Must have a value for the system to function",
"Créer le Paramètre": "Create Setting",
"Configurez les accès aux modules pour chaque rôle": "Configure module access for each role",
"Utilisez les cases à cocher ci-dessous pour configurer les permissions d'accès aux modules pour chaque rôle. Les modifications seront appliquées immédiatement.": "Use the checkboxes below to configure module permissions for each role. Changes take effect immediately.",
"Légende des Rôles": "Role Legend",
"Légende des Permissions": "Permission Legend",
"Créer de nouveaux éléments": "Create new items",
"Modifier les éléments existants": "Edit existing items",
"Supprimer des éléments": "Delete items",
"Exporter des données": "Export data",
"Gestion complète du module": "Full module management",
"✗ Tout Désactiver": "✗ Disable All",
"🔄 Réinitialiser": "🔄 Reset",
"Upload Photo Joueur - Système de Licences": "Player Photo Upload - Licence System",
"📸 Upload Photo Joueur - Système de Licences": "📸 Player Photo Upload - Licence System",
"📤 Upload de Photo": "📤 Upload Photo",
"✅ Pré-sélectionné": "✅ Preselected",
"Sélectionnez un club": "Select a club",
"Sélectionnez d'abord un club": "Select a club first",
"PNG, JPG, JPEG jusqu'à 5MB": "PNG, JPG, JPEG up to 5 MB",
"Sélectionnez le type de licence": "Select the licence type",
"ℹ️ Cette licence sera créée dans le système de licences existant de FIT.": "ℹ️ This licence will be created in FIT's existing licence system.",
"📤 Uploader la Photo et Créer la Licence": "📤 Upload Photo and Create Licence",
"🖼️ Photos Uploadées Récemment": "🖼️ Recently Uploaded Photos",
"Championnat Régional U19": "Regional U19 Championship",
"Coupe Régionale": "Regional Cup",
"Extérieur": "Away",
"Équipe A": "Team A",
"Équipe B": "Team B",
"Légende": "Legend",
"Matchs programmés": "Scheduled Matches",
"Matchs terminés": "Completed Matches",
"Matchs reportés": "Postponed Matches",
}
paths = ('admin/system-settings/create.blade.php',
'admin/rbac/module-permissions.blade.php',
'licenses/upload-photo.blade.php', 'modules/fixtures/index.blade.php')
en_file = root / 'resources/lang/en.json'
old = en_file.read_text()
known = json.loads(old)
used = {}
for name in paths:
    file = root / 'resources/views' / name
    source = file.read_text()
    if name == 'licenses/upload-photo.blade.php':
        source = source.replace("Sélectionnez d\\'abord un club</option>", "Sélectionnez d'abord un club</option>", 1)
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
