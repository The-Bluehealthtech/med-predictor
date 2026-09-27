# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("@section('title', 'Nouveau PCMA - Med Predictor')",
     "@section('title', __('pcma.title'))", 1),

    ('<h1 class="text-3xl font-bold text-gray-900"> Nouveau PCMA</h1>',
     '<h1 class="text-3xl font-bold text-gray-900"> {{ __(\'pcma.page_heading\') }}</h1>', 1),

    ('<p class="text-gray-600 mt-2">Créer une nouvelle évaluation médicale pré-compétition</p>',
     "<p class=\"text-gray-600 mt-2\">{{ __('pcma.page_subheading') }}</p>", 1),

    ('                    ← Retour au Dashboard\n',
     "                    {{ __('pcma.back_to_dashboard') }}\n", 1),

    ('<h2 class="text-xl font-semibold text-gray-800">Modes de collecte</h2>',
     "<h2 class=\"text-xl font-semibold text-gray-800\">{{ __('pcma.modes_heading') }}</h2>", 1),

    ('<h3 class="font-semibold">Mode Manuel</h3>\n                            <p class="text-sm opacity-90">Saisie directe au clavier</p>',
     "<h3 class=\"font-semibold\">{{ __('pcma.mode_manual_title') }}</h3>\n                            <p class=\"text-sm opacity-90\">{{ __('pcma.mode_manual_desc') }}</p>", 1),

    ('<h3 class="font-semibold">Mode Vocal</h3>\n                            <p class="text-sm opacity-90">Reconnaissance vocale intelligente</p>',
     "<h3 class=\"font-semibold\">{{ __('pcma.mode_vocal_title') }}</h3>\n                            <p class=\"text-sm opacity-90\">{{ __('pcma.mode_vocal_desc') }}</p>", 1),

    ("<h3 class=\"font-semibold\">Mode OCR</h3>\n                            <p class=\"text-sm opacity-90\">Scan d'image et extraction</p>",
     "<h3 class=\"font-semibold\">{{ __('pcma.mode_ocr_title') }}</h3>\n                            <p class=\"text-sm opacity-90\">{{ __('pcma.mode_ocr_desc') }}</p>", 1),

    ('<h3 class="font-semibold">Mode FHIR</h3>\n                            <p class="text-sm opacity-90">Import de données médicales</p>',
     "<h3 class=\"font-semibold\">{{ __('pcma.mode_fhir_title') }}</h3>\n                            <p class=\"text-sm opacity-90\">{{ __('pcma.mode_fhir_desc') }}</p>", 1),

    ('<h2 class="text-xl font-semibold text-indigo-900">Assistant IA PCMA</h2>',
     "<h2 class=\"text-xl font-semibold text-indigo-900\">{{ __('pcma.ai_assistant_title') }}</h2>", 1),

    ("<p class=\"text-indigo-700 mb-4\">Décrivez les résultats de l'examen médical pour une analyse automatique</p>",
     "<p class=\"text-indigo-700 mb-4\">{{ __('pcma.ai_assistant_desc') }}</p>", 1),

    ('placeholder="Exemple: Patient présente une tension artérielle de 120/80 mmHg, fréquence cardiaque de 65 bpm au repos. Pas d\'antécédents cardiovasculaires. Examen neurologique normal. Pas de douleurs musculo-squelettiques..."',
     'placeholder="{{ __(\'pcma.clinical_notes_placeholder\') }}"', 1),

    ("                                    Analyser avec l'IA\n",
     "                                    {{ __('pcma.analyze_ai_btn') }}\n", 1),

    ('                                    Effacer\n                                </button>\n                            </div>\n                            \n                            <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">\n                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Analyse IA</h3>',
     "                                    {{ __('pcma.clear_btn') }}\n                                </button>\n                            </div>\n                            \n                            <div id=\"ai-results\" class=\"hidden bg-white border border-gray-200 rounded-lg p-4\">\n                                <h3 class=\"text-lg font-semibold text-gray-900 mb-3\">{{ __('pcma.ai_results_title') }}</h3>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:100]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH01 OK", len(replacements), "replacements applied")
