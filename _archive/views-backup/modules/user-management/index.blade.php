@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">👥 Gestion des Utilisateurs</h1>
                    <p class="mt-2 text-gray-600">Gérez les utilisateurs, leurs rôles et permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('user-management.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        + Créer Utilisateur
                    </a>
                    <a href="/admin/account-requests" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        📋 Demandes ({{ $accountRequests->count() }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ $users->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Utilisateurs</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ $users->where('role', 'admin')->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Administrateurs</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ $users->where('role', 'referee')->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Arbitres</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ $accountRequests->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Demandes en Attente</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Utilisateurs</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Liste de tous les utilisateurs du système</p>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($users as $user)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->role == 'admin') bg-red-100 text-red-800
                                        @elseif($user->role == 'referee') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->status == 'active') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('user-management.edit', $user) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <form method="POST" action="{{ route('user-management.destroy', $user) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Permissions -->
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions :</h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                            @foreach($availablePermissions as $permission => $label)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       class="permission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       data-user-id="{{ $user->id }}"
                                       data-permission="{{ $permission }}"
                                       @if($user->permissions && in_array($permission, is_array($user->permissions) ? $user->permissions : json_decode($user->permissions, true) ?? []))
                                       checked
                                       @endif
                                       onchange="updatePermission({{ $user->id }}, '{{ $permission }}', this.checked)">
                                <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
function updatePermission(userId, permission, isChecked) {
    // Envoyer la requête AJAX pour mettre à jour la permission
    fetch('/api/users/' + userId + '/permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            permission: permission,
            is_granted: isChecked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Permission mise à jour:', permission, isChecked);
        } else {
            alert('Erreur lors de la mise à jour de la permission: ' + data.message);
            // Revenir à l'état précédent
            event.target.checked = !isChecked;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour de la permission');
        // Revenir à l'état précédent
        event.target.checked = !isChecked;
    });
}
</script>
@endsection