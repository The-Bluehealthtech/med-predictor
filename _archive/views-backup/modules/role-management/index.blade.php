@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🎭 Gestion des Rôles</h1>
                    <p class="mt-2 text-gray-600">Définissez les rôles et leurs permissions prédéfinies</p>
                </div>
                <div class="flex space-x-3">
                    <a href="/user-management" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        👥 Gestion Utilisateurs
                    </a>
                </div>
            </div>
        </div>

        <!-- Rôles prédéfinis -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($predefinedRoles as $roleKey => $role)
            <div class="bg-white overflow-hidden shadow rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $role['name'] }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($roleKey == 'system_admin') bg-red-100 text-red-800
                            @elseif($roleKey == 'association_admin') bg-purple-100 text-purple-800
                            @elseif($roleKey == 'club_admin') bg-green-100 text-green-800
                            @elseif($roleKey == 'referee') bg-orange-100 text-orange-800
                            @elseif($roleKey == 'healthcare_provider') bg-blue-100 text-blue-800
                            @elseif($roleKey == 'data_analyst') bg-indigo-100 text-indigo-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $roleKey }}
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-4">{{ $role['description'] }}</p>
                    
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">
                            Permissions ({{ count($role['permissions']) }})
                        </h4>
                        <div class="space-y-1">
                            @if(count($role['permissions']) > 0)
                                @foreach(array_slice($role['permissions'], 0, 5) as $permission)
                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                    {{ $permission }}
                                </span>
                                @endforeach
                                @if(count($role['permissions']) > 5)
                                <span class="text-xs text-gray-500">
                                    +{{ count($role['permissions']) - 5 }} autres...
                                </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-500">Aucune permission spécifique</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <button onclick="viewRoleDetails('{{ $roleKey }}')" 
                                class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            👁️ Voir détails
                        </button>
                        <button onclick="applyRoleToUsers('{{ $roleKey }}')" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Statistiques des rôles -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition des Rôles</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $roleStats = [
                    'system_admin' => \App\Models\User::where('role', 'system_admin')->count(),
                    'association_admin' => \App\Models\User::where('role', 'association_admin')->count(),
                    'club_admin' => \App\Models\User::where('role', 'club_admin')->count(),
                    'referee' => \App\Models\User::where('role', 'referee')->count(),
                    'healthcare_provider' => \App\Models\User::where('role', 'healthcare_provider')->count(),
                    'data_analyst' => \App\Models\User::where('role', 'data_analyst')->count(),
                    'user' => \App\Models\User::where('role', 'user')->count(),
                ];
                @endphp
                
                @foreach($roleStats as $role => $count)
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                    <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $role)) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails du rôle -->
<div id="roleDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="roleDetailsTitle" class="text-lg font-medium text-gray-900">Détails du Rôle</h3>
                <button onclick="closeRoleDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="roleDetailsContent">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
</div>

<!-- Modal d'application du rôle -->
<div id="applyRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="applyRoleTitle" class="text-lg font-medium text-gray-900">Appliquer le Rôle</h3>
                <button onclick="closeApplyRoleModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="applyRoleContent">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
</div>

<script>
const predefinedRoles = @json($predefinedRoles);

function viewRoleDetails(roleKey) {
    const role = predefinedRoles[roleKey];
    
    document.getElementById('roleDetailsTitle').textContent = role.name;
    document.getElementById('roleDetailsContent').innerHTML = `
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
            <p class="text-sm text-gray-600">${role.description}</p>
        </div>
        
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions (${role.permissions.length})</h4>
            <div class="grid grid-cols-2 gap-2">
                ${role.permissions.map(permission => `
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        ${permission}
                    </span>
                `).join('')}
            </div>
        </div>
        
        <div class="flex justify-end">
            <button onclick="closeRoleDetailsModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                Fermer
            </button>
        </div>
    `;
    
    document.getElementById('roleDetailsModal').classList.remove('hidden');
}

function closeRoleDetailsModal() {
    document.getElementById('roleDetailsModal').classList.add('hidden');
}

function applyRoleToUsers(roleKey) {
    const role = predefinedRoles[roleKey];
    
    document.getElementById('applyRoleTitle').textContent = `Appliquer le rôle: ${role.name}`;
    document.getElementById('applyRoleContent').innerHTML = `
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-4">
                Sélectionnez les utilisateurs auxquels vous voulez appliquer ce rôle et ses permissions.
            </p>
            
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions qui seront appliquées :</h4>
                <div class="grid grid-cols-2 gap-2">
                    ${role.permissions.map(permission => `
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            ${permission}
                        </span>
                    `).join('')}
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateurs à modifier :</label>
                <select id="userSelect" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <!-- Les utilisateurs seront chargés via AJAX -->
                </select>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeApplyRoleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                Annuler
            </button>
            <button onclick="confirmApplyRole('${roleKey}')" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                Appliquer le Rôle
            </button>
        </div>
    `;
    
    // Charger les utilisateurs
    loadUsersForRoleApplication();
    
    document.getElementById('applyRoleModal').classList.remove('hidden');
}

function loadUsersForRoleApplication() {
    fetch('/api/users')
        .then(response => response.json())
        .then(users => {
            const select = document.getElementById('userSelect');
            select.innerHTML = users.map(user => `
                <option value="${user.id}">${user.name} (${user.email}) - ${user.role}</option>
            `).join('');
        })
        .catch(error => {
            console.error('Erreur lors du chargement des utilisateurs:', error);
        });
}

function closeApplyRoleModal() {
    document.getElementById('applyRoleModal').classList.add('hidden');
}

function confirmApplyRole(roleKey) {
    const selectedUsers = Array.from(document.getElementById('userSelect').selectedOptions).map(option => option.value);
    
    if (selectedUsers.length === 0) {
        alert('Veuillez sélectionner au moins un utilisateur');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir appliquer le rôle "${predefinedRoles[roleKey].name}" à ${selectedUsers.length} utilisateur(s) ?`)) {
        // Appliquer le rôle et les permissions
        applyRoleToSelectedUsers(roleKey, selectedUsers);
    }
}

function applyRoleToSelectedUsers(roleKey, userIds) {
    const role = predefinedRoles[roleKey];
    
    fetch('/api/users/apply-role', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            role: roleKey,
            permissions: role.permissions,
            user_ids: userIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rôle appliqué avec succès à ' + data.updated_count + ' utilisateur(s)');
            closeApplyRoleModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erreur: ' + error.message);
    });
}

// Fermer les modals en cliquant à l'extérieur
window.onclick = function(event) {
    const roleDetailsModal = document.getElementById('roleDetailsModal');
    const applyRoleModal = document.getElementById('applyRoleModal');
    
    if (event.target == roleDetailsModal) {
        roleDetailsModal.classList.add('hidden');
    }
    if (event.target == applyRoleModal) {
        applyRoleModal.classList.add('hidden');
    }
}
</script>
@endsection