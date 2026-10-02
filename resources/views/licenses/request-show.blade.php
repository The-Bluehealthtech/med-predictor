@extends('layouts.app')

@section('title', 'Suivi de la demande de licence - FIT Platform')

@php
    use App\Services\Licensing\LicenseWorkflow;
    $player = $license->player;
    $name = app(LicenseWorkflow::class)->holderName($license);
    $open = in_array($license->status, ['pending', 'justification_requested'], true);
    $tone = ['pending' => 'border-amber-200 bg-amber-50 text-amber-900', 'justification_requested' => 'border-orange-200 bg-orange-50 text-orange-900',
        'active' => 'border-emerald-200 bg-emerald-50 text-emerald-900', 'revoked' => 'border-red-200 bg-red-50 text-red-900'];
    $documentTypes = config('licensing.documents', []);
@endphp

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        :title="$name"
        :subtitle="collect([LicenseWorkflow::describe($license), $license->season ? 'saison ' . $license->season : null, $license->club?->name, $license->fee_amount !== null ? number_format((float) $license->fee_amount, 2, ',', ' ') . ' ' . $license->fee_currency : null])->filter()->implode(' · ')"
        eyebrow="Licences · suivi de la demande"
        :back-href="route('modules.licenses.index')"
        back-label="Retour aux demandes"
    />

    @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $key => $class)
        @if(session($key))<div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ $class }}" role="status">{{ session($key) }}</div>@endif
    @endforeach

    <div class="mb-4 rounded-2xl border px-5 py-4 {{ $tone[$license->status] ?? 'border-slate-200 bg-white text-slate-900' }}" data-license-status="{{ $license->status }}">
        <p class="text-base font-semibold">{{ LicenseWorkflow::STATUS_LABELS[$license->status] ?? $license->status }}</p>
        <p class="text-sm">
            @switch($license->status)
                @case('pending') La fédération examine la demande. Vous serez notifié de sa décision. @break
                @case('justification_requested') La fédération attend un complément : <strong>{{ $license->rejection_reason }}</strong> @break
                @case('active') Licence valable jusqu'au {{ $license->expiry_date?->format('d/m/Y') }}. @break
                @case('revoked') Motif du refus : {{ $license->rejection_reason }} @break
            @endswitch
        </p>
    </div>

    @if($license->status === 'justification_requested')
        <section class="mb-4 rounded-2xl border border-orange-200 bg-white p-5" aria-labelledby="h-respond">
            <h2 id="h-respond" class="text-base font-semibold text-slate-900">Compléter la demande</h2>
            <form method="POST" action="{{ route('player-licenses.respond', $license) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                @csrf
                <label class="block"><span class="text-sm font-semibold text-slate-700">Votre réponse à la fédération</span>
                    <textarea name="club_response" rows="3" maxlength="2000" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea></label>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach($documentTypes as $type => $label)
                        <label class="block text-sm"><span class="text-slate-700">{{ $label }}{{ in_array($type, $missing, true) ? ' (manquante)' : '' }}</span>
                            <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold"></label>
                    @endforeach
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Envoyer le complément à la fédération</button>
            </form>
        </section>
    @endif

    @if(!$license->club_official_id && $pcma['required'])
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-pcma">
            <h2 id="h-pcma" class="text-base font-semibold text-slate-900">Aptitude médicale (PCMA)</h2>
            <div class="mt-2">@include('licenses.partials.pcma')</div>
            @if($pcma['blocking'])<p class="mt-2 text-sm text-slate-700">Faites réaliser le PCMA du joueur par le médecin du club et faites-le signer : la fédération pourra alors approuver la licence.</p>@endif
        </section>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-docs">
            <h2 id="h-docs" class="text-base font-semibold text-slate-900">Pièces justificatives</h2>
            @if($missing !== [])<p class="mt-1 text-sm text-red-700">Pièces exigées manquantes : la fédération ne pourra pas approuver la licence sans elles.</p>@endif
            <div class="mt-2">@include('licenses.partials.documents')</div>
            @if($license->status === 'pending')
                <form method="POST" action="{{ route('player-licenses.documents', $license) }}" enctype="multipart/form-data" class="mt-3 flex flex-wrap items-end gap-2 border-t border-slate-100 pt-3">
                    @csrf
                    <label class="text-sm"><span class="block text-slate-700">Ajouter une pièce</span>
                        <select name="doc_type" class="mt-1 rounded-xl border border-slate-300 px-2 py-1.5 text-sm" onchange="this.form.querySelector('input[type=file]').name = 'documents[' + this.value + ']'">
                            @foreach($documentTypes as $type => $label)<option value="{{ $type }}" @selected($type === ($missing[0] ?? 'other'))>{{ $label }}</option>@endforeach
                        </select></label>
                    <input type="file" name="documents[{{ $missing[0] ?? 'other' }}]" required accept=".pdf,.jpg,.jpeg,.png" class="text-sm file:mr-2 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold">
                    <button class="rounded-xl border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Ajouter</button>
                </form>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-timeline">
            <h2 id="h-timeline" class="text-base font-semibold text-slate-900">Suivi</h2>
            <div class="mt-3">@include('licenses.partials.timeline')</div>
        </section>
    </div>
</div>
@endsection
