<?php

namespace App\Http\Controllers;

use App\Services\ApiConnectorState;
use App\Services\Documents\AdobeSignProvider;
use App\Services\Documents\DocumentSignatureService;
use App\Services\Documents\DocumentSignatureStorage;
use App\Services\Licensing\AwsRekognitionFaceMatcher;
use App\Services\Licensing\FifaIdRegistry;
use App\Services\Licensing\SignotecSignatureProvider;
use Illuminate\Http\Request;

final class ApiConnectorSettingsController extends Controller
{
    public function index(
        Request $request,
        ApiConnectorState $state,
        AwsRekognitionFaceMatcher $rekognition,
        SignotecSignatureProvider $signotec,
        FifaIdRegistry $fifaId,
        DocumentSignatureService $documentSignatures,
        DocumentSignatureStorage $signatureStorage,
    ) {
        $this->authorizeAdmin($request);
        $connectors = $this->connectors($state, $rekognition, $signotec, $fifaId, $documentSignatures);
        $signatureStorageStatus = $signatureStorage->status();

        return view('modules.api-connectors.index', compact('connectors', 'signatureStorageStatus'));
    }

    public function activation(
        Request $request,
        string $connector,
        ApiConnectorState $state,
        AwsRekognitionFaceMatcher $rekognition,
        SignotecSignatureProvider $signotec,
        FifaIdRegistry $fifaId,
        DocumentSignatureService $documentSignatures,
    ) {
        $this->authorizeAdmin($request);
        $data = $request->validate(['enabled' => 'required|boolean']);
        $connectors = collect($this->connectors($state, $rekognition, $signotec, $fifaId, $documentSignatures))->keyBy('slug');
        $item = $connectors->get($connector);
        abort_unless($item, 404);

        $enable = (bool) $data['enabled'];
        if ($enable && !$item['configured']) {
            return back()->with('error', $item['name'] . ' ne peut pas être activé : configuration requise incomplète.');
        }

        $state->setEnabled($connector, $enable, $request->user()->id);

        return back()->with('success', $item['name'] . ($enable ? ' activé.' : ' désactivé.'));
    }

    public function test(
        Request $request,
        string $connector,
        ApiConnectorState $state,
        AwsRekognitionFaceMatcher $rekognition,
        SignotecSignatureProvider $signotec,
        FifaIdRegistry $fifaId,
        DocumentSignatureService $documentSignatures,
    ) {
        $this->authorizeAdmin($request);
        $items = collect($this->connectors($state, $rekognition, $signotec, $fifaId, $documentSignatures))->keyBy('slug');
        $item = $items->get($connector);
        abort_unless($item, 404);

        $result = match ($connector) {
            'aws_rekognition' => $rekognition->testConnection(),
            'signotec' => $signotec->testConnection(),
            'adobe_sign' => app(AdobeSignProvider::class)->testConnection(),
            'fifa_id' => $fifaId->isConfigured()
                ? ['ok' => true, 'message' => 'Configuration FIFA ID détectée ; test métier disponible depuis un dossier de licence.']
                : ['ok' => false, 'message' => 'Configuration FIFA ID incomplète.'],
            default => $item['configured']
                ? ['ok' => true, 'message' => 'Pré-requis de configuration détectés.']
                : ['ok' => false, 'message' => 'Pré-requis de configuration incomplets.'],
        };

        return back()->with($result['ok'] ? 'success' : 'error', $item['name'] . ' : ' . $result['message']);
    }

