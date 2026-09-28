@extends('layouts.app')

@section('title', 'RPM - Real-time Performance Monitoring')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">⚡ Real-time Performance Monitoring</h1>
            <p class="text-sm text-gray-600">{{ __('Dernières mesures réellement enregistrées dans player_real_time_health.') }}</p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Mesures chargées') }}</div>
            <div class="text-2xl font-bold">{{ $stats['measurements'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">{{ __('common.players') }}</div>
            <div class="text-2xl font-bold">{{ $stats['players'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Alertes sur les mesures') }}</div>
            <div class="text-2xl font-bold">{{ $stats['active_alerts'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">{{ __('Dernière mesure') }}</div>
            <div class="text-sm font-semibold">
                {{ $stats['latest_measurement'] ? \Carbon\Carbon::parse($stats['latest_measurement'])->format('d/m/Y H:i') : 'N/A' }}
            </div>
        </div>
    </div>

    @if($stats['measurements'] === 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5 text-yellow-800">{{ __('Aucune mesure temps réel n\'est disponible dans le périmètre autorisé. Le module ne simule aucune donnée.') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesure</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('auth.role_player') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FC</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SpO₂</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temp.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hydratation</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('medical_predictions.dashboard_type_recovery_short') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Readiness</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($measurements as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($row->measurement_time)->format('d/m/Y H:i') }}</td>
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
                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">{{ __('Aucune mesure disponible.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <section class="rpm-team-data mt-6">
        <div class="fifa-stat-card">
            <div class="fifa-stat-header">
                <span>Données de jeu · équipe</span>
                <span class="text-xs opacity-70">Source KSA · affichage séparé du RPM</span>
            </div>
            <div class="grid gap-3 md:grid-cols-4 mt-4">
                <div class="fifa-stat-card"><div class="text-xs opacity-70">Joueurs couverts</div><div class="fifa-stat-value">{{ $advancedMetrics->pluck('player_id')->filter()->unique()->count() ?: 'Non disponible' }}</div></div>
                <div class="fifa-stat-card"><div class="text-xs opacity-70">Métriques renseignées</div><div class="fifa-stat-value">{{ $advancedMetrics->whereNotNull('metric_value')->count() ?: 'Non disponible' }}</div></div>
                <div class="fifa-stat-card"><div class="text-xs opacity-70">Métriques suivies</div><div class="fifa-stat-value">{{ $advancedMetrics->pluck('metric_name')->unique()->count() }}</div></div>
                <div class="fifa-stat-card"><div class="text-xs opacity-70">Source</div><div class="fifa-stat-value">KSA</div></div>
            </div>
            <div class="overflow-x-auto mt-5">
                <table class="fifa-license-table w-full">
                    <thead><tr><th>Joueur</th><th>Métrique</th><th>Valeur</th><th>Unité</th></tr></thead>
                    <tbody>
                    @forelse($advancedMetrics as $metric)
                        <tr><td>{{ $metric->player_name ?? 'Équipe' }}</td><td>{{ $metric->metric_name }}</td><td>{{ $metric->metric_value ?? 'Non disponible' }}</td><td>{{ $metric->metric_unit ?: '—' }}</td></tr>
                    @empty
                        <tr><td colspan="4">Aucune donnée disponible.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
