@extends('layouts.app')

@section('title', 'Joueurs - FIT Platform')

@php
    use App\Http\Controllers\PlayerDirectoryController as Directory;
    $active = collect([
        'q' => $filters['q'] ?? null ? '« ' . $filters['q'] . ' »' : null,
        'club_id' => !empty($filters['club_id']) ? ($clubs->firstWhere('id', (int) $filters['club_id'])?->name ?? 'Club') : null,
        'line' => !empty($filters['line']) ? Directory::LINES[$filters['line']] : null,
        'nationality' => $filters['nationality'] ?? null,
        'license' => !empty($filters['license']) ? 'Licence : ' . mb_strtolower(Directory::LICENSES[$filters['license']][0]) : null,
    ])->filter();
    $without = fn (string $key) => route('modules.players.index', array_filter(array_merge(request()->query(), [$key => null, 'page' => null])));
    $licenseTone = ['active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'pending' => 'bg-amber-50 text-amber-800 ring-amber-200', 'expired' => 'bg-slate-100 text-slate-700 ring-slate-200', 'suspended' => 'bg-red-50 text-red-700 ring-red-200'];
    $licenseKey = fn (?string $status) => collect(Directory::LICENSES)->search(fn ($def) => in_array($status, $def[1], true)) ?: null;
    $icons = [
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'license' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h6"/>',
        'passport' => '<rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M9 17h6"/>',
        'medical' => '<path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10Z"/>',
        'portal' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
    ];
@endphp

@section('content')
<div class="min-h-screen bg-slate-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Joueurs"
        subtitle="Recherchez un joueur, filtrez l'effectif et ouvrez sa fiche, sa licence ou son passeport."
        eyebrow="Administration"
        :back-href="route('modules.index')"
        back-label="Retour aux modules"
        :count="$scopeTotal"
        count-label="joueur(s) dans votre périmètre"
    >
        <x-slot:actions>
            @if(Route::has('modules.licenses.index'))
                <a href="{{ route('modules.licenses.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Licences</a>
            @endif
            @if(Route::has('passports.transfer.index'))
                <a href="{{ route('passports.transfer.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Passeports</a>
            @endif
            @if($canCreate)
                <a href="{{ route('player-registration.create') }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Ajouter un joueur</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Barre de filtres : recherche et critères combinables, appliqués dès qu'une liste change. --}}
    <form method="GET" action="{{ route('modules.players.index') }}" id="player-filters" role="search" aria-label="Filtrer les joueurs"
          class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
            <label class="block lg:col-span-4">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Rechercher</span>
                <span class="relative block">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nom, prénom ou identifiant FIFA"
                           class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" autocomplete="off">
                </span>
            </label>
            <label class="block lg:col-span-2">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Club</span>
                <select name="club_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" data-autosubmit>
                    <option value="">Tous les clubs</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" @selected((int) ($filters['club_id'] ?? 0) === $club->id)>{{ str_replace(' (Démo)', '', $club->name) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block lg:col-span-2">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Ligne</span>
                <select name="line" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" data-autosubmit>
                    <option value="">Toutes</option>
                    @foreach(Directory::LINES as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['line'] ?? null) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block lg:col-span-2">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Nationalité</span>
                <select name="nationality" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" data-autosubmit>
                    <option value="">Toutes</option>
                    @foreach($nationalities as $nationality)
                        <option value="{{ $nationality }}" @selected(($filters['nationality'] ?? null) === $nationality)>{{ $nationality }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block lg:col-span-2">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Trier par</span>
                <select name="sort" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200" data-autosubmit>
                    @foreach(Directory::SORTS as $key => $label)
                        <option value="{{ $key }}" @selected($filters['sort'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        @if(!empty($filters['license']))<input type="hidden" name="license" value="{{ $filters['license'] }}">@endif

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-600">Licence :</span>
            @php $licenseLink = fn ($status) => route('modules.players.index', array_filter(array_merge(request()->query(), ['license' => $status, 'page' => null]))); @endphp
            <a href="{{ $licenseLink(null) }}" class="rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ empty($filters['license']) ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' }}" @if(empty($filters['license'])) aria-current="true" @endif>Toutes · {{ number_format($licenseCounts['all'], 0, ',', ' ') }}</a>
            @foreach(Directory::LICENSES as $status => [$label])
                <a href="{{ $licenseLink($status) }}" class="rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ ($filters['license'] ?? null) === $status ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' }}" @if(($filters['license'] ?? null) === $status) aria-current="true" @endif>{{ $label }} · {{ number_format($licenseCounts[$status], 0, ',', ' ') }}</a>
            @endforeach
            <span class="ml-auto flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Filtrer</button>
                @if($active->isNotEmpty())
                    <a href="{{ route('modules.players.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Réinitialiser</a>
                @endif
            </span>
        </div>
    </form>

    <div class="mb-3 flex flex-wrap items-center gap-2 text-sm text-slate-600" aria-live="polite">
        <span><b class="text-slate-900">{{ number_format($players->total(), 0, ',', ' ') }}</b> joueur(s){{ $active->isNotEmpty() ? ' correspondant aux filtres' : '' }}</span>
        @foreach($active as $key => $label)
            <a href="{{ $without($key) }}" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200" aria-label="Retirer le filtre {{ $label }}">{{ $label }} <span aria-hidden="true">×</span></a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if($players->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3">Joueur</th>
                            <th scope="col" class="px-5 py-3">Club</th>
                            <th scope="col" class="px-5 py-3">Poste</th>
                            <th scope="col" class="px-5 py-3">Licence</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($players as $player)
                            @php
                                $license = $player->licenses->first();
                                $name = trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: ($player->name ?? 'Joueur');
                                $initials = collect(preg_split('/\s+/u', $name))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600" aria-hidden="true">{{ $initials }}</span>
                                        <span class="min-w-0">
                                            <a href="{{ route('players.show', $player) }}" class="block font-semibold text-slate-900 hover:underline">{{ $name }}</a>
                                            <span class="block text-xs text-slate-500">{{ collect([$player->nationality, $player->fifa_connect_id ? 'FIFA ' . $player->fifa_connect_id : null])->filter()->implode(' · ') ?: '—' }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="block text-slate-900">{{ $player->club ? str_replace(' (Démo)', '', $player->club->name) : '—' }}</span>
                                    @php $city = $player->club?->getAttributes()['city'] ?? null; @endphp
                                    @if($city)<span class="block text-xs text-slate-500">{{ $city }}</span>@endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($player->position)<span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $player->position }}</span>@else<span class="text-slate-400">—</span>@endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($license && ($key = $licenseKey($license->status)))
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $licenseTone[$key] }}">{{ Directory::LICENSES[$key][0] }}</span>
                                        <span class="block text-xs text-slate-500">{{ $license->expiry_date ? 'jusqu\'au ' . \Illuminate\Support\Carbon::parse($license->expiry_date)->format('d/m/Y') : $license->updated_at?->format('d/m/Y') }}</span>
                                    @elseif($license)
                                        <span class="inline-flex rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">{{ ucfirst((string) $license->status) }}</span>
                                    @else
                                        <span class="text-xs text-slate-500">Sans licence</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('players.show', $player) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Fiche</a>
                                        @if($actions[$player->id])
                                            <details class="relative">
                                                <summary class="flex cursor-pointer list-none items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 [&::-webkit-details-marker]:hidden" aria-label="Autres actions pour {{ $name }}">
                                                    Actions <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                                </summary>
                                                <ul class="absolute right-0 z-20 mt-1 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 text-left shadow-lg">
                                                    @foreach($actions[$player->id] as $action)
                                                        <li><a href="{{ $action['url'] }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$action['icon']] ?? '' !!}</svg>
                                                            {{ $action['label'] }}
                                                        </a></li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($players->hasPages())
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-3 text-sm text-slate-600">
                    <span>{{ $players->firstItem() }}–{{ $players->lastItem() }} sur {{ number_format($players->total(), 0, ',', ' ') }}</span>
                    {{ $players->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <h2 class="text-sm font-semibold text-slate-900">{{ $active->isNotEmpty() ? 'Aucun joueur ne correspond à ces filtres' : 'Aucun joueur dans votre périmètre' }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $active->isNotEmpty() ? 'Retirez un filtre ou réinitialisez la recherche.' : 'Ajoutez un joueur pour commencer.' }}</p>
                <div class="mt-4">
                    @if($active->isNotEmpty())
                        <a href="{{ route('modules.players.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Réinitialiser les filtres</a>
                    @elseif($canCreate)
                        <a href="{{ route('player-registration.create') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Ajouter un joueur</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Une liste modifiée applique les filtres aussitôt (le bouton « Filtrer » reste pour la recherche texte).
    document.querySelectorAll('#player-filters [data-autosubmit]').forEach((el) => el.addEventListener('change', () => el.form.submit()));
    // Un seul menu « Actions » ouvert à la fois, fermé par un clic ailleurs ou Échap.
    document.addEventListener('click', (e) => document.querySelectorAll('details[open]').forEach((d) => { if (!d.contains(e.target)) d.removeAttribute('open'); }));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') document.querySelectorAll('details[open]').forEach((d) => d.removeAttribute('open')); });
</script>
@endpush
