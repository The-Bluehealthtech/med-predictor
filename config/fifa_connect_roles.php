<?php

// Rôles des officiels d'un club au format FIFA Connect (Data 3.3).
// « confirmed » : code présent dans les jeux de données FIFA Connect du dépôt (tests aller-retour).
// Les autres codes sont à confirmer contre le bundle XSD officiel (FIFA_CONNECT_XSD_PATH) ;
// quand il est installé, l'application valide les codes saisis contre TeamOfficialRoleType
// et OrganisationOfficialRoleType.
return [
    'team_official' => [
        'Coach' => ['label' => 'Entraîneur principal', 'confirmed' => true],
        'AssistantCoach' => ['label' => 'Entraîneur adjoint', 'confirmed' => false],
        'GoalkeeperCoach' => ['label' => 'Entraîneur des gardiens', 'confirmed' => false],
        'FitnessCoach' => ['label' => 'Préparateur physique', 'confirmed' => false],
        'TeamDoctor' => ['label' => 'Médecin d’équipe', 'confirmed' => true],
        'Physiotherapist' => ['label' => 'Kinésithérapeute', 'confirmed' => false],
        'TeamManager' => ['label' => 'Team manager', 'confirmed' => false],
        'Other' => ['label' => 'Autre officiel d\'équipe', 'confirmed' => false],
    ],
    'organisation_official' => [
        'President' => ['label' => 'Président', 'confirmed' => true],
        'VicePresident' => ['label' => 'Vice-président', 'confirmed' => false],
        'GeneralSecretary' => ['label' => 'Secrétaire général', 'confirmed' => false],
        'Treasurer' => ['label' => 'Trésorier', 'confirmed' => false],
        'TechnicalDirector' => ['label' => 'Directeur technique', 'confirmed' => false],
        'BoardMember' => ['label' => 'Membre du comité directeur', 'confirmed' => false],
        'Other' => ['label' => 'Autre dirigeant', 'confirmed' => false],
    ],
    'genders' => ['male' => 'Homme', 'female' => 'Femme'],
    'statuses' => ['active' => 'Actif', 'inactive' => 'Inactif'],
    'disciplines' => ['Football' => 'Football', 'Futsal' => 'Futsal', 'BeachSoccer' => 'Beach soccer'],
];
