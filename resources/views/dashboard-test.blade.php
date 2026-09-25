<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('dashboard_test.title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('dashboard_test.title') }}</h1>
            <div class="flex items-center gap-4"><a href="{{ route('modules.index') }}" class="text-blue-700 font-semibold hover:underline">{{ __('dashboard_test.modules') }}</a><x-language-switcher /></div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-2">{{ __('dashboard_test.overview') }}</h2>
        <p class="text-gray-600 mb-5">{{ __('dashboard_test.scope') }}</p>
        <div class="bg-amber-50 border border-amber-300 text-amber-950 rounded-xl p-4 mb-8" role="note">
            {{ __('dashboard_test.provenance') }}
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $label => $count)
                <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard_test.' . ['Joueurs' => 'players', 'Clubs' => 'clubs', 'Équipes' => 'teams', 'Compétitions' => 'competitions'][$label]) }}</h3>
                    <p class="text-3xl font-semibold mt-2">{{ number_format($count, 0, ',', ' ') }}</p>
                </section>
            @endforeach
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">{{ __('dashboard_test.positions') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('dashboard_test.positions_help') }}</p>
                @forelse ($positions as $position)
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $position->position ?: '{{ __('dashboard_test.unknown') }}' }}</span>
                            <strong>{{ number_format($position->total, 0, ',', ' ') }}</strong>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $stats['Joueurs'] ? round($position->total / $stats['Joueurs'] * 100, 1) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">{{ __('dashboard_test.no_players') }}</p>
                @endforelse
            </section>
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">{{ __('dashboard_test.new_players') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('dashboard_test.new_players_help') }}</p>
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
                <h3 class="text-lg font-semibold mb-1">{{ __('dashboard_test.club_squads') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('dashboard_test.club_squads_help') }}</p>
                <div class="divide-y divide-gray-100">
                    @forelse ($topClubs as $club)
                        <div class="flex justify-between gap-4 py-3">
                            <span>{{ $club->name }}</span>
                            <span class="font-semibold">{{ __('dashboard_test.players_count', ['count' => number_format($club->players_count, 0, ',', ' ')]) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">{{ __('dashboard_test.no_clubs') }}</p>
                    @endforelse
                </div>
            </section>
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-1">{{ __('dashboard_test.recent_players') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('dashboard_test.recent_players_help') }}</p>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentPlayers as $player)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <div>
                                <a href="{{ route('joueur.portal', ['playerId' => $player->id]) }}" class="font-medium text-blue-700 hover:underline">{{ trim($player->first_name . ' ' . $player->last_name) ?: $player->name }}</a>
                                <p class="text-sm text-gray-500">{{ $player->club?->name ?? '{{ __('dashboard_test.unknown_club') }}' }} · {{ $player->position ?: '{{ __('dashboard_test.unknown_position') }}' }}</p>
                            </div>
                            <time class="text-xs text-gray-500 whitespace-nowrap">{{ $player->created_at?->format('d/m/Y') }}</time>
                        </div>
                    @empty
                        <p class="text-gray-500">{{ __('dashboard_test.no_recent_players') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
        @if (array_key_exists('Compétitions', $stats))
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mt-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('dashboard_test.recent_competitions') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('dashboard_test.recent_competitions_help') }}</p>
                    </div>
                    <a href="{{ route('modules.competitions.index') }}" class="text-blue-700 hover:underline">{{ __('dashboard_test.view_competitions') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($recentCompetitions as $competition)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="font-medium">{{ $competition->name }}</span>
                            <span class="text-sm text-gray-500">{{ $competition->status ?: '{{ __('dashboard_test.unknown_status') }}' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">{{ __('dashboard_test.no_competitions') }}</p>
                    @endforelse
                </div>
            </section>
        @endif
    </main>
</body>
</html>
