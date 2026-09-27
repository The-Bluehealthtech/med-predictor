# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="text-lg font-semibold text-gray-900">Signes Vitaux</h3>',
     "<h3 class=\"text-lg font-semibold text-gray-900\">{{ __('pcma.vital_signs_title') }}</h3>", 1),
    ("                                        Tension Artérielle\n",
     "                                        {{ __('pcma.blood_pressure_label') }}\n", 1),
    ("                                        Fréquence Cardiaque\n",
     "                                        {{ __('pcma.heart_rate_label') }}\n", 1),
    ("                                        Température\n",
     "                                        {{ __('pcma.temperature_label') }}\n", 1),
    ("                                        Fréquence Respiratoire\n",
     "                                        {{ __('pcma.respiratory_rate_label') }}\n", 1),
    ("                                        Saturation O₂\n",
     "                                        {{ __('pcma.oxygen_saturation_label') }}\n", 1),
    ("                                        Poids\n",
     "                                        {{ __('pcma.weight_label') }}\n", 1),

    ('<h3 class="text-lg font-semibold text-green-900"> Antécédents Médicaux</h3>',
     "<h3 class=\"text-lg font-semibold text-green-900\"> {{ __('pcma.medical_history_title') }}</h3>", 1),
    ("                                        Antécédents Cardio-vasculaires\n",
     "                                        {{ __('pcma.cardiovascular_history_label') }}\n", 1),
    ('placeholder="Rechercher des conditions cardio-vasculaires..."',
     'placeholder="{{ __(\'pcma.cardiovascular_search_placeholder\') }}"', 1),
    ("                                        Antécédents Chirurgicaux\n",
     "                                        {{ __('pcma.surgical_history_label') }}\n", 1),
    ('placeholder="Rechercher des procédures chirurgicales..."',
     'placeholder="{{ __(\'pcma.surgical_search_placeholder\') }}"', 1),
    ("                                        Médicaments Actuels\n",
     "                                        {{ __('pcma.medications_label') }}\n", 1),
    ('placeholder="Rechercher des médicaments..."',
     'placeholder="{{ __(\'pcma.medication_search_placeholder\') }}"', 1),
    ("                                    Allergies\n",
     "                                    {{ __('pcma.allergies_label') }}\n", 1),
    ('placeholder="Rechercher des allergies..."',
     'placeholder="{{ __(\'pcma.allergy_search_placeholder\') }}"', 1),

    ('<h3 class="text-lg font-semibold text-gray-900">Examen Physique</h3>',
     "<h3 class=\"text-lg font-semibold text-gray-900\">{{ __('pcma.physical_exam_title') }}</h3>", 1),
    ("                                        Apparence Générale\n",
     "                                        {{ __('pcma.general_appearance_label') }}\n", 1),
    ("                                        Examen Cutané\n",
     "                                        {{ __('pcma.skin_exam_label') }}\n", 1),
    ("                                        Ganglions Lymphatiques\n",
     "                                        {{ __('pcma.lymph_nodes_label') }}\n", 1),
    (">Hypertrophiés</option>", ">{{ __('pcma.lymph_enlarged_option') }}</option>", 1),
    ("                                        Examen Abdominal\n",
     "                                        {{ __('pcma.abdomen_exam_label') }}\n", 1),

    ('<option value="">Sélectionner</option>', "<option value=\"\">{{ __('pcma.select_placeholder') }}</option>", 17),
    ('>Normal</option>', ">{{ __('pcma.normal_option') }}</option>", 6),
    ('>Anormal</option>', ">{{ __('pcma.abnormal_option') }}</option>", 3),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH07 OK", len(replacements), "replacements applied")
