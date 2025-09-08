@extends('layouts.app')

@section('title', 'Gestion des Budgets')

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
                                <span class="text-white font-bold text-lg">💰</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Gestion des Budgets
                                </h1>
                                <p class="text-sm text-gray-600">Planification et suivi des budgets {{ ucfirst($userType) }}</p>
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
        <!-- Budget Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Budget Annuel</h3>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-sm">📅</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-blue-600">500,000 €</p>
                    <p class="text-sm text-gray-500 mt-2">Budget alloué pour 2024</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Budget Utilisé</h3>
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600 text-sm">💸</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-600">295,000 €</p>
                    <p class="text-sm text-gray-500 mt-2">59% du budget utilisé</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Budget Restant</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">✅</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">205,000 €</p>
                    <p class="text-sm text-gray-500 mt-2">41% du budget restant</p>
                </div>
            </div>
        </div>

        <!-- Budget Categories -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Catégories de Budget</h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-blue-600">👥</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Salaires Joueurs</p>
                                <p class="text-sm text-gray-500">Budget: 200,000 € | Utilisé: 120,000 €</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-blue-600">60%</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-green-600">🏟️</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Maintenance Installations</p>
                                <p class="text-sm text-gray-500">Budget: 50,000 € | Utilisé: 25,000 €</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-green-600">50%</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-yellow-600">✈️</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Voyages & Déplacements</p>
                                <p class="text-sm text-gray-500">Budget: 30,000 € | Utilisé: 15,000 €</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-yellow-600">50%</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-purple-600">⚽</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Équipements Sportifs</p>
                                <p class="text-sm text-gray-500">Budget: 20,000 € | Utilisé: 10,000 €</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-purple-600">50%</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Planning -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Planification Budgetaire</h3>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Nouveau Budget
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-blue-600 text-xl">Q1</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Q1 2024</h4>
                            <p class="text-sm text-gray-600 mb-3">Jan - Mar</p>
                            <p class="text-lg font-bold text-blue-600">125,000 €</p>
                            <p class="text-xs text-gray-500">75,000 € utilisé</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-green-600 text-xl">Q2</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Q2 2024</h4>
                            <p class="text-sm text-gray-600 mb-3">Avr - Jun</p>
                            <p class="text-lg font-bold text-green-600">125,000 €</p>
                            <p class="text-xs text-gray-500">80,000 € utilisé</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-yellow-600 text-xl">Q3</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Q3 2024</h4>
                            <p class="text-sm text-gray-600 mb-3">Jul - Sep</p>
                            <p class="text-lg font-bold text-yellow-600">125,000 €</p>
                            <p class="text-xs text-gray-500">70,000 € utilisé</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <span class="text-purple-600 text-xl">Q4</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Q4 2024</h4>
                            <p class="text-sm text-gray-600 mb-3">Oct - Dec</p>
                            <p class="text-lg font-bold text-purple-600">125,000 €</p>
                            <p class="text-xs text-gray-500">70,000 € utilisé</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
