@extends('layouts.app')

@section('title', 'Historique des Évaluations Posturales')

@section('content')
<div class="container mx-auto p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            📊 Historique des Évaluations Posturales
        </h1>
        <p class="text-gray-600">
            Comparaison des évaluations posturales pour {{ $player->name }}
        </p>
    </div>

    <!-- Player Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $player->name }}</h2>
                <p class="text-gray-600">{{ $player->position }} • {{ $player->club->name ?? 'Club non défini' }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Score de risque actuel</div>
                <div class="text-2xl font-bold {{ $player->injury_risk_level === 'high' ? 'text-red-600' : ($player->injury_risk_level === 'moderate' ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ number_format($player->injury_risk_score * 100, 1) }}%
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($player->injury_risk_level) }} risk</div>
            </div>
        </div>
    </div>

    <!-- Trends Overview -->
    @if(isset($comparison['trends']) && !empty($comparison['trends']))
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Tendances</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">
                    @if($comparison['trends']['score_trend'] === 'improving')
                        📈
                    @elseif($comparison['trends']['score_trend'] === 'worsening')
                        📉
                    @else
                        ➡️
                    @endif
                </div>
                <div class="text-sm text-gray-600">Tendance</div>
                <div class="font-semibold text-gray-900">
                    {{ ucfirst($comparison['trends']['score_trend']) }}
                </div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">
                    {{ number_format($comparison['trends']['improvement_rate'], 1) }}%
                </div>
                <div class="text-sm text-gray-600">Taux de changement</div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">
                    {{ count($comparison['assessments']) }}
                </div>
                <div class="text-sm text-gray-600">Évaluations</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Assessments Timeline -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Timeline des Évaluations</h3>
        
        <div class="space-y-4">
            @forelse($comparison['assessments'] as $assessment)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($assessment['date'])->format('d/m/Y H:i') }}
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $assessment['score'] >= 7 ? 'bg-red-100 text-red-800' : 
                                       ($assessment['score'] >= 4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    Score: {{ $assessment['score'] }}/10
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $assessment['imbalances_count'] }} déséquilibres
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $assessment['risk_factors_count'] }} facteurs de risque
                                </span>
                            </div>
                        </div>
                        
                        @if(!empty($assessment['recommendations']))
                        <div class="mt-2">
                            <div class="text-sm font-medium text-gray-700 mb-1">Recommandations:</div>
                            <ul class="text-xs text-gray-600 space-y-1">
                                @foreach(array_slice($assessment['recommendations'], 0, 3) as $recommendation)
                                <li class="flex items-center">
                                    <span class="w-1 h-1 bg-blue-500 rounded-full mr-2"></span>
                                    {{ $recommendation }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('postural-assessments.show', $assessment['id']) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Voir détails
                        </a>
                        <a href="{{ route('postural-assessments.edit', $assessment['id']) }}" 
                           class="text-green-600 hover:text-green-800 text-sm">
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-4">📊</div>
                <p>Aucune évaluation posturale trouvée pour ce joueur.</p>
                <a href="{{ route('postural-assessments.create') }}" 
                   class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Créer la première évaluation
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Risk Analysis -->
    @if(isset($comparison['assessments']) && count($comparison['assessments']) > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Analyse des Risques</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Risk Factors -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Facteurs de Risque Détectés</h4>
                <div class="space-y-2">
                    @php
                        $riskFactors = collect($comparison['assessments'])
                            ->flatMap(function($assessment) {
                                return $assessment['recommendations'] ?? [];
                            })
                            ->filter(function($rec) {
                                return str_contains(strtolower($rec), 'risque') || 
                                       str_contains(strtolower($rec), 'anormal') ||
                                       str_contains(strtolower($rec), 'surveillance');
                            })
                            ->unique()
                            ->take(5);
                    @endphp
                    
                    @forelse($riskFactors as $factor)
                    <div class="flex items-center p-2 bg-red-50 rounded">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                        <span class="text-sm text-red-800">{{ $factor }}</span>
                    </div>
                    @empty
                    <div class="text-sm text-gray-500">Aucun facteur de risque majeur détecté</div>
                    @endforelse
                </div>
            </div>
            
            <!-- Improvements -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Améliorations Recommandées</h4>
                <div class="space-y-2">
                    @php
                        $improvements = collect($comparison['assessments'])
                            ->flatMap(function($assessment) {
                                return $assessment['recommendations'] ?? [];
                            })
                            ->filter(function($rec) {
                                return str_contains(strtolower($rec), 'exercice') || 
                                       str_contains(strtolower($rec), 'étirement') ||
                                       str_contains(strtolower($rec), 'massage');
                            })
                            ->unique()
                            ->take(5);
                    @endphp
                    
                    @forelse($improvements as $improvement)
                    <div class="flex items-center p-2 bg-green-50 rounded">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                        <span class="text-sm text-green-800">{{ $improvement }}</span>
                    </div>
                    @empty
                    <div class="text-sm text-gray-500">Aucune amélioration spécifique recommandée</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('players.show', $player) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
            ← Retour au profil
        </a>
        
        <div class="flex space-x-4">
            <a href="{{ route('postural-assessments.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                ➕ Nouvelle Évaluation
            </a>
            
            @if(count($comparison['assessments']) > 0)
            <button onclick="exportHistory()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                💾 Exporter l'Historique
            </button>
            @endif
        </div>
    </div>
</div>

<script>
function exportHistory() {
    // Exporter les données d'historique
    const data = @json($comparison);
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `postural-history-{{ $player->name }}-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection 