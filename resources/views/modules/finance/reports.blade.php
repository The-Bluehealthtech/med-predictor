@extends('layouts.app')

@section('title', 'Rapports Financiers')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📊</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Rapports Financiers
                                </h1>
                                <p class="text-sm text-gray-600">Analyse détaillée des finances {{ ucfirst($userType) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.finance.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Reports Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé Financier</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Revenus Totaux</span>
                        <span class="text-lg font-bold text-green-600">295,000 €</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Dépenses Totales</span>
                        <span class="text-lg font-bold text-red-600">180,000 €</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Bénéfice Net</span>
                        <span class="text-lg font-bold text-blue-600">115,000 €</span>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Métriques de Performance</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Marge Bénéficiaire</span>
                        <span class="text-lg font-bold text-purple-600">39.0%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">ROI</span>
                        <span class="text-lg font-bold text-yellow-600">23.0%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Croissance</span>
                        <span class="text-lg font-bold text-indigo-600">+12.5%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Rapports Détaillés</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-green-600">📈</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Mensuel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Analyse des revenus et dépenses du mois</p>
                        <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                            Générer PDF
                        </button>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-blue-600">📊</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Trimestriel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Bilan financier du trimestre</p>
                        <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Générer PDF
                        </button>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-purple-600">📋</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Annuel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Bilan complet de l'année</p>
                        <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            Générer PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
