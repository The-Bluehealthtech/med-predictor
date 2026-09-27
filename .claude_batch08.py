# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="text-lg font-semibold text-indigo-900">Imagerie Médicale</h3>',
     "<h3 class=\"text-lg font-semibold text-indigo-900\">{{ __('pcma.imaging_title') }}</h3>", 1),
    ('<h4 class="font-semibold text-gray-900">Électrocardiogramme (ECG)</h4>',
     "<h4 class=\"font-semibold text-gray-900\">{{ __('pcma.ecg_upload_title') }}</h4>", 1),
    ("                                                Fichier ECG\n",
     "                                                {{ __('pcma.ecg_file_label') }}\n", 1),
    ('<p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG, BMP, TIFF, DICOM</p>',
     "<p class=\"text-xs text-gray-500 mt-1\">{{ __('pcma.accepted_formats') }}</p>", 2),
    ("                                                Date de l'ECG\n",
     "                                                {{ __('pcma.ecg_date_label') }}\n", 1),
    ("                                                Interprétation ECG\n",
     "                                                {{ __('pcma.ecg_interpretation_label') }}\n", 1),
    (">Bradycardie sinusale</option>", ">{{ __('pcma.ecg_sinus_brady_option') }}</option>", 1),
    (">Tachycardie sinusale</option>", ">{{ __('pcma.ecg_sinus_tachy_option') }}</option>", 1),
    (">Fibrillation auriculaire</option>", ">{{ __('pcma.ecg_afib_option') }}</option>", 1),
    (">Tachycardie ventriculaire</option>", ">{{ __('pcma.ecg_vtach_option') }}</option>", 1),
    (">Élévation du segment ST</option>", ">{{ __('pcma.ecg_st_elevation_option') }}</option>", 1),
    (">Dépression du segment ST</option>", ">{{ __('pcma.ecg_st_depression_option') }}</option>", 1),
    (">Prolongation QT</option>", ">{{ __('pcma.ecg_qt_prolongation_option') }}</option>", 1),
    (">Anormal (préciser)</option>", ">{{ __('pcma.ecg_abnormal_specify_option') }}</option>", 1),
    ("                                                Notes ECG\n",
     "                                                {{ __('pcma.ecg_notes_label') }}\n", 1),
    ('placeholder="Détails de l\'interprétation ECG..."',
     'placeholder="{{ __(\'pcma.ecg_notes_placeholder\') }}"', 1),

    ('<h4 class="font-semibold text-gray-900">🧠 Imagerie par Résonance Magnétique (IRM)</h4>',
     "<h4 class=\"font-semibold text-gray-900\">{{ __('pcma.mri_upload_title') }}</h4>", 1),
    ("                                                Fichier IRM\n",
     "                                                {{ __('pcma.mri_file_label') }}\n", 1),
    ("                                                Date de l'IRM\n",
     "                                                {{ __('pcma.mri_date_label') }}\n", 1),
    ("                                                Type d'IRM\n",
     "                                                {{ __('pcma.mri_type_label') }}\n", 1),
    (">IRM Cérébrale</option>", ">{{ __('pcma.mri_brain_option') }}</option>", 1),
    (">IRM Rachidienne</option>", ">{{ __('pcma.mri_spine_option') }}</option>", 1),
    (">IRM du Genou</option>", ">{{ __('pcma.mri_knee_option') }}</option>", 1),
    (">IRM de l'Épaule</option>", ">{{ __('pcma.mri_shoulder_option') }}</option>", 1),
    (">IRM de la Cheville</option>", ">{{ __('pcma.mri_ankle_option') }}</option>", 1),
    (">IRM de la Hanche</option>", ">{{ __('pcma.mri_hip_option') }}</option>", 1),
    (">IRM Cardiaque</option>", ">{{ __('pcma.mri_cardiac_option') }}</option>", 1),
    (">Autre</option>", ">{{ __('pcma.other_option') }}</option>", 2),
    ("                                                Résultats IRM\n",
     "                                                {{ __('pcma.mri_findings_label') }}\n", 1),
    (">Anomalie légère</option>", ">{{ __('pcma.mri_mild_abn_option') }}</option>", 1),
    (">Anomalie modérée</option>", ">{{ __('pcma.mri_moderate_abn_option') }}</option>", 1),
    (">Anomalie sévère</option>", ">{{ __('pcma.mri_severe_abn_option') }}</option>", 1),
    (">Fracture</option>", ">{{ __('pcma.fracture_option') }}</option>", 1),
    (">Tumeur</option>", ">{{ __('pcma.tumor_option') }}</option>", 1),
    (">Inflammation</option>", ">{{ __('pcma.inflammation_option') }}</option>", 1),
    (">Changements dégénératifs</option>", ">{{ __('pcma.degenerative_option') }}</option>", 1),
    ("                                                Notes IRM\n",
     "                                                {{ __('pcma.mri_notes_label') }}\n", 1),
    ('placeholder="Détails des résultats IRM..."',
     'placeholder="{{ __(\'pcma.mri_notes_placeholder\') }}"', 1),

    ("<h4 class=\"font-semibold text-gray-900 mb-3\"> Autres Examens d'Imagerie</h4>",
     "<h4 class=\"font-semibold text-gray-900 mb-3\"> {{ __('pcma.additional_imaging_title') }}</h4>", 1),
    ("                                            Radiographie (X-Ray)\n",
     "                                            {{ __('pcma.xray_label') }}\n", 1),
    ("                                            Notes Radiographie (X-Ray)\n",
     "                                            {{ __('pcma.xray_notes_label') }}\n", 1),
    ('placeholder="Résultats de l\'analyse radiographique..."',
     'placeholder="{{ __(\'pcma.xray_notes_placeholder\') }}"', 1),
    ("                                            Scanner (CT)\n",
     "                                            {{ __('pcma.ct_label') }}\n", 1),
    ("                                            Échographie\n",
     "                                            {{ __('pcma.ultrasound_label') }}\n", 1),
    ("                                            Notes Scanner (CT)\n",
     "                                            {{ __('pcma.ct_notes_label') }}\n", 1),
    ('placeholder="Résultats de l\'analyse scanner..."',
     'placeholder="{{ __(\'pcma.ct_notes_placeholder\') }}"', 1),
    ("                                            Notes Échographie\n",
     "                                            {{ __('pcma.ultrasound_notes_label') }}\n", 1),
    ('placeholder="Résultats de l\'analyse échographique..."',
     'placeholder="{{ __(\'pcma.ultrasound_notes_placeholder\') }}"', 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH08 OK", len(replacements), "replacements applied")
