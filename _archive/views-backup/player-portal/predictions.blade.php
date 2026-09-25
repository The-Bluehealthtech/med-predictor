<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prédictions - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Prédictions Médicales</h1>
        <p class="text-gray-600">Analyse prédictive de votre santé et performances</p>
    </div>

    @if(Auth::user() && Auth::user()->player && Auth::user()->player->medicalPredictions->count() > 0)
        <div class="space-y-6">
            @foreach(Auth::user()->player->medicalPredictions as $prediction)
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ ucfirst($prediction->prediction_type ?? 'Prédiction') }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $prediction->prediction_date ? $prediction->prediction_date->format('d/m/Y') : 'Date non spécifiée' }}
                        </p>
                        @if($prediction->predicted_condition)
                            <p class="text-sm text-gray-700 mt-2">
                                <strong>Condition prédite:</strong> {{ $prediction->predicted_condition }}
                            </p>
                        @endif
                        @if($prediction->risk_probability)
                            <p class="text-sm text-gray-700">
                                <strong>Probabilité de risque:</strong> {{ number_format($prediction->risk_probability * 100, 1) }}%
                            </p>
                        @endif
                        @if($prediction->confidence_score)
                            <p class="text-sm text-gray-700">
                                <strong>Score de confiance:</strong> {{ number_format($prediction->confidence_score * 100, 1) }}%
                            </p>
                        @endif
                        @if($prediction->recommendations)
                            <p class="text-sm text-gray-700 mt-2">
                                <strong>Recommandations:</strong> {{ $prediction->recommendations }}
                            </p>
                        @endif
                        @if($prediction->prediction_notes)
                            <p class="text-sm text-gray-700 mt-2">
                                <strong>Notes:</strong> {{ $prediction->prediction_notes }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($prediction->confidence_score)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $prediction->confidence_score > 0.8 ? 'bg-green-100 text-green-800' : 
                                   ($prediction->confidence_score > 0.6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($prediction->confidence_score * 100, 0) }}% confiance
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white shadow rounded-lg">
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune prédiction disponible</h3>
                <p class="text-gray-500">Aucune prédiction médicale n'a été générée pour le moment.</p>
            </div>
        </div>
    @endif
    </div>
</body>
</html>
