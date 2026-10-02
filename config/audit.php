<?php

// Audit trail (table audit_logs, en ajout seul) : modèles suivis à la création,
// à la modification et à la suppression, et routes de consultation de données
// sensibles. « sensitive » : pour les données de santé, seuls les noms des
// champs modifiés sont journalisés, jamais leurs valeurs.
return [
    // Durée de conservation (années) avant purge par « php artisan audit:retention --force ».
    // Valeur par défaut à faire valider par le DPO / la direction ; en deçà, rien n'est effaçable.
    'retention_years' => (int) env('AUDIT_RETENTION_YEARS', 6),

    'models' => [
        App\Models\User::class => ['module' => 'comptes'],
        App\Models\Player::class => ['module' => 'joueurs'],
        App\Models\Club::class => ['module' => 'organisations'],
        App\Models\Association::class => ['module' => 'organisations'],
        App\Models\License::class => ['module' => 'licences'],
        App\Models\Transfer::class => ['module' => 'transferts'],
        App\Models\ClubOfficial::class => ['module' => 'organisations'],
        App\Models\NationalSelection::class => ['module' => 'selections'],
        App\Models\NationalSelectionReport::class => ['module' => 'selections'],
        App\Models\RoleConfigVersion::class => ['module' => 'performance'],
        App\Models\LicenseAgeCategory::class => ['module' => 'licences'],
        App\Models\LicenseScaleSetting::class => ['module' => 'licences'],
        App\Models\PlayerLicense::class => ['module' => 'licences'],
        App\Models\PCMA::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\HealthRecord::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\TUERequest::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\Appointment::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\Injury::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\Immunisation::class => ['module' => 'medical', 'sensitive' => true],
        App\Models\PassportAttestation::class => ['module' => 'medical', 'sensitive' => true],
    ],

    // Champs jamais copiés dans l'audit, quel que soit le modèle (en plus des champs $hidden).
    'redacted_fields' => ['password', 'remember_token', 'api_token', 'two_factor_secret', 'two_factor_recovery_codes', 'access_code', 'portal_access_code', 'token'],

    // Champs techniques mis à jour à chaque connexion : une modification qui ne touche qu'eux n'est pas journalisée.
    'noise_fields' => ['remember_token', 'last_login_at', 'last_login', 'last_activity', 'last_seen_at', 'login_count', 'updated_at'],

    // Consultations journalisées (GET, utilisateur connecté) : motifs de noms de route.
    // (les recherches d'autocomplétion, comme la CIM-11, ne sont pas journalisées)
    'sensitive_routes' => [
        'modules.healthcare.index', 'healthcare.index', 'healthcare.records.show', 'healthcare.records.edit', 'healthcare.export', 'healthcare.predictions',
        'health-records.index', 'health-records.show', 'health-records.edit', 'health-records.modules.show', 'health-records.view-hl7-cda', 'health-records.download-hl7-cda',
        'pcma.show', 'pcma.edit', 'pcma.pdf', 'pcma.index',
        'passports.medical.show', 'passports.medical.pdf', 'passports.medical.fhir',
        'medical-aut.pdf', 'medical-aut.edit', 'medical-aut.source',
        'licenses.document', // pièces de licence : certificat médical, pièce d'identité
        '/api/v1/passports/medical/*',
    ],
];
