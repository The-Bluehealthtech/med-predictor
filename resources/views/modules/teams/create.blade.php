@extends('layouts.app')

@section('title', 'Créer une Équipe')

@section('content')
<div class="container-fluid">
    <!-- Header avec bouton retour -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-plus text-green-600 mr-3"></i>
                Créer une Équipe
            </h1>
            <p class="text-gray-600 mt-2">Ajouter une nouvelle équipe selon les standards FIFA Connect</p>
        </div>
        <a href="{{ route('modules.teams.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire de création -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('modules.teams.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de l'équipe -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'équipe *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Équipe Première, Réserve, U-17..."
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Club -->
                <div>
                    <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Club *
                    </label>
                    <select id="club_id" 
                            name="club_id" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('club_id') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un club</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }} ({{ $club->association->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('club_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Niveau -->
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                        Niveau *
                    </label>
                    <select id="level" 
                            name="level" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('level') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un niveau</option>
                        <option value="professional" {{ old('level') == 'professional' ? 'selected' : '' }}>
                            Professionnel
                        </option>
                        <option value="semi-professional" {{ old('level') == 'semi-professional' ? 'selected' : '' }}>
                            Semi-Professionnel
                        </option>
                        <option value="amateur" {{ old('level') == 'amateur' ? 'selected' : '' }}>
                            Amateur
                        </option>
                        <option value="youth" {{ old('level') == 'youth' ? 'selected' : '' }}>
                            Jeunes
                        </option>
                        <option value="academy" {{ old('level') == 'academy' ? 'selected' : '' }}>
                            Académie
                        </option>
                    </select>
                    @error('level')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catégorie d'âge -->
                <div>
                    <label for="age_category" class="block text-sm font-medium text-gray-700 mb-2">
                        Catégorie d'âge *
                    </label>
                    <select id="age_category" 
                            name="age_category" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age_category') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner une catégorie</option>
                        <option value="U-12" {{ old('age_category') == 'U-12' ? 'selected' : '' }}>U-12</option>
                        <option value="U-14" {{ old('age_category') == 'U-14' ? 'selected' : '' }}>U-14</option>
                        <option value="U-16" {{ old('age_category') == 'U-16' ? 'selected' : '' }}>U-16</option>
                        <option value="U-17" {{ old('age_category') == 'U-17' ? 'selected' : '' }}>U-17</option>
                        <option value="U-18" {{ old('age_category') == 'U-18' ? 'selected' : '' }}>U-18</option>
                        <option value="U-19" {{ old('age_category') == 'U-19' ? 'selected' : '' }}>U-19</option>
                        <option value="U-20" {{ old('age_category') == 'U-20' ? 'selected' : '' }}>U-20</option>
                        <option value="U-21" {{ old('age_category') == 'U-21' ? 'selected' : '' }}>U-21</option>
                        <option value="U-23" {{ old('age_category') == 'U-23' ? 'selected' : '' }}>U-23</option>
                        <option value="Senior" {{ old('age_category') == 'Senior' ? 'selected' : '' }}>Senior</option>
                    </select>
                    @error('age_category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discipline -->
                <div>
                    <label for="discipline" class="block text-sm font-medium text-gray-700 mb-2">
                        Discipline *
                    </label>
                    <select id="discipline" 
                            name="discipline" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('discipline') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner une discipline</option>
                        <option value="football" {{ old('discipline') == 'football' ? 'selected' : '' }}>
                            Football
                        </option>
                        <option value="futsal" {{ old('discipline') == 'futsal' ? 'selected' : '' }}>
                            Futsal
                        </option>
                        <option value="beach_soccer" {{ old('discipline') == 'beach_soccer' ? 'selected' : '' }}>
                            Beach Soccer
                        </option>
                        <option value="women_football" {{ old('discipline') == 'women_football' ? 'selected' : '' }}>
                            Football Féminin
                        </option>
                    </select>
                    @error('discipline')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut *
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un statut</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                            Actif
                        </option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                            Inactif
                        </option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                            En attente
                        </option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('modules.teams.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Créer l'équipe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

