<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Audit\Auditor;
use App\Services\DicomWeb\DicomWebClient;
use App\Services\Fhir\ClinicalDataQuery;
use App\Services\Fhir\FhirException;
use App\Services\MedicalImageRenderer;
use App\Services\MedicalRecordAccess;
use App\Services\Privacy\PlayerConsents;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Examens d'imagerie des établissements ouverts dans la visionneuse de FIT par DICOMweb
 * (IHE RAD WIA : QIDO-RS, WADO-RS). Accès : soignant du joueur, consentement au partage
 * (IHE PCF), examen présent dans les dossiers d'établissements rattachés au joueur (serveur
 * FHIR). Les images sont rendues à la volée par le décodeur de FIT et ne sont pas conservées.
 */
class ExternalImagingController extends Controller
{
    private const SESSION_TTL = 900; // autorisation d'un examen mémorisée 15 min pour les images

    public function __construct(private readonly DicomWebClient $pacs, private readonly MedicalImageRenderer $renderer)
    {
    }

    public function show(Request $request, Player $player, string $study, ClinicalDataQuery $query, Auditor $auditor)
    {
        $this->authorizePlayer($request, $player);
        abort_unless($this->pacs->configured(), 503, 'PACS DICOMweb non configuré (MEDICAL_PACS_DICOMWEB_URL en HTTPS).');
        try {
            $item = collect($query->fetch($player, 'imaging'))->firstWhere('study_uid', $study);
        } catch (FhirException $e) {
            abort(502, $e->getMessage());
        }
        abort_unless($item, 404, 'Examen absent des dossiers d’établissements du joueur.');
        $request->session()->put($this->sessionKey($player, $study), time());

        try {
            $series = $this->pacs->series($study);
            $current = collect($series)->firstWhere('uid', $request->query('series')) ?? ($series[0] ?? null);
            $images = [];
            $meta = null;
            if ($current) {
                foreach ($this->pacs->instances($study, $current['uid']) as $instance) {
                    for ($frame = 0; $frame < $instance['frames']; $frame++) {
                        $images[] = ['instance' => $instance['uid'], 'frame' => $frame];
                    }
                }
                if ($images) {
                    $dataset = $this->pacs->metadata($study, $current['uid'], $images[0]['instance']);
                    $meta = ['modality' => $current['modality'] ?? $this->pacs->value($dataset, '00080060'),
                        'window_center' => is_numeric($c = $this->pacs->value($dataset, '00281050')) ? (float) $c : null,
                        'window_width' => is_numeric($w = $this->pacs->value($dataset, '00281051')) ? (float) $w : null];
                }
            }
        } catch (RuntimeException $e) {
            abort(502, $e->getMessage());
        }
        $auditor->record(['event_type' => 'data_access', 'module' => 'imaging', 'action' => 'dicomweb_study_view',
            'description' => 'Examen d’imagerie d’établissement consulté (DICOMweb, IHE RAD WIA)', 'model' => $player, 'sensitive' => true,
            'metadata' => ['study_uid' => $study, 'series_uid' => $current['uid'] ?? null]]);

        return view('clinical.external-imaging', ['player' => $player, 'study' => $study, 'item' => $item, 'series' => $series, 'current' => $current,
            'images' => $images, 'windows' => $this->renderer->windows($meta), 'back' => $request->query('back')]);
    }

    public function frame(Request $request, Player $player, string $study, string $series, string $instance)
    {
        $this->authorizePlayer($request, $player);
        $granted = (int) $request->session()->get($this->sessionKey($player, $study), 0);
        abort_unless($granted && time() - $granted < self::SESSION_TTL, 403, 'Ouvrez l’examen depuis la liste des données des établissements.');
        $data = $request->validate(['frame' => 'sometimes|integer|min:0|max:9999', 'center' => 'nullable|numeric|between:-100000,100000', 'width' => 'nullable|numeric|between:1,200000']);
        try {
            $bytes = $this->pacs->retrieve($study, $series, $instance);
        } catch (RuntimeException $e) {
            abort(502, $e->getMessage());
        }
        $png = $this->renderer->frame(['bytes' => $bytes, 'name' => $instance . '.dcm', 'mime' => 'application/dicom'], (int) ($data['frame'] ?? 0),
            isset($data['center']) ? (float) $data['center'] : null, isset($data['width']) ? (float) $data['width'] : null);
        abort_unless($png, 422, 'Image non décodable par la visionneuse de FIT ; ouvrez-la dans la visionneuse du PACS.');

        return $this->renderer->png($png);
    }

    private function authorizePlayer(Request $request, Player $player): void
    {
        app(MedicalRecordAccess::class)->authorize($request->user(), $player, null);
        app(PlayerConsents::class)->assertExternalSharing($player);
    }

    private function sessionKey(Player $player, string $study): string
    {
        return 'dicomweb.study.' . $player->id . '.' . sha1($study);
    }
}
