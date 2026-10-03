<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\PCMA;
use App\Models\Player;
use App\Services\MedicalFileStore;
use App\Services\MedicalImageRenderer;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Visionneuse commune des fichiers médicaux : examens joints au PCMA et documents reçus au
 * pré-accueil (PDF, images, TIFF/BMP, DICOM mono ou multi-images).
 */
class MedicalFileViewerController extends Controller
{
    public const PCMA_FIELDS = ['ecg_file' => 'ECG', 'mri_file' => 'IRM', 'xray_file' => 'Radiographie', 'ct_scan_file' => 'Scanner', 'ultrasound_file' => 'Échographie'];

    public function __construct(private readonly MedicalFileStore $store, private readonly MedicalImageRenderer $renderer)
    {
    }

    public function pcma(Request $request, PCMA $pcma, string $field)
    {
        $file = $this->pcmaFile($request, $pcma, $field);

        return $this->page($file, self::PCMA_FIELDS[$field] . ' — PCMA du ' . ($pcma->assessment_date?->format('d/m/Y') ?? '—'),
            route('medical-files.pcma.frame', [$pcma, $field]), route('pcma.file', [$pcma, $field]), route('pcma.show', $pcma));
    }

    public function pcmaFrame(Request $request, PCMA $pcma, string $field): Response
    {
        return $this->frame($request, $this->pcmaFile($request, $pcma, $field));
    }

    public function document(Request $request, Document $document)
    {
        $file = $this->documentFile($request, $document);

        return $this->page($file, ($document->description ?: $file['name']) . ' — document reçu à l’accueil',
            route('medical-files.document.frame', $document), route('medical-files.document.source', $document), url()->previous());
    }

    public function documentFrame(Request $request, Document $document): Response
    {
        return $this->frame($request, $this->documentFile($request, $document));
    }

    public function documentSource(Request $request, Document $document): Response
    {
        return $this->renderer->download($this->documentFile($request, $document));
    }

    private function page(array $file, string $title, string $frameUrl, string $sourceUrl, ?string $back)
    {
        $kind = $this->renderer->kind($file);
        $meta = $kind === 'dicom' ? $this->renderer->describe($file) : null;

        return view('medical-files.show', [
            'title' => $title, 'file' => ['name' => $file['name'], 'size' => strlen($file['bytes'])], 'kind' => $kind,
            'meta' => $meta, 'windows' => $this->renderer->windows($meta), 'frameUrl' => $frameUrl, 'sourceUrl' => $sourceUrl,
            'back' => $back && str_starts_with($back, url('/')) ? $back : null,
        ]);
    }

    private function frame(Request $request, array $file): Response
    {
        $data = $request->validate(['frame' => 'sometimes|integer|min:0|max:9999', 'center' => 'nullable|numeric|between:-100000,100000', 'width' => 'nullable|numeric|between:1,200000']);
        $png = match ($this->renderer->kind($file)) {
            'dicom' => $this->renderer->frame($file, (int) ($data['frame'] ?? 0), isset($data['center']) ? (float) $data['center'] : null, isset($data['width']) ? (float) $data['width'] : null),
            'raster' => $this->renderer->raster($file),
            default => null,
        };
        abort_unless($png, 422, 'Cette image ne peut pas être décodée par la visionneuse ; téléchargez le fichier source pour l’ouvrir sur une station adaptée.');

        return $this->renderer->png($png);
    }

    private function pcmaFile(Request $request, PCMA $pcma, string $field): array
    {
        abort_unless(isset(self::PCMA_FIELDS[$field]), 404);
        app(MedicalRecordAccess::class)->record($request->user(), $pcma);
        $file = $this->store->read($pcma->$field);
        abort_unless($file, 404, 'Fichier introuvable.');

        return $file;
    }

    /** Document du pré-accueil : secrétariat du club (ou de la fédération) du joueur, ou soignant habilité. */
    private function documentFile(Request $request, Document $document): array
    {
        $player = $document->visit?->athlete?->player_id ? Player::withoutGlobalScopes()->with('club')->find($document->visit->athlete->player_id) : null;
        abort_unless($player, 404);
        $user = $request->user();
        if (($user->role ?? null) === 'secretary') {
            $club = $player->club;
            abort_unless($club && (($user->club_id && (int) $user->club_id === (int) $club->id)
                || ($user->association_id && (int) $user->association_id === (int) $club->association_id)), 403);
        } else {
            app(MedicalRecordAccess::class)->authorize($user, $player, null);
        }
        $file = $this->store->read($document->file_path);
        abort_unless($file, 404, 'Fichier introuvable (document enregistré avant la conservation en base).');

        return $file;
    }
}
