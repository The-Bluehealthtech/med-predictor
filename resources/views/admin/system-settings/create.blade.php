@extends('layouts.app')

@section('title', 'Nouveau Paramètre - Administration')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">➕</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Nouveau Paramètre
                                </h1>
                                <p class="text-sm text-gray-600">Créer un nouveau paramètre système</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.system-settings.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Paramètres</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.system-settings.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Informations de base -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations de base</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                                Clé du paramètre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="key" id="key" value="{{ old('key') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="ex: new_feature_enabled" required>
                            <p class="mt-1 text-sm text-gray-500">Identifiant unique du paramètre (en anglais, sans espaces)</p>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom du paramètre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="ex: Nouvelle fonctionnalité activée" required>
                            <p class="mt-1 text-sm text-gray-500">Nom affiché dans l'interface</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Description détaillée du paramètre">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Configuration du paramètre -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Configuration</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                                Groupe <span class="text-red-500">*</span>
                            </label>
                            <select name="group" id="group" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groups as $groupKey => $groupDescription)
                                    <option value="{{ $groupKey }}" {{ old('group') === $groupKey ? 'selected' : '' }}>
                                        {{ ucfirst($groupKey) }} - {{ $groupDescription }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Sélectionner un type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                            Valeur <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="value" id="value" value="{{ old('value') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Valeur du paramètre" required>
                    </div>

                    <div class="mt-6">
                        <label for="default_value" class="block text-sm font-medium text-gray-700 mb-2">
                            Valeur par défaut
                        </label>
                        <input type="text" name="default_value" id="default_value" value="{{ old('default_value') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Valeur par défaut (optionnel)">
                    </div>

                    <div class="mt-6">
                        <label for="validation_rules" class="block text-sm font-medium text-gray-700 mb-2">
                            Règles de validation
                        </label>
                        <input type="text" name="validation_rules" id="validation_rules" value="{{ old('validation_rules') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="ex: min:1|max:100">
                        <p class="mt-1 text-sm text-gray-500">Règles Laravel (ex: min:1|max:100|required)</p>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Permissions</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_public" id="is_public" value="1" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                   {{ old('is_public') ? 'checked' : '' }}>
                            <label for="is_public" class="ml-2 block text-sm text-gray-900">
                                Paramètre public
                            </label>
                            <p class="ml-2 text-sm text-gray-500">Peut être consulté sans authentification</p>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_editable" id="is_editable" value="1" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                   {{ old('is_editable', true) ? 'checked' : '' }}>
                            <label for="is_editable" class="ml-2 block text-sm text-gray-900">
                                Modifiable
                            </label>
                            <p class="ml-2 text-sm text-gray-500">Peut être modifié par les administrateurs</p>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_required" id="is_required" value="1" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                   {{ old('is_required') ? 'checked' : '' }}>
                            <label for="is_required" class="ml-2 block text-sm text-gray-900">
                                Paramètre requis
                            </label>
                            <p class="ml-2 text-sm text-gray-500">Doit avoir une valeur pour le fonctionnement du système</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.system-settings.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                    Créer le Paramètre
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill default value when type changes
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const valueInput = document.getElementById('value');
    const defaultInput = document.getElementById('default_value');
    
    if (!valueInput.value) {
        switch(type) {
            case 'boolean':
                valueInput.value = '0';
                defaultInput.value = '0';
                break;
            case 'integer':
                valueInput.value = '0';
                defaultInput.value = '0';
                break;
            case 'string':
                valueInput.value = '';
                defaultInput.value = '';
                break;
            case 'text':
                valueInput.value = '';
                defaultInput.value = '';
                break;
            case 'json':
                valueInput.value = '{}';
                defaultInput.value = '{}';
                break;
        }
    }
});
</script>
@endsection

