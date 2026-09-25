@extends('layouts.app')

@section('title', 'DTN - Direction Technique Nationale')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">⚽ Direction Technique Nationale</h1>
            <p class="text-sm text-gray-600">
                Pilotage technique basé sur les données du périmètre association autorisé.
            </p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            'Joueurs' => $stats['players'],
            'Clubs' => $stats['clubs'],
            'Équipes' => $stats['teams'],
            'Compétitions' => $stats['competitions'],
            'Performances' => $stats['performance_records'],
            'Joueurs évalués' => $stats['players_with_performance'],
        ] as $label => $value)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">{{ $label }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('modules.players.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md">
            <div class="font-semibold text-gray-900">🏃 Joueurs</div>
            <p class="text-sm text-gray-600 mt-1">Effectifs et profils du périmètre autorisé.</p>
        </a>
        <a href="{{ route('modules.teams.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md">
            <div class="font-semibold text-gray-900">👥 Équipes</div>
            <p class="text-sm text-gray-600 mt-1">Organisation technique des équipes.</p>
        </a>
        <a href="{{ route('modules.competitions.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md">
            <div class="font-semibold text-gray-900">🏆 Compétitions</div>
            <p class="text-sm text-gray-600 mt-1">Compétitions réellement enregistrées.</p>
        </a>
        <a href="{{ route('performances.analytics') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md">
            <div class="font-semibold text-gray-900">📈 Performance Analytics</div>
            <p class="text-sm text-gray-600 mt-1">Analyse des performances enregistrées.</p>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-5 border-b">
            <h2 class="font-semibold text-gray-900">Dernières évaluations de performance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Global</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Physique</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technique</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tactique</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentPerformances as $performance)
                        <tr>
                            <td class="px-4 py-3">{{ $performance->performance_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                {{ trim(($performance->player?->first_name ?? '') . ' ' . ($performance->player?->last_name ?? '')) ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3">{{ $performance->overall_performance_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->physical_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->technical_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->tactical_score ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Aucune performance enregistrée dans le périmètre autorisé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
