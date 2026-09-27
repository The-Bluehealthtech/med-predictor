# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("<h2 class=\"text-xl font-semibold text-gray-800\">Mode OCR - Scan d'image</h2>",
     "<h2 class=\"text-xl font-semibold text-gray-800\">{{ __('pcma.ocr_mode_heading') }}</h2>", 1),

    ('<p class="text-gray-600 mb-4">Téléchargez une image de document médical pour extraction automatique</p>\n                        \n                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">\n                            <input type="file" id="image-upload" accept="image/*" class="hidden">\n                            <label for="image-upload" class="cursor-pointer">\n                                <div class="text-center">\n                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">\n                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />\n                                    </svg>\n                                    <p class="text-gray-600">Cliquez pour sélectionner une image</p>\n                                </div>\n                            </label>\n                        </div>\n                        \n                        <!-- Bouton pour transférer vers le formulaire -->\n                        <button type="button" id="transfer-ocr-to-form-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hidden">\n                            Transférer vers le formulaire PCMA\n                        </button>',
     "<p class=\"text-gray-600 mb-4\">{{ __('pcma.ocr_upload_prompt') }}</p>\n                        \n                        <div class=\"border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4\">\n                            <input type=\"file\" id=\"image-upload\" accept=\"image/*\" class=\"hidden\">\n                            <label for=\"image-upload\" class=\"cursor-pointer\">\n                                <div class=\"text-center\">\n                                    <svg class=\"w-12 h-12 mx-auto text-gray-400 mb-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12\" />\n                                    </svg>\n                                    <p class=\"text-gray-600\">{{ __('pcma.ocr_select_image') }}</p>\n                                </div>\n                            </label>\n                        </div>\n                        \n                        <!-- Bouton pour transférer vers le formulaire -->\n                        <button type=\"button\" id=\"transfer-ocr-to-form-btn\" class=\"bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hidden\">\n                            {{ __('pcma.transfer_to_form_btn') }}\n                        </button>", 1),

    ('<h2 class="text-xl font-semibold text-gray-800">Mode FHIR - Import de données</h2>',
     "<h2 class=\"text-xl font-semibold text-gray-800\">{{ __('pcma.fhir_mode_heading') }}</h2>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH03 OK", len(replacements), "replacements applied")
