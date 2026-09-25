@extends('layouts.app')

@section('title', 'Fixtures - Calendrier des Matchs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                Fixtures
            </h1>
            <p class="text-gray-600 mt-2">Calendrier des matchs et rencontres</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
            </a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-download mr-2"></i>Exporter
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les compétitions</option>
                    <option value="championnat-u19">Championnat Régional U19</option>
                    <option value="coupe-regionale">Coupe Régionale</option>
                    <option value="championnat-senior">Championnat Senior</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="semaine">Cette semaine</option>
                    <option value="mois">Ce mois</option>
                    <option value="trimestre">Ce trimestre</option>
                    <option value="saison">Toute la saison</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="programme">Programmé</option>
                    <option value="en-cours">En cours</option>
                    <option value="termine">Terminé</option>
                    <option value="reporte">Reporté</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Matchs Programmés</p>
                    <p class="text-2xl font-bold text-gray-900">24</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Matchs Terminés</p>
                    <p class="text-2xl font-bold text-gray-900">18</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-2xl font-bold text-gray-900">2</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Reportés</p>
                    <p class="text-2xl font-bold text-gray-900">4</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier des Fixtures -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Calendrier des Fixtures</h2>
        </div>
        
        <div class="p-6">
            <!-- Vue par Semaine -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Semaine du 15-21 Septembre 2024</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-chevron-left mr-1"></i>Précédent
                        </button>
                        <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Suivant<i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Matchs de la semaine -->
                <div class="space-y-4">
                    <!-- Match 1 -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">15 Sep</div>
                                    <div class="text-xs text-gray-500">15:00</div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900">FC Ville</div>
                                        <div class="text-sm text-gray-500">Domicile</div>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-400">vs</div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-900">Notre Club</div>
                                        <div class="text-sm text-gray-500">Extérieur</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">Championnat U19</div>
                                    <div class="text-xs text-gray-500">Stade Municipal</div>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Programmé
                                </span>
                                <button class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Match 2 -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">17 Sep</div>
                                    <div class="text-xs text-gray-500">17:00</div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900">AS Sport</div>
                                        <div class="text-sm text-gray-500">Domicile</div>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-400">vs</div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-900">FC Athletic</div>
                                        <div class="text-sm text-gray-500">Extérieur</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">Coupe Régionale</div>
                                    <div class="text-xs text-gray-500">Stade des Sports</div>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Reporté
                                </span>
                                <button class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Match 3 -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">19 Sep</div>
                                    <div class="text-xs text-gray-500">20:00</div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900">Équipe A</div>
                                        <div class="text-sm text-gray-500">Domicile</div>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-400">vs</div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-900">Équipe B</div>
                                        <div class="text-sm text-gray-500">Extérieur</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">Championnat Senior</div>
                                    <div class="text-xs text-gray-500">Stade Principal</div>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Terminé
                                </span>
                                <button class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue par Jour -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vue par Jour</h3>
                <div class="grid grid-cols-7 gap-2">
                    <!-- Lundi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Lun</div>
                        <div class="text-xs text-gray-500 mb-1">15</div>
                        <div class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            2
                        </div>
                    </div>
                    <!-- Mardi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Mar</div>
                        <div class="text-xs text-gray-500 mb-1">16</div>
                        <div class="w-8 h-8 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            0
                        </div>
                    </div>
                    <!-- Mercredi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Mer</div>
                        <div class="text-xs text-gray-500 mb-1">17</div>
                        <div class="w-8 h-8 bg-yellow-100 text-yellow-800 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            1
                        </div>
                    </div>
                    <!-- Jeudi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Jeu</div>
                        <div class="text-xs text-gray-500 mb-1">18</div>
                        <div class="w-8 h-8 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            0
                        </div>
                    </div>
                    <!-- Vendredi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Ven</div>
                        <div class="text-xs text-gray-500 mb-1">19</div>
                        <div class="w-8 h-8 bg-green-100 text-green-800 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            1
                        </div>
                    </div>
                    <!-- Samedi -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Sam</div>
                        <div class="text-xs text-gray-500 mb-1">20</div>
                        <div class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            3
                        </div>
                    </div>
                    <!-- Dimanche -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-900 mb-2">Dim</div>
                        <div class="text-xs text-gray-500 mb-1">21</div>
                        <div class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-xs font-medium mx-auto">
                            2
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Légende -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Légende</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-100 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Matchs programmés</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-100 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Matchs terminés</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-100 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Matchs reportés</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-100 rounded-full mr-3"></div>
                <span class="text-sm text-gray-600">Aucun match</span>
            </div>
        </div>
    </div>
</div>
@endsection
