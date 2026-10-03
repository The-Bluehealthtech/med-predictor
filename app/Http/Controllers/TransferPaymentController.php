<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class TransferPaymentController extends Controller
{
    public function index(Request $request, Transfer $transfer)
    {
        $this->authorizeRead($request, $transfer);

        return response()->json([
            'data' => $transfer->payments()->with(['payer:id,name', 'payee:id,name'])->orderBy('due_date')->get(),
        ]);
    }

    public function store(Request $request, Transfer $transfer)
    {
        $this->authorizeAssociation($request, $transfer);

        $data = $request->validate([
            'payer_id' => 'required|exists:clubs,id',
            'payee_id' => 'required|exists:clubs,id|different:payer_id',
            'payment_type' => 'required|in:transfer_fee,training_compensation,solidarity_contribution,other',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|in:bank_transfer,check,cash,other',
            'due_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:2000',
        ]);

        $payment = $transfer->payments()->create($data + [
            'payment_status' => 'pending',
            'proof_status' => 'missing',
            'created_by' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json(['data' => $payment], 201)
            : back()->with('success', 'Échéance de paiement ajoutée au dossier FIT.');
    }

    public function show(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->authorizeRead($request, $transfer);
        $this->assertPayment($transfer, $payment);

        return response()->json(['data' => $payment->load(['payer:id,name', 'payee:id,name'])]);
    }

    public function update(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->authorizeAssociation($request, $transfer);
        $this->assertPayment($transfer, $payment);
        abort_unless($payment->proof_status === 'missing' && $payment->payment_status === 'pending', 409);

        $data = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
            'payment_method' => 'sometimes|in:bank_transfer,check,cash,other',
            'due_date' => 'sometimes|date',
            'reference_number' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:2000',
        ]);
        $payment->update($data);

        return response()->json(['data' => $payment->fresh()]);
    }

    public function destroy(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->authorizeAssociation($request, $transfer);
        $this->assertPayment($transfer, $payment);
        abort_unless($payment->proof_status === 'missing' && $payment->payment_status === 'pending', 409);
        $payment->delete();

        return response()->json(['success' => true]);
    }

    public function uploadProof(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->assertPayment($transfer, $payment);
        $this->authorizeProofUpload($request, $transfer, $payment);

        $data = $request->validate([
            'file' => 'required|file|max:10240|mimetypes:application/pdf,image/jpeg,image/png',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $disk = (string) config('services.transfers.document_disk', 'local');
        abort_unless(array_key_exists($disk, (array) config('filesystems.disks', [])), 503);

        $file = $data['file'];
        $bytes = $file->get();
        $path = 'transfers/'.$transfer->id.'/payments/'.$payment->id.'/'.Str::uuid().'.'.($file->guessExtension() ?: 'bin');
        abort_unless(Storage::disk($disk)->put($path, $bytes, ['visibility' => 'private']), 503);

        $oldDisk = $payment->proof_storage_disk ?: 'local';
        $oldPath = $payment->proof_file_path;

        $payment->forceFill([
            'payment_status' => 'completed',
            'payment_date' => $data['payment_date'],
            'processed_at' => now(),
            'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
            'reference_number' => $data['reference_number'] ?? $payment->reference_number,
            'proof_status' => 'pending',
            'proof_file_path' => $path,
            'proof_storage_disk' => $disk,
            'proof_file_name' => mb_substr(basename($file->getClientOriginalName()), 0, 255),
            'proof_mime_type' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
            'proof_file_size' => strlen($bytes),
            'proof_sha256' => hash('sha256', $bytes),
            'proof_uploaded_at' => now(),
            'proof_uploaded_by' => $request->user()->id,
            'proof_validated_at' => null,
            'proof_validated_by' => null,
            'proof_validation_notes' => null,
        ])->save();

        if ($oldPath && $oldPath !== $path && array_key_exists($oldDisk, (array) config('filesystems.disks', []))) {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return back()->with('success', 'Preuve de paiement déposée. Validation fédération requise avant préparation TMS.');
    }

    public function proofDecision(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->authorizeAssociation($request, $transfer);
        $this->assertPayment($transfer, $payment);
        abort_unless($payment->proof_status === 'pending', 409);

        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:2000|required_if:decision,reject',
        ]);

        $payment->forceFill([
            'proof_status' => $data['decision'] === 'approve' ? 'approved' : 'rejected',
            'proof_validated_at' => now(),
            'proof_validated_by' => $request->user()->id,
            'proof_validation_notes' => $data['notes'] ?? null,
        ])->save();

        return back()->with('success', $data['decision'] === 'approve'
            ? 'Preuve de paiement validée et prête pour TMS.'
            : 'Preuve refusée. Le club payeur doit déposer une nouvelle version.');
    }

    public function downloadProof(Request $request, Transfer $transfer, TransferPayment $payment)
    {
        $this->authorizeRead($request, $transfer);
        $this->assertPayment($transfer, $payment);
        $path = $payment->proof_file_path;
        $disk = $payment->proof_storage_disk ?: 'local';

        abort_unless($path && !str_contains($path, '..') && array_key_exists($disk, (array) config('filesystems.disks', [])), 404);
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, $payment->proof_file_name ?: 'proof-of-payment-'.$payment->id.'.pdf', [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeRead(Request $request, Transfer $transfer): void
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

    private function authorizeAssociation(Request $request, Transfer $transfer): void
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['association_admin', 'association_registrar'], true), 403);
        abort_unless($this->associationMatches($user->association_id, $transfer), 403);
    }

    private function authorizeProofUpload(Request $request, Transfer $transfer, TransferPayment $payment): void
    {
        $user = $request->user();
        abort_unless($user, 401);

        if (in_array($user->role, ['association_admin', 'association_registrar'], true)
            && $this->associationMatches($user->association_id, $transfer)) {
            return;
        }

        abort_unless(
            in_array($user->role, ['club_admin', 'club_manager'], true)
            && $user->club_id
            && (int) $user->club_id === (int) $payment->payer_id
            && in_array((int) $user->club_id, [(int) $transfer->club_origin_id, (int) $transfer->club_destination_id], true),
            403
        );
    }

    private function associationMatches(?int $associationId, Transfer $transfer): bool
    {
        if (!$associationId) {
            return false;
        }

        $associations = DB::table('clubs')
            ->whereIn('id', [$transfer->club_origin_id, $transfer->club_destination_id])
            ->pluck('association_id')->filter()->map(fn ($id) => (int) $id)->all();

        return in_array((int) $associationId, $associations, true);
    }

    private function assertPayment(Transfer $transfer, TransferPayment $payment): void
    {
        abort_unless((int) $payment->transfer_id === (int) $transfer->id, 404);
    }
}
