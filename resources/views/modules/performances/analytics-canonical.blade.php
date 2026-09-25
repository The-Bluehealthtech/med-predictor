@extends('layouts.app')

@section('title', 'Performance Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📈 Performance Analytics</h1>
            <p class="text-sm text-gray-600">Données enregistrées dans player_performances. Aucune valeur de démonstration.</p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            'Enregistrements' => $stats['records'],
            'Score global moyen' => $stats['overall'],
            'Buts' => $stats['goals'],
            'Passes décisives' => $stats['assists'],
        ] as $label => $value)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">{{ $label }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ $value ?? 'N/A' }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach([
            'Physique' => $stats['physical'],
            'Technique' => $stats['technical'],
            'Tactique' => $stats['tactical'],
            'Mental' => $stats['mental'],
            'Social' => $stats['social'],
        ] as $label => $value)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">{{ $label }}</div>
                <div class="text-xl font-semibold text-gray-900">{{ $value ?? 'N/A' }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-900 mb-4">Évolution du score global</h2>
            @if(count($trend['values']) > 0)
                <canvas id="performanceTrend"></canvas>
            @else
                <p class="text-sm text-gray-500">Aucune donnée historique disponible.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-900 mb-4">Meilleures moyennes enregistrées</h2>
            @if(count($topPerformers['values']) > 0)
                <canvas id="topPerformers"></canvas>
            @else
                <p class="text-sm text-gray-500">Aucune donnée de performance disponible.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-5 border-b">
            <h2 class="font-semibold text-gray-900">Performances récentes</h2>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mental</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Social</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recent as $performance)
                        <tr>
                            <td class="px-4 py-3">{{ $performance->performance_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                {{ trim(($performance->player?->first_name ?? '') . ' ' . ($performance->player?->last_name ?? '')) ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3">{{ $performance->overall_performance_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->physical_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->technical_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->tactical_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->mental_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $performance->social_score ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Aucune performance enregistrée dans le périmètre autorisé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(count($trend['values']) > 0 || count($topPerformers['values']) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(count($trend['values']) > 0)
new Chart(document.getElementById('performanceTrend'), {
    type: 'line',
    data: {
        labels: @json($trend['labels']),
        datasets: [{
            label: 'Score global',
            data: @json($trend['values'])
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
});
@endif

@if(count($topPerformers['values']) > 0)
new Chart(document.getElementById('topPerformers'), {
    type: 'bar',
    data: {
        labels: @json($topPerformers['labels']),
        datasets: [{
            label: 'Moyenne',
            data: @json($topPerformers['values'])
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
});
@endif
</script>
@endif
@endsection
