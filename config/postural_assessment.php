<?php

return [
    'views' => ['anterior', 'posterior', 'left_lateral', 'right_lateral'],
    'sides' => ['left', 'right', 'bilateral', 'midline', 'not_applicable'],
    'severities' => ['trace', 'mild', 'moderate', 'marked'],
    'sources' => ['clinician', 'manual_measurement', 'assisted_analysis', 'imported'],

    'measurements' => [
        'head_forward_offset' => ['type' => 'offset', 'points' => 2, 'unit' => 'normalized', 'landmarks' => ['ear_tragus', 'shoulder_acromion']],
        'head_tilt_angle' => ['type' => 'angle', 'points' => 2, 'unit' => 'deg', 'landmarks' => ['left_eye', 'right_eye']],
        'shoulder_line_angle' => ['type' => 'angle', 'points' => 2, 'unit' => 'deg', 'landmarks' => ['left_acromion', 'right_acromion']],
        'scapular_distance' => ['type' => 'distance', 'points' => 2, 'unit' => 'normalized', 'landmarks' => ['scapula_reference', 'thoracic_midline']],
        'thoracic_kyphosis_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['upper_thoracic', 'thoracic_apex', 'lower_thoracic']],
        'lumbar_lordosis_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['upper_lumbar', 'lumbar_apex', 'sacral_reference']],
        'spinal_offset' => ['type' => 'offset', 'points' => 2, 'unit' => 'normalized', 'landmarks' => ['spinal_reference', 'body_midline']],
        'trunk_lateral_offset' => ['type' => 'offset', 'points' => 2, 'unit' => 'normalized', 'landmarks' => ['trunk_center', 'body_midline']],
        'pelvic_line_angle' => ['type' => 'angle', 'points' => 2, 'unit' => 'deg', 'landmarks' => ['left_asis', 'right_asis']],
        'pelvic_tilt_angle' => ['type' => 'angle', 'points' => 2, 'unit' => 'deg', 'landmarks' => ['asis', 'psis']],
        'hip_frontal_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['pelvis_reference', 'hip_center', 'knee_center']],
        'knee_frontal_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['hip_center', 'knee_center', 'ankle_center']],
        'knee_sagittal_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['hip_center', 'knee_center', 'ankle_center']],
        'rearfoot_angle' => ['type' => 'angle', 'points' => 3, 'unit' => 'deg', 'landmarks' => ['lower_leg_axis', 'calcaneus_upper', 'calcaneus_lower']],
        'foot_progression_angle' => ['type' => 'angle', 'points' => 2, 'unit' => 'deg', 'landmarks' => ['heel_center', 'second_toe']],
    ],

    'findings' => [
        'head_forward' => ['region' => 'head_neck', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'head_forward_offset'],
        'head_backward' => ['region' => 'head_neck', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'head_forward_offset'],
        'head_tilt' => ['region' => 'head_neck', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => 'head_tilt_angle'],
        'head_rotation' => ['region' => 'head_neck', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => null],

        'shoulder_elevation' => ['region' => 'shoulder', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => 'shoulder_line_angle'],
        'shoulder_depression' => ['region' => 'shoulder', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => 'shoulder_line_angle'],
        'shoulders_rounded' => ['region' => 'shoulder', 'views' => ['left_lateral','right_lateral'], 'sides' => ['bilateral'], 'measurement' => null],

        'scapular_winging' => ['region' => 'scapula', 'views' => ['posterior'], 'sides' => ['left','right','bilateral'], 'measurement' => 'scapular_distance'],
        'scapular_protraction' => ['region' => 'scapula', 'views' => ['posterior','left_lateral','right_lateral'], 'sides' => ['left','right'], 'measurement' => null],
        'scapular_retraction' => ['region' => 'scapula', 'views' => ['posterior'], 'sides' => ['left','right'], 'measurement' => null],

        'thoracic_kyphosis' => ['region' => 'thoracic_spine', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'thoracic_kyphosis_angle'],
        'lumbar_lordosis' => ['region' => 'lumbar_spine', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'lumbar_lordosis_angle'],
        'spinal_lateral_deviation' => ['region' => 'spine', 'views' => ['posterior'], 'sides' => ['left','right'], 'measurement' => 'spinal_offset'],
        'trunk_shift' => ['region' => 'trunk', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => 'trunk_lateral_offset'],

        'pelvic_obliquity' => ['region' => 'pelvis', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => 'pelvic_line_angle'],
        'pelvic_anterior_tilt' => ['region' => 'pelvis', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'pelvic_tilt_angle'],
        'pelvic_posterior_tilt' => ['region' => 'pelvis', 'views' => ['left_lateral','right_lateral'], 'sides' => ['midline'], 'measurement' => 'pelvic_tilt_angle'],

        'hip_adduction_posture' => ['region' => 'hip', 'views' => ['anterior'], 'sides' => ['left','right'], 'measurement' => 'hip_frontal_angle'],
        'hip_abduction_posture' => ['region' => 'hip', 'views' => ['anterior'], 'sides' => ['left','right'], 'measurement' => 'hip_frontal_angle'],
        'hip_internal_rotation' => ['region' => 'hip', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => null],
        'hip_external_rotation' => ['region' => 'hip', 'views' => ['anterior','posterior'], 'sides' => ['left','right'], 'measurement' => null],

        'knee_valgus' => ['region' => 'knee', 'views' => ['anterior'], 'sides' => ['left','right','bilateral'], 'measurement' => 'knee_frontal_angle'],
        'knee_varus' => ['region' => 'knee', 'views' => ['anterior'], 'sides' => ['left','right','bilateral'], 'measurement' => 'knee_frontal_angle'],
        'knee_flexion_posture' => ['region' => 'knee', 'views' => ['left_lateral','right_lateral'], 'sides' => ['left','right'], 'measurement' => 'knee_sagittal_angle'],
        'knee_hyperextension' => ['region' => 'knee', 'views' => ['left_lateral','right_lateral'], 'sides' => ['left','right'], 'measurement' => 'knee_sagittal_angle'],

        'ankle_pronation' => ['region' => 'ankle', 'views' => ['posterior'], 'sides' => ['left','right'], 'measurement' => 'rearfoot_angle'],
        'ankle_supination' => ['region' => 'ankle', 'views' => ['posterior'], 'sides' => ['left','right'], 'measurement' => 'rearfoot_angle'],

        'foot_internal_rotation' => ['region' => 'foot', 'views' => ['anterior'], 'sides' => ['left','right'], 'measurement' => 'foot_progression_angle'],
        'foot_external_rotation' => ['region' => 'foot', 'views' => ['anterior'], 'sides' => ['left','right'], 'measurement' => 'foot_progression_angle'],
        'flat_arch' => ['region' => 'foot', 'views' => ['left_lateral','right_lateral'], 'sides' => ['left','right'], 'measurement' => null],
        'high_arch' => ['region' => 'foot', 'views' => ['left_lateral','right_lateral'], 'sides' => ['left','right'], 'measurement' => null],
    ],
];
