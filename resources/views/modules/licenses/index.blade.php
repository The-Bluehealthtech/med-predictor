@extends('layouts.app')

@section('title', 'Demande de licence - FIT Platform')

@php
    use App\Services\Licensing\LicenseWorkflow;
    $tone = ['pending' => 'bg-amber-50 text-amber-800 ring-amber-200', 'justification_requested' => 'bg-orange-50 text-orange-800 ring-orange-200',
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'revoked' => 'bg-red-50 text-red-700 ring-red-200'];
    $playerName = fn ($p) => $p ? (trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: ($p->name ?? 'Joueur')) : 'Joueur';
    $holder = fn ($license) => app(LicenseWorkflow::class)->holderName($license);
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Demande de licence"
        subtitle="Côté club : choisissez un joueur, remplissez la demande et envoyez-la à la fédération, qui l'approuve."
        eyebrow="Licences · club"
        :back-href="route('modules.index', ['section' => 'administration'])"
        back-label="Retour aux modules"
    >
        @if($canApprove)
            <x-slot:actions>
                <a href="{{ route('licenses.validation') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Aller à l'approbation (fédération)</a>
            </x-slot:actions>
        @endif
    </x-page-header>

    @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $key => $class)
        @if(session($key))<div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ $class }}" role="status">{{ session($key) }}</div>@endif
    @endforeach

    {{-- Le processus en trois étapes, pour savoir qui fait quoi. --}}
    <ol class="mb-6 grid gap-3 sm:grid-cols-3" aria-label="Étapes de la demande de licence">
        <li class="rounded-2xl border border-slate-200 bg-white p-4">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Étape 1 · Club</span>
            <p class="mt-1 font-semibold text-slate-900">Choisir le joueur</p>
            <p class="text-sm text-slate-600">Un joueur, un officiel d'équipe ou un dirigeant sans licence active ni demande en cours.</p>
        </li>
        <li class="rounded-2xl border border-slate-200 bg-white p-4">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Étape 2 · Club</span>
            <p class="mt-1 font-semibold text-slate-900">Remplir et envoyer la demande</p>
            <p class="text-sm text-slate-600">Saison, discipline, niveau et nature (FIFA Connect), pièces exigées par le barème. Elle part aussitôt à la fédération, qui est notifiée.</p>
        </li>
        <li class="rounded-2xl border border-slate-200 bg-white p-4">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Étape 3 · Fédération</span>
            <p class="mt-1 font-semibold text-slate-900">Approbation</p>
            <p class="text-sm text-slate-600">La fédération vérifie pièces et identité (FIFA ID, si le registre est connecté), puis approuve, demande un complément ou refuse. Vous êtes notifié à chaque décision.</p>
        </li>
    </ol>

    <div class="mb-6 grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">Compléments à fournir</span><b class="block text-2xl text-slate-900">{{ $counts['info'] }}</b></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">En attente de la fédération</span><b class="block text-2xl text-slate-900">{{ $counts['pending'] }}</b></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">Licences actives</span><b class="block text-2xl text-slate-900">{{ number_format($counts['active'], 0, ',', ' ') }}</b></div>
    </div>

    @php $toComplete = $requests->where('status', 'justification_requested'); @endphp
    @if($toComplete->isNotEmpty())
        <section class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 p-5" aria-labelledby="h-complete">
            <h2 id="h-complete" class="text-lg font-semibold text-orange-900">À compléter pour la fédération</h2>
            <ul class="mt-3 grid gap-2">
                @foreach($toComplete as $license)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-orange-200 bg-white p-4">
                        <span>
                            <span class="block font-semibold text-slate-900">{{ $holder($license) }} · {{ LicenseWorkflow::describe($license) }}</span>
                            <span class="block text-sm text-slate-700"><span class="font-semibold">Demande de la fédération :</span> {{ $license->rejection_reason }}</span>
                        </span>
                        <a href="{{ route('player-licenses.show', $license) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Compléter la demande</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="h-requests">
        <h2 id="h-requests" class="border-b border-slate-200 px-5 py-3 text-lg font-semibold text-slate-900">Suivi des demandes</h2>
        @if($requests->isEmpty())
            <p class="px-5 py-6 text-sm text-slate-500">Aucune demande pour l'instant.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-2">Titulaire</th><th class="px-5 py-2">Licence</th><th class="px-5 py-2">Validité</th><th class="px-5 py-2">Statut</th><th class="px-5 py-2">Pièces</th><th class="px-5 py-2">Mise à jour</th><th class="px-5 py-2 text-right">Suivi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($requests->take(30) as $license)
                            <tr>
                                <td class="px-5 py-2 font-medium text-slate-900">{{ $holder($license) }}<span class="block text-xs font-normal text-slate-500">{{ $license->club?->name }}</span></td>
                                <td class="px-5 py-2">{{ LicenseWorkflow::describe($license) }}{{ $license->season ? ' · ' . $license->season : '' }}</td>
                                <td class="px-5 py-2">{{ $license->expiry_date ? 'jusqu\'au ' . $license->expiry_date->format('d/m/Y') : '—' }}</td>
                                <td class="px-5 py-2">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $tone[$license->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">{{ LicenseWorkflow::STATUS_LABELS[$license->status] ?? $license->status }}</span>
                                    @if($license->status === 'revoked' && $license->rejection_reason)<span class="block text-xs text-slate-500">Motif : {{ $license->rejection_reason }}</span>@endif
                                </td>
                                <td class="px-5 py-2 text-slate-600">{{ $license->documents_count }}</td>
                                <td class="px-5 py-2 text-slate-500">{{ $license->updated_at?->format('d/m/Y') }}</td>
                                <td class="px-5 py-2 text-right"><a href="{{ route('player-licenses.show', $license) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Suivre</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="h-players">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-3">
            <h2 id="h-players" class="text-lg font-semibold text-slate-900">Licences de joueurs</h2>
            <form method="GET" action="{{ route('modules.licenses.index') }}" role="search" class="flex gap-2">
                <label class="sr-only" for="lic-q">Rechercher un joueur</label>
                <input id="lic-q" type="search" name="q" value="{{ $search }}" placeholder="Nom du joueur" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <button class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Rechercher</button>
            </form>
        </div>
        @if($players->count() === 0)
            <p class="px-5 py-6 text-sm text-slate-500">{{ $search !== '' ? 'Aucun joueur sans licence ne correspond à cette recherche.' : 'Tous les joueurs de votre périmètre ont une licence active ou une demande en cours.' }}</p>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($players as $player)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                        <span><span class="font-medium text-slate-900">{{ $playerName($player) }}</span><span class="block text-xs text-slate-500">{{ collect([$player->club?->name, $player->position, $player->fifa_connect_id ? 'FIFA ' . $player->fifa_connect_id : 'sans identifiant FIFA'])->filter()->implode(' · ') }}</span></span>
                        <a href="{{ route('player-licenses.request.create', $player) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Demander une licence</a>
                    </li>
                @endforeach
            </ul>
            @if($players->hasPages())<div class="border-t border-slate-200 px-5 py-3">{{ $players->links() }}</div>@endif
        @endif
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="h-officials">
        <div class="border-b border-slate-200 px-5 py-3">
            <h2 id="h-officials" class="text-lg font-semibold text-slate-900">Licences des officiels d'équipe et des dirigeants</h2>
            <p class="text-sm text-slate-600">Enregistrements FIFA Connect « TeamOfficial » et « OrganisationOfficial » de la fiche « Dirigeants et staff », sans licence pour la saison {{ $season }}.</p>
        </div>
        @if($officials->isEmpty())
            <p class="px-5 py-6 text-sm text-slate-500">Aucun officiel ou dirigeant sans licence pour cette saison.</p>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($officials as $official)
                    @php
                        $role = $official->registration_type === 'TeamOfficial'
                            ? config('fifa_connect_roles.team_official.' . $official->team_official_role . '.label', $official->team_official_role)
                            : config('fifa_connect_roles.organisation_official.' . $official->organisation_official_role . '.label', $official->organisation_official_role);
                    @endphp
                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                        <span><span class="font-medium text-slate-900">{{ trim($official->international_first_name . ' ' . $official->international_last_name) ?: $official->popular_name }}</span>
                            <span class="block text-xs text-slate-500">{{ config('licensing.registration_types.' . $official->registration_type, $official->registration_type) }} · {{ $role }}{{ $official->person_fifa_id ? ' · FIFA ' . $official->person_fifa_id : ' · sans identifiant FIFA' }}</span></span>
                        <a href="{{ route('official-licenses.request.create', $official) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Demander une licence</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
