<?php

// Serveur HL7 FHIR R4 dédié à FIT (HAPI FHIR, fhir-server/). FIT ne parle qu'à ce serveur ;
// les EMR, LIS, RIS et PACS l'alimentent par leurs propres connecteurs.
return [
    // Adresse privée du serveur (service privé Render), ex. http://fit-fhir:8080/fhir.
    'base_url' => env('FIT_FHIR_BASE_URL'),
    'timeout' => (int) env('FIT_FHIR_TIMEOUT', 20),

    'fhir_version' => '4.0.1',

    // Systèmes d'identifiants des Patients alimentés par FIT (domaine de FIT ; FIFA ne
    // publie pas de système FHIR pour le FIFA ID). Les sources gardent leurs propres systèmes.
    'identifier_systems' => [
        'player' => 'https://fit.tbhc.uk/fhir/sid/player',
        'fifa_id' => 'https://fit.tbhc.uk/fhir/sid/fifa-id',
    ],

    // Partage de documents (IHE MHD, option Comprehensive ; IPS selon IHE sIPS).
    'document_sharing' => [
        // OID racine de FIT comme source documentaire (SubmissionSet.sourceId, XDS sourceId) :
        // à obtenir auprès d'un registre d'OID ; aucune publication tant qu'il est vide.
        'source_oid' => env('FIT_FHIR_SOURCE_OID'),
        // Valeurs du domaine d'affinité (politique de partage) : texte tant que la fédération
        // n'a pas arrêté de codes ; les liaisons FHIR de ces éléments sont « example ».
        'facility_type' => env('FIT_FHIR_FACILITY_TYPE', 'Service médical de club de football'),
        'practice_setting' => env('FIT_FHIR_PRACTICE_SETTING', 'Médecine du sport'),
        // Langue des documents produits par FIT (BCP 47).
        'language' => 'fr-FR',
    ],

    // Imagerie : visionneuse du PACS appelée selon IHE RAD Invoke Image Display (IID), en HTTPS.
    'imaging' => [
        'iid_viewer_url' => env('FIT_IID_VIEWER_URL'),
    ],

    // Guides chargés sur le serveur (fhir-server/application.yaml) : versions identiques.
    'implementation_guides' => [
        'hl7.fhir.uv.ips' => '1.1.0',
        'ihe.iti.sips' => '1.0.0',
        'ihe.iti.mhd' => '4.2.4',
        'ihe.iti.pdqm' => '3.2.0',
        'ihe.iti.pixm' => '3.1.0',
        'ihe.iti.balp' => '1.1.4',
    ],

    // Un profil de chaque guide : sa présence sur le serveur prouve que le guide est installé.
    'profiles' => [
        'hl7.fhir.uv.ips' => 'http://hl7.org/fhir/uv/ips/StructureDefinition/Bundle-uv-ips',
        'ihe.iti.mhd' => 'https://profiles.ihe.net/ITI/MHD/StructureDefinition/IHE.MHD.Comprehensive.ProvideBundle',
        'ihe.iti.pdqm' => 'https://profiles.ihe.net/ITI/PDQm/StructureDefinition/IHE.PDQm.Patient',
        'ihe.iti.pixm' => 'https://profiles.ihe.net/ITI/PIXm/StructureDefinition/IHE.PIXm.Patient',
        'ihe.iti.balp' => 'https://profiles.ihe.net/ITI/BALP/StructureDefinition/IHE.BasicAudit.PatientRead',
    ],

    // Acteurs IHE / HL7 que le serveur doit tenir pour FIT : CapabilityStatement officiel
    // de chaque acteur (copié des guides publiés dans resources/fhir/ihe).
    'actors' => [
        'pdqm_supplier' => ['label' => 'PDQm Patient Demographics Supplier (ITI-78)', 'file' => 'CapabilityStatement-IHE.PDQm.PatientDemographicsSupplier.json'],
        'pixm_manager' => ['label' => 'PIXm Patient Identifier Cross-reference Manager (ITI-83, ITI-104)', 'file' => 'CapabilityStatement-IHE.PIXm.Manager.json'],
        'mhd_recipient' => ['label' => 'MHD Document Recipient, option Comprehensive (ITI-65)', 'file' => 'CapabilityStatement-IHE.MHD.DocumentRecipient.Comprehensive.json'],
        'mhd_responder' => ['label' => 'MHD Document Responder (ITI-66, ITI-67, ITI-68)', 'file' => 'CapabilityStatement-IHE.MHD.DocumentResponder.json'],
        'qedm_source' => ['label' => 'QEDm Clinical Data Source (PCC-44)', 'file' => 'CapabilityStatement-IHE.QEDm.Clinical-Data-Source.json'],
        'balp_repository' => ['label' => 'BALP Audit Record Repository (ITI-20)', 'file' => 'CapabilityStatement-IHE.BALP.ATNA.AuditRecordRepository.json'],
        'ips_server' => ['label' => 'IPS Server (HL7 IPS 1.1.0)', 'file' => 'CapabilityStatement-ips-server.json'],
    ],
];
