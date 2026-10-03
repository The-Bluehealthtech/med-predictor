<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class TransferDocumentController extends Controller
{
    private const TYPES = [
        'passport' => 'Passeport',
        'contract' => 'Contrat',
        'parental_consent' => 'Consentement parental',
        'work_permit' => 'Permis de travail',
        'identity_card' => 'Carte d’identité',
        'birth_certificate' => 'Acte de naissance',
        'transfer_form' => 'Formulaire de transfert',
    ];

    public function store(Request $request, Transfer $transfer)
    {
        $this->authorizeUpload($request, $transfer);
        abort_unless(in_array($transfer->transfer_status, ['draft', 'rejected'], true), 409);

        $data = $request->validate([
            'document_type' => 'required|in:'.implode(',', array_keys(self::TYPES)),
            'file' => 'required|file|max:10240|mimetypes:application/pdf,image/jpeg,image/png',
        ]);

        $disk = (string) config('services.transfers.document_disk', 'local');
        abort_unless(array_key_exists($disk, (array) config('filesystems.disks', [])), 503, 'Stockage privé des pièces de transfert non configuré.');

        $file = $data['file'];
        $bytes = $file->get();
        $extension = $file->guessExtension() ?: 'bin';
        $path = 'transfers/'.$transfer->id.'/documents/'.Str::uuid().'.'.$extension;

        abort_unless(Storage::disk($disk)->put($path, $bytes, ['visibility' => 'private']), 503, 'Impossible de stocker la pièce de transfert.');

        try {
            $document = DB::transaction(function () use ($transfer, $request, $data, $file, $disk, $path, $bytes) {
                TransferDocument::query()
                    ->where('transfer_id', $transfer->id)
                    ->where('document_type', $data['document_type'])
                    ->whereIn('validation_status', ['pending', 'approved'])
                    ->update([
                        'validation_status' => 'expired',
                        'validation_notes' => 'Version remplacée par un nouveau dépôt.',
                        'validated_at' => now(),
                    ]);

                return TransferDocument::query()->create([
                    'transfer_id' => $transfer->id,
                    'uploaded_by' => $request->user()->id,
                    'document_type' => $data['document_type'],
                    'document_name' => self::TYPES[$data['document_type']],
                    'file_path' => $path,
                    'storage_disk' => $disk,
                    'file_name' => mb_substr(basename($file->getClientOriginalName()), 0, 255),
                    'mime_type' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
                    'file_size' => strlen($bytes),
                    'sha256' => hash('sha256', $bytes),
                    'validation_status' => 'pending',
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk($disk)->delete($path);
            throw $e;
        }

        return back()->with('success', $document->document_type_label.' déposée. Validation fédération requise.');
    }

    public function decision(Request $request, Transfer $transfer, TransferDocument $document)
    {
        $this->authorizeValidation($request, $transfer);
        $this->assertDocument($transfer, $document);
        abort_unless($document->validation_status === 'pending', 409);

        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:2000|required_if:decision,reject',
        ]);

        if ($data['decision'] === 'approve') {
            $document->approve($request->user(), $data['notes'] ?? null);
            return back()->with('success', $document->document_type_label.' approuvée.');
        }

        $document->reject($request->user(), trim((string) $data['notes']));
        return back()->with('success', $document->document_type_label.' refusée. Le club doit déposer une nouvelle version.');
    }

    public function download(Request $request, Transfer $transfer, TransferDocument $document)
    {
        $this->authorizeDownload($request, $transfer);
        $this->assertDocument($transfer, $document);

        $disk = $document->storage_disk ?: 'local';
        abort_unless(array_key_exists($disk, (array) config('filesystems.disks', [])), 404);
        abort_unless($document->file_path && !str_contains($document->file_path, '..'), 404);
        abort_unless(Storage::disk($disk)->exists($document->file_path), 404);

        return Storage::disk($disk)->download($document->file_path, $document->file_name, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeUpload(Request $request, Transfer $transfer): void
    {
        $user = $request->user();
        abort_unless($user, 401);

        if (in_array($user->role, ['club_admin', 'club_manager'], true)) {
            abort_unless($user->club_id && in_array((int) $user->club_id, [(int) $transfer->club_origin_id, (int) $transfer->club_destination_id], true), 403);
            return;
        }

        if (in_array($user->role, ['association_admin', 'association_registrar'], true)) {
            abort_unless($this->associationMatches($user->association_id, $transfer), 403);
            return;
        }

        abort(403);
    }

    private function authorizeValidation(Request $request, Transfer $transfer): void
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['association_admin', 'association_registrar'], true), 403);
        abort_unless($this->associationMatches($user->association_id, $transfer), 403);
    }

    private function authorizeDownload(Request $request, Transfer $transfer): void
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }
        if (in_array($user->role, ['club_admin', 'club_manager'], true)
            && $user->club_id
            && in_array((int) $user->club_id, [(int) $transfer->club_origin_id, (int) $transfer->club_destination_id], true)) {
            return;
        }
        if (in_array($user->role, ['association_admin', 'association_registrar'], true)
            && $this->associationMatches($user->association_id, $transfer)) {
            return;
        }

        abort(403);
    }

    private function associationMatches(?int $associationId, Transfer $transfer): bool
    {
        if (!$associationId) {
            return false;
        }

        $associations = DB::table('clubs')
            ->whereIn('id', [$transfer->club_origin_id, $transfer->club_destination_id])
            ->pluck('association_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        return in_array((int) $associationId, $associations, true);
    }

    private function assertDocument(Transfer $transfer, TransferDocument $document): void
    {
        abort_unless((int) $document->transfer_id === (int) $transfer->id, 404);
    }
}
