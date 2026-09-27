# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h2 class="text-xl font-semibold text-gray-800">Mode Vocal - Reconnaissance Intelligente</h2>',
     "<h2 class=\"text-xl font-semibold text-gray-800\">{{ __('pcma.vocal_mode_heading') }}</h2>", 1),

    ('<h3 class="text-lg font-semibold text-gray-800 mb-4">Assistant Vocal Google Speech-to-Text</h3>',
     "<h3 class=\"text-lg font-semibold text-gray-800 mb-4\">{{ __('pcma.vocal_assistant_title') }}</h3>", 1),

    ('<h4 class="text-md font-medium text-gray-700 mb-3">Configuration API Google</h4>',
     "<h4 class=\"text-md font-medium text-gray-700 mb-3\">{{ __('pcma.google_api_config_title') }}</h4>", 1),

    ('<p class="text-yellow-800 font-medium">⚠️ Clé API Google Speech-to-Text non configurée</p>\n                                <p class="text-yellow-800 text-sm">Aucune clé n\'est chargée automatiquement par le serveur. Saisissez votre propre clé API dans le panel de debug ci-dessous puis initialisez le service pour activer la reconnaissance vocale.</p>',
     "<p class=\"text-yellow-800 font-medium\">{{ __('pcma.api_key_warning_title') }}</p>\n                                <p class=\"text-yellow-800 text-sm\">{{ __('pcma.api_key_warning_desc') }}</p>", 1),

    ('<h4 class="text-md font-medium text-blue-700 mb-3">Initialiser le Service</h4>\n                            <button type="button" id="init-service" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">\n                                Tester le Service\n                            </button>\n                            <div id="service-status" class="mt-2 text-sm text-gray-600">Service non initialisé</div>',
     "<h4 class=\"text-md font-medium text-blue-700 mb-3\">{{ __('pcma.init_service_title') }}</h4>\n                            <button type=\"button\" id=\"init-service\" class=\"bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm\">\n                                {{ __('pcma.test_service_btn') }}\n                            </button>\n                            <div id=\"service-status\" class=\"mt-2 text-sm text-gray-600\">{{ __('pcma.service_not_initialized') }}</div>", 1),

    ('<h4 class="text-md font-medium text-blue-700 mb-3">Contrôles de Reconnaissance</h4>',
     "<h4 class=\"text-md font-medium text-blue-700 mb-3\">{{ __('pcma.recognition_controls_title') }}</h4>", 1),

    ('                                                                         Démarrer Reconnaissance\n',
     "                                                                         {{ __('pcma.start_recognition_btn') }}\n", 1),

    ('                                    Arrêter Reconnaissance\n',
     "                                    {{ __('pcma.stop_recognition_btn') }}\n", 1),

    ('<div id="voice-status" class="text-sm text-gray-500 mb-4">Service non initialisé</div>',
     "<div id=\"voice-status\" class=\"text-sm text-gray-500 mb-4\">{{ __('pcma.service_not_initialized') }}</div>", 1),

    ('<h4 class="font-medium text-blue-800 mb-3">Résultats de la reconnaissance :</h4>',
     "<h4 class=\"font-medium text-blue-800 mb-3\">{{ __('pcma.recognition_results_title') }}</h4>", 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-1">Nom du joueur</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{{ __('pcma.player_name_label') }}</label>", 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-1">Âge</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{{ __('pcma.age_label') }}</label>", 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-1">Position</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{{ __('pcma.position_label') }}</label>", 1),

    ('<label class="block text-sm font-medium text-gray-700 mb-1">Club</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{{ __('pcma.club_label') }}</label>", 1),

    ('<h4 class="font-medium text-gray-800 mb-2">Texte reconnu :</h4>',
     "<h4 class=\"font-medium text-gray-800 mb-2\">{{ __('pcma.recognized_text_title') }}</h4>", 1),

    ('<h5 class="font-medium text-gray-800 mb-2">Données extraites :</h5>',
     "<h5 class=\"font-medium text-gray-800 mb-2\">{{ __('pcma.extracted_data_title') }}</h5>", 1),

    ('<div><strong>Nom:</strong> <span id="extracted-name" class="text-gray-700">-</span></div>',
     "<div><strong>{{ __('pcma.name_short') }}</strong> <span id=\"extracted-name\" class=\"text-gray-700\">-</span></div>", 1),

    ('<div><strong>Âge:</strong> <span id="extracted-age" class="text-gray-700">-</span></div>',
     "<div><strong>{{ __('pcma.age_short') }}</strong> <span id=\"extracted-age\" class=\"text-gray-700\">-</span></div>", 1),

    ("                                 Appliquer les données extraites\n",
     "                                 {{ __('pcma.apply_extracted_data_btn') }}\n", 1),

    ("                                 Transférer vers le formulaire PCMA\n                            </button>\n                        </div>\n                    </div>\n                    \n                    <!-- MESSAGE IMPORTANT -->\n                    <div class=\"bg-blue-50 border border-blue-200 rounded-lg p-4\">\n                        <p class=\"text-blue-800 text-sm\">\n                            <strong>Note :</strong> Cette console vocale est connectée au formulaire PCMA du mode manuel. \n                            Les données reconnues seront automatiquement transférées dans les champs correspondants.\n                        </p>\n                    </div>",
     "                                 {{ __('pcma.transfer_to_form_btn') }}\n                            </button>\n                        </div>\n                    </div>\n                    \n                    <!-- MESSAGE IMPORTANT -->\n                    <div class=\"bg-blue-50 border border-blue-200 rounded-lg p-4\">\n                        <p class=\"text-blue-800 text-sm\">\n                            <strong>{{ __('pcma.vocal_console_note_label') }}</strong> {{ __('pcma.vocal_console_note_text') }}\n                        </p>\n                    </div>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH02 OK", len(replacements), "replacements applied")
