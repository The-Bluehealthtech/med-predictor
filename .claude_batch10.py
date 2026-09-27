# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h4 class="text-lg font-semibold text-purple-900">🤖 Analyse IA - Med-Gemini</h4>',
     "<h4 class=\"text-lg font-semibold text-purple-900\">{{ __('pcma.ai_med_gemini_title') }}</h4>", 1),
    ("                                        Analyse automatique des fichiers ECG et IRM pour détecter les anomalies et évaluer l'âge osseux\n",
     "                                        {{ __('pcma.ai_analysis_desc') }}\n", 1),
    ('                                             Analyser ECG\n',
     "                                            {{ __('pcma.analyze_ecg_btn') }}\n", 1),
    ('                                            🧠 Analyser IRM (Âge Osseux)\n',
     "                                            {{ __('pcma.analyze_mri_btn') }}\n", 1),
    ('                                             Analyser X-Ray\n',
     "                                            {{ __('pcma.analyze_xray_btn') }}\n", 1),
    ('                                            🖥️ Analyser CT\n',
     "                                            {{ __('pcma.analyze_ct_btn') }}\n", 1),
    ('                                            🔊 Analyser Échographie\n',
     "                                            {{ __('pcma.analyze_us_btn') }}\n", 1),
    ('                                             Analyse Complète\n',
     "                                            {{ __('pcma.analyze_all_btn') }}\n", 1),
    ("<h5 class=\"font-semibold text-gray-900 mb-3\"> Résultats de l'Analyse IA</h5>",
     "<h5 class=\"font-semibold text-gray-900 mb-3\"> {{ __('pcma.ai_analysis_results_title') }}</h5>", 1),
    ('<span class="text-purple-600">Analyse en cours...</span>',
     "<span class=\"text-purple-600\">{{ __('pcma.analysis_in_progress') }}</span>", 1),

    ('<h3 class="text-lg font-semibold text-blue-900">❤️ Évaluation Cardiovasculaire</h3>',
     "<h3 class=\"text-lg font-semibold text-blue-900\">{{ __('pcma.cardio_assessment_title') }}</h3>", 1),
    ('<h4 class="text-sm font-semibold text-gray-700 mb-3">Électrocardiogramme (ECG)</h4>',
     "<h4 class=\"text-sm font-semibold text-gray-700 mb-3\">{{ __('pcma.ecg_upload_title') }}</h4>", 1),
    ('<p class="text-xs text-gray-500 mt-2">Rythme sinusal normal - 65 bpm</p>',
     "<p class=\"text-xs text-gray-500 mt-2\">{{ __('pcma.sinus_rhythm_note') }}</p>", 1),
    ("                                        Rythme Cardiaque\n",
     "                                        {{ __('pcma.cardiac_rhythm_label') }}\n", 1),
    (">Rythme sinusal</option>", ">{{ __('pcma.rhythm_sinus_option') }}</option>", 1),
    (">Rythme irrégulier</option>", ">{{ __('pcma.rhythm_irregular_option') }}</option>", 1),
    (">Arythmie</option>", ">{{ __('pcma.arrhythmia_option') }}</option>", 1),
    ("                                        Souffle Cardiaque\n",
     "                                        {{ __('pcma.heart_murmur_label') }}\n", 1),
    (">Aucun</option>", ">{{ __('pcma.murmur_none_option') }}</option>", 1),
    (">Systolique</option>", ">{{ __('pcma.murmur_systolic_option') }}</option>", 1),
    (">Diastolique</option>", ">{{ __('pcma.murmur_diastolic_option') }}</option>", 1),
    ("                                        Tension au Repos\n",
     "                                        {{ __('pcma.bp_rest_label') }}\n", 1),
    ("                                        Tension à l'Effort\n",
     "                                        {{ __('pcma.bp_exercise_label') }}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH10 OK", len(replacements), "replacements applied")
