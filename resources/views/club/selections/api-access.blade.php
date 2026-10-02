@extends('layouts.app')

{{-- Espace club : jetons d'API et documentation des appels de l'espace. --}}

@section('title', 'Accès API — ' . $config['label'])

@php
    $base = rtrim(url('/api/v1'), '/');
    $endpoints = [
        ['GET', '/club/selections?status=', 'club:selections:read', 'Convocations reçues pour les joueurs du club'],
        ['GET', '/club/selections/{id}', 'club:selections:read', 'Convocation, état de départ (données pré-remplies) et état de retour de sélection (?include_medical=1 avec selections:medical)'],
        ['PUT', '/club/selections/{id}/departure', 'club:selections:write', 'État de départ : availability, load_recommendation, vigilance, technical_notes, contact ; refresh_data=true pour actualiser les données ; send=true pour l\'envoyer ; medical{} et fitness_status avec selections:medical'],
        ['POST', '/club/selections/{id}/acknowledge', 'club:selections:write', 'Accuser réception de l\'état de retour (clôture la sélection)'],
    ];
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    @include('club.selections.partials.nav', ['active' => 'api'])
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Accès API</h1>
        <p class="text-sm text-gray-600 max-w-2xl">Créez un jeton pour connecter le logiciel du club. Un jeton est limité à cet espace et aux droits de votre compte ; il n'est affiché qu'une seule fois.</p>
    </div>

    @if(session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if($plainToken)
        <div class="rounded-lg border border-amber-300 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-900">Votre nouveau jeton — copiez-le maintenant, il ne sera plus affiché :</p>
            <code class="mt-2 block break-all rounded bg-white border border-amber-200 px-3 py-2 text-sm">{{ $plainToken }}</code>
        </div>
    @endif

    <section class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold text-gray-900">Créer un jeton</h2>
        <form method="POST" action="{{ route('club.selections.api-access.store') }}" class="mt-3 flex flex-wrap items-end gap-3">
            @csrf
            <label class="block text-sm font-medium text-gray-700">Nom du logiciel
                <input name="name" required maxlength="60" class="mt-1 block w-64 rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Ex. Logiciel du staff">
            </label>
            <label class="block text-sm font-medium text-gray-700">Validité
                <select name="expires_in_days" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm"><option value="30">30 jours</option><option value="90" selected>90 jours</option><option value="365">1 an</option></select>
            </label>
            @if($canHaveMedical)
                <label class="flex items-center gap-2 text-sm text-gray-700 pb-2"><input type="checkbox" name="with_medical" value="1" class="rounded border-gray-300"> Inclure le droit médical (selections:medical)</label>
            @endif
            <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Créer le jeton</button>
        </form>
        <p class="mt-2 text-xs text-gray-500">Droits accordés : {{ implode(', ', $config['abilities']) }}@if($canHaveMedical), et selections:medical si coché (réservé aux rôles médicaux)@endif.</p>
    </section>

    <section class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b"><h2 class="font-semibold text-gray-900">Vos jetons</h2></div>
        @if($tokens->isEmpty())
            <p class="px-5 py-6 text-sm text-gray-500">Aucun jeton pour cet espace.</p>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-2 text-left">Nom</th><th class="px-5 py-2 text-left">Droits</th><th class="px-5 py-2 text-left">Dernière utilisation</th><th class="px-5 py-2 text-left">Expire le</th><th class="px-5 py-2"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tokens as $token)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ \Illuminate\Support\Str::after($token->name, ':') }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ implode(', ', $token->abilities ?? []) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $token->last_used_at?->format('d/m/Y H:i') ?? 'jamais' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $token->expires_at?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('club.selections.api-access.destroy', $token->id) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">Révoquer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    <section class="bg-white rounded-lg shadow p-5 space-y-3">
        <h2 class="font-semibold text-gray-900">Documentation</h2>
        <p class="text-sm text-gray-600">Adresse de base : <code class="text-xs bg-gray-100 px-1 rounded">{{ $base }}</code>. Chaque requête porte l'en-tête <code class="text-xs bg-gray-100 px-1 rounded">Authorization: Bearer &lt;jeton&gt;</code> et <code class="text-xs bg-gray-100 px-1 rounded">Accept: application/json</code>. Réponses en JSON sous la clé <code class="text-xs bg-gray-100 px-1 rounded">data</code> ; 403 si le jeton ou le compte n'a pas le droit, 404 si la sélection n'est pas dans votre périmètre, 422 en cas de données invalides.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-gray-500"><tr><th class="py-1 text-left">Méthode</th><th class="py-1 text-left">Chemin</th><th class="py-1 text-left">Droit</th><th class="py-1 text-left">Usage</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($endpoints as [$method, $path, $ability, $usage])
                        <tr class="align-top"><td class="py-2 pr-3 font-mono text-xs font-semibold">{{ $method }}</td><td class="py-2 pr-3 font-mono text-xs whitespace-nowrap">{{ $path }}</td><td class="py-2 pr-3 font-mono text-xs">{{ $ability }}</td><td class="py-2 text-gray-700">{{ $usage }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-500">Exemple : <code class="bg-gray-100 px-1 rounded">curl -H "Authorization: Bearer $JETON" -H "Accept: application/json" {{ $base }}/club/selections?status=return_sent</code></p>
    </section>
</div>
@endsection
