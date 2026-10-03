@extends('layouts.app')

@section('title', 'Mise en service FHIR - FIT')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <a href="{{ route('modules.api-connectors.index') }}" class="text-sm text-blue-600">← Configuration des API</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Mise en service du serveur FHIR</h1>
        <p class="text-sm text-gray-600 mt-1">Vérification de toute la chaîne : configuration de FIT, serveur HAPI et conformité IHE, abonnement des comptes rendus, sécurité (IUA, BALP), consentement (PCF), décodeur d’imagerie. Guide pas à pas : <code>docs/fhir/INSTALLATION.md</code>.</p>
    </div>
    @if(session('success'))<p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ session('error') }}</p>@endif

    @php $failed = collect($rows)->where('state', 'fail')->count(); @endphp
    <section class="bg-white rounded-xl shadow overflow-x-auto" aria-labelledby="h-readiness">
        <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
            <h2 id="h-readiness" class="font-semibold text-gray-900">{{ $failed ? $failed . ' point(s) bloquant(s)' : 'Chaîne prête' }}</h2>
            <a href="{{ route('admin.fhir-setup') }}" class="text-sm font-semibold text-blue-700">Relancer la vérification</a>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-2">Point</th><th class="px-4 py-2">État</th><th class="px-4 py-2">Détail / action</th></tr></thead>
            <tbody class="divide-y">
                @foreach($rows as $row)
                    <tr>
                        <td class="px-4 py-2 font-medium text-slate-900">{{ $row['label'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ ['ok' => 'bg-emerald-50 text-emerald-800', 'warn' => 'bg-amber-50 text-amber-800', 'fail' => 'bg-red-50 text-red-700'][$row['state']] }}">{{ ['ok' => 'Prêt', 'warn' => 'À faire', 'fail' => 'Bloquant'][$row['state']] }}</span>
                        </td>
                        <td class="px-4 py-2 text-slate-700">{{ $row['detail'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="bg-white rounded-xl shadow p-5 space-y-3" aria-labelledby="h-actions">
        <h2 id="h-actions" class="font-semibold text-gray-900">Actions de mise en service</h2>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.fhir-setup.subscription') }}">@csrf
                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Installer l’abonnement des comptes rendus</button>
            </form>
            <form method="POST" action="{{ route('admin.fhir-setup.resend') }}">@csrf
                <button class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-800">Renvoyer les examens en attente</button>
            </form>
            <a href="{{ route('admin.fhir-setup', ['conformance' => 1]) }}#h-conformance" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-800">Conformité IHE détaillée</a>
        </div>
        <p class="text-xs text-slate-500">L’abonnement demande au serveur de prévenir FIT à chaque compte rendu, sur <code>{{ $endpoint }}</code>, avec le secret partagé.</p>
    </section>

    @if($error)<p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $error }}</p>@endif
    @if($details)
        <section class="bg-white rounded-xl shadow p-5 space-y-4" aria-labelledby="h-conformance">
            <h2 id="h-conformance" class="font-semibold text-gray-900">Conformité aux acteurs IHE / HL7</h2>
            @foreach($details['actors'] as $actor)
                @php $missing = collect($actor['findings'])->where('ok', false); @endphp
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">{{ $actor['label'] }} — {{ $missing->where('level', 'SHALL')->count() }} SHALL manquante(s), {{ $missing->where('level', '!=', 'SHALL')->count() }} SHOULD/MAY</h3>
                    @if($missing->isNotEmpty())
                        <ul class="mt-1 text-xs text-slate-600 list-disc pl-5">@foreach($missing as $f)<li>{{ $f['level'] }} — {{ $f['requirement'] }}</li>@endforeach</ul>
                    @endif
                </div>
            @endforeach
        </section>
    @endif
</div>
@endsection
