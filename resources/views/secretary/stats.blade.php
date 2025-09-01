@extends('layouts.secretary')

@section('title', 'Statistiques')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Statistiques Médicales</h1>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900">Rendez-vous</h3>
            <p class="text-3xl font-bold text-blue-600">25</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
            <p class="text-3xl font-bold text-green-600">156</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900">Athlètes</h3>
            <p class="text-3xl font-bold text-purple-600">89</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900">Visites</h3>
            <p class="text-3xl font-bold text-orange-600">342</p>
        </div>
    </div>
</div>
@endsection
