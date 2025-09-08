@extends('layouts.secretary')

@section('title', 'Documents')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Documents</h1>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-upload mr-2"></i>Nouveau Document
        </button>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Liste des documents médicaux...</p>
    </div>
</div>
@endsection
