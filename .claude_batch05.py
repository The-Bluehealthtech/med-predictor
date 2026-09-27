# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="ml-3 text-lg font-medium text-gray-900">Incohérences détectées</h3>',
     "<h3 class=\"ml-3 text-lg font-medium text-gray-900\">{{ __('pcma.inconsistencies_title') }}</h3>", 1),

    ("                            Des différences ont été détectées entre vos données vocales et celles de la base. \n                            Veuillez confirmer l'identité du joueur en utilisant l'une des méthodes suivantes :\n",
     "                            {{ __('pcma.inconsistencies_desc') }}\n", 1),

    ('<h4 class="font-medium text-blue-900 mb-3">Méthodes de confirmation :</h4>',
     "<h4 class=\"font-medium text-blue-900 mb-3\">{{ __('pcma.confirmation_methods_title') }}</h4>", 1),

    ('<div class="text-sm font-medium text-blue-800">Numéro de Licence</div>',
     "<div class=\"text-sm font-medium text-blue-800\">{{ __('pcma.license_number_label') }}</div>", 1),

    ('<div class="text-sm font-medium text-blue-800">Séquence complète</div>',
     "<div class=\"text-sm font-medium text-blue-800\">{{ __('pcma.full_sequence_label') }}</div>", 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-2">ID FIFA Connect ou Numéro de Licence</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-2\">{{ __('pcma.confirmation_id_label') }}</label>", 1),

    ('placeholder="FIFA ID officiel (7 caractères) ou référence locale de licence"',
     'placeholder="{{ __(\'pcma.confirmation_id_placeholder\') }}"', 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-2">Séquence complète (Nom + Âge + Club + Position)</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-2\">{{ __('pcma.confirmation_sequence_label') }}</label>", 1),

    ('placeholder="Ex: Ali Jebali 24 AS Gabès Milieu offensif"',
     'placeholder="{{ __(\'pcma.confirmation_sequence_placeholder\') }}"', 1),

    ('                            Annuler\n',
     "                            {{ __('pcma.cancel_btn') }}\n", 1),

    ("                            Confirmer l'identité\n",
     "                            {{ __('pcma.confirm_identity_btn') }}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH05 OK", len(replacements), "replacements applied")
