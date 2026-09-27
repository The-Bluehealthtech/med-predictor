#!/usr/bin/env python3
"""Localize visible inline JavaScript messages using JSON-safe Blade strings."""
import json
import pathlib
root = pathlib.Path(__file__).resolve().parents[1]
translations = {
"Sélectionner une métrique": "Select a metric",
"La métrique sélectionnée ne fait pas partie du catalogue FIT.": "The selected metric is not in the FIT catalogue.",
"Sélectionnez le mode de mesure sociale.": "Select the social measurement mode.",
"Le maximum de l’échelle doit être supérieur au minimum.": "The scale maximum must be greater than the minimum.",
"Enregistrement refusé.": "Save rejected.",
"La requête n’a pas pu être envoyée.": "The request could not be sent.",
"Vérification autorisée :": "Authorized verification:",
"Fonctionnalité de visualisation des joueurs à implémenter": "Player viewing is not implemented yet",
"Fonctionnalité de visualisation des matchs à implémenter": "Match viewing is not implemented yet",
"Fonctionnalité de visualisation des statistiques à implémenter": "Statistics viewing is not implemented yet",
"Fonctionnalité d'export à implémenter": "Export is not implemented yet",
"Motif du rejet :": "Reason for rejection:",
"Êtes-vous sûr de vouloir supprimer cette association ?": "Are you sure you want to delete this association?",
"Veuillez sélectionner au moins un utilisateur": "Select at least one user",
"Rôle appliqué avec succès à": "Role applied successfully to",
"Erreur:": "Error:",
"Minute du but pour": "Minute of the goal for",
"Minute du carton": "Minute of the card",
"jaune": "yellow",
"rouge": "red",
"pour": "for",
"Veuillez remplir tous les champs obligatoires": "Fill in all required fields",
"Erreur lors de la soumission du rapport": "Error submitting the report",
}

def edit(name, changes):
    path = root / 'resources/views' / name
    source = path.read_text()
    for before, after in changes:
        count = source.count(before)
        if not count:
            print('Missing expected source:', name, before[:65])
            continue
        source = source.replace(before, after)
        print(name, count, before[:55])
    path.write_text(source)


def expression(key):
    return "@json(__('" + key.replace("'", "\\'") + "'))"


fit_file = 'performances/fit-metrics.blade.php'
# Construct the option as a DOM node to avoid HTML interpolation in JavaScript.
fit_changes = [
    ('nameInput.innerHTML =\n                            \'<option value="">Sélectionner une métrique</option>\';',
     "nameInput.replaceChildren(new Option(" + expression('Sélectionner une métrique') + ", ''));"),
]
for phrase in ("La métrique sélectionnée ne fait pas partie du catalogue FIT.",
               "Sélectionnez le mode de mesure sociale.",
               "Le maximum de l’échelle doit être supérieur au minimum.",
               "Enregistrement refusé.", "La requête n’a pas pu être envoyée."):
    fit_changes.append(("'" + phrase + "'", expression(phrase)))
fit_changes.append(("Vérification autorisée :", "{{ __('Vérification autorisée :') }}"))
edit(fit_file, fit_changes)

teams = 'modules/teams/show.blade.php'
team_changes = []
for phrase in (
    'Fonctionnalité de visualisation des joueurs à implémenter',
    'Fonctionnalité de visualisation des matchs à implémenter',
    'Fonctionnalité de visualisation des statistiques à implémenter',
):
    team_changes.append(("alert('" + phrase + "')", "alert(" + expression(phrase) + ")"))
team_changes.append(("alert('Fonctionnalité d\\'export à implémenter')",
                     "alert(" + expression("Fonctionnalité d'export à implémenter") + ")"))
edit(teams, team_changes)
edit('licenses/validation-canonical.blade.php',
     [("prompt('Motif du rejet :')", "prompt(" + expression('Motif du rejet :') + ")")])
edit('modules/associations/index.blade.php',
     [("confirm('Êtes-vous sûr de vouloir supprimer cette association ?')",
       "confirm(" + expression('Êtes-vous sûr de vouloir supprimer cette association ?') + ")")])
edit('performance/index.blade.php',
     [("alert('⚡ Données en temps réel - Surveillance active des performances...')",
       "alert(" + expression('⚡ Données en temps réel - Surveillance active des performances...') + ")")])
translations["⚡ Données en temps réel - Surveillance active des performances..."] = "⚡ Real-time data - Active performance monitoring..."

referee_changes = [
    ('prompt(`Minute du but pour ${player}:`)',
     'prompt(`${' + expression('Minute du but pour') + '} ${player}:`)'),
    ("prompt(`Minute du carton ${type === 'yellow' ? 'jaune' : 'rouge'} pour ${player}:`)",
     "prompt(`${" + expression('Minute du carton') +
     "} ${type === 'yellow' ? " + expression('jaune') + ' : ' + expression('rouge') +
     "} ${" + expression('pour') + "} ${player}:`)"),
]
for phrase in ('Veuillez remplir tous les champs obligatoires',
               'Erreur lors de la soumission du rapport'):
    referee_changes.append(("alert('" + phrase + "')", "alert(" + expression(phrase) + ")"))
edit('referee/create-report-form.blade.php', referee_changes)

en_file = root / 'resources/lang/en.json'
original = en_file.read_text()
known = json.loads(original)
new = {key: value for key, value in translations.items() if key not in known}
if new:
    items = ',\n'.join('  ' + json.dumps(key, ensure_ascii=False) + ': ' + json.dumps(value, ensure_ascii=False) for key, value in new.items())
    pos = original.rfind('}')
    en_file.write_text(original[:pos].rstrip() + ',\n' + items + '\n' + original[pos:])
print('New English translations:', len(new))
