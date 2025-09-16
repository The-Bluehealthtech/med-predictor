@extends('layouts.app')

@section('title', 'Gestion des Permissions par Module')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🔐</span>
                            </div>
                            <div class="ml-3">
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
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-blue-900 mb-2">Instructions</h3>
            <p class="text-blue-800">
                Utilisez les cases à cocher ci-dessous pour configurer les permissions d'accès aux modules pour chaque rôle. 
                Les modifications seront appliquées immédiatement.
            </p>
        </div>

        <!-- Permissions Matrix -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Matrice des Permissions par Module</h3>
                    <button onclick="savePermissions()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        💾 Sauvegarder les Permissions
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                                @foreach($roles as $roleKey => $roleName)
                                <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $roleName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($modules as $moduleKey => $module)
                                @foreach($module['permissions'] as $permission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($loop->first)
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-3">{{ $module['icon'] }}</span>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $module['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $module['description'] }}</div>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst(str_replace('-', ' ', $permission)) }}
                                    </td>
                                    @foreach($roles as $roleKey => $roleName)
                                    <td class="px-2 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" 
                                               class="permission-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                               data-module="{{ $moduleKey }}"
                                               data-permission="{{ $permission }}"
                                               data-role="{{ $roleKey }}"
                                               {{ (isset($currentPermissions[$roleKey][$permission]) && $currentPermissions[$roleKey][$permission] === true) ? 'checked' : '' }}
                                               onchange="updatePermission('{{ $moduleKey }}', '{{ $permission }}', '{{ $roleKey }}', this.checked)">
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Roles Legend -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Légende des Rôles</h3>
                <div class="space-y-2">
                    @foreach($roles as $roleKey => $roleName)
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-700">{{ $roleName }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Permissions Legend -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Légende des Permissions</h3>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">View</span>
                        <span class="text-xs text-gray-500">Consulter et visualiser</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Create</span>
                        <span class="text-xs text-gray-500">Créer de nouveaux éléments</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Edit</span>
                        <span class="text-xs text-gray-500">Modifier les éléments existants</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Delete</span>
                        <span class="text-xs text-gray-500">Supprimer des éléments</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Export</span>
                        <span class="text-xs text-gray-500">Exporter des données</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">Manage</span>
                        <span class="text-xs text-gray-500">Gestion complète du module</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions en Masse</h3>
            <div class="flex space-x-4">
                <button onclick="activateAll()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    ✓ Tout Activer
                </button>
                <button onclick="deactivateAll()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                    ✗ Tout Désactiver
                </button>
                <button onclick="resetPermissions()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                    🔄 Réinitialiser
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function updatePermission(module, permission, role, isChecked) {
    // Update the permission in the currentPermissions object
    if (!window.currentPermissions) {
        window.currentPermissions = @json($currentPermissions);
    }
    
    if (!window.currentPermissions[role]) {
        window.currentPermissions[role] = {};
    }
    
    window.currentPermissions[role][permission] = isChecked;
    
    console.log('Permission updated:', { module, permission, role, isChecked });
}

function savePermissions() {
    if (!window.currentPermissions) {
        alert('Aucune modification à sauvegarder');
        return;
    }
    
    // Send AJAX request to save permissions
    fetch('/public-module-permissions/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            permissions: window.currentPermissions
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Permissions sauvegardées avec succès!');
        } else {
            alert('Erreur lors de la sauvegarde: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la sauvegarde des permissions');
    });
}

function activateAll() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const module = checkbox.dataset.module;
        const permission = checkbox.dataset.permission;
        const role = checkbox.dataset.role;
        updatePermission(module, permission, role, true);
    });
    showSuccess('Toutes les permissions ont été activées');
}

function deactivateAll() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        const module = checkbox.dataset.module;
        const permission = checkbox.dataset.permission;
        const role = checkbox.dataset.role;
        updatePermission(module, permission, role, false);
    });
    showSuccess('Toutes les permissions ont été désactivées');
}

function resetPermissions() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les permissions?')) {
        // Reset to default permissions
        location.reload();
    }
}

function showSuccess(message) {
    // Create a temporary success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 3000);
}
</script>
@endsection