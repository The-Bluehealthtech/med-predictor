@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('role-management.index') }}" class="text-blue-600 hover:text-blue-900 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $role->display_name }}</h1>
                <p class="text-gray-600 mt-2">Role details and assigned users</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Role Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Role Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                        <p class="text-sm text-gray-900">{{ $role->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                        <p class="text-sm text-gray-900">{{ $role->display_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->is_system_role ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $role->is_system_role ? 'System Role' : 'Custom Role' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">FIFA Connect ID Prefix</label>
                        <p class="text-sm text-gray-900">{{ $role->fifa_connect_id_prefix ?: 'Not set' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Users with this Role</label>
                        <p class="text-sm text-gray-900">{{ $role->getUsersCount() }} users</p>
                    </div>
                </div>
                
                @if($role->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-sm text-gray-900">{{ $role->description }}</p>
                </div>
                @endif
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                    <p class="text-sm text-gray-900">{{ $role->created_at->format('M j, Y \a\t g:i A') }}</p>
                </div>
                
                @if($role->creator)
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created By</label>
                    <p class="text-sm text-gray-900">{{ $role->creator->name }}</p>
                </div>
                @endif
            </div>

            <!-- Permissions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Permissions ({{ $role->getPermissionsCount() }})</h2>
                
                @if(count($role->getPermissionsList()) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($role->getPermissionsList() as $permission)
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                <div class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $permission)) }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $permission }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No permissions assigned to this role.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    @if(!$role->is_system_role)
                        <a href="{{ route('role-management.edit', $role) }}" 
                           class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Role
                        </a>
                        
                        <form action="{{ route('role-management.duplicate', $role) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Duplicate Role
                            </button>
                        </form>
                        
                        @if($role->isDeletable())
                            <form action="{{ route('role-management.destroy', $role) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Role
                                </button>
                            </form>
                        @endif
                    @endif
                    
                    <a href="{{ route('role-management.index') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Roles
                    </a>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Permissions</span>
                        <span class="text-sm font-medium text-gray-900">{{ $role->getPermissionsCount() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Assigned Users</span>
                        <span class="text-sm font-medium text-gray-900">{{ $role->getUsersCount() }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Created</span>
                        <span class="text-sm font-medium text-gray-900">{{ $role->created_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($role->updated_at != $role->created_at)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $role->updated_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Users with this Role -->
    @if($usersWithRole->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Users with this Role ({{ $usersWithRole->total() }})</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($usersWithRole as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->hasProfilePicture())
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->getProfilePictureAlt() }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ $user->getInitials() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->fifa_connect_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->getEntityName() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : ($user->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($usersWithRole->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $usersWithRole->links() }}
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection 