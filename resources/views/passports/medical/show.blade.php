@extends('layouts.app')

@section('title', 'Passeport médical — ' . $summary['patient']['name'])

@section('content')
<style>
    .ips-doc { background:#fff; border-radius:.75rem; box-shadow:0 1px 3px rgba(0,0,0,.08); padding:1.5rem; }
    .ips-header { display:flex; flex-wrap:wrap; justify-content:space-between; gap:1rem; border-bottom:2px solid #b91c1c; padding-bottom:1rem; }
    .ips-kicker { font-size:.7rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#b91c1c; }
    .ips-title { font-size:1.5rem; font-weight:700; color:#111827; }
    .ips-sub { font-size:.875rem; color:#4b5563; }
    .ips-meta td { font-size:.8rem; padding:.1rem .5rem; color:#374151; } .ips-meta td:first-child { color:#6b7280; }
    .ips-notice { margin:1rem 0; padding:.75rem 1rem; border-radius:.5rem; background:#fef3c7; color:#78350f; font-size:.8rem; }
    .ips-section { margin-top:1.25rem; }
    .ips-section-title { font-weight:600; color:#111827; border-bottom:1px solid #e5e7eb; padding-bottom:.25rem; }
    .ips-code { font-size:.7rem; font-weight:400; color:#9ca3af; }
    .ips-table { width:100%; margin-top:.5rem; font-size:.85rem; } .ips-table td { padding:.35rem .25rem; border-bottom:1px solid #f3f4f6; vertical-align:top; }
    .ips-label { font-weight:500; color:#111827; width:38%; } .ips-date { color:#6b7280; text-align:right; white-space:nowrap; width:6rem; }
    .ips-empty { font-size:.8rem; color:#9ca3af; font-style:italic; margin-top:.4rem; }
    .ips-footer { margin-top:1.5rem; font-size:.7rem; color:#9ca3af; }
</style>
<div class="max-w-5xl mx-auto px-4 py-8 space-y-4">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <label class="block text-sm font-medium text-gray-700">Motif du partage
                <select name="purpose" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm" onchange="this.form.submit()">
                    @foreach($purposes as $key => $label)<option value="{{ $key }}" @selected($summary['document']['purpose'] === $key)>{{ $label }}</option>@endforeach
                </select>
            </label>
        </form>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('passports.medical.pdf', ['player' => $summary['patient']['id'], 'purpose' => $summary['document']['purpose']]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-700 text-white font-semibold hover:bg-red-800">@include('modules.partials.icon', ['name' => 'file', 'class' => 'w-4 h-4']) Télécharger le PDF</a>
            <a href="{{ route('passports.medical.fhir', ['player' => $summary['patient']['id'], 'purpose' => $summary['document']['purpose']]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-red-200 text-red-800 font-semibold hover:bg-red-50">FHIR (IPS)</a>
            @unless(auth()->user()->isPlayer())<a href="{{ route('passports.medical.index') }}" class="text-blue-600 hover:text-blue-800">← Passeports médicaux</a>@endunless
        </div>
    </div>
    @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
    <div class="ips-doc">@include('passports.medical._document')</div>
    @include('passports._digital-signature', [
        'passportPlayerId' => $summary['patient']['id'],
        'canRequestDigitalSignature' => $canAttest && ($attestation['state'] ?? 'none') === 'valid',
        'canManageDigitalSignature' => $canAttest,
        'digitalSignatureAction' => route('passports.medical.digital-signature', $summary['patient']['id']),
        'digitalSignaturePurpose' => $summary['document']['purpose'],
        'digitalSignatureBlockedMessage' => $canAttest ? 'Attestez d’abord la version médicale courante avant sa signature numérique.' : 'La signature numérique du passeport médical est réservée au médecin autorisé.',
    ])
    @if($canAttest)
        <form method="POST" action="{{ route('passports.medical.attest', ['player' => $summary['patient']['id'], 'purpose' => $summary['document']['purpose']]) }}" class="bg-white rounded-lg shadow p-5 space-y-3">
            @csrf
            <h2 class="font-semibold text-gray-900">{{ ($attestation['state'] ?? 'none') === 'valid' ? 'Attester à nouveau' : 'Attester ce passeport médical' }}</h2>
            <p class="text-xs text-gray-500">Signature électronique simple : votre identité FIT et votre mot de passe confirment que vous avez vérifié ce résumé. L'empreinte SHA-256 du contenu est conservée ; toute modification ultérieure des données rendra l'attestation périmée.</p>
            @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
            <div class="flex flex-wrap items-end gap-3">
                <label class="block text-sm font-medium text-gray-700">Numéro d'ordre (facultatif)<input name="license" maxlength="60" class="mt-1 block w-48 rounded-lg border-gray-300 shadow-sm text-sm"></label>
                <label class="block text-sm font-medium text-gray-700">Mot de passe<input type="password" name="password" required autocomplete="current-password" class="mt-1 block w-56 rounded-lg border-gray-300 shadow-sm text-sm"></label>
                <label class="flex items-center gap-2 text-sm text-gray-700 pb-2"><input type="checkbox" name="confirm" value="1" required class="rounded border-gray-300"> J'ai vérifié le contenu de ce résumé</label>
                <button class="px-4 py-2 rounded-lg bg-red-700 text-white text-sm font-semibold hover:bg-red-800">Attester</button>
            </div>
        </form>
    @endif
</div>
@endsection
