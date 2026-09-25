@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📊 Analytics Dashboard</h1>
            <p class="text-sm text-gray-600">Agrégats et alertes réels dans le périmètre autorisé.</p>
        </div>
        <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            'Performances' => $stats['performance_records'],
            'Score global moyen' => $stats['average_overall'],
            'Physique moyen' => $stats['average_physical'],
            'Technique moyen' => $stats['average_technical'],
            'Alertes actives' => $stats['active_alerts'],
            'Alertes critiques' => $stats['critical_alerts'],
        ] as $label => $value)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">{{ $label }}</div>
                <div class="text-xl font-bold text-gray-900">{{ $value ?? 'N/A' }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <a href="{{ route('performances.analytics') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md">
            <h2 class="font-semibold text-gray-900">📈 Performance Analytics</h2>
            <p class="text-sm text-gray-600 mt-2">Tendances et scores enregistrés dans player_performances.</p>
        </a>
        <a href="{{ route('analytics.digital-twin') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md">
            <h2 class="font-semibold text-gray-900">🔄 Digital Twin</h2>
            <p class="text-sm text-gray-600 mt-2">Scénarios de simulation, séparés des données observées.</p>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-5 border-b">
            <h2 class="font-semibold text-gray-900">Alertes de performance actives</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($alerts as $alert)
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-900">{{ $alert->title }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ $alert->description }}</div>
                            <div class="text-xs text-gray-500 mt-2">
                                @if($alert->player)
                                    {{ trim(($alert->player->first_name ?? '') . ' ' . ($alert->player->last_name ?? '')) }}
                                @endif
                                @if($alert->club)
                                    @if($alert->player) — @endif{{ $alert->club->name }}
                                @endif
                            </div>
                        </div>
                        <span class="text-xs font-semibold uppercase text-gray-600">{{ $alert->alert_level }}</span>
                    </div>
                    @if($alert->trigger_value !== null || $alert->threshold_value !== null)
                        <div class="text-xs text-gray-500 mt-2">
                            Valeur: {{ $alert->trigger_value ?? 'N/A' }}
                            · Seuil: {{ $alert->threshold_value ?? 'N/A' }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    Aucune alerte active dans le périmètre autorisé.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
