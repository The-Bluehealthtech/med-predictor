<?php
// Version de collecte : noms issus des formulaires et colonnes existants.
// Aucun seuil médical ni résultat calculé n'est défini dans cette configuration.
$groups = [
    'dental'=>['column'=>'dental_records','date'=>'dental_appointment_date','fields'=>[
        'dental_data'=>'json','dental_notes'=>'text','dental_treatment_plan'=>'text',
        'dental_health_status'=>'text','icd_10_dental'=>'text',
        'dental_appointment_date'=>'date','dental_appointment_time'=>'time','dental_appointment_reason'=>'text']],
    'scat'=>['column'=>'scat_assessments','date'=>'scat_date','fields'=>[
        'scat_assessments'=>'text','scat_date'=>'date','scat_time'=>'time','scat_context'=>'text','scat_evaluator'=>'text',
        'scat_red_flags'=>'list','scat_observable_signs'=>'list','scat_cervical_normal'=>'boolean',
        'scat_diagnosis'=>'text','scat_follow_up_plan'=>'text',
        'scat_headache'=>'symptom','scat_nausea'=>'symptom','scat_dizziness'=>'symptom','scat_fatigue'=>'symptom',
        'scat_light_sensitivity'=>'symptom','scat_noise_sensitivity'=>'symptom',
        'scat_orientation_month'=>'text','scat_orientation_date'=>'text','scat_orientation_year'=>'text',
        'scat_orientation_time'=>'text','scat_orientation_day'=>'text',
        'scat_immediate_memory_trial1'=>'text','scat_immediate_memory_trial2'=>'text','scat_immediate_memory_trial3'=>'text',
        'scat_concentration_digits'=>'text','scat_concentration_months'=>'text','scat_delayed_recall'=>'text',
        'scat_mbess_firm_feet'=>'balance','scat_mbess_firm_single'=>'balance','scat_mbess_firm_tandem'=>'balance',
        'scat_mbess_foam_feet'=>'balance','scat_mbess_foam_single'=>'balance','scat_mbess_foam_tandem'=>'balance']],
    'mapa'=>['column'=>'mapa_results','date'=>'mapa_date','fields'=>[
        'mapa_date'=>'date','mapa_reason'=>'text','mapa_device'=>'text','mapa_conclusion'=>'text','mapa_dipping'=>'text',
        'mapa_pas_24h'=>'number','mapa_pad_24h'=>'number','mapa_fc_24h'=>'number',
        'mapa_pas_day'=>'number','mapa_pad_day'=>'number','mapa_fc_day'=>'number','mapa_load_day'=>'number',
        'mapa_pas_night'=>'number','mapa_pad_night'=>'number','mapa_fc_night'=>'number','mapa_load_night'=>'number',
        'mapa_day_start'=>'time','mapa_day_end'=>'time','mapa_night_start'=>'time','mapa_night_end'=>'time']],
    'imaging'=>['column'=>'imaging_results','date'=>'imaging_date','fields'=>[
        'imaging_data'=>'json','imaging_type'=>'text','imaging_date'=>'date','imaging_facility'=>'text',
        'imaging_radiologist'=>'text','imaging_indication'=>'text','imaging_technique'=>'text',
        'imaging_findings'=>'text','imaging_conclusion'=>'text']],
    'mri'=>['column'=>'mri_results','date'=>'mri_date','fields'=>[
        'mri_results'=>'text','mri_date'=>'date','mri_body_part'=>'text','mri_technique'=>'text',
        'mri_facility'=>'text','mri_radiologist'=>'text','mri_findings'=>'text']],
    'ecg_effort'=>['column'=>'ecg_effort_results','date'=>'ecg_effort_date','fields'=>[
        'ecg_effort_results'=>'text','ecg_effort_date'=>'date','ecg_effort_duration'=>'number',
        'ecg_effort_max_fc'=>'number','ecg_effort_interpretation'=>'text','ecg_effort_findings'=>'text']],
    'scintigraphy'=>['column'=>'scintigraphy_results','date'=>'scintigraphy_date','fields'=>[
        'scintigraphy_results'=>'text','scintigraphy_date'=>'date','scintigraphy_type'=>'text',
        'scintigraphy_isotope'=>'text','scintigraphy_facility'=>'text','scintigraphy_radiologist'=>'text',
        'scintigraphy_findings'=>'text']],
    'fmarc'=>['column'=>'fifa_fmarc_assessments','date'=>'injury_date','fields'=>[
        'injury_records'=>'text','injury_date'=>'date','injury_location'=>'text','injury_severity'=>'text',
        'injury_mechanism'=>'text','return_to_play_date'=>'date',
        // Champs explicitement visibles dans le PDF fourni, page 30 ; stockage JSON existant.
        'context'=>'text','match_minute'=>'nonnegative_integer','injury_type'=>'text',
        'absence_cause'=>'text','absence_days'=>'nonnegative_integer','expected_return_date'=>'date']],
    'illness'=>['column'=>'illness_records','date'=>null,'fields'=>[
        'diagnosis'=>'text','symptoms'=>'list','treatment_plan'=>'text','icd_10_primary_diagnosis'=>'text',
        // Cause et absence demandées par le formulaire F-MARC, PDF page 32.
        'illness_cause'=>'text','absence_days'=>'nonnegative_integer']],
    'biological'=>['column'=>'biological_profile','date'=>null,'fields'=>[]],
    'laboratory'=>['column'=>'blood_test_results','date'=>null,'fields'=>[
        'blood_test_panel'=>'text','blood_test_result_value'=>'text','blood_test_result_unit'=>'text',
        'blood_test_result_status'=>'text','blood_test_normal_range'=>'text','blood_test_reference'=>'text',
        'blood_test_interpretation'=>'text','laboratory_results'=>'text']],
];
$ui = [
    'dental' => [
        'groups' => [
            'État bucco-dentaire' => ['dental_health_status','dental_appointment_reason'],
            'Prise en charge' => ['dental_treatment_plan','icd_10_dental','dental_notes'],
        ],
        'controls' => [
            'dental_health_status' => ['type'=>'select','options'=>['Bon','Satisfaisant','À surveiller','Pathologie active','Urgence dentaire']],
            'dental_appointment_reason' => ['type'=>'select','options'=>['Contrôle systématique','Douleur','Traumatisme','Caries suspectées','Gencives / parodonte','Orthodontie','Prothèse / restauration','Autre']],
            'dental_treatment_plan' => ['type'=>'multiselect','options'=>['Surveillance','Hygiène bucco-dentaire','Détartrage','Soins conservateurs','Endodontie','Extraction','Orthodontie','Avis spécialisé']],
            'icd_10_dental' => ['type'=>'select','options'=>['K00-K14 — Affections de la cavité buccale','S02 — Traumatisme facial / dentaire','Z01.2 — Examen dentaire','Autre / non codé']],
            'dental_notes' => ['type'=>'select','options'=>['Aucune remarque particulière','Sensibilité','Douleur spontanée','Saignement gingival','Mobilité dentaire','Lésion muqueuse','Autre élément à documenter']],
        ],
    ],
    'scat' => [
        'groups' => [
            'Contexte' => ['scat_context','scat_evaluator','scat_cervical_normal'],
            'Signes d’alerte' => ['scat_red_flags','scat_observable_signs'],
            'Symptômes' => ['scat_headache','scat_nausea','scat_dizziness','scat_fatigue','scat_light_sensitivity','scat_noise_sensitivity'],
            'Orientation et mémoire' => ['scat_orientation_month','scat_orientation_date','scat_orientation_year','scat_orientation_time','scat_orientation_day','scat_immediate_memory_trial1','scat_immediate_memory_trial2','scat_immediate_memory_trial3','scat_concentration_digits','scat_concentration_months','scat_delayed_recall'],
            'Équilibre' => ['scat_mbess_firm_feet','scat_mbess_firm_single','scat_mbess_firm_tandem','scat_mbess_foam_feet','scat_mbess_foam_single','scat_mbess_foam_tandem'],
            'Conclusion' => ['scat_diagnosis','scat_follow_up_plan'],
        ],
        'controls' => [
            'scat_context' => ['type'=>'select','options'=>['Match','Entraînement','Compétition','Hors sport','Suivi']],
            'scat_evaluator' => ['type'=>'select','options'=>['Médecin','Kinésithérapeute','Professionnel de santé habilité']],
            'scat_red_flags' => ['type'=>'multiselect','options'=>['Douleur cervicale','Vision double','Faiblesse / paresthésies','Céphalée sévère ou croissante','Crise convulsive','Perte de connaissance','Vomissements','Agitation / comportement inhabituel']],
            'scat_observable_signs' => ['type'=>'multiselect','options'=>['Immobilité au sol','Désorientation','Trouble de l’équilibre','Regard vide','Lenteur à répondre','Confusion','Aucun signe observable']],
            'scat_diagnosis' => ['type'=>'select','options'=>['Évaluation rassurante','Commotion suspectée','Commotion probable','Commotion confirmée','Évaluation incomplète / à revoir']],
            'scat_follow_up_plan' => ['type'=>'multiselect','options'=>['Repos relatif','Surveillance','Réévaluation 24-48 h','Protocole retour progressif','Avis neurologique','Imagerie indiquée']],
        ],
    ],
    'mapa' => [
        'groups' => [
            'Indication' => ['mapa_reason','mapa_device','mapa_dipping'],
            'Moyennes 24 h' => ['mapa_pas_24h','mapa_pad_24h','mapa_fc_24h'],
            'Période diurne' => ['mapa_pas_day','mapa_pad_day','mapa_fc_day','mapa_load_day','mapa_day_start','mapa_day_end'],
            'Période nocturne' => ['mapa_pas_night','mapa_pad_night','mapa_fc_night','mapa_load_night','mapa_night_start','mapa_night_end'],
            'Conclusion' => ['mapa_conclusion'],
        ],
        'controls' => [
            'mapa_reason' => ['type'=>'select','options'=>['Dépistage','TA élevée en consultation','Suspicion HTA masquée','Contrôle traitement','Bilan pré-participation','Suivi cardiologique']],
            'mapa_device' => ['type'=>'select','options'=>['Appareil validé — automatique','Appareil ambulatoire — autre']],
            'mapa_dipping' => ['type'=>'select','options'=>['Dipper','Non-dipper','Extreme dipper','Reverse dipper','Non interprétable']],
            'mapa_conclusion' => ['type'=>'select','options'=>['Profil tensionnel normal','Valeurs élevées à confirmer','HTA diurne','HTA nocturne','Profil non-dipper','Examen non interprétable','Avis cardiologique requis']],
        ],
    ],
    'imaging' => [
        'groups' => [
            'Examen' => ['imaging_type','imaging_indication','imaging_technique'],
            'Compte rendu' => ['imaging_findings','imaging_conclusion','imaging_facility','imaging_radiologist'],
        ],
        'controls' => [
            'imaging_type' => ['type'=>'select','options'=>['Radiographie','Échographie','Scanner / CT','IRM','Scintigraphie','Autre imagerie']],
            'imaging_indication' => ['type'=>'select','options'=>['Traumatisme aigu','Douleur persistante','Suspicion fracture','Suspicion lésion musculaire','Suspicion lésion ligamentaire','Suivi lésion connue','Bilan préopératoire','Autre indication']],
            'imaging_technique' => ['type'=>'select','options'=>['Sans contraste','Avec contraste','Doppler','Dynamique / stress','Standard selon protocole']],
            'imaging_findings' => ['type'=>'select','options'=>['Pas d’anomalie significative','Lésion osseuse','Lésion musculaire','Lésion tendineuse','Lésion ligamentaire','Atteinte articulaire','Épanchement / inflammation','Autre anomalie']],
            'imaging_conclusion' => ['type'=>'select','options'=>['Examen normal','Anomalie mineure','Anomalie à surveiller','Lésion confirmée','Complément d’imagerie recommandé','Avis spécialisé recommandé']],
        ],
    ],
    'mri' => [
        'groups' => [
            'Examen' => ['mri_body_part','mri_technique'],
            'Résultat' => ['mri_findings','mri_results','mri_facility','mri_radiologist'],
        ],
        'controls' => [
            'mri_body_part' => ['type'=>'select','options'=>['Épaule','Coude','Poignet / main','Rachis cervical','Rachis thoracique','Rachis lombaire','Bassin / hanche','Cuisse','Genou','Jambe','Cheville','Pied','Autre']],
            'mri_technique' => ['type'=>'select','options'=>['IRM standard','IRM avec contraste','Arthro-IRM','IRM dynamique','Autre protocole']],
            'mri_findings' => ['type'=>'select','options'=>['Pas d’anomalie significative','Lésion musculaire','Lésion tendineuse','Lésion ligamentaire','Lésion méniscale / labrale','Lésion osseuse','Atteinte cartilagineuse','Inflammation / épanchement','Autre anomalie']],
            'mri_results' => ['type'=>'select','options'=>['Normal','Anomalie mineure','Lésion confirmée','Contrôle recommandé','Avis spécialisé recommandé']],
        ],
    ],
    'ecg_effort' => [
        'groups' => [
            'Épreuve d’effort' => ['ecg_effort_duration','ecg_effort_max_fc'],
            'Interprétation' => ['ecg_effort_interpretation','ecg_effort_findings','ecg_effort_results'],
        ],
        'controls' => [
            'ecg_effort_interpretation' => ['type'=>'select','options'=>['Épreuve normale','Épreuve sous-maximale','Réponse tensionnelle atypique','Trouble du rythme observé','Anomalie ST-T','Symptômes limitants','Examen non concluant']],
            'ecg_effort_findings' => ['type'=>'multiselect','options'=>['Aucune anomalie','Douleur thoracique','Dyspnée inhabituelle','Palpitations','Extrasystoles','Trouble du rythme','Modification ST-T','Réponse tensionnelle anormale']],
            'ecg_effort_results' => ['type'=>'select','options'=>['Aptitude sans restriction','Surveillance recommandée','Exploration complémentaire','Avis cardiologique requis','Non concluant']],
        ],
    ],
    'scintigraphy' => [
        'groups' => [
            'Examen' => ['scintigraphy_type','scintigraphy_isotope'],
            'Résultat' => ['scintigraphy_findings','scintigraphy_facility','scintigraphy_radiologist','scintigraphy_results'],
        ],
        'controls' => [
            'scintigraphy_type' => ['type'=>'select','options'=>['Osseuse corps entier','Osseuse ciblée','Myocardique','Autre']],
            'scintigraphy_isotope' => ['type'=>'select','options'=>['Technétium-99m','Thallium-201','Autre isotope']],
            'scintigraphy_findings' => ['type'=>'select','options'=>['Pas d’hyperfixation significative','Hyperfixation focale','Hyperfixation diffuse','Aspect compatible avec lésion de stress','Autre anomalie']],
            'scintigraphy_results' => ['type'=>'select','options'=>['Examen normal','Anomalie mineure','Lésion probable','Exploration complémentaire recommandée']],
        ],
    ],
    'fmarc' => [
        'groups' => [
            'Contexte de blessure' => ['context','match_minute','injury_mechanism'],
            'Classification' => ['injury_location','injury_type','injury_severity'],
            'Conséquences' => ['absence_cause','absence_days','expected_return_date','return_to_play_date','injury_records'],
        ],
        'controls' => [
            'context' => ['type'=>'select','options'=>['Match','Entraînement','Échauffement','Hors football']],
            'injury_mechanism' => ['type'=>'select','options'=>['Contact joueur','Contact objet / sol','Course / sprint','Changement de direction','Saut / réception','Tir / passe','Surmenage progressif','Sans contact identifiable','Autre']],
            'injury_type' => ['type'=>'select','options'=>['Contusion','Entorse / ligament','Muscle / tendon','Fracture','Luxation / subluxation','Lésion méniscale / cartilage','Plaie / abrasion','Commotion','Surcharge / douleur progressive','Autre']],
            'injury_severity' => ['type'=>'select','options'=>['0 jour — aucune absence','1-3 jours — minime','4-7 jours — légère','8-28 jours — modérée','>28 jours — sévère','Fin de saison / carrière']],
            'absence_cause' => ['type'=>'select','options'=>['Blessure actuelle','Complication','Récidive','Rééducation','Décision médicale']],
            'injury_records' => ['type'=>'select','options'=>['Nouvelle blessure','Récidive même site / même type','Aggravation d’une blessure connue','Suivi de récupération']],
        ],
    ],
    'illness' => [
        'groups' => [
            'Épisode' => ['diagnosis','icd_10_primary_diagnosis','illness_cause'],
            'Symptômes' => ['symptoms'],
            'Prise en charge' => ['treatment_plan','absence_days'],
        ],
        'controls' => [
            'diagnosis' => ['type'=>'select','options'=>['Infection respiratoire haute','Syndrome grippal / virose','Gastro-entérite','Infection digestive','Affection ORL','Affection dermatologique','Allergie / réaction hypersensible','Crise d’asthme / bronchospasme','Migraine / céphalée','Trouble musculosquelettique non traumatique','Fatigue / surmenage','Déshydratation / coup de chaleur','Trouble du sommeil','Autre pathologie']],
            'icd_10_primary_diagnosis' => ['type'=>'select','options'=>['J00-J06 — Infections respiratoires hautes','J10-J11 — Grippe','A00-A09 — Infections intestinales','J45 — Asthme','L00-L99 — Dermatologie','T78 — Allergie / hypersensibilité','G43-G44 — Migraine / céphalées','M00-M99 — Appareil locomoteur','E86 — Déshydratation','R53 — Fatigue / malaise','Autre / non codé']],
            'illness_cause' => ['type'=>'select','options'=>['Infectieuse probable','Allergique','Inflammatoire','Environnementale / chaleur','Nutrition / hydratation','Sommeil / récupération','Stress / charge','Cause indéterminée']],
            'symptoms' => ['type'=>'multiselect','options'=>['Fièvre','Frissons','Toux','Rhinorrhée / congestion','Mal de gorge','Dyspnée','Nausées','Vomissements','Diarrhée','Douleur abdominale','Céphalée','Myalgies','Fatigue','Éruption cutanée','Prurit','Vertiges','Autre symptôme']],
            'treatment_plan' => ['type'=>'multiselect','options'=>['Repos','Hydratation','Antalgique / antipyrétique','Traitement symptomatique','Traitement prescrit','Isolement / précautions','Examens biologiques','Avis spécialiste','Réévaluation médicale','Restriction sportive']],
        ],
    ],
    'laboratory' => [
        'groups' => [
            'Bilan' => ['blood_test_panel','blood_test_result_status','blood_test_interpretation'],
            'Résultat principal' => ['blood_test_result_value','blood_test_result_unit','blood_test_normal_range','blood_test_reference','laboratory_results'],
        ],
        'controls' => [
            'blood_test_panel' => ['type'=>'select','options'=>['NFS','Ionogramme','Fonction rénale','Fonction hépatique','Bilan inflammatoire','Bilan martial','Bilan thyroïdien','Bilan lipidique','Glycémie / HbA1c','Vitamine D','Bilan hormonal','Autre panel']],
            'blood_test_result_status' => ['type'=>'select','options'=>['Dans les limites du laboratoire','Hors limites du laboratoire','À contrôler','Non interprétable']],
            'blood_test_interpretation' => ['type'=>'select','options'=>['Sans anomalie notable','Anomalie isolée à surveiller','Contrôle biologique recommandé','Évaluation médicale complémentaire','Avis spécialisé']],
            'laboratory_results' => ['type'=>'select','options'=>['Bilan global rassurant','Anomalie biologique mineure','Anomalie biologique significative','Contrôle requis']],
        ],
    ],
];

return ['version'=>'2026-10-02.2','max_file_kb'=>10240,'max_files'=>10,'sections'=>$groups,'ui'=>$ui,
    'file_inputs'=>['imaging_file'=>'imaging','mri_files'=>'mri','ecg_file'=>'imaging',
        'ecg_effort_file'=>'ecg_effort','scintigraphy_file'=>'scintigraphy','ct_files'=>'imaging','xray_file'=>'imaging']];
