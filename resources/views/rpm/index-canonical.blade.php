@extends('layouts.app')

@section('title', 'RPM - Real-time Performance Monitoring')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">⚡ Real-time Performance Monitoring</h1>
            <p class="text-sm text-gray-600">
                Dernières mesures réellement enregistrées dans player_real_time_health.
            </p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Mesures chargées</div>
            <div class="text-2xl font-bold">{{ $stats['measurements'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Joueurs</div>
            <div class="text-2xl font-bold">{{ $stats['players'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Alertes sur les mesures</div>
            <div class="text-2xl font-bold">{{ $stats['active_alerts'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Dernière mesure</div>
            <div class="text-sm font-semibold">
                {{ $stats['latest_measurement'] ? CarbonCarbon::parse($stats['latest_measurement'])->format('d/m/Y H:i') : 'N/A' }}
            </div>
        </div>
    </div>

    @if($stats['measurements'] === 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 text-yellow-800">
            Aucune mesure temps réel n'est disponible dans le périmètre autorisé. Le module ne simule aucune donnée.
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesure</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FC</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SpO₂</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temp.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hydratation</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Récupération</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Readiness</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($measurements as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ CarbonCarbon::parse($row->measurement_time)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: 'N/A' }}
                                @if($row->club_name)<div class="text-xs text-gray-500">{{ $row->club_name }}</div>@endif
                            </td>
                            <td class="px-4 py-3">{{ $row->heart_rate ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $row->oxygen_saturation ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $row->temperature ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $row->hydration_level ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $row->recovery_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $row->readiness_score ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $row->data_source ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Aucune mesure disponible.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
