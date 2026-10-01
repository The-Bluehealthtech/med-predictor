<?php

return [
    'version' => '2026-10-02.1',
    'models' => [
        'identity' => [
            'id' => 'fifa-connect-consistency-v1',
            'name' => 'FIFA Connect — cohérence identité / âge',
            'mode' => 'observation',
            'validation_status' => 'reference_only',
            'population' => 'Joueurs enregistrés, vigilance renforcée chez les mineurs',
            'source' => 'FIFA Connect',
            'url' => 'https://legal.fifa.com/advancing-football/fifa-connect/programme-details',
            'required_data' => [
                'date_of_birth' => 'Date de naissance',
                'fifa_connect_id' => 'Identifiant FIFA Connect',
                'passport' => 'Document/passeport joueur',
            ],
        ],
        'injury' => [
            'id' => 'ioc-football-injury-surveillance-v1',
            'name' => 'IOC / football — surveillance des blessures',
            'mode' => 'observation',
            'validation_status' => 'reference_only',
            'population' => 'Athlètes suivis longitudinalement',
            'source' => 'IOC + football injury consensus',
            'url' => 'https://bjsm.bmj.com/content/54/7/372',
            'required_data' => [
                'injury_history' => 'Historique structuré des blessures',
                'training_exposure' => 'Exposition individuelle entraînement',
                'match_exposure' => 'Exposition individuelle match',
                'time_loss' => 'Indisponibilité / retour au jeu',
            ],
        ],
        'cardiac' => [
            'id' => 'fifa-cardiac-screening-v1',
            'name' => 'FIFA — screening cardiovasculaire',
            'mode' => 'observation',
            'validation_status' => 'reference_only',
            'population' => 'Joueurs soumis à une surveillance cardiovasculaire',
            'source' => 'FIFA Health & Medical',
            'url' => 'https://inside.fifa.com/health-and-medical/education-awareness/sudden-cardiac-arrest',
            'required_data' => [
                'pcma' => 'PCMA / screening médical',
                'ecg' => 'ECG documenté',
                'medical_history' => 'Antécédents personnels et familiaux',
                'physical_examination' => 'Examen clinique ciblé',
            ],
        ],
    ],
];
