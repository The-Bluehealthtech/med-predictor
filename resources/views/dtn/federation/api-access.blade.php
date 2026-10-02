@extends('layouts.app')

{{-- Espace fédération (DTN) : jetons d'API et documentation des appels de l'espace. --}}

@section('title', 'Accès API — ' . $config['label'])

@php
    $base = rtrim(url('/api/v1'), '/');
    $endpoints = [
        ['GET', '/dtn/players?q=&club_id=&position=&per_page=', 'dtn:players:read', 'Rechercher des joueurs (fiches résumées, tous clubs)'],
        ['GET', '/dtn/players/{id}', 'dtn:players:read', 'Fiche joueur : identité sportive, données de match, historique des sélections (sans donnée médicale)'],
        ['POST', '/dtn/selections', 'dtn:selections:write', 'Convoquer : player_id, team_label, event_type (friendly, qualifier, tournament, training_camp), event_name, opponent, start_date, end_date, convocation_note'],
        ['GET', '/dtn/selections?status=', 'dtn:selections:read', 'Sélections de la fédération, avec l\'état de départ envoyé par le club'],
        ['GET', '/dtn/selections/{id}', 'dtn:selections:read', 'Détail d\'une sélection (?include_medical=1 : partie médicale, droit selections:medical requis)'],
        ['PUT', '/dtn/selections/{id}/return', 'dtn:selections:write', 'État de retour : matches, starts, minutes, goals, assists, yellow_cards, red_cards, avg_rating, training_sessions, incidents, staff_evaluation, evaluation_comment, fatigue_level, injury_risk, recommendations ; send=true pour l\'envoyer ; medical{} et fitness_status avec selections:medical'],
        ['POST', '/dtn/selections/{id}/cancel', 'dtn:selections:write', 'Annuler une convocation'],
        ['GET', '/passports/medical/{id}?purpose=transfer|selection', 'selections:medical', 'Passeport médical du joueur en HL7 FHIR (Bundle IPS de type document), pour un transfert ou une sélection ; compte médical ayant accès au joueur'],
    ];
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    @include('dtn.federation.partials.nav', ['active' => 'api'])
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Accès API</h1>
        <p class="text-sm text-gray-600 max-w-2xl">Créez un jeton pour connecter le logiciel de la Direction technique nationale. Un jeton est limité à cet espace et aux droits de votre compte ; il n'est affiché qu'une seule fois.</p>
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
        <form method="POST" action="{{ route('dtn.api-access.store') }}" class="mt-3 flex flex-wrap items-end gap-3">
            @csrf
            <label class="block text-sm font-medium text-gray-700">Nom du logiciel
                <input name="name" required maxlength="60" class="mt-1 block w-64 rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Ex. Logiciel DTN">
            </label>
            <label class="block text-sm font-medium text-gray-700">Validité
                <select name="expires_in_days" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm"><option value="30">30 jours</option><option value="90" selected>90 jours</option><option value="365">1 an</option></select>
            </label>
            @if($canHaveMedical)
                <label class="flex items-center gap-2 text-sm text-gray-700 pb-2"><input type="checkbox" name="with_medical" value="1" class="rounded border-gray-300"> Inclure le droit médical (selections:medical)</label>
            @endif
            <button class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Créer le jeton</button>
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
                                <form method="POST" action="{{ route('dtn.api-access.destroy', $token->id) }}">
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
        <p class="text-xs text-gray-500">Exemple : <code class="bg-gray-100 px-1 rounded">curl -H "Authorization: Bearer $JETON" -H "Accept: application/json" {{ $base }}/dtn/players?q=roux</code></p>
    </section>
</div>
@endsection
