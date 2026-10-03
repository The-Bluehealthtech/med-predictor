@extends('layouts.app')

@section('title', 'Dossier de licence - FIT Platform')

@php
    use App\Services\Licensing\{FifaIdRegistry, LicenseWorkflow};
    $player = $license->player;
    $official = $license->clubOfficial;
    $name = app(LicenseWorkflow::class)->holderName($license);
    // Personne titulaire (FIFA Connect) : joueur, ou officiel d'équipe / dirigeant.
    $person = $official
        ? ['dob' => $official->date_of_birth, 'nationality' => $official->nationality, 'fifa_id' => $official->person_fifa_id]
        : ['dob' => $player?->date_of_birth, 'nationality' => $player?->nationality, 'fifa_id' => $player?->fifa_connect_id];
    $check = $license->identity_check ?? null;
    $checkTone = ['match' => 'border-emerald-200 bg-emerald-50 text-emerald-900', 'partial' => 'border-amber-200 bg-amber-50 text-amber-900',
        'mismatch' => 'border-red-200 bg-red-50 text-red-900', 'not_found' => 'border-red-200 bg-red-50 text-red-900'];
    $fieldLabels = ['first_name' => 'prénom', 'last_name' => 'nom', 'date_of_birth' => 'date de naissance'];
    $pending = $license->status === 'pending';
