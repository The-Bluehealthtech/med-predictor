#!/usr/bin/env python3
"""Localize reviewed inline JavaScript alerts, prompts and notifications."""
import json
import pathlib
import re
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Veuillez remplir les champs obligatoires": "Fill in the required fields",
"❌ Veuillez sélectionner un fichier image valide (JPG, PNG, GIF)": "❌ Select a valid image file (JPG, PNG, GIF)",
"❌ La taille du fichier ne doit pas dépasser 5MB": "❌ The file size must not exceed 5 MB",
"Fonctionnalité en cours de développement. Les statistiques FIFA seront bientôt disponibles !": "This feature is under development. FIFA statistics will be available soon!",
"Veuillez sélectionner une dent avant de sauvegarder": "Select a tooth before saving",
"Veuillez remplir tous les champs obligatoires.": "Fill in all required fields.",
"✅ Données dentaires sauvegardées !": "✅ Dental data saved!",
"Êtes-vous sûr de vouloir réinitialiser toutes les zones ?": "Are you sure you want to reset all areas?",
"Notes sauvegardées !": "Notes saved!",
"📊 Rapport mensuel généré avec succès!": "📊 Monthly report generated successfully!",
"👤 Rapport individuel généré avec succès!": "👤 Individual report generated successfully!",
"📈 Rapport de progression généré avec succès!": "📈 Progress report generated successfully!",
"Aucune modification à sauvegarder": "No changes to save",
"Permissions sauvegardées avec succès!": "Permissions saved successfully!",
"Erreur lors de la sauvegarde: ": "Error while saving: ",
"Erreur lors de la sauvegarde des permissions": "Error while saving permissions",
"Toutes les permissions ont été activées": "All permissions have been enabled",
"Toutes les permissions ont été désactivées": "All permissions have been disabled",
"Êtes-vous sûr de vouloir réinitialiser toutes les permissions?": "Are you sure you want to reset all permissions?",
"Édition de rôle ": "Editing role ",
"Êtes-vous sûr de vouloir supprimer ce rôle ?": "Are you sure you want to delete this role?",
"Suppression de rôle ": "Deleting role ",
"Êtes-vous sûr de vouloir supprimer cette vaccination ?": "Are you sure you want to delete this vaccination?",
};
translations.update({
"Aucune vaccination enregistrée pour générer un certificat": "No vaccination recorded to generate a certificate",
"Certificat vaccinal généré avec succès !": "Vaccination certificate generated successfully!",
"Êtes-vous sûr de vouloir supprimer ce test ?": "Are you sure you want to delete this test?",
"Êtes-vous sûr de vouloir supprimer cette AUT ?": "Are you sure you want to delete this TUE?",
"Erreur lors de la suppression : ": "Error while deleting: ",
"Erreur lors de la suppression": "Error while deleting",
"Fusion réussie !": "Merge successful!",
"Erreur lors de la fusion : ": "Error while merging: ",
"Erreur lors de la fusion": "Error while merging",
"Veuillez remplir tous les champs obligatoires": "Fill in all required fields",
"Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible.": "Are you sure you want to delete this team? This cannot be undone.",
"Connexion réussie avec ": "Connected successfully to ",
"Erreur de connexion avec ": "Connection error with ",
"Erreur lors du test de connexion": "Connection test failed",
"Erreur de synchronisation: ": "Synchronization error: ",
"Erreur lors de la synchronisation": "Synchronization failed",
"Erreur lors de la mise à jour de la permission: ": "Error while updating permission: ",
"Erreur lors de la mise à jour de la permission": "Failed to update permission",
"❌ Erreur : Le fichier doit être une image (JPG, PNG, JPEG)": "❌ Error: the file must be an image (JPG, PNG, JPEG)",
})
pattern = re.compile(r'''\b(alert|confirm|prompt|showMessage|showNotification|showSuccess)\(\s*(['"])((?:\\.|(?!\2).)*?)\2''')
count = [0]
def convert_script(match):
    script = match.group(2)
    def translate(call):
        phrase = call.group(3)
        if phrase not in translations:
            return call.group(0)
        count[0] += 1
        key = phrase.replace("'", "\\'")
        return call.group(1) + "(@json(__('" + key + "'))"
    return match.group(1) + pattern.sub(translate, script) + match.group(3)
for file in (root / 'resources/views').rglob('*.blade.php'):
    original = file.read_text()
    revised = re.sub(r'(?is)(<script\b[^>]*>)(.*?)(</script>)', convert_script, original)
    if revised != original:
        file.write_text(revised)
print('Visible JavaScript messages translated:', count[0])

en_file = root / 'resources/lang/en.json'
original = en_file.read_text()
known = json.loads(original)
new = {key: value for key, value in translations.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = original.rfind('}')
    en_file.write_text(original[:pos].rstrip() + ',\n' + items + '\n' + original[pos:])
print('New English translations:', len(new))
