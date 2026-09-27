# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('URL du serveur FHIR', "{{ __('pcma.fhir_server_url_label') }}", 2),
    ('ID du patient FHIR', "{{ __('pcma.fhir_patient_id_label') }}", 2),
    ('Type de ressource', "{{ __('pcma.fhir_resource_type_label') }}", 2),
    ('Observation (Observations médicales)</option>', "{{ __('pcma.fhir_option_observation') }}</option>", 2),
    ('Condition (Diagnostics)</option>', "{{ __('pcma.fhir_option_condition') }}</option>", 2),
    ('Procedure (Procédures)</option>', "{{ __('pcma.fhir_option_procedure') }}</option>", 2),
    (' Récupérer les données FHIR\n', " {{ __('pcma.fetch_fhir_btn') }}\n", 2),
    ('Données FHIR récupérées', "{{ __('pcma.fhir_results_title') }}", 2),
    ('<h2 class="text-xl font-semibold text-gray-800">📥 Téléchargement depuis FHIR</h2>',
     "<h2 class=\"text-xl font-semibold text-gray-800\">{{ __('pcma.fhir_download_title') }}</h2>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH04 OK", len(replacements), "replacements applied")