    private function connectors(ApiConnectorState $state, AwsRekognitionFaceMatcher $rekognition, SignotecSignatureProvider $signotec, FifaIdRegistry $fifaId, DocumentSignatureService $documentSignatures): array
    {
        $aws = $rekognition->status();
        $signature = $signotec->status();
        $documentProviders = collect($documentSignatures->allStatuses())->keyBy('slug');

        return [
            $this->item('aws_rekognition', 'AWS Rekognition CompareFaces', 'Comparaison photo ↔ photo pour la revue d’identité des licences.', $rekognition->isConfigured(), $rekognition->isEnabled(), ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_REKOGNITION_SIMILARITY_THRESHOLD'], true, $aws['label']),
            $this->item('signotec', 'signotec Biometrics API', 'Comparaison dynamique de signatures via le bridge FIT sous licence signotec.', $signotec->isConfigured(), $signotec->isEnabled(), ['SIGNOTEC_BRIDGE_URL', 'SIGNOTEC_BRIDGE_TOKEN', 'SIGNOTEC_LICENSE_ID'], true, $signature['label']),
            $this->item('signotec_document', 'signotec signoSign/Universal', 'Signature manuscrite, distante ou qualifiée des documents FIT, dont les PCMA.', (bool) $documentProviders['signotec_document']['configured'], (bool) $documentProviders['signotec_document']['enabled'], $documentProviders['signotec_document']['variables'], true, $documentProviders['signotec_document']['label']),
            $this->item('adobe_sign', 'Adobe Acrobat Sign', 'Circuit de signature électronique à distance des documents FIT par médecins, joueurs et dirigeants.', (bool) $documentProviders['adobe_sign']['configured'], (bool) $documentProviders['adobe_sign']['enabled'], $documentProviders['adobe_sign']['variables'], true, $documentProviders['adobe_sign']['label']),
            $this->item('globalsign_dss', 'GlobalSign DSS', 'Signature numérique PDF par certificat, horodatage et validation long terme des documents FIT.', (bool) $documentProviders['globalsign_dss']['configured'], (bool) $documentProviders['globalsign_dss']['enabled'], $documentProviders['globalsign_dss']['variables'], true, $documentProviders['globalsign_dss']['label']),
            $this->item('fifa_id', 'FIFA ID Registry', 'Contrôle d’identité FIFA pendant l’approbation des licences.', $fifaId->isConfigured(), $fifaId->isEnabled(), ['FIFA_ID_REGISTRY_URL', 'FIFA_ID_REGISTRY_TOKEN'], true),
            $this->item('fifa_connect', 'FIFA Connect', 'Échanges FIFA Connect et validation des identifiants/structures.', filled(config('services.fifa_connect.api_key')), $state->enabled('fifa_connect', false), ['FIFA_CONNECT_BASE_URL', 'FIFA_CONNECT_API_KEY'], false),
            $this->item('fifa_tms', 'FIFA TMS', 'Synchronisation des données de transfert lorsque les accès officiels sont disponibles.', filled(config('services.fifa_tms.api_key')) && !config('services.fifa_tms.mock_mode'), $state->enabled('fifa_tms', false), ['FIFA_TMS_BASE_URL', 'FIFA_TMS_API_KEY'], false),
            $this->item('fhir_hl7', 'Serveur FHIR de FIT (HL7 FHIR R4, IHE)', 'Serveur HAPI dédié alimenté par les EMR, LIS, RIS et PACS : identité clinique, IPS, examens et comptes rendus, consentement. Vérification et mise en service : page « Mise en service FHIR ».', filled(config('fhir.base_url')) && filled(config('fhir.webhook_secret')), $state->enabled('fhir_hl7', false), ['FIT_FHIR_BASE_URL', 'FIT_FHIR_WEBHOOK_SECRET', 'FIT_FHIR_SOURCE_OID', 'FIT_IID_VIEWER_URL', 'FIT_FHIR_TOKEN_URL', 'FIT_FHIR_CLIENT_ID', 'FIT_FHIR_CLIENT_SECRET', 'FIT_FHIR_SCOPE'], false),
            $this->item('pacs', 'PACS / DICOMweb', 'Accès aux images et objets d’imagerie médicale.', filled(config('services.pacs.username')) && filled(config('services.pacs.password')), $state->enabled('pacs', false), ['PACS_BASE_URL', 'PACS_USERNAME', 'PACS_PASSWORD'], false),
        ];
    }

    private function item(string $slug, string $name, string $usage, bool $configured, bool $enabled, array $variables, bool $runtimeEnforced, ?string $providerLabel = null): array
    {
        $status = !$configured ? 'not_configured' : ($enabled ? 'ready' : 'disabled');

        return compact('slug', 'name', 'usage', 'configured', 'enabled', 'variables') + [
            'status' => $status,
            'label' => $providerLabel ?: match ($status) {
                'ready' => 'Activé',
                'disabled' => 'Configuré · désactivé',
                default => 'Configuration requise',
            },
            'runtime_enforced' => $runtimeEnforced,
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user() && in_array($request->user()->role, ['system_admin', 'super_admin'], true), 403);
    }
}
