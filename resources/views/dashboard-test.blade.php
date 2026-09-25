<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Dashboard FIT</h1>
            <a href="{{ route('modules.index') }}" class="text-blue-700 font-semibold hover:underline">Modules</a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-2">Vue d’ensemble</h2>
        <p class="text-gray-600 mb-5">Enregistrements accessibles à votre compte, comptés dans la base FIT.</p>
        <div class="bg-amber-50 border border-amber-300 text-amber-950 rounded-xl p-4 mb-8" role="note">
            Ces chiffres décrivent les enregistrements présents en base. Certains peuvent provenir de jeux de démonstration ; leur identité et leur conformité FIFA Connect ne sont pas validées par ce tableau de bord.
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $label => $count)
                <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-sm font-medium text-gray-600">{{ $label }}</h3>
                    <p class="text-3xl font-semibold mt-2">{{ number_format($count, 0, ',', ' ') }}</p>
                </section>
            @endforeach
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Répartition par poste</h3>
                <p class="text-sm text-gray-500 mb-5">Postes renseignés dans les fiches joueurs.</p>
                @forelse ($positions as $position)
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $position->position ?: 'Non renseigné' }}</span>
                            <strong>{{ number_format($position->total, 0, ',', ' ') }}</strong>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $stats['Joueurs'] ? round($position->total / $stats['Joueurs'] * 100, 1) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Aucun joueur dans ce périmètre.</p>
                @endforelse
            </section>
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Nouvelles fiches joueurs</h3>
                <p class="text-sm text-gray-500 mb-5">Créations enregistrées en base, par mois.</p>
                @foreach ($monthlyPlayers as $month)
                    <div class="flex items-center gap-4 mb-3 text-sm">
                        <span class="w-20 text-gray-600">{{ $month['label'] }}</span>
                        <div class="flex-1 h-5 bg-gray-100 rounded overflow-hidden">
                            <div class="h-full bg-teal-500" style="width: {{ $monthlyPlayers->max('count') ? round($month['count'] / $monthlyPlayers->max('count') * 100, 1) : 0 }}%"></div>
                        </div>
                        <strong class="w-10 text-right">{{ $month['count'] }}</strong>
                    </div>
                @endforeach
            </section>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Clubs et effectifs</h3>
                <p class="text-sm text-gray-500 mb-5">Cinq clubs du périmètre, classés par nombre de joueurs.</p>
                <div class="divide-y divide-gray-100">
                    @forelse ($topClubs as $club)
                        <div class="flex justify-between gap-4 py-3">
                            <span>{{ $club->name }}</span>
                            <span class="font-semibold">{{ number_format($club->players_count, 0, ',', ' ') }} joueurs</span>
                        </div>
                    @empty
                        <p class="text-gray-500">Aucun club dans ce périmètre.</p>
                    @endforelse
                </div>
            </section>
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">Fiches joueurs récentes</h3>
                <p class="text-sm text-gray-500 mb-5">Derniers enregistrements créés dans la base.</p>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentPlayers as $player)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <div>
                                <a href="{{ route('joueur.portal', ['playerId' => $player->id]) }}" class="font-medium text-blue-700 hover:underline">{{ trim($player->first_name . ' ' . $player->last_name) ?: $player->name }}</a>
                                <p class="text-sm text-gray-500">{{ $player->club?->name ?? 'Club non renseigné' }} · {{ $player->position ?: 'Poste non renseigné' }}</p>
                            </div>
                            <time class="text-xs text-gray-500 whitespace-nowrap">{{ $player->created_at?->format('d/m/Y') }}</time>
                        </div>
                    @empty
                        <p class="text-gray-500">Aucune fiche récente.</p>
                    @endforelse
                </div>
            </section>
        </div>
        @if (array_key_exists('Compétitions', $stats))
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mt-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Compétitions récentes</h3>
                        <p class="text-sm text-gray-500">Enregistrements du périmètre autorisé.</p>
                    </div>
                    <a href="{{ route('modules.competitions.index') }}" class="text-blue-700 hover:underline">Voir les compétitions</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentCompetitions as $competition)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="font-medium">{{ $competition->name }}</span>
                            <span class="text-sm text-gray-500">{{ $competition->status ?: 'Statut non renseigné' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">Aucune compétition dans ce périmètre.</p>
                    @endforelse
                </div>
            </section>
        @endif
    </main>
</body>
</html>
