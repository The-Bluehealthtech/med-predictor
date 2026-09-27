# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="text-lg font-semibold text-blue-800 mb-4">Informations de l\'Athlète</h3>',
     "<h3 class=\"text-lg font-semibold text-blue-800 mb-4\">{{ __('pcma.athlete_info_title') }}</h3>", 1),

    ("                                    Athlète *\n",
     "                                    {{ __('pcma.athlete_label') }}\n", 1),

    ('<option value="">Sélectionner un athlète</option>',
     "<option value=\"\">{{ __('pcma.select_athlete_placeholder') }}</option>", 1),

    ("{{ $athlete->fifa_connect_id ?? 'Pas d\\'ID FIFA' }}",
     "{{ $athlete->fifa_connect_id ?? __('pcma.no_fifa_id') }}", 1),

    ("                                    ID FIFA Connect\n                                </label>\n                                <input \n                                    type=\"text\" \n                                    id=\"fifa_connect_id\" \n                                    name=\"fifa_connect_id\" \n                                    value=\"{{ old('fifa_connect_id') }}\"\n                                    placeholder=\"Entrez l'ID FIFA Connect du joueur\"\n                                    class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent\"\n                                >\n                                <p class=\"text-xs text-gray-600 mt-1\">Laissez vide si l'athlète n'a pas d'ID FIFA Connect</p>",
     "                                    {{ __('pcma.fifa_connect_id_label') }}\n                                </label>\n                                <input \n                                    type=\"text\" \n                                    id=\"fifa_connect_id\" \n                                    name=\"fifa_connect_id\" \n                                    value=\"{{ old('fifa_connect_id') }}\"\n                                    placeholder=\"{{ __('pcma.fifa_connect_id_placeholder') }}\"\n                                    class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent\"\n                                >\n                                <p class=\"text-xs text-gray-600 mt-1\">{{ __('pcma.fifa_connect_id_hint') }}</p>", 1),

    ("                                Type d'évaluation *\n",
     "                                {{ __('pcma.assessment_type_label') }}\n", 1),

    ('<option value="">Sélectionner le type</option>',
     "<option value=\"\">{{ __('pcma.select_type_placeholder') }}</option>", 1),

    (">Cardiovasculaire</option>", ">{{ __('pcma.type_cardio') }}</option>", 1),
    (">Dentaire</option>", ">{{ __('pcma.type_dental') }}</option>", 1),
    (">Neurologique</option>", ">{{ __('pcma.type_neurological') }}</option>", 1),
    (">Orthopédique</option>", ">{{ __('pcma.type_orthopedic') }}</option>", 1),

    ("                                Assesseur *\n",
     "                                {{ __('pcma.assessor_label') }}\n", 1),

    ('<option value="">Sélectionner un assesseur</option>',
     "<option value=\"\">{{ __('pcma.select_assessor_placeholder') }}</option>", 1),

    ("                                Date d'évaluation *\n",
     "                                {{ __('pcma.assessment_date_label') }}\n", 1),

    ("                                Décision médicale *\n",
     "                                {{ __('pcma.medical_decision_label') }}\n", 1),

    ('<option value="">Sélectionner la décision</option>',
     "<option value=\"\">{{ __('pcma.select_decision_placeholder') }}</option>", 1),

    (">Apte</option>", ">{{ __('pcma.decision_fit') }}</option>", 1),
    (">Inapte</option>", ">{{ __('pcma.decision_not_fit') }}</option>", 1),
    (">Apte sous conditions</option>", ">{{ __('pcma.decision_conditional') }}</option>", 1),

    ('<p class="text-xs text-gray-500 mt-1">Cette décision saisie dans le PCMA sera la seule décision proposée à la signature.</p>',
     "<p class=\"text-xs text-gray-500 mt-1\">{{ __('pcma.decision_hint') }}</p>", 1),

    ("                                Statut *\n",
     "                                {{ __('pcma.status_label') }}\n", 1),

    (">En attente</option>", ">{{ __('pcma.status_pending') }}</option>", 1),
    (">Complété</option>", ">{{ __('pcma.status_completed') }}</option>", 1),
    (">Échoué</option>", ">{{ __('pcma.status_failed') }}</option>", 1),

    ("                                Notes\n                            </label>\n                            <textarea \n                                id=\"notes\" \n                                name=\"notes\" \n                                rows=\"4\"\n                                class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent\"\n                                placeholder=\"Notes additionnelles sur l'évaluation...\"",
     "                                {{ __('pcma.notes_label') }}\n                            </label>\n                            <textarea \n                                id=\"notes\" \n                                name=\"notes\" \n                                rows=\"4\"\n                                class=\"w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent\"\n                                placeholder=\"{{ __('pcma.notes_placeholder') }}\"", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH06 OK", len(replacements), "replacements applied")
