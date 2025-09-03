@extends('layouts.app')

@section('title', 'Engagements des Clubs - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                Engagements des Clubs
            </h1>
            <p class="text-gray-600 mt-2">Validation des inscriptions aux compétitions</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Clubs Inscrits</h2>
        <div class="text-center py-8">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun engagement en attente</h3>
            <p class="text-gray-500">Les engagements des clubs apparaîtront ici.</p>
        </div>
    </div>
</div>
@endsection

