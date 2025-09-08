@extends('layouts.secretary')

@section('title', 'Recherche Athlètes')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Recherche d'Athlètes</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <input type="text" placeholder="Rechercher un athlète..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <p class="text-gray-600">Résultats de recherche...</p>
    </div>
</div>
@endsection
