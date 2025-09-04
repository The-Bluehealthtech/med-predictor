@extends('layouts.app')

@section('title', 'Résultats & Classements - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-trophy text-yellow-600 mr-3"></i>
                Résultats & Classements
            </h1>
            <p class="text-gray-600 mt-2">Compilation automatique des résultats</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Classements</h2>
        <div class="text-center py-8">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun classement disponible</h3>
            <p class="text-gray-500">Les classements apparaîtront ici une fois les matchs joués.</p>
        </div>
    </div>
</div>
@endsection