@endphp

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        :title="$name"
        :subtitle="LicenseWorkflow::describe($license) . ' · ' . ($license->club?->name ?? '') . ' · ' . (LicenseWorkflow::STATUS_LABELS[$license->status] ?? $license->status)"
        eyebrow="Licences · dossier"
        :back-href="route('licenses.validation')"
        back-label="Retour à la file d'approbation"
    />

    @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'info' => 'border-slate-200 bg-white text-slate-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $key => $class)
        @if(session($key))<div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ $class }}" role="status">{{ session($key) }}</div>@endif
    @endforeach

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-request">
            <h2 id="h-request" class="text-base font-semibold text-slate-900">1 · La demande</h2>
            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <dt class="text-slate-500">{{ $official ? 'Titulaire' : 'Joueur' }}</dt><dd class="text-slate-900">{{ $name }}</dd>
                <dt class="text-slate-500">Date de naissance</dt><dd class="text-slate-900">{{ $person['dob'] ? \Illuminate\Support\Carbon::parse($person['dob'])->format('d/m/Y') : '—' }}</dd>
                <dt class="text-slate-500">Nationalité</dt><dd class="text-slate-900">{{ $person['nationality'] ?: '—' }}</dd>
                <dt class="text-slate-500">Identifiant FIFA</dt><dd class="text-slate-900">{{ $person['fifa_id'] ?: 'absent' }}</dd>
                <dt class="text-slate-500">Club</dt><dd class="text-slate-900">{{ $license->club?->name ?? '—' }}</dd>
                <dt class="text-slate-500">Licence (FIFA Connect)</dt><dd class="text-slate-900">{{ LicenseWorkflow::describe($license) }}</dd>
                <dt class="text-slate-500">Saison</dt><dd class="text-slate-900">{{ $license->season ?: '—' }}</dd>
                <dt class="text-slate-500">Motif</dt><dd class="text-slate-900">{{ config('licensing.request_reasons.' . $license->request_reason . '.label', '—') }}@if($license->previous_license_id)<span class="block text-xs text-slate-500">Enregistrement précédent : licence n° {{ $license->previous_license_id }}{{ in_array($license->request_reason, ['transfer', 'level_change', 'loan_return'], true) ? ', clôturé à l\'approbation' : '' }}</span>@endif</dd>
                <dt class="text-slate-500">Statut FIFA Connect</dt><dd class="text-slate-900"><code>{{ LicenseWorkflow::fifaStatus($license) }}</code></dd>
                <dt class="text-slate-500">Tarif</dt><dd class="text-slate-900">{{ $license->fee_amount !== null ? number_format((float) $license->fee_amount, 2, ',', ' ') . ' ' . $license->fee_currency : 'non défini au dépôt' }}</dd>
                <dt class="text-slate-500">Validité</dt><dd class="text-slate-900">{{ $license->contract_start_date?->format('d/m/Y') ?? '—' }} → {{ $license->expiry_date?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="text-slate-500">Demandée par</dt><dd class="text-slate-900">{{ $requester?->name ?? '—' }}, le {{ $license->created_at?->format('d/m/Y') }}</dd>
            </dl>
            @if($license->notes)<p class="mt-3 rounded-xl bg-slate-50 p-3 text-sm text-slate-700"><span class="font-semibold">Notes du club :</span> {{ $license->notes }}</p>@endif
            @if($license->club_response)<p class="mt-3 rounded-xl bg-orange-50 p-3 text-sm text-orange-900"><span class="font-semibold">Complément fourni par le club :</span> {{ $license->club_response }}</p>@endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-identity" data-fifa-id-status="{{ $registryConnected ? 'connected' : 'not_configured' }}">
            <h2 id="h-identity" class="text-base font-semibold text-slate-900">2 · Identité — registre FIFA ID <span class="font-normal text-slate-500">(facultatif)</span></h2>
            <p class="mt-1 text-sm text-slate-600">FIFA ID sert de registre externe d'identité : il confirme que la personne existe sous cet identifiant et que son nom et sa date de naissance concordent avec la fiche FIT.</p>

            @if($check)
                <div class="mt-3 rounded-xl border px-3 py-2 text-sm {{ $checkTone[$check['status']] ?? 'border-slate-200 bg-slate-50 text-slate-800' }}">
                    <p class="font-semibold">{{ FifaIdRegistry::STATUSES[$check['status']] ?? $check['status'] }}</p>
                    <p class="text-xs">Vérifié le {{ $license->identity_checked_at?->format('d/m/Y à H:i') }}</p>
                    @if(!empty($check['differences']))<p class="mt-1">Écart sur : {{ collect($check['differences'])->map(fn ($f) => $fieldLabels[$f] ?? $f)->implode(', ') }}.</p>@endif
                    @if(!empty($check['registry']))
                        <p class="mt-1 text-xs">FIFA ID : {{ trim(($check['registry']['first_name'] ?? '') . ' ' . ($check['registry']['last_name'] ?? '')) ?: '—' }}{{ !empty($check['registry']['date_of_birth']) ? ', né(e) le ' . \Illuminate\Support\Carbon::parse($check['registry']['date_of_birth'])->format('d/m/Y') : '' }}</p>
                    @endif
                </div>
            @endif

            @if($registryConnected)
                <form method="POST" action="{{ route('licenses.verify-identity', $license) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50" @disabled(!$person['fifa_id'])>{{ $check ? 'Vérifier à nouveau' : 'Vérifier l\'identité auprès de FIFA ID' }}</button>
                    @unless($person['fifa_id'])<p class="mt-1 text-xs text-slate-500">Impossible sans identifiant FIFA sur la fiche.</p>@endunless
                </form>
            @else
                <p class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">Registre non connecté : cette étape est sautée, la décision reste possible. Pour l'activer, l'administrateur renseigne l'adresse et le jeton du service FIFA Connect ID de la fédération (FIFA_ID_REGISTRY_URL, FIFA_ID_REGISTRY_TOKEN).</p>
            @endif
        </section>
    </div>

    @unless($official)
    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-pcma">
        <h2 id="h-pcma" class="text-base font-semibold text-slate-900">Aptitude médicale (PCMA)</h2>
        <p class="mt-1 text-sm text-slate-600">Bilan médical pré-compétition, exigé selon la catégorie d'âge et le niveau (barème de la fédération). Seuls l'état et la date sont affichés.</p>
        <div class="mt-2">@include('licenses.partials.pcma')</div>
    </section>
    @endunless

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-docs">
            <h2 id="h-docs" class="text-base font-semibold text-slate-900">Pièces justificatives</h2>
            @if($missing !== [])<p class="mt-1 text-sm text-red-700">Pièces exigées manquantes : l'approbation est bloquée tant qu'elles ne sont pas fournies. Demandez un complément au club.</p>@else<p class="mt-1 text-sm text-emerald-700">Toutes les pièces exigées pour ce type de licence sont fournies.</p>@endif
            <div class="mt-2">@include('licenses.partials.documents')</div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-timeline">
            <h2 id="h-timeline" class="text-base font-semibold text-slate-900">Suivi de la demande</h2>
            <div class="mt-3">@include('licenses.partials.timeline')</div>
        </section>
    </div>

    @unless($official)
        @include('licenses.approval._fraud-check')
    @endunless

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-decision">
        <h2 id="h-decision" class="text-base font-semibold text-slate-900">3 · Décision de la fédération</h2>
        @if($pending)
            <form method="POST" action="{{ route('licenses.decide', $license) }}" class="mt-3 space-y-3">
                @csrf
                <fieldset class="grid gap-2 sm:grid-cols-3">
                    <legend class="sr-only">Décision</legend>
                    @foreach(['approve' => ['Approuver', $missing === [] && !$pcma['blocking'] ? 'La licence devient active jusqu\'au ' . ($license->expiry_date?->format('d/m/Y') ?? '—') . '.' : ($pcma['blocking'] ? 'Impossible sans PCMA valide.' : 'Impossible tant que des pièces exigées manquent.')],
                              'request_info' => ['Demander un complément', 'La demande revient au club avec votre message ; il est notifié.'],
                              'reject' => ['Refuser', 'Un motif est obligatoire ; le club est notifié.']] as $value => [$label, $help])
                        <label class="flex cursor-pointer gap-2 rounded-xl border border-slate-200 p-3 hover:bg-slate-50 has-[:checked]:border-slate-900 has-[:checked]:ring-1 has-[:checked]:ring-slate-900">
                            <input type="radio" name="decision" value="{{ $value }}" required class="mt-1" @checked(old('decision') === $value)>
                            <span><span class="block text-sm font-semibold text-slate-900">{{ $label }}</span><span class="block text-xs text-slate-600">{{ $help }}</span></span>
                        </label>
                    @endforeach
                </fieldset>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Message au club <span class="font-normal text-slate-500">(obligatoire pour un complément ou un refus)</span></span>
                    <textarea name="message" rows="3" maxlength="2000" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('message') }}</textarea>
                </label>
                @if($registryConnected && !$check)<p class="text-xs text-slate-500">L'identité n'a pas été vérifiée auprès de FIFA ID : c'est facultatif, mais recommandé.</p>@endif
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Enregistrer la décision</button>
            </form>
        @else
            <p class="mt-2 text-sm text-slate-700">{{ LicenseWorkflow::STATUS_LABELS[$license->status] ?? $license->status }}{{ $decider ? ' — par ' . $decider->name : '' }}{{ $license->approved_at ? ', le ' . $license->approved_at->format('d/m/Y') : '' }}.</p>
            @if($license->rejection_reason && in_array($license->status, ['revoked', 'justification_requested'], true))<p class="mt-1 text-sm text-slate-700"><span class="font-semibold">Message au club :</span> {{ $license->rejection_reason }}</p>@endif
        @endif
    </section>
</div>
@endsection
