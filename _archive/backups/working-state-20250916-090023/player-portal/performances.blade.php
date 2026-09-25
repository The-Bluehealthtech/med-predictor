<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performances - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes Performances</h1>
        <p class="text-gray-600">Suivi détaillé de vos performances sportives</p>
    </div>

    @if($performances->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($performances as $performance)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Performance du {{ $performance->performance_date ? $performance->performance_date->format('d/m/Y') : 'Date non spécifiée' }}
                        </h2>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Score: {{ number_format($performance->overall_performance_score ?? 0, 1) }}/10
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Scores Physiques</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Endurance</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->endurance_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Force</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->strength_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Vitesse</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->speed_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Agilité</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->agility_score ?? 0, 1) }}/10</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Scores Techniques</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Technique</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->technical_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Tactique</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->tactical_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Mental</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->mental_score ?? 0, 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Social</span>
                                    <span class="text-sm font-medium">{{ number_format($performance->social_score ?? 0, 1) }}/10</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($performance->notes)
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                        <p class="text-sm text-gray-700">{{ $performance->notes }}</p>
                    </div>
                    @endif

                    @if($performance->improvement_areas)
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Axes d'amélioration</h3>
                        <p class="text-sm text-gray-700">{{ $performance->improvement_areas }}</p>
                    </div>
                    @endif

                    @if($performance->strengths_highlighted)
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Points forts</h3>
                        <p class="text-sm text-gray-700">{{ $performance->strengths_highlighted }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $performances->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg">
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune performance enregistrée</h3>
                <p class="text-gray-500">Aucune donnée de performance n'a été trouvée pour le moment.</p>
            </div>
        </div>
    @endif
    </div>
</body>
</html>
