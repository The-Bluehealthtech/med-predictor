<?php

// Catalogue des modules de /modules (sections : clinique, performance, selections,
// administration). Partagé avec le tableau de bord général (/dashboard).
return [
    'modules' => [
        // LA CLINIQUE — staff médical uniquement
        [
            'name' => 'Prise en charge médicale',
            'description' => 'File d’attente clinique : prise en charge des joueurs transmis par le secrétariat',
            'icon' => 'stethoscope',
            'route' => 'modules.medical.index',
            'status' => 'active',
            'color' => 'red',
            'category' => 'clinique'
        ],
        [
            'name' => 'Dossiers médicaux',
            'description' => 'Dossier de santé de chaque joueur : diagnostics CIM-11, AUT, antidopage, dentaire, imagerie, médicaments',
            'icon' => 'folder',
            'route' => 'modules.healthcare.index',
            'status' => 'active',
            'color' => 'red',
            'category' => 'clinique'
        ],
        [
            'name' => 'Bilan médical pré-compétition (PCMA)',
            'description' => 'Évaluations médicales avant compétition (Pre-Competition Medical Assessment)',
            'icon' => 'clipboard-check',
            'route' => 'pcma.index',
            'status' => 'active',
            'color' => 'red',
            'category' => 'clinique'
        ],
        [
            'name' => 'Secrétariat médical',
            'description' => 'Accueil, rendez-vous et documents médicaux',
            'icon' => 'calendar',
            'route' => 'secretary.dashboard',
            'status' => 'active',
            'color' => 'red',
            'category' => 'clinique'
        ],

        [
            'name' => 'Passeport médical (IPS)',
            'description' => 'Résumé médical du joueur au format International Patient Summary, à partager lors d’un transfert, d’une sélection ou à sa demande',
            'icon' => 'passport',
            'route' => 'passports.medical.index',
            'status' => 'active',
            'color' => 'red',
            'category' => 'clinique'
        ],

        // LE CENTRE DE PERFORMANCE — staff sportif
        [
            'name' => 'Cockpit entraîneur',
            'description' => 'Performance de l’équipe, pronostic du prochain match, onze optimal et grille des postes',
            'icon' => 'target',
            'route' => 'modules.coach-cockpit',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],
        [
            'name' => 'Analyse des performances',
            'description' => 'Statistiques de saison issues des feuilles de match',
            'icon' => 'chart',
            'route' => 'performances.analytics',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],
        [
            'name' => 'Suivi de la charge (RPM)',
            'description' => 'Charge et état de forme des joueurs (intégration des capteurs à venir)',
            'icon' => 'gauge',
            'route' => 'rpm.index',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],
        [
            'name' => 'Saisie des métriques FIT',
            'description' => 'Saisie et vérification des métriques du score FIT canonique',
            'icon' => 'pencil',
            'route' => 'performances.fit-metrics',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],
        [
            'name' => 'Jumeau numérique (simulation)',
            'description' => 'Scénarios simulés à partir du profil de chaque joueur',
            'icon' => 'layers',
            'route' => 'analytics.digital-twin',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],
        [
            'name' => 'Appareils connectés',
            'description' => 'Montres, capteurs et appareils synchronisés par les joueurs',
            'icon' => 'watch',
            'route' => 'portal.devices',
            'status' => 'active',
            'color' => 'blue',
            'category' => 'performance'
        ],

        // LES SÉLECTIONS NATIONALES — deux espaces séparés par le RBAC
        [
            'name' => 'Convocations et retours',
            'description' => 'Convoquer des joueurs, recevoir l’état de départ du club et envoyer l’état de retour de sélection',
            'icon' => 'flag',
            'route' => 'dtn.index',
            'status' => 'active',
            'color' => 'indigo',
            'group' => 'dtn',
            'category' => 'selections'
        ],
        [
            'name' => 'Fiches joueurs',
            'description' => 'Données sportives des joueurs de tous les clubs pour préparer une convocation',
            'icon' => 'id-card',
            'route' => 'dtn.players.index',
            'status' => 'active',
            'color' => 'indigo',
            'group' => 'dtn',
            'category' => 'selections'
        ],
        [
            'name' => 'Accès API — DTN',
            'description' => 'Jetons et documentation de l’API pour le logiciel de la Direction technique nationale',
            'icon' => 'key',
            'route' => 'dtn.api-access',
            'status' => 'active',
            'color' => 'indigo',
            'group' => 'dtn',
            'category' => 'selections'
        ],
        [
            'name' => 'Convocations reçues',
            'description' => 'Convocations de vos joueurs par la DTN : préparer et envoyer l’état de départ',
            'icon' => 'mail',
            'route' => 'club.selections.index',
            'status' => 'active',
            'color' => 'emerald',
            'group' => 'club',
            'category' => 'selections'
        ],
        [
            'name' => 'Retours de sélection',
            'description' => 'États de retour reçus de la DTN : incidents, performances, risques, indice de performance',
            'icon' => 'clipboard-list',
            'route' => 'club.selections.returns',
            'status' => 'active',
            'color' => 'emerald',
            'group' => 'club',
            'category' => 'selections'
        ],
        [
            'name' => 'Accès API — club',
            'description' => 'Jetons et documentation de l’API pour le logiciel du club',
            'icon' => 'key',
            'route' => 'club.selections.api-access',
            'status' => 'active',
            'color' => 'emerald',
            'group' => 'club',
            'category' => 'selections'
        ],

        // L'ADMINISTRATION
        [
            'name' => 'Clubs',
            'description' => 'Gestion des clubs',
            'icon' => 'building',
            'route' => 'modules.clubs.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'organisations',
            'category' => 'administration'
        ],
        [
            'name' => 'Fédérations',
            'description' => 'Gestion des fédérations nationales',
            'icon' => 'landmark',
            'route' => 'modules.associations.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'organisations',
            'category' => 'administration'
        ],
        [
            'name' => 'Confédérations',
            'description' => 'Gestion des confédérations continentales',
            'icon' => 'globe',
            'route' => 'modules.confederations.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'organisations',
            'category' => 'administration'
        ],
        [
            'name' => 'Dirigeants et staff',
            'description' => 'Fiches des entraîneurs, du staff et des dirigeants de chaque club au format FIFA Connect',
            'icon' => 'users',
            'route' => 'club-officials.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'organisations',
            'category' => 'administration'
        ],
        [
            'name' => 'Joueurs',
            'description' => 'Liste des joueurs, fiches et licences',
            'icon' => 'users',
            'route' => 'modules.players.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'sport',
            'category' => 'administration'
        ],
        [
            'name' => 'Équipes',
            'description' => 'Gestion des équipes',
            'icon' => 'shield',
            'route' => 'modules.teams.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'sport',
            'category' => 'administration'
        ],
        [
            'name' => 'Compétitions',
            'description' => 'Gestion des compétitions',
            'icon' => 'trophy',
            'route' => 'modules.competitions.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'sport',
            'category' => 'administration'
        ],
        [
            'name' => 'Préparation Match Day',
            'description' => 'Cockpit de préparation du match : feuille de match, officiels, stade, Medical Matchday, contrôles pré-match et clôture documentaire',
            'icon' => 'clipboard-list',
            'route' => 'competition-management.matches.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'sport',
            'category' => 'administration'
        ],
        [
            'name' => 'Arbitres',
            'description' => 'Gestion des arbitres et officiels',
            'icon' => 'whistle',
            'route' => 'referee-portal.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'sport',
            'category' => 'administration'
        ],
        [
            'name' => 'Demande de licence',
            'description' => 'Côté club : licences des joueurs, officiels d’équipe et dirigeants (FIFA Connect) ; envoi à la fédération, suivi et compléments',
            'icon' => 'badge',
            'route' => 'modules.licenses.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'audience' => 'club',
            'category' => 'administration'
        ],
        [
            'name' => 'Approbation des licences',
            'description' => 'Côté fédération : examiner la demande, vérifier l’identité via FIFA ID (facultatif), approuver, demander un complément ou refuser',
            'icon' => 'check-circle',
            'route' => 'licenses.validation',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'audience' => 'federation',
            'category' => 'administration'
        ],
        [
            'name' => 'Barème des licences',
            'description' => 'Côté fédération : par genre et discipline (FIFA Connect), catégories d’âge de U-15 à senior, niveaux, tarifs, PCMA, pièces ; tarifs des officiels',
            'icon' => 'clipboard-check',
            'route' => 'licenses.scale',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'category' => 'administration',
            'audience' => 'federation',
        ],
        [
            'name' => 'Politique de confidentialité',
            'description' => 'Côté fédération : texte versionné présenté au joueur lorsqu’il consent au partage de ses données de santé hors du club (IHE PCF)',
            'icon' => 'shield',
            'route' => 'privacy-policies.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'category' => 'administration',
            'audience' => 'federation',
        ],
        [
            'name' => 'Transferts',
            'description' => 'Gestion des transferts de joueurs',
            'icon' => 'transfer',
            'route' => 'admin.transfer-management.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'category' => 'administration'
        ],
        [
            'name' => 'Passeport de transfert',
            'description' => 'Passeport joueur au format FIFA : clubs d’enregistrement, statut, transferts et ITC',
            'icon' => 'passport',
            'route' => 'passports.transfer.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'category' => 'administration'
        ],
        [
            'name' => 'FIFA Connect · FIFA ID',
            'description' => 'Identifiants FIFA des joueurs, clubs et fédérations ; registre d’identité externe consulté (facultatif) pour approuver les licences',
            'icon' => 'globe',
            'route' => 'fifa.dashboard',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'licences',
            'category' => 'administration'
        ],
        [
            'name' => 'Finance',
            'description' => 'Gestion financière et comptabilité',
            'icon' => 'banknote',
            'route' => 'modules.finance.dashboard',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'finance',
            'category' => 'administration'
        ],
        [
            'name' => 'Configuration des API',
            'description' => 'État et configuration des connecteurs externes : biométrie, FIFA, FHIR/HL7, PACS et autres API',
            'icon' => 'key',
            'route' => 'modules.api-connectors.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'systeme',
            'category' => 'administration'
        ],
        [
            'name' => 'Administration du système',
            'description' => 'Comptes, demandes d’accès, journal d’audit et paramètres',
            'icon' => 'sliders',
            'route' => 'modules.administration.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'systeme',
            'category' => 'administration'
        ],
        [
            'name' => 'Contenu du site',
            'description' => 'Articles, pages et médias du site',
            'icon' => 'file',
            'route' => 'admin.content-management.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'systeme',
            'category' => 'administration'
        ],
        [
            'name' => 'IA Gemini',
            'description' => 'Configuration du modèle d’IA Google Gemini',
            'icon' => 'chip',
            'route' => 'gemini.index',
            'status' => 'active',
            'color' => 'gray',
            'group' => 'systeme',
            'category' => 'administration'
        ],
    ],
];
