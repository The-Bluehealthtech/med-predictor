<?php

namespace App\Http\Controllers;

use App\Services\Licensing\AwsRekognitionFaceMatcher;
use App\Services\Licensing\FifaIdRegistry;
use App\Services\Licensing\SignotecSignatureProvider;
use Illuminate\Http\Request;

final class ApiConnectorSettingsController extends Controller
{
    public function index(
        Request $request,
        AwsRekognitionFaceMatcher $rekognition,
        SignotecSignatureProvider $signotec,
        FifaIdRegistry $fifaId,
    ) {
        abort_unless($request->user() && in_array($request->user()->role, ['system_admin', 'super_admin'], true), 403);

        $connectors = [
            [
                'name' => 'AWS Rekognition CompareFaces',
                'usage' => 'Comparaison photo ↔ photo pour la revue d’identité des licences.',
                'status' => $rekognition->status()['status'],
                'label' => $rekognition->status()['label'],
                'variables' => ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_REKOGNITION_SIMILARITY_THRESHOLD'],
            ],
            [
                'name' => 'signotec Biometrics API',
                'usage' => 'Comparaison dynamique de signatures via le bridge FIT sous licence signotec.',
                'status' => $signotec->status()['status'],
                'label' => $signotec->status()['label'],
                'variables' => ['SIGNOTEC_BRIDGE_URL', 'SIGNOTEC_BRIDGE_TOKEN', 'SIGNOTEC_LICENSE_ID'],
            ],
            [
                'name' => 'FIFA ID Registry',
                'usage' => 'Contrôle d’identité FIFA pendant l’approbation des licences.',
                'status' => $fifaId->isConfigured() ? 'ready' : 'not_configured',
                'label' => $fifaId->isConfigured() ? 'Registre configuré' : 'Registre non connecté',
                'variables' => ['FIFA_ID_REGISTRY_URL', 'FIFA_ID_REGISTRY_TOKEN'],
            ],
            $this->generic('FIFA Connect', 'Échanges FIFA Connect et validation des identifiants/structures.', ['FIFA_CONNECT_BASE_URL', 'FIFA_CONNECT_API_KEY'], [config('services.fifa_connect.base_url'), config('services.fifa_connect.api_key')]),
            $this->generic('FIFA TMS', 'Synchronisation des données de transfert lorsque les accès officiels sont disponibles.', ['FIFA_TMS_BASE_URL', 'FIFA_TMS_API_KEY'], [config('services.fifa_tms.base_url'), config('services.fifa_tms.api_key')]),
            $this->generic('FHIR / HL7', 'Interopérabilité avec EMR/LIS et systèmes cliniques.', ['FHIR_BASE_URL', 'HL7_FHIR_BASE_URL'], [config('services.fhir.base_url'), config('services.hl7_fhir.base_url')]),
            $this->generic('PACS / DICOMweb', 'Accès aux images et objets d’imagerie médicale.', ['PACS_BASE_URL', 'PACS_USERNAME', 'PACS_PASSWORD'], [config('services.pacs.base_url'), config('services.pacs.username'), config('services.pacs.password')]),
        ];

        return view('modules.api-connectors.index', compact('connectors'));
    }

    private function generic(string $name, string $usage, array $variables, array $values): array
    {
        $configured = collect($values)->contains(fn ($value) => filled($value));

        return [
            'name' => $name,
            'usage' => $usage,
            'status' => $configured ? 'partial' : 'not_configured',
            'label' => $configured ? 'Configuration détectée' : 'Non configuré',
            'variables' => $variables,
        ];
    }
}
