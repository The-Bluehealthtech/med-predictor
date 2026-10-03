@extends('layouts.app')

@section('title', 'Transfert - FIT')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <x-page-header
        title="Dossier de transfert"
        subtitle="{{ $transfer->player?->full_name ?? 'Joueur' }} · {{ $transfer->clubOrigin?->name }} → {{ $transfer->clubDestination?->name }}"
        eyebrow="Football operations · transferts"
        :back-href="route('transfers.index')"
        back-label="Retour aux transferts"
    />

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    <div id="transfer-feedback" class="hidden rounded-xl px-4 py-3 text-sm"></div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contexte</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $transfer->player?->full_name }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $transfer->clubOrigin?->name }} → {{ $transfer->clubDestination?->name }}</p>
            </div>
            <div class="text-right text-sm">
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-800">{{ ucfirst(str_replace('_',' ',$transfer->transfer_status)) }}</span>
                <p class="mt-2 text-slate-500">{{ ucfirst(str_replace('_',' ',$transfer->transfer_type)) }} · {{ $transfer->is_international ? 'International' : 'National' }}</p>
            </div>
        </div>

        <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="text-xs uppercase text-slate-500">Date transfert</dt><dd class="mt-1 font-semibold">{{ $transfer->transfer_date?->format('d/m/Y') ?? '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Début contrat</dt><dd class="mt-1 font-semibold">{{ $transfer->contract_start_date?->format('d/m/Y') ?? '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Montant</dt><dd class="mt-1 font-semibold">{{ $transfer->transfer_fee ? number_format((float)$transfer->transfer_fee,2,',',' ').' '.$transfer->currency : 'Sans indemnité renseignée' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">ITC</dt><dd class="mt-1 font-semibold">{{ $transfer->is_international ? ucfirst(str_replace('_',' ',$transfer->itc_status)) : 'Non requis' }}</dd></div>
        </dl>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Préparation TMS</p>
                <h2 class="mt-1 font-semibold text-slate-900">FIT prépare le dossier ; FIFA TMS exécute le transfert</h2>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $transfer->tms_sync_status === 'linked' ? 'bg-emerald-100 text-emerald-800' : ($transfer->tms_sync_status === 'ready' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst(str_replace('_',' ',$transfer->tms_sync_status ?? 'not_ready')) }}</span>
        </div>

        <div class="mt-4 space-y-2">
            @forelse($tmsReadiness['blockers'] as $blocker)
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $blocker['label'] }}</div>
            @empty
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Dossier FIT complet : données, identifiants FIFA et pièces approuvées sont prêts pour TMS.</div>
            @endforelse
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach($tmsReadiness['flows'] as $key => $flow)
                <div class="rounded-xl border {{ ($flow['applicable'] ?? false) ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }} p-3">
                    <p class="text-xs font-bold uppercase tracking-wide {{ ($flow['applicable'] ?? false) ? 'text-emerald-700' : 'text-slate-500' }}">{{ $flow['label'] }}</p>
                    @if($key === 'first_pro_registration')
                        @if($flow['applicable'] ?? false)
                            <p class="mt-1 text-sm font-semibold text-emerald-900">Oui · première licence pro liée à ce transfert</p>
                            <p class="mt-1 text-xs text-emerald-800">Licence {{ $flow['license_number'] ?: '#'.$flow['first_license_id'] }} · {{ $flow['effective_date'] ?? 'date non renseignée' }}</p>
                        @elseif(!empty($flow['first_license_id']))
                            <p class="mt-1 text-sm text-slate-700">Non pour ce transfert</p>
                            <p class="mt-1 text-xs text-slate-500">Première licence pro historique : {{ $flow['license_number'] ?: '#'.$flow['first_license_id'] }} · {{ $flow['effective_date'] ?? 'date non renseignée' }}</p>
                        @else
                            <p class="mt-1 text-sm text-slate-600">Non détecté dans l’historique FIT</p>
                        @endif
                    @elseif($key === 'proof_of_payment')
                        <p class="mt-1 text-sm text-slate-700">{{ ($flow['applicable'] ?? false) ? ($flow['payments_count'].' paiement(s) préparé(s)') : 'Non applicable actuellement' }}</p>
                    @else
                        <p class="mt-1 text-sm text-slate-700">{{ ($flow['applicable'] ?? false) ? 'Applicable' : 'Non applicable' }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        @if($transfer->tms_payload_sha256)
            <p class="mt-3 font-mono text-xs text-slate-500">Empreinte dossier TMS : {{ $transfer->tms_payload_sha256 }}</p>
        @endif
        @if($transfer->tms_transfer_id)
            <p class="mt-2 text-sm text-slate-700">Référence TMS rattachée : <strong>{{ $transfer->tms_transfer_id }}</strong></p>
        @endif
        @if($transfer->tms_remote_status)
            <p class="mt-1 text-sm text-slate-700">Statut TMS : <strong>{{ $transfer->tms_remote_status }}</strong></p>
        @endif
        @if($transfer->tms_last_synced_at)
            <p class="mt-1 text-xs text-slate-500">Dernière synchronisation : {{ $transfer->tms_last_synced_at->format('d/m/Y H:i') }}</p>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Prochaine action</p>
        <div class="mt-3 flex flex-wrap gap-3">
            <a href="{{ route('passports.transfer.show',$transfer->player_id) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Passeport de transfert</a>

            @if($associationOperator && $tmsReadiness['ready'] && !in_array($transfer->tms_sync_status,['ready','linked'],true))
                <button data-transfer-action="{{ route('transfers.prepare-tms',$transfer) }}" class="transfer-action rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Marquer prêt pour TMS</button>
            @endif

            @if($associationOperator && in_array($transfer->tms_sync_status,['ready','linked','synced'],true) && $transfer->tms_snapshot)
                <a href="{{ route('transfers.tms-package',$transfer) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Télécharger le dossier TMS JSON</a>
            @endif

            @if($associationOperator && $transfer->tms_sync_status === 'ready' && !$transfer->tms_transfer_id)
                <form method="POST" action="{{ route('transfers.link-tms',$transfer) }}" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <label class="text-sm font-medium text-slate-700">Référence TMS
                        <input name="tms_transfer_id" class="mt-1 rounded-lg border-slate-300" placeholder="tmsTransferId" required>
                    </label>
                    <button class="rounded-xl border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-800">Rattacher TMS</button>
                </form>
            @endif

            @if(!$tmsReadiness['ready'])
                <span class="rounded-xl bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-800">Lever les blocages avant passage dans TMS</span>
            @elseif($associationOperator && $transfer->tms_transfer_id && $tmsBridgeConfigured)
                <form method="POST" action="{{ route('transfers.sync-tms',$transfer) }}">@csrf
                    <button class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800">Synchroniser depuis TMS</button>
                </form>
            @elseif($transfer->tms_transfer_id)
                <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">Référence TMS liée · synchronisation automatique à activer au Go Live</span>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-slate-900">Échéancier & Proof of Payment</h2>
                <p class="mt-1 text-sm text-slate-600">FIT prépare les données de paiement et conserve la preuve privée ; seule une preuve validée par la fédération est marquée prête pour TMS.</p>
            </div>
        </div>

        @if($associationOperator)
            <form method="POST" action="{{ route('transfers.payments.store',$transfer) }}" class="mt-4 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-3">
                @csrf
                <input type="hidden" name="payer_id" value="{{ $transfer->club_destination_id }}">
                <input type="hidden" name="payee_id" value="{{ $transfer->club_origin_id }}">
                <label class="text-sm">Type
                    <select name="payment_type" class="mt-1 w-full rounded-lg border-slate-300" required>
                        <option value="transfer_fee">Indemnité de transfert</option>
                        <option value="training_compensation">Indemnité de formation</option>
                        <option value="solidarity_contribution">Contribution de solidarité</option>
                        <option value="other">Autre</option>
                    </select>
                </label>
                <label class="text-sm">Montant
                    <input name="amount" type="number" min="0.01" step="0.01" class="mt-1 w-full rounded-lg border-slate-300" required>
                </label>
                <label class="text-sm">Devise
                    <input name="currency" value="{{ $transfer->currency ?: 'EUR' }}" maxlength="3" class="mt-1 w-full rounded-lg border-slate-300 uppercase" required>
                </label>
                <label class="text-sm">Mode
                    <select name="payment_method" class="mt-1 w-full rounded-lg border-slate-300" required>
                        <option value="bank_transfer">Virement bancaire</option>
                        <option value="check">Chèque</option>
                        <option value="cash">Espèces</option>
                        <option value="other">Autre</option>
                    </select>
                </label>
                <label class="text-sm">Échéance
                    <input name="due_date" type="date" class="mt-1 w-full rounded-lg border-slate-300" required>
                </label>
                <div class="flex items-end"><button class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Ajouter l’échéance</button></div>
            </form>
        @endif

        <div class="mt-4 space-y-3">
            @forelse($transfer->payments->sortBy('due_date') as $payment)
                @php($canUploadPaymentProof = $associationOperator || (in_array(auth()->user()->role,['club_admin','club_manager'],true) && (int)auth()->user()->club_id === (int)$payment->payer_id))
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $payment->payment_type_label }} · {{ $payment->formatted_amount }}</p>
                            <p class="mt-1 text-xs text-slate-500">Échéance {{ $payment->due_date?->format('d/m/Y') }} · {{ $payment->payment_status_label }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->proof_status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($payment->proof_status === 'rejected' ? 'bg-red-100 text-red-800' : ($payment->proof_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')) }}">
                            Preuve : {{ ucfirst($payment->proof_status ?? 'missing') }}
                        </span>
                    </div>

                    @if($payment->proof_file_path)
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <span>{{ $payment->proof_file_name }}</span>
                            @if($payment->proof_sha256)<span class="font-mono">SHA-256 {{ substr($payment->proof_sha256,0,12) }}…</span>@endif
                            <a class="font-semibold text-blue-700" href="{{ route('transfers.payments.proof.download',[$transfer,$payment]) }}">Télécharger</a>
                        </div>
                    @endif

                    @if($canUploadPaymentProof)
                        <form method="POST" action="{{ route('transfers.payments.proof',[$transfer,$payment]) }}" enctype="multipart/form-data" class="mt-3 grid gap-2 md:grid-cols-[1fr_180px_1fr_auto] md:items-end">
                            @csrf
                            <label class="text-xs">Preuve
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full" required>
                            </label>
                            <label class="text-xs">Date paiement
                                <input type="date" name="payment_date" class="mt-1 w-full rounded-lg border-slate-300 text-sm" required>
                            </label>
                            <label class="text-xs">Référence transaction
                                <input name="transaction_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            </label>
                            <button class="rounded-lg bg-blue-700 px-3 py-2 text-xs font-semibold text-white">Déposer</button>
                        </form>
                    @endif

                    @if($associationOperator && $payment->proof_status === 'pending')
                        <form method="POST" action="{{ route('transfers.payments.proof.decision',[$transfer,$payment]) }}" class="mt-3 flex flex-wrap gap-2">
                            @csrf
                            <input name="notes" class="min-w-[260px] flex-1 rounded-lg border-slate-300 text-sm" placeholder="Note fédération (obligatoire en cas de refus)">
                            <button name="decision" value="approve" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white">Valider la preuve</button>
                            <button name="decision" value="reject" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 ring-1 ring-red-200">Refuser</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500">Aucune échéance de paiement enregistrée.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-slate-900">Pièces et traçabilité</h2>
                <p class="mt-1 text-sm text-slate-600">Les fichiers restent privés. Une nouvelle version remplace l’ancienne sans supprimer l’historique.</p>
            </div>
        </div>

        @if($canUploadDocuments)
            <form method="POST" action="{{ route('transfers.documents.store',$transfer) }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-[220px_1fr_auto] md:items-end">
                @csrf
                <label class="text-sm font-medium text-slate-700">Type de pièce
                    <select name="document_type" class="mt-1 w-full rounded-lg border-slate-300" required>
                        <option value="passport">Passeport</option>
                        <option value="contract">Contrat</option>
                        @if($transfer->is_minor_transfer)<option value="parental_consent">Consentement parental</option>@endif
                        <option value="work_permit">Permis de travail</option>
                        <option value="identity_card">Carte d’identité</option>
                        <option value="birth_certificate">Acte de naissance</option>
                        <option value="transfer_form">Formulaire de transfert</option>
                    </select>
                </label>
                <label class="text-sm font-medium text-slate-700">Fichier
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm" required>
                </label>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Déposer</button>
            </form>
        @endif

        <div class="mt-4 divide-y divide-slate-100">
            @forelse($transfer->documents->sortByDesc('id') as $document)
                <div class="py-3 text-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="font-medium text-slate-900">{{ $document->document_type_label }}</span>
                            <span class="text-slate-500"> · {{ $document->file_name ?: $document->document_name }}</span>
                            @if($document->sha256)<span class="ml-2 font-mono text-[11px] text-slate-400">SHA-256 {{ substr($document->sha256,0,12) }}…</span>@endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="{{ $document->validation_status === 'approved' ? 'text-emerald-700' : ($document->validation_status === 'rejected' ? 'text-red-700' : ($document->validation_status === 'expired' ? 'text-slate-500' : 'text-amber-700')) }} font-semibold">{{ $document->validation_status_label }}</span>
                            @if($canDownloadDocuments)
                                <a class="font-semibold text-blue-700" href="{{ route('transfers.documents.download',[$transfer,$document]) }}">Télécharger</a>
                            @endif
                        </div>
                    </div>
                    @if($document->validation_notes)<p class="mt-1 text-xs text-slate-500">{{ $document->validation_notes }}</p>@endif
                    @if($canValidateDocuments && $document->validation_status === 'pending')
                        <form method="POST" action="{{ route('transfers.documents.decision',[$transfer,$document]) }}" class="mt-2 flex flex-wrap items-center gap-2">
                            @csrf
                            <input name="notes" class="min-w-[260px] flex-1 rounded-lg border-slate-300 text-sm" placeholder="Note fédération (obligatoire en cas de refus)">
                            <button name="decision" value="approve" class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white">Approuver</button>
                            <button name="decision" value="reject" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 ring-1 ring-red-200">Refuser</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="py-3 text-sm text-slate-500">Aucune pièce de transfert enregistrée.</p>
            @endforelse
        </div>
        @if($transfer->fifa_transfer_id)
            <p class="mt-3 text-xs text-slate-500">Référence FIFA/TMS reçue : <strong>{{ $transfer->fifa_transfer_id }}</strong></p>
        @endif
        @if($transfer->fifa_itc_id)
            <p class="mt-1 text-xs text-slate-500">Référence ITC reçue : <strong>{{ $transfer->fifa_itc_id }}</strong></p>
        @endif
    </section>
</div>

<script>
document.querySelectorAll('.transfer-action').forEach((button) => {
    button.addEventListener('click', async () => {
        const feedback = document.getElementById('transfer-feedback');
        button.disabled = true;
        try {
            const response = await fetch(button.dataset.transferAction, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json'}
            });
            const data = await response.json();
            feedback.className = 'rounded-xl px-4 py-3 text-sm ' + (response.ok ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800');
            feedback.textContent = data.message || data.error || 'Action terminée.';
            if (response.ok) window.setTimeout(() => window.location.reload(), 700);
        } catch (e) {
            feedback.className = 'rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800';
            feedback.textContent = 'Impossible d’exécuter cette action.';
        } finally {
            button.disabled = false;
        }
    });
});
</script>
@endsection
