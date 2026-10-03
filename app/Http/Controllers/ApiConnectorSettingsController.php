<?php

namespace App\Http\Controllers;

use App\Services\ApiConnectorState;
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
    ) {
        $this->authorizeAdmin($request);
        $connectors = $this->connectors($state, $rekognition, $signotec, $fifaId);

        return view('modules.api-connectors.index', compact('connectors'));
    }

    public function activation(
        Request $request,
        string $connector,
        ApiConnectorState $state,
        AwsRekognitionFaceMatcher $rekognition,
        SignotecSignatureProvider $signotec,
        FifaIdRegistry $fifaId,
    ) {
        $this->authorizeAdmin($request);
        $data = $request->validate(['enabled' => 'required|boolean']);
        $connectors = collect($this->connectors($state, $rekognition, $signotec, $fifaId))->keyBy('slug');
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
    ) {
        $this->authorizeAdmin($request);
        $items = collect($this->connectors($state, $rekognition, $signotec, $fifaId))->keyBy('slug');
        $item = $items->get($connector);
        abort_unless($item, 404);

        $result = match ($connector) {
            'aws_rekognition' => $rekognition->testConnection(),
            'signotec' => $signotec->testConnection(),
            'fifa_id' => $fifaId->isConfigured()
                ? ['ok' => true, 'message' => 'Configuration FIFA ID détectée ; test métier disponible depuis un dossier de licence.']
                : ['ok' => false, 'message' => 'Configuration FIFA ID incomplète.'],
            default => $item['configured']
                ? ['ok' => true, 'message' => 'Pré-requis de configuration détectés.']
                : ['ok' => false, 'message' => 'Pré-requis de configuration incomplets.'],
        };

        return back()->with($result['ok'] ? 'success' : 'error', $item['name'] . ' : ' . $result['message']);
    }

    private function connectors(ApiConnectorState $state, AwsRekognitionFaceMatcher $rekognition, SignotecSignatureProvider $signotec, FifaIdRegistry $fifaId): array
    {
        $aws = $rekognition->status();
        $signature = $signotec->status();

        return [
            $this->item('aws_rekognition', 'AWS Rekognition CompareFaces', 'Comparaison photo ↔ photo pour la revue d’identité des licences.', $rekognition->isConfigured(), $rekognition->isEnabled(), ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_REKOGNITION_SIMILARITY_THRESHOLD'], true, $aws['label']),
            $this->item('signotec', 'signotec Biometrics API', 'Comparaison dynamique de signatures via le bridge FIT sous licence signotec.', $signotec->isConfigured(), $signotec->isEnabled(), ['SIGNOTEC_BRIDGE_URL', 'SIGNOTEC_BRIDGE_TOKEN', 'SIGNOTEC_LICENSE_ID'], true, $signature['label']),
            $this->item('fifa_id', 'FIFA ID Registry', 'Contrôle d’identité FIFA pendant l’approbation des licences.', $fifaId->isConfigured(), $fifaId->isEnabled(), ['FIFA_ID_REGISTRY_URL', 'FIFA_ID_REGISTRY_TOKEN'], true),
            $this->item('fifa_connect', 'FIFA Connect', 'Échanges FIFA Connect et validation des identifiants/structures.', filled(config('services.fifa_connect.api_key')), $state->enabled('fifa_connect', false), ['FIFA_CONNECT_BASE_URL', 'FIFA_CONNECT_API_KEY'], false),
            $this->item('fifa_tms', 'FIFA TMS', 'Synchronisation des données de transfert lorsque les accès officiels sont disponibles.', filled(config('services.fifa_tms.api_key')) && !config('services.fifa_tms.mock_mode'), $state->enabled('fifa_tms', false), ['FIFA_TMS_BASE_URL', 'FIFA_TMS_API_KEY'], false),
            $this->item('fhir_hl7', 'FHIR / HL7', 'Interopérabilité avec EMR/LIS et systèmes cliniques.', filled(config('services.hl7_fhir.client_id')) && filled(config('services.hl7_fhir.client_secret')), $state->enabled('fhir_hl7', false), ['HL7_FHIR_BASE_URL', 'HL7_FHIR_CLIENT_ID', 'HL7_FHIR_CLIENT_SECRET'], false),
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
