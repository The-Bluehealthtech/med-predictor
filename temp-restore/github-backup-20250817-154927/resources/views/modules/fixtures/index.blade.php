@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">📅 Fixtures</h1>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-blue-800">
                🚧 Cette section est en cours de développement.
            </p>
            <p class="text-blue-600 text-sm mt-2">
                Les fixtures et calendriers des matchs seront bientôt disponibles.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Placeholder pour les fixtures -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-2">Match 1</h3>
                <p class="text-gray-600 text-sm">À venir</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-2">Match 2</h3>
                <p class="text-gray-600 text-sm">À venir</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-2">Match 3</h3>
                <p class="text-gray-600 text-sm">À venir</p>
            </div>
        </div>
        
        <div class="mt-6">
            <a href="{{ route('competitions.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                ← Retour aux compétitions
            </a>
        </div>
    </div>
</div>
@endsection
