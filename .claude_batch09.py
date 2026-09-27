# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h4 class="text-lg font-semibold text-green-900">🔬 Visualiseur DICOM</h4>',
     "<h4 class=\"text-lg font-semibold text-green-900\">{{ __('pcma.dicom_viewer_title') }}</h4>", 1),
    ("                                        Visualisez les fichiers d'imagerie médicale (DICOM, images) avec des outils d'analyse intégrés\n",
     "                                        {{ __('pcma.dicom_viewer_desc') }}\n", 1),
    ("                                                Sélectionner un fichier à visualiser\n",
     "                                                {{ __('pcma.select_file_viewer_label') }}\n", 1),
    ('<option value="">Choisir un fichier...</option>',
     "<option value=\"\">{{ __('pcma.choose_file_placeholder') }}</option>", 1),
    ('<option value="ecg">ECG - Fichier sélectionné</option>',
     "<option value=\"ecg\">{{ __('pcma.dicom_ecg_option') }}</option>", 1),
    ('<option value="mri">IRM - Fichier sélectionné</option>',
     "<option value=\"mri\">{{ __('pcma.dicom_mri_option') }}</option>", 1),
    ('<option value="xray">Radiographie - Fichier sélectionné</option>',
     "<option value=\"xray\">{{ __('pcma.dicom_xray_option') }}</option>", 1),
    ('<option value="ct">Scanner CT - Fichier sélectionné</option>',
     "<option value=\"ct\">{{ __('pcma.dicom_ct_option') }}</option>", 1),
    ('<option value="ultrasound">Échographie - Fichier sélectionné</option>',
     "<option value=\"ultrasound\">{{ __('pcma.dicom_us_option') }}</option>", 1),
    ("                                                Outils de visualisation\n",
     "                                                {{ __('pcma.viewer_tools_label') }}\n", 1),
    ("                                                Mesures\n",
     "                                                {{ __('pcma.measurements_label') }}\n", 1),
    ("<p>Chargement de l'image...</p>",
     "<p>{{ __('pcma.loading_image') }}</p>", 1),
    ('<p>Erreur de chargement</p>',
     "<p>{{ __('pcma.loading_error') }}</p>", 1),
    ('<p class="text-lg font-semibold">Visualiseur DICOM</p>',
     "<p class=\"text-lg font-semibold\">{{ __('pcma.dicom_placeholder_title') }}</p>", 1),
    ('<p class="text-sm">Sélectionnez un fichier pour commencer</p>',
     "<p class=\"text-sm\">{{ __('pcma.dicom_placeholder_desc') }}</p>", 1),
    ('<span id="dicom-info" class="text-sm">Aucun fichier sélectionné</span>',
     "<span id=\"dicom-info\" class=\"text-sm\">{{ __('pcma.no_file_selected') }}</span>", 1),
    ('<h5 class="font-semibold text-gray-900 mb-3"> Métadonnées DICOM</h5>',
     "<h5 class=\"font-semibold text-gray-900 mb-3\"> {{ __('pcma.dicom_metadata_title') }}</h5>", 1),
    ('<p><strong>Patient:</strong> <span id="dicom-patient-name">-</span></p>',
     "<p><strong>{{ __('pcma.patient_label') }}</strong> <span id=\"dicom-patient-name\">-</span></p>", 1),
    ('<p><strong>ID Patient:</strong> <span id="dicom-patient-id">-</span></p>',
     "<p><strong>{{ __('pcma.patient_id_label') }}</strong> <span id=\"dicom-patient-id\">-</span></p>", 1),
    ("<p><strong>Date d'examen:</strong> <span id=\"dicom-study-date\">-</span></p>",
     "<p><strong>{{ __('pcma.exam_date_label') }}</strong> <span id=\"dicom-study-date\">-</span></p>", 1),
    ('<p><strong>Médecin:</strong> <span id="dicom-physician">-</span></p>',
     "<p><strong>{{ __('pcma.physician_label') }}</strong> <span id=\"dicom-physician\">-</span></p>", 1),
    ('<h5 class="font-semibold text-gray-900 mb-3">📏 Outils de Mesure</h5>',
     "<h5 class=\"font-semibold text-gray-900 mb-3\">{{ __('pcma.measurement_tools_title') }}</h5>", 1),
    ('<label class="block text-sm font-medium text-gray-700 mb-2">Distance</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-2\">{{ __('pcma.distance_label') }}</label>", 1),
    ('<label class="block text-sm font-medium text-gray-700 mb-2">Angle</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-2\">{{ __('pcma.angle_label') }}</label>", 1),
    ('<label class="block text-sm font-medium text-gray-700 mb-2">Surface</label>',
     "<label class=\"block text-sm font-medium text-gray-700 mb-2\">{{ __('pcma.area_label') }}</label>", 1),
    ('                                                    📐 Surface\n',
     "                                                    {{ __('pcma.area_btn') }}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH09 OK", len(replacements), "replacements applied")
