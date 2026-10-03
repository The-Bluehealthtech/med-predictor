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
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Blocages</p>
        <div class="mt-3 space-y-2">
            @if($missingDocuments)
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Pièces approuvées manquantes : {{ collect($missingDocuments)->map(fn($t)=>['passport'=>'passeport','contract'=>'contrat','parental_consent'=>'consentement parental'][$t] ?? $t)->implode(', ') }}.
                </div>
            @else
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Pièces obligatoires approuvées dans FIT.</div>
            @endif
            @unless($transfer->is_in_transfer_window)
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">La fenêtre de transfert enregistrée dans ce dossier n’est pas ouverte aujourd’hui.</div>
            @endunless
            @unless($fifaConfigured)
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">Connexion FIFA TMS/ITC non activée. FIT n’effectuera aucune soumission externe tant que les accès officiels ne sont pas configurés au Go Live.</div>
            @endunless
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Prochaine action</p>
        <div class="mt-3 flex flex-wrap gap-3">
            <a href="{{ route('passports.transfer.show',$transfer->player_id) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Passeport de transfert</a>
            @if($transfer->transfer_status === 'draft')
                @if($transfer->canBeSubmitted() && $fifaConfigured)
                    <button data-transfer-action="{{ route('transfers.submit-to-fifa',$transfer) }}" class="transfer-action rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Soumettre à FIFA/TMS</button>
                @elseif($missingDocuments)
                    <span class="rounded-xl bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-800">Compléter et faire valider les pièces</span>
                @elseif(!$fifaConfigured)
                    <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">Attente activation FIFA TMS au Go Live</span>
                @endif
            @endif
            @if($transfer->is_international && $transfer->fifa_itc_id && in_array($transfer->itc_status,['requested','pending'],true) && $fifaConfigured)
                <button data-transfer-action="{{ route('transfers.check-itc',$transfer) }}" class="transfer-action rounded-xl border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-800">Vérifier le statut ITC</button>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="font-semibold text-slate-900">Pièces et traçabilité</h2>
        <div class="mt-3 divide-y divide-slate-100">
            @forelse($transfer->documents as $document)
                <div class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm">
                    <span>{{ $document->document_type_label }} · {{ $document->file_name ?: $document->document_name }}</span>
                    <span class="{{ $document->validation_status === 'approved' ? 'text-emerald-700' : ($document->validation_status === 'rejected' ? 'text-red-700' : 'text-amber-700') }} font-semibold">{{ $document->validation_status_label }}</span>
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
