<?php

// Licences : vocabulaire de l'enregistrement FIFA Connect (registration.xsd, Data 3.3)
// et barème par défaut d'une fédération.
return [
    // GenderType (porté par la personne)
    'genders' => ['male' => 'Hommes', 'female' => 'Femmes'],

    // DisciplineType
    'disciplines' => ['Football' => 'Football (à 11)', 'Futsal' => 'Futsal', 'BeachSoccer' => 'Beach soccer'],

    // RegistrationLevelType (joueurs)
    'levels' => ['amateur' => 'Amateur', 'pro' => 'Professionnel'],

    // PlayerRegistrationNatureType
    'natures' => ['Registration' => 'Enregistrement', 'Loan' => 'Prêt'],

    // Types d'enregistrement couverts par une licence (MatchOfficial : arbitrage, hors périmètre club).
    'registration_types' => ['Player' => 'Joueur', 'TeamOfficial' => 'Officiel d’équipe', 'OrganisationOfficial' => 'Dirigeant'],

    // Pièces justificatives (réglage de la fédération, hors modèle FIFA Connect).
    'documents' => [
        'identity' => 'Pièce d’identité (passeport ou carte nationale)',
        'photo' => 'Photo d’identité récente',
        'medical' => 'Certificat médical d’aptitude',
        'parental' => 'Autorisation parentale (joueur mineur)',
        'contract' => 'Contrat professionnel signé',
        'loan_agreement' => 'Accord de prêt signé',
        'other' => 'Autre document',
    ],

    // Pièces ajoutées selon le niveau (pro) ou la nature (prêt), en plus de celles de la catégorie.
    'level_documents' => ['pro' => ['contract']],
    'nature_documents' => ['Loan' => ['loan_agreement']],

    // Pièces exigées pour une licence d'officiel d'équipe ou de dirigeant.
    'official_documents' => ['identity', 'photo'],

    // Barème par défaut, identique pour chaque genre et chaque discipline (modifiable à
    // l'écran « Barème des licences »). max_age : « moins de » à la date de référence ;
    // null = senior. Tarifs vides tant que la fédération ne les a pas saisis.
    // pcma_rule : none | pro | all.
    'default_scale' => [
        'currency' => 'EUR',
        'season_start_month' => 7,
        'season_start_day' => 1,
        'reference_month' => 1,
        'reference_day' => 1,
        'categories' => [
            ['code' => 'U15', 'label' => 'U-15', 'max_age' => 15, 'allowed_levels' => ['amateur'], 'pcma_rule' => 'none',
                'required_documents' => ['identity', 'photo', 'medical', 'parental']],
            ['code' => 'U17', 'label' => 'U-17', 'max_age' => 17, 'allowed_levels' => ['amateur', 'pro'], 'pcma_rule' => 'pro',
                'required_documents' => ['identity', 'photo', 'medical', 'parental']],
            ['code' => 'U19', 'label' => 'U-19', 'max_age' => 19, 'allowed_levels' => ['amateur', 'pro'], 'pcma_rule' => 'pro',
                'required_documents' => ['identity', 'photo', 'medical']],
            ['code' => 'U21', 'label' => 'U-21', 'max_age' => 21, 'allowed_levels' => ['amateur', 'pro'], 'pcma_rule' => 'pro',
                'required_documents' => ['identity', 'photo', 'medical']],
            ['code' => 'SENIOR', 'label' => 'Senior', 'max_age' => null, 'allowed_levels' => ['amateur', 'pro'], 'pcma_rule' => 'all',
                'required_documents' => ['identity', 'photo', 'medical']],
        ],
    ],

    // PCMA : la règle de chaque catégorie vient du barème. Un PCMA compte s'il est signé par
    // un médecin « apte » (ou apte avec restrictions), réalisé depuis moins de validity_months,
    // et non synthétique. Les licences d'officiels n'en demandent pas.
    'pcma' => ['validity_months' => 12],

    'max_kilobytes' => 5120,
    'mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
];
