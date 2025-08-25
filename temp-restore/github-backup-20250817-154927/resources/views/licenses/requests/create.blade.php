@extends('layouts.app')
@section('title', 'Nouvelle demande de licence')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Nouvelle demande de licence</h1>
    <form action="{{ route('license-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="type" class="block font-semibold">Type de licence</label>
            <select name="type" id="type" class="border rounded w-full p-2" required>
                <option value="">-- Sélectionner --</option>
                <option value="joueur">Joueur</option>
                <option value="staff">Staff</option>
            </select>
        </div>
        <div>
            <label for="player_id" class="block font-semibold">Joueur (optionnel)</label>
            <input type="number" name="player_id" id="player_id" class="border rounded w-full p-2">
        </div>
        <div>
            <label for="staff_id" class="block font-semibold">Staff (optionnel)</label>
            <input type="number" name="staff_id" id="staff_id" class="border rounded w-full p-2">
        </div>
        <div>
            <label for="attachments" class="block font-semibold">Pièces jointes</label>
            <input type="file" name="attachments[]" id="attachments" class="border rounded w-full p-2" multiple required accept=".pdf,.jpg,.jpeg,.png">
            <small>Formats acceptés : PDF, JPG, PNG. Taille max : 4 Mo par fichier.</small>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Envoyer</button>
    </form>
</div>
@endsection 