# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h4 class="text-lg font-semibold text-yellow-800 mb-3">Panel de Debug</h4>',
     "<h4 class=\"text-lg font-semibold text-yellow-800 mb-3\">{{ __('pcma.debug_panel_title') }}</h4>", 1),
    ('<label class="block text-sm font-medium text-gray-700 mb-1">Clé API Google Speech-to-Text :</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{{ __('pcma.api_key_label') }}</label>", 1),
    ('placeholder="Entrez votre clé API Google Speech-to-Text"',
     'placeholder="{{ __(\'pcma.api_key_placeholder\') }}"', 1),
    ('                                     Tester la Clé API\n',
     "                                     {{ __('pcma.test_api_key_btn') }}\n", 1),
    ('                                    🗑️ Vider la Console\n',
     "                                    {{ __('pcma.clear_console_btn') }}\n", 1),
    ('                                    Test Analyse Vocale\n',
     "                                    {{ __('pcma.test_voice_analysis_btn') }}\n", 1),
    ('                                🗑️ Effacer Tout\n',
     "                                {{ __('pcma.clear_all_btn') }}\n", 1),

    ("<h2 class=\"text-xl font-semibold text-gray-800\"> Scan d'image avec OCR</h2>",
     "<h2 class=\"text-xl font-semibold text-gray-800\"> {{ __('pcma.ocr_scan_title') }}</h2>", 1),
    ('<p class="text-gray-600 mb-4">Téléchargez une image de document médical pour extraction automatique</p>',
     "<p class=\"text-gray-600 mb-4\">{{ __('pcma.ocr_upload_prompt') }}</p>", 1),
    ('<p class="text-gray-600">Cliquez pour sélectionner une image</p>',
     "<p class=\"text-gray-600\">{{ __('pcma.ocr_select_image') }}</p>", 1),
    ("<p class=\"text-sm text-gray-500\">PNG, JPG, PDF jusqu'à 10MB</p>",
     "<p class=\"text-sm text-gray-500\">{{ __('pcma.ocr_upload_size_hint') }}</p>", 1),
    ('alt="Aperçu">',
     "alt=\"{{ __('pcma.image_preview_alt') }}\">", 1),
    ('placeholder="Le texte extrait apparaîtra ici..."',
     'placeholder="{{ __(\'pcma.extracted_text_placeholder\') }}"', 1),
    ('                                 Extraire le texte (OCR)\n',
     "                                 {{ __('pcma.extract_text_btn') }}\n", 1),
    ('<h3 class="text-lg font-semibold text-gray-900 mb-3">Texte extrait</h3>',
     "<h3 class=\"text-lg font-semibold text-gray-900 mb-3\">{{ __('pcma.extracted_text_title') }}</h3>", 1),

    ('<h3 class="text-2xl font-bold text-gray-900"> Signature Médecin - PCMA</h3>',
     "<h3 class=\"text-2xl font-bold text-gray-900\"> {{ __('pcma.doctor_signoff_modal_title') }}</h3>", 1),
    ('<span class="font-semibold text-gray-700">FIFA ID du médecin :</span>',
     "<span class=\"font-semibold text-gray-700\">{{ __('pcma.doctor_fifa_id_label') }}</span>", 1),
    ('<span class="ml-2 text-gray-900" id="signoff-ip-address">Non renseignée</span>',
     "<span class=\"ml-2 text-gray-900\" id=\"signoff-ip-address\">{{ __('pcma.ip_not_provided') }}</span>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH13 OK", len(replacements), "replacements applied")
