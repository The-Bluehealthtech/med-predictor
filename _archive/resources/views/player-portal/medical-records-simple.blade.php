<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossiers Médicaux - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dossiers Médicaux</h1>
        <p class="text-gray-600">Historique complet de vos examens médicaux et évaluations</p>
    </div>

    <!-- Dossiers de Santé -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Dossiers de Santé</h2>
            </div>
            <div class="p-6">
                @if(Auth::user() && Auth::user()->player && Auth::user()->player->healthRecords->count() > 0)
                    <div class="space-y-4">
                        @foreach(Auth::user()->player->healthRecords as $record)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $record->visit_type ?? 'Examen médical' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $record->record_date ? $record->record_date->format('d/m/Y') : 'Date non spécifiée' }}
                                    </p>
                                    @if($record->diagnosis)
                                        <p class="text-sm text-gray-700 mt-2">
                                            <strong>Diagnostic:</strong> {{ $record->diagnosis }}
                                        </p>
                                    @endif
                                    @if($record->notes)
                                        <p class="text-sm text-gray-700 mt-2">
                                            <strong>Notes:</strong> {{ $record->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($record->risk_score)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $record->risk_score > 0.7 ? 'bg-red-100 text-red-800' : 
                                               ($record->risk_score > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            Risque: {{ number_format($record->risk_score * 100, 0) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier de santé</h3>
                        <p class="text-gray-500">Aucun dossier médical n'a été trouvé pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PCMA -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">PCMA (Pre-Competition Medical Assessment)</h2>
            </div>
            <div class="p-6">
                @if(Auth::user() && Auth::user()->player && Auth::user()->player->pcmas->count() > 0)
                    <div class="space-y-4">
                        @foreach(Auth::user()->player->pcmas as $pcma)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        PCMA - {{ $pcma->type ?? 'Évaluation pré-compétition' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $pcma->assessment_date ? $pcma->assessment_date->format('d/m/Y') : 'Date non spécifiée' }}
                                    </p>
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Statut:</strong> 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($pcma->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($pcma->status ?? 'Inconnu') }}
                                        </span>
                                    </p>
                                    @if($pcma->notes)
                                        <p class="text-sm text-gray-700 mt-2">
                                            <strong>Notes:</strong> {{ $pcma->notes }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($pcma->fifa_compliant)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            FIFA Compliant
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune PCMA</h3>
                        <p class="text-gray-500">Aucune évaluation PCMA n'a été trouvée pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>


















