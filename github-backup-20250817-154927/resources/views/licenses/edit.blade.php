@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Modifier la licence</h1>
    <form method="POST" action="{{ route('licenses.update', $license) }}" class="max-w-lg space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium">Nom</label>
            <input type="text" name="name" id="name" class="border border-gray-300 rounded px-3 py-2 w-full" value="{{ old('name', $license->name) }}" required>
        </div>
        <div>
            <label for="type" class="block text-sm font-medium">Type</label>
            <select name="type" id="type" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="Joueur" @if(old('type', $license->type)=='Joueur') selected @endif>Joueur</option>
                <option value="Staff" @if(old('type', $license->type)=='Staff') selected @endif>Staff</option>
                <option value="Médical" @if(old('type', $license->type)=='Médical') selected @endif>Médical</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium">Statut</label>
            <select name="status" id="status" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="Active" @if(old('status', $license->status)=='Active') selected @endif>Active</option>
                <option value="Inactive" @if(old('status', $license->status)=='Inactive') selected @endif>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('licenses.index') }}" class="ml-4 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection 