# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<h3 class="text-lg font-semibold text-purple-900">🧠 Évaluation Neurologique</h3>',
     "<h3 class=\"text-lg font-semibold text-purple-900\">{{ __('pcma.neuro_assessment_title') }}</h3>", 1),
    ('<h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Cérébrale</h4>',
     "<h4 class=\"text-sm font-semibold text-gray-700 mb-3\">{{ __('pcma.brain_anatomy_title') }}</h4>", 1),
    ('<p class="text-xs text-gray-500 mt-2">Examen neurologique normal</p>',
     "<p class=\"text-xs text-gray-500 mt-2\">{{ __('pcma.neuro_exam_normal_note') }}</p>", 1),
    ('<text x="50" y="35" class="text-xs" fill="#6b7280">Frontal</text>',
     "<text x=\"50\" y=\"35\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.frontal_label') }}</text>", 1),
    ('<text x="140" y="85" class="text-xs" fill="#6b7280">Occipital</text>',
     "<text x=\"140\" y=\"85\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.occipital_label') }}</text>", 1),
    ('<text x="100" y="125" class="text-xs" fill="#6b7280">Tronc</text>',
     "<text x=\"100\" y=\"125\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.brainstem_label') }}</text>", 1),
    ("                                        Niveau de Conscience\n",
     "                                        {{ __('pcma.consciousness_label') }}\n", 1),
    (">Vigile</option>", ">{{ __('pcma.alert_state_option') }}</option>", 1),
    (">Confus</option>", ">{{ __('pcma.confused_state_option') }}</option>", 1),
    (">Somnolent</option>", ">{{ __('pcma.drowsy_state_option') }}</option>", 1),
    ("                                        Nerfs Crâniens\n",
     "                                        {{ __('pcma.cranial_nerves_label') }}\n", 1),
    (">Normaux</option>", ">{{ __('pcma.cranial_normal_option') }}</option>", 1),
    (">Anormaux</option>", ">{{ __('pcma.cranial_abnormal_option') }}</option>", 1),
    ("                                        Fonction Motrice\n",
     "                                        {{ __('pcma.motor_function_label') }}\n", 1),
    (">Faiblesse</option>", ">{{ __('pcma.weakness_option') }}</option>", 1),
    (">Paralysie</option>", ">{{ __('pcma.paralysis_option') }}</option>", 1),
    ("                                        Fonction Sensitive\n",
     "                                        {{ __('pcma.sensory_function_label') }}\n", 1),
    (">Normale</option>", ">{{ __('pcma.function_normal_option') }}</option>", 4),
    (">Diminuée</option>", ">{{ __('pcma.sensory_decreased_option') }}</option>", 1),
    (">Absente</option>", ">{{ __('pcma.sensory_absent_option') }}</option>", 1),

    ('<h3 class="text-lg font-semibold text-orange-900">💪 Évaluation Musculo-squelettique</h3>',
     "<h3 class=\"text-lg font-semibold text-orange-900\">{{ __('pcma.msk_assessment_title') }}</h3>", 1),
    ('<h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Musculo-squelettique</h4>',
     "<h4 class=\"text-sm font-semibold text-gray-700 mb-3\">{{ __('pcma.msk_anatomy_title') }}</h4>", 1),
    ('<p class="text-xs text-gray-500 mt-2">Examen musculo-squelettique normal</p>',
     "<p class=\"text-xs text-gray-500 mt-2\">{{ __('pcma.msk_exam_normal_note') }}</p>", 1),
    ('<text x="75" y="15" class="text-xs" fill="#6b7280">Tête</text>',
     "<text x=\"75\" y=\"15\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.head_label') }}</text>", 1),
    ('<text x="75" y="85" class="text-xs" fill="#6b7280">Tronc</text>',
     "<text x=\"75\" y=\"85\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.msk_torso_label') }}</text>", 1),
    ('<text x="15" y="85" class="text-xs" fill="#6b7280">Bras</text>',
     "<text x=\"15\" y=\"85\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.arm_label') }}</text>", 1),
    ('<text x="130" y="85" class="text-xs" fill="#6b7280">Bras</text>',
     "<text x=\"130\" y=\"85\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.arm_label') }}</text>", 1),
    ('<text x="55" y="140" class="text-xs" fill="#6b7280">Jambe</text>',
     "<text x=\"55\" y=\"140\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.leg_label') }}</text>", 1),
    ('<text x="85" y="140" class="text-xs" fill="#6b7280">Jambe</text>',
     "<text x=\"85\" y=\"140\" class=\"text-xs\" fill=\"#6b7280\">{{ __('pcma.leg_label') }}</text>", 1),
    ("                                        Mobilité Articulaire\n",
     "                                        {{ __('pcma.joint_mobility_label') }}\n", 1),
    (">Limitée</option>", ">{{ __('pcma.limited_option') }}</option>", 2),
    (">Restreinte</option>", ">{{ __('pcma.restricted_option') }}</option>", 2),
    ("                                        Force Musculaire\n",
     "                                        {{ __('pcma.muscle_strength_label') }}\n", 1),
    (">Réduite</option>", ">{{ __('pcma.reduced_option') }}</option>", 1),
    (">Faible</option>", ">{{ __('pcma.weak_option') }}</option>", 1),
    ("                                        Évaluation de la Douleur\n",
     "                                        {{ __('pcma.pain_assessment_label') }}\n", 1),
    (">Aucune</option>", ">{{ __('pcma.pain_none_option') }}</option>", 1),
    (">Légère</option>", ">{{ __('pcma.pain_mild_option') }}</option>", 1),
    (">Modérée</option>", ">{{ __('pcma.pain_moderate_option') }}</option>", 1),
    (">Sévère</option>", ">{{ __('pcma.pain_severe_option') }}</option>", 1),
    ("                                        Amplitude de Mouvement\n",
     "                                        {{ __('pcma.rom_label') }}\n", 1),
    (">Complète</option>", ">{{ __('pcma.rom_full_option') }}</option>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH11 OK", len(replacements), "replacements applied")
