@extends('layouts.app')

@section('title', 'Détails du Dossier Médical')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🩺 Détails du Dossier Médical</h1>
                    <p class="text-gray-600 mt-2">Informations détaillées du dossier médical</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('modules.healthcare.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ← Retour
                    </a>
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ✏️ Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Record Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du Patient</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom du Patient</label>
                            <p class="text-sm text-gray-900">Patient Example</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de Création</label>
                            <p class="text-sm text-gray-900">01/08/2024</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Statut</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation des Risques</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Score de Risque</label>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                                <span class="text-sm text-gray-600">45%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Niveau de Risque</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Modéré
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations Médicales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Antécédents Médicaux</h4>
                    <p class="text-sm text-gray-600">Aucun antécédent médical significatif noté.</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Allergies</h4>
                    <p class="text-sm text-gray-600">Aucune allergie connue.</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Médicaments Actuels</h4>
                    <p class="text-sm text-gray-600">Aucun médicament en cours.</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Conditions Spéciales</h4>
                    <p class="text-sm text-gray-600">Aucune condition spéciale.</p>
                </div>
            </div>
        </div>

        <!-- Predictions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prédictions IA</h3>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">Risque de Blessure</h4>
                            <p class="text-sm text-gray-600">Évaluation basée sur les données de performance</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Modéré
                        </span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">Recommandations</h4>
                            <p class="text-sm text-gray-600">Exercices de prévention recommandés</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Actif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 