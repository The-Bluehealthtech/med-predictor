@extends('layouts.app')

@section('title', 'Gestion des Permissions par Module - RBAC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-2xl mr-3 bg-blue-100 text-blue-600">
                                🔐
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Gestion des Permissions par Module
                                </h1>
                                <p class="text-sm text-gray-600">Configurez les accès aux modules pour chaque rôle</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.rbac.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au RBAC</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Instructions</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Utilisez les cases à cocher ci-dessous pour configurer les permissions d'accès aux modules pour chaque rôle. Les modifications seront appliquées immédiatement.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module Permissions Matrix -->
        <form action="{{ route('admin.rbac.update-module-permissions') }}" method="POST" id="permissions-form">
            @csrf
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Matrice des Permissions par Module</h2>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sauvegarder les Permissions
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table Header -->
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                    Module
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Permission
                                </th>
                                @foreach($roles as $roleKey => $roleName)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $roleName }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($modules as $moduleKey => $module)
                                @foreach($module['permissions'] as $permissionIndex => $permission)
                                <tr class="hover:bg-gray-50 {{ $permissionIndex === 0 ? 'border-t-2 border-gray-300' : '' }}">
                                    @if($permissionIndex === 0)
                                    <!-- Module Info (only on first permission row) -->
                                    <td class="px-6 py-4 whitespace-nowrap" rowspan="{{ count($module['permissions']) }}">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 shadow-sm">
                                                    {{ $module['icon'] }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900">{{ $module['name'] }}</div>
                                                <div class="text-xs text-gray-600 mt-1">{{ $module['description'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                    
                                    <!-- Permission Label -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($permission === 'view') bg-green-100 text-green-800
                                                @elseif($permission === 'create') bg-blue-100 text-blue-800
                                                @elseif($permission === 'edit') bg-yellow-100 text-yellow-800
                                                @elseif($permission === 'delete') bg-red-100 text-red-800
                                                @elseif($permission === 'export') bg-purple-100 text-purple-800
                                                @elseif($permission === 'manage') bg-indigo-100 text-indigo-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($permission) }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Role Permissions -->
                                    @foreach($roles as $roleKey => $roleName)
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <input 
                                                type="checkbox" 
                                                name="permissions[{{ $roleKey }}][{{ $permission }}]" 
                                                value="1"
                                                {{ $currentPermissions[$roleKey][$permission] ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            >
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <!-- Legend -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Roles Legend -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Légende des Rôles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($roles as $roleKey => $roleName)
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                        <span class="text-sm text-gray-700">{{ $roleName }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Permissions Legend -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Légende des Permissions</h3>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">View</span>
                        <span class="text-sm text-gray-700">Consulter et visualiser</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">Create</span>
                        <span class="text-sm text-gray-700">Créer de nouveaux éléments</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3">Edit</span>
                        <span class="text-sm text-gray-700">Modifier les éléments existants</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3">Delete</span>
                        <span class="text-sm text-gray-700">Supprimer des éléments</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-3">Export</span>
                        <span class="text-sm text-gray-700">Exporter des données</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-3">Manage</span>
                        <span class="text-sm text-gray-700">Gestion complète du module</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600">✓</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Tout Activer</h4>
                        <p class="text-sm text-gray-500">Donner accès à tous les modules</p>
                        <button type="button" onclick="selectAll()" class="mt-2 text-xs text-green-600 hover:text-green-700 font-medium">Activer tout</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600">✗</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Tout Désactiver</h4>
                        <p class="text-sm text-gray-500">Retirer l'accès à tous les modules</p>
                        <button type="button" onclick="deselectAll()" class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium">Désactiver tout</button>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600">🔄</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Réinitialiser</h4>
                        <p class="text-sm text-gray-500">Restaurer les permissions par défaut</p>
                        <button type="button" onclick="resetToDefault()" class="mt-2 text-xs text-blue-600 hover:text-blue-700 font-medium">Réinitialiser</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function resetToDefault() {
    // Réinitialiser aux permissions par défaut
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        // Logique de réinitialisation basée sur les permissions par défaut
        // Pour l'instant, on désélectionne tout
        checkbox.checked = false;
    });
}

// Auto-save functionality
let autoSaveTimeout;
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Optionnel: auto-save après 2 secondes d'inactivité
            // document.getElementById('permissions-form').submit();
        }, 2000);
    });
});
</script>
@endsection

