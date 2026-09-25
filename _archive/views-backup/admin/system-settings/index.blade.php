@extends('layouts.app')

@section('title', 'Paramètres Système - Administration')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-gray-600 to-slate-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚙️</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Paramètres Système
                                </h1>
                                <p class="text-sm text-gray-600">Configurer les paramètres et constantes du système</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.administration.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à l'Administration</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_settings'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✏️</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Modifiables</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-blue-600">{{ $stats['editable_settings'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⚠️</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Requis</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-red-600">{{ $stats['required_settings'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🌐</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Publics</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-green-600">{{ $stats['public_settings'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📁</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Groupes</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-purple-600">{{ $stats['groups_count'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.system-settings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ➕ Nouveau Paramètre
                    </a>
                    <form action="{{ route('admin.system-settings.initialize') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            ⚡ Initialiser les Paramètres
                        </button>
                    </form>
                    <a href="{{ route('admin.system-settings.export', ['format' => 'csv', 'group' => $group]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        📤 Exporter CSV
                    </a>
                    <a href="{{ route('admin.system-settings.export', ['format' => 'json', 'group' => $group]) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                        📄 Exporter JSON
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation par groupes -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Groupes de Paramètres</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($groups as $groupName)
                        @php
                            $groupIcons = App\Models\SystemSetting::getGroupIcons();
                            $groupDescriptions = App\Models\SystemSetting::getGroupDescriptions();
                            $icon = $groupIcons[$groupName] ?? '⚙️';
                            $description = $groupDescriptions[$groupName] ?? 'Paramètres ' . $groupName;
                            $count = App\Models\SystemSetting::byGroup($groupName)->count();
                        @endphp
                        <a href="{{ route('admin.system-settings.index', ['group' => $groupName]) }}" 
                           class="block p-4 rounded-lg border-2 transition-all {{ $group === $groupName ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                            <div class="flex items-center mb-2">
                                <span class="text-2xl mr-3">{{ $icon }}</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ ucfirst($groupName) }}</h4>
                                    <p class="text-sm text-gray-500">{{ $count }} paramètre{{ $count > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600">{{ $description }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Liste des paramètres -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    @php
                        $groupIcons = App\Models\SystemSetting::getGroupIcons();
                        $icon = $groupIcons[$group] ?? '⚙️';
                    @endphp
                    <span class="text-2xl mr-2">{{ $icon }}</span>
                    Paramètres - {{ ucfirst($group) }}
                </h3>
                
                @if($settings->count() > 0)
                    <form action="{{ route('admin.system-settings.update-bulk') }}" method="POST">
                        @csrf
                        <input type="hidden" name="group" value="{{ $group }}">
                        
                        <div class="space-y-6">
                            @foreach($settings as $setting)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $setting->name }}</h4>
                                            @if($setting->is_required)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Requis
                                                </span>
                                            @endif
                                            @if(!$setting->is_editable)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Lecture seule
                                                </span>
                                            @endif
                                            @if($setting->is_public)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Public
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-3">{{ $setting->description }}</p>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><strong>Clé:</strong> <code class="bg-gray-100 px-2 py-1 rounded">{{ $setting->key }}</code></span>
                                            <span><strong>Type:</strong> {{ $setting->type }}</span>
                                            @if($setting->default_value)
                                                <span><strong>Défaut:</strong> {{ $setting->default_value }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="flex space-x-2">
                                            @if($setting->is_editable)
                                                <a href="{{ route('admin.system-settings.edit', $setting->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                    Modifier
                                                </a>
                                                @if($setting->default_value)
                                                    <form action="{{ route('admin.system-settings.reset', $setting->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 text-sm">
                                                            Réinitialiser
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            <a href="{{ route('admin.system-settings.show', $setting->id) }}" class="text-gray-600 hover:text-gray-900 text-sm">
                                                Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($setting->is_editable)
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Valeur actuelle</label>
                                        @if($setting->type === 'boolean')
                                            <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Oui</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Non</option>
                                            </select>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @elseif($setting->type === 'text')
                                            <textarea name="settings[{{ $setting->key }}]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Valeur actuelle</label>
                                        <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-900">
                                            {{ $setting->value }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        
                        @if($settings->where('is_editable', true)->count() > 0)
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    💾 Sauvegarder les Modifications
                                </button>
                            </div>
                        @endif
                    </form>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Aucun paramètre trouvé pour le groupe "{{ $group }}".</p>
                        <form action="{{ route('admin.system-settings.initialize') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                ⚡ Initialiser les Paramètres
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
