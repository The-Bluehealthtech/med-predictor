@extends('layouts.app')

@section('title', 'Détails du Dossier Médical')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('🩺 Détails du Dossier Médical') }}</h1>
                    <p class="text-gray-600 mt-2">{{ __('Informations détaillées du dossier médical') }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('modules.healthcare.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('errors.generic_back') }}
                    </a>
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('pcma_extra.label_723bbbfede8a') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Record Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('health_records.show_page.patient_info_heading') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Nom du Patient') }}</label>
                            <p class="text-sm text-gray-900">Patient Example</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Date de Création') }}</label>
                            <p class="text-sm text-gray-900">01/08/2024</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('clinical.table_status') }}</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('healthcare.status_active') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('healthcare.risk_assessment') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.risk_score_heading') }}</label>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                                <span class="text-sm text-gray-600">45%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('medical_predictions.show_risk_level_label') }}</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('Modéré') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('health_records.show_page.medical_info_heading') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">{{ __('health_records_edit.medical_history') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('Aucun antécédent médical significatif noté.') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Allergies</h4>
                    <p class="text-sm text-gray-600">{{ __('Aucune allergie connue.') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">{{ __('health_records_edit.current_medications_2') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('Aucun médicament en cours.') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">{{ __('Conditions Spéciales') }}</h4>
                    <p class="text-sm text-gray-600">{{ __('Aucune condition spéciale.') }}</p>
                </div>
            </div>
        </div>

        <!-- Predictions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Prédictions IA') }}</h3>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ __('medical_predictions.show_type_injury_risk') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Évaluation basée sur les données de performance') }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('Modéré') }}</span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ __('medical_predictions.shared_recommendations_label') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Exercices de prévention recommandés') }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ __('healthcare.status_active') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 