@extends('layouts.app')

@section('title', 'Demande de Licence Joueur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-6">
            @if($player->player_picture || $player->player_face_url)
                <img src="{{ asset($player->player_picture ?? $player->player_face_url) }}" alt="Player Picture" class="w-24 h-24 rounded-full object-cover border-2 border-blue-700 mr-4">
            @else
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl font-bold text-blue-700 mr-4">
                    {{ strtoupper(substr($player->first_name ?? $player->full_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-900">⚽ Demande de Licence pour {{ $player->full_name ?? ($player->first_name . ' ' . $player->last_name) }}</h1>
                <p class="text-gray-600 mt-2">Mettre à jour les informations du joueur et soumettre une demande de licence à l'association</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Joueur</h2>
            </div>
            <form action="{{ route('player-licenses.request.store', $player) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Photo du Joueur</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200">
                                @if($player->player_picture)
                                    <img src="{{ asset('storage/' . $player->player_picture) }}" class="h-24 w-24 rounded-full object-cover">
                                @else
                                    <span class="text-gray-500 font-bold text-2xl">?</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="player_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                Modifier la photo
                            </label>
                            <input type="file" name="player_picture" id="player_picture" accept="image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPEG, PNG, JPG, GIF. Taille max: 2MB</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $player->first_name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $player->last_name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance *</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $player->date_of_birth ? $player->date_of_birth->format('Y-m-d') : '') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationalité *</label>
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $player->nationality) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $player->position) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Taille (cm)</label>
                        <input type="number" name="height" id="height" value="{{ old('height', $player->height) }}" min="150" max="220" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Poids (kg)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $player->weight) }}" min="40" max="120" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="overall_rating" class="block text-sm font-medium text-gray-700 mb-2">Note globale</label>
                        <input type="number" name="overall_rating" id="overall_rating" value="{{ old('overall_rating', $player->overall_rating) }}" min="1" max="99" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="potential_rating" class="block text-sm font-medium text-gray-700 mb-2">Note potentielle</label>
                        <input type="number" name="potential_rating" id="potential_rating" value="{{ old('potential_rating', $player->potential_rating) }}" min="1" max="99" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $player->email) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $player->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <!-- Supporting Documents Upload Section -->
                <div class="mb-8 mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Documents justificatifs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="id_document" class="block text-sm font-medium text-gray-700 mb-2">Pièce d'identité (PDF, JPG, PNG)</label>
                            <input type="file" name="id_document" id="id_document" accept=".pdf,image/*" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="medical_certificate" class="block text-sm font-medium text-gray-700 mb-2">Certificat médical (PDF, JPG, PNG)</label>
                            <input type="file" name="medical_certificate" id="medical_certificate" accept=".pdf,image/*" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="proof_of_age" class="block text-sm font-medium text-gray-700 mb-2">Justificatif d'âge (PDF, JPG, PNG)</label>
                            <input type="file" name="proof_of_age" id="proof_of_age" accept=".pdf,image/*" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <!-- License Info (editable) -->
                <div class="mb-4">
                    <label for="license_type" class="block text-gray-700 font-semibold mb-1">Type de licence</label>
                    <input type="text" name="license_type" id="license_type" class="w-full border rounded px-3 py-2" value="{{ old('license_type', $license->license_type ?? 'standard') }}">
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-gray-700 font-semibold mb-1">Statut</label>
                    <input type="text" name="status" id="status" class="w-full border rounded px-3 py-2" value="{{ old('status', $license->status ?? 'pending') }}">
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-gray-700 font-semibold mb-1">Notes supplémentaires</label>
                    <textarea name="notes" id="notes" class="w-full border rounded px-3 py-2" rows="3">{{ old('notes', $license->notes ?? '') }}</textarea>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('players.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">Annuler</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">Soumettre la demande de licence</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 